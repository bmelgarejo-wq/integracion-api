<?php

interface conectorStock{
    public function enviarMovimiento(movimientoStockDTO $movimiento): bool;
}

?>
