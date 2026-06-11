<?php
// models/Usuario.php — Toda lógica de banco de dados relacionada ao usuário

require_once __DIR__ . '/../Config/Banco.php'; 

class Usuario {
    private $db;

    public function __construct() {
        $this->db = Banco::getConexao(); // pega a conexão única
    }

    // Salva usuário novo — senha sempre embaralhada com password_hash
    public function registrar($dados) {
        $stmt = $this->db->prepare(
            "INSERT INTO usuarios (nome, email, senha, cpf, data_nascimento, tipo_usuario)
             VALUES (:nome, :email, :senha, :cpf, :data_nascimento, :tipo_usuario)"
        );
        return $stmt->execute([
            ':nome'            => $dados['nome'],
            ':email'           => $dados['email'],
            ':senha'           => password_hash($dados['senha'], PASSWORD_BCRYPT),
            ':cpf'             => $dados['cpf'],
            ':data_nascimento' => $dados['data_nascimento'],
            ':tipo_usuario'    => $dados['tipo_usuario'],
        ]);
    }

    // Busca pelo e-mail — usado no login
    public function buscarPorEmail($email) {
        $stmt = $this->db->prepare("SELECT * FROM usuarios WHERE email = :email LIMIT 1");
        $stmt->execute([':email' => $email]);
        return $stmt->fetch();
    }

    // Busca por CPF + data de nascimento — usado na recuperação de senha
    public function buscarPorCpfEData($cpf, $data) {
        $stmt = $this->db->prepare(
            "SELECT * FROM usuarios WHERE cpf = :cpf AND data_nascimento = :data LIMIT 1"
        );
        $stmt->execute([':cpf' => $cpf, ':data' => $data]);
        return $stmt->fetch();
    }

    // Verifica se e-mail já está cadastrado
    public function emailExiste($email) {
        $stmt = $this->db->prepare("SELECT id FROM usuarios WHERE email = :email LIMIT 1");
        $stmt->execute([':email' => $email]);
        return (bool) $stmt->fetch();
    }

    // Atualiza a senha (usada na recuperação)
    public function atualizarSenha($id, $novaSenha) {
        $stmt = $this->db->prepare("UPDATE usuarios SET senha = :senha WHERE id = :id");
        return $stmt->execute([
            ':senha' => password_hash($novaSenha, PASSWORD_BCRYPT),
            ':id'    => $id,
        ]);
    }
}
