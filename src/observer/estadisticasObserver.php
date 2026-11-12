<?php

//le avisa al sistema C (estadisticas) cuando hay una venta
class estadisticasObserver implements ventaObserver{
    private ventaAestadisticaMapper $mapper;
    private conectorEstadisticas $conector;

    public function __construct(ventaAestadisticaMapper $mapper, conectorEstadisticas $conector){
        $this->mapper = $mapper;
        $this->conector = $conector;
    }

    public function nombre(): string{
        return "estadisticas";
    }

    public function avisar(ventaDTO $venta): bool{
        $estadistica = $this->mapper->transformar($venta); //venta a -> c
        return $this->conector->enviarEstadistica($estadistica);
    }
}

?>
