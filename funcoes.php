<?php
require_once 'dbconexao.php';
$conexao = obterConexao();

function verificarSeSegue($conexao, $id_logado, $id_perfil)
{
    //VERIFICAR SE USUARIO LOGADO SEGUE OUTRO USUARIO
    $stmt = mysqli_prepare($conexao, "SELECT quem_esta_seguindo, quem_foi_seguido FROM seguidores WHERE quem_esta_seguindo = ? AND quem_foi_seguido = ?");
    mysqli_stmt_bind_param($stmt, "ii", $id_logado, $id_perfil);
    mysqli_stmt_execute($stmt);
    $resVerificarSeSegueUsuario = mysqli_stmt_get_result($stmt);
    return mysqli_num_rows($resVerificarSeSegueUsuario) > 0;
}

function buscarQtdePostagens($conexao, $id_logado)
{
    $stmt = mysqli_prepare($conexao, "SELECT COUNT(id) FROM postagem WHERE id_usuario = ?");
    mysqli_stmt_bind_param($stmt, 'i', $id_logado);
    mysqli_stmt_execute($stmt);
    $resBuscarQtdePostagens =  mysqli_stmt_get_result($stmt);
    $qtde = mysqli_fetch_row($resBuscarQtdePostagens);
    mysqli_stmt_close($stmt);
    return $qtde[0] ?? 0;
}

function buscarQtdeFavoritos($conexao, $id_logado)
{
    $stmt = mysqli_prepare($conexao, "SELECT COUNT(id) FROM favoritos WHERE id_usuario = ?");
    mysqli_stmt_bind_param($stmt, 'i', $id_logado);
    mysqli_stmt_execute($stmt);
    $resBuscarQtdeFavoritos =  mysqli_stmt_get_result($stmt);
    $qtde = mysqli_fetch_row($resBuscarQtdeFavoritos);
    mysqli_stmt_close($stmt);
    return $qtde[0] ?? 0;
}

function mostrarPosts($conexao)
{
    $stmt = mysqli_prepare($conexao, "SELECT p.*, u.nome, u.nome_usuario, u.foto_perfil FROM postagem AS p
    INNER JOIN usuario AS u
    ON p.id_usuario = u.id
	ORDER BY id DESC;");
    mysqli_stmt_execute($stmt);
    $resBuscarPostUsuario =  mysqli_stmt_get_result($stmt);
    $posts = [];
    while ($post = mysqli_fetch_assoc($resBuscarPostUsuario)) {
        $posts[] = $post;
    }
    mysqli_stmt_close($stmt);
    return $posts;
}

function mostrarPostsApenasUsuario($conexao, $id_perfil)
{
    $stmt = mysqli_prepare($conexao, "SELECT p.*, u.nome, u.nome_usuario, u.foto_perfil FROM postagem AS p
    INNER JOIN usuario AS u
    ON p.id_usuario = u.id
    WHERE p.id_usuario = ?
	ORDER BY id DESC;");
    mysqli_stmt_bind_param($stmt, 'i', $id_perfil);
    mysqli_stmt_execute($stmt);
    $resBuscarPostUsuario =  mysqli_stmt_get_result($stmt);
    $posts = [];
    while ($post = mysqli_fetch_assoc($resBuscarPostUsuario)) {
        $posts[] = $post;
    }
    mysqli_stmt_close($stmt);
    return $posts;
}

function formatarHora($dataSQL){
    $dataPost = new DateTime($dataSQL);
    $agora = new DateTime();
    $diferenca = $agora->diff($dataPost);

    if ($diferenca->y > 0) return $diferenca->y . 'a';
    if ($diferenca->m > 0) return $diferenca->m . 'm';
    if ($diferenca->d > 0) return $diferenca->d . 'd';
    if ($diferenca->h > 0) return $diferenca->h . 'h';
    if ($diferenca->i > 0) return $diferenca->i . 'min';
    
    return 'agora';
}