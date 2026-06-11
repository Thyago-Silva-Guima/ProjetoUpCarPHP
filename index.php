<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/controllers/AuthController.php';
require_once __DIR__ . '/controllers/ReservaController.php';

$action = $_GET['action'] ?? 'login';
$auth   = new AuthController();

switch ($action) {

    case 'login':
        $auth->exibirLogin();
        break;
    case 'processarLogin':
        $auth->processarLogin();
        break;
    case 'registro':
        $auth->exibirRegistro();
        break;
    case 'processarRegistro':
        $auth->processarRegistro();
        break;
    case 'recuperar':
        $auth->exibirRecuperacao();
        break;
    case 'processarRecuperacao':
        $auth->processarRecuperacao();
        break;
    case 'logout':
        $auth->logout();
        break;

    case 'dashboard':
        if (empty($_SESSION['usuario_id'])) {
            header('Location: index.php?action=login');
            exit;
        }
        if ($_SESSION['tipo_usuario'] === 'motorista') {
            header('Location: views/caronas/index.php');
        } else {
            header('Location: controllers/buscar_caronas.php');
        }
        exit;
        break;

    case 'minhasReservas':
    case 'solicitarVaga':
    case 'solicitar':
    case 'cancelarReserva':
    case 'cancelar':
    case 'gerenciarReservas':
    case 'gerenciar':
    case 'alterarStatus':
    case 'historicoParceiros':
    case 'historico':
        if (empty($_SESSION['usuario_id'])) {
            header('Location: index.php?action=login');
            exit;
        }

        $traducao = [
            'solicitarVaga'      => 'solicitar',
            'cancelarReserva'    => 'cancelar',
            'gerenciarReservas'  => 'gerenciar',
            'historicoParceiros' => 'historico'
        ];
        
        if (isset($traducao[$action])) {
            $_GET['action'] = $traducao[$action];
        }

        $reserva = new ReservaController(Banco::getConexao());
        $reserva->processarRequisicao();
        break;

    default:
        http_response_code(404);
        echo "<style>body{background:#030712;color:white;font-family:sans-serif;display:flex;align-items:center;justify-content:center;height:100vh;}</style>
              <h1 style='color:#2196f3'>404 — Página não encontrada</h1>";
}