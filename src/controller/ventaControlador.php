<?php

class ventaControlador{
    private procesarVenta $coordinacion;

    public function __construct(procesarVenta $coordinacion){
        $this->coordinacion = $coordinacion;
    }

    public function recibirVenta (): void{
        $metodo = $_SERVER["REQUEST_METHOD"];

        if ($metodo != "POST"){
            http_response_code(405);
            echo json_encode(["mensaje" => "Este endpoint solo recibe ventas por POST"]);
            return;
        }

        $texto = file_get_contents('php://input');

        if ($texto == false || $texto === ""){
            http_response_code(400);
            echo json_encode(["mensaje" => "La petición vino sin datos, falta el json de la venta"]);
            return;
        }

        // convierte el JSON en array, por eso después se lee con corchetes
        $datos = json_decode($texto, true);

        // por cada producto del json creo un ventaProducto
        $listaVentaProductos = [];
        foreach($datos["productos"] as $producto){
            $listaVentaProductos[] = new ventaProducto($producto['codigo'],$producto['cantidad'],$producto['precioUnitario']);
        }

        // armo la venta con los datos del json y la lista de productos
        $venta=new venta($datos['ventaId'],$datos['fecha'], $datos['cajeroId'], $listaVentaProductos);
        $llego = $this->coordinacion->procesar($venta);

        // si no llegó a B, respondo error 502
        if (!$llego){
            http_response_code(502);
            echo json_encode(['mensaje' => 'No se pudo registrar el movimiento en el sistema de stock']);
            return;
        }

        http_response_code(200);
        echo json_encode(['mensaje' => 'Venta procesada correctamente']);
    }
}

?>