<?php

class ventaProducto{
    public string $codigo;
    public int $cantidad;
    public int $precioUnitarioCentavos;

    public function __construct(string $codigo, int $cantidad, float $precioUnitario){
        $this->codigo = $codigo;
        $this->cantidad = $cantidad;
        /* paso a centavos ya q float a veces suma mal, lo multiplico por 100, redondeo porque faltaria un centavo
        y lo paso a entero, para pasarlo a pesos de nuevo solo hay q dividir */
        $this->precioUnitarioCentavos = (int)round($precioUnitario*100);
    }
}

?>