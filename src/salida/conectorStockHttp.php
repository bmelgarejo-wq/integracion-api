<?php

class conectorStockHttp implements conectorStock{
    public function enviarMovimiento(movimientoStockDTO $movimiento): bool{
        $datos = $movimiento->paraEnviar();
        $json = json_encode($datos);

        $url = "http://localhost:3000/api/stock/movimiento";
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $json);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $respuesta = curl_exec($ch);
        $codigo = curl_getinfo($ch, CURLINFO_HTTP_CODE); //el numero con el que contesto B (200, 500, etc)
        curl_close($ch);

        // si no llegó a B, curl_exec devuelve false. Si llegó pero contesto con error (400, 500) tambien es fallo
        if ($respuesta === false || $codigo < 200 || $codigo >= 300){
            return false;
        }
        return true;
    }
}

?>
