<?php
// controllers/AuthController.php — Controla login, registro, recuperação e logout

require_once __DIR__ . '/../Config/Banco.php'; // ✅ path atualizado para Config/ (maiúsculo)
require_once __DIR__ . '/../models/Usuario.php';

class AuthController {
    private $model;

    public function __construct() {
        if (session_status() === PHP_SESSION_NONE) session_start();

        // Gera token CSRF global na sessão (protege todos os formulários)
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }

        $this->model = new Usuario();
    }

    // Verifica se o token do formulário bate com o da sessão
    private function csrfValido() {
        return isset($_POST['csrf_token']) &&
               hash_equals($_SESSION['csrf_token'], $_POST['csrf_token']);
    }

    // Atalho para redirecionar com mensagem de erro ou sucesso
    private function redirecionar($action, $erro = null, $sucesso = null) {
        if ($erro)    $_SESSION['erro']    = $erro;
        if ($sucesso) $_SESSION['sucesso'] = $sucesso;
        header("Location: index.php?action=$action");
        exit;
    }

    // --- Exibir telas ---
    public function exibirLogin()       { require __DIR__ . '/../views/auth/login.php'; }
    public function exibirRegistro()    { require __DIR__ . '/../views/auth/registro.php'; }
    public function exibirRecuperacao() { require __DIR__ . '/../views/auth/recuperar_senha.php'; }

    // --- Processar Login ---
    public function processarLogin() {
        if (!$this->csrfValido()) {
            $this->redirecionar('login', 'Requisição inválida.');
        }

        $email = trim($_POST['email'] ?? '');
        $senha = $_POST['senha'] ?? '';

        $usuario = $this->model->buscarPorEmail($email);

        // password_verify compara a senha digitada com o hash salvo no banco
        if ($usuario && password_verify($senha, $usuario['senha'])) {
            session_regenerate_id(true); // segurança: troca o ID da sessão após login
            $_SESSION['usuario_id']   = $usuario['id'];
            $_SESSION['usuario_nome'] = $usuario['nome'];

            // ✅ CORREÇÃO: chave renomeada de 'usuario_tipo' para 'tipo_usuario'
            // O CaronaController do Aluno 2 verifica $_SESSION['tipo_usuario'].
            // Se a chave fosse diferente, o motorista nunca passaria pela verificação
            // de acesso e recebia "Acesso negado" em todas as operações.
            $_SESSION['tipo_usuario'] = $usuario['tipo_usuario'];

            // Cookie "lembrar e-mail" por 7 dias
            if (!empty($_POST['lembrar'])) {
                setcookie('upcar_email', $email, time() + 604800, '/', '', false, true);
            }

            header('Location: index.php?action=dashboard');
            exit;
        }

        $this->redirecionar('login', 'E-mail ou senha incorretos.');
    }

    // --- Processar Registro ---
    public function processarRegistro() {
        if (!$this->csrfValido()) {
            $this->redirecionar('registro', 'Requisição inválida.');
        }

        $nome            = trim($_POST['nome']   ?? '');
        $email           = trim($_POST['email']  ?? '');
        $senha           = $_POST['senha']        ?? '';
        $cpf             = preg_replace('/\D/', '', $_POST['cpf'] ?? '');
        $data_nascimento = $_POST['data_nascimento'] ?? '';
        $tipo_usuario    = $_POST['tipo_usuario'] ?? '';

        // Validações com if/else e switch
        $erros = [];

        if (strlen($nome) < 3) {
            $erros[] = 'Nome deve ter pelo menos 3 caracteres.';
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $erros[] = 'E-mail inválido.';
        }

        if (strlen($senha) < 6) {
            $erros[] = 'Senha deve ter no mínimo 6 caracteres.';
        }

        if (strlen($cpf) !== 11) {
            $erros[] = 'CPF inválido.';
        }

        switch ($tipo_usuario) {
            case 'passageiro':
            case 'motorista':
                break; // válido
            default:
                $erros[] = 'Selecione um tipo de usuário.';
        }

        if (!empty($erros)) {
            $_SESSION['erros'] = $erros;
            header('Location: index.php?action=registro');
            exit;
        }

        if ($this->model->emailExiste($email)) {
            $this->redirecionar('registro', 'Este e-mail já está cadastrado.');
        }

        $this->model->registrar([
            'nome'            => $nome,
            'email'           => $email,
            'senha'           => $senha,
            'cpf'             => $cpf,
            'data_nascimento' => $data_nascimento,
            'tipo_usuario'    => $tipo_usuario,
        ]);

        $this->redirecionar('login', null, 'Cadastro realizado! Faça login.');
    }

    // --- Processar Recuperação de Senha ---
    public function processarRecuperacao() {
        if (!$this->csrfValido()) {
            $this->redirecionar('recuperar', 'Requisição inválida.');
        }

        $cpf  = preg_replace('/\D/', '', $_POST['cpf'] ?? '');
        $data = $_POST['data_nascimento'] ?? '';
        $nova = $_POST['nova_senha'] ?? '';

        $usuario = $this->model->buscarPorCpfEData($cpf, $data);

        if ($usuario) {
            if (strlen($nova) < 6) {
                $this->redirecionar('recuperar', 'Nova senha deve ter no mínimo 6 caracteres.');
            }
            $this->model->atualizarSenha($usuario['id'], $nova);
            $this->redirecionar('login', null, 'Senha redefinida com sucesso!');
        } else {
            $this->redirecionar('recuperar', 'CPF ou data de nascimento não encontrados.');
        }
    }

    // --- Logout ---
    public function logout() {
        session_unset();
        session_destroy();
        setcookie('upcar_email', '', time() - 3600, '/');
        header('Location: index.php?action=login');
        exit;
    }
}
