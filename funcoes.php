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