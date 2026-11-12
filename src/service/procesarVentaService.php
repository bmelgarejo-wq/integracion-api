<?php

//no guarda ni envía por su cuenta, le pide al repository que guarde y a los observers que avisen
class procesarVentaService {
    private ventaRepository $repository;
    private array $observadores; //lista de ventaObserver, uno por cada sistema al que hay que avisar

    /*el repository y los observers se reciben desde afuera (desde arranque.php)
    en vez de crearlos aca con new asi se puede agregar un sistema nuevo sin tocar esta clase*/
    public function __construct (ventaRepository $repository, array $observadores){
        $this->repository = $repository;
        $this->observadores = $observadores;
    }

    // recibe una venta, la guarda y avisa a cada sistema. Devuelve "duplicada", "procesada" o "pendiente"
    public function procesar(ventaDTO $venta): string {
        $destinos = [];
        foreach ($this->observadores as $observador){
            $destinos[] = $observador->nombre(); //saco el nombre de cada destino para guardarlo
        }

        //primero guardo, asi si B o C fallan la venta no se pierde
        $esNueva = $this->repository->guardar($venta, $destinos);
        if (!$esNueva){
            return "duplicada"; //ya estaba, no se avisa de nuevo a nadie
        }

        $todoOk = true;
        foreach ($this->observadores as $observador){
            if (!$this->avisar($observador, $venta)){
                $todoOk = false; //si uno falla sigo con los demas igual
            }
        }
        return $todoOk ? "procesada" : "pendiente";
    }

    // vuelve a intentar los envios que quedaron pendientes. Devuelve cuantos reintento
    public function reintentarPendientes(): int {
        $cantidad = 0;
        foreach ($this->repository->pendientes() as $envio){
            $venta = $this->repository->buscar($envio['origen'], $envio['ventaId']);
            if ($venta === null){
                continue;
            }
            foreach ($this->observadores as $observador){
                if ($observador->nombre() === $envio['destino']){ //solo reintento el destino que fallo
                    $this->avisar($observador, $venta);
                    $cantidad++;
                }
            }
        }
        return $cantidad;
    }

    // devuelve los envios con su estado
    public function listar(?string $estado): array {
        return $this->repository->listar($estado);
    }

    // le avisa a un observer y anota como salio
    private function avisar(ventaObserver $observador, ventaDTO $venta): bool {
        $ok = $observador->avisar($venta);
        $error = $ok ? null : "no se pudo enviar";
        $this->repository->marcarEnvio($venta->origen, (string)$venta->ventaId, $observador->nombre(), $ok, $error);
        return $ok;
    }
}

?>
