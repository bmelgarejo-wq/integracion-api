<?php

class venta{
    public int $ventaId; //el transformador lo usa para armar la "referencia" de B, le agrega "VENTA-" adelante
    public string $fecha;
    public int $cajeroId; //B no lo pide, pero lo guardo porque es parte de la venta
    public array $productos; 

    public function __construct(int $ventaId, string $fecha, int $cajeroId, array $productos){
        $this->ventaId = $ventaId;
        $this->fecha = $fecha;
        $this->cajeroId = $cajeroId;
        $this->productos = $productos;
    }
}

?>