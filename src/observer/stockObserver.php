<?php

//le avisa al sistema B (stock) de la venta
class stockObserver implements ventaObserver{
    private ventaAmovimientoMapper $mapper;
    private conectorStock $conector;

    public function __construct(ventaAmovimientoMapper $mapper, conectorStock $conector){
        $this->mapper = $mapper;
        $this->conector = $conector;
    }

    public function nombre(): string{
        return "stock";
    }

    public function avisar(ventaDTO $venta): bool{
        $movimiento = $this->mapper->transformar($venta); //venta a -> b
        return $this->conector->enviarMovimiento($movimiento);
    }
}

?>
