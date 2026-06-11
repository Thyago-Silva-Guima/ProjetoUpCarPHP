<?php
// views/auth/login.php
if (session_status() === PHP_SESSION_NONE) session_start();
$emailCookie = $_COOKIE['upcar_email'] ?? '';
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>UPCAR – Login</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-950 text-white min-h-screen flex items-center justify-center">

<main class="w-full max-w-md px-6">
    <section class="bg-gray-900 rounded-2xl p-8 shadow-lg border border-gray-800">

        <header class="mb-8 text-center">
            <h1 class="text-3xl font-bold text-[#2196f3]">UPCAR</h1>
            <p class="text-[#81c9fa] mt-1 text-sm">Caronas Universitárias</p>
        </header>

        <?php if (!empty($_SESSION['sucesso'])): ?>
            <div class="mb-4 p-3 bg-green-900 border border-green-600 rounded-lg text-green-300 text-sm">
                <?= htmlspecialchars($_SESSION['sucesso']) ?>
            </div>
            <?php unset($_SESSION['sucesso']); ?>
        <?php endif; ?>

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

        <form action="index.php?action=processarLogin" method="POST" novalidate>
            <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">

            <article class="mb-4">
                <label for="email" class="block text-[#81c9fa] text-sm mb-1">E-mail</label>
                <input type="email" id="email" name="email"
                       value="<?= htmlspecialchars($emailCookie) ?>"
                       required
                       class="w-full bg-gray-800 border border-gray-700 rounded-lg px-4 py-2 text-white focus:outline-none focus:border-[#2196f3] transition">
            </article>

            <article class="mb-4">
                <label for="senha" class="block text-[#81c9fa] text-sm mb-1">Senha</label>
                <input type="password" id="senha" name="senha" required
                       class="w-full bg-gray-800 border border-gray-700 rounded-lg px-4 py-2 text-white focus:outline-none focus:border-[#2196f3] transition">
            </article>

            <article class="mb-6 flex items-center gap-2">
                <input type="checkbox" id="lembrar" name="lembrar" class="accent-[#2196f3]">
                <label for="lembrar" class="text-sm text-gray-400">Lembrar meu e-mail</label>
            </article>

            <button type="submit"
                    class="w-full bg-[#2196f3] hover:bg-blue-600 text-white font-semibold py-2 rounded-lg transition">
                Entrar
            </button>
        </form>

        <footer class="mt-6 text-center text-sm text-gray-500">
            <p>Não tem conta?
                <a href="index.php?action=registro" class="text-[#2196f3] hover:underline">Cadastre-se</a>
            </p>
            <p class="mt-2">
                <a href="index.php?action=recuperar" class="text-[#81c9fa] hover:underline">Esqueci minha senha</a>
            </p>
        </footer>

    </section>
</main>

</body>
</html>
