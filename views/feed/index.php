<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }

$caronas        = $dados['caronas']        ?? [];
$filtro_origem  = $dados['filtro_origem']  ?? '';
$filtro_destino = $dados['filtro_destino'] ?? '';
$total          = $dados['total']          ?? 0;

$tipoUsuarioLogado = $_SESSION['tipo_usuario'] ?? 'passageiro';
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Feed de Caronas - UPCAR</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-900 text-gray-100 font-sans min-h-screen">

    <header class="bg-[#2196f3] p-4 shadow-lg">
        <div class="container mx-auto flex justify-between items-center">
            <h1 class="text-2xl font-bold text-white">UPCAR</h1>
            <nav class="flex gap-4 text-sm font-semibold items-center">
                <a href="listar_usuarios.php" class="text-white hover:text-blue-200 transition-colors">Ver Perfis</a>
                <span class="text-blue-200">|</span>
                <a href="../index.php?action=minhasReservas" class="text-white hover:text-blue-200 transition-colors">Meu Painel</a>
                <span class="text-blue-200">|</span>
                <span class="text-blue-100">Olá, <?= htmlspecialchars($_SESSION['usuario_nome'] ?? 'Usuário') ?></span>
                <span class="text-blue-200">|</span>
                <a href="../index.php?action=logout" class="text-red-400 hover:text-red-300 transition-colors">Sair</a>
            </nav>
        </div>
    </header>

    <main class="container mx-auto p-6">

        <section class="mb-8">
            <h2 class="text-3xl font-semibold text-[#81c9fa] mb-4 border-b border-[#81c9fa] pb-2">
                Buscar Caronas
            </h2>

            <form method="GET" action="" class="bg-gray-800 p-5 rounded-xl border border-gray-700 flex flex-col md:flex-row gap-4 items-end">
                <div class="flex-1">
                    <label for="origem" class="block text-sm font-semibold text-[#81c9fa] mb-1">Origem</label>
                    <input type="text" id="origem" name="origem"
                        value="<?= htmlspecialchars($filtro_origem) ?>"
                        placeholder="Ex: Campus Ecoville"
                        class="w-full bg-gray-700 border border-gray-600 rounded-lg px-4 py-2 text-white placeholder-gray-400 focus:outline-none focus:border-[#2196f3]">
                </div>

                <div class="flex-1">
                    <label for="destino" class="block text-sm font-semibold text-[#81c9fa] mb-1">Destino</label>
                    <input type="text" id="destino" name="destino"
                        value="<?= htmlspecialchars($filtro_destino) ?>"
                        placeholder="Ex: Centro"
                        class="w-full bg-gray-700 border border-gray-600 rounded-lg px-4 py-2 text-white placeholder-gray-400 focus:outline-none focus:border-[#2196f3]">
                </div>

                <div class="flex gap-3">
                    <button type="submit"
                        class="bg-[#2196f3] hover:bg-[#1976d2] text-white font-bold py-2 px-6 rounded-lg transition-colors">
                        Buscar
                    </button>
                    <?php if (!empty($filtro_origem) || !empty($filtro_destino)): ?>
                        <a href="<?= $_SERVER['PHP_SELF'] ?>"
                            class="bg-gray-600 hover:bg-gray-500 text-white font-bold py-2 px-4 rounded-lg transition-colors">
                            Limpar
                        </a>
                    <?php endif; ?>
                </div>
            </form>
        </section>

        <section>
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-xl text-gray-300">
                    <?php if (!empty($filtro_origem) || !empty($filtro_destino)): ?>
                        Resultados para
                        <?php if (!empty($filtro_origem)): ?>
                            <span class="text-[#81c9fa] font-semibold">"<?= htmlspecialchars($filtro_origem) ?>"</span>
                        <?php endif; ?>
                        <?php if (!empty($filtro_origem) && !empty($filtro_destino)): ?> → <?php endif; ?>
                        <?php if (!empty($filtro_destino)): ?>
                            <span class="text-[#81c9fa] font-semibold">"<?= htmlspecialchars($filtro_destino) ?>"</span>
                        <?php endif; ?>
                    <?php else: ?>
                        Todas as caronas disponíveis
                    <?php endif; ?>
                </h3>
                <span class="text-gray-400 text-sm">
                    <?= $total ?> carona<?= $total !== 1 ? 's' : '' ?> encontrada<?= $total !== 1 ? 's' : '' ?>
                </span>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <?php if (!empty($caronas)): ?>
                    <?php foreach ($caronas as $carona): ?>
                        <?php
                        if ($carona['vagas_disponiveis'] >= 3) {
                            $corBorda = 'border-green-500';
                            $corVagas = 'text-green-400';
                        } elseif ($carona['vagas_disponiveis'] === 2) {
                            $corBorda = 'border-yellow-500';
                            $corVagas = 'text-yellow-400';
                        } else {
                            $corBorda = 'border-red-500';
                            $corVagas = 'text-red-400';
                        }

                        switch ($carona['tipo_usuario']) {
                            case 'motorista':
                                $badgeClasse = 'bg-[#2196f3] text-white';
                                $badgeTexto  = '🚗 Motorista';
                                break;
                            default:
                                $badgeClasse = 'bg-gray-600 text-gray-200';
                                $badgeTexto  = '👤 Usuário';
                        }
                        ?>
                        <article class="bg-gray-800 p-5 rounded-xl border-l-4 <?= $corBorda ?> shadow-md flex flex-col justify-between">
                            <div>
                                <div class="flex justify-between items-start mb-3">
                                    <h4 class="text-lg font-bold text-white leading-tight">
                                        <?= htmlspecialchars($carona['origem']) ?>
                                        <span class="text-[#81c9fa]">➔</span>
                                        <?= htmlspecialchars($carona['destino']) ?>
                                    </h4>
                                    <span class="text-xs font-semibold px-2 py-1 rounded-full <?= $badgeClasse ?> whitespace-nowrap ml-2">
                                        <?= $badgeTexto ?>
                                    </span>
                                </div>
                                <p class="text-sm text-gray-400 mb-1">
                                    <strong class="text-[#81c9fa]">Data/Hora:</strong>
                                    <?= date('d/m/Y H:i', strtotime($carona['data_hora'])) ?>
                                </p>
                                <p class="text-sm text-gray-400 mb-1">
                                    <strong class="text-[#81c9fa]">Motorista:</strong>
                                    <?= htmlspecialchars($carona['motorista_nome']) ?>
                                </p>
                                <p class="text-sm mb-3">
                                    <strong class="text-[#81c9fa]">Vagas:</strong>
                                    <span class="font-bold <?= $corVagas ?>">
                                        <?= (int)$carona['vagas_disponiveis'] ?> / <?= (int)$carona['vagas_totais'] ?>
                                    </span>
                                </p>
                            </div>

                            <div class="flex gap-3 mt-2">
                                <?php if ($tipoUsuarioLogado === 'passageiro'): ?>
                                    <form method="POST" action="../index.php?action=solicitar&carona_id=<?= (int)$carona['id'] ?>" class="flex-1">
                                        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                                        
                                        <button type="submit" 
                                            class="w-full bg-[#2196f3] hover:bg-[#1976d2] text-white text-center text-sm font-bold py-2 px-3 rounded-lg transition-colors">
                                            Solicitar Vaga
                                        </button>
                                    </form>
                                <?php elseif ($tipoUsuarioLogado === 'motorista'): ?>
                                    <span class="flex-1 text-center text-xs text-gray-500 py-2 italic">Você é motorista</span>
                                <?php endif; ?>

                                <a href="../views/relatorios/form_relatorio.php?denunciado_id=<?= (int)$carona['motorista_id'] ?>"
                                    class="bg-red-800 hover:bg-red-700 text-white text-sm font-bold py-2 px-3 rounded-lg transition-colors"
                                    title="Reportar este usuário">⚑</a>
                            </div>
                        </article>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="col-span-3 text-center py-16">
                        <p class="text-gray-400 text-lg mb-2">Nenhuma carona encontrada.</p>
                        <?php if (!empty($filtro_origem) || !empty($filtro_destino)): ?>
                            <p class="text-gray-500 text-sm">Tente outros termos ou
                                <a href="<?= $_SERVER['PHP_SELF'] ?>" class="text-[#81c9fa] underline">veja todas</a>.
                            </p>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            </div>
        </section>

    </main>
</body>
</html>