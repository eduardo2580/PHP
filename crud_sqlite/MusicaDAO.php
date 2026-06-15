<?php

class MusicaDAO {
    private PDO $connection;

    public function __construct(PDO $connection) {
        $this->connection = $connection;
    }

    public function criarMusica(Musica $musica): int {
        $stmt = $this->connection->prepare(
            'INSERT INTO musicas (nome, autor) VALUES (?, ?)'
        );
        $stmt->execute([$musica->getNome(), $musica->getAutor()]);
        $id = (int) $this->connection->lastInsertId();
        $musica->setId($id);
        return $id;
    }

    public function buscarMusica(int $id): ?Musica {
        $stmt = $this->connection->prepare(
            'SELECT * FROM musicas WHERE id = ?'
        );
        $stmt->execute([$id]);
        $row = $stmt->fetch();
        return $row ? new Musica($row['id'], $row['nome'], $row['autor']) : null;
    }

    public function atualizarMusica(Musica $musica): bool {
        $stmt = $this->connection->prepare(
            'UPDATE musicas SET nome = ?, autor = ? WHERE id = ?'
        );
        $stmt->execute([$musica->getNome(), $musica->getAutor(), $musica->getId()]);
        return $stmt->rowCount() > 0;
    }

    public function excluirMusica(int $id): bool {
        $stmt = $this->connection->prepare(
            'DELETE FROM musicas WHERE id = ?'
        );
        $stmt->execute([$id]);
        return $stmt->rowCount() > 0;
    }

    /** @return Musica[] */
    public function buscarTodas(): array {
        $stmt = $this->connection->query('SELECT * FROM musicas ORDER BY id');
        $musicas = [];
        while ($row = $stmt->fetch()) {
            $musicas[] = new Musica($row['id'], $row['nome'], $row['autor']);
        }
        return $musicas;
    }
}
