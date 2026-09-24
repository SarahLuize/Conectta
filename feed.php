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

//buscar sugestão de outros usuarios

//LIMIT 3 é pra buscar só 3 resultados
$stmt = mysqli_prepare($conexao, "SELECT id, nome, nome_usuario, foto_perfil, verificado FROM usuario WHERE id != ? LIMIT 3");
mysqli_stmt_bind_param($stmt, "i", $id_logado);
mysqli_stmt_execute($stmt);
$resProcurarUsuarios = mysqli_stmt_get_result($stmt);
$sugestao_usuario = mysqli_fetch_assoc($resProcurarUsuarios);


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $conteudo = $_POST['conteudo'] ?? '';

    if (!empty($conteudo)) {
        //para aceitar aspas no texto e não quebrar o sql
        $conteudoLimpo = mysqli_real_escape_string($conexao, $conteudo);

        $sqlSalvarPost = "INSERT INTO postagem(id_usuario, conteudo)
        VALUES('$id_usuario', '$conteudoLimpo')";

        if (mysqli_query($conexao, $sqlSalvarPost)) {
            header("Location: index.php");
            exit;
        } else {
            echo "Erro ao salvar." . mysqli_error($conexao);
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br" data-bs-theme="dark">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <link rel="stylesheet" href="./css/style.css">
    <link rel="stylesheet" href="./css/posts.css">
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

                    <!--CAIXA POSTAGEM PARA TELAS MAIORES-->
                    <div class="card post-bg-color text-white border-secondary p-3 m-4 caixa-postagem">
                        <div class="d-flex gap-3">
                            <div>
                                <img src="<?php echo htmlspecialchars($fotoPerfil); ?>" class="rounded-circle" width="48" height="48" alt="Foto de perfil">
                            </div>

                            <form action="recebe-postar-post.php" id="caixaPostagem" method="POST" class="w-100 gem">
                                <label for="PostarTexto" class="form-label textoDarkMode fw-bold text-center">Publicar um novo post</label>
                                <textarea name="PostarTexto" id="PostarTexto" class="form-control post-bg-color" rows="3" cols="40" maxlength="140" style="resize: none;" placeholder="O que está acontecendo?"></textarea>
                                <!--DIV PARA IMAGEM/GIF/ANEXOS-->
                                <div id="previaAnexosFeed"></div>

                                <label for="post-imagem-feed" class="form-label">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-card-image icone-img" viewBox="0 0 16 16">
                                        <path d="M6.002 5.5a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0" />
                                        <path d="M1.5 2A1.5 1.5 0 0 0 0 3.5v9A1.5 1.5 0 0 0 1.5 14h13a1.5 1.5 0 0 0 1.5-1.5v-9A1.5 1.5 0 0 0 14.5 2zm13 1a.5.5 0 0 1 .5.5v6l-3.775-1.947a.5.5 0 0 0-.577.093l-3.71 3.71-2.66-1.772a.5.5 0 0 0-.63.062L1.002 12v.54L1 12.5v-9a.5.5 0 0 1 .5-.5z" />
                                    </svg>
                                </label>
                                <input class="form-control" type="file" id="post-imagem-feed" name="anexos[]" accept=".png .jpg .jpeg" multiple hidden>

                                <label for="post-gif-feed" class="form-label">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6 icone-gif">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12.75 8.25v7.5m6-7.5h-3V12m0 0v3.75m0-3.75H18M9.75 9.348c-1.03-1.464-2.698-1.464-3.728 0-1.03 1.465-1.03 3.84 0 5.304 1.03 1.464 2.699 1.464 3.728 0V12h-1.5M4.5 19.5h15a2.25 2.25 0 0 0 2.25-2.25V6.75A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25v10.5A2.25 2.25 0 0 0 4.5 19.5Z" />
                                    </svg>
                                </label>
                                <input class="form-control" type="file" id="post-gif-feed" name="anexos[]" accept=".gif" multiple hidden>

                                <div class="d-flex justify-content-end mt-2">
                                    <button class="btn btn-primary px-4 py-2 fw-bold" type="submit">Postar</button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!--BOTÃO + CAIXA POSTAGEM PARA TELAS MENORES-->
                    <button type="button" class="btn btn-primary justify-content-end" data-bs-toggle="modal" data-bs-target="#modalPostarPost">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-pencil-square" viewBox="0 0 16 16">
                            <path d="M15.502 1.94a.5.5 0 0 1 0 .706L14.459 3.69l-2-2L13.502.646a.5.5 0 0 1 .707 0l1.293 1.293zm-1.75 2.456-2-2L4.939 9.21a.5.5 0 0 0-.121.196l-.805 2.414a.25.25 0 0 0 .316.316l2.414-.805a.5.5 0 0 0 .196-.12l6.813-6.814z" />
                            <path fill-rule="evenodd" d="M1 13.5A1.5 1.5 0 0 0 2.5 15h11a1.5 1.5 0 0 0 1.5-1.5v-6a.5.5 0 0 0-1 0v6a.5.5 0 0 1-.5.5h-11a.5.5 0 0 1-.5-.5v-11a.5.5 0 0 1 .5-.5H9a.5.5 0 0 0 0-1H2.5A1.5 1.5 0 0 0 1 2.5z" />
                        </svg>
                    </button>

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
                                    Meu primeiro post de teste no Conectta!
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
                                <?php if(!empty($sugestao_usuario) && is_array($sugestao_usuario)) : ?>
                                    <a href="perfil.php?username=<?php echo $sugestao_usuario['nome_usuario']; ?>">
                                      <img src="<?php echo htmlspecialchars($sugestao_usuario['foto_perfil']); ?>" alt="Perfil" class="rounded" style="height: 52px; width:52px; object-fit:cover;">
                                    </a>
                                    <a class="text-decoration-none" href="perfil.php?username=<?php echo $sugestao_usuario['nome_usuario']; ?>">
                                        <div class="text-white fw-bold small"><?php echo htmlspecialchars($sugestao_usuario['nome']); ?></div>
                                        <div class="text-secondary x-small"><?php echo htmlspecialchars($sugestao_usuario['nome_usuario']); ?></div>
                                    </a>
                                    <button class="btn btn-secondary btn-sm">SEGUIR</button>
                                    <?php else: ?>
                                        <div class="text-secondary small">Nenhuma sugestão no momento</div>
                                <?php endif?>
                            </div>
                        </div>
                    </div>

                    <!--MODAL CAIXA POSTAGEM-->
                    <!-- Modal -->
                    <div class="modal fade" id="modalPostarPost" tabindex="-1" aria-labelledby="modalPostarPost" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content bg-dark text-white border-secondary">
                                <div class="modal-header border-secondary">
                                    <h5 class="modal-title fs-5" id="modalPostarPost">Publicar um novo post</h5>
                                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Publicar post"></button>
                                </div>
                                <form action="recebe-postar-post.php" id="caixaPostagemFlutuante" method="post" enctype="multipart/form-data">
                                    <div class="modal-body">
                                        <!--ID DO USUÁRIO-->
                                        <input type="hidden" name="id" value="<?php echo $_SESSION['usuario_id']; ?>">

                                        <label for="PostarTexto" class="form-label textoDarkMode fw-bold text-center"></label>
                                        <textarea name="PostarTexto" id="PostarTexto" class="form-control post-bg-color" rows="3" cols="40" maxlength="140" style="resize: none;" placeholder="O que está acontecendo?"></textarea>
                                        <!--DIV PARA IMAGEM/GIF/ANEXOS-->
                                        <div id="previaAnexosModal"></div>
                                        
                                        <label for="post-imagem-modal" class="form-label">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-card-image icone-img" viewBox="0 0 16 16">
                                                <path d="M6.002 5.5a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0" />
                                                <path d="M1.5 2A1.5 1.5 0 0 0 0 3.5v9A1.5 1.5 0 0 0 1.5 14h13a1.5 1.5 0 0 0 1.5-1.5v-9A1.5 1.5 0 0 0 14.5 2zm13 1a.5.5 0 0 1 .5.5v6l-3.775-1.947a.5.5 0 0 0-.577.093l-3.71 3.71-2.66-1.772a.5.5 0 0 0-.63.062L1.002 12v.54L1 12.5v-9a.5.5 0 0 1 .5-.5z" />
                                            </svg>
                                        </label>
                                        <input class="form-control" type="file" id="post-imagem-modal" name="anexos[]" accept=".png .jpg .jpeg" multiple hidden>

                                        <label for="post-gif" class="form-label">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6 icone-gif">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M12.75 8.25v7.5m6-7.5h-3V12m0 0v3.75m0-3.75H18M9.75 9.348c-1.03-1.464-2.698-1.464-3.728 0-1.03 1.465-1.03 3.84 0 5.304 1.03 1.464 2.699 1.464 3.728 0V12h-1.5M4.5 19.5h15a2.25 2.25 0 0 0 2.25-2.25V6.75A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25v10.5A2.25 2.25 0 0 0 4.5 19.5Z" />
                                            </svg>
                                        </label>
                                        <input class="form-control" type="file" id="post-gif-modal" name="anexos[]" accept=".gif" multiple hidden>

                                        <div class="d-flex justify-content-end mt-2">
                                            <button class="btn btn-primary px-4 py-2 fw-bold" type="submit">Postar</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>
    </body>

</html>