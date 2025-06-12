<?php
/**
 * Klassen voor het Ziekmeldsysteem
 * Bevat de Docent en Ziekmelding klassen met CRUD operaties
 * 
 * Auteur: [Suhail Ismaili]
 * Datum: 2025-06-12
 */

require_once 'config.php';

/**
 * Docent klasse
 * Beheert alle docent-gerelateerde operaties
 */
class Docent {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }
    
    /**
     * Alle docenten ophalen
     * @return array
     */
    public function getAllDocenten() {
        try {
            $stmt = $this->db->prepare("SELECT * FROM docenten ORDER BY achternaam, voornaam");
            $stmt->execute();
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Fout bij ophalen docenten: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Docent ophalen op basis van ID
     * @param int $id
     * @return array|null
     */
    public function getDocentById($id) {
        try {
            $stmt = $this->db->prepare("SELECT * FROM docenten WHERE id = ?");
            $stmt->execute([$id]);
            return $stmt->fetch();
        } catch (PDOException $e) {
            error_log("Fout bij ophalen docent: " . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Docent ophalen op basis van email
     * @param string $email
     * @return array|null
     */
    public function getDocentByEmail($email) {
        try {
            $stmt = $this->db->prepare("SELECT * FROM docenten WHERE email = ?");
            $stmt->execute([$email]);
            return $stmt->fetch();
        } catch (PDOException $e) {
            error_log("Fout bij ophalen docent: " . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Nieuwe docent toevoegen
     * @param array $data
     * @return bool
     */
    public function addDocent($data) {
        try {
            $stmt = $this->db->prepare("
                INSERT INTO docenten (voornaam, achternaam, email, telefoon, afdeling) 
                VALUES (?, ?, ?, ?, ?)
            ");
            return $stmt->execute([
                $data['voornaam'],
                $data['achternaam'],
                $data['email'],
                $data['telefoon'],
                $data['afdeling']
            ]);
        } catch (PDOException $e) {
            error_log("Fout bij toevoegen docent: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Docent bijwerken
     * @param int $id
     * @param array $data
     * @return bool
     */
    public function updateDocent($id, $data) {
        try {
            $stmt = $this->db->prepare("
                UPDATE docenten 
                SET voornaam = ?, achternaam = ?, email = ?, telefoon = ?, afdeling = ?
                WHERE id = ?
            ");
            return $stmt->execute([
                $data['voornaam'],
                $data['achternaam'],
                $data['email'],
                $data['telefoon'],
                $data['afdeling'],
                $id
            ]);
        } catch (PDOException $e) {
            error_log("Fout bij bijwerken docent: " . $e->getMessage());
            return false;
        }
    }
}

/**
 * Ziekmelding klasse
 * Beheert alle ziekmelding-gerelateerde operaties
 */
class Ziekmelding {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }
    
    /**
     * Alle ziekmeldingen ophalen met docent informatie
     * @param string $status Filter op status (optioneel)
     * @return array
     */
    public function getAllZiekmeldingen($status = null) {
        try {
            $sql = "
                SELECT z.*, 
                       CONCAT(d.voornaam, ' ', d.achternaam) AS docent_naam,
                       d.email, d.telefoon, d.afdeling
                FROM ziekmeldingen z
                JOIN docenten d ON z.docent_id = d.id
            ";
            
            if ($status) {
                $sql .= " WHERE z.status = ?";
                $stmt = $this->db->prepare($sql . " ORDER BY z.startdatum DESC");
                $stmt->execute([$status]);
            } else {
                $stmt = $this->db->prepare($sql . " ORDER BY z.startdatum DESC");
                $stmt->execute();
            }
            
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Fout bij ophalen ziekmeldingen: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Actieve ziekmeldingen ophalen
     * @return array
     */
    public function getActieveZiekmeldingen() {
        try {
            $stmt = $this->db->prepare("SELECT * FROM actieve_ziekmeldingen");
            $stmt->execute();
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Fout bij ophalen actieve ziekmeldingen: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Ziekmelding ophalen op basis van ID
     * @param int $id
     * @return array|null
     */
    public function getZiekmeldingById($id) {
        try {
            $stmt = $this->db->prepare("
                SELECT z.*, 
                       CONCAT(d.voornaam, ' ', d.achternaam) AS docent_naam,
                       d.email, d.telefoon, d.afdeling
                FROM ziekmeldingen z
                JOIN docenten d ON z.docent_id = d.id
                WHERE z.id = ?
            ");
            $stmt->execute([$id]);
            return $stmt->fetch();
        } catch (PDOException $e) {
            error_log("Fout bij ophalen ziekmelding: " . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Nieuwe ziekmelding toevoegen
     * @param array $data
     * @return bool
     */
    public function addZiekmelding($data) {
        try {
            $stmt = $this->db->prepare("
                INSERT INTO ziekmeldingen 
                (docent_id, startdatum, einddatum, reden, vervanger_geregeld, vervanger_naam, opmerkingen) 
                VALUES (?, ?, ?, ?, ?, ?, ?)
            ");
            return $stmt->execute([
                $data['docent_id'],
                $data['startdatum'],
                $data['einddatum'] ?: null,
                $data['reden'],
                isset($data['vervanger_geregeld']) ? 1 : 0,
                $data['vervanger_naam'] ?: null,
                $data['opmerkingen'] ?: null
            ]);
        } catch (PDOException $e) {
            error_log("Fout bij toevoegen ziekmelding: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Ziekmelding bijwerken
     * @param int $id
     * @param array $data
     * @return bool
     */
    public function updateZiekmelding($id, $data) {
        try {
            $stmt = $this->db->prepare("
                UPDATE ziekmeldingen 
                SET docent_id = ?, startdatum = ?, einddatum = ?, reden = ?, 
                    vervanger_geregeld = ?, vervanger_naam = ?, status = ?, opmerkingen = ?
                WHERE id = ?
            ");
            return $stmt->execute([
                $data['docent_id'],
                $data['startdatum'],
                $data['einddatum'] ?: null,
                $data['reden'],
                isset($data['vervanger_geregeld']) ? 1 : 0,
                $data['vervanger_naam'] ?: null,
                $data['status'],
                $data['opmerkingen'] ?: null,
                $id
            ]);
        } catch (PDOException $e) {
            error_log("Fout bij bijwerken ziekmelding: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Ziekmelding status wijzigen
     * @param int $id
     * @param string $status
     * @return bool
     */
    public function updateStatus($id, $status) {
        try {
            $stmt = $this->db->prepare("UPDATE ziekmeldingen SET status = ? WHERE id = ?");
            return $stmt->execute([$status, $id]);
        } catch (PDOException $e) {
            error_log("Fout bij wijzigen status: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Ziekmelding verlengen
     * @param int $id
     * @param string $nieuwe_einddatum
     * @param string $reden_verlenging
     * @return bool
     */
    public function verlengZiekmelding($id, $nieuwe_einddatum, $reden_verlenging) {
        try {
            $this->db->beginTransaction();
            
            // Update de ziekmelding
            $stmt = $this->db->prepare("
                UPDATE ziekmeldingen 
                SET einddatum = ?, status = 'verlengd' 
                WHERE id = ?
            ");
            $stmt->execute([$nieuwe_einddatum, $id]);
            
            // Voeg verlenging toe aan verlengingen tabel
            $stmt = $this->db->prepare("
                INSERT INTO ziekmelding_verlengingen 
                (ziekmelding_id, nieuwe_einddatum, reden_verlenging) 
                VALUES (?, ?, ?)
            ");
            $stmt->execute([$id, $nieuwe_einddatum, $reden_verlenging]);
            
            $this->db->commit();
            return true;
        } catch (PDOException $e) {
            $this->db->rollBack();
            error_log("Fout bij verlengen ziekmelding: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Statistieken ophalen
     * @return array
     */
    public function getStatistieken() {
        try {
            $stmt = $this->db->prepare("SELECT * FROM ziekmelding_statistieken");
            $stmt->execute();
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Fout bij ophalen statistieken: " . $e->getMessage());
            return [];
        }
    }
}
?>