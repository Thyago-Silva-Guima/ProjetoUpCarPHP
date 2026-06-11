<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }

require_once '../Config/Banco.php';
require_once 'CaronaController.php';

if ($_SERVER['REQUEST_METHOD']=== 'POST'){
    try{
        $db = (new Banco())->conectar();

        $controller = new CaronaController($db);

        $controller ->armazenar($_POST);
    }catch(exception $e){
        die("Erro de sistema: " . $e->getMessage());
    }
}else{
    header('Location: ../views/caronas/index.php');
    exit;
}