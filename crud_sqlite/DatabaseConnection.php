<?php

class DatabaseConnection {
    private string $dbPath;
    private ?PDO $connection = null;

    public function __construct(string $dbPath = __DIR__ . '/musicas.db') {
        $this->dbPath = $dbPath;
    }

    public function connect(): void {
        try {
            $this->connection = new PDO('sqlite:' . $this->dbPath);
            $this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->connection->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
            $this->initSchema();
        } catch (PDOException $e) {
            die('Erro ao conectar ao banco de dados: ' . $e->getMessage());
        }
    }

    private function initSchema(): void {
        $this->connection->exec('
            CREATE TABLE IF NOT EXISTS musicas (
                id      INTEGER PRIMARY KEY AUTOINCREMENT,
                nome    TEXT    NOT NULL,
                autor   TEXT    NOT NULL
            )
        ');
    }

    public function disconnect(): void {
        $this->connection = null;
    }

    public function getConnection(): PDO {
        if ($this->connection === null) {
            throw new RuntimeException('Conexão não iniciada. Chame connect() primeiro.');
        }
        return $this->connection;
    }
}
