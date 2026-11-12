<?php

class ventaDTO{
    public int $ventaId; //el transformador lo usa para armar la "referencia" de B, le agrega "VENTA-" adelante
    public string $fecha;
    public ?int $cajeroId; //B no lo pide, pero lo guardo porque es parte de la venta. A2 no lo manda, por eso puede ser null
    public array $productos;
    public string $origen; //de que sistema viene la venta (A, A2)

    public function __construct(int $ventaId, string $fecha, ?int $cajeroId, array $productos, string $origen = "A"){
        $this->ventaId = $ventaId;
        $this->fecha = $fecha;
        $this->cajeroId = $cajeroId;
        $this->productos = $productos;
        $this->origen = $origen;
    }
}

?>
