<?php
// Error reporting voor debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once '../config/config/database.php';

try {
    $db = getDB();
    
    // Haal basis data op
    $categoryId = isset($_GET['category']) ? (int)$_GET['category'] : null;
    
    // Haal artikelen op
    if ($categoryId) {
        $stmt = $db->prepare("SELECT a.*, c.name as category_name, u.username as author_name 
                              FROM news_articles a 
                              INNER JOIN categories c ON a.category_id = c.id 
                              INNER JOIN users u ON a.author_id = u.id 
                              WHERE a.is_published = 1 AND a.category_id = ?
                              ORDER BY a.published_at DESC");
        $stmt->execute([$categoryId]);
    } else {
        $stmt = $db->prepare("SELECT a.*, c.name as category_name, u.username as author_name 
                              FROM news_articles a 
                              INNER JOIN categories c ON a.category_id = c.id 
                              INNER JOIN users u ON a.author_id = u.id 
                              WHERE a.is_published = 1 
                              ORDER BY a.published_at DESC");
        $stmt->execute();
    }
    $articles = $stmt->fetchAll();
    
    // Haal categorieën op
    $stmt = $db->prepare("SELECT c.*, COUNT(a.id) as article_count 
                          FROM categories c 
                          LEFT JOIN news_articles a ON c.id = a.category_id AND a.is_published = 1
                          GROUP BY c.id ORDER BY c.name");
    $stmt->execute();
    $categories = $stmt->fetchAll();
    
    // Haal uitgelichte artikelen op
    $stmt = $db->prepare("SELECT a.*, c.name as category_name 
                          FROM news_articles a 
                          INNER JOIN categories c ON a.category_id = c.id 
                          WHERE a.is_published = 1 AND a.is_featured = 1 
                          ORDER BY a.published_at DESC LIMIT 3");
    $stmt->execute();
    $featuredArticles = $stmt->fetchAll();
    
    // Haal meest gelezen op
    $stmt = $db->prepare("SELECT a.*, c.name as category_name 
                          FROM news_articles a 
                          INNER JOIN categories c ON a.category_id = c.id 
                          WHERE a.is_published = 1 
                          ORDER BY a.read_count DESC LIMIT 5");
    $stmt->execute();
    $mostReadArticles = $stmt->fetchAll();
    
    // Huidige categorie info
    $currentCategory = null;
    if ($categoryId) {
        $stmt = $db->prepare("SELECT * FROM categories WHERE id = ?");
        $stmt->execute([$categoryId]);
        $currentCategory = $stmt->fetch();
    }
    
} catch (Exception $e) {
    die("Database fout: " . $e->getMessage());
}

$pageTitle = $currentCategory ? 'Categorie: ' . $currentCategory['name'] . ' - Nieuwswebsite' : 'Nieuwswebsite - Actueel Nieuws';
?>
<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($pageTitle); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --primary-color: #2c3e50;
            --secondary-color: #3498db;
            --accent-color: #e74c3c;
            --text-color: #2c3e50;
            --light-bg: #f8f9fa;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: var(--text-color);
        }
        
        .navbar {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        .navbar-brand {
            font-weight: 700;
            font-size: 1.5rem;
        }
        
        .hero-section {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            color: white;
            padding: 4rem 0 2rem;
            margin-bottom: 2rem;
        }
        
        .featured-article {
            background: white;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            transition: transform 0.3s ease;
            margin-bottom: 2rem;
        }
        
        .featured-article:hover {
            transform: translateY(-5px);
        }
        
        .article-card {
            background: white;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(0,0,0,0.08);
            transition: all 0.3s ease;
            margin-bottom: 2rem;
            border: none;
        }
        
        .article-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.15);
        }
        
        .article-image {
            height: 200px;
            background: linear-gradient(45deg, #667eea 0%, #764ba2 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 3rem;
        }
        
        .article-meta {
            color: #6c757d;
            font-size: 0.9rem;
        }
        
        .category-badge {
            background: var(--secondary-color);
            color: white;
            padding: 0.25rem 0.75rem;
            border-radius: 50px;
            font-size: 0.8rem;
            text-decoration: none;
            display: inline-block;
            margin-bottom: 0.5rem;
        }
        
        .category-badge:hover {
            background: var(--primary-color);
            color: white;
        }
        
        .sidebar {
            background: var(--light-bg);
            border-radius: 10px;
            padding: 1.5rem;
            margin-bottom: 2rem;
        }
        
        .sidebar h5 {
            color: var(--primary-color);
            border-bottom: 2px solid var(--secondary-color);
            padding-bottom: 0.5rem;
            margin-bottom: 1rem;
        }
        
        .read-count {
            color: var(--secondary-color);
            font-size: 0.9rem;
        }
        
        .search-box {
            background: rgba(255,255,255,0.1);
            border: 2px solid rgba(255,255,255,0.2);
            border-radius: 25px;
            color: white;
        }
        
        .search-box::placeholder {
            color: rgba(255,255,255,0.7);
        }
        
        .search-box:focus {
            background: rgba(255,255,255,0.2);
            border-color: rgba(255,255,255,0.5);
            box-shadow: none;
            color: white;
        }
        
        .btn-search {
            background: var(--accent-color);
            border: none;
            border-radius: 25px;
            padding: 0.5rem 1.5rem;
        }
        
        .btn-search:hover {
            background: #c0392b;
        }
        
        .footer {
            background: var(--primary-color);
            color: white;
            padding: 2rem 0;
            margin-top: 4rem;
        }
        
        .category-nav {
            background: white;
            border-radius: 10px;
            padding: 1rem;
            margin-bottom: 2rem;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        }
        
        .category-link {
            color: var(--text-color);
            text-decoration: none;
            padding: 0.5rem 1rem;
            border-radius: 5px;
            display: inline-block;
            margin: 0.25rem;
            transition: all 0.3s ease;
        }
        
        .category-link:hover, .category-link.active {
            background: var(--secondary-color);
            color: white;
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container">
            <a class="navbar-brand" href="index.php">
                <i class="fas fa-newspaper me-2"></i>
                Nieuwswebsite
            </a>
            
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link <?php echo !$currentCategory ? 'active' : ''; ?>" href="index.php">Home</a>
                    </li>
                    <?php foreach ($categories as $category): ?>
                        <li class="nav-item">
                            <a class="nav-link <?php echo $currentCategory && $currentCategory['id'] == $category['id'] ? 'active' : ''; ?>" 
                               href="index.php?category=<?php echo $category['id']; ?>">
                                <?php echo htmlspecialchars($category['name']); ?>
                                <span class="badge bg-light text-dark ms-1"><?php echo $category['article_count']; ?></span>
                            </a>
                        </li>
                    <?php endforeach; ?>
                </ul>
                
                <form class="d-flex" action="search.php" method="GET">
                    <input class="form-control search-box me-2" type="search" name="q" placeholder="Zoek nieuws..." 
                           value="<?php echo htmlspecialchars($_GET['q'] ?? ''); ?>">
                    <button class="btn btn-search" type="submit">
                        <i class="fas fa-search"></i>
                    </button>
                </form>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <?php if (!$currentCategory && !empty($featuredArticles)): ?>
    <section class="hero-section">
        <div class="container">
            <div class="row">
                <div class="col-12 text-center mb-4">
                    <h1 class="display-4 fw-bold">Actueel Nieuws</h1>
                    <p class="lead">Blijf op de hoogte van het laatste nieuws</p>
                </div>
            </div>
            
            <div class="row">
                <?php foreach (array_slice($featuredArticles, 0, 1) as $featured): ?>
                <div class="col-lg-8 mx-auto">
                    <div class="featured-article">
                        <div class="row g-0">
                            <div class="col-md-4">
                                <div class="article-image">
                                    <i class="fas fa-star"></i>
                                </div>
                            </div>
                            <div class="col-md-8">
                                <div class="card-body p-4">
                                    <a href="index.php?category=<?php echo $featured['category_id']; ?>" class="category-badge">
                                        <?php echo htmlspecialchars($featured['category_name']); ?>
                                    </a>
                                    <h3 class="card-title text-dark"><?php echo htmlspecialchars($featured['title']); ?></h3>
                                    <p class="card-text text-muted"><?php echo htmlspecialchars($featured['summary']); ?></p>
                                    <div class="article-meta mb-3">
                                        <i class="fas fa-calendar me-1"></i>
                                        <?php echo date('d-m-Y H:i', strtotime($featured['published_at'])); ?>
                                        <span class="ms-3">
                                            <i class="fas fa-eye me-1"></i>
                                            <?php echo number_format($featured['read_count']); ?> views
                                        </span>
                                    </div>
                                    <a href="article.php?id=<?php echo $featured['id']; ?>" class="btn btn-primary">
                                        Lees meer <i class="fas fa-arrow-right ms-1"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
    <?php endif; ?>

    <!-- Main Content -->
    <div class="container">
        <div class="row">
            <!-- Articles -->
            <div class="col-lg-8">
                <?php if ($currentCategory): ?>
                    <div class="mb-4">
                        <h2>Categorie: <?php echo htmlspecialchars($currentCategory['name']); ?></h2>
                        <p class="text-muted"><?php echo htmlspecialchars($currentCategory['description']); ?></p>
                    </div>
                <?php endif; ?>
                
                <?php if (empty($articles)): ?>
                    <div class="text-center py-5">
                        <i class="fas fa-newspaper fa-3x text-muted mb-3"></i>
                        <h3>Geen artikelen gevonden</h3>
                        <p class="text-muted">Er zijn momenteel geen artikelen beschikbaar in deze categorie.</p>
                    </div>
                <?php else: ?>
                    <div class="row">
                        <?php foreach ($articles as $article): ?>
                        <div class="col-md-6">
                            <div class="card article-card h-100">
                                <div class="article-image">
                                    <i class="fas fa-newspaper"></i>
                                </div>
                                <div class="card-body d-flex flex-column">
                                    <div class="mb-2">
                                        <a href="index.php?category=<?php echo $article['category_id']; ?>" class="category-badge">
                                            <?php echo htmlspecialchars($article['category_name']); ?>
                                        </a>
                                    </div>
                                    <h5 class="card-title">
                                        <a href="article.php?id=<?php echo $article['id']; ?>" class="text-decoration-none text-dark">
                                            <?php echo htmlspecialchars($article['title']); ?>
                                        </a>
                                    </h5>
                                    <p class="card-text text-muted flex-grow-1">
                                        <?php echo htmlspecialchars($article['summary']); ?>
                                    </p>
                                    <div class="article-meta mt-auto">
                                        <small class="text-muted">
                                            <i class="fas fa-user me-1"></i>
                                            <?php echo htmlspecialchars($article['author_name']); ?>
                                        </small>
                                        <br>
                                        <small class="text-muted">
                                            <i class="fas fa-calendar me-1"></i>
                                            <?php echo date('d-m-Y H:i', strtotime($article['published_at'])); ?>
                                            <span class="ms-2">
                                                <i class="fas fa-eye me-1"></i>
                                                <?php echo number_format($article['read_count']); ?>
                                            </span>
                                        </small>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Sidebar -->
            <div class="col-lg-4">
                <!-- Categories -->
                <div class="sidebar">
                    <h5><i class="fas fa-list me-2"></i>Categorieën</h5>
                    <div class="category-nav p-0 bg-transparent shadow-none">
                        <a href="index.php" class="category-link <?php echo !$currentCategory ? 'active' : ''; ?>">
                            <i class="fas fa-home me-1"></i> Alle nieuws
                        </a>
                        <?php foreach ($categories as $category): ?>
                            <a href="index.php?category=<?php echo $category['id']; ?>" 
                               class="category-link <?php echo $currentCategory && $currentCategory['id'] == $category['id'] ? 'active' : ''; ?>">
                                <?php echo htmlspecialchars($category['name']); ?>
                                <span class="badge bg-secondary ms-1"><?php echo $category['article_count']; ?></span>
                            </a>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- Most Read Articles -->
                <?php if (!empty($mostReadArticles)): ?>
                <div class="sidebar">
                    <h5><i class="fas fa-fire me-2"></i>Meest Gelezen</h5>
                    <?php foreach ($mostReadArticles as $index => $popular): ?>
                    <div class="d-flex align-items-start mb-3">
                        <div class="badge bg-primary rounded-pill me-2 mt-1" style="min-width: 25px;">
                            <?php echo $index + 1; ?>
                        </div>
                        <div class="flex-grow-1">
                            <h6 class="mb-1">
                                <a href="article.php?id=<?php echo $popular['id']; ?>" class="text-decoration-none text-dark">
                                    <?php echo htmlspecialchars($popular['title']); ?>
                                </a>
                            </h6>
                            <small class="text-muted">
                                <span class="category-badge py-1 px-2 me-2" style="font-size: 0.7rem;">
                                    <?php echo htmlspecialchars($popular['category_name']); ?>
                                </span>
                                <span class="read-count">
                                    <i class="fas fa-eye me-1"></i>
                                    <?php echo number_format($popular['read_count']); ?>
                                </span>
                            </small>
                        </div>
                    </div>
                    <?php if ($index < count($mostReadArticles) - 1): ?>
                        <hr class="my-2">
                    <?php endif; ?>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>

                <!-- Quick Stats -->
                <div class="sidebar">
                    <h5><i class="fas fa-chart-bar me-2"></i>Statistieken</h5>
                    <div class="row text-center">
                        <div class="col-6">
                            <div class="p-2">
                                <h4 class="text-primary mb-0"><?php echo count($articles); ?></h4>
                                <small class="text-muted">Artikelen</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="p-2">
                                <h4 class="text-success mb-0"><?php echo count($categories); ?></h4>
                                <small class="text-muted">Categorieën</small>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Admin Link -->
                <div class="sidebar text-center">
                    <h5><i class="fas fa-cog me-2"></i>Beheer</h5>
                    <p class="small text-muted">Toegang tot het beheerpaneel</p>
                    <a href="../admin/login.php" class="btn btn-primary btn-sm w-100">
                        <i class="fas fa-sign-in-alt me-1"></i>
                        Admin Login
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="row">
                <div class="col-md-6">
                    <h5>Nieuwswebsite</h5>
                    <p class="mb-0">Uw betrouwbare bron voor actueel nieuws</p>
                </div>
                <div class="col-md-6 text-end">
                    <div class="social-links">
                        <a href="#" class="text-white me-3"><i class="fab fa-facebook"></i></a>
                        <a href="#" class="text-white me-3"><i class="fab fa-twitter"></i></a>
                        <a href="#" class="text-white me-3"><i class="fab fa-linkedin"></i></a>
                        <a href="#" class="text-white"><i class="fab fa-instagram"></i></a>
                    </div>
                    <small class="mt-2 d-block">
                        © <?php echo date('Y'); ?> Nieuwswebsite. Alle rechten voorbehouden.
                    </small>
                </div>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Smooth scrolling voor interne links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                document.querySelector(this.getAttribute('href')).scrollIntoView({
                    behavior: 'smooth'
                });
            });
        });
    </script>
</body>
</html>