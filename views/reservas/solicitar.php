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
    <title>Minhas Reservas - UPCAR</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-900 text-gray-100 font-sans min-h-screen">

    <header class="bg-[#2196f3] p-4 shadow-lg">
        <div class="container mx-auto relative flex items-center justify-center">
            <a href="controllers/buscar_caronas.php" class="absolute left-0 text-white hover:text-blue-200 text-sm font-semibold transition-colors">
                ← Voltar ao Feed
            </a>
            <h1 class="text-2xl font-bold text-white text-center">UPCAR - Minhas Reservas</h1>
        </div>
    </header>

    <main class="container mx-auto p-6">

        <?php if (!empty($_SESSION['flash'])): ?>
            <?php
                $mensagemFlash     = $_SESSION['flash'];
                unset($_SESSION['flash']);
                $classeAlertaFlash = $mensagemFlash['tipo'] === 'sucesso'
                    ? 'bg-blue-900 border border-[#2196f3] text-[#81c9fa]'
                    : 'bg-red-900 border border-red-500 text-red-300';
            ?>
            <div class="<?= $classeAlertaFlash ?> rounded-lg px-5 py-3 mb-6 text-sm">
                <?= htmlspecialchars($mensagemFlash['msg']) ?>
            </div>
        <?php endif; ?>

        <section class="flex justify-between items-center mb-8 border-b border-[#81c9fa] pb-4">
            <h2 class="text-3xl font-semibold text-[#81c9fa]">Vagas Solicitadas</h2>
            <div class="flex gap-3">
                <a href="index.php?action=historicoParceiros"
                   class="bg-gray-700 hover:bg-gray-600 text-white py-2 px-4 rounded-lg font-bold transition-colors">
                    Historico de Parceiros
                </a>
            </div>
        </section>

        <?php if (empty($reservas)): ?>
            <article class="bg-gray-800 p-8 rounded-xl border border-gray-700 text-center">
                <p class="text-gray-400 text-lg mb-4">Voce ainda nao solicitou nenhuma vaga.</p>
            </article>
        <?php else: ?>

            <section class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <?php foreach ($reservas as $numeroOrdem => $reserva): ?>
                    <?php
                        $dataHoraFormatada = date('d/m/Y H:i', strtotime($reserva['data_hora']));

                        switch ($reserva['status']) {
                            case 'aceito':
                                $classeBordaCard = 'border-[#2196f3]';
                                $classeTextoBadge = 'text-[#81c9fa] bg-blue-900';
                                $rotuloDoStatus   = 'Aceito';
                                break;
                            case 'recusado':
                                $classeBordaCard  = 'border-red-600';
                                $classeTextoBadge = 'text-red-300 bg-red-900';
                                $rotuloDoStatus   = 'Recusado';
                                break;
                            default:
                                $classeBordaCard  = 'border-[#81c9fa]';
                                $classeTextoBadge = 'text-gray-300 bg-gray-700';
                                $rotuloDoStatus   = 'Aguardando';
                        }
                    ?>

                    <article class="bg-gray-800 p-5 rounded-xl border-l-4 <?= $classeBordaCard ?> shadow-md">

                        <div class="flex items-center justify-between mb-3">
                            <span class="text-xs font-semibold px-3 py-1 rounded-full <?= $classeTextoBadge ?>">
                                <?= $rotuloDoStatus ?>
                            </span>
                            <span class="text-gray-600 text-xs">#<?= $numeroOrdem + 1 ?></span>
                        </div>

                        <h3 class="text-xl font-bold text-white mb-2">
                            <?= htmlspecialchars($reserva['origem']) ?> ➔ <?= htmlspecialchars($reserva['destino']) ?>
                        </h3>

                        <p class="text-sm text-gray-400 mb-1">
                            <strong class="text-[#81c9fa]">Data/Hora:</strong> <?= $dataHoraFormatada ?>
                        </p>

                        <p class="text-sm text-gray-400 mb-4">
                            <strong class="text-[#81c9fa]">Motorista:</strong> <?= htmlspecialchars($reserva['motorista_nome']) ?>
                        </p>

                        <?php if ($reserva['status'] === 'solicitado'): ?>
                            <form method="POST" action="index.php?action=cancelarReserva"
                                  onsubmit="return confirm('Tem certeza que deseja cancelar esta reserva?');">
                                <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                                <input type="hidden" name="reserva_id" value="<?= (int) $reserva['reserva_id'] ?>">
                                <button type="submit"
                                    class="bg-red-600 hover:bg-red-500 text-white text-sm font-bold px-3 py-1 rounded transition-colors">
                                    Cancelar Reserva
                                </button>
                            </form>
                        <?php endif; ?>

                    </article>
                <?php endforeach; ?>
            </section>

        <?php endif; ?>

    </main>

</body>
</html>