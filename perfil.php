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

if (isset($_SESSION['erro_post_null'])) {
    echo '<div class="alert alert-danger w-50" role="alert">' . $_SESSION['erro_post_null'] . '</div>';
    unset($_SESSION['erro_post_null']);
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

$posts = mostrarPostsApenasUsuario($conexao, $_SESSION['usuario_id']);
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

                    <!-- POSTAGEM -->
                    <div class="post-item p-3">
                        <div class="d-flex gap-3">
                            <div class="w-100">
                                <?php foreach ($posts as $post): ?>
                                    <div class="d-flex align-items-center gap-2">
                                        <a href="perfil.php">
                                            <img src="<?php echo htmlspecialchars($post['foto_perfil']); ?>" class="rounded" width="48" height="48" alt="Foto de perfil">
                                        </a>
                                        <div>
                                            <a class="user-link d-flex align-items-center gap-2 text-decoration-none" href="perfil.php">
                                                <strong class="text-white"><?php echo htmlspecialchars($post['nome']); ?></strong>
                                                <small class="text-secondary">@<span><?php echo htmlspecialchars($post['nome_usuario']); ?></span></small>
                                            </a>
                                        </div>
                                        <?php $hora = formatarHora($post['data_criacao']); ?>
                                        <small class="text-secondary">•<?php echo ' ' . $hora; ?></small>

                                        <div class="justify-content-lg-end ms-auto p-2">
                                            <div class="dropdown">
                                                <button type="button" class="btn" data-bs-toggle="dropdown" aria-expanded="false">
                                                    ⋮
                                                </button>
                                                <ul class="dropdown-menu">
                                                    <?php if ($post['id_usuario'] == $_SESSION["usuario_id"]): ?>
                                                        <li>
                                                            <form action="recebe-editar-post.php" method="POST" enctype="multipart/form-data">
                                                                <input type="hidden" name="paginaRedirecionar" value="<?php echo $_SERVER['REQUEST_URI']; ?>">
                                                                <input type="hidden" name="idPost" value="<?php echo $post['id']; ?>">

                                                                <button type="button" class="dropdown-item" data-bs-toggle="modal" data-bs-target="#modalPostarPost" data-acao="editar-post" data-id="<?php echo $post['id']; ?>" data-texto="<?php echo $post['texto']; ?>" data-anexo="<?php echo $post['anexo']; ?>" data-anexo2="<?php echo $post['anexo2']; ?>">
                                                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-pencil-fill" viewBox="0 0 16 16">
                                                                        <path d="M12.854.146a.5.5 0 0 0-.707 0L10.5 1.793 14.207 5.5l1.647-1.646a.5.5 0 0 0 0-.708zm.646 6.061L9.793 2.5 3.293 9H3.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.207zm-7.468 7.468A.5.5 0 0 1 6 13.5V13h-.5a.5.5 0 0 1-.5-.5V12h-.5a.5.5 0 0 1-.5-.5V11h-.5a.5.5 0 0 1-.5-.5V10h-.5a.5.5 0 0 1-.175-.032l-.179.178a.5.5 0 0 0-.11.168l-2 5a.5.5 0 0 0 .65.65l5-2a.5.5 0 0 0 .168-.11z" />
                                                                    </svg>
                                                                    Editar post
                                                                </button>
                                                            </form>
                                                        </li>

                                                        <li>
                                                            <form action="recebe-excluir-post.php" method="POST" class="m-0">
                                                                <input type="hidden" name="paginaRedirecionar" value="<?php echo $_SERVER['REQUEST_URI']; ?>">
                                                                <input type="hidden" name="idPost" value="<?php echo $post['id']; ?>">

                                                                <button type="submit" class="dropdown-item">
                                                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-trash-fill" viewBox="0 0 16 16">
                                                                        <path d="M2.5 1a1 1 0 0 0-1 1v1a1 1 0 0 0 1 1H3v9a2 2 0 0 0 2 2h6a2 2 0 0 0 2-2V4h.5a1 1 0 0 0 1-1V2a1 1 0 0 0-1-1H10a1 1 0 0 0-1-1H7a1 1 0 0 0-1 1zm3 4a.5.5 0 0 1 .5.5v7a.5.5 0 0 1-1 0v-7a.5.5 0 0 1 .5-.5M8 5a.5.5 0 0 1 .5.5v7a.5.5 0 0 1-1 0v-7A.5.5 0 0 1 8 5m3 .5v7a.5.5 0 0 1-1 0v-7a.5.5 0 0 1 1 0" />
                                                                    </svg>
                                                                    Deletar post
                                                                </button>
                                                            </form>
                                                        </li>
                                                    <?php else: ?>
                                                        <li><button type="button" class="dropdown-item">
                                                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-ban" viewBox="0 0 16 16">
                                                                    <path d="M15 8a6.97 6.97 0 0 0-1.71-4.584l-9.874 9.875A7 7 0 0 0 15 8M2.71 12.584l9.874-9.875a7 7 0 0 0-9.874 9.874ZM16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0" />
                                                                </svg>
                                                                Bloquear usuário
                                                            </button></li>

                                                        <li><button type="button" class="dropdown-item">
                                                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-eye-slash-fill" viewBox="0 0 16 16">
                                                                    <path d="m10.79 12.912-1.614-1.615a3.5 3.5 0 0 1-4.474-4.474l-2.06-2.06C.938 6.278 0 8 0 8s3 5.5 8 5.5a7 7 0 0 0 2.79-.588M5.21 3.088A7 7 0 0 1 8 2.5c5 0 8 5.5 8 5.5s-.939 1.721-2.641 3.238l-2.062-2.062a3.5 3.5 0 0 0-4.474-4.474z" />
                                                                    <path d="M5.525 7.646a2.5 2.5 0 0 0 2.829 2.829zm4.95.708-2.829-2.83a2.5 2.5 0 0 1 2.829 2.829zm3.171 6-12-12 .708-.708 12 12z" />
                                                                </svg>
                                                                Não interessado
                                                            </button></li>
                                                    <?php endif; ?>

                                                </ul>
                                            </div>
                                        </div>
                                    </div>

                                    <?php if (!empty($post['texto'])): ?>
                                        <p class="text-white mt-1 mb-2">
                                            <?php echo $post['texto']; ?>
                                        </p>
                                    <?php endif; ?>
                                    <?php if (!empty($post['anexo']) || !empty($post['anexo2'])): ?>
                                        <div class="d-flex gap-2 mt-2">
                                            <?php if (!empty($post['anexo'])): ?>
                                                <img src="./<?php echo htmlspecialchars($post['anexo']); ?>" alt="" class="img-fluid mt-2 anexo-posts">
                                            <?php endif; ?>
                                            <?php if (!empty($post['anexo2'])): ?>
                                                <img src="./<?php echo htmlspecialchars($post['anexo2']); ?>" alt="" class="img-fluid mt-2 anexo-posts">
                                            <?php endif; ?>
                                        </div>
                                    <?php endif; ?>

                                    <div class="d-flex justify-content-between text-secondary pt-2" style="max-width: 300px;">
                                        <div class="link-informacoes" title="Comentar">
                                            <form action="#" method="POST">
                                                <input type="hidden" name="paginaRedirecionar" value="<?php echo $_SERVER['REQUEST_URI']; ?>">
                                                <input type="hidden" name="idPost" value="<?php echo $post['id']; ?>">
                                                <button type="submit" class="btn icone-acoes d-flex gap-3">
                                                    💬<span> 0</span>
                                                </button>
                                            </form>
                                        </div>
                                        <div class="link-informacoes" title="Repostar">
                                            <form action="#" method="POST">
                                                <input type="hidden" name="paginaRedirecionar" value="<?php echo $_SERVER['REQUEST_URI']; ?>">
                                                <input type="hidden" name="idPost" value="<?php echo $post['id']; ?>">
                                                <button type="submit" class="btn icone-acoes d-flex gap-3">
                                                    🔄<span> 0</span>
                                                </button>
                                            </form>
                                        </div>
                                        <div class="link-informacoes">
                                            <?php $jaFavoritou = verificarSeFavoritouPost($conexao, $_SESSION['usuario_id'], $post['id']); ?>
                                            <?php if ($jaFavoritou): ?>
                                                <form action="recebe-acao-desfavoritar-post.php" method="POST" title="Desfavoritar">
                                                    <input type="hidden" name="paginaRedirecionar" value="<?php echo $_SERVER['REQUEST_URI']; ?>">
                                                    <input type="hidden" name="idPost" value="<?php echo $post['id']; ?>">
                                                    <button type="submit" class="btn icone-acoes d-flex gap-3">
                                                        ⭐<span> <?php echo $qtdeFavoritos = buscarQtdeFavoritosPost($conexao, $post['id']); ?></span>
                                                    </button>
                                                </form>
                                            <?php else: ?>
                                                <form action="recebe-acao-favoritar-post.php" method="POST" title="Favoritar">
                                                    <input type="hidden" name="paginaRedirecionar" value="<?php echo $_SERVER['REQUEST_URI']; ?>">
                                                    <input type="hidden" name="idPost" value="<?php echo $post['id']; ?>">
                                                    <button type="submit" class="btn icone-acoes d-flex gap-3">
                                                        ⭐<span> <?php echo $qtdeFavoritos = buscarQtdeFavoritosPost($conexao, $post['id']); ?></span>
                                                    </button>
                                                </form>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                    <hr>
                                    <br>
                                <?php endforeach; ?>
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