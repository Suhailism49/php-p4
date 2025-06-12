<?php
session_start();

// Error reporting
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Controleer of ingelogd
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: login.php');
    exit;
}

require_once '../config/database.php';

try {
    $db = getDB();
    
    // Haal basis statistieken op
    $stmt = $db->query("SELECT COUNT(*) as total FROM news_articles");
    $totalArticles = $stmt->fetch()['total'];
    
    $stmt = $db->query("SELECT COUNT(*) as published FROM news_articles WHERE is_published = 1");
    $publishedArticles = $stmt->fetch()['published'];
    
    $stmt = $db->query("SELECT COUNT(*) as total FROM categories");
    $totalCategories = $stmt->fetch()['total'];
    
    $stmt = $db->query("SELECT COUNT(*) as total FROM comments WHERE is_approved = 0");
    $pendingComments = $stmt->fetch()['total'];
    
    $stmt = $db->query("SELECT SUM(read_count) as total FROM news_articles WHERE is_published = 1");
    $totalViews = $stmt->fetch()['total'] ?? 0;
    
    // Haal recente artikelen op
    $stmt = $db->query("SELECT a.*, c.name as category_name 
                        FROM news_articles a 
                        JOIN categories c ON a.category_id = c.id 
                        ORDER BY a.created_at DESC LIMIT 5");
    $recentArticles = $stmt->fetchAll();
    
} catch (Exception $e) {
    $error = "Database fout: " . $e->getMessage();
}

$username = $_SESSION['admin_username'] ?? 'Admin';
$role = $_SESSION['admin_role'] ?? 'admin';
?>
<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Nieuwswebsite Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --primary-color: #2c3e50;
            --secondary-color: #3498db;
            --success-color: #27ae60;
            --warning-color: #f39c12;
            --danger-color: #e74c3c;
            --light-bg: #f8f9fa;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: var(--light-bg);
        }
        
        .navbar {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        .stat-card {
            background: white;
            border-radius: 15px;
            padding: 2rem;
            box-shadow: 0 5px 15px rgba(0,0,0,0.08);
            border: none;
            transition: transform 0.3s ease;
            margin-bottom: 2rem;
        }
        
        .stat-card:hover {
            transform: translateY(-5px);
        }
        
        .stat-icon {
            width: 60px;
            height: 60px;
            border-radius: 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            color: white;
            margin-bottom: 1rem;
        }
        
        .stat-number {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
        }
        
        .stat-label {
            color: #6c757d;
            font-size: 0.9rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .welcome-card {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            color: white;
            border-radius: 15px;
            padding: 2rem;
            margin-bottom: 2rem;
        }
        
        .recent-articles {
            background: white;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.08);
            padding: 2rem;
        }
        
        .article-item {
            padding: 1rem 0;
            border-bottom: 1px solid #e9ecef;
        }
        
        .article-item:last-child {
            border-bottom: none;
        }
        
        .quick-actions {
            background: white;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.08);
            padding: 2rem;
        }
        
        .action-btn {
            display: block;
            width: 100%;
            padding: 1rem;
            margin-bottom: 1rem;
            border-radius: 10px;
            text-decoration: none;
            text-align: center;
            transition: transform 0.2s;
        }
        
        .action-btn:hover {
            transform: translateY(-2px);
            text-decoration: none;
        }
        
        .btn-primary-gradient {
            background: linear-gradient(135deg, var(--secondary-color) 0%, var(--primary-color) 100%);
            color: white;
        }
        
        .btn-success-gradient {
            background: linear-gradient(135deg, var(--success-color) 0%, #229954 100%);
            color: white;
        }
        
        .btn-warning-gradient {
            background: linear-gradient(135deg, var(--warning-color) 0%, #d68910 100%);
            color: white;
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="dashboard.php">
                <i class="fas fa-tachometer-alt me-2"></i>
                Nieuwswebsite Admin
            </a>
            
            <div class="navbar-nav ms-auto">
                <div class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown">
                        <i class="fas fa-user-circle me-1"></i>
                        <?php echo htmlspecialchars($username); ?>
                    </a>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="../public/index.php" target="_blank">
                            <i class="fas fa-external-link-alt me-2"></i>Website bekijken
                        </a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item" href="logout.php">
                            <i class="fas fa-sign-out-alt me-2"></i>Uitloggen
                        </a></li>
                    </ul>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="container-fluid mt-4">
        
        <?php if (isset($error)): ?>
            <div class="alert alert-danger" role="alert">
                <i class="fas fa-exclamation-triangle me-2"></i>
                <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>
        
        <!-- Welcome Card -->
        <div class="welcome-card">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h2 class="mb-2">Welkom terug, <?php echo htmlspecialchars($username); ?>!</h2>
                    <p class="mb-0">Hier is een overzicht van je nieuwswebsite vandaag.</p>
                </div>
                <div class="col-md-4 text-end">
                    <i class="fas fa-chart-line fa-3x opacity-50"></i>
                </div>
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="row">
            <div class="col-lg-3 col-md-6">
                <div class="stat-card">
                    <div class="stat-icon" style="background: var(--primary-color);">
                        <i class="fas fa-newspaper"></i>
                    </div>
                    <div class="stat-number text-primary"><?php echo number_format($totalArticles); ?></div>
                    <div class="stat-label">Totaal Artikelen</div>
                </div>
            </div>
            
            <div class="col-lg-3 col-md-6">
                <div class="stat-card">
                    <div class="stat-icon" style="background: var(--success-color);">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <div class="stat-number text-success"><?php echo number_format($publishedArticles); ?></div>
                    <div class="stat-label">Gepubliceerd</div>
                </div>
            </div>
            
            <div class="col-lg-3 col-md-6">
                <div class="stat-card">
                    <div class="stat-icon" style="background: var(--secondary-color);">
                        <i class="fas fa-eye"></i>
                    </div>
                    <div class="stat-number text-info"><?php echo number_format($totalViews); ?></div>
                    <div class="stat-label">Totaal Views</div>
                </div>
            </div>
            
            <div class="col-lg-3 col-md-6">
                <div class="stat-card">
                    <div class="stat-icon" style="background: var(--warning-color);">
                        <i class="fas fa-comments"></i>
                    </div>
                    <div class="stat-number text-warning"><?php echo number_format($pendingComments); ?></div>
                    <div class="stat-label">Wachtende Reacties</div>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Recent Articles -->
            <div class="col-lg-8">
                <div class="recent-articles">
                    <h4 class="mb-4">
                        <i class="fas fa-newspaper me-2"></i>
                        Recente Artikelen
                    </h4>
                    
                    <?php if (empty($recentArticles)): ?>
                        <div class="text-center py-4 text-muted">
                            <i class="fas fa-newspaper fa-2x mb-3"></i>
                            <p>Nog geen artikelen aangemaakt.</p>
                        </div>
                    <?php else: ?>
                        <?php foreach ($recentArticles as $article): ?>
                        <div class="article-item">
                            <div class="row align-items-center">
                                <div class="col-md-8">
                                    <h6 class="mb-1"><?php echo htmlspecialchars($article['title']); ?></h6>
                                    <small class="text-muted">
                                        <?php echo htmlspecialchars($article['category_name']); ?> • 
                                        <?php echo date('d-m-Y H:i', strtotime($article['created_at'])); ?>
                                    </small>
                                </div>
                                <div class="col-md-4 text-end">
                                    <span class="badge bg-<?php echo $article['is_published'] ? 'success' : 'warning'; ?>">
                                        <?php echo $article['is_published'] ? 'Gepubliceerd' : 'Concept'; ?>
                                    </span>
                                    <small class="text-muted ms-2">
                                        <i class="fas fa-eye"></i> <?php echo number_format($article['read_count']); ?>
                                    </small>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="col-lg-4">
                <div class="quick-actions">
                    <h4 class="mb-4">
                        <i class="fas fa-bolt me-2"></i>
                        Snelle Acties
                    </h4>
                    
                    <a href="create_article.php" class="action-btn btn-primary-gradient">
                        <i class="fas fa-plus-circle me-2"></i>
                        Nieuw Artikel
                    </a>
                    
                    <a href="manage_articles.php" class="action-btn btn-success-gradient">
                        <i class="fas fa-edit me-2"></i>
                        Artikelen Beheren
                    </a>
                    
                    <a href="manage_categories.php" class="action-btn btn-warning-gradient">
                        <i class="fas fa-list me-2"></i>
                        Categorieën
                    </a>
                    
                    <?php if ($pendingComments > 0): ?>
                    <a href="manage_comments.php" class="action-btn" style="background: var(--danger-color); color: white;">
                        <i class="fas fa-comments me-2"></i>
                        Reacties Modereren
                        <span class="badge bg-light text-dark ms-2"><?php echo $pendingComments; ?></span>
                    </a>
                    <?php endif; ?>
                </div>
                
                <!-- Info Card -->
                <div class="quick-actions mt-4">
                    <h5 class="mb-3">
                        <i class="fas fa-info-circle me-2"></i>
                        Systeem Info
                    </h5>
                    <p class="small text-muted mb-2">
                        <strong>Ingelogd als:</strong> <?php echo htmlspecialchars($username); ?><br>
                        <strong>Rol:</strong> <?php echo ucfirst($role); ?><br>
                        <strong>Laatste login:</strong> <?php echo date('d-m-Y H:i'); ?>
                    </p>
                    
                    <div class="mt-3">
                        <a href="../public/index.php" target="_blank" class="btn btn-outline-primary btn-sm w-100">
                            <i class="fas fa-external-link-alt me-2"></i>
                            Website Bekijken
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Auto refresh elke 5 minuten
        setTimeout(function() {
            location.reload();
        }, 300000);
        
        // Real-time clock
        function updateTime() {
            const now = new Date();
            const timeString = now.toLocaleString('nl-NL');
            document.title = `Dashboard - ${timeString}`;
        }
        
        setInterval(updateTime, 60000);
    </script>
</body>
</html>