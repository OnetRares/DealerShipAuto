<?php
class Database
{
    private $host = "localhost";
    private $db_name = "your_database";
    private $user = "postgres";
    private $password = "your_password";
    private $conn;

    public function getConnection()
    {
        $this->conn = null;
        $dsn = "pgsql:host={$this->host};dbname={$this->db_name}";

        try {
            $this->conn = new PDO($dsn, $this->user, $this->password);
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            error_log('Eroare Conexiune BD: ' . $e->getMessage());
            return null;
        }

        return $this->conn;
    }
}
?>