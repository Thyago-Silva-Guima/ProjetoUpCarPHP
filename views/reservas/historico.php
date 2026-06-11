<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Historico de Parceiros - UPCAR</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-900 text-gray-100 font-sans min-h-screen">

    <header class="bg-[#2196f3] p-4 shadow-lg">
        <h1 class="text-2xl font-bold text-white text-center">UPCAR - Historico de Parceiros</h1>
    </header>

    <main class="container mx-auto p-6">

        <section class="flex justify-between items-center mb-8 border-b border-[#81c9fa] pb-4">
            <h2 class="text-3xl font-semibold text-[#81c9fa]">Quem ja viajou comigo</h2>
            <?php if ($_SESSION['tipo_usuario'] === 'passageiro'): ?>
                <a href="index.php?action=minhasReservas"
                   class="bg-[#2196f3] hover:bg-[#1976d2] text-white py-2 px-4 rounded-lg font-bold transition-colors">
                    Minhas Reservas
                </a>
            <?php else: ?>
                <a href="index.php?action=gerenciarReservas"
                   class="bg-[#2196f3] hover:bg-[#1976d2] text-white py-2 px-4 rounded-lg font-bold transition-colors">
                    Gerenciar Reservas
                </a>
            <?php endif; ?>
        </section>

        <?php if (empty($parceiros)): ?>
            <article class="bg-gray-800 p-8 rounded-xl border border-gray-700 text-center">
                <p class="text-gray-400 text-lg">Nenhuma viagem compartilhada ainda.</p>
                <p class="text-gray-600 text-sm mt-2">Quando uma reserva for aceita, o parceiro aparecera aqui.</p>
            </article>
        <?php else: ?>

            <p class="text-gray-500 text-sm mb-6"><?= count($parceiros) ?> parceiro(s) encontrado(s)</p>

            <section class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <?php foreach ($parceiros as $parceiro): ?>
                    <?php
                        $dataHoraFormatada = date('d/m/Y H:i', strtotime($parceiro['data_hora']));

                        if ($parceiro['tipo_usuario'] === 'motorista') {
                            $classeBordaCard = 'border-[#2196f3]';
                            $rotuloDoTipo    = 'Motorista';
                            $classeCorRotulo = 'text-[#2196f3]';
                        } else {
                            $classeBordaCard = 'border-[#81c9fa]';
                            $rotuloDoTipo    = 'Passageiro';
                            $classeCorRotulo = 'text-[#81c9fa]';
                        }

                        $partesDoNome   = explode(' ', trim($parceiro['nome']));
                        $iniciaisDoNome = '';
                        foreach ($partesDoNome as $parteDoNome) {
                            $iniciaisDoNome .= strtoupper(mb_substr($parteDoNome, 0, 1));
                            if (strlen($iniciaisDoNome) >= 2) break;
                        }
                    ?>

                    <article class="bg-gray-800 p-5 rounded-xl border-l-4 <?= $classeBordaCard ?> shadow-md">

                        <div class="flex items-center gap-4 mb-3">
                            <div class="w-12 h-12 rounded-full bg-gray-700 border border-gray-600 flex items-center justify-center text-white font-bold text-sm flex-shrink-0">
                                <?= htmlspecialchars($iniciaisDoNome) ?>
                            </div>
                            <div>
                                <span class="<?= $classeCorRotulo ?> text-xs font-semibold block">
                                    <?= $rotuloDoTipo ?>
                                </span>
                                <p class="text-white font-bold">
                                    <?= htmlspecialchars($parceiro['nome']) ?>
                                </p>
                            </div>
                        </div>

                        <p class="text-gray-400 text-sm mb-1">
                            <?= htmlspecialchars($parceiro['email']) ?>
                        </p>

                        <p class="text-sm text-gray-400 mb-1">
                            <strong class="text-[#81c9fa]">Rota:</strong>
                            <?= htmlspecialchars($parceiro['origem']) ?> ➔ <?= htmlspecialchars($parceiro['destino']) ?>
                        </p>

                        <p class="text-sm text-gray-400">
                            <strong class="text-[#81c9fa]">Data:</strong> <?= $dataHoraFormatada ?>
                        </p>

                    </article>
                <?php endforeach; ?>
            </section>

        <?php endif; ?>

    </main>

</body>
</html>