<?php

class ventaAmovimiento{
    public function transformar(venta $venta): movimientoStock {
        $listaMovimientoProducto = [];
        foreach ($venta->productos as $ventaProducto){
            /* por cada ventaProducto creo un movimientoProducto copiando solo el código y la cantidad.
            hace falta porque movimientoStock, en su paraEnviar(), le pide paraEnviar() a cada producto, y ventaProducto no lo tiene :b */
            $listaMovimientoProducto[] = new movimientoProducto($ventaProducto->codigo, $ventaProducto->cantidad);
        }
        $referencia = "VENTA-".$venta->ventaId;
        return new movimientoStock("SALIDA",$venta->fecha,$referencia,$listaMovimientoProducto);
    }
}

?>