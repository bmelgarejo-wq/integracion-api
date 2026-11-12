<?php

class ventaAestadisticaMapper{
    public function transformar(ventaDTO $venta): estadisticaVentaDTO {
        $importeCentavos = 0;
        foreach ($venta->productos as $ventaProducto){
            if ($ventaProducto->precioUnitarioCentavos === null){ //si algun producto no tiene precio no se puede calcular el total
                $importeCentavos = null;
                break;
            }
            $importeCentavos += $ventaProducto->cantidad * $ventaProducto->precioUnitarioCentavos; //cantidad por precio, de cada producto
        }
        $importeTotal = null;
        if ($importeCentavos !== null){
            $importeTotal = $importeCentavos / 100; //de centavos a pesos
        }
        return new estadisticaVentaDTO($venta->ventaId, count($venta->productos), $importeTotal);
    }
}

?>
