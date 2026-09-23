<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once 'dbconexao.php';
$conexao = obterConexao();

$id_logado = isset($_SESSION['usuario_id']) ? $_SESSION['usuario_id'] : null;
$username_logado = '';

// Se tiver usuário logado, busca o nome_usuario dele no banco de dados
if ($id_logado) {
    $sqlHeader = "SELECT nome_usuario, foto_perfil FROM usuario WHERE id = '$id_logado'";
    $resHeader = mysqli_query($conexao, $sqlHeader);

    if ($resHeader && $rowHeader = mysqli_fetch_assoc($resHeader)) {
        $username_logado = $rowHeader['nome_usuario'];

        if(!empty ($rowHeader['foto_perfil'])){
            $fotoPerfil = $rowHeader['foto_perfil'];
        }
    }
}
?>

<div class="col-md-auto">
    <nav class="navbar navbar-expand-lg">
        <div class="container-fluid">
            <div class="d-flex align-items-center gap-4">
                <a class="nav-link active" href="feed.php">🏠Home</a>
                <a class="nav-link active" href="#">🔔Notificações</a>
                <a class="nav-link active" href="#">🗨Mensagens</a>
            </div>
            <div class="d-flex justify-content-center">
                <a class="nav-link active" href="index.php" title="Connectta">📞</a>
            </div>
            <div class="d-flex align-content-center gap-2">
                <form class="d-flex" role="search" action="#">
                    <div class="input-group input-group-sm">
                        <input class="form-control me-2 rounded-pill" type="search" name="search" id="search" placeholder="Procure no Conectta">
                        <button class="btn btn-outline-success rounded" type="submit" title="Procurar">🔍</button>
                    </div>
                </form>
                <a class="rounded d-flex align-items-center ms-1" href="perfil.php?username=<?php echo urlencode($username_logado); ?>">
                    <img src="<?php echo htmlspecialchars($fotoPerfil); ?>" alt="Perfil" title="Ir para seu perfil" class="rounded" style="height: 32px; width:32px; object-fit:cover;">
                </a>
                <button class="btn btn-outline-primary rounded" title="Postar">Postagem</button>
            </div>
        </div>
    </nav>
</div>