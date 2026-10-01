<?php

class conectorStockHttp implements conectorStock{
    public function enviarMovimiento(movimientoStock $movimiento): void{
        $datos = $movimiento->paraEnviar();
        $json = json_encode($datos);

        $url = "http://localhost:3000/api/stock/movimiento";
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $json);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $respuesta = curl_exec($ch);
    }
}

?>