<?php

require_once __DIR__ . '/../Config/Banco.php';
require_once __DIR__ . '/../controllers/GeralController.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../views/feed/index.php");
    exit;
}

$db = Banco::getConexao();
$controller = new GeralController($db);
$controller->reportar($_POST);