<?php

class ventaProductoDTO{
    public string $codigo;
    public int $cantidad;
    public ?int $precioUnitarioCentavos; //A2 no manda precios, por eso puede ser null

    public function __construct(string $codigo, int $cantidad, ?float $precioUnitario){
        $this->codigo = $codigo;
        $this->cantidad = $cantidad;
        /* paso a centavos ya q float a veces suma mal, lo multiplico por 100, redondeo porque faltaria un centavo
        y lo paso a entero, para pasarlo a pesos de nuevo solo hay q dividir.
        si no hay precio lo dejo en null y no en 0, para que no parezca una venta gratis */
        $this->precioUnitarioCentavos = $precioUnitario === null ? null : (int)round($precioUnitario*100);
    }
}

?>
