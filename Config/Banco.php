<?php
// Config/Banco.php — Conexão PDO com padrão Singleton
// ATENÇÃO: pasta renomeada de config/ para Config/ (C maiúsculo)
// para compatibilidade com os require_once do Aluno 2.

class Banco {
    private static $instancia = null;


    public function __construct() {}

       public function conectar(): PDO {
        return self::getConexao();
    }

    
    public static function getConexao(): PDO {
        if (self::$instancia === null) {
            self::$instancia = new PDO(
                "mysql:host=localhost;dbname=upcar;charset=utf8mb4",
                "root", "",
                [
                    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES   => false,
                ]
            );
        }
        return self::$instancia;
    }
}
