<?php

class traductorVentaA2 implements traductorVenta{
    public function puedeTraducir(array $datos): bool{
        return isset($datos['id']) && isset($datos['items']); //A2 manda id e items
    }

    public function traducir(array $datos): ventaDTO{
        // por cada item del json creo un ventaProducto, A2 no manda precios
        $listaVentaProductos = [];
        foreach($datos["items"] as $item){
            $listaVentaProductos[] = new ventaProductoDTO($item['sku'],$item['units'],null);
        }

        // A2 manda el id como texto, lo paso a numero
        return new ventaDTO((int)$datos['id'],$datos['timestamp'], null, $listaVentaProductos, "A2");
    }
}

?>
