<?php

class ventaRepository{
    private PDO $pdo; //php data objects

    //recibe la conexion a la base y la guarda para usarla en los metodos
    public function __construct(PDO $pdo){
        $this->pdo = $pdo;
    }

    //guarda la venta, sus productos y un envio por cada destino true = era nueva, false = ya existia
    public function guardar(ventaDTO $venta, array $destinos): bool{
        try{
            $this->pdo->beginTransaction(); //nada es definitivo hasta el commit, si falla algo se deshace todo

            //guardo los datos generales de la venta. si ya existe (mismo origen y ventaId) aca salta el error
            $this->pdo->prepare(
                "INSERT INTO ventas (origen, ventaId, fecha, cajeroId) VALUES (?, ?, ?, ?)" //los ? se rellenan con los datos del execute en orden
            )->execute([$venta->origen, $venta->ventaId, $venta->fecha, $venta->cajeroId]);

            //preparo una sola vez el insert de productos y lo uso por cada producto
            $insProducto = $this->pdo->prepare(
                "INSERT INTO ventaproducto (origen, ventaId, codigo, cantidad, precioUnitario) VALUES (?, ?, ?, ?, ?)"
            );
            foreach ($venta->productos as $p) { //recorro los productos uno por uno
                $insProducto->execute([
                    $venta->origen, $venta->ventaId, $p->codigo, $p->cantidad,
                    $p->precioUnitarioCentavos === null ? null : $p->precioUnitarioCentavos / 100 //estaba en centavos, lo paso a pesos para la columna (si no hay precio queda null)
                ]);
            }

            $insEnvio = $this->pdo->prepare(
                "INSERT INTO envio (origen, ventaId, destino) VALUES (?, ?, ?)"
            );
            foreach ($destinos as $destino) { //recorro los destinos, uno por uno
                $insEnvio->execute([$venta->origen, $venta->ventaId, $destino]);
            }

            $this->pdo->commit(); //recien aca queda guardado todo de verdad
            return true; //venta nueva
        } catch (PDOException $e) {
            $this->pdo->rollBack(); //deshago lo que se haya guardado a medias
            if ($e->errorInfo[1] === 1062) { //1062 = clave duplicada, o sea la venta ya estaba guardada
                return false; //venta repetida, no se vuelve a procesar
            }
            throw $e; //cualquier otro error lo dejo pasar para que se vea
        }
    }

    //despues de intentar mandar a un destino anota como salio suma un intento y cambia el estado (1 pendiente, 2 procesado, 3 fallido)
    public function marcarEnvio(string $origen, string $ventaId, string $destino, bool $ok, ?string $error): void{
        $consulta = $this->pdo->prepare("SELECT intentos FROM envio WHERE origen = ? AND ventaId = ? AND destino = ?");
        $consulta->execute([$origen, $ventaId, $destino]);
        $intentos = (int)$consulta->fetchColumn() + 1; //le sumo el intento que acabo de hacer

        if ($ok){
            $estado = 2; //procesado
        } elseif ($intentos >= 3){
            $estado = 3; //fallido, ya se intento 3 veces
        } else {
            $estado = 1; //sigue pendiente, se puede reintentar
        }

        $this->pdo->prepare(
            "UPDATE envio SET estado_id = ?, intentos = ?, ultimoError = ? WHERE origen = ? AND ventaId = ? AND destino = ?"
        )->execute([$estado, $intentos, $error, $origen, $ventaId, $destino]);
    }

    //devuelve los envios que quedaron pendientes (para reintentarlos)
    public function pendientes(): array{
        $consulta = $this->pdo->query("SELECT origen, ventaId, destino FROM envio WHERE estado_id = 1");
        return $consulta->fetchAll(PDO::FETCH_ASSOC);
    }

    //arma de nuevo la venta desde la base para reintentar el envio
    public function buscar(string $origen, string $ventaId): ?ventaDTO{
        $consulta = $this->pdo->prepare("SELECT fecha, cajeroId FROM ventas WHERE origen = ? AND ventaId = ?");
        $consulta->execute([$origen, $ventaId]);
        $fila = $consulta->fetch(PDO::FETCH_ASSOC);
        if (!$fila){
            return null; //no existe esa venta
        }

        $consulta = $this->pdo->prepare("SELECT codigo, cantidad, precioUnitario FROM ventaproducto WHERE origen = ? AND ventaId = ?");
        $consulta->execute([$origen, $ventaId]);
        $productos = [];
        foreach ($consulta->fetchAll(PDO::FETCH_ASSOC) as $p){ //un ventaProducto por cada fila
            $precio = null; //si no tiene precio (A2) queda null
            if ($p['precioUnitario'] !== null){
                $precio = (float)$p['precioUnitario']; //de la base viene como texto, lo paso a numero
            }
            $productos[] = new ventaProductoDTO($p['codigo'], (int)$p['cantidad'], $precio);
        }

        $cajeroId = $fila['cajeroId'] === null ? null : (int)$fila['cajeroId'];
        return new ventaDTO((int)$ventaId, $fila['fecha'], $cajeroId, $productos, $origen);
    }

    public function listar(?string $estado): array{
        $sql = "SELECT e.origen, e.ventaId, e.destino, s.nombre AS estado, e.intentos, e.ultimoError
                FROM envio e JOIN estadoventa s ON s.id = e.estado_id";
        if ($estado === null){
            $consulta = $this->pdo->query($sql); //sin filtro, trae todos
        } else {
            $consulta = $this->pdo->prepare($sql . " WHERE s.nombre = ?"); //con filtro, solo los de ese estado
            $consulta->execute([$estado]);
        }
        return $consulta->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>
