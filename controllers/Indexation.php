<?php
class Indexation {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function indexerTexte($idDocument, $texte) {
        $mots = str_word_count(strtolower($texte), 1);
        $mots = array_filter($mots, function($mot) {
            return ctype_alpha($mot);
        });

        $motLiaison = new MotLiaison($this->conn);
        $motsExclus = $motLiaison->getMotsExclus();

        $frequence = [];
        foreach ($mots as $mot) {
            if (!in_array($mot, $motsExclus)) {
                if (!isset($frequence[$mot])) {
                    $frequence[$mot] = 0;
                }
                $frequence[$mot]++;
            }
        }

        foreach ($frequence as $mot => $count) {
            $stmt = $this->conn->prepare('INSERT INTO Indexation (Mot_Cle, Id_Documents, Nombre_Mot) VALUES (?, ?, ?)');
            $stmt->bind_param('sii', $mot, $idDocument, $count);
            $stmt->execute();
        }
    }
}
