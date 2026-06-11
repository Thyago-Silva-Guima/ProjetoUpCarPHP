<?php

require_once '../models/Carona.php';

    if(session_status()=== PHP_SESSION_NONE){
        session_start();
    }

    class CaronaController{
        private Carona $caronaModel;

        public function __construct(PDO $db){
            $this->caronaModel = new Carona($db);
        }
        public function armazenar(array $dadosPost){
            if (!isset($_SESSION['usuario_id']) || $_SESSION['tipo_usuario'] !== 'motorista'){
                die("Acesso negado,Apenas motoristas podem criar caronas.");
            }
            if (!isset($dadosPost['csrf_token']) || $dadosPost['csrf_token'] !== $_SESSION['csrf_token']){
                die("Erro de validação de formulário (CSRF).");
            }
            $vagas = (int) $dadosPost['vagas'];
            if ($vagas<1){
                echo"<script>alert('Erro:O veiculo deve ter pelo menos 1 vaga disponível para carona'); window.history.back();</script>";
            }
            $origem = htmlspecialchars(trim($dadosPost['origem']));
            $destino = htmlspecialchars(trim($dadosPost['destino']));
            $dataHora = trim($dadosPost['data_hora']);
                
            $sucesso= $this->caronaModel -> criar($_SESSION['usuario_id'],$origem,$destino,$dataHora,$vagas);
            if ($sucesso){
                header("Location: ../views/caronas/index.php?sucesso=1");
            exit;
            }else{
                echo "Erro ao criar carona no banco de dados.";
            }     
        }

       
    public function excluir(int $id, array $dadosPost) {
       
        if (!isset($_SESSION['usuario_id']) || $_SESSION['tipo_usuario'] !== 'motorista') {
            die("Acesso negado.");
        }

        if (!isset($dadosPost['csrf_token']) || $dadosPost['csrf_token'] !== $_SESSION['csrf_token']) {
            die("Erro de validação de formulário (CSRF).");
        }

        $motorista_id = $_SESSION['usuario_id'];

        $sucesso = $this->caronaModel->deletar($id, $motorista_id);

        if ($sucesso) {
            header("Location: ../views/caronas/index.php?mensagem=excluida");
            exit;
        } else {
            echo "<script>alert('Erro ao excluir a carona. Verifique se existem reservas vinculadas.'); window.history.back();</script>";
        }
    }
        public function atualizar(int $id, array $dadosPost) {
        // Segurança básica
        if (!isset($_SESSION['usuario_id']) || $_SESSION['tipo_usuario'] !== 'motorista') {
            die("Acesso negado.");
        }

        // Validação CSRF
        if (!isset($dadosPost['csrf_token']) || $dadosPost['csrf_token'] !== $_SESSION['csrf_token']) {
            die("Erro de validação de formulário (CSRF).");
        }

        $vagas = (int) $dadosPost['vagas'];
        if ($vagas < 1) {
            echo "<script>alert('O veículo deve ter pelo menos 1 vaga!'); window.history.back();</script>";
            return;
        }

        $origem = htmlspecialchars(trim($dadosPost['origem']));
        $destino = htmlspecialchars(trim($dadosPost['destino']));
        $dataHora = trim($dadosPost['data_hora']);
        $motorista_id = $_SESSION['usuario_id'];

        $sucesso = $this->caronaModel->atualizar($id, $motorista_id, $origem, $destino, $dataHora, $vagas);

        if ($sucesso) {
            header("Location: ../views/caronas/index.php?mensagem=atualizada");
            exit;
        } else {
            echo "<script>alert('Erro ao atualizar a carona.'); window.history.back();</script>";
        }
    }
    }