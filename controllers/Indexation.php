<?php
class Indexation {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function indexerTexte($idDocument, $texte) {
        $texte = mb_strtolower($texte, 'UTF-8');
        $texte = preg_replace('/[^\\p{L}\\s]/u', ' ', $texte);
        $mots = preg_split('/\\s+/', $texte, -1, PREG_SPLIT_NO_EMPTY);
    
        $motLiaison = new MotLiaison($this->conn);
        $motsExclus = $motLiaison->getMotsExclus();
    
        $frequence = [];
        foreach ($mots as $mot) {
            $type = in_array($mot, $motsExclus) ? 'mot_liaison' : 'mot_cle';
            if (!isset($frequence[$mot])) {
                $frequence[$mot] = ['count' => 0, 'type' => $type];
            }
            $frequence[$mot]['count']++;
        }
    
        foreach ($frequence as $mot => $data) {
            $stmt = $this->conn->prepare('INSERT INTO Indexation (Mot_Cle, Id_Documents, Nombre_Mot, Type) VALUES (?, ?, ?, ?)');
            $stmt->bind_param('siis', $mot, $idDocument, $data['count'], $data['type']);
            $stmt->execute();
        }
    }
    
    
}
?>
