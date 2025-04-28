<?php
class MotLiaison {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function getMotsExclus() {
        $result = $this->conn->query('SELECT Mot_Exclu FROM Mot_Liaison');
        $mots = [];
        while ($row = $result->fetch_assoc()) {
            $mots[] = strtolower($row['Mot_Exclu']);
        }
        return $mots;
    }
}
