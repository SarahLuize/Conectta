<?php
session_start();
require_once 'dbconexao.php';
require_once 'funcoes.php';
$conexao = obterConexao();

if (!isset($_SESSION['usuario_id'])) {
    header("Location: index.php?redirect=" . urlencode("perfil.php"));
    exit;
}

$id_logado = $_SESSION['usuario_id'];
$usuario = null;

if (isset($_GET['username'])) {
    $username = $_GET['username'];
    $stmt = mysqli_prepare($conexao, "SELECT * FROM usuario WHERE nome_usuario = ?");
    mysqli_stmt_bind_param($stmt, "s", $username);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
    $usuario = mysqli_fetch_assoc($res);
} else if (isset($_GET['id'])) {
    $id_perfil = (int)$_GET['id'];
    $stmt = mysqli_prepare($conexao, "SELECT * FROM usuario WHERE id = ?");
    mysqli_stmt_bind_param($stmt, "i", $id_perfil);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
    $usuario = mysqli_fetch_assoc($res);
} else {
    // se não passar parâmetro, carrega o perfil do usuário logado
    $stmt = mysqli_prepare($conexao, "SELECT * FROM usuario WHERE id = ?");
    mysqli_stmt_bind_param($stmt, "i", $id_logado);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
    $usuario = mysqli_fetch_assoc($res);
}

//se não existir no banco
if (!$usuario) {
    echo "Usuário não encontrado!";
    exit;
}

// ID DO PERFIL
$id_perfil = $usuario['id'];

//buscar sugestão de outros usuarios
//LIMIT 3 é pra buscar só 3 resultados
$stmt = mysqli_prepare($conexao, "SELECT id, nome, nome_usuario, foto_perfil, verificado FROM usuario WHERE id != ? LIMIT 3");
mysqli_stmt_bind_param($stmt, "i", $id_logado);
mysqli_stmt_execute($stmt);
$resProcurarUsuarios = mysqli_stmt_get_result($stmt);
include 'header.php';
?>

<!DOCTYPE html>
<html lang="pt-br" data-bs-theme="dark">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <link rel="stylesheet" href="./css/style.css">
    <link rel="stylesheet" href="./css/perfil.css">
    <link rel="stylesheet" href="./css/posts.css">
    <title>Perfil | <?php echo $usuario['nome'] . " (@" . $usuario['nome_usuario'] . ")"; ?></title>
</head>

<body class="bg-dark">
    <?php
    if (isset($_SESSION['erro_nomeusuario'])) {
        echo '<div class="alert alert-danger w-50" role="alert">' . $_SESSION['erro_nomeusuario'] . '</div>';
        unset($_SESSION['erro_nomeusuario']);
    } ?>
    <div class="perfil-header mb-4">
        <?php
        $fotoCapa = !empty($usuario['foto_capa']) ? $usuario['foto_capa'] : './img/PLACEHOLDERheader.png';
        ?>
        <div class="capa-container" style="background-image: url('<?php echo htmlspecialchars($fotoCapa); ?>'); height:200px; background-size:cover;"></div>
        <div class="capa-wrapper">
            <!--FOTO DE PERFIL-->
            <?php $fotoPerfil = !empty($usuario['foto_perfil']) ? $usuario['foto_perfil'] : './img/PLACEHOLDERpfp.png'; ?>
            <img src="<?php echo htmlspecialchars($fotoPerfil); ?>" id="fotoPerfil" class="rounded border border-3 border-dark" alt="Foto de perfil">
        </div>
    </div>

    <div class="container-fluid d-flex border-bottom border-secondary px-4 pb-2 mb-3">
        <div class="w-100 d-flex align-items-center gap-5" style="min-height: 35px;">

            <div style="width: 200px;" class="d-none d-md-block"></div>
            <!--POSTS + SEGUINDO + SEGUIDORES-->
            <div class="hstack gap-3 info-seg-posts px-3" style="height: 35px;">
                <!--POSTS-->
                <div class="link-informacoes d-flex flex-column">
                    <span class="text-secondary small d-block">POSTS</span>
                    <strong class="text-white d-flex flex-column align-items-center"><?php echo buscarQtdePostagens($conexao, $usuario['id']); ?></strong>
                </div>
                <!--SEGUINDO-->
                <div class="link-informacoes">
                    <span class="text-secondary small d-block">SEGUINDO</span>
                    <strong class="text-white d-flex flex-column align-items-center"><?php echo htmlspecialchars($usuario['numero_seguindo']); ?></strong>
                </div>
                <!--SEGUIDORES-->
                <div class="link-informacoes">
                    <span class="text-secondary small d-block">SEGUIDORES</span>
                    <strong class="text-white d-flex flex-column align-items-center"><?php echo htmlspecialchars($usuario['numero_seguidores']); ?></strong>
                </div>
                <!--FAVORITOS-->
                <div class="link-informacoes">
                    <span class="text-secondary small d-block">FAVORITOS</span>
                    <strong class="text-white d-flex flex-column align-items-center"><?php echo buscarQtdeFavoritos($conexao, $usuario['id']); ?></strong>
                </div>
                <!--BOTÃO EDITAR PERFIL-->
                <?php if ($id_perfil == $_SESSION['usuario_id']) : ?>
                    <div class="ms-auto d-inline-flex p-2 ps-5">
                        <a class="text-decoration-none btn btn-outline-secondary btn-sm fw-bold" data-bs-toggle="modal" data-bs-target="#modalEditarPerfil">
                            ⚙ Editar perfil
                        </a>
                    </div>
                <?php else: ?>
                    <?php $jaSegue = verificarSeSegue($conexao, $_SESSION['usuario_id'], $id_perfil);
                        if ($jaSegue) : ?>
                        <div class="ms-auto d-inline-flex p-2 ps-5">
                            <form action="recebe-acao-deixar-de-seguir.php" method="POST">
                                <input type="hidden" name="idPerfilVisitado" value="<?php echo $id_perfil; ?>">
                                <input type="hidden" name="nomeUsuarioPerfilVisitado" value="<?php echo $usuario['nome_usuario']; ?>">
                                <input type="hidden" name="paginaRedirecionar" value="<?php echo $_SERVER['REQUEST_URI']; ?>">
                                <input type="submit" name="deixarDeSeguir" class="text-decoration-none btn btn-outline-secondary btn-sm fw-bold" value="DEIXAR DE SEGUIR">
                            </form>
                        </div>
                    <?php else: ?>
                        <div class="ms-auto d-inline-flex p-2 ps-5">
                            <form action="recebe-acao-seguir.php" method="POST">
                                <input type="hidden" name="idPerfilVisitado" value="<?php echo $id_perfil; ?>">
                                <input type="hidden" name="nomeUsuarioPerfilVisitado" value="<?php echo $usuario['nome_usuario']; ?>">
                                <input type="hidden" name="paginaRedirecionar" value="<?php echo $_SERVER['REQUEST_URI']; ?>">
                                <input type="submit" name="seguir" class="text-decoration-none btn btn-outline-secondary btn-sm fw-bold" value="SEGUIR">
                            </form>
                        </div>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <div class="container-fluid">
        <div class="row">

            <div class="col-md-3 p-3">

                <div class="retangulo h-100">
                    <!--INFORMAÇÕES DO PERFIL-->
                    <div class="textoPerfil mt-2 ps-3">
                        <h2 class="h5 fw-bold mb-0 text-white"><?php echo htmlspecialchars($usuario['nome']); ?></h2>
                        <div class="text-secondary mb-0">
                            @<span><?php echo htmlspecialchars($usuario['nome_usuario']); ?></span>
                        </div>
                    </div>
                    <div>
                        <div class="textoBiografia mt-3 px-3 text-break">
                            <?php echo htmlspecialchars($usuario['biografia']); ?>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-6 p-3">
                <div class="retangulo h-100">
                    <div class="hstack gap-3 info-seg-posts px-3 mt-2">
                        <div class="w-100 d-flex justify-content-start gap-5" style="min-height: 10px;">
                            <div class="link-informacoes">
                                <span class="text-secondary d-block">Posts</span>
                            </div>
                            <div class="link-informacoes">
                                <span class="text-secondary d-block">Posts & Respostas</span>
                            </div>
                            <div class="link-informacoes">
                                <span class="text-secondary d-block">Mídia</span>
                            </div>
                        </div>
                    </div>
                    <hr class="border-secondary my-3">

                    <!--POSTAGENS-->
                    <div class="post-item p-3 border-bottom border-secondary">
                        <div class="d-flex gap-3">
                            <div class="w-100">
                                <div class="d-flex align-items-center gap-2">
                                    <a href="perfil.php">
                                        <img src="./img/PLACEHOLDERpfp.png" class="rounded" width="48" height="48" alt="Foto de perfil">
                                    </a>
                                    <div>
                                        <a class="user-link d-flex align-items-center gap-2 text-decoration-none" href="perfil.php"> <!--Trocar para usuario que postou-->
                                            <strong class="text-white">NAME</strong>
                                            <small class="text-secondary">@<span>USERNAME</span></small>
                                        </a>
                                    </div>
                                    <small class="text-secondary">• 2h</small>
                                </div>

                                <p class="text-white mt-1 mb-2">
                                    Meu primeiro post de teste na rede social!
                                </p>

                                <div class="d-flex justify-content-between text-secondary pt-2" style="max-width: 300px;">
                                    <div class="link-informacoes" title="Comentar">
                                        💬<span> 0</span>
                                    </div>
                                    <div class="link-informacoes" title="Rezettar">
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

            <!--SEGUIDORES / EM ALTA-->
            <div class="col-md-3 p-3">
                <div class="retangulo p-3 mb-3">
                    <div class="mb-2">
                        <span class="text-secondary">Talvez você goste de..</span>
                        <br>
                        <a href="#" class="small link-hover-blue text-decoration-none">Recarregar</a>
                        <span class="text-secondary"> | </span>
                        <a href="#" class="small link-hover-blue text-decoration-none">Ver todos</a>
                        <br><br><br>
                        <?php if (mysqli_num_rows($resProcurarUsuarios) > 0) : ?>
                            <?php while ($sugestao = mysqli_fetch_assoc($resProcurarUsuarios)) : ?>
                                <?php
                                $stmtSegue = mysqli_prepare($conexao, "SELECT 1 FROM seguidores WHERE quem_esta_seguindo = ? AND quem_foi_seguido = ?");
                                mysqli_stmt_bind_param($stmtSegue, "ii", $id_logado, $sugestao['id']);
                                mysqli_stmt_execute($stmtSegue);
                                $jaSegue = mysqli_num_rows(mysqli_stmt_get_result($stmtSegue)) > 0;
                                ?>

                                <div class="d-flex align-items-center justify-content-between mb-3">
                                    <div class="d-flex align-items-center gap-2">
                                        <a href="perfil.php?username=<?php echo urlencode($sugestao['nome_usuario']); ?>">
                                            <img src="<?php echo htmlspecialchars($sugestao['foto_perfil'] ?? './img/PLACEHOLDERpfp.png'); ?>" alt="Perfil" class="rounded" style="height: 42px; width: 42px; object-fit: cover;">
                                        </a>
                                        <div>
                                            <a class="text-decoration-none" href="perfil.php?username=<?php echo urlencode($sugestao['nome_usuario']); ?>">
                                                <div class="text-white fw-bold small mb-0"><?php echo htmlspecialchars($sugestao['nome']); ?></div>
                                                <div class="text-secondary x-small">@<?php echo htmlspecialchars($sugestao['nome_usuario']); ?></div>
                                            </a>
                                        </div>
                                    </div>

                                    <?php $jaSegue = verificarSeSegue($conexao, $_SESSION['usuario_id'], $sugestao['id']);
                                        if ($jaSegue) : ?>
                                        <form action="recebe-acao-deixar-de-seguir.php" method="POST">
                                            <input type="hidden" name="idPerfilVisitado" value="<?php echo $sugestao['id']; ?>">
                                            <input type="hidden" name="paginaRedirecionar" value="<?php echo $_SERVER['REQUEST_URI']; ?>">
                                            <button type="submit" name="seguir" class="btn btn-secondary btn-sm fw-bold">SEGUINDO</button>
                                        </form>
                                    <?php else : ?>
                                        <form action="recebe-acao-seguir.php" method="POST">
                                            <input type="hidden" name="idPerfilVisitado" value="<?php echo $sugestao['id']; ?>">
                                            <input type="hidden" name="paginaRedirecionar" value="<?php echo $_SERVER['REQUEST_URI']; ?>">
                                            <button type="submit" name="seguir" class="btn btn-primary btn-sm fw-bold">SEGUIR</button>
                                        </form>
                                    <?php endif; ?>
                                </div>
                            <?php endwhile; ?>
                        <?php else : ?>
                            <div class="text-secondary small">Nenhuma sugestão no momento</div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <div class="p-3">
                <hr>
                <span class="text-white fw-bold mb-2">EM ALTA</span>
                <div class="link-informacoes">
                    <span class="text-secondary small">#Topico1</span>
                </div>
                <div class="link-informacoes">
                    <span class="text-secondary small">#Topico2</span>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalEditarPerfil" tabindex="-1" aria-labelledby="modalEditarPerfilLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content bg-dark text-white border-secondary">
                <div class="modal-header border-secondary">
                    <h5 class="modal-title fs-5" id="modalEditarPerfilLabel
                    <button type=" button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Fechar">Editar perfil</button>
                </div>
                <form action="recebe-editar-perfil.php" method="post" enctype="multipart/form-data">
                    <div class="modal-body">
                        <input type="hidden" name="id" value="<?php echo $_SESSION['usuario_id']; ?>">

                        <!--FOTO DE PERFIL-->
                        <div class="mb-3">
                            <label class="form-label text-secondary small">Foto de perfil</label>
                            <input type="file" name="foto_perfil" id="foto_perfil" class="form-control bg-dark text-white border-secondary" accept=".png, .jpg, .jpeg, .gif, .webp">
                        </div>

                        <!--FOTO DE CAPA-->
                        <div class="mb-3">
                            <label class="form-label text-secondary small">Foto de capa</label>
                            <input type="file" name="foto_capa" id="foto_capa" class="form-control bg-dark text-white border-secondary" accept=".png, .jpg, .jpeg, .gif, .webp">
                        </div>

                        <!--NOME-->
                        <div class="mb-3">
                            <label class="form-label text-secondary small">Nome</label>
                            <input type="text" name="nome" class="form-control bg-dark text-white border-secondary" value="<?php echo htmlspecialchars($usuario['nome']); ?>" required>
                        </div>
                        <!--NOME DE USUARIO-->
                        <div class="mb-3">
                            <label class="form-label text-secondary small">Nome de usuário</label>
                            <div class="input-group">
                                <span class="input-group-text bg-dark text-white" id="addon-wrapping">@</span>
                                <input type="text" name="nome_usuario" class="form-control bg-dark text-white border-secondary" value="<?php echo htmlspecialchars($usuario['nome_usuario']); ?>" required>
                            </div>
                        </div>

                        <!--BIOGRAFIA-->
                        <div class="mb-3">
                            <label class="form-label text-secondary small">Biografia</label>
                            <textarea type="text" name="biografia" class="form-control bg-dark text-white border-secondary" rows="3" maxlength="160"><?php echo htmlspecialchars($usuario['biografia'] ?? ''); ?></textarea>
                        </div>
                    </div>
                    <div class="modal-footer border-secondary">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary fw-bold">Salvar</button>
                    </div>
                </form>

            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>