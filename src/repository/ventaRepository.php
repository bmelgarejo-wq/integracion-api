<?php

class ventaRepository{
    private PDO $pdo; //php data objects

    //recibe la conexion a la base y la guarda para usarla en los metodos
    public function __construct(PDO $pdo){
        $this->pdo = $pdo;
    }

    //guarda la venta con sus productos y un envio por cada destino. true = venta nueva, false = ya estaba
    public function guardar(ventaDTO $venta, array $destinos): bool{
        try{
            $this->pdo->beginTransaction(); //hasta el commit no queda nada guardado, si falla algo se deshace todo

            //primero la venta, si ya existe (mismo origen y ventaId) falla aca y salta al catch
            $this->pdo->prepare(
                "INSERT INTO ventas (origen, ventaId, fecha, cajeroId) VALUES (?, ?, ?, ?)" //los ? se rellenan con lo que va en el execute, en orden
            )->execute([$venta->origen, $venta->ventaId, $venta->fecha, $venta->cajeroId]);

            //preparo el insert una vez y lo uso por cada producto
            $insProducto = $this->pdo->prepare(
                "INSERT INTO ventaproducto (origen, ventaId, codigo, cantidad, precioUnitario) VALUES (?, ?, ?, ?, ?)"
            );
            foreach ($venta->productos as $p) { //recorro los productos, uno por uno
                $precio = null; //si no tiene precio (A2) queda null
                if ($p->precioUnitarioCentavos !== null){
                    $precio = $p->precioUnitarioCentavos / 100; //estaba en centavos, lo paso a pesos
                }
                $insProducto->execute([$venta->origen, $venta->ventaId, $p->codigo, $p->cantidad, $precio]);
            }

            //un envio por cada destino (stock, estadisticas), arrancan pendientes por el default de la tabla
            $insEnvio = $this->pdo->prepare(
                "INSERT INTO envio (origen, ventaId, destino) VALUES (?, ?, ?)"
            );
            foreach ($destinos as $destino) { //recorro los destinos, uno por uno
                $insEnvio->execute([$venta->origen, $venta->ventaId, $destino]);
            }

            $this->pdo->commit(); //recien aca se guarda de verdad
            return true; //era nueva
        } catch (PDOException $e) {
            $this->pdo->rollBack(); //deshago lo que se haya guardado a medias
            if ($e->errorInfo[1] === 1062) { //1062 es el error de clave repetida, o sea que la venta ya estaba
                return false;
            }
            throw $e; //si es otro error lo dejo pasar para que se vea
        }
    }

    //despues de mandar a un destino anota como salio: suma un intento y cambia el estado (1 pendiente, 2 procesado, 3 fallido)
    public function marcarEnvio(string $origen, string $ventaId, string $destino, bool $ok, ?string $error): void{
        $consulta = $this->pdo->prepare("SELECT intentos FROM envio WHERE origen = ? AND ventaId = ? AND destino = ?");
        $consulta->execute([$origen, $ventaId, $destino]);
        $intentos = (int)$consulta->fetchColumn() + 1; //le sumo el intento que recien hice

        if ($ok){
            $estado = 2; //procesado
        } elseif ($intentos >= 3){
            $estado = 3; //fallido, ya lo intente 3 veces
        } else {
            $estado = 1; //sigue pendiente, se puede reintentar
        }

        $this->pdo->prepare(
            "UPDATE envio SET estado_id = ?, intentos = ?, ultimoError = ? WHERE origen = ? AND ventaId = ? AND destino = ?"
        )->execute([$estado, $intentos, $error, $origen, $ventaId, $destino]);
    }

    //devuelve los envios que quedaron pendientes, para reintentarlos
    public function pendientes(): array{
        $consulta = $this->pdo->query("SELECT origen, ventaId, destino FROM envio WHERE estado_id = 1");
        return $consulta->fetchAll(PDO::FETCH_ASSOC);
    }

    //arma de nuevo la venta desde la base, la necesito para poder reintentar
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

        $cajeroId = null; //A2 no manda cajero
        if ($fila['cajeroId'] !== null){
            $cajeroId = (int)$fila['cajeroId'];
        }
        return new ventaDTO((int)$ventaId, $fila['fecha'], $cajeroId, $productos, $origen);
    }

    //lista los envios con su estado, si me pasan un estado trae solo los de ese estado
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
