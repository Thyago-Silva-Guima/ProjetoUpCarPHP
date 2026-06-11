<?php
class Carona {
    private PDO $conexao;

    public function __construct(PDO $db){
        $this->conexao =$db;
    }

 public function criar(int $motorista_id, string $origem, string $destino, string $data_hora, int $vagas): bool {
        $query = "INSERT INTO caronas (motorista_id, origem, destino, data_hora, vagas_totais, vagas_disponiveis)
                  VALUES (:motorista_id, :origem, :destino, :data_hora, :vagas_totais, :vagas_disponiveis)";
        
        $stmt = $this->conexao->prepare($query);
        $stmt->bindValue(':motorista_id', $motorista_id, PDO::PARAM_INT);
        $stmt->bindValue(':origem', $origem, PDO::PARAM_STR);
        $stmt->bindValue(':destino', $destino, PDO::PARAM_STR);
        $stmt->bindValue(':data_hora', $data_hora, PDO::PARAM_STR);
        
        $stmt->bindValue(':vagas_totais', $vagas, PDO::PARAM_INT);
        $stmt->bindValue(':vagas_disponiveis', $vagas, PDO::PARAM_INT);

        return $stmt->execute();
    }

    public function listarPorMotorista(int $motorista_id): array{
        $query = "SELECT * FROM caronas WHERE motorista_id = :motorista_id ORDER BY data_hora ASC";
        $stmt = $this->conexao->prepare($query);
        $stmt->bindValue(':motorista_id',$motorista_id,PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function deletar(int $id,int $motorista_id): bool{
        $query = "DELETE FROM caronas WHERE id = :id AND motorista_id = :motorista_id";
        $stmt=$this->conexao->prepare($query);
        $stmt->bindValue(':id',$id,PDO::PARAM_INT);
        $stmt->bindValue(':motorista_id',$motorista_id,PDO::PARAM_INT);

        return $stmt->execute();
    }

    public function buscarPorId(int $id, int $motorista_id) {
        $query = "SELECT * FROM caronas WHERE id = :id AND motorista_id = :motorista_id LIMIT 1";
        $stmt = $this->conexao->prepare($query);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->bindValue(':motorista_id', $motorista_id, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC); 
    }
public function atualizar(int $id, int $motorista_id, string $origem, string $destino, string $data_hora, int $vagas): bool {
        
        $query = "UPDATE caronas SET origem = :origem, destino = :destino, data_hora = :data_hora, vagas_disponiveis = vagas_disponiveis + (:vagas_calc - vagas_totais), vagas_totais = :vagas_nova
                  WHERE id = :id AND motorista_id = :motorista_id";
                  
        $stmt = $this->conexao->prepare($query);
        $stmt->bindValue(':origem', $origem, PDO::PARAM_STR);
        $stmt->bindValue(':destino', $destino, PDO::PARAM_STR);
        $stmt->bindValue(':data_hora', $data_hora, PDO::PARAM_STR);
        
        $stmt->bindValue(':vagas_calc', $vagas, PDO::PARAM_INT);
        $stmt->bindValue(':vagas_nova', $vagas, PDO::PARAM_INT);
        
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->bindValue(':motorista_id', $motorista_id, PDO::PARAM_INT);
        
        return $stmt->execute();
    }
}