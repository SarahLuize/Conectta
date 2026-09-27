<?php
require_once 'dbconexao.php';
$conexao = obterConexao();

function verificarSeSegue($conexao, $id_logado, $id_visitado){
    $stmt = mysqli_prepare($conexao, "SELECT quem_esta_seguindo, quem_foi_seguido FROM seguidores WHERE quem_esta_seguindo = ? AND quem_foi_seguido = ?");
    mysqli_stmt_bind_param($stmt, "ii", $id_logado, $id_visitado);
    mysqli_stmt_execute($stmt);
    $resVerificarSeSegueUsuario = mysqli_stmt_get_result($stmt);
    //Se já seguir 
    return mysqli_num_rows($resVerificarSeSegueUsuario) > 0;
}