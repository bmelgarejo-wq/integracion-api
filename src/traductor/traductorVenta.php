<?php

interface traductorVenta{
    public function puedeTraducir(array $datos): bool; //se fija si el json es del formato de este sistema
    public function traducir(array $datos): ventaDTO; //pasa el json al ventaDTO
}

?>
