<?php
/**
 * InfinityBinary - Homepage
 */

define('IB_INIT', true);
require_once __DIR__ . '/includes/config.php';

$page_title = 'Home';
$meta_description = get_setting('site_description', 'Digital Excellence Delivered');

// Fetch services
$services = db()->fetchAll("SELECT * FROM services ORDER BY order_index ASC LIMIT 6");

// Fetch featured portfolio
$portfolio = db()->fetchAll("SELECT * FROM portfolio WHERE featured = 1 ORDER BY created_at DESC LIMIT 3");

require_once __DIR__ . '/includes/head.php';
require_once __DIR__ . '/includes/header.php';
?>

<!-- Hero Section -->
<section class="hero">
    <div class="container">
        <div class="hero-content">
            <div class="hero-eyebrow">Digital Innovation Partners</div>
            <h1 class="hero-title text-gradient">
                <?= esc(get_setting('hero_headline', 'We Build What Others Imagine')) ?>
            </h1>
            <p class="hero-subtitle">
                <?= esc(get_setting('hero_subheadline', 'Transforming visionary ideas into exceptional digital experiences')) ?>
            </p>
            <div class="hero-buttons">
                <a href="/contact.php" class="btn btn-primary">Start Your Project</a>
                <a href="/portfolio.php" class="btn btn-secondary">View Our Work</a>
            </div>
        </div>
    </div>
</section>

<!-- Services Section -->
<section id="services" class="services">
    <div class="container">
        <div class="text-center mb-3">
            <h2>Our Services</h2>
            <p>Comprehensive digital solutions tailored to your needs</p>
        </div>

        <div class="services-grid">
            <?php foreach ($services as $service): ?>
            <div class="service-card" data-animate>
                <div class="service-icon">
                    <?= $service['icon'] ?>
                </div>
                <h3><?= esc($service['name']) ?></h3>
                <p><?= esc($service['description']) ?></p>
            </div>
            <?php endforeach; ?>
        </div>

        <div class="text-center mt-3">
            <a href="/services.php" class="btn btn-secondary">View All Services</a>
        </div>
    </div>
</section>

<!-- Portfolio Section -->
<?php if (!empty($portfolio)): ?>
<section id="portfolio" class="portfolio">
    <div class="container">
        <div class="text-center mb-3">
            <h2>Featured Work</h2>
            <p>Showcasing our recent projects and success stories</p>
        </div>

        <div class="portfolio-grid">
            <?php foreach ($portfolio as $item): ?>
            <div class="portfolio-item" data-animate>
                <div class="portfolio-image">
                    <?php if (!empty($item['image'])): ?>
                        <img src="<?= esc($item['image']) ?>" alt="<?= esc($item['title']) ?>">
                    <?php else: ?>
                        <?= esc($item['title']) ?>
                    <?php endif; ?>
                </div>
                <div class="portfolio-info">
                    <h3><?= esc($item['title']) ?></h3>
                    <p><?= esc(truncate($item['description'], 120)) ?></p>
                    <span class="portfolio-category"><?= esc($item['category']) ?></span>
                </div>
            </div>
            <?php endforeach; ?>
        </div>

        <div class="text-center mt-3">
            <a href="/portfolio.php" class="btn btn-secondary">View All Projects</a>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- CTA Section -->
<section class="cta-section">
    <div class="container">
        <div class="glass-panel text-center">
            <h2 class="mb-2">Ready to Start Your Project?</h2>
            <p class="mb-2">Let's discuss how we can help bring your vision to life</p>
            <div class="hero-buttons">
                <a href="/contact.php" class="btn btn-primary">Get In Touch</a>
                <a href="/about.php" class="btn btn-secondary">Learn More About Us</a>
            </div>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
