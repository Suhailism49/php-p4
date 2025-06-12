<?php
require_once '../config/database.php';

/**
 * CommentModel - Model voor reacties op artikelen (extra functie)
 * Bevat alle database operaties voor reacties
 */
class CommentModel {
    private $db;
    
    public function __construct() {
        $this->db = getDB();
    }
    
    /**
     * Haal goedgekeurde reacties op voor een artikel
     */
    public function getApprovedComments($articleId) {
        $sql = "SELECT * FROM comments 
                WHERE article_id = :article_id AND is_approved = 1 
                ORDER BY created_at ASC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':article_id', $articleId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }
    
    /**
     * Haal alle reacties op (voor beheer)
     */
    public function getAllComments($articleId = null) {
        $sql = "SELECT c.*, a.title as article_title 
                FROM comments c 
                INNER JOIN news_articles a ON c.article_id = a.id";
        
        if ($articleId) {
            $sql .= " WHERE c.article_id = :article_id";
        }
        
        $sql .= " ORDER BY c.created_at DESC";
        
        $stmt = $this->db->prepare($sql);
        
        if ($articleId) {
            $stmt->bindValue(':article_id', $articleId, PDO::PARAM_INT);
        }
        
        $stmt->execute();
        return $stmt->fetchAll();
    }
    
    /**
     * Haal reacties op die wachten op goedkeuring
     */
    public function getPendingComments() {
        $sql = "SELECT c.*, a.title as article_title 
                FROM comments c 
                INNER JOIN news_articles a ON c.article_id = a.id 
                WHERE c.is_approved = 0 
                ORDER BY c.created_at DESC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }
    
    /**
     * Voeg nieuwe reactie toe
     */
    public function addComment($articleId, $authorName, $authorEmail, $content) {
        $sql = "INSERT INTO comments (article_id, author_name, author_email, content) 
                VALUES (:article_id, :author_name, :author_email, :content)";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':article_id', $articleId, PDO::PARAM_INT);
        $stmt->bindValue(':author_name', $authorName);
        $stmt->bindValue(':author_email', $authorEmail);
        $stmt->bindValue(':content', $content);
        
        return $stmt->execute();
    }
    
    /**
     * Keur reactie goed
     */
    public function approveComment($commentId) {
        $sql = "UPDATE comments SET is_approved = 1 WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':id', $commentId, PDO::PARAM_INT);
        return $stmt->execute();
    }
    
    /**
     * Wijs reactie af
     */
    public function rejectComment($commentId) {
        $sql = "UPDATE comments SET is_approved = 0 WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':id', $commentId, PDO::PARAM_INT);
        return $stmt->execute();
    }
    
    /**
     * Verwijder reactie
     */
    public function deleteComment($commentId) {
        $sql = "DELETE FROM comments WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':id', $commentId, PDO::PARAM_INT);
        return $stmt->execute();
    }
    
    /**
     * Haal reactie op via ID
     */
    public function getCommentById($id) {
        $sql = "SELECT c.*, a.title as article_title 
                FROM comments c 
                INNER JOIN news_articles a ON c.article_id = a.id 
                WHERE c.id = :id";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch();
    }
    
    /**
     * Tel aantal reacties per artikel
     */
    public function getCommentCount($articleId, $approvedOnly = true) {
        $sql = "SELECT COUNT(*) as count FROM comments WHERE article_id = :article_id";
        
        if ($approvedOnly) {
            $sql .= " AND is_approved = 1";
        }
        
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':article_id', $articleId, PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetch();
        return $result['count'];
    }
    
    /**
     * Haal reactie statistieken op
     */
    public function getCommentStatistics() {
        $stats = [];
        
        // Totaal aantal reacties
        $sql = "SELECT COUNT(*) as total_comments FROM comments";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        $stats['total_comments'] = $stmt->fetch()['total_comments'];
        
        // Goedgekeurde reacties
        $sql = "SELECT COUNT(*) as approved_comments FROM comments WHERE is_approved = 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        $stats['approved_comments'] = $stmt->fetch()['approved_comments'];
        
        // Wachtende reacties
        $sql = "SELECT COUNT(*) as pending_comments FROM comments WHERE is_approved = 0";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        $stats['pending_comments'] = $stmt->fetch()['pending_comments'];
        
        // Reacties vandaag
        $sql = "SELECT COUNT(*) as today_comments FROM comments WHERE DATE(created_at) = CURDATE()";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        $stats['today_comments'] = $stmt->fetch()['today_comments'];
        
        // Artikelen met meeste reacties
        $sql = "SELECT a.id, a.title, COUNT(c.id) as comment_count 
                FROM news_articles a 
                INNER JOIN comments c ON a.id = c.article_id 
                WHERE c.is_approved = 1 
                GROUP BY a.id, a.title 
                ORDER BY comment_count DESC 
                LIMIT 5";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        $stats['most_commented_articles'] = $stmt->fetchAll();
        
        return $stats;
    }
    
    /**
     * Valideer reactie data
     */
    public function validateComment($data) {
        $errors = [];
        
        if (empty($data['author_name']) || strlen(trim($data['author_name'])) < 2) {
            $errors[] = "Naam is verplicht (minimaal 2 karakters)";
        }
        
        if (empty($data['author_email']) || !DatabaseUtils::isValidEmail($data['author_email'])) {
            $errors[] = "Geldig email adres is verplicht";
        }
        
        if (empty($data['content']) || strlen(trim($data['content'])) < 10) {
            $errors[] = "Reactie is verplicht (minimaal 10 karakters)";
        }
        
        if (!empty($data['content']) && strlen($data['content']) > 1000) {
            $errors[] = "Reactie mag maximaal 1000 karakters bevatten";
        }
        
        // Simpele spam detectie
        if (!empty($data['content']) && $this->containsSpam($data['content'])) {
            $errors[] = "Reactie bevat niet toegestane inhoud";
        }
        
        return $errors;
    }
    
    /**
     * Simpele spam detectie
     */
    private function containsSpam($content) {
        $spamWords = ['casino', 'poker', 'viagra', 'cialis', 'loan', 'mortgage', 'bitcoin'];
        $content = strtolower($content);
        
        foreach ($spamWords as $word) {
            if (strpos($content, $word) !== false) {
                return true;
            }
        }
        
        // Controleer op te veel links
        if (substr_count($content, 'http') > 2) {
            return true;
        }
        
        return false;
    }
    
    /**
     * Haal recente reacties op
     */
    public function getRecentComments($limit = 5, $approvedOnly = true) {
        $sql = "SELECT c.*, a.title as article_title 
                FROM comments c 
                INNER JOIN news_articles a ON c.article_id = a.id";
        
        if ($approvedOnly) {
            $sql .= " WHERE c.is_approved = 1";
        }
        
        $sql .= " ORDER BY c.created_at DESC LIMIT :limit";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }
}
?>