<?php

class conexionBase {
    public static function obtener(): PDO {
        $pdo = new PDO(
            "mysql:host=localhost;dbname=pd2;charset=utf8mb4",
            "root",
            ""
        );
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $pdo;
    }
}