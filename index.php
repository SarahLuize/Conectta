<?php
session_start();

$redirect = isset($_GET['redirect']) ? $_GET['redirect'] : '';

?>

<!DOCTYPE html>
<html lang="pt-br" data-bs-theme="dark">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <link rel="stylesheet" href="./css/index.css">
    <title>Conectta | 📞</title>
</head>

<body class="bg-dark">

    <div class="container my-5 p-3">
        <div class="row g-2">
            <div class="col-md-7 p-3 d-flex flex-column">
                <div class="retangulo p-4 h-100">
                    <h4 class="text-center">Bem vindo ao</h4>
                    <div class="text-center mx-auto d-block">
                        <img src="./img/conectta-logo.png" class="logo mx-auto d-block" alt="Conectta logo">
                    </div>
                    <br>
                    <div class="text-center">
                        <p class="fs-6 lh-base">
                            <b>Conectta</b> é o espaço para compartilhar, descobrir e se conectar<br>
                            <b>Conheça</b> novas pessoas, compartilhe suas ideias e acompanhe o que está acontecendo na sua comunidade<br>
                            <b>Crie</b>  sua conta e comece a se conectar
                        </p>
                    </div>
                </div>
            </div>

            <div class="col-md-5 d-flex p-3 flex-column gap-2">
                <div class="retangulo colunas-esquerda p-3">
                    <div class="text-center mb-3 text-light">Acesse o Conectta com a sua conta</div>

                    <?php
                    if (isset($_SESSION['erro_login'])) {
                        echo '<div class="alert alert-danger w-50" role="alert">' . $_SESSION['erro_login'] . '</div>';
                        unset($_SESSION['erro_login']);
                    }
                    ?>
                    <form action="recebe-login.php" method="POST">
                        <!--INFORMAÇÕES DO PERFIL-->
                        <div class="textoPerfil st-2 ps-3">
                            <!--EMAIL OU NOME DE USUARIO-->
                            <p class="fs-5 mb-0 text-white small mb-1">E-mail ou nome de usuário</p>
                            <div class="input-group mb-3">
                                <input type="text" name="LoginUsuario" class="form-control bg-dark text-white" placeholder="Seu e-mail ou nome de usuário" aria-label="Sizing example input" aria-describedby="inputGroup-sizing-default" required>
                            </div>
                            <!--SENHA-->
                            <p class="fs-5 mb-0 text-white small mb-1">Senha</p>
                            <div class="input-group mb-3">
                                <input type="password" name="LoginSenha" class="form-control bg-dark text-white" placeholder="Senha" aria-label="Sizing example input" aria-describedby="inputGroup-sizing-default" required>
                            </div>
                            <input type="hidden" name="redirect" value="<?php echo htmlspecialchars($redirect); ?>">
                        </div>

                        <!--BOTÃO-->
                        <div class="d-flex justify-content-center">
                            <button class="btn btn-primary botao" type="submit">Entrar</button>
                        </div>
                    </form>
                </div>
                <div class="retangulo colunas-esquerda p-3">
                    <p class="text-center mb-2 small text-light">Não tem uma conta ainda?
                        <a class="btn btn-primary btn-sm fw-bold" href="cadastro.php">Crie uma agora mesmo!</a>
                    </p>
                </div>
            </div>

            <!--FOOTER PÁGINA-->
            <p class="footer text-center"> © <?php echo date("Y"); ?> Conectta - <a href="#">Sobre o Conectta</a> </p>

        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>
</body>

</html>