<?php

require_once __DIR__ . '/../models/Relatorio.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

class GeralController {
    private Relatorio $relatorioModel;

    public function __construct(PDO $db) {
        $this->relatorioModel = new Relatorio($db);
    }

    public function feed(): array {
        if (!isset($_SESSION['usuario_id'])) {
            header("Location: ../views/auth/login.php");
            exit;
        }

        $origem  = isset($_GET['origem'])  ? htmlspecialchars(trim($_GET['origem']))  : '';
        $destino = isset($_GET['destino']) ? htmlspecialchars(trim($_GET['destino'])) : '';

        if (!empty($origem) && mb_strlen($origem) < 2)   $origem  = '';
        if (!empty($destino) && mb_strlen($destino) < 2) $destino = '';

        $caronas = $this->relatorioModel->buscarCaronas($origem, $destino);

        return [
            'caronas'        => $caronas,
            'filtro_origem'  => $origem,
            'filtro_destino' => $destino,
            'total'          => count($caronas),
        ];
    }

    public function listarUsuarios(): array {
        if (!isset($_SESSION['usuario_id'])) {
            header("Location: ../views/auth/login.php");
            exit;
        }

        $usuarios    = $this->relatorioModel->listarUsuarios((int) $_SESSION['usuario_id']);
        $motoristas  = [];
        $passageiros = [];

        foreach ($usuarios as $usuario) {
            if ($usuario['tipo_usuario'] === 'motorista') {
                $motoristas[] = $usuario;
            } else {
                $passageiros[] = $usuario;
            }
        }

        return [
            'motoristas'  => $motoristas,
            'passageiros' => $passageiros,
            'todos'       => $usuarios,
        ];
    }

    public function reportar(array $dadosPost): void {
        if (!isset($_SESSION['usuario_id'])) {
            header("Location: ../views/auth/login.php");
            exit;
        }

        if (!isset($dadosPost['csrf_token']) || $dadosPost['csrf_token'] !== $_SESSION['csrf_token']) {
            die("Erro de validação de formulário (CSRF).");
        }

        $denunciante_id = (int) $_SESSION['usuario_id'];
        $denunciado_id  = (int) ($dadosPost['denunciado_id'] ?? 0);
        $motivo         = trim($dadosPost['motivo'] ?? '');

        if ($denunciante_id === $denunciado_id || $denunciado_id <= 0) {
            echo "<script>alert('Operação inválida: você não pode denunciar a si mesmo.'); window.history.back();</script>";
            return;
        }

        if (empty($motivo) || mb_strlen($motivo) < 10) {
            echo "<script>alert('O motivo da denúncia deve ter pelo menos 10 caracteres.'); window.history.back();</script>";
            return;
        }

        if ($this->relatorioModel->jaReportou($denunciante_id, $denunciado_id)) {
            echo "<script>alert('Você já registrou uma denúncia contra este usuário.'); window.history.back();</script>";
            return;
        }

        $denunciado = $this->relatorioModel->buscarUsuarioPorId($denunciado_id);
        if (!$denunciado) {
            echo "<script>alert('Usuário não encontrado.'); window.history.back();</script>";
            return;
        }

        $motivoSeguro = htmlspecialchars($motivo);
        $sucesso = $this->relatorioModel->criarRelatorio($denunciante_id, $denunciado_id, $motivoSeguro);

        if ($sucesso) {
            header("Location: ../views/relatorios/confirmacao.php?denunciado=" . urlencode($denunciado['nome']));
            exit;
        } else {
            echo "<script>alert('Erro ao registrar a denúncia. Tente novamente.'); window.history.back();</script>";
        }
    }
}