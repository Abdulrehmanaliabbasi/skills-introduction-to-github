<?php
/**
 * InfinityBinary - Footer
 */

if (!defined('IB_INIT')) {
    die('Direct access not permitted');
}
?>

<footer class="site-footer glass-panel">
    <div class="container">
        <div class="footer-grid">
            <div class="footer-col">
                <h3><?= esc(get_setting('site_name', 'InfinityBinary')) ?></h3>
                <p><?= esc(get_setting('footer_text', 'Building the future of digital experiences.')) ?></p>
            </div>

            <div class="footer-col">
                <h4>Quick Links</h4>
                <ul>
                    <li><a href="/about.php">About</a></li>
                    <li><a href="/services.php">Services</a></li>
                    <li><a href="/portfolio.php">Portfolio</a></li>
                    <li><a href="/blog.php">Blog</a></li>
                </ul>
            </div>

            <div class="footer-col">
                <h4>Services</h4>
                <ul>
                    <li><a href="/services.php#web">Web Development</a></li>
                    <li><a href="/services.php#mobile">Mobile Apps</a></li>
                    <li><a href="/services.php#ai">AI & ML</a></li>
                    <li><a href="/services.php#cloud">Cloud</a></li>
                </ul>
            </div>

            <div class="footer-col">
                <h4>Contact</h4>
                <ul>
                    <li><a href="mailto:<?= esc(get_setting('contact_email', '')) ?>"><?= esc(get_setting('contact_email', 'hello@infinitybinary.com')) ?></a></li>
                    <li><a href="tel:<?= esc(str_replace([' ', '(', ')', '-'], '', get_setting('contact_phone', ''))) ?>"><?= esc(get_setting('contact_phone', '+1 (555) 123-4567')) ?></a></li>
                </ul>
            </div>
        </div>

        <div class="footer-bottom">
            <p>&copy; <?= date('Y') ?> <?= esc(get_setting('site_name', 'InfinityBinary')) ?>. All rights reserved<a href="/cms/login.php" class="cms-entry" tabindex="-1" aria-hidden="true">.</a></p>
        </div>
    </div>
</footer>

<!-- Scripts -->
<script src="/assets/js/core.js"></script>
<script src="/assets/js/halo.js"></script>

</body>
</html>
