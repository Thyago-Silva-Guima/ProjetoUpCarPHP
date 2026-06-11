<?php

require_once __DIR__ . '/../Config/Banco.php';
require_once __DIR__ . '/../controllers/GeralController.php';

$db = Banco::getConexao();
$controller = new GeralController($db);
$dados = $controller->listarUsuarios();

require_once __DIR__ . '/../views/feed/usuarios.php';