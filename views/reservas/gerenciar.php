<?php
$reservas = $reservas ?? [];
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gerenciar Reservas - UPCAR</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-900 text-gray-100 font-sans min-h-screen">

    <header class="bg-[#2196f3] p-4 shadow-lg mb-6">
        <div class="container mx-auto relative flex items-center justify-center">
            <a href="views/caronas/index.php" class="absolute left-0 text-white hover:text-blue-200 text-sm font-semibold transition-colors">
                ← Voltar para Minhas Rotas
            </a>
            <h1 class="text-2xl font-bold text-white text-center">UPCAR - Painel do Motorista</h1>
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
            <h2 class="text-3xl font-semibold text-[#81c9fa]">Solicitacoes de Reserva</h2>
            <div class="flex gap-3">
                <a href="index.php?action=historicoParceiros"
                   class="bg-gray-700 hover:bg-gray-600 text-white py-2 px-4 rounded-lg font-bold transition-colors">
                    Historico de Parceiros
                </a>
                <a href="views/caronas/index.php"
                   class="bg-[#2196f3] hover:bg-[#1976d2] text-white py-2 px-4 rounded-lg font-bold transition-colors">
                    Minhas Caronas
                </a>
            </div>
        </section>

        <?php
            $totalSolicitadas = 0;
            $totalAceitas     = 0;
            $totalRecusadas   = 0;

            foreach ($reservas as $reserva) {
                if ($reserva['status'] === 'solicitado') $totalSolicitadas++;
                if ($reserva['status'] === 'aceito')     $totalAceitas++;
                if ($reserva['status'] === 'recusado')   $totalRecusadas++;
            }
        ?>

        <section class="grid grid-cols-3 gap-4 mb-8">
            <article class="bg-gray-800 p-4 rounded-xl border border-gray-700 text-center">
                <p class="text-3xl font-bold text-gray-300"><?= $totalSolicitadas ?></p>
                <p class="text-xs text-gray-500 mt-1">Aguardando</p>
            </article>
            <article class="bg-gray-800 p-4 rounded-xl border border-[#2196f3] text-center">
                <p class="text-3xl font-bold text-[#81c9fa]"><?= $totalAceitas ?></p>
                <p class="text-xs text-[#81c9fa] mt-1">Aceitas</p>
            </article>
            <article class="bg-gray-800 p-4 rounded-xl border border-red-900 text-center">
                <p class="text-3xl font-bold text-red-400"><?= $totalRecusadas ?></p>
                <p class="text-xs text-red-500 mt-1">Recusadas</p>
            </article>
        </section>

        <?php if (empty($reservas)): ?>
            <article class="bg-gray-800 p-8 rounded-xl border border-gray-700 text-center">
                <p class="text-gray-400 text-lg">Nenhuma reserva recebida ainda.</p>
                <p class="text-gray-600 text-sm mt-2">Quando passageiros solicitarem vagas nas suas caronas, elas aparecerao aqui.</p>
            </article>
        <?php else: ?>

            <?php
                $reservasAgrupadasPorCarona = [];
                foreach ($reservas as $reserva) {
                    $chaveDeAgrupamento = $reserva['origem'] . ' → ' . $reserva['destino'] . ' | ' . date('d/m/Y H:i', strtotime($reserva['data_hora']));
                    $reservasAgrupadasPorCarona[$chaveDeAgrupamento][] = $reserva;
                }
            ?>

            <?php foreach ($reservasAgrupadasPorCarona as $tituloCarona => $reservasDaCarona): ?>

                <section class="mb-8">
                    <h2 class="text-[#2196f3] font-semibold text-base mb-3 border-b border-gray-700 pb-2">
                        <?= htmlspecialchars($tituloCarona) ?>
                        <span class="text-gray-600 text-xs ml-2">
                            — <?= count($reservasDaCarona) ?> reserva(s) | vagas disponiveis: <?= (int) $reservasDaCarona[0]['vagas_disponiveis'] ?>
                        </span>
                    </h2>

                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        <?php for ($posicao = 0; $posicao < count($reservasDaCarona); $posicao++): ?>
                            <?php
                                $reserva = $reservasDaCarona[$posicao];

                                switch ($reserva['status']) {
                                    case 'aceito':
                                        $classeBordaCard  = 'border-[#2196f3]';
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

                                <div class="flex items-center gap-2 mb-3">
                                    <span class="text-xs font-semibold px-3 py-1 rounded-full <?= $classeTextoBadge ?>">
                                        <?= $rotuloDoStatus ?>
                                    </span>
                                    <span class="text-gray-600 text-xs">Reserva #<?= (int) $reserva['reserva_id'] ?></span>
                                </div>

                                <p class="text-white font-bold mb-1">
                                    <?= htmlspecialchars($reserva['passageiro_nome']) ?>
                                </p>
                                <p class="text-gray-400 text-sm mb-4">
                                    <?= htmlspecialchars($reserva['passageiro_email']) ?>
                                </p>

                                <?php if ($reserva['status'] === 'solicitado'): ?>
                                    <div class="flex gap-2">
                                        <form method="POST" action="index.php?action=alterarStatus">
                                            <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                                            <input type="hidden" name="reserva_id" value="<?= (int) $reserva['reserva_id'] ?>">
                                            <input type="hidden" name="status" value="aceito">
                                            <button type="submit"
                                                class="bg-[#2196f3] hover:bg-[#1976d2] text-white text-sm font-bold px-3 py-1 rounded transition-colors">
                                                Aceitar
                                            </button>
                                        </form>

                                        <form method="POST" action="index.php?action=alterarStatus"
                                              onsubmit="return confirm('Recusar a reserva deste passageiro?');">
                                            <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                                            <input type="hidden" name="reserva_id" value="<?= (int) $reserva['reserva_id'] ?>">
                                            <input type="hidden" name="status" value="recusado">
                                            <button type="submit"
                                                class="bg-red-600 hover:bg-red-500 text-white text-sm font-bold px-3 py-1 rounded transition-colors">
                                                Recusar
                                            </button>
                                        </form>
                                    </div>
                                <?php endif; ?>

                            </article>
                        <?php endfor; ?>
                    </div>
                </section>

            <?php endforeach; ?>

        <?php endif; ?>

    </main>

</body>
</html>