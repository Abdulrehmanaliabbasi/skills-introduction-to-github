<?php
/**
 * InfinityBinary CMS - Dashboard
 */

define('IB_INIT', true);
require_once __DIR__ . '/../includes/config.php';

require_auth();

$user = current_user();

// Get stats
$pages_count = db()->count('pages');
$posts_count = db()->count('posts');
$portfolio_count = db()->count('portfolio');
$services_count = db()->count('services');

// Get recent posts
$recent_posts = db()->fetchAll("SELECT * FROM posts ORDER BY created_at DESC LIMIT 5");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - CMS</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: #f8f9fc;
            color: #1a1d2e;
        }

        .header {
            background: white;
            border-bottom: 1px solid #e2e6ea;
            padding: 1rem 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .header h1 {
            font-size: 1.5rem;
            color: #1a1d2e;
        }

        .header-actions {
            display: flex;
            gap: 1rem;
            align-items: center;
        }

        .btn {
            padding: 0.625rem 1.25rem;
            border-radius: 6px;
            text-decoration: none;
            font-weight: 500;
            font-size: 0.875rem;
            transition: all 0.2s;
        }

        .btn-primary {
            background: #00d4ff;
            color: white;
        }

        .btn-primary:hover {
            background: #00b8e6;
        }

        .btn-secondary {
            background: #f1f3f5;
            color: #495057;
        }

        .btn-secondary:hover {
            background: #e9ecef;
        }

        .user-info {
            color: #6c757d;
            font-size: 0.875rem;
        }

        .container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 2rem;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }

        .stat-card {
            background: white;
            border: 1px solid #e2e6ea;
            border-radius: 12px;
            padding: 1.5rem;
        }

        .stat-card h3 {
            font-size: 0.875rem;
            color: #6c757d;
            margin-bottom: 0.5rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .stat-card .number {
            font-size: 2.5rem;
            font-weight: 700;
            color: #1a1d2e;
        }

        .content-section {
            background: white;
            border: 1px solid #e2e6ea;
            border-radius: 12px;
            padding: 2rem;
            margin-bottom: 2rem;
        }

        .content-section h2 {
            font-size: 1.25rem;
            margin-bottom: 1.5rem;
            color: #1a1d2e;
        }

        .table {
            width: 100%;
            border-collapse: collapse;
        }

        .table th {
            text-align: left;
            padding: 0.75rem;
            border-bottom: 2px solid #e2e6ea;
            font-weight: 600;
            font-size: 0.875rem;
            color: #6c757d;
        }

        .table td {
            padding: 0.75rem;
            border-bottom: 1px solid #f1f3f5;
        }

        .badge {
            display: inline-block;
            padding: 0.25rem 0.75rem;
            border-radius: 4px;
            font-size: 0.75rem;
            font-weight: 600;
        }

        .badge-success {
            background: #d4f5dd;
            color: #2b8a3e;
        }

        .badge-warning {
            background: #fff3bf;
            color: #f08c00;
        }

        .quick-actions {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
            margin-top: 2rem;
        }

        .action-card {
            background: linear-gradient(135deg, #00d4ff, #7b2fff);
            color: white;
            padding: 1.5rem;
            border-radius: 12px;
            text-decoration: none;
            transition: transform 0.2s;
        }

        .action-card:hover {
            transform: translateY(-4px);
        }

        .action-card h3 {
            font-size: 1rem;
            margin-bottom: 0.5rem;
        }

        .action-card p {
            font-size: 0.875rem;
            opacity: 0.9;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>CMS Dashboard</h1>
        <div class="header-actions">
            <span class="user-info">Welcome, <?= esc($user['name']) ?></span>
            <a href="/" class="btn btn-secondary" target="_blank">View Site</a>
            <a href="/cms/logout.php" class="btn btn-secondary">Logout</a>
        </div>
    </div>

    <div class="container">
        <div class="stats-grid">
            <div class="stat-card">
                <h3>Pages</h3>
                <div class="number"><?= $pages_count ?></div>
            </div>
            <div class="stat-card">
                <h3>Blog Posts</h3>
                <div class="number"><?= $posts_count ?></div>
            </div>
            <div class="stat-card">
                <h3>Portfolio</h3>
                <div class="number"><?= $portfolio_count ?></div>
            </div>
            <div class="stat-card">
                <h3>Services</h3>
                <div class="number"><?= $services_count ?></div>
            </div>
        </div>

        <div class="content-section">
            <h2>Recent Posts</h2>
            <?php if (empty($recent_posts)): ?>
                <p>No posts yet. <a href="/cms/posts/edit.php">Create your first post</a></p>
            <?php else: ?>
            <table class="table">
                <thead>
                    <tr>
                        <th>Title</th>
                        <th>Category</th>
                        <th>Status</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($recent_posts as $post): ?>
                    <tr>
                        <td><?= esc($post['title']) ?></td>
                        <td><?= esc($post['category'] ?: 'Uncategorized') ?></td>
                        <td>
                            <span class="badge <?= $post['status'] === 'published' ? 'badge-success' : 'badge-warning' ?>">
                                <?= esc(ucfirst($post['status'])) ?>
                            </span>
                        </td>
                        <td><?= format_date($post['created_at'], 'M j, Y') ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <?php endif; ?>
        </div>

        <div class="content-section">
            <h2>Quick Actions</h2>
            <div class="quick-actions">
                <a href="/cms/posts/edit.php" class="action-card">
                    <h3>New Post</h3>
                    <p>Create a new blog post</p>
                </a>
                <a href="/cms/portfolio/edit.php" class="action-card">
                    <h3>Add Project</h3>
                    <p>Add to portfolio</p>
                </a>
                <a href="/cms/media/" class="action-card">
                    <h3>Media Library</h3>
                    <p>Manage uploads</p>
                </a>
                <a href="/cms/pages/edit.php" class="action-card">
                    <h3>New Page</h3>
                    <p>Create a new page</p>
                </a>
            </div>
        </div>
    </div>
</body>
</html>
