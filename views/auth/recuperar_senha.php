<?php
// views/auth/recuperar_senha.php
if (session_status() === PHP_SESSION_NONE) session_start();
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>UPCAR – Recuperar Senha</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-950 text-white min-h-screen flex items-center justify-center">

<main class="w-full max-w-md px-6">
    <section class="bg-gray-900 rounded-2xl p-8 shadow-lg border border-gray-800">

        <header class="mb-8 text-center">
            <h1 class="text-2xl font-bold text-[#2196f3]">Recuperar Senha</h1>
            <p class="text-[#81c9fa] mt-1 text-sm">Informe seu CPF e data de nascimento</p>
        </header>

        <?php if (!empty($_SESSION['erro'])): ?>
            <div class="mb-4 p-3 bg-red-900 border border-red-600 rounded-lg text-red-300 text-sm">
                <?= htmlspecialchars($_SESSION['erro']) ?>
            </div>
            <?php unset($_SESSION['erro']); ?>
        <?php endif; ?>

        <?php if (!empty($_SESSION['erros'])): ?>
            <div class="mb-4 p-3 bg-red-900 border border-red-600 rounded-lg text-red-300 text-sm">
                <?php foreach ($_SESSION['erros'] as $erro): ?>
                    <p>• <?= htmlspecialchars($erro) ?></p>
                <?php endforeach; ?>
            </div>
            <?php unset($_SESSION['erros']); ?>
        <?php endif; ?>

        <form action="index.php?action=processarRecuperacao" method="POST" novalidate>
            <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">

            <article class="mb-4">
                <label for="cpf" class="block text-[#81c9fa] text-sm mb-1">CPF</label>
                <input type="text" id="cpf" name="cpf" placeholder="000.000.000-00" required
                       class="w-full bg-gray-800 border border-gray-700 rounded-lg px-4 py-2 text-white focus:outline-none focus:border-[#2196f3] transition">
            </article>

            <article class="mb-4">
                <label for="data_nascimento" class="block text-[#81c9fa] text-sm mb-1">Data de Nascimento</label>
                <input type="date" id="data_nascimento" name="data_nascimento" required
                       class="w-full bg-gray-800 border border-gray-700 rounded-lg px-4 py-2 text-white focus:outline-none focus:border-[#2196f3] transition">
            </article>

            <article class="mb-6">
                <label for="nova_senha" class="block text-[#81c9fa] text-sm mb-1">Nova Senha</label>
                <input type="password" id="nova_senha" name="nova_senha" required
                       class="w-full bg-gray-800 border border-gray-700 rounded-lg px-4 py-2 text-white focus:outline-none focus:border-[#2196f3] transition">
            </article>

            <button type="submit"
                    class="w-full bg-[#2196f3] hover:bg-blue-600 text-white font-semibold py-2 rounded-lg transition">
                Redefinir Senha
            </button>
        </form>

        <footer class="mt-6 text-center text-sm text-gray-500">
            <a href="index.php?action=login" class="text-[#81c9fa] hover:underline">Voltar ao login</a>
        </footer>

    </section>
</main>

</body>
</html>
