<?php

//se corre a mano con: php reintentar.php, vuelve a mandar lo que quedó pendiente
require_once __DIR__ . '/src/DTO/ventaDTO.php';
require_once __DIR__ . '/src/DTO/ventaProductoDTO.php';
require_once __DIR__ . '/src/DTO/movimientoProductoDTO.php';
require_once __DIR__ . '/src/DTO/movimientoStockDTO.php';
require_once __DIR__ . '/src/DTO/estadisticaVentaDTO.php';
require_once __DIR__ . '/src/mapper/ventaAmovimientoMapper.php';
require_once __DIR__ . '/src/mapper/ventaAestadisticaMapper.php';
require_once __DIR__ . '/src/salida/conectorStock.php';
require_once __DIR__ . '/src/salida/conectorStockHttp.php';
require_once __DIR__ . '/src/salida/conectorEstadisticas.php';
require_once __DIR__ . '/src/salida/conectorEstadisticasHttp.php';
require_once __DIR__ . '/src/observer/ventaObserver.php';
require_once __DIR__ . '/src/observer/stockObserver.php';
require_once __DIR__ . '/src/observer/estadisticasObserver.php';
require_once __DIR__ . '/src/repository/conexionBase.php';
require_once __DIR__ . '/src/repository/ventaRepository.php';
require_once __DIR__ . '/src/service/procesarVentaService.php';

$observadores = [
    new stockObserver(new ventaAmovimientoMapper(), new conectorStockHttp()),
    new estadisticasObserver(new ventaAestadisticaMapper(), new conectorEstadisticasHttp()),
];
$repository = new ventaRepository(conexionBase::obtener());
$service = new procesarVentaService($repository, $observadores);

$cantidad = $service->reintentarPendientes();
echo "Envios reintentados: " . $cantidad . "\n";

?>
