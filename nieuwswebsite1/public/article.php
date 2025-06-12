<h6>
                            <a href="article.php?id=<?= $related['id'] ?>" class="text-decoration-none text-dark">
                                <?= htmlspecialchars($related['title']) ?>
                            </a>
                        </h6>
                        <small class="text-muted">
                            <?= date('d-m-Y', strtotime($related['published_at'])) ?>
                            <span class="ms-2">
                                <i class="fas fa-eye me-1"></i>
                                <?= number_format($related['read_count']) ?>
                            </span>
                        </small>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>

                <!-- Back to Category -->
                <div class="related-articles mt-4">
                    <h5 class="mb-3">
                        <i class="fas fa-arrow-left me-2"></i>
                        Navigatie
                    </h5>
                    <div class="d-grid gap-2">
                        <a href="index.php?category=<?= $article['category_id'] ?>" class="btn btn-outline-primary">
                            <i class="fas fa-list me-2"></i>
                            Meer in <?= htmlspecialchars($article['category_name']) ?>
                        </a>
                        <a href="index.php" class="btn btn-outline-secondary">
                            <i class="fas fa-home me-2"></i>
                            Terug naar home
                        </a>
                    </div>
                </div>

                <!-- Quick Share -->
                <div class="related-articles mt-4 text-center">
                    <h5 class="mb-3">
                        <i class="fas fa-heart me-2"></i>
                        Vond je dit interessant?
                    </h5>
                    <p class="text-muted small mb-3">Deel het met je vrienden!</p>
                    <a href="share.php?id=<?= $article['id'] ?>" class="btn btn-success btn-sm">
                        <i class="fas fa-user-friends me-2"></i>
                        Tip een vriend
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-dark text-white py-4 mt-5">
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
                        © <?= date('Y') ?> Nieuwswebsite. Alle rechten voorbehouden.
                    </small>
                </div>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Social sharing functionality
        document.addEventListener('DOMContentLoaded', function() {
            const shareButtons = {
                facebook: document.querySelector('.share-btn.facebook'),
                twitter: document.querySelector('.share-btn.twitter'),
                linkedin: document.querySelector('.share-btn.linkedin')
            };
            
            const articleTitle = <?= json_encode($article['title']) ?>;
            const articleUrl = window.location.href;
            const articleSummary = <?= json_encode($article['summary']) ?>;
            
            // Facebook sharing
            if (shareButtons.facebook) {
                shareButtons.facebook.href = `https://www.facebook.com/sharer/sharer.php?u=${encodeURIComponent(articleUrl)}`;
                shareButtons.facebook.addEventListener('click', function(e) {
                    e.preventDefault();
                    window.open(this.href, 'facebook-share', 'width=580,height=400');
                });
            }
            
            // Twitter sharing
            if (shareButtons.twitter) {
                const twitterText = `${articleTitle} - ${articleSummary}`;
                shareButtons.twitter.href = `https://twitter.com/intent/tweet?text=${encodeURIComponent(twitterText)}&url=${encodeURIComponent(articleUrl)}`;
                shareButtons.twitter.addEventListener('click', function(e) {
                    e.preventDefault();
                    window.open(this.href, 'twitter-share', 'width=580,height=400');
                });
            }
            
            // LinkedIn sharing
            if (shareButtons.linkedin) {
                shareButtons.linkedin.href = `https://www.linkedin.com/sharing/share-offsite/?url=${encodeURIComponent(articleUrl)}`;
                shareButtons.linkedin.addEventListener('click', function(e) {
                    e.preventDefault();
                    window.open(this.href, 'linkedin-share', 'width=580,height=400');
                });
            }
        });
        
        // Auto-hide success alerts
        setTimeout(function() {
            const alerts = document.querySelectorAll('.alert-success');
            alerts.forEach(function(alert) {
                alert.style.transition = 'opacity 0.5s';
                alert.style.opacity = '0';
                setTimeout(() => alert.remove(), 500);
            });
        }, 5000);
        
        // Character counter for comment textarea
        const commentTextarea = document.getElementById('content');
        if (commentTextarea) {
            const maxLength = 1000;
            const counterDiv = document.createElement('div');
            counterDiv.className = 'text-muted small text-end mt-1';
            counterDiv.innerHTML = `<span id="char-count">0</span> / ${maxLength} karakters`;
            commentTextarea.parentNode.appendChild(counterDiv);
            
            const charCount = document.getElementById('char-count');
            
            commentTextarea.addEventListener('input', function() {
                const currentLength = this.value.length;
                charCount.textContent = currentLength;
                
                if (currentLength > maxLength * 0.9) {
                    counterDiv.className = 'text-warning small text-end mt-1';
                }
                if (currentLength > maxLength) {
                    counterDiv.className = 'text-danger small text-end mt-1';
                } else if (currentLength <= maxLength * 0.9) {
                    counterDiv.className = 'text-muted small text-end mt-1';
                }
            });
        }
    </script>
</body>
</html><?php
require_once '../controllers/NewsController.php';
require_once '../models/CommentModel.php';

$newsController = new NewsController();
$commentModel = new CommentModel();

$articleId = DatabaseUtils::validateInt($_GET['id'] ?? '');

if (!$articleId) {
    header('Location: index.php');
    exit;
}

// Handle comment submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_comment'])) {
    $commentData = [
        'author_name' => DatabaseUtils::sanitizeString($_POST['author_name'] ?? ''),
        'author_email' => DatabaseUtils::sanitizeString($_POST['author_email'] ?? ''),
        'content' => DatabaseUtils::sanitizeString($_POST['content'] ?? '')
    ];
    
    $commentErrors = $commentModel->validateComment($commentData);
    
    if (empty($commentErrors)) {
        if ($commentModel->addComment($articleId, $commentData['author_name'], $commentData['author_email'], $commentData['content'])) {
            $commentSuccess = 'Uw reactie is verzonden en wacht op goedkeuring.';
            // Clear form data
            $commentData = [];
        } else {
            $commentErrors[] = 'Er ging iets mis bij het verzenden van uw reactie.';
        }
    }
}

$result = $newsController->show($articleId);

if ($result['view'] === '404') {
    header('HTTP/1.0 404 Not Found');
    echo '<h1>Artikel niet gevonden</h1>';
    exit;
}

$article = $result['data']['article'];
$comments = $result['data']['comments'];
$commentCount = $result['data']['commentCount'];
$relatedArticles = $result['data']['relatedArticles'];

$pageTitle = htmlspecialchars($article['title']) . ' - Nieuwswebsite';
?>
<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $pageTitle ?></title>
    <meta name="description" content="<?= htmlspecialchars($article['summary']) ?>">
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
            line-height: 1.7;
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
        
        .article-header {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            color: white;
            padding: 3rem 0;
        }
        
        .article-content {
            font-size: 1.1rem;
            line-height: 1.8;
        }
        
        .article-content p {
            margin-bottom: 1.5rem;
        }
        
        .article-meta {
            background: var(--light-bg);
            border-radius: 10px;
            padding: 1.5rem;
            margin: 2rem 0;
        }
        
        .category-badge {
            background: var(--secondary-color);
            color: white;
            padding: 0.5rem 1rem;
            border-radius: 50px;
            font-size: 0.9rem;
            text-decoration: none;
            display: inline-block;
        }
        
        .category-badge:hover {
            background: var(--primary-color);
            color: white;
        }
        
        .share-buttons {
            background: white;
            border-radius: 10px;
            padding: 1.5rem;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            margin: 2rem 0;
        }
        
        .share-btn {
            display: inline-block;
            padding: 0.75rem;
            margin: 0.25rem;
            border-radius: 50px;
            color: white;
            text-decoration: none;
            font-size: 1.2rem;
            transition: transform 0.2s;
        }
        
        .share-btn:hover {
            transform: translateY(-2px);
            color: white;
        }
        
        .share-btn.facebook { background: #3b5998; }
        .share-btn.twitter { background: #1da1f2; }
        .share-btn.linkedin { background: #0077b5; }
        .share-btn.email { background: var(--accent-color); }
        
        .comment-section {
            background: white;
            border-radius: 10px;
            padding: 2rem;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            margin: 2rem 0;
        }
        
        .comment {
            border-left: 4px solid var(--secondary-color);
            padding: 1rem;
            margin: 1rem 0;
            background: var(--light-bg);
            border-radius: 0 10px 10px 0;
        }
        
        .comment-author {
            font-weight: 600;
            color: var(--primary-color);
        }
        
        .comment-date {
            color: #6c757d;
            font-size: 0.9rem;
        }
        
        .comment-form {
            background: var(--light-bg);
            border-radius: 10px;
            padding: 2rem;
            margin-top: 2rem;
        }
        
        .related-articles {
            background: white;
            border-radius: 10px;
            padding: 2rem;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        }
        
        .related-article {
            border-bottom: 1px solid #e9ecef;
            padding: 1rem 0;
        }
        
        .related-article:last-child {
            border-bottom: none;
        }
        
        .article-image-placeholder {
            height: 300px;
            background: linear-gradient(45deg, #667eea 0%, #764ba2 100%);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 4rem;
            margin: 2rem 0;
        }
        
        .breadcrumb {
            background: transparent;
            padding: 0;
        }
        
        .breadcrumb-item a {
            color: rgba(255,255,255,0.8);
            text-decoration: none;
        }
        
        .breadcrumb-item a:hover {
            color: white;
        }
        
        .breadcrumb-item.active {
            color: rgba(255,255,255,0.6);
        }
        
        .btn-share-article {
            background: var(--accent-color);
            border: none;
            border-radius: 25px;
            padding: 0.75rem 2rem;
            color: white;
            text-decoration: none;
            display: inline-block;
            transition: all 0.3s ease;
        }
        
        .btn-share-article:hover {
            background: #c0392b;
            transform: translateY(-2px);
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
                        <a class="nav-link" href="index.php">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="index.php?category=<?= $article['category_id'] ?>">
                            <?= htmlspecialchars($article['category_name']) ?>
                        </a>
                    </li>
                </ul>
                
                <form class="d-flex" action="search.php" method="GET">
                    <input class="form-control me-2" type="search" name="q" placeholder="Zoek nieuws...">
                    <button class="btn btn-outline-light" type="submit">
                        <i class="fas fa-search"></i>
                    </button>
                </form>
            </div>
        </div>
    </nav>

    <!-- Article Header -->
    <section class="article-header">
        <div class="container">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                    <li class="breadcrumb-item">
                        <a href="index.php?category=<?= $article['category_id'] ?>">
                            <?= htmlspecialchars($article['category_name']) ?>
                        </a>
                    </li>
                    <li class="breadcrumb-item active"><?= htmlspecialchars($article['title']) ?></li>
                </ol>
            </nav>
            
            <div class="row">
                <div class="col-lg-8">
                    <a href="index.php?category=<?= $article['category_id'] ?>" class="category-badge mb-3">
                        <?= htmlspecialchars($article['category_name']) ?>
                    </a>
                    <h1 class="display-5 fw-bold mb-3"><?= htmlspecialchars($article['title']) ?></h1>
                    <p class="lead"><?= htmlspecialchars($article['summary']) ?></p>
                </div>
            </div>
        </div>
    </section>

    <!-- Main Content -->
    <div class="container">
        <div class="row">
            <div class="col-lg-8">
                <!-- Article Meta -->
                <div class="article-meta">
                    <div class="row align-items-center">
                        <div class="col-md-6">
                            <div class="d-flex align-items-center">
                                <div class="me-3">
                                    <div class="bg-primary rounded-circle d-flex align-items-center justify-content-center" 
                                         style="width: 50px; height: 50px;">
                                        <i class="fas fa-user text-white"></i>
                                    </div>
                                </div>
                                <div>
                                    <h6 class="mb-0"><?= htmlspecialchars($article['author_name']) ?></h6>
                                    <small class="text-muted">Auteur</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 text-end">
                            <div class="mb-1">
                                <i class="fas fa-calendar me-2"></i>
                                <strong><?= date('d-m-Y H:i', strtotime($article['published_at'])) ?></strong>
                            </div>
                            <div>
                                <i class="fas fa-eye me-2"></i>
                                <span class="text-muted"><?= number_format($article['read_count']) ?> weergaven</span>
                                <span class="ms-3">
                                    <i class="fas fa-comments me-2"></i>
                                    <span class="text-muted"><?= $commentCount ?> reacties</span>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Article Image -->
                <?php if ($article['image_url']): ?>
                    <img src="<?= htmlspecialchars($article['image_url']) ?>" class="img-fluid rounded mb-4" alt="<?= htmlspecialchars($article['title']) ?>">
                <?php else: ?>
                    <div class="article-image-placeholder">
                        <i class="fas fa-image"></i>
                    </div>
                <?php endif; ?>

                <!-- Article Content -->
                <div class="article-content">
                    <?= nl2br(htmlspecialchars($article['content'])) ?>
                </div>

                <!-- Share Buttons -->
                <div class="share-buttons">
                    <h5 class="mb-3"><i class="fas fa-share-alt me-2"></i>Deel dit artikel</h5>
                    <div class="d-flex align-items-center flex-wrap">
                        <a href="#" class="share-btn facebook" title="Deel op Facebook">
                            <i class="fab fa-facebook-f"></i>
                        </a>
                        <a href="#" class="share-btn twitter" title="Deel op Twitter">
                            <i class="fab fa-twitter"></i>
                        </a>
                        <a href="#" class="share-btn linkedin" title="Deel op LinkedIn">
                            <i class="fab fa-linkedin-in"></i>
                        </a>
                        <a href="mailto:?subject=<?= urlencode($article['title']) ?>&body=<?= urlencode($article['summary'] . "\n\nLees meer: " . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']) ?>" 
                           class="share-btn email" title="Deel via email">
                            <i class="fas fa-envelope"></i>
                        </a>
                        <a href="share.php?id=<?= $article['id'] ?>" class="btn-share-article ms-3">
                            <i class="fas fa-user-friends me-2"></i>
                            Tip een vriend
                        </a>
                    </div>
                </div>

                <!-- Comments Section -->
                <div class="comment-section">
                    <h4 class="mb-4">
                        <i class="fas fa-comments me-2"></i>
                        Reacties (<?= $commentCount ?>)
                    </h4>
                    
                    <?php if (isset($commentSuccess)): ?>
                        <div class="alert alert-success" role="alert">
                            <i class="fas fa-check-circle me-2"></i>
                            <?= $commentSuccess ?>
                        </div>
                    <?php endif; ?>
                    
                    <?php if (!empty($commentErrors)): ?>
                        <div class="alert alert-danger" role="alert">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            <ul class="mb-0">
                                <?php foreach ($commentErrors as $error): ?>
                                    <li><?= htmlspecialchars($error) ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endif; ?>

                    <!-- Existing Comments -->
                    <?php if (empty($comments)): ?>
                        <div class="text-center py-4 text-muted">
                            <i class="fas fa-comment-slash fa-2x mb-3"></i>
                            <p>Nog geen reacties. Wees de eerste om te reageren!</p>
                        </div>
                    <?php else: ?>
                        <?php foreach ($comments as $comment): ?>
                        <div class="comment">
                            <div class="comment-header d-flex justify-content-between align-items-start mb-2">
                                <span class="comment-author"><?= htmlspecialchars($comment['author_name']) ?></span>
                                <span class="comment-date">
                                    <?= date('d-m-Y H:i', strtotime($comment['created_at'])) ?>
                                </span>
                            </div>
                            <div class="comment-content">
                                <?= nl2br(htmlspecialchars($comment['content'])) ?>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    <?php endif; ?>

                    <!-- Comment Form -->
                    <div class="comment-form">
                        <h5 class="mb-3">Plaats een reactie</h5>
                        <form method="POST" action="">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="author_name" class="form-label">Naam *</label>
                                    <input type="text" class="form-control" id="author_name" name="author_name" 
                                           value="<?= htmlspecialchars($commentData['author_name'] ?? '') ?>" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="author_email" class="form-label">Email *</label>
                                    <input type="email" class="form-control" id="author_email" name="author_email" 
                                           value="<?= htmlspecialchars($commentData['author_email'] ?? '') ?>" required>
                                    <div class="form-text">Uw email adres wordt niet gepubliceerd.</div>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="content" class="form-label">Reactie *</label>
                                <textarea class="form-control" id="content" name="content" rows="4" 
                                          placeholder="Uw reactie..." required><?= htmlspecialchars($commentData['content'] ?? '') ?></textarea>
                            </div>
                            <button type="submit" name="add_comment" class="btn btn-primary">
                                <i class="fas fa-paper-plane me-2"></i>
                                Plaats reactie
                            </button>
                            <small class="text-muted ms-3">
                                * Reacties worden gecontroleerd voordat ze worden gepubliceerd.
                            </small>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="col-lg-4">
                <!-- Related Articles -->
                <?php if (!empty($relatedArticles)): ?>
                <div class="related-articles">
                    <h5 class="mb-3">
                        <i class="fas fa-newspaper me-2"></i>
                        Gerelateerde Artikelen
                    </h5>
                    <?php foreach ($relatedArticles as $related): ?>
                    <div class="related-article">
                        <h6>
                            <a href="article.php?id=<?= $related['id'] ?>" class="text-decoration-none text-dar