<?php

//no guarda ni envía por su cuenta, le pide al repository que guarde y a los observers que avisen
class procesarVentaService {
    private ventaRepository $repository;
    private array $observadores; //lista de observers, uno por cada sistema al que hay que avisar (stock, estadisticas)

    /*el repository y los observers se reciben desde afuera (desde el index.php)
    en vez de crearlos aca con new, asi se puede sumar un sistema nuevo sin tocar esta clase*/
    public function __construct (ventaRepository $repository, array $observadores){
        $this->repository = $repository;
        $this->observadores = $observadores;
    }

    // recibe una venta, la guarda y le avisa a cada sistema. Devuelve "duplicada", "procesada" o "pendiente"
    public function procesar(ventaDTO $venta): string {
        $destinos = []; //lista vacia para llenarla con el nombre de cada destino
        foreach ($this->observadores as $observador){
            $destinos[] = $observador->nombre();
        }

        //guardo primero, asi si B o C fallan la venta no se pierde
        $esNueva = $this->repository->guardar($venta, $destinos);
        if (!$esNueva){
            return "duplicada"; //ya estaba guardada, no le aviso a nadie de nuevo
        }

        $todoOk = true;
        foreach ($this->observadores as $observador){
            if (!$this->avisar($observador, $venta)){
                $todoOk = false; //si uno falla igual sigo con los demas
            }
        }
        return $todoOk ? "procesada" : "pendiente";
    }

    // vuelve a mandar lo que quedo pendiente. Devuelve cuantos reintento
    public function reintentarPendientes(): int {
        $cantidad = 0;
        foreach ($this->repository->pendientes() as $envio){
            $venta = $this->repository->buscar($envio['origen'], $envio['ventaId']);
            if ($venta === null){
                continue;
            }
            foreach ($this->observadores as $observador){
                if ($observador->nombre() === $envio['destino']){ //reintento solo el destino que fallo
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
