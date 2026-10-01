<?php

require_once __DIR__ . '/../src/DTO/venta.php';
require_once __DIR__ . '/../src/DTO/ventaProducto.php';
require_once __DIR__ . '/../src/DTO/movimientoProducto.php';
require_once __DIR__ . '/../src/DTO/movimientoStock.php';
require_once __DIR__ . '/../src/mapper/ventaAmovimiento.php';
require_once __DIR__ . '/../src/salida/conectorStock.php';
require_once __DIR__ . '/../src/salida/conectorStockHttp.php';
require_once __DIR__ . '/../src/service/procesarVenta.php';
require_once __DIR__ . '/../src/controller/ventaControlador.php';
//conector mapper service controlador 

$conector = new conectorStockHttp();
$transformador= new ventaAmovimiento();
$coordinacion = new procesarVenta($transformador, $conector);
$entrada= new ventaControlador($coordinacion);

$entrada->recibirVenta();

?>