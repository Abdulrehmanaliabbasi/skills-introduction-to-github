<?php
/**
 * InfinityBinary - Triple-Source Media System
 * Handles upload/URL/code media sources
 */

if (!defined('IB_INIT')) {
    die('Direct access not permitted');
}

/**
 * Render media from any source type
 * @param string $source - Can be upload path, URL, or SVG/icon code
 * @param string $alt - Alt text for images
 * @param string $class - Additional CSS classes
 * @param string $type - Media type: image, icon, video, bg
 * @return string HTML output
 */
function render_media($source, $alt = '', $class = '', $type = 'image') {
    if (empty($source)) {
        return '';
    }

    // Detect if source is code (starts with < or contains SVG tags)
    if (str_starts_with(trim($source), '<')) {
        return '<span class="media-code ' . esc($class) . '" aria-label="' . esc($alt) . '">' . $source . '</span>';
    }

    // Determine if URL or local path
    $is_external = str_starts_with($source, 'http://') || str_starts_with($source, 'https://');
    $url = $is_external ? $source : '/assets/media/' . ltrim($source, '/');

    // Render based on type
    return match($type) {
        'icon' => render_icon($url, $alt, $class),
        'video' => render_video($source, $class),
        'bg' => 'style="background-image:url(\'' . esc($url) . '\')"',
        default => render_image($url, $alt, $class)
    };
}

/**
 * Render image element
 */
function render_image($url, $alt = '', $class = '') {
    return sprintf(
        '<img src="%s" alt="%s" class="media-img %s" loading="lazy" decoding="async">',
        esc($url),
        esc($alt),
        esc($class)
    );
}

/**
 * Render icon element
 */
function render_icon($url, $alt = '', $class = '') {
    return sprintf(
        '<img src="%s" alt="%s" class="media-icon %s" loading="lazy" decoding="async">',
        esc($url),
        esc($alt),
        esc($class)
    );
}

/**
 * Render video element (supports YouTube, Vimeo, and direct files)
 */
function render_video($source, $class = '') {
    // YouTube detection
    if (preg_match('/(?:youtube\.com\/watch\?v=|youtu\.be\/)([a-zA-Z0-9_-]+)/', $source, $matches)) {
        $video_id = $matches[1];
        return sprintf(
            '<iframe class="media-video %s" src="https://www.youtube.com/embed/%s?autoplay=1&mute=1&loop=1&playlist=%s&controls=0" frameborder="0" allow="autoplay;encrypted-media" allowfullscreen></iframe>',
            esc($class),
            esc($video_id),
            esc($video_id)
        );
    }

    // Vimeo detection
    if (preg_match('/vimeo\.com\/(\d+)/', $source, $matches)) {
        $video_id = $matches[1];
        return sprintf(
            '<iframe class="media-video %s" src="https://player.vimeo.com/video/%s?autoplay=1&muted=1&loop=1&background=1" frameborder="0" allowfullscreen></iframe>',
            esc($class),
            esc($video_id)
        );
    }

    // Direct video file
    $extension = pathinfo($source, PATHINFO_EXTENSION);
    return sprintf(
        '<video class="media-video %s" autoplay muted loop playsinline preload="none"><source src="%s" type="video/%s"></video>',
        esc($class),
        esc($source),
        esc($extension)
    );
}

/**
 * Upload file and return path
 */
function upload_file($file, $destination = 'uploads') {
    if (!isset($file['error']) || is_array($file['error'])) {
        throw new Exception('Invalid file upload');
    }

    // Check for upload errors
    switch ($file['error']) {
        case UPLOAD_ERR_OK:
            break;
        case UPLOAD_ERR_NO_FILE:
            throw new Exception('No file was uploaded');
        case UPLOAD_ERR_INI_SIZE:
        case UPLOAD_ERR_FORM_SIZE:
            throw new Exception('File size exceeds limit');
        default:
            throw new Exception('Upload error occurred');
    }

    // Validate file size
    if ($file['size'] > MAX_UPLOAD_SIZE) {
        throw new Exception('File size exceeds ' . format_bytes(MAX_UPLOAD_SIZE));
    }

    // Validate MIME type
    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mime_type = $finfo->file($file['tmp_name']);

    $allowed_types = array_merge(ALLOWED_IMAGE_TYPES, ALLOWED_VIDEO_TYPES);
    if (!in_array($mime_type, $allowed_types)) {
        throw new Exception('Invalid file type');
    }

    // Generate unique filename
    $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
    $filename = uniqid() . '_' . time() . '.' . $extension;

    // Determine upload path
    $upload_path = MEDIA_PATH . '/' . $destination;
    if (!is_dir($upload_path)) {
        mkdir($upload_path, 0755, true);
    }

    $filepath = $upload_path . '/' . $filename;

    // Move uploaded file
    if (!move_uploaded_file($file['tmp_name'], $filepath)) {
        throw new Exception('Failed to move uploaded file');
    }

    // Generate thumbnail for images
    if (in_array($mime_type, ALLOWED_IMAGE_TYPES)) {
        generate_thumbnail($filepath);
    }

    // Get image dimensions if applicable
    $dimensions = null;
    if (in_array($mime_type, ALLOWED_IMAGE_TYPES)) {
        $image_info = getimagesize($filepath);
        $dimensions = [
            'width' => $image_info[0] ?? null,
            'height' => $image_info[1] ?? null
        ];
    }

    // Store in database
    $media_id = db()->insert('media', [
        'filename' => $filename,
        'original_name' => $file['name'],
        'filepath' => $destination . '/' . $filename,
        'source_type' => 'upload',
        'mime_type' => $mime_type,
        'file_size' => $file['size'],
        'width' => $dimensions['width'] ?? null,
        'height' => $dimensions['height'] ?? null,
        'uploaded_by' => $_SESSION['user_id'] ?? null
    ]);

    return [
        'id' => $media_id,
        'filename' => $filename,
        'filepath' => $destination . '/' . $filename,
        'url' => '/assets/media/' . $destination . '/' . $filename,
        'mime_type' => $mime_type,
        'size' => $file['size']
    ];
}

/**
 * Generate thumbnail for image
 */
function generate_thumbnail($source_path, $max_width = 400, $max_height = 400) {
    if (!function_exists('imagecreatefromjpeg')) {
        return false; // GD library not available
    }

    $info = getimagesize($source_path);
    if (!$info) {
        return false;
    }

    list($width, $height, $type) = $info;

    // Calculate new dimensions
    $ratio = min($max_width / $width, $max_height / $height);
    $new_width = (int)($width * $ratio);
    $new_height = (int)($height * $ratio);

    // Create new image
    $thumb = imagecreatetruecolor($new_width, $new_height);

    // Preserve transparency for PNG
    if ($type === IMAGETYPE_PNG) {
        imagealphablending($thumb, false);
        imagesavealpha($thumb, true);
        $transparent = imagecolorallocatealpha($thumb, 255, 255, 255, 127);
        imagefilledrectangle($thumb, 0, 0, $new_width, $new_height, $transparent);
    }

    // Load source image
    $source = match($type) {
        IMAGETYPE_JPEG => imagecreatefromjpeg($source_path),
        IMAGETYPE_PNG => imagecreatefrompng($source_path),
        IMAGETYPE_GIF => imagecreatefromgif($source_path),
        IMAGETYPE_WEBP => imagecreatefromwebp($source_path),
        default => null
    };

    if (!$source) {
        return false;
    }

    // Resize
    imagecopyresampled($thumb, $source, 0, 0, 0, 0, $new_width, $new_height, $width, $height);

    // Save thumbnail
    $thumb_path = preg_replace('/(\.[^.]+)$/', '_thumb$1', $source_path);

    $saved = match($type) {
        IMAGETYPE_JPEG => imagejpeg($thumb, $thumb_path, 85),
        IMAGETYPE_PNG => imagepng($thumb, $thumb_path, 8),
        IMAGETYPE_GIF => imagegif($thumb, $thumb_path),
        IMAGETYPE_WEBP => imagewebp($thumb, $thumb_path, 85),
        default => false
    };

    // Free memory
    imagedestroy($source);
    imagedestroy($thumb);

    return $saved ? $thumb_path : false;
}

/**
 * Delete media file
 */
function delete_media($media_id) {
    $media = db()->fetch("SELECT * FROM media WHERE id = ?", [$media_id]);

    if (!$media) {
        return false;
    }

    // Delete file if it's an upload
    if ($media['source_type'] === 'upload') {
        $file_path = MEDIA_PATH . '/' . $media['filepath'];
        $thumb_path = preg_replace('/(\.[^.]+)$/', '_thumb$1', $file_path);

        if (file_exists($file_path)) {
            unlink($file_path);
        }

        if (file_exists($thumb_path)) {
            unlink($thumb_path);
        }
    }

    // Delete from database
    return db()->delete('media', 'id = ?', [$media_id]);
}

/**
 * Format bytes to human-readable size
 */
function format_bytes($bytes, $precision = 2) {
    $units = ['B', 'KB', 'MB', 'GB', 'TB'];

    for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
        $bytes /= 1024;
    }

    return round($bytes, $precision) . ' ' . $units[$i];
}

/**
 * Get media thumbnail URL
 */
function get_thumbnail_url($media_path) {
    $thumb_path = preg_replace('/(\.[^.]+)$/', '_thumb$1', $media_path);
    $full_path = MEDIA_PATH . '/' . $thumb_path;

    if (file_exists($full_path)) {
        return '/assets/media/' . $thumb_path;
    }

    return '/assets/media/' . $media_path;
}

/**
 * Validate media source
 */
function validate_media_source($source, $type = 'image') {
    if (empty($source)) {
        return ['valid' => false, 'error' => 'Source cannot be empty'];
    }

    // If it's code (SVG or icon HTML)
    if (str_starts_with(trim($source), '<')) {
        // Basic validation for SVG/HTML
        if (strpos($source, '<script') !== false || strpos($source, 'javascript:') !== false) {
            return ['valid' => false, 'error' => 'Invalid code: contains potentially harmful content'];
        }
        return ['valid' => true, 'type' => 'code'];
    }

    // If it's a URL
    if (str_starts_with($source, 'http://') || str_starts_with($source, 'https://')) {
        if (!filter_var($source, FILTER_VALIDATE_URL)) {
            return ['valid' => false, 'error' => 'Invalid URL format'];
        }
        return ['valid' => true, 'type' => 'url'];
    }

    // If it's a local path
    $full_path = MEDIA_PATH . '/' . ltrim($source, '/');
    if (!file_exists($full_path)) {
        return ['valid' => false, 'error' => 'File does not exist'];
    }

    return ['valid' => true, 'type' => 'upload'];
}
