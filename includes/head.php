<?php
/**
 * InfinityBinary - HTML Head Section
 */

if (!defined('IB_INIT')) {
    die('Direct access not permitted');
}

$page_title = $page_title ?? get_setting('site_name', 'InfinityBinary');
$meta_description = $meta_description ?? get_setting('site_description', 'Digital Excellence Delivered');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="<?= esc($meta_description) ?>">
    <title><?= esc($page_title) ?> | <?= esc(get_setting('site_name', 'InfinityBinary')) ?></title>

    <!-- Preconnect for performance -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Syne:wght@400;600;700;800&family=Outfit:wght@300;400;500;600;700&family=DM+Sans:wght@300;400;500;700&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">

    <!-- Stylesheets -->
    <link rel="stylesheet" href="/assets/css/main.css">
    <link rel="stylesheet" href="/assets/css/glass.css">
    <link rel="stylesheet" href="/assets/css/components.css">

    <!-- Favicon -->
    <link rel="icon" type="image/svg+xml" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><text y='.9em' font-size='90'>∞</text></svg>">
</head>
<body>
