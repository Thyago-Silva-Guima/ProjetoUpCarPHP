<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../models/Reserva.php';

class ReservaController {
    private Reserva $modelReserva;

    private const STATUS_PERMITIDOS = ['aceito', 'recusado'];

    public function __construct(PDO $conexao) {
        $this->modelReserva = new Reserva($conexao);
    }

    public function processarRequisicao(): void {
        $acaoSolicitada = $_GET['action'] ?? 'minhasReservas';

        switch ($acaoSolicitada) {
            case 'solicitar':
                $this->exibirFormularioEProcessarSolicitacao();
                break;
            case 'alterarStatus':
                $this->processarAlteracaoDeStatus();
                break;
            case 'cancelar':
                $this->processarCancelamento();
                break;
            case 'gerenciar':
                $this->exibirPainelDoMotorista();
                break;
            case 'historico':
                $this->exibirHistoricoDeParceiros();
                break;
            case 'minhasReservas':
            default:
                $this->exibirReservasDoPassageiro();
                break;
        }
    }

    private function exibirFormularioEProcessarSolicitacao(): void {
        $caronaId = (int) ($_GET['carona_id'] ?? 0);

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->bloquearRequisicaoSemTokenCsrfValido($_POST);
            $this->bloquearAcessoDeMotorista();

            $passageiroId   = (int) $_SESSION['usuario_id'];
            $vagaSolicitada = $this->modelReserva->solicitarVaga($caronaId, $passageiroId);

            if ($vagaSolicitada) {
                $this->definirMensagemFlash('sucesso', 'Vaga solicitada com sucesso! Aguarde a confirmacao do motorista.');
            } else {
                $this->definirMensagemFlash('erro', 'Nao foi possivel solicitar a vaga. Voce ja reservou esta carona ou nao ha vagas disponiveis.');
            }

            header('Location: index.php?action=minhasReservas');
            exit;
        }

        require_once __DIR__ . '/../views/reservas/solicitar.php';
    }

    private function processarAlteracaoDeStatus(): void {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?action=gerenciarReservas');
            exit;
        }

        $this->bloquearRequisicaoSemTokenCsrfValido($_POST);
        $this->bloquearAcessoDePassageiro();

        $reservaId  = (int) ($_POST['reserva_id'] ?? 0);
        $novoStatus = $_POST['status'] ?? '';

        if (!in_array($novoStatus, self::STATUS_PERMITIDOS, true)) {
            $this->definirMensagemFlash('erro', 'Status informado e invalido.');
            header('Location: index.php?action=gerenciarReservas');
            exit;
        }

        $motoristaId = (int) $_SESSION['usuario_id'];
        $statusAlterado = $this->modelReserva->alterarStatusDaReserva($reservaId, $motoristaId, $novoStatus);

        if ($statusAlterado) {
            $mensagemDeSucesso = $novoStatus === 'aceito'
                ? 'Reserva aceita. A vaga foi decrementada automaticamente.'
                : 'Reserva recusada.';
            $this->definirMensagemFlash('sucesso', $mensagemDeSucesso);
        } else {
            $this->definirMensagemFlash('erro', 'Erro ao alterar o status da reserva.');
        }

        header('Location: index.php?action=gerenciarReservas');
        exit;
    }

    private function processarCancelamento(): void {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?action=minhasReservas');
            exit;
        }

        $this->bloquearRequisicaoSemTokenCsrfValido($_POST);
        $this->bloquearAcessoDeMotorista();

        $reservaId        = (int) ($_POST['reserva_id'] ?? 0);
        $passageiroId     = (int) $_SESSION['usuario_id'];
        $reservaCancelada = $this->modelReserva->cancelarReservaDoPassageiro($reservaId, $passageiroId);

        if ($reservaCancelada) {
            $this->definirMensagemFlash('sucesso', 'Reserva cancelada com sucesso.');
        } else {
            $this->definirMensagemFlash('erro', 'Não foi possível cancelar a reserva.');
        }

        header('Location: index.php?action=minhasReservas');
        exit;
    }

    private function exibirPainelDoMotorista(): void {
        $this->bloquearAcessoDePassageiro();

        $motoristaId = (int) $_SESSION['usuario_id'];
        $reservas = $this->modelReserva->listarSolicitacoesRecebidasPeloMotorista($motoristaId);

        require_once __DIR__ . '/../views/reservas/gerenciar.php';
    }

    private function exibirReservasDoPassageiro(): void {
        $this->bloquearAcessoDeMotorista();

        $passageiroId = (int) $_SESSION['usuario_id'];
        $reservas     = $this->modelReserva->listarReservasDoPassageiro($passageiroId);

        require_once __DIR__ . '/../views/reservas/solicitar.php';
    }

    private function exibirHistoricoDeParceiros(): void {
        $usuarioId = (int) $_SESSION['usuario_id'];
        $tipoUsuario = $_SESSION['tipo_usuario'];
        $parceiros   = $this->modelReserva->listarParceirosDaViagemDoUsuario($usuarioId, $tipoUsuario);

        require_once __DIR__ . '/../views/reservas/historico.php';
    }

    private function bloquearAcessoDePassageiro(): void {
        if (!isset($_SESSION['usuario_id']) || $_SESSION['tipo_usuario'] !== 'motorista') {
            $this->definirMensagemFlash('erro', 'Acesso restrito a motoristas.');
            header('Location: index.php?action=dashboard');
            exit;
        }
    }

    private function bloquearAcessoDeMotorista(): void {
        if (!isset($_SESSION['usuario_id']) || $_SESSION['tipo_usuario'] !== 'passageiro') {
            $this->definirMensagemFlash('erro', 'Acesso restrito a passageiros.');
            header('Location: index.php?action=dashboard');
            exit;
        }
    }

    private function bloquearRequisicaoSemTokenCsrfValido(array $dadosPost): void {
        if (!isset($dadosPost['csrf_token']) || $dadosPost['csrf_token'] !== $_SESSION['csrf_token']) {
            die('Erro de validacao de formulario (CSRF).');
        }
    }

    private function definirMensagemFlash(string $tipo, string $mensagem): void {
        $_SESSION['flash'] = ['tipo' => $tipo, 'msg' => $mensagem];
    }
}