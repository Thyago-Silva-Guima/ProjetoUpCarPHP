<?php
session_start();
require_once '../../models/Carona.php';
require_once '../../Config/Banco.php';

if (!isset($_GET['id'])) {
    header('Location: index.php');
    exit;
}

$id_carona = (int) $_GET['id'];

$caronaModel = new Carona(Banco::getConexao());

$caronaAtual  = $caronaModel->buscarPorId($id_carona, $_SESSION['usuario_id']);

if (!$caronaAtual) { header('Location: index.php'); exit; }

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Carona - UPCAR</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-900 text-gray-100 font-sans min-h-screen">

    <header class="bg-[#2196f3] p-4 shadow-lg mb-8">
        <h1 class="text-2xl font-bold text-white text-center">UPCAR - Editar Carona</h1>
    </header>

    <main class="container mx-auto p-4 max-w-2xl">
        <section class="bg-gray-800 p-8 rounded-xl shadow-md border-t-4 border-[#81c9fa]">
            <h2 class="text-2xl font-semibold mb-6 text-[#81c9fa]">Atualizar Rota</h2>

            <form action="../../controllers/atualizar_carona.php" method="POST" class="space-y-6">
                <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                <input type="hidden" name="id" value="<?= $caronaAtual['id'] ?>">

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="origem" class="block text-sm font-medium text-gray-300 mb-1">Ponto de Origem</label>
                        <input type="text" id="origem" name="origem" required value="<?= htmlspecialchars($caronaAtual['origem']) ?>"
                               class="w-full bg-gray-700 border border-gray-600 rounded-lg p-3 text-white focus:outline-none focus:border-[#2196f3] focus:ring-1 focus:ring-[#2196f3]">
                    </div>

                    <div>
                        <label for="destino" class="block text-sm font-medium text-gray-300 mb-1">Ponto de Destino</label>
                        <input type="text" id="destino" name="destino" required value="<?= htmlspecialchars($caronaAtual['destino']) ?>"
                               class="w-full bg-gray-700 border border-gray-600 rounded-lg p-3 text-white focus:outline-none focus:border-[#2196f3] focus:ring-1 focus:ring-[#2196f3]">
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="data_hora" class="block text-sm font-medium text-gray-300 mb-1">Data e Horário</label>
                        <input type="datetime-local" id="data_hora" name="data_hora" required value="<?= date('Y-m-d\TH:i', strtotime($caronaAtual['data_hora'])) ?>"
                               class="w-full bg-gray-700 border border-gray-600 rounded-lg p-3 text-white focus:outline-none focus:border-[#2196f3] focus:ring-1 focus:ring-[#2196f3]">
                    </div>

                    <div>
                        <label for="vagas" class="block text-sm font-medium text-gray-300 mb-1">Assentos Disponíveis</label>
                        <input type="number" id="vagas" name="vagas" min="1" required value="<?= (int)$caronaAtual['vagas_totais'] ?>"
                               class="w-full bg-gray-700 border border-gray-600 rounded-lg p-3 text-white focus:outline-none focus:border-[#2196f3] focus:ring-1 focus:ring-[#2196f3]">
                    </div>
                </div>

                <div class="flex justify-end space-x-4 pt-4 border-t border-gray-700">
                    <a href="index.php" class="px-6 py-2 rounded-lg font-medium text-gray-300 bg-gray-700 hover:bg-gray-600 transition-colors">
                        Cancelar
                    </a>
                    <button type="submit" class="px-6 py-2 rounded-lg font-bold text-white bg-[#2196f3] hover:bg-[#1976d2] transition-colors shadow-lg shadow-blue-500/30">
                        Salvar Alterações
                    </button>
                </div>
            </form>
        </section>
    </main>

</body>
</html>