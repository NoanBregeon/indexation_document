<?php
class bdd {
    private $host = 'localhost';
    private $db_name = 'indexation_documents';
    private $username = 'root';
    private $password = '';
    private $conn;

    public function getConnection() {
        $this->conn = new mysqli($this->host, $this->username, $this->password, $this->db_name);
        if ($this->conn->connect_error) {
            die('Erreur connexion: ' . $this->conn->connect_error);
        }
        return $this->conn;
    }
}
