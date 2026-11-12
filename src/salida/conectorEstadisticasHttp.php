<?php

class conectorEstadisticasHttp implements conectorEstadisticas{
    public function enviarEstadistica(estadisticaVentaDTO $estadistica): bool{
        $json = json_encode($estadistica->paraEnviar());

        $url = "http://localhost:3001/api/estadisticas/ventas"; 
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $json);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $respuesta = curl_exec($ch);
        $codigo = curl_getinfo($ch, CURLINFO_HTTP_CODE); //el numero con el que contesto C (200, 500, etc)
        curl_close($ch);

        // si no llegó a C, curl_exec devuelve false. Si llegó pero contesto con error (400, 500) 
        if ($respuesta === false || $codigo < 200 || $codigo >= 300){
            return false;
        }
        return true;
    }
}

?>
