<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
require_once '../Config/Banco.php'; 
require_once 'CaronaController.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    $id_carona = (int) $_POST['id'];
    
    try {
        $db = (new Banco())->conectar();
        $controller = new CaronaController($db);
        $controller->atualizar($id_carona, $_POST);
    } catch (Exception $e) {
        die("Erro de sistema: " . $e->getMessage());
    }
} else {
    header('Location: ../views/caronas/index.php');
    exit;
}
