<?php
class Carro {

    private $marca;
    private $ano_modelo;
    private $ano_fabricacao;
    private $placa;
    private $renavam;
    private $valor;

    public function __construct() {
        $this->marca = "Chevrolet";
        $this->ano_modelo = 2010;
        $this->ano_fabricacao = 2010;
        $this->placa = "ABC1020";
        $this->renavam = 102030;
        $this->valor = 30000.00;
    }

    public function imprimir() {
        echo "<p>Marca: $this->marca </p>";
        echo "<p>Ano Modelo: $this->ano_modelo </p>";
        echo "<p>Ano de Fabricacao: $this->ano_fabricacao </p>";
        echo "<p>Placa: $this->placa </p>";
        echo "<p>Renavam: $this->renavam </p>";
        echo "<p>Valor: $this->valor </p>";
    }

    public function depreciacao() {
        $this->valor = $this->valor * 0.99;
    }
}

$carro = new Carro();
$carro->imprimir();
$carro->depreciacao();
$carro->imprimir();

