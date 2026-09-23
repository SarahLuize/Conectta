<?php
session_start();
require_once 'dbconexao.php';
$conexao = obterConexao();
include 'header.php';

$id_usuario = isset($_POST['id']) ? $_POST['id'] : null;
$conteudo = isset($_POST['conteudo']) ? $_POST['conteudo'] : null;

if (!isset($_SESSION['usuario_id'])) {
    header("Location: index.php?redirect=" . urlencode("perfil.php"));
    exit;
}

?>

<!DOCTYPE html>
<html lang="pt-br" data-bs-theme="dark">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <link rel="stylesheet" href="./css/style.css">
    <title>Conectta | 📞</title>
</head>

<body class="bg-dark">
    <div class="container-fluid">
        <div class="row row-cols-3">

            <div class="col-md-3 p-3">
                <div class="retangulo h-100">
                    <form action="logout.php" method="post">
                        <button type="submit" class="m-4 rounded btn btn-danger" title="Desconectar conta">Desconectar</button>
                    </form>
                    <!--EM ALTA-->
                    <div class="p-3">
                        <span class="text-white fw-bold mb-2">EM ALTA</span>
                        <div class="link-informacoes">
                            <span class="text-secondary small">#Topico1</span>
                        </div>
                        <div class="link-informacoes">
                            <span class="text-secondary small">#Topico2</span>
                        </div>
                        <div class="link-informacoes">
                            <span class="text-secondary small">#Topico3</span>
                        </div>
                        <div class="link-informacoes">
                            <span class="text-secondary small">#Topico4</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-6 p-3">

                <div class="retangulo h-100">

                    <!--caixa de postagem-->
                    <div class="card post-bg-color text-white border-secondary p-3 m-4 caixa-postagem">
                        <div class="d-flex gap-3">
                            <div>
                                <img src="<?php echo htmlspecialchars($fotoPerfil); ?>" class="rounded-circle" width="48" height="48" alt="Foto de perfil">
                            </div>

                            <form action="recebe-postar-post.php" id="caixaPostagem" method="POST" class="w-100 gem">
                                <label for="PostarTexto" class="form-label textoDarkMode fw-bold text-center">Publicar um novo post</label>
                                <textarea name="PostarTexto" id="PostarTexto" class="form-control post-bg-color" rows="3" cols="40" maxlength="140" style="resize: none;" placeholder="O que está acontecendo?"></textarea>
                                <!--DIV PARA IMAGEM/GIF/ANEXOS-->
                                <div id="previa-anexos"></div>

                                <label for="post-imagem" class="form-label">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-card-image icone-img" viewBox="0 0 16 16">
                                        <path d="M6.002 5.5a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0" />
                                        <path d="M1.5 2A1.5 1.5 0 0 0 0 3.5v9A1.5 1.5 0 0 0 1.5 14h13a1.5 1.5 0 0 0 1.5-1.5v-9A1.5 1.5 0 0 0 14.5 2zm13 1a.5.5 0 0 1 .5.5v6l-3.775-1.947a.5.5 0 0 0-.577.093l-3.71 3.71-2.66-1.772a.5.5 0 0 0-.63.062L1.002 12v.54L1 12.5v-9a.5.5 0 0 1 .5-.5z" />
                                    </svg>
                                </label>
                                <input class="form-control" type="file" id="post-imagem" name="anexos[]" accept=".png .jpg .jpeg" multiple hidden>

                                <label for="post-gif" class="form-label">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6 icone-gif">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12.75 8.25v7.5m6-7.5h-3V12m0 0v3.75m0-3.75H18M9.75 9.348c-1.03-1.464-2.698-1.464-3.728 0-1.03 1.465-1.03 3.84 0 5.304 1.03 1.464 2.699 1.464 3.728 0V12h-1.5M4.5 19.5h15a2.25 2.25 0 0 0 2.25-2.25V6.75A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25v10.5A2.25 2.25 0 0 0 4.5 19.5Z" />
                                    </svg>
                                </label>
                                <input class="form-control" type="file" id="post-gif" name="anexos[]" accept=".gif" multiple hidden>

                                <div class="d-flex justify-content-end mt-2">
                                    <button class="btn btn-primary px-4 py-2 fw-bold" type="submit">Postar</button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <br>

                    <!-- POSTAGEM -->
                    <div class="post-item p-3 border-bottom border-secondary">
                        <div class="d-flex gap-3">
                            <div class="w-100">
                                <div class="d-flex align-items-center gap-2">
                                    <a href="perfil.php">
                                        <img src="./img/PLACEHOLDERpfp.png" class="rounded" width="48" height="48" alt="Foto de perfil">
                                    </a>
                                    <div>
                                        <a class="user-link d-flex align-items-center gap-2 text-decoration-none" href="perfil.php">
                                            <strong class="text-white">NAME</strong>
                                            <small class="text-secondary">@<span>USERNAME</span></small>
                                        </a>
                                    </div>
                                    <small class="text-secondary">• 2h</small>
                                </div>

                                <p class="text-white mt-1 mb-2">
                                    Meu primeiro post no Conectta!
                                </p>

                                <div class="d-flex justify-content-between text-secondary pt-2" style="max-width: 300px;">
                                    <div class="link-informacoes" title="Comentar">
                                        💬<span> 0</span>
                                    </div>
                                    <div class="link-informacoes" title="Repostar">
                                        🔄<span> 0</span>
                                    </div>
                                    <div class="link-informacoes" title="Favoritar">
                                        ⭐<span> 0</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
            <!--SEGUIDORES-->
            <div class="col-md-3 p-3">
                <div class="retangulo p-3 mb-3">
                    <div class="mb-2">
                        <span class="text-secondary">Quem seguir</span>
                        <br>
                        <a href="#" class="small link-hover-blue text-decoration-none">Recarregar</a>
                        <span class="text-secondary"> | </span>
                        <a href="#" class="small link-hover-blue text-decoration-none">Ver todos</a>
                        <br><br><br>
                        <div class="d-flex align-items-center justify-content-between">
                            <div class="d-flex align-items-center gap-2">
                                <a href="perfil.php">
                                    <img src="./img/PLACEHOLDERpfp.png" alt="Perfil" class="rounded" style="height: 52px; width:52px; object-fit:cover;">
                                </a>
                                <a class="text-decoration-none" href="perfil.php">
                                    <div class="text-white fw-bold small">Nome</div>
                                    <div class="text-secondary x-small">@nomeusuario</div>
                                </a>
                                <button class="btn btn-secondary btn-sm">SEGUIR</button>
                                <br>
                                <div class="text-secondary small">Nenhuma sugestão no momento</div>
                            </div>

                        </div>
                    </div>

                </div>
            </div>
        </div>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>
        <script src="./js/posts.js"></script>
</body>

</html>