<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }

$motoristas  = $dados['motoristas']  ?? [];
$passageiros = $dados['passageiros'] ?? [];
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Usuários - UPCAR</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-900 text-gray-100 font-sans min-h-screen">

    <header class="bg-[#2196f3] p-4 shadow-lg">
        <div class="container mx-auto flex justify-between items-center">
            <h1 class="text-2xl font-bold text-white">UPCAR</h1>
            <a href="../../controllers/buscar_caronas.php" class="text-white hover:text-blue-200 text-sm font-semibold transition-colors">
                ← Voltar ao Feed
            </a>
        </div>
    </header>

    <main class="container mx-auto p-6">

        <!-- MOTORISTAS -->
        <section class="mb-10">
            <h2 class="text-2xl font-semibold text-[#81c9fa] border-b border-[#81c9fa] pb-2 mb-5">
                <span class="text-base text-gray-400 font-normal ml-2">(<?= count($motoristas) ?>)</span>
            </h2>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
                <?php if (!empty($motoristas)): ?>
                    <?php foreach ($motoristas as $usuario): ?>
                        <?php $dataCadastro = date('d/m/Y', strtotime($usuario['criado_em'])); ?>
                        <article class="bg-gray-800 rounded-xl p-5 border-l-4 border-[#2196f3] shadow-md flex flex-col justify-between">
                            <div>
                                <div class="flex items-center gap-3 mb-3">
                                    <div class="w-10 h-10 rounded-full bg-[#2196f3] flex items-center justify-center font-bold text-white text-lg">
                                        <?= mb_strtoupper(mb_substr($usuario['nome'], 0, 1)) ?>
                                    </div>
                                    <div>
                                        <h3 class="font-bold text-white text-base"><?= htmlspecialchars($usuario['nome']) ?></h3>
                                        <span class="text-xs text-[#81c9fa] font-semibold">Motorista</span>
                                    </div>
                                </div>
                                <p class="text-xs text-gray-400 mb-1">
                                    <strong class="text-[#81c9fa]">Email:</strong> <?= htmlspecialchars($usuario['email']) ?>
                                </p>
                                <p class="text-xs text-gray-400 mb-4">
                                    <strong class="text-[#81c9fa]">Membro desde:</strong> <?= $dataCadastro ?>
                                </p>
                            </div>
                            <a href="../relatorios/form_relatorio.php?denunciado_id=<?= (int)$usuario['id'] ?>"
                                class="text-center text-xs bg-red-900 hover:bg-red-700 text-red-200 font-semibold py-1 px-3 rounded-lg transition-colors">
                            </a>
                        </article>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p class="text-gray-500 col-span-3">Nenhum motorista cadastrado.</p>
                <?php endif; ?>
            </div>
        </section>

        <section>
            <h2 class="text-2xl font-semibold text-[#81c9fa] border-b border-[#81c9fa] pb-2 mb-5">
                <span class="text-base text-gray-400 font-normal ml-2">(<?= count($passageiros) ?>)</span>
            </h2>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
                <?php if (!empty($passageiros)): ?>
                    <?php foreach ($passageiros as $usuario): ?>
                        <?php $dataCadastro = date('d/m/Y', strtotime($usuario['criado_em'])); ?>
                        <article class="bg-gray-800 rounded-xl p-5 border-l-4 border-gray-500 shadow-md flex flex-col justify-between">
                            <div>
                                <div class="flex items-center gap-3 mb-3">
                                    <div class="w-10 h-10 rounded-full bg-gray-600 flex items-center justify-center font-bold text-white text-lg">
                                        <?= mb_strtoupper(mb_substr($usuario['nome'], 0, 1)) ?>
                                    </div>
                                    <div>
                                        <h3 class="font-bold text-white text-base"><?= htmlspecialchars($usuario['nome']) ?></h3>
                                        <span class="text-xs text-gray-400 font-semibold">Passageiro</span>
                                    </div>
                                </div>
                                <p class="text-xs text-gray-400 mb-1">
                                    <strong class="text-[#81c9fa]">Email:</strong> <?= htmlspecialchars($usuario['email']) ?>
                                </p>
                                <p class="text-xs text-gray-400 mb-4">
                                    <strong class="text-[#81c9fa]">Membro desde:</strong> <?= $dataCadastro ?>
                                </p>
                            </div>
                            <a href="../relatorios/form_relatorio.php?denunciado_id=<?= (int)$usuario['id'] ?>"
                                class="text-center text-xs bg-red-900 hover:bg-red-700 text-red-200 font-semibold py-1 px-3 rounded-lg transition-colors">
                            </a>
                        </article>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p class="text-gray-500 col-span-3">Nenhum passageiro cadastrado.</p>
                <?php endif; ?>
            </div>
        </section>

    </main>
</body>
</html>
