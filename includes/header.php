<?php
/**
 * InfinityBinary - Header Navigation
 */

if (!defined('IB_INIT')) {
    die('Direct access not permitted');
}
?>

<header class="site-header glass-nav">
    <div class="container">
        <nav class="navbar">
            <a href="/" class="logo">
                <span class="logo-text"><?= esc(get_setting('site_name', 'InfinityBinary')) ?></span>
            </a>

            <ul class="nav-links">
                <li><a href="/">Home</a></li>
                <li><a href="/about.php">About</a></li>
                <li><a href="/services.php">Services</a></li>
                <li><a href="/portfolio.php">Portfolio</a></li>
                <li><a href="/blog.php">Blog</a></li>
                <li><a href="/contact.php">Contact</a></li>
            </ul>

            <button class="mobile-toggle" aria-label="Toggle menu">
                <span></span>
                <span></span>
                <span></span>
            </button>
        </nav>
    </div>
</header>
