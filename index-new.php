<?php
/**
 * INFINITYBINARY - Homepage (PHP Dynamic Version)
 * Reads all content from database
 */
require_once 'includes/config.php';

// Fetch homepage settings from database
$hero_eyebrow = get_setting('hero_eyebrow', 'DIGITAL INNOVATION');
$hero_headline = get_setting('hero_headline', 'We Build What Others Imagine');
$hero_sub = get_setting('hero_sub', 'Expert digital solutions for businesses that dare to be different. From concept to deployment, we transform visionary ideas into powerful digital experiences.');
$hero_cta_primary = get_setting('hero_cta_primary_label', 'View Our Work');
$hero_cta_primary_url = get_setting('hero_cta_primary_url', '/portfolio');
$hero_cta_secondary = get_setting('hero_cta_secondary_label', 'Get Started');
$hero_cta_secondary_url = get_setting('hero_cta_secondary_url', '/contact');
$hero_media_source = get_setting('hero_media_source', 'https://cdn.example.com/hero-video.mp4');

// Fetch services
$services = db()->fetchAll("SELECT * FROM services WHERE status = 'active' ORDER BY `order` ASC LIMIT 6");

// Fetch stats
$stats = json_decode(get_setting('hero_stats', '[]'), true);
if (empty($stats)) {
    $stats = [
        ['value' => '500+', 'label' => 'Projects Completed'],
        ['value' => '98%', 'label' => 'Client Satisfaction'],
        ['value' => '50+', 'label' => 'Team Members'],
        ['value' => '15+', 'label' => 'Years Experience']
    ];
}

// Page meta
$page_title = get_setting('site_name', 'InfinityBinary') . ' - ' . get_setting('site_tagline', 'Innovation Beyond Limits');
$page_description = get_setting('site_description', 'We build what others imagine. Expert digital solutions for the modern world.');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo esc_html($page_title); ?></title>
    <meta name="description" content="<?php echo esc_attr($page_description); ?>">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Syne:wght@400..800&family=Outfit:wght@300;400;500;600;700&family=DM+Sans:wght@300;400;500&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">

    <!-- Styles -->
    <link rel="stylesheet" href="./assets/css/cms-theme.css">
    <link rel="stylesheet" href="./assets/css/glass.css">
    <link rel="stylesheet" href="./assets/css/typography.css">

    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: var(--font-body);
            background: var(--bg-void);
            color: var(--text-primary);
            overflow-x: hidden;
        }
        .hero {
            position: relative;
            min-height: 100dvh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem;
            overflow: hidden;
        }
        .hero-media {
            position: absolute;
            inset: 0;
            width: 100%;
            height: 100%;
            object-fit: cover;
            z-index: 1;
        }
        .hero::before {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(180deg, rgba(2,4,7,0.55) 0%, rgba(2,4,7,0.92) 100%);
            z-index: 2;
        }
        .hero-content {
            position: relative;
            z-index: 3;
            text-align: center;
            max-width: 900px;
        }
        .hero-eyebrow {
            margin-bottom: 1.5rem;
        }
        .hero-headline {
            margin-bottom: 1.5rem;
        }
        .hero-sub {
            margin-bottom: 2.5rem;
            max-width: 700px;
            margin-left: auto;
            margin-right: auto;
        }
        .hero-buttons {
            display: flex;
            gap: 1rem;
            justify-content: center;
            flex-wrap: wrap;
        }
        .btn {
            display: inline-block;
            padding: 1rem 2rem;
            border-radius: var(--btn-radius);
            text-decoration: none;
            font-weight: 500;
            transition: all 0.3s;
        }
        .btn-primary {
            background: var(--accent-1);
            color: var(--bg-void);
        }
        .btn-primary:hover {
            background: var(--accent-2);
            transform: translateY(-2px);
            box-shadow: 0 8px 24px rgba(0,212,255,0.3);
        }
        .btn-secondary {
            background: rgba(255,255,255,0.1);
            color: var(--text-primary);
            border: 1px solid rgba(255,255,255,0.2);
        }
        .btn-secondary:hover {
            background: rgba(255,255,255,0.2);
            border-color: var(--accent-1);
        }
        .stats-bar {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            z-index: 4;
            padding: 1.5rem 2rem;
            display: flex;
            justify-content: space-around;
            gap: 2rem;
        }
        .stat {
            text-align: center;
        }
        .stat-value {
            font-family: var(--font-display);
            font-size: 2rem;
            font-weight: 700;
            color: var(--accent-1);
            margin-bottom: 0.25rem;
        }
        .stat-label {
            font-size: 0.875rem;
            color: var(--text-secondary);
        }
        .section {
            padding: var(--section-pad) 2rem;
            max-width: 1200px;
            margin: 0 auto;
        }
        .services-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 2rem;
            margin-top: 3rem;
        }
        .service-card {
            padding: 2rem;
            text-align: center;
        }
        .service-icon {
            font-size: 3rem;
            margin-bottom: 1rem;
            color: var(--accent-1);
        }
        .service-name {
            margin-bottom: 1rem;
        }
        @media (max-width: 768px) {
            .stats-bar {
                flex-wrap: wrap;
                gap: 1rem;
            }
            .services-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <!-- Hero Section -->
    <section class="hero">
        <?php echo render_media($hero_media_source, 'Hero Background', 'hero-media', 'video'); ?>

        <div class="hero-content">
            <div class="t-eyebrow hero-eyebrow"><?php echo esc_html($hero_eyebrow); ?></div>
            <h1 class="t-display t-gradient t-shimmer hero-headline"><?php echo esc_html($hero_headline); ?></h1>
            <p class="t-body-large hero-sub"><?php echo esc_html($hero_sub); ?></p>

            <div class="hero-buttons">
                <a href="<?php echo esc_url($hero_cta_primary_url); ?>" class="btn btn-primary">
                    <?php echo esc_html($hero_cta_primary); ?>
                </a>
                <a href="<?php echo esc_url($hero_cta_secondary_url); ?>" class="btn btn-secondary">
                    <?php echo esc_html($hero_cta_secondary); ?>
                </a>
            </div>
        </div>

        <div class="stats-bar glass-dark">
            <?php foreach ($stats as $stat): ?>
            <div class="stat">
                <div class="stat-value"><?php echo esc_html($stat['value']); ?></div>
                <div class="stat-label"><?php echo esc_html($stat['label']); ?></div>
            </div>
            <?php endforeach; ?>
        </div>
    </section>

    <!-- Services Section -->
    <section class="section">
        <div class="t-eyebrow" style="text-align: center; margin-bottom: 1rem;">OUR SERVICES</div>
        <h2 class="t-h2" style="text-align: center; margin-bottom: 3rem;">What We Do Best</h2>

        <div class="services-grid">
            <?php foreach ($services as $service): ?>
            <div class="service-card glass-panel">
                <div class="service-icon">
                    <?php echo render_media($service['icon_source'], $service['name'], '', 'icon'); ?>
                </div>
                <h3 class="t-h3 service-name"><?php echo esc_html($service['name']); ?></h3>
                <p class="t-body"><?php echo esc_html($service['description']); ?></p>
            </div>
            <?php endforeach; ?>
        </div>
    </section>

    <!-- Halo Effect -->
    <script src="./assets/js/halo.js"></script>
</body>
</html>
