<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }

if (!isset($_SESSION['usuario_id'])) {
    header("Location: ../auth/login.php");
    exit;
}

$nomeDenunciado = isset($_GET['denunciado'])
    ? htmlspecialchars(urldecode($_GET['denunciado']))
    : 'o usuário';
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Denúncia Registrada - UPCAR</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-900 text-gray-100 font-sans min-h-screen flex flex-col">

    <header class="bg-[#2196f3] p-4 shadow-lg">
        <h1 class="text-2xl font-bold text-white text-center">UPCAR</h1>
    </header>

    <main class="flex-1 flex items-center justify-center p-6">
        <section class="bg-gray-800 rounded-2xl p-10 border border-gray-700 shadow-xl text-center max-w-md w-full">

            <div class="text-5xl mb-4"></div>
            <h2 class="text-2xl font-bold text-white mb-3">Denúncia Registrada!</h2>
            <p class="text-gray-400 mb-2">
                Sua denúncia contra
                <strong class="text-[#81c9fa]"><?= $nomeDenunciado ?></strong>
                foi registrada com sucesso.
            </p>
            <p class="text-gray-500 text-sm mb-8">
                Nossa equipe de moderação irá analisar o caso. Obrigado por ajudar a manter a comunidade UPCAR segura.
            </p>
            <a href="../../controllers/buscar_caronas.php"
                class="block w-full bg-[#2196f3] hover:bg-[#1976d2] text-white font-bold py-3 px-6 rounded-lg transition-colors">
                Voltar ao Feed
            </a>
        </section>
    </main>

</body>
</html>