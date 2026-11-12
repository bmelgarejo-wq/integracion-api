<?php

//contrato de los que se enteran de una venta (stock, estadisticas, y los que vengan)
interface ventaObserver{
    public function nombre(): string; //nombre del destino, es el que se guarda en la tabla envio
    public function avisar(ventaDTO $venta): bool; //true si le llego bien
}

?>
