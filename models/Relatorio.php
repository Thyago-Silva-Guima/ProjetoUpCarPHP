<?php

class Relatorio {
    private PDO $conexao;

    public function __construct(PDO $db) {
        $this->conexao = $db;
    }

    public function buscarCaronas(string $origem = '', string $destino = ''): array {
        $sql = "SELECT c.id,
                       c.origem,
                       c.destino,
                       c.data_hora,
                       c.vagas_totais,
                       c.vagas_disponiveis,
                       u.id   AS motorista_id,
                       u.nome AS motorista_nome,
                       u.tipo_usuario
                FROM caronas c
                INNER JOIN usuarios u ON u.id = c.motorista_id
                WHERE c.vagas_disponiveis > 0
                  AND c.data_hora > NOW()";

        $params = [];

        if (!empty($origem)) {
            $sql .= " AND c.origem LIKE :origem";
            $params[':origem'] = '%' . $origem . '%';
        }

        if (!empty($destino)) {
            $sql .= " AND c.destino LIKE :destino";
            $params[':destino'] = '%' . $destino . '%';
        }

        $sql .= " ORDER BY c.data_hora ASC";

        $stmt = $this->conexao->prepare($sql);

        foreach ($params as $chave => $valor) {
            $stmt->bindValue($chave, $valor, PDO::PARAM_STR);
        }

        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function listarUsuarios(int $usuarioLogadoId): array {
        $sql = "SELECT id, nome, email, tipo_usuario, criado_em
                FROM usuarios
                WHERE id != :id
                ORDER BY tipo_usuario ASC, nome ASC";

        $stmt = $this->conexao->prepare($sql);
        $stmt->bindValue(':id', $usuarioLogadoId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function criarRelatorio(int $denunciante_id, int $denunciado_id, string $motivo): bool {
        $sql = "INSERT INTO relatorios (denunciante_id, denunciado_id, motivo)
                VALUES (:denunciante_id, :denunciado_id, :motivo)";

        $stmt = $this->conexao->prepare($sql);
        $stmt->bindValue(':denunciante_id', $denunciante_id, PDO::PARAM_INT);
        $stmt->bindValue(':denunciado_id',  $denunciado_id,  PDO::PARAM_INT);
        $stmt->bindValue(':motivo',         $motivo,         PDO::PARAM_STR);

        return $stmt->execute();
    }

    public function jaReportou(int $denunciante_id, int $denunciado_id): bool {
        $sql = "SELECT COUNT(*) FROM relatorios
                WHERE denunciante_id = :denunciante_id
                  AND denunciado_id  = :denunciado_id";

        $stmt = $this->conexao->prepare($sql);
        $stmt->bindValue(':denunciante_id', $denunciante_id, PDO::PARAM_INT);
        $stmt->bindValue(':denunciado_id',  $denunciado_id,  PDO::PARAM_INT);
        $stmt->execute();

        return (int) $stmt->fetchColumn() > 0;
    }

    public function buscarUsuarioPorId(int $id): array|false {
        $sql = "SELECT id, nome, email, tipo_usuario FROM usuarios WHERE id = :id LIMIT 1";
        $stmt = $this->conexao->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}