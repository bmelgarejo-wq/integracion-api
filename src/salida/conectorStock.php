<?php

interface conectorStock{
    public function enviarMovimiento(movimientoStock $movimiento): bool;
}

?>