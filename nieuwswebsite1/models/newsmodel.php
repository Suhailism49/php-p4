<?php
require_once '../config/database.php';

/**
 * NewsModel - Model voor nieuwsartikelen
 * Bevat alle database operaties voor nieuwsberichten
 */
class NewsModel {
    private $db;
    
    public function __construct() {
        $this->db = getDB();
    }
    
    /**
     * Haal alle gepubliceerde artikelen op
     */
    public function getPublishedArticles($limit = null, $categoryId = null) {
        $sql = "SELECT a.*, c.name as category_name, u.username as author_name 
                FROM news_articles a 
                INNER JOIN categories c ON a.category_id = c.id 
                INNER JOIN users u ON a.author_id = u.id 
                WHERE a.is_published = 1 
                ORDER BY a.published_at DESC";
        
        if ($categoryId) {
            $sql = "SELECT a.*, c.name as category_name, u.username as author_name 
                    FROM news_articles a 
                    INNER JOIN categories c ON a.category_id = c.id 
                    INNER JOIN users u ON a.author_id = u.id 
                    WHERE a.is_published = 1 AND a.category_id = :category_id 
                    ORDER BY a.published_at DESC";
        }
        
        if ($limit) {
            $sql .= " LIMIT :limit";
        }
        
        $stmt = $this->db->prepare($sql);
        
        if ($categoryId) {
            $stmt->bindValue(':category_id', $categoryId, PDO::PARAM_INT);
        }
        
        if ($limit) {
            $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        }
        
        $stmt->execute();
        return $stmt->fetchAll();
    }
    
    /**
     * Haal alle artikelen op (voor beheer)
     */
    public function getAllArticles() {
        $sql = "SELECT a.*, c.name as category_name, u.username as author_name 
                FROM news_articles a 
                INNER JOIN categories c ON a.category_id = c.id 
                INNER JOIN users u ON a.author_id = u.id 
                ORDER BY a.created_at DESC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }
    
    /**
     * Haal specifiek artikel op via ID
     */
    public function getArticleById($id) {
        $sql = "SELECT a.*, c.name as category_name, u.username as author_name 
                FROM news_articles a 
                INNER JOIN categories c ON a.category_id = c.id 
                INNER JOIN users u ON a.author_id = u.id 
                WHERE a.id = :id";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch();
    }
    
    /**
     * Verhoog leesteller van artikel
     */
    public function incrementReadCount($id) {
        $sql = "UPDATE news_articles SET read_count = read_count + 1 WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }
    
    /**
     * Voeg nieuw artikel toe
     */
    public function addArticle($data) {
        $sql = "INSERT INTO news_articles (title, content, summary, category_id, author_id, image_url, is_published, is_featured, published_at) 
                VALUES (:title, :content, :summary, :category_id, :author_id, :image_url, :is_published, :is_featured, :published_at)";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':title', $data['title']);
        $stmt->bindValue(':content', $data['content']);
        $stmt->bindValue(':summary', $data['summary']);
        $stmt->bindValue(':category_id', $data['category_id'], PDO::PARAM_INT);
        $stmt->bindValue(':author_id', $data['author_id'], PDO::PARAM_INT);
        $stmt->bindValue(':image_url', $data['image_url']);
        $stmt->bindValue(':is_published', $data['is_published'], PDO::PARAM_BOOL);
        $stmt->bindValue(':is_featured', $data['is_featured'], PDO::PARAM_BOOL);
        $stmt->bindValue(':published_at', $data['is_published'] ? date('Y-m-d H:i:s') : null);
        
        return $stmt->execute();
    }
    
    /**
     * Update bestaand artikel
     */
    public function updateArticle($id, $data) {
        $sql = "UPDATE news_articles 
                SET title = :title, content = :content, summary = :summary, 
                    category_id = :category_id, image_url = :image_url, 
                    is_published = :is_published, is_featured = :is_featured,
                    published_at = :published_at, updated_at = NOW()
                WHERE id = :id";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->bindValue(':title', $data['title']);
        $stmt->bindValue(':content', $data['content']);
        $stmt->bindValue(':summary', $data['summary']);
        $stmt->bindValue(':category_id', $data['category_id'], PDO::PARAM_INT);
        $stmt->bindValue(':image_url', $data['image_url']);
        $stmt->bindValue(':is_published', $data['is_published'], PDO::PARAM_BOOL);
        $stmt->bindValue(':is_featured', $data['is_featured'], PDO::PARAM_BOOL);
        
        // Als artikel gepubliceerd wordt en nog geen published_at heeft, zet huidige tijd
        if ($data['is_published']) {
            $currentArticle = $this->getArticleById($id);
            $publishedAt = $currentArticle['published_at'] ?? date('Y-m-d H:i:s');
        } else {
            $publishedAt = null;
        }
        $stmt->bindValue(':published_at', $publishedAt);
        
        return $stmt->execute();
    }
    
    /**
     * Verwijder artikel
     */
    public function deleteArticle($id) {
        $sql = "DELETE FROM news_articles WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }
    
    /**
     * Haal meest gelezen artikelen op
     */
    public function getMostReadArticles($limit = 5) {
        $sql = "SELECT a.*, c.name as category_name 
                FROM news_articles a 
                INNER JOIN categories c ON a.category_id = c.id 
                WHERE a.is_published = 1 
                ORDER BY a.read_count DESC 
                LIMIT :limit";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }
    
    /**
     * Haal uitgelichte artikelen op
     */
    public function getFeaturedArticles($limit = 3) {
        $sql = "SELECT a.*, c.name as category_name 
                FROM news_articles a 
                INNER JOIN categories c ON a.category_id = c.id 
                WHERE a.is_published = 1 AND a.is_featured = 1 
                ORDER BY a.published_at DESC 
                LIMIT :limit";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }
    
    /**
     * Zoek artikelen op basis van zoekterm
     */
    public function searchArticles($searchTerm) {
        $sql = "SELECT a.*, c.name as category_name, u.username as author_name 
                FROM news_articles a 
                INNER JOIN categories c ON a.category_id = c.id 
                INNER JOIN users u ON a.author_id = u.id 
                WHERE a.is_published = 1 
                AND (a.title LIKE :search OR a.content LIKE :search OR a.summary LIKE :search)
                ORDER BY a.published_at DESC";
        
        $stmt = $this->db->prepare($sql);
        $searchPattern = '%' . $searchTerm . '%';
        $stmt->bindValue(':search', $searchPattern);
        $stmt->execute();
        return $stmt->fetchAll();
    }
    
    /**
     * Haal statistieken op voor dashboard
     */
    public function getStatistics() {
        $stats = [];
        
        // Totaal aantal artikelen
        $sql = "SELECT COUNT(*) as total FROM news_articles";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        $stats['total_articles'] = $stmt->fetch()['total'];
        
        // Gepubliceerde artikelen
        $sql = "SELECT COUNT(*) as published FROM news_articles WHERE is_published = 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        $stats['published_articles'] = $stmt->fetch()['published'];
        
        // Totaal aantal views
        $sql = "SELECT SUM(read_count) as total_views FROM news_articles";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        $stats['total_views'] = $stmt->fetch()['total_views'] ?? 0;
        
        // Artikelen per categorie
        $sql = "SELECT c.name, COUNT(a.id) as count 
                FROM categories c 
                LEFT JOIN news_articles a ON c.id = a.category_id AND a.is_published = 1
                GROUP BY c.id, c.name 
                ORDER BY count DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        $stats['articles_per_category'] = $stmt->fetchAll();
        
        return $stats;
    }
}
?>