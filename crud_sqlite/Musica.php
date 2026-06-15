<?php

class Musica {
    private ?int $id;
    private string $nome;
    private string $autor;

    public function __construct(?int $id, string $nome, string $autor) {
        $this->id   = $id;
        $this->nome = trim($nome);
        $this->autor = trim($autor);
    }

    public function getId(): ?int    { return $this->id; }
    public function getNome(): string  { return $this->nome; }
    public function getAutor(): string { return $this->autor; }

    public function setId(?int $id): void      { $this->id = $id; }
    public function setNome(string $n): void   { $this->nome = trim($n); }
    public function setAutor(string $a): void  { $this->autor = trim($a); }
}
