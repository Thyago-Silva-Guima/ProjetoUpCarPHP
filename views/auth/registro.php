<?php
// views/auth/registro.php
if (session_status() === PHP_SESSION_NONE) session_start();
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>UPCAR – Cadastro</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-950 text-white min-h-screen flex items-center justify-center py-10">

<main class="w-full max-w-lg px-6">
    <section class="bg-gray-900 rounded-2xl p-8 shadow-lg border border-gray-800">

        <header class="mb-8 text-center">
            <h1 class="text-3xl font-bold text-[#2196f3]">UPCAR</h1>
            <p class="text-[#81c9fa] mt-1 text-sm">Criar nova conta</p>
        </header>

        <?php if (!empty($_SESSION['erros'])): ?>
            <div class="mb-4 p-3 bg-red-900 border border-red-600 rounded-lg text-red-300 text-sm">
                <?php foreach ($_SESSION['erros'] as $erro): ?>
                    <p>• <?= htmlspecialchars($erro) ?></p>
                <?php endforeach; ?>
            </div>
            <?php unset($_SESSION['erros']); ?>
        <?php endif; ?>

        <form action="index.php?action=processarRegistro" method="POST" novalidate>
            <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">

            <?php
            // foreach para renderizar os campos dinamicamente
            $campos = [
                ['id' => 'nome',            'label' => 'Nome Completo',        'type' => 'text',     'name' => 'nome'],
                ['id' => 'email',           'label' => 'E-mail Institucional', 'type' => 'email',    'name' => 'email'],
                ['id' => 'cpf',             'label' => 'CPF',                  'type' => 'text',     'name' => 'cpf'],
                ['id' => 'data_nascimento', 'label' => 'Data de Nascimento',   'type' => 'date',     'name' => 'data_nascimento'],
                ['id' => 'senha',           'label' => 'Senha',                'type' => 'password', 'name' => 'senha'],
            ];

            foreach ($campos as $campo):
            ?>
                <article class="mb-4">
                    <label for="<?= $campo['id'] ?>" class="block text-[#81c9fa] text-sm mb-1">
                        <?= $campo['label'] ?>
                    </label>
                    <input type="<?= $campo['type'] ?>"
                           id="<?= $campo['id'] ?>"
                           name="<?= $campo['name'] ?>"
                           required
                           class="w-full bg-gray-800 border border-gray-700 rounded-lg px-4 py-2 text-white focus:outline-none focus:border-[#2196f3] transition">
                </article>
            <?php endforeach; ?>

            <article class="mb-6">
                <label class="block text-[#81c9fa] text-sm mb-2">Tipo de Usuário</label>
                <div class="flex gap-4">
                    <?php
                    $tipos = ['passageiro' => 'Passageiro', 'motorista' => 'Motorista'];
                    foreach ($tipos as $valor => $rotulo):
                    ?>
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="radio" name="tipo_usuario" value="<?= $valor ?>" class="accent-[#2196f3]">
                            <span class="text-sm"><?= $rotulo ?></span>
                        </label>
                    <?php endforeach; ?>
                </div>
            </article>

            <button type="submit"
                    class="w-full bg-[#2196f3] hover:bg-blue-600 text-white font-semibold py-2 rounded-lg transition">
                Criar Conta
            </button>
        </form>

        <footer class="mt-6 text-center text-sm text-gray-500">
            Já tem conta?
            <a href="index.php?action=login" class="text-[#2196f3] hover:underline">Fazer login</a>
        </footer>

    </section>
</main>

</body>
</html>
