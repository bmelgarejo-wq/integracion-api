<?php

class ventaController{
    private procesarVentaService $service;
    private array $traductores; //lista de traductorVenta, uno por cada sistema que manda ventas

    public function __construct(procesarVentaService $service, array $traductores){
        $this->service = $service;
        $this->traductores = $traductores;
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

        if (!is_array($datos)){
            http_response_code(422);
            echo json_encode(["mensaje" => "El json no es válido"]);
            return;
        }

        // busco el traductor que entiende este formato y le pido la venta
        $venta = null;
        foreach ($this->traductores as $traductor){
            if ($traductor->puedeTraducir($datos)){
                $venta = $traductor->traducir($datos);
                break;
            }
        }

        if ($venta === null){
            http_response_code(422);
            echo json_encode(["mensaje" => "No se reconoce el formato de la venta"]);
            return;
        }

        $resultado = $this->service->procesar($venta);

        // la venta ya está guardada, si algún sistema falló queda pendiente y se reintenta después
        if ($resultado === "duplicada"){
            http_response_code(200);
            echo json_encode(['mensaje' => 'La venta ya estaba registrada, no se procesa de nuevo']);
            return;
        }

        if ($resultado === "pendiente"){
            http_response_code(202);
            echo json_encode(['mensaje' => 'Venta recibida, quedó pendiente el envío a algún sistema']);
            return;
        }

        http_response_code(200);
        echo json_encode(['mensaje' => 'Venta procesada correctamente']);
    }

    // el área de stock consulta las ventas, se puede filtrar con ?estado=pendiente (o procesado, fallido)
    public function consultarVentas (): void{
        $estado = $_GET['estado'] ?? null;
        http_response_code(200);
        echo json_encode($this->service->listar($estado));
    }
}

?>
