<?php

class Reserva {
    private PDO $conexao;

    public function __construct(PDO $conexao) {
        $this->conexao = $conexao;
    }

    public function solicitarVaga(int $caronaId, int $passageiroId): bool {
        if ($this->passageiroJaPossuiReservaNaCarona($caronaId, $passageiroId)) {
            return false;
        }

        if (!$this->caronaPossuiVagasDisponiveis($caronaId)) {
            return false;
        }

        $query = "INSERT INTO reservas (carona_id, passageiro_id, status)
                  VALUES (:carona_id, :passageiro_id, 'solicitado')";

        $stmt = $this->conexao->prepare($query);
        $stmt->bindValue(':carona_id',     $caronaId,     PDO::PARAM_INT);
        $stmt->bindValue(':passageiro_id', $passageiroId, PDO::PARAM_INT);

        return $stmt->execute();
    }

    public function alterarStatusDaReserva(int $reservaId, int $motoristaId, string $novoStatus): bool {
        $reservaAtual = $this->buscarReservaPorIdEMotorista($reservaId, $motoristaId);

        if (!$reservaAtual) {
            return false;
        }

        if ($novoStatus === 'aceito') {
            if ($reservaAtual['vagas_disponiveis'] < 1) {
                return false;
            }

            $this->conexao->beginTransaction();

            try {
                $queryAtualizaReserva = "UPDATE reservas SET status = 'aceito' WHERE id = :reserva_id";
                $stmtReserva = $this->conexao->prepare($queryAtualizaReserva);
                $stmtReserva->bindValue(':reserva_id', $reservaId, PDO::PARAM_INT);
                $stmtReserva->execute();

                $queryReduzVaga = "UPDATE caronas SET vagas_disponiveis = vagas_disponiveis - 1
                                   WHERE id = :carona_id AND vagas_disponiveis > 0";
                $stmtVaga = $this->conexao->prepare($queryReduzVaga);
                $stmtVaga->bindValue(':carona_id', $reservaAtual['carona_id'], PDO::PARAM_INT);
                $stmtVaga->execute();

                $this->conexao->commit();
                return true;
            } catch (Exception $excecao) {
                $this->conexao->rollBack();
                return false;
            }
        }

        if ($novoStatus === 'recusado') {
            $query = "UPDATE reservas SET status = 'recusado' WHERE id = :reserva_id";
            $stmt  = $this->conexao->prepare($query);
            $stmt->bindValue(':reserva_id', $reservaId, PDO::PARAM_INT);
            return $stmt->execute();
        }

        return false;
    }

    public function cancelarReservaDoPassageiro(int $reservaId, int $passageiroId): bool {
        $reservaAtual = $this->buscarReservaPorIdEPassageiro($reservaId, $passageiroId);

        if (!$reservaAtual) {
            return false;
        }

        $query = "DELETE FROM reservas WHERE id = :reserva_id AND passageiro_id = :passageiro_id";
        $stmt  = $this->conexao->prepare($query);
        $stmt->bindValue(':reserva_id',    $reservaId,    PDO::PARAM_INT);
        $stmt->bindValue(':passageiro_id', $passageiroId, PDO::PARAM_INT);
        $stmt->execute();

        if ($reservaAtual['status'] === 'aceito') {
            $this->devolverVagaDisponivel($reservaAtual['carona_id']);
        }

        return true;
    }

    public function listarSolicitacoesRecebidasPeloMotorista(int $motoristaId): array {
        $query = "SELECT r.id AS reserva_id, r.status,
                         c.origem, c.destino, c.data_hora, c.vagas_disponiveis,
                         u.nome  AS passageiro_nome,
                         u.email AS passageiro_email
                  FROM reservas r
                  JOIN caronas  c ON r.carona_id     = c.id
                  JOIN usuarios u ON r.passageiro_id = u.id
                  WHERE c.motorista_id = :motorista_id
                  ORDER BY c.data_hora ASC";

        $stmt = $this->conexao->prepare($query);
        $stmt->bindValue(':motorista_id', $motoristaId, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function listarReservasDoPassageiro(int $passageiroId): array {
        $query = "SELECT r.id AS reserva_id, r.status,
                         c.origem, c.destino, c.data_hora,
                         u.nome AS motorista_nome
                  FROM reservas r
                  JOIN caronas  c ON r.carona_id    = c.id
                  JOIN usuarios u ON c.motorista_id = u.id
                  WHERE r.passageiro_id = :passageiro_id
                  ORDER BY c.data_hora ASC";

        $stmt = $this->conexao->prepare($query);
        $stmt->bindValue(':passageiro_id', $passageiroId, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function listarParceirosDaViagemDoUsuario(int $usuarioId, string $tipoUsuario): array {
        if ($tipoUsuario === 'passageiro') {
            $query = "SELECT DISTINCT u.id, u.nome, u.email, u.tipo_usuario,
                             c.origem, c.destino, c.data_hora
                      FROM reservas r
                      JOIN caronas  c ON r.carona_id    = c.id
                      JOIN usuarios u ON c.motorista_id = u.id
                      WHERE r.passageiro_id = :usuario_id AND r.status = 'aceito'
                      ORDER BY c.data_hora DESC";
        } else {
            $query = "SELECT DISTINCT u.id, u.nome, u.email, u.tipo_usuario,
                             c.origem, c.destino, c.data_hora
                      FROM reservas r
                      JOIN caronas  c ON r.carona_id     = c.id
                      JOIN usuarios u ON r.passageiro_id = u.id
                      WHERE c.motorista_id = :usuario_id AND r.status = 'aceito'
                      ORDER BY c.data_hora DESC";
        }

        $stmt = $this->conexao->prepare($query);
        $stmt->bindValue(':usuario_id', $usuarioId, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    private function passageiroJaPossuiReservaNaCarona(int $caronaId, int $passageiroId): bool {
        $query = "SELECT id FROM reservas
                  WHERE carona_id = :carona_id AND passageiro_id = :passageiro_id
                    AND status != 'recusado'";

        $stmt = $this->conexao->prepare($query);
        $stmt->bindValue(':carona_id',     $caronaId,     PDO::PARAM_INT);
        $stmt->bindValue(':passageiro_id', $passageiroId, PDO::PARAM_INT);
        $stmt->execute();

        return (bool) $stmt->fetch();
    }

    private function caronaPossuiVagasDisponiveis(int $caronaId): bool {
        $query = "SELECT vagas_disponiveis FROM caronas WHERE id = :carona_id";

        $stmt = $this->conexao->prepare($query);
        $stmt->bindValue(':carona_id', $caronaId, PDO::PARAM_INT);
        $stmt->execute();

        $carona = $stmt->fetch(PDO::FETCH_ASSOC);

        return $carona && $carona['vagas_disponiveis'] > 0;
    }

    private function buscarReservaPorIdEMotorista(int $reservaId, int $motoristaId): array|false {
        $query = "SELECT r.id, r.carona_id, r.status,
                         c.vagas_disponiveis
                  FROM reservas r
                  JOIN caronas c ON r.carona_id = c.id
                  WHERE r.id = :reserva_id AND c.motorista_id = :motorista_id";

        $stmt = $this->conexao->prepare($query);
        $stmt->bindValue(':reserva_id',  $reservaId,  PDO::PARAM_INT);
        $stmt->bindValue(':motorista_id', $motoristaId, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    private function buscarReservaPorIdEPassageiro(int $reservaId, int $passageiroId): array|false {
        $query = "SELECT carona_id, status FROM reservas
                  WHERE id = :reserva_id AND passageiro_id = :passageiro_id";

        $stmt = $this->conexao->prepare($query);
        $stmt->bindValue(':reserva_id',    $reservaId,    PDO::PARAM_INT);
        $stmt->bindValue(':passageiro_id', $passageiroId, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    private function devolverVagaDisponivel(int $caronaId): void {
        $query = "UPDATE caronas
                  SET vagas_disponiveis = vagas_disponiveis + 1
                  WHERE id = :carona_id AND vagas_disponiveis < vagas_totais";

        $stmt = $this->conexao->prepare($query);
        $stmt->bindValue(':carona_id', $caronaId, PDO::PARAM_INT);
        $stmt->execute();
    }
}