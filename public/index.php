<?php

require_once __DIR__ . '/../src/DTO/ventaDTO.php';
require_once __DIR__ . '/../src/DTO/ventaProductoDTO.php';
require_once __DIR__ . '/../src/DTO/movimientoProductoDTO.php';
require_once __DIR__ . '/../src/DTO/movimientoStockDTO.php';
require_once __DIR__ . '/../src/DTO/estadisticaVentaDTO.php';
require_once __DIR__ . '/../src/mapper/ventaAmovimientoMapper.php';
require_once __DIR__ . '/../src/mapper/ventaAestadisticaMapper.php';
require_once __DIR__ . '/../src/salida/conectorStock.php';
require_once __DIR__ . '/../src/salida/conectorStockHttp.php';
require_once __DIR__ . '/../src/salida/conectorEstadisticas.php';
require_once __DIR__ . '/../src/salida/conectorEstadisticasHttp.php';
require_once __DIR__ . '/../src/observer/ventaObserver.php';
require_once __DIR__ . '/../src/observer/stockObserver.php';
require_once __DIR__ . '/../src/observer/estadisticasObserver.php';
require_once __DIR__ . '/../src/traductor/traductorVenta.php';
require_once __DIR__ . '/../src/traductor/traductorVentaA.php';
require_once __DIR__ . '/../src/traductor/traductorVentaA2.php';
require_once __DIR__ . '/../src/repository/conexionBase.php';
require_once __DIR__ . '/../src/repository/ventaRepository.php';
require_once __DIR__ . '/../src/service/procesarVentaService.php';
require_once __DIR__ . '/../src/controller/ventaController.php';
//repository conector mapper observer traductor service controlador

header('Content-Type: application/json');

//los sistemas a los que se avisa, para agregar uno nuevo se crea su observer y se suma a esta lista
$observadores = [
    new stockObserver(new ventaAmovimientoMapper(), new conectorStockHttp()),
    new estadisticasObserver(new ventaAestadisticaMapper(), new conectorEstadisticasHttp()),
];
$repository = new ventaRepository(conexionBase::obtener());
$service = new procesarVentaService($repository, $observadores);

//un traductor por cada sistema que manda ventas
$traductores = [new traductorVentaA(), new traductorVentaA2()];
$entrada = new ventaController($service, $traductores);

if ($_SERVER["REQUEST_METHOD"] === "GET"){
    $entrada->consultarVentas();
} else {
    $entrada->recibirVenta();
}

?>
