<?php
require_once '../controllers/NewsController.php';

$newsController = new NewsController();
$articleId = DatabaseUtils::validateInt($_GET['id'] ?? '');

if (!$articleId) {
    header('Location: index.php');
    exit;
}

$result = $newsController->shareArticle($articleId);

if ($result['view'] === '404') {
    header('HTTP/1.0 404 Not Found');
    echo '<h1>Artikel niet gevonden</h1>';
    exit;
}

$article = $result['data']['article'];
$errors = $result['data']['errors'] ?? [];
$formData = $result['data']['formData'] ?? [];

// Check if it's a success page
if ($result['view'] === 'share_success') {
    $recipient = $result['data']['recipient'];
    $pageTitle = 'Artikel gedeeld - Nieuwswebsite';
} else {
    $pageTitle = 'Deel artikel: ' . htmlspecialchars($article['title']) . ' - Nieuwswebsite';
}
?>
<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $pageTitle ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --primary-color: #2c3e50;
            --secondary-color: #3498db;
            --accent-color: #e74c3c;
            --success-color: #27ae60;
            --text-color: #2c3e50;
            --light-bg: #f8f9fa;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: var(--text-color);
            background: var(--light-bg);
        }
        
        .navbar {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        .navbar-brand {
            font-weight: 700;
            font-size: 1.5rem;
        }
        
        .share-header {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            color: white;
            padding: 3rem 0;
        }
        
        .share-container {
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            padding: 2rem;
            margin: -2rem 0 2rem;
            position: relative;
        }
        
        .article-preview {
            background: var(--light-bg);
            border-radius: 10px;
            padding: 1.5rem;
            margin-bottom: 2rem;
            border-left: 4px solid var(--secondary-color);
        }
        
        .form-control {
            border-radius: 8px;
            border: 2px solid #e9ecef;
            padding: 0.75rem 1rem;
            transition: all 0.3s ease;
        }
        
        .form-control:focus {
            border-color: var(--secondary-color);
            box-shadow: 0 0 0 0.2rem rgba(52, 152, 219, 0.25);
        }
        
        .btn-share {
            background: linear-gradient(135deg, var(--secondary-color) 0%, var(--primary-color) 100%);
            border: none;
            border-radius: 25px;
            padding: 0.75rem 2rem;
            font-weight: 600;
            color: white;
            transition: all 0.3s ease;
        }
        
        .btn-share:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
            color: white;
        }
        
        .success-animation {
            text-align: center;
            padding: 3rem 0;
        }
        
        .success-icon {
            font-size: 4rem;
            color: var(--success-color);
            animation: bounceIn 0.8s ease-out;
        }
        
        @keyframes bounceIn {
            0% { transform: scale(0.3); opacity: 0; }
            50% { transform: scale(1.05); }
            70% { transform: scale(0.9); }
            100% { transform: scale(1); opacity: 1; }
        }
        
        .share-tips {
            background: #e8f4f8;
            border-radius: 10px;
            padding: 1.5rem;
            margin-top: 2rem;
        }
        
        .share-tips h6 {
            color: var(--primary-color);
            margin-bottom: 1rem;
        }
        
        .share-tips ul {
            margin-bottom: 0;
        }
        
        .share-tips li {
            margin-bottom: 0.5rem;
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
                        <a class="nav-link" href="article.php?id=<?= $article['id'] ?>">
                            Terug naar artikel
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Share Header -->
    <section class="share-header">
        <div class="container">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                    <li class="breadcrumb-item"><a href="article.php?id=<?= $article['id'] ?>">Artikel</a></li>
                    <li class="breadcrumb-item active">Tip een vriend</li>
                </ol>
            </nav>
            
            <div class="text-center">
                <h1 class="display-5 fw-bold mb-3">
                    <i class="fas fa-user-friends me-3"></i>
                    <?php if ($result['view'] === 'share_success'): ?>
                        Artikel Gedeeld!
                    <?php else: ?>
                        Tip een Vriend
                    <?php endif; ?>
                </h1>
                <p class="lead">
                    <?php if ($result['view'] === 'share_success'): ?>
                        Je aanbeveling is succesvol verzonden
                    <?php else: ?>
                        Deel dit interessante artikel met je vrienden
                    <?php endif; ?>
                </p>
            </div>
        </div>
    </section>

    <!-- Main Content -->
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="share-container">
                    
                    <?php if ($result['view'] === 'share_success'): ?>
                        <!-- Success Message -->
                        <div class="success-animation">
                            <i class="fas fa-check-circle success-icon"></i>
                            <h3 class="mt-3 mb-4">Succesvol verzonden!</h3>
                            <p class="lead">
                                Het artikel "<strong><?= htmlspecialchars($article['title']) ?></strong>" 
                                is succesvol gedeeld met <strong><?= htmlspecialchars($recipient) ?></strong>.
                            </p>
                            <p class="text-muted">
                                Je vriend ontvangt een email met de link naar dit artikel en jouw persoonlijke bericht.
                            </p>
                            
                            <div class="mt-4">
                                <a href="article.php?id=<?= $article['id'] ?>" class="btn btn-primary me-2">
                                    <i class="fas fa-arrow-left me-2"></i>
                                    Terug naar artikel
                                </a>
                                <a href="index.php" class="btn btn-outline-secondary">
                                    <i class="fas fa-home me-2"></i>
                                    Naar homepage
                                </a>
                            </div>
                        </div>
                        
                    <?php else: ?>
                        <!-- Share Form -->
                        
                        <!-- Article Preview -->
                        <div class="article-preview">
                            <h5 class="mb-2">
                                <i class="fas fa-newspaper me-2 text-primary"></i>
                                Je gaat dit artikel delen:
                            </h5>
                            <h4 class="mb-2"><?= htmlspecialchars($article['title']) ?></h4>
                            <p class="text-muted mb-2"><?= htmlspecialchars($article['summary']) ?></p>
                            <small class="text-muted">
                                <span class="badge bg-primary"><?= htmlspecialchars($article['category_name']) ?></span>
                                <span class="ms-2">
                                    <i class="fas fa-eye me-1"></i>
                                    <?= number_format($article['read_count']) ?> weergaven
                                </span>
                            </small>
                        </div>

                        <!-- Error Messages -->
                        <?php if (!empty($errors)): ?>
                            <div class="alert alert-danger" role="alert">
                                <i class="fas fa-exclamation-triangle me-2"></i>
                                <strong>Er zijn enkele problemen:</strong>
                                <ul class="mb-0 mt-2">
                                    <?php foreach ($errors as $error): ?>
                                        <li><?= htmlspecialchars($error) ?></li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                        <?php endif; ?>

                        <!-- Share Form -->
                        <form method="POST" action="">
                            <div class="row">
                                <!-- Sender Information -->
                                <div class="col-12">
                                    <h5 class="mb-3">
                                        <i class="fas fa-user me-2"></i>
                                        Jouw gegevens
                                    </h5>
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <label for="sender_name" class="form-label">Jouw naam *</label>
                                    <input type="text" class="form-control" id="sender_name" name="sender_name" 
                                           value="<?= htmlspecialchars($formData['sender_name'] ?? '') ?>" 
                                           placeholder="Bijv. Jan Janssen" required>
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <label for="sender_email" class="form-label">Jouw email *</label>
                                    <input type="email" class="form-control" id="sender_email" name="sender_email" 
                                           value="<?= htmlspecialchars($formData['sender_email'] ?? '') ?>" 
                                           placeholder="jan@email.nl" required>
                                </div>
                            </div>

                            <hr class="my-4">

                            <div class="row">
                                <!-- Recipient Information -->
                                <div class="col-12">
                                    <h5 class="mb-3">
                                        <i class="fas fa-user-friends me-2"></i>
                                        Gegevens van je vriend
                                    </h5>
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <label for="recipient_name" class="form-label">Naam van je vriend *</label>
                                    <input type="text" class="form-control" id="recipient_name" name="recipient_name" 
                                           value="<?= htmlspecialchars($formData['recipient_name'] ?? '') ?>" 
                                           placeholder="Bijv. Marie de Vries" required>
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <label for="recipient_email" class="form-label">Email van je vriend *</label>
                                    <input type="email" class="form-control" id="recipient_email" name="recipient_email" 
                                           value="<?= htmlspecialchars($formData['recipient_email'] ?? '') ?>" 
                                           placeholder="marie@email.nl" required>
                                </div>
                            </div>

                            <!-- Personal Message -->
                            <div class="mb-4">
                                <label for="message" class="form-label">
                                    Persoonlijk bericht <span class="text-muted">(optioneel)</span>
                                </label>
                                <textarea class="form-control" id="message" name="message" rows="4" 
                                          placeholder="Voeg een persoonlijk bericht toe voor je vriend..."
                                          maxlength="500"><?= htmlspecialchars($formData['message'] ?? '') ?></textarea>
                                <div class="form-text">
                                    <span id="char-count">0</span> / 500 karakters
                                </div>
                            </div>

                            <!-- Submit Button -->
                            <div class="text-center">
                                <button type="submit" class="btn btn-share btn-lg">
                                    <i class="fas fa-paper-plane me-2"></i>
                                    Verstuur Aanbeveling
                                </button>
                            </div>
                        </form>

                        <!-- Share Tips -->
                        <div class="share-tips">
                            <h6><i class="fas fa-lightbulb me-2"></i>Tips voor het delen:</h6>
                            <ul class="small">
                                <li>Voeg een persoonlijk bericht toe om je aanbeveling kracht bij te zetten</li>
                                <li>Controleer de email adressen zorgvuldig voordat je verstuurt</li>
                                <li>Je vriend ontvangt een email met de link naar het artikel</li>
                                <li>Je eigen email adres wordt niet gedeeld met de ontvanger</li>
                            </ul>
                        </div>
                        
                    <?php endif; ?>
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
                    <small>
                        © <?= date('Y') ?> Nieuwswebsite. Alle rechten voorbehouden.
                    </small>
                </div>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Character counter for message textarea
        document.addEventListener('DOMContentLoaded', function() {
            const messageTextarea = document.getElementById('message');
            const charCount = document.getElementById('char-count');
            
            if (messageTextarea && charCount) {
                function updateCharCount() {
                    const currentLength = messageTextarea.value.length;
                    charCount.textContent = currentLength;
                    
                    // Change color based on character count
                    if (currentLength > 450) {
                        charCount.parentElement.className = 'form-text text-danger';
                    } else if (currentLength > 400) {
                        charCount.parentElement.className = 'form-text text-warning';
                    } else {
                        charCount.parentElement.className = 'form-text text-muted';
                    }
                }
                
                // Initial count
                updateCharCount();
                
                // Update on input
                messageTextarea.addEventListener('input', updateCharCount);
            }
            
            // Form validation
            const form = document.querySelector('form');
            if (form) {
                form.addEventListener('submit', function(e) {
                    const requiredFields = form.querySelectorAll('[required]');
                    let isValid = true;
                    
                    requiredFields.forEach(function(field) {
                        if (!field.value.trim()) {
                            field.classList.add('is-invalid');
                            isValid = false;
                        } else {
                            field.classList.remove('is-invalid');
                        }
                    });
                    
                    // Email validation
                    const emailFields = form.querySelectorAll('input[type="email"]');
                    emailFields.forEach(function(field) {
                        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                        if (field.value && !emailRegex.test(field.value)) {
                            field.classList.add('is-invalid');
                            isValid = false;
                        }
                    });
                    
                    if (!isValid) {
                        e.preventDefault();
                        alert('Vul alle verplichte velden correct in.');
                    }
                });
            }
        });
    </script>
</body>
</html>