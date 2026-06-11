<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }

if (!isset($_SESSION['usuario_id'])) {
    header("Location: ../auth/login.php");
    exit;
}

$denunciado_id = isset($_GET['denunciado_id']) ? (int)$_GET['denunciado_id'] : 0;

if ($denunciado_id <= 0) {
    header("Location: ../../controllers/buscar_caronas.php");
    exit;
}

if ($denunciado_id === (int)$_SESSION['usuario_id']) {
    echo "<script>alert('Você não pode reportar a si mesmo.'); window.history.back();</script>";
    exit;
}

require_once __DIR__ . '/../../Config/Banco.php';
require_once __DIR__ . '/../../models/Relatorio.php';

$db = Banco::getConexao();
$relatorioModel = new Relatorio($db);
$denunciado = $relatorioModel->buscarUsuarioPorId($denunciado_id);

if (!$denunciado) {
    header("Location: ../../controllers/buscar_caronas.php");
    exit;
}

$motivosPredefinidos = [
    'comportamento_inapropriado' => 'Comportamento inapropriado durante a carona',
    'perfil_falso'               => 'Perfil falso ou informações falsas',
    'no_show'                    => 'Não compareceu sem aviso prévio',
    'assedio'                    => 'Assédio ou ameaça',
    'outros'                     => 'Outros (descrever abaixo)',
];
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reportar Usuário - UPCAR</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-900 text-gray-100 font-sans min-h-screen">

    <header class="bg-[#2196f3] p-4 shadow-lg">
        <div class="container mx-auto flex justify-between items-center">
            <h1 class="text-2xl font-bold text-white">UPCAR</h1>
            <a href="javascript:history.back()" class="text-white hover:text-blue-200 text-sm font-semibold transition-colors">
                ← Voltar
            </a>
        </div>
    </header>

    <main class="container mx-auto p-6 max-w-2xl">
        <section class="bg-gray-800 rounded-xl p-8 border border-gray-700 shadow-lg">

            <div class="mb-6 border-b border-gray-700 pb-4">
                <h2 class="text-2xl font-semibold text-red-400 mb-1">⚑ Reportar Usuário</h2>
                <p class="text-gray-400 text-sm">
                    Você está reportando:
                    <strong class="text-white"><?= htmlspecialchars($denunciado['nome']) ?></strong>
                    <span class="text-[#81c9fa] text-xs ml-1">(<?= htmlspecialchars($denunciado['tipo_usuario']) ?>)</span>
                </p>
            </div>

            <form method="POST" action="../../controllers/salvar_relatorios.php">
                <input type="hidden" name="csrf_token"    value="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '') ?>">
                <input type="hidden" name="denunciado_id" value="<?= $denunciado_id ?>">

                <div class="mb-5">
                    <label for="tipo_motivo" class="block text-sm font-semibold text-[#81c9fa] mb-2">
                        Motivo da denúncia <span class="text-red-400">*</span>
                    </label>
                    <select id="tipo_motivo" name="tipo_motivo" onchange="atualizarMotivo(this.value)"
                        class="w-full bg-gray-700 border border-gray-600 rounded-lg px-4 py-2 text-white focus:outline-none focus:border-[#2196f3]">
                        <option value="">— Selecione um motivo —</option>
                        <?php foreach ($motivosPredefinidos as $valor => $label): ?>
                            <option value="<?= htmlspecialchars($valor) ?>"><?= htmlspecialchars($label) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="mb-6">
                    <label for="motivo" class="block text-sm font-semibold text-[#81c9fa] mb-2">
                        Descrição detalhada <span class="text-red-400">*</span>
                    </label>
                    <textarea id="motivo" name="motivo" rows="5" minlength="10" required
                        placeholder="Descreva o ocorrido com detalhes (mínimo 10 caracteres)..."
                        class="w-full bg-gray-700 border border-gray-600 rounded-lg px-4 py-3 text-white placeholder-gray-500 focus:outline-none focus:border-[#2196f3] resize-y"></textarea>
                    <p class="text-xs text-gray-500 mt-1">Sua denúncia é registrada e analisada pelos administradores.</p>
                </div>

                <div class="bg-gray-700 border border-yellow-600 rounded-lg p-3 mb-6 text-sm text-yellow-300">
                     Denúncias falsas ou de má-fé podem resultar em suspensão da sua conta.
                </div>

                <div class="flex gap-4">
                    <button type="submit"
                        class="flex-1 bg-red-700 hover:bg-red-600 text-white font-bold py-3 px-6 rounded-lg transition-colors">
                        Enviar Denúncia
                    </button>
                    <a href="javascript:history.back()"
                        class="flex-1 text-center bg-gray-600 hover:bg-gray-500 text-white font-bold py-3 px-6 rounded-lg transition-colors">
                        Cancelar
                    </a>
                </div>
            </form>
        </section>
    </main>

    <script>
        const motivos = <?= json_encode($motivosPredefinidos) ?>;
        function atualizarMotivo(valor) {
            const textarea = document.getElementById('motivo');
            if (valor && valor !== 'outros') {
                textarea.value = motivos[valor] ?? '';
            } else {
                textarea.value = '';
                textarea.focus();
            }
        }
    </script>
</body>
</html>