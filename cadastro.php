<?php
session_start();

if (isset($_SESSION['erro_senhas'])) {
    echo '<div class="alert alert-danger w-50" role="alert">' . $_SESSION['erro_senhas'] . '</div>';
    unset($_SESSION['erro_senhas']);
}

if (isset($_SESSION['erro_cadastro'])) {
    echo '<div class="alert alert-danger w-50" role="alert">' . $_SESSION['erro_cadastro'] . '</div>';
    unset($_SESSION['erro_cadastro']);
}

if (isset($_SESSION['sucesso_cadastro'])) {
    echo '<div class="alert alert-success w-50" role="alert">' . $_SESSION['sucesso_cadastro'] . '</div>';
    unset($_SESSION['sucesso_cadastro']);
}

if (isset($_SESSION['erro_nomeusuario'])) {
    echo '<div class="alert alert-danger w-50" role="alert">' . $_SESSION['erro_nomeusuario'] . '</div>';
    unset($_SESSION['erro_nomeusuario']);
}
?>

<!DOCTYPE html>
<html lang="pt-br" data-bs-theme="dark">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <link rel="stylesheet" href="./css/style.css">
    <link rel="stylesheet" href="./css/cadastro.css">
    <title>GenZetta | Criar conta</title>
</head>

<body class="bg-dark">
    <div class="retangulo">
        <br>
        <h4 class="fs-1 text-center">Criar conta</h4>

        <form action="recebe-cadastro.php" method="POST">

            <!--INFORMAÇÕES DO PERFIL-->
            <div class="textoPerfil mt-2">
                <!--NOME-->
                <br><br>
                <p class="fs-5 mb-0 text-white">Nome</p>
                <div class="input-group mb-3">
                    <input type="text" name="CadastrarNome" class="form-control bg-dark text-white" placeholder="Nome" aria-label="Sizing example input" aria-describedby="inputGroup-sizing-default" required>
                </div>
                <!--E-MAIL-->
                <p class="fs-5 mb-0 text-white">E-mail</p>
                <div class="input-group mb-3">
                    <input type="email" name="CadastrarEmail" class="form-control bg-dark text-white" placeholder="E-mail" aria-label="Sizing example input" aria-describedby="inputGroup-sizing-default" required>
                </div>
                <!--SENHA-->
                <p class="fs-5 mb-0 text-white">Senha</p>
                <div class="input-group mb-3">
                    <input type="password" name="CadastrarSenha" class="form-control bg-dark text-white" placeholder="Senha" aria-label="Sizing example input" aria-describedby="inputGroup-sizing-default" required>
                </div>
                <!--CONFIRMAR SENHA-->
                <p class="fs-5 mb-0 text-white">Confirmar senha</p>
                <div class="input-group mb-3">
                    <input type="password" name="CadastrarConfSenha" class="form-control bg-dark text-white" placeholder="Confirmar senha" aria-label="Sizing example input" aria-describedby="inputGroup-sizing-default" required>
                </div>
                <!--NOME DE USUÁRIO-->
                <p class="fs-5 mb-0 text-white">Nome de usuário</p>
                <div class="input-group flex-nowrap mb-3">
                    <span class="input-group-text bg-dark text-white" id="addon-wrapping">@</span>
                    <input type="text" name="CadastrarNomeUsuario" class="form-control bg-dark text-white" placeholder="Nome de usuário" aria-label="Username" aria-describedby="addon-wrapping" required>
                </div>
            </div>

            <!--BOTÃO-->
            <div class="d-flex justify-content-center pt-3 pe-2">
                <button class="btn btn-primary botao" type="submit">Cadastrar</button>
            </div>
            <br><br>
        </form>

        <p class="text-center">Motivos para criar uma conta na nossa plataforma: <br>
            não somos o Elon Musk, não iremos estragar o GenZetta.</p>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>
</body>

</html>