<?php 
/**
* Database Connection
*/
class DbConnect {
    private $server = '127.0.0.1';   // host exacto de phpMyAdmin
    private $port   = '3309';        // puerto exacto de phpMyAdmin
    private $dbname = 'react_crud';  // nombre de la base que importaste
    private $user   = 'root';
    private $pass   = '';

    public function connect() {
        try {
            $conn = new PDO(
                "mysql:host={$this->server};port={$this->port};dbname={$this->dbname};charset=utf8mb4",
                $this->user,
                $this->pass
            );
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            return $conn;
        } catch (\Exception $e) {
            echo "❌ Database Error: " . $e->getMessage();
        }
    }
}
?>
