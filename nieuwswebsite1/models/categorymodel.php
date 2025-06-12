<?php
require_once '../config/database.php';

/**
 * CategoryModel - Model voor categorieën
 * Bevat alle database operaties voor categorieën
 */
class CategoryModel {
    private $db;
    
    public function __construct() {
        $this->db = getDB();
    }
    
    /**
     * Haal alle categorieën op
     */
    public function getAllCategories() {
        $sql = "SELECT * FROM categories ORDER BY name ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }
    
    /**
     * Haal categorie op via ID
     */
    public function getCategoryById($id) {
        $sql = "SELECT * FROM categories WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch();
    }
    
    /**
     * Haal categorieën op met artikel aantallen
     */
    public function getCategoriesWithCounts() {
        $sql = "SELECT c.*, COUNT(a.id) as article_count 
                FROM categories c 
                LEFT JOIN news_articles a ON c.id = a.category_id AND a.is_published = 1
                GROUP BY c.id, c.name, c.description 
                ORDER BY c.name ASC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }
    
    /**
     * Voeg nieuwe categorie toe
     */
    public function addCategory($name, $description) {
        $sql = "INSERT INTO categories (name, description) VALUES (:name, :description)";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':name', $name);
        $stmt->bindValue(':description', $description);
        return $stmt->execute();
    }
    
    /**
     * Update categorie
     */
    public function updateCategory($id, $name, $description) {
        $sql = "UPDATE categories SET name = :name, description = :description, updated_at = NOW() WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->bindValue(':name', $name);
        $stmt->bindValue(':description', $description);
        return $stmt->execute();
    }
    
    /**
     * Verwijder categorie (alleen als geen artikelen)
     */
    public function deleteCategory($id) {
        // Controleer eerst of er artikelen in deze categorie zijn
        $sql = "SELECT COUNT(*) as count FROM news_articles WHERE category_id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetch();
        
        if ($result['count'] > 0) {
            return false; // Kan niet verwijderen, er zijn nog artikelen
        }
        
        $sql = "DELETE FROM categories WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }
    
    /**
     * Controleer of categorie naam al bestaat
     */
    public function categoryExists($name, $excludeId = null) {
        $sql = "SELECT COUNT(*) as count FROM categories WHERE name = :name";
        
        if ($excludeId) {
            $sql .= " AND id != :exclude_id";
        }
        
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':name', $name);
        
        if ($excludeId) {
            $stmt->bindValue(':exclude_id', $excludeId, PDO::PARAM_INT);
        }
        
        $stmt->execute();
        $result = $stmt->fetch();
        return $result['count'] > 0;
    }
}
?>