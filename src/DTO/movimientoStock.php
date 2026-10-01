<?php

class movimientoStock { //molde de lo que se le manda al sistema B, asi tiene que quedar lo q mande
    public string $tipoMovimiento; //salida
    public string $fecha;
    public string $referencia;
    public array $productos; 

    public function __construct(string $tipoMovimiento, string $fecha, string $referencia, array $productos) { //recibe los datos y los guarda
        $this->tipoMovimiento = $tipoMovimiento;
        $this->fecha = $fecha;
        $this->referencia = $referencia;
        $this->productos = $productos;
    }

    public function paraEnviar(): array { //arma los datos con lo que pide el formato b, para pasarlo a json y poder corregirlo mas facil si piden un cambio
        $listaproductos = []; //creo una lista vacia para llenarla con codigoproducto y cantidad que traemos de movimiento producto
        foreach($this->productos as $producto){ ////recorro los productos, uno por uno
            $listaproductos[] = $producto->paraEnviar(); //le pido a cada producto su paraEnviar() y guardo lo que devuelve en listaproductos
        }
        return[
            'tipoMovimiento' => $this->tipoMovimiento,
            'fecha' => $this->fecha, //por ejemplo, si no quieren mas fecha lo saco de aca
            'referencia' => $this->referencia, //o si referencia pasa a tener otro nombre, lo corrijo aca
            'productos' => $listaproductos, //ahora dentro de productos está la lista de cada producto
        ];
    }
}

?>