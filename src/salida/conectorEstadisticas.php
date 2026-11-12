<?php

interface conectorEstadisticas{
    public function enviarEstadistica(estadisticaVentaDTO $estadistica): bool;
}

?>
