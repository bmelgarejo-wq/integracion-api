<?php

class traductorVentaA2 implements traductorVenta{
    public function puedeTraducir(array $datos): bool{
        return isset($datos['id']) && isset($datos['items']); //A2 manda id e items
    }

    public function traducir(array $datos): ventaDTO{
        // igual que en A pero con los nombres de A2, y como no manda precio va null
        $listaVentaProductos = [];
        foreach($datos["items"] as $item){
            $listaVentaProductos[] = new ventaProductoDTO($item['sku'],$item['units'],null);
        }

        // A2 manda el id como texto, lo paso a numero. Cajero tampoco manda, va null
        return new ventaDTO((int)$datos['id'],$datos['timestamp'], null, $listaVentaProductos, "A2");
    }
}

?>
