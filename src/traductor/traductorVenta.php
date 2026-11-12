<?php

//cada sistema que manda ventas tiene su traductor, todos tienen que cumplir esto
interface traductorVenta{
    public function puedeTraducir(array $datos): bool; //mira si el json es del formato de este sistema
    public function traducir(array $datos): ventaDTO; //pasa el json a ventaDTO
}

?>
