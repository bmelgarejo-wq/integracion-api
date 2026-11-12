<?php

class estadisticaVentaDTO { //molde de lo que se le manda al sistema C
    public int $ventaId;
    public int $cantidadProductos;
    public ?float $importeTotal; //null si la venta no trae precios (A2)

    public function __construct(int $ventaId, int $cantidadProductos, ?float $importeTotal) {
        $this->ventaId = $ventaId;
        $this->cantidadProductos = $cantidadProductos;
        $this->importeTotal = $importeTotal;
    }

    public function paraEnviar(): array { //arma los datos con el formato que pide C
        return[
            'ventaId' => $this->ventaId,
            'cantidadProductos' => $this->cantidadProductos,
            'importeTotal' => $this->importeTotal,
        ];
    }
}

?>
