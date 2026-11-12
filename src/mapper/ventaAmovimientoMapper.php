<?php

class ventaAmovimientoMapper{
    public function transformar(ventaDTO $venta): movimientoStockDTO {
        $listaMovimientoProducto = [];
        foreach ($venta->productos as $ventaProducto){
            /* por cada ventaProducto creo un movimientoProducto copiando solo el código y la cantidad.
            hace falta porque movimientoStock, en su paraEnviar(), le pide paraEnviar() a cada producto, y ventaProducto no lo tiene :b */
            $listaMovimientoProducto[] = new movimientoProductoDTO($ventaProducto->codigo, $ventaProducto->cantidad);
        }
        $referencia = "VENTA-".$venta->ventaId;
        return new movimientoStockDTO("SALIDA",$venta->fecha,$referencia,$listaMovimientoProducto);
    }
}

?>
