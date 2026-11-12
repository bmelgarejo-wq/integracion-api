<?php

class traductorVentaA implements traductorVenta{
    public function puedeTraducir(array $datos): bool{
        return isset($datos['ventaId']); //A manda ventaId
    }

    public function traducir(array $datos): ventaDTO{
        // por cada producto del json creo un ventaProducto
        $listaVentaProductos = [];
        foreach($datos["productos"] as $producto){
            $listaVentaProductos[] = new ventaProductoDTO($producto['codigo'],$producto['cantidad'],$producto['precioUnitario']);
        }

        // armo la venta con los datos del json y la lista de productos
        return new ventaDTO($datos['ventaId'],$datos['fecha'], $datos['cajeroId'], $listaVentaProductos, "A");
    }
}

?>
