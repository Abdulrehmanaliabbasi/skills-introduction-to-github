<?php
// Media Handling System - Triple Source Support
if (!defined('INFINITYBINARY_INIT')) {
    die('Direct access not permitted');
}

/**
 * Render media from triple-source system (Upload/URL/Code)
 *
 * @param string $source Media source (file path, URL, or SVG/icon code)
 * @param string $alt Alt text for images
 * @param string $class Additional CSS classes
 * @param string $type Media type: 'image', 'icon', 'video', 'bg'
 * @return string Rendered HTML
 */
function render_media($source, $alt = '', $class = '', $type = 'image') {
    if (empty($source)) {
        return '';
    }

    // Clean up source
    $source = trim($source);

    // Check if it's raw HTML/SVG code (starts with < character)
    if (str_starts_with($source, '<')) {
        return '<span class="media-code ' . esc_attr($class) . '" aria-label="' . esc_attr($alt) . '">' . $source . '</span>';
    }

    // Determine if URL is external or local
    if (str_starts_with($source, 'http://') || str_starts_with($source, 'https://')) {
        $url = $source;
    } else {
        // Local file - construct path
        $url = BASE_URL . '/assets/media/' . ltrim($source, '/');
    }

    // Render based on type
    return match($type) {
        'icon' => '<img src="' . esc_attr($url) . '" alt="' . esc_attr($alt) . '" class="media-icon ' . esc_attr($class) . '" loading="lazy" decoding="async">',
        'video' => render_video($url, $class),
        'bg' => 'style="background-image:url(\'' . esc_attr($url) . '\')"',
        default => '<img src="' . esc_attr($url) . '" alt="' . esc_attr($alt) . '" class="media-img ' . esc_attr($class) . '" loading="lazy" decoding="async">',
    };
}

/**
 * Render video element with platform detection
 *
 * @param string $source Video URL or file path
 * @param string $class Additional CSS classes
 * @return string Rendered video HTML
 */
function render_video($source, $class = '') {
    // YouTube
    if (preg_match('/(?:youtube\.com\/watch\?v=|youtu\.be\/)([a-zA-Z0-9_-]+)/', $source, $matches)) {
        $videoId = $matches[1];
        return '<iframe class="media-video ' . esc_attr($class) . '" src="https://www.youtube.com/embed/' . $videoId . '?autoplay=1&mute=1&loop=1&playlist=' . $videoId . '&controls=0" frameborder="0" allow="autoplay;encrypted-media" allowfullscreen></iframe>';
    }

    // Vimeo
    if (preg_match('/vimeo\.com\/(\d+)/', $source, $matches)) {
        $videoId = $matches[1];
        return '<iframe class="media-video ' . esc_attr($class) . '" src="https://player.vimeo.com/video/' . $videoId . '?autoplay=1&muted=1&loop=1&background=1" frameborder="0" allowfullscreen></iframe>';
    }

    // HTML5 video
    $extension = pathinfo($source, PATHINFO_EXTENSION);
    return '<video class="media-video ' . esc_attr($class) . '" autoplay muted loop playsinline preload="none"><source src="' . esc_attr($source) . '" type="video/' . $extension . '"></video>';
}

/**
 * Get media type from source
 *
 * @param string $source Media source
 * @return string Type: 'code', 'url', or 'file'
 */
function get_media_type($source) {
    if (empty($source)) {
        return 'none';
    }

    $source = trim($source);

    if (str_starts_with($source, '<')) {
        return 'code';
    }

    if (str_starts_with($source, 'http://') || str_starts_with($source, 'https://')) {
        return 'url';
    }

    return 'file';
}

/**
 * Handle file upload
 *
 * @param array $file $_FILES array element
 * @param string $targetDir Target directory relative to uploads folder
 * @return array Result with 'success' boolean and 'message' or 'filename'
 */
function handle_upload($file, $targetDir = '') {
    // Check for upload errors
    if ($file['error'] !== UPLOAD_ERR_OK) {
        return [
            'success' => false,
            'message' => 'Upload error: ' . $file['error']
        ];
    }

    // Check file size
    if ($file['size'] > MAX_UPLOAD_SIZE) {
        return [
            'success' => false,
            'message' => 'File size exceeds maximum allowed size (' . format_bytes(MAX_UPLOAD_SIZE) . ')'
        ];
    }

    // Check file type
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mimeType = finfo_file($finfo, $file['tmp_name']);
    finfo_close($finfo);

    $allowedTypes = array_merge(ALLOWED_IMAGE_TYPES, ALLOWED_VIDEO_TYPES);
    if (!in_array($mimeType, $allowedTypes)) {
        return [
            'success' => false,
            'message' => 'Invalid file type. Only images and videos are allowed.'
        ];
    }

    // Generate unique filename
    $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
    $filename = uniqid() . '_' . time() . '.' . $extension;

    // Create target directory if it doesn't exist
    $uploadDir = UPLOAD_PATH . ltrim($targetDir, '/');
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }

    $targetPath = $uploadDir . '/' . $filename;
    $relativePath = ltrim($targetDir, '/') . '/' . $filename;

    // Move uploaded file
    if (move_uploaded_file($file['tmp_name'], $targetPath)) {
        // If image, try to create WebP version
        if (in_array($mimeType, ALLOWED_IMAGE_TYPES) && function_exists('imagewebp')) {
            create_webp_version($targetPath);
        }

        return [
            'success' => true,
            'filename' => $relativePath,
            'url' => UPLOAD_URL . $relativePath,
            'type' => $mimeType
        ];
    }

    return [
        'success' => false,
        'message' => 'Failed to move uploaded file'
    ];
}

/**
 * Create WebP version of image
 *
 * @param string $imagePath Path to original image
 * @return bool Success status
 */
function create_webp_version($imagePath) {
    $info = getimagesize($imagePath);
    if (!$info) {
        return false;
    }

    $image = null;
    switch ($info['mime']) {
        case 'image/jpeg':
            $image = imagecreatefromjpeg($imagePath);
            break;
        case 'image/png':
            $image = imagecreatefrompng($imagePath);
            break;
        case 'image/gif':
            $image = imagecreatefromgif($imagePath);
            break;
        default:
            return false;
    }

    if ($image) {
        $webpPath = preg_replace('/\.[^.]+$/', '.webp', $imagePath);
        $result = imagewebp($image, $webpPath, 85);
        imagedestroy($image);
        return $result;
    }

    return false;
}

/**
 * Delete media file
 *
 * @param string $filepath File path relative to uploads directory
 * @return bool Success status
 */
function delete_media_file($filepath) {
    if (empty($filepath)) {
        return false;
    }

    $fullPath = UPLOAD_PATH . ltrim($filepath, '/');

    if (file_exists($fullPath)) {
        // Delete WebP version if exists
        $webpPath = preg_replace('/\.[^.]+$/', '.webp', $fullPath);
        if (file_exists($webpPath)) {
            @unlink($webpPath);
        }

        return @unlink($fullPath);
    }

    return false;
}
