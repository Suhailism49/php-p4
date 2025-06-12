<?php
require_once '../config/database.php';

/**
 * ShareModel - Model voor tip-een-vriend functionaliteit
 * Bevat alle database operaties voor het delen van artikelen
 */
class ShareModel {
    private $db;
    
    public function __construct() {
        $this->db = getDB();
    }
    
    /**
     * Registreer een artikel share
     */
    public function shareArticle($articleId, $senderName, $senderEmail, $recipientName, $recipientEmail, $message = '') {
        $sql = "INSERT INTO article_shares (article_id, sender_name, sender_email, recipient_name, recipient_email, message) 
                VALUES (:article_id, :sender_name, :sender_email, :recipient_name, :recipient_email, :message)";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':article_id', $articleId, PDO::PARAM_INT);
        $stmt->bindValue(':sender_name', $senderName);
        $stmt->bindValue(':sender_email', $senderEmail);
        $stmt->bindValue(':recipient_name', $recipientName);
        $stmt->bindValue(':recipient_email', $recipientEmail);
        $stmt->bindValue(':message', $message);
        
        return $stmt->execute();
    }
    
    /**
     * Haal share statistieken op voor een artikel
     */
    public function getArticleShareCount($articleId) {
        $sql = "SELECT COUNT(*) as share_count FROM article_shares WHERE article_id = :article_id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':article_id', $articleId, PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetch();
        return $result['share_count'];
    }
    
    /**
     * Haal alle shares op voor een artikel
     */
    public function getArticleShares($articleId) {
        $sql = "SELECT * FROM article_shares WHERE article_id = :article_id ORDER BY shared_at DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':article_id', $articleId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }
    
    /**
     * Haal recente shares op
     */
    public function getRecentShares($limit = 10) {
        $sql = "SELECT s.*, a.title as article_title 
                FROM article_shares s 
                INNER JOIN news_articles a ON s.article_id = a.id 
                ORDER BY s.shared_at DESC 
                LIMIT :limit";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }
    
    /**
     * Haal meest gedeelde artikelen op
     */
    public function getMostSharedArticles($limit = 5) {
        $sql = "SELECT a.id, a.title, COUNT(s.id) as share_count 
                FROM news_articles a 
                INNER JOIN article_shares s ON a.id = s.article_id 
                WHERE a.is_published = 1 
                GROUP BY a.id, a.title 
                ORDER BY share_count DESC 
                LIMIT :limit";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }
    
    /**
     * Genereer share statistieken
     */
    public function getShareStatistics() {
        $stats = [];
        
        // Totaal aantal shares
        $sql = "SELECT COUNT(*) as total_shares FROM article_shares";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        $stats['total_shares'] = $stmt->fetch()['total_shares'];
        
        // Shares vandaag
        $sql = "SELECT COUNT(*) as today_shares FROM article_shares WHERE DATE(shared_at) = CURDATE()";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        $stats['today_shares'] = $stmt->fetch()['today_shares'];
        
        // Shares deze week
        $sql = "SELECT COUNT(*) as week_shares FROM article_shares WHERE shared_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();  
        $stats['week_shares'] = $stmt->fetch()['week_shares'];
        
        // Shares per dag (laatste 7 dagen)
        $sql = "SELECT DATE(shared_at) as share_date, COUNT(*) as count 
                FROM article_shares 
                WHERE shared_at >= DATE_SUB(NOW(), INTERVAL 7 DAY) 
                GROUP BY DATE(shared_at) 
                ORDER BY share_date DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        $stats['daily_shares'] = $stmt->fetchAll();
        
        return $stats;
    }
    
    /**
     * Verstuur email notificatie (placeholder voor email functionaliteit)
     */
    public function sendShareEmail($shareData, $articleData) {
        // In een echte implementatie zou hier mail() of een email library gebruikt worden
        // Voor nu loggen we de email data
        
        $emailContent = [
            'to' => $shareData['recipient_email'],
            'subject' => "Je vriend {$shareData['sender_name']} heeft een artikel met je gedeeld: {$articleData['title']}",
            'body' => $this->generateEmailBody($shareData, $articleData)
        ];
        
        // Log email voor debugging (in productie zou dit echt verstuurd worden)
        error_log("Email zou verstuurd worden naar: " . $shareData['recipient_email']);
        error_log("Subject: " . $emailContent['subject']);
        
        return true; // Simuleer succesvolle verzending
    }
    
    /**
     * Genereer email body voor share notificatie
     */
    private function generateEmailBody($shareData, $articleData) {
        $body = "Hallo {$shareData['recipient_name']},\n\n";
        $body .= "Je vriend {$shareData['sender_name']} ({$shareData['sender_email']}) heeft een interessant artikel met je gedeeld:\n\n";
        $body .= "Titel: {$articleData['title']}\n";
        $body .= "Categorie: {$articleData['category_name']}\n\n";
        
        if (!empty($shareData['message'])) {
            $body .= "Persoonlijk bericht van {$shareData['sender_name']}:\n";
            $body .= "\"{$shareData['message']}\"\n\n";
        }
        
        $body .= "Je kunt het volledige artikel lezen op onze website.\n\n";
        $body .= "Met vriendelijke groet,\n";
        $body .= "Het Nieuwswebsite Team";
        
        return $body;
    }
    
    /**
     * Valideer share data
     */
    public function validateShareData($data) {
        $errors = [];
        
        if (empty($data['sender_name']) || strlen(trim($data['sender_name'])) < 2) {
            $errors[] = "Naam van verzender is verplicht (minimaal 2 karakters)";
        }
        
        if (empty($data['sender_email']) || !DatabaseUtils::isValidEmail($data['sender_email'])) {
            $errors[] = "Geldig email adres van verzender is verplicht";
        }
        
        if (empty($data['recipient_name']) || strlen(trim($data['recipient_name'])) < 2) {
            $errors[] = "Naam van ontvanger is verplicht (minimaal 2 karakters)";
        }
        
        if (empty($data['recipient_email']) || !DatabaseUtils::isValidEmail($data['recipient_email'])) {
            $errors[] = "Geldig email adres van ontvanger is verplicht";
        }
        
        if (!empty($data['message']) && strlen($data['message']) > 500) {
            $errors[] = "Persoonlijk bericht mag maximaal 500 karakters bevatten";
        }
        
        return $errors;
    }
}
?>