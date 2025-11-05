<?php
class Database
{
    private $host = "localhost";
    private $db_name = "cars_db";
    private $user = "postgres";
    private $password = "rares123";
    private $conn;

    public function getConnection()
    {
        $this->conn = null;
        $dsn = "pgsql:host={$this->host};dbname={$this->db_name}";

        try {
            $this->conn = new PDO($dsn, $this->user, $this->password);
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            echo json_encode(['success' => false, 'error' => 'Eroare BD: ' . $e->getMessage()]);
            exit;
        }

        return $this->conn;
    }
}
?>