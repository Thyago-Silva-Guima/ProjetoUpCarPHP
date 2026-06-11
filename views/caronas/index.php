<?php
session_start();

if (empty($_SESSION['usuario_id'])) {
    header('Location: ../../index.php?action=login');
    exit;
}

if ($_SESSION['tipo_usuario'] !== 'motorista') {
    header('Location: ../../index.php?action=dashboard');
    exit;
}
require_once '../../Config/Banco.php';
require_once '../../models/Carona.php';

$db = Banco::getConexao();
$caronaModel = new Carona($db);
$minhasCaronas = $caronaModel->listarPorMotorista($_SESSION['usuario_id']);

?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Minhas Caronas - UPCAR</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-900 text-gray-100 font-sans min-h-screen">
    <header class="bg-gray-900 border-b border-gray-800 p-4 shadow-lg">
        <div class="container mx-auto flex justify-between items-center">
                <h1 class="text-2xl font-bold text-[#2196f3]">UPCAR</h1>
            
            <nav class="flex items-center gap-4 text-sm font-semibold">
                <a href="../../index.php?action=gerenciar" class="text-gray-400 hover:text-white transition-colors">
                    Painel Motorista
                </a>
                    
                <span class="text-gray-600">|</span>
                
                <span class="text-blue-100">
                    Olá, <?= htmlspecialchars($_SESSION['usuario_nome'] ?? 'Motorista') ?>
                </span>
                
                <a href="../../index.php?action=logout" class="bg-red-900/30 hover:bg-red-800 text-red-400 py-1 px-3 rounded-lg transition-colors">
                    Sair
                </a>
            </nav>
        </div>
    </header>

    <main class="container mx-auto p-6">
        <section class="flex justify-between items-center mb-8 border-b border-[#81c9fa] pb-4">
            <h2 class="text-3xl font-semibold text-[#81c9fa]">Minhas Rotas Oferecidas</h2>
            <a href="criar.php" class="bg-[#2196f3] hover:bg-[#1976d2] text-white py-2 px-4 rounded-lg font-bold transition-colors">
                + Nova Carona
            </a>
        </section>

        <section class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <?php 
            
            if (!empty($minhasCaronas)): 
                foreach ($minhasCaronas as $carona): 
            ?>
                <article class="bg-gray-800 p-5 rounded-xl border-l-4 border-[#81c9fa] shadow-md">
                    <h3 class="text-xl font-bold text-white mb-2">
                        <?= htmlspecialchars($carona['origem']) ?> ➔ <?= htmlspecialchars($carona['destino']) ?>
                    </h3>
                    <p class="text-sm text-gray-400 mb-1">
                        <strong class="text-[#81c9fa]">Data/Hora:</strong> <?= date('d/m/Y H:i', strtotime($carona['data_hora'])) ?>
                    </p>
                    <p class="text-sm text-gray-400 mb-4">
                        <strong class="text-[#81c9fa]">Vagas Livres:</strong> <?= (int)$carona['vagas_disponiveis'] ?>
                    </p>
                    

                      <div class="flex space-x-3">
                            <a href="editar.php?id=<?= $carona['id'] ?>" class="bg-yellow-600 hover:bg-yellow-500 text-white px-3 py-1 rounded text-sm">Editar</a>
                            
                            <form method="POST" action="../../controllers/deletar_carona.php" onsubmit="return confirm('Tem certeza que deseja cancelar esta carona?');">
                            <input type="hidden" name="id" value="<?= $carona['id'] ?>">
                            <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                            <button type="submit" class="bg-red-600 hover:bg-red-500 text-white px-3 py-1 rounded text-sm transition-colors">Excluir</button>
                        </form>
                    </div>
                </article>
            <?php 
                endforeach; 
            else: 
            ?>
                <p class="text-gray-400">Você ainda não ofereceu nenhuma carona. Que tal cadastrar a primeira?</p>
            <?php endif; ?>
        </section>
    </main>

</body>
</html>