<?php

require_once __DIR__ . '/../src/moldes/venta.php';
require_once __DIR__ . '/../src/moldes/ventaProducto.php';
require_once __DIR__ . '/../src/moldes/movimientoProducto.php';
require_once __DIR__ . '/../src/moldes/movimientoStock.php';
require_once __DIR__ . '/../src/transformador/ventaAmovimiento.php';
require_once __DIR__ . '/../src/salida/conectorStock.php';
require_once __DIR__ . '/../src/salida/conectorStockHttp.php';
require_once __DIR__ . '/../src/coordinacion/procesarVenta.php';
require_once __DIR__ . '/../src/entrada/ventaControlador.php';
//conector mapper service controlador 

$conector = new conectorStockHttp();
$transformador= new ventaAmovimiento();
$coordinacion = new procesarVenta($transformador, $conector);
$entrada= new ventaControlador($coordinacion);

$entrada->recibirVenta();

?>