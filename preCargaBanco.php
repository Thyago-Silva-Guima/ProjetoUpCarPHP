<?php


require_once __DIR__ . '/Config/Banco.php';

try {
    $db = Banco::getConexao();

   
    $db->exec("SET FOREIGN_KEY_CHECKS = 0;");
    $db->exec("TRUNCATE TABLE reservas;");
    $db->exec("TRUNCATE TABLE caronas;");
    $db->exec("TRUNCATE TABLE relatorios;");
    $db->exec("TRUNCATE TABLE usuarios;");
    $db->exec("SET FOREIGN_KEY_CHECKS = 1;");

    $senhaHash = password_hash('123456', PASSWORD_BCRYPT);
    
    $queryUsuario = "INSERT INTO usuarios (id, nome, email, senha, cpf, data_nascimento, tipo_usuario) VALUES (?, ?, ?, ?, ?, ?, ?)";
    $stmtUsuario = $db->prepare($queryUsuario);

   
    $stmtUsuario->execute([1, 'Motorista Um ', 'motorista1@upcar.com', $senhaHash, '11111111111', '1995-01-01', 'motorista']);
    $stmtUsuario->execute([2, 'Motorista Dois ', 'motorista2@upcar.com', $senhaHash, '22222222222', '1996-02-02', 'motorista']);
    $stmtUsuario->execute([3, 'Motorista Três ', 'motorista3@upcar.com', $senhaHash, '33333333333', '1997-03-03', 'motorista']);

  
    $stmtUsuario->execute([4, 'Passageiro Um', 'passageiro1@upcar.com', $senhaHash, '44444444444', '2000-04-04', 'passageiro']);
    $stmtUsuario->execute([5, 'Passageiro Dois', 'passageiro2@upcar.com', $senhaHash, '55555555555', '2001-05-05', 'passageiro']);
    $stmtUsuario->execute([6, 'Passageiro Três', 'passageiro3@upcar.com', $senhaHash, '66666666666', '2002-06-06', 'passageiro']);
    $stmtUsuario->execute([7, 'Passageiro Quatro', 'passageiro4@upcar.com', $senhaHash, '77777777777', '2003-07-07', 'passageiro']);

    $queryCarona = "INSERT INTO caronas (id, motorista_id, origem, destino, data_hora, vagas_totais, vagas_disponiveis) VALUES (?, ?, ?, ?, ?, ?, ?)";
    $stmtCarona = $db->prepare($queryCarona);
    
    $dataFutura = date('Y-m-d H:i:s', strtotime('+2 days'));

    $stmtCarona->execute([1, 1, 'Campus Ecoville', 'Centro', $dataFutura, 3, 3]);

    $stmtCarona->execute([2, 2, 'Centro', 'Campus Ecoville', $dataFutura, 4, 1]);

    $stmtCarona->execute([3, 3, 'Batel', 'Campus Ecoville', $dataFutura, 1, 0]);

    $queryReserva = "INSERT INTO reservas (carona_id, passageiro_id, status) VALUES (?, ?, ?)";
    $stmtReserva = $db->prepare($queryReserva);


    $stmtReserva->execute([2, 4, 'aceito']); 
    $stmtReserva->execute([2, 5, 'aceito']); 
    $stmtReserva->execute([2, 6, 'aceito']); 

    $stmtReserva->execute([3, 7, 'aceito']); 

    echo "<h1>banco de dados pré preenchido com sucesso </h1>";
    echo "<p>Cenários criados e prontos para apresentação.</p>";
    echo "<a href='index.php'>Ir para o Login</a>";

} catch (PDOException $e) {
    echo "<h1>Erro ao popular banco:</h1> " . $e->getMessage();
}
?>