<?php

//no transforma ni envía por su cuenta, le pide al transformador y al conector que lo hagan
class procesarVenta {
    private ventaAmovimiento $transformador;
    private conectorStock $conector;

    /*el transformador y el conector se reciben desde afuera (desde el index.php) 
    en vez de crearlos aca con new asi se puede cambiar de conector sin tocar esta clase*/
    public function __construct (ventaAmovimiento $transformador, conectorStock $conector){
        $this->transformador = $transformador;
        $this->conector = $conector;
    }

    // recibe una venta, la transforma y la manda. Devuelve si llegó a B o no
    public function procesar(venta $venta): bool {
        $movimiento = $this->transformador->transformar($venta); //venta a -> b
        return $this->conector->enviarMovimiento($movimiento);
    }
}

?>