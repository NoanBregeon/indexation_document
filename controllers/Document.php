<?php
require_once 'MotLiaison.php';
require_once 'Indexation.php';

class Document {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function ajouter($titre, $texte) {
        $stmt = $this->conn->prepare('INSERT INTO Documents (Titre, Texte, Date_Creation) VALUES (?, ?, NOW())');
        $stmt->bind_param('ss', $titre, $texte);
        $stmt->execute();
    }

    public function indexerDernierDocument() {
        $id = $this->conn->insert_id;
        $stmt = $this->conn->prepare('SELECT Texte FROM Documents WHERE Id_Documents = ?');
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $texte = $row['Texte'];

        $indexation = new Indexation($this->conn);
        $indexation->indexerTexte($id, $texte);
    }
}
