<?php
require_once '../models/NewsModel.php';
require_once '../models/CategoryModel.php';
require_once '../models/CommentModel.php';
require_once '../models/ShareModel.php';
require_once 'AuthController.php';

/**
 * NewsController - Controller voor nieuwsartikelen
 * Handelt alle nieuwsgerelateerde acties af
 */
class NewsController {
    private $newsModel;
    private $categoryModel;
    private $commentModel;
    private $shareModel;
    private $authController;
    
    public function __construct() {
        $this->newsModel = new NewsModel();
        $this->categoryModel = new CategoryModel();
        $this->commentModel = new CommentModel();
        $this->shareModel = new ShareModel();
        $this->authController = new AuthController();
    }
    
    /**
     * Toon alle nieuwsartikelen (publieke weergave)
     */
    public function index($categoryId = null) {
        $articles = $this->newsModel->getPublishedArticles(null, $categoryId);
        $categories = $this->categoryModel->getCategoriesWithCounts();
        $featuredArticles = $this->newsModel->getFeaturedArticles(3);
        $mostReadArticles = $this->newsModel->getMostReadArticles(5);
        
        return $this->renderPublicView('index', [
            'articles' => $articles,
            'categories' => $categories,
            'featuredArticles' => $featuredArticles,
            'mostReadArticles' => $mostReadArticles,
            'currentCategory' => $categoryId ? $this->categoryModel->getCategoryById($categoryId) : null
        ]);
    }
    
    /**
     * Toon specifiek artikel
     */
    public function show($id) {
        $article = $this->newsModel->getArticleById($id);
        
        if (!$article || !$article['is_published']) {
            return $this->renderPublicView('404');
        }
        
        // Verhoog leesteller
        $this->newsModel->incrementReadCount($id);
        
        // Haal reacties op
        $comments = $this->commentModel->getApprovedComments($id);
        $commentCount = $this->commentModel->getCommentCount($id);
        
        // Haal gerelateerde artikelen op
        $relatedArticles = $this->newsModel->getPublishedArticles(4, $article['category_id']);
        // Verwijder huidige artikel uit gerelateerde artikelen
        $relatedArticles = array_filter($relatedArticles, function($a) use ($id) {
            return $a['id'] != $id;
        });
        
        return $this->renderPublicView('article', [
            'article' => $article,
            'comments' => $comments,
            'commentCount' => $commentCount,
            'relatedArticles' => array_slice($relatedArticles, 0, 3)
        ]);
    }
    
    /**
     * Beheer overzicht (admin)
     */
    public function manage() {
        $this->authController->requireLogin();
        
        $articles = $this->newsModel->getAllArticles();
        $categories = $this->categoryModel->getAllCategories();
        
        return $this->renderAdminView('manage_articles', [
            'articles' => $articles,
            'categories' => $categories
        ]);
    }
    
    /**
     * Toon formulier voor nieuw artikel
     */
    public function create() {
        $this->authController->requireLogin();
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            return $this->store();
        }
        
        $categories = $this->categoryModel->getAllCategories();
        
        return $this->renderAdminView('create_article', [
            'categories' => $categories
        ]);
    }
    
    /**
     * Sla nieuw artikel op
     */
    public function store() {
        $this->authController->requireLogin();
        
        if (!$this->authController->validateCSRFToken($_POST['csrf_token'] ?? '')) {
            return $this->renderAdminView('create_article', [
                'error' => 'Ongeldige beveiligingstoken',
                'categories' => $this->categoryModel->getAllCategories()
            ]);
        }
        
        $data = $this->validateArticleData($_POST);
        
        if (!empty($data['errors'])) {
            return $this->renderAdminView('create_article', [
                'errors' => $data['errors'],
                'formData' => $_POST,
                'categories' => $this->categoryModel->getAllCategories()
            ]);
        }
        
        $articleData = [
            'title' => $data['title'],
            'content' => $data['content'],
            'summary' => $data['summary'],
            'category_id' => $data['category_id'],
            'author_id' => $_SESSION['user_id'],
            'image_url' => $data['image_url'],
            'is_published' => $data['is_published'],
            'is_featured' => $data['is_featured']
        ];
        
        if ($this->newsModel->addArticle($articleData)) {
            header('Location: manage_articles.php?success=' . urlencode('Artikel succesvol toegevoegd'));
            exit;
        } else {
            return $this->renderAdminView('create_article', [
                'error' => 'Fout bij opslaan van artikel',
                'formData' => $_POST,
                'categories' => $this->categoryModel->getAllCategories()
            ]);
        }
    }
    
    /**
     * Toon formulier voor artikel bewerken
     */
    public function edit($id) {
        $this->authController->requireLogin();
        
        $article = $this->newsModel->getArticleById($id);
        
        if (!$article) {
            header('Location: manage_articles.php?error=' . urlencode('Artikel niet gevonden'));
            exit;
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            return $this->update($id);
        }
        
        $categories = $this->categoryModel->getAllCategories();
        
        return $this->renderAdminView('edit_article', [
            'article' => $article,
            'categories' => $categories
        ]);
    }
    
    /**
     * Update artikel
     */
    public function update($id) {
        $this->authController->requireLogin();
        
        if (!$this->authController->validateCSRFToken($_POST['csrf_token'] ?? '')) {
            return $this->renderAdminView('edit_article', [
                'error' => 'Ongeldige beveiligingstoken',
                'article' => $this->newsModel->getArticleById($id),
                'categories' => $this->categoryModel->getAllCategories()
            ]);
        }
        
        $data = $this->validateArticleData($_POST);
        
        if (!empty($data['errors'])) {
            return $this->renderAdminView('edit_article', [
                'errors' => $data['errors'],
                'article' => array_merge($this->newsModel->getArticleById($id), $_POST),
                'categories' => $this->categoryModel->getAllCategories()
            ]);
        }
        
        $articleData = [
            'title' => $data['title'],
            'content' => $data['content'],
            'summary' => $data['summary'],
            'category_id' => $data['category_id'],
            'image_url' => $data['image_url'],
            'is_published' => $data['is_published'],
            'is_featured' => $data['is_featured']
        ];
        
        if ($this->newsModel->updateArticle($id, $articleData)) {
            header('Location: manage_articles.php?success=' . urlencode('Artikel succesvol bijgewerkt'));
            exit;
        } else {
            return $this->renderAdminView('edit_article', [
                'error' => 'Fout bij bijwerken van artikel',
                'article' => array_merge($this->newsModel->getArticleById($id), $_POST),
                'categories' => $this->categoryModel->getAllCategories()
            ]);
        }
    }
    
    /**
     * Verwijder artikel
     */
    public function delete($id) {
        $this->authController->requireLogin();
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!$this->authController->validateCSRFToken($_POST['csrf_token'] ?? '')) {
                header('Location: manage_articles.php?error=' . urlencode('Ongeldige beveiligingstoken'));
                exit;
            }
            
            if ($this->newsModel->deleteArticle($id)) {
                header('Location: manage_articles.php?success=' . urlencode('Artikel succesvol verwijderd'));
                exit;
            } else {
                header('Location: manage_articles.php?error=' . urlencode('Fout bij verwijderen van artikel'));
                exit;
            }
        }
        
        $article = $this->newsModel->getArticleById($id);
        
        if (!$article) {
            header('Location: manage_articles.php?error=' . urlencode('Artikel niet gevonden'));
            exit;
        }
        
        return $this->renderAdminView('delete_article', [
            'article' => $article
        ]);
    }
    
    /**
     * Zoek artikelen
     */
    public function search() {
        $searchTerm = DatabaseUtils::sanitizeString($_GET['q'] ?? '');
        
        if (empty($searchTerm)) {
            header('Location: index.php');
            exit;
        }
        
        $articles = $this->newsModel->searchArticles($searchTerm);
        $categories = $this->categoryModel->getCategoriesWithCounts();
        
        return $this->renderPublicView('search', [
            'articles' => $articles,
            'categories' => $categories,
            'searchTerm' => $searchTerm
        ]);
    }
    
    /**
     * Tip-een-vriend functionaliteit
     */
    public function shareArticle($id) {
        $article = $this->newsModel->getArticleById($id);
        
        if (!$article || !$article['is_published']) {
            return $this->renderPublicView('404');
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $shareData = [
                'sender_name' => DatabaseUtils::sanitizeString($_POST['sender_name'] ?? ''),
                'sender_email' => DatabaseUtils::sanitizeString($_POST['sender_email'] ?? ''),
                'recipient_name' => DatabaseUtils::sanitizeString($_POST['recipient_name'] ?? ''),
                'recipient_email' => DatabaseUtils::sanitizeString($_POST['recipient_email'] ?? ''),
                'message' => DatabaseUtils::sanitizeString($_POST['message'] ?? '')
            ];
            
            $errors = $this->shareModel->validateShareData($shareData);
            
            if (empty($errors)) {
                if ($this->shareModel->shareArticle($id, $shareData['sender_name'], $shareData['sender_email'], 
                    $shareData['recipient_name'], $shareData['recipient_email'], $shareData['message'])) {
                    
                    // Verstuur email (in productie)
                    $this->shareModel->sendShareEmail($shareData, $article);
                    
                    return $this->renderPublicView('share_success', [
                        'article' => $article,
                        'recipient' => $shareData['recipient_name']
                    ]);
                } else {
                    $errors[] = 'Er ging iets mis bij het versturen. Probeer het later opnieuw.';
                }
            }
            
            return $this->renderPublicView('share_article', [
                'article' => $article,
                'errors' => $errors,
                'formData' => $shareData
            ]);
        }
        
        return $this->renderPublicView('share_article', [
            'article' => $article
        ]);
    }
    
    /**
     * Valideer artikel data
     */
    private function validateArticleData($data) {
        $errors = [];
        $cleanData = [];
        
        // Title
        $cleanData['title'] = DatabaseUtils::sanitizeString($data['title'] ?? '');
        if (empty($cleanData['title']) || strlen($cleanData['title']) < 5) {
            $errors[] = 'Titel is verplicht (minimaal 5 karakters)';
        }
        
        // Content
        $cleanData['content'] = trim($data['content'] ?? '');
        if (empty($cleanData['content']) || strlen($cleanData['content']) < 50) {
            $errors[] = 'Inhoud is verplicht (minimaal 50 karakters)';
        }
        
        // Summary
        $cleanData['summary'] = DatabaseUtils::sanitizeString($data['summary'] ?? '');
        if (empty($cleanData['summary']) || strlen($cleanData['summary']) < 20) {
            $errors[] = 'Samenvatting is verplicht (minimaal 20 karakters)';
        }
        
        // Category
        $cleanData['category_id'] = DatabaseUtils::validateInt($data['category_id'] ?? '');
        if (!$cleanData['category_id']) {
            $errors[] = 'Geldige categorie is verplicht';
        } else {
            $category = $this->categoryModel->getCategoryById($cleanData['category_id']);
            if (!$category) {
                $errors[] = 'Geselecteerde categorie bestaat niet';
            }
        }
        
        // Image URL (optioneel)
        $cleanData['image_url'] = DatabaseUtils::sanitizeString($data['image_url'] ?? '');
        if (!empty($cleanData['image_url']) && !filter_var($cleanData['image_url'], FILTER_VALIDATE_URL)) {
            $errors[] = 'Afbeelding URL moet een geldige URL zijn';
        }
        
        // Published status
        $cleanData['is_published'] = isset($data['is_published']) ? 1 : 0;
        
        // Featured status
        $cleanData['is_featured'] = isset($data['is_featured']) ? 1 : 0;
        
        return array_merge($cleanData, ['errors' => $errors]);
    }
    
    /**
     * Render publieke weergave
     */
    private function renderPublicView($view, $data = []) {
        // Deze functie zou normaal een template engine gebruiken
        // Voor nu retourneren we de view naam en data
        return [
            'view' => $view,
            'data' => $data
        ];
    }
    
    /**
     * Render admin weergave
     */
    private function renderAdminView($view, $data = []) {
        // Deze functie zou normaal een template engine gebruiken
        // Voor nu retourneren we de view naam en data
        return [
            'view' => $view,
            'data' => $data
        ];
    }
}
?>