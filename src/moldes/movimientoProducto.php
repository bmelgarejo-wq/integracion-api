<?php

class movimientoProducto {
    public string $codigoProducto; //en la consigna estaba puesto con ""
    public int $cantidad;

    public function __construct(string $codigo, int $cantidad){
        $this->codigoProducto = $codigo;
        $this->cantidad = $cantidad;
    }

    public function paraEnviar(): array {
        return[
            'codigoProducto' => $this->codigoProducto,
            'cantidad' => $this->cantidad,
        ];
    }
}

?>