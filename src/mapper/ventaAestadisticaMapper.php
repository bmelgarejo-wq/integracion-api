<?php

class ventaAestadisticaMapper{
    public function transformar(ventaDTO $venta): estadisticaVentaDTO {
        $importeCentavos = 0;
        foreach ($venta->productos as $ventaProducto){
            if ($ventaProducto->precioUnitarioCentavos === null){ //si falta un precio no se puede calcular el total
                $importeCentavos = null;
                break;
            }
            $importeCentavos += $ventaProducto->cantidad * $ventaProducto->precioUnitarioCentavos; //sumo cantidad por precio de cada producto
        }
        $importeTotal = $importeCentavos === null ? null : $importeCentavos / 100; //paso de centavos a pesos
        return new estadisticaVentaDTO($venta->ventaId, count($venta->productos), $importeTotal);
    }
}

?>
