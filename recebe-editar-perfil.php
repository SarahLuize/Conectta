<?php
session_start();
require_once 'dbconexao.php';
$conexao = obterConexao();

if (!isset($_SESSION['usuario_id'])) {
    header("Location: index.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_usuario = $_SESSION['usuario_id'];
    $consultaAtual = "SELECT nome_usuario, foto_perfil, foto_capa FROM usuario WHERE id = '$id_usuario'";
    $respostaAtual = mysqli_query($conexao, $consultaAtual);
    $usuarioAtual = mysqli_fetch_assoc($respostaAtual);
    
    $fotoPerfilAntiga = $usuarioAtual['foto_perfil'] ?? '';
    $fotoCapaAntiga = $usuarioAtual['foto_capa'] ?? '';

    $novaFotoPerfil = $fotoPerfilAntiga;
    $novaFotoCapa = $fotoCapaAntiga;
    $nomeUsuario = $usuarioAtual['nome_usuario'] ?? '';

    // Cria a pasta uploads se ela não existir
    $diretorio_destino = "./uploads/";
    if (!file_exists($diretorio_destino)) {
        mkdir($diretorio_destino, 0755, true);
    }

    //PROCESSA FOTO DE PERFIL
    if (isset($_FILES['foto_perfil']) && $_FILES['foto_perfil']['error'] === UPLOAD_ERR_OK) {
        $extensao = strtolower(pathinfo($_FILES['foto_perfil']['name'], PATHINFO_EXTENSION));
        $entensoesPermitidas = ['png', 'jpg', 'jpeg', 'gif', 'webp'];

        //verificar se a extensão da imagem é de uma das extensões permitidas  
        $verificar = getimagesize($_FILES["foto_perfil"]["tmp_name"]);
        if ($verificar !== false && in_array($extensao, $entensoesPermitidas)) {
            //verifica tamanho do arquivo
            if ($_FILES["foto_perfil"]["size"] <= 5242880) {
                //nome unico, caso seja foto.png não sobreescreve a foto em mais de um perfil
                $nomeUnico = "perfil_" . $id_usuario . "_" . time() . ".". $extensao;
                $caminhoFinal = $diretorio_destino . $nomeUnico;

                if (move_uploaded_file($_FILES["foto_perfil"]["tmp_name"], $caminhoFinal)) {
                    $novaFotoPerfil = $caminhoFinal;

                    if($fotoPerfilAntiga !== '' && $fotoPerfilAntiga !== './img/PLACEHOLDERpfp.png'){
                        if(file_exists($fotoPerfilAntiga)){
                            unlink($fotoPerfilAntiga);
                        }
                    }
                }
                else{
                    echo "A foto de perfil excede o tamanho máximo de 5MB.<br>";
                }
            }
        } else {
            echo "Formato de foto de perfil inválido.<br>";
        }
    }

    //PROCESSA FOTO DE CAPA
    if (isset($_FILES['foto_capa']) && $_FILES['foto_capa']['error'] === UPLOAD_ERR_OK) {
        $extensao = strtolower(pathinfo($_FILES['foto_capa']['name'], PATHINFO_EXTENSION));
        $entensoesPermitidas = ['png', 'jpg', 'jpeg', 'gif', 'webp'];

        //verificar se a extensão imagem é de uma das extensões permitidas  
        $verificar = getimagesize($_FILES["foto_capa"]["tmp_name"]);
        if ($verificar !== false && in_array($extensao, $entensoesPermitidas)) {
            //verifica tamanho do arquivo // limite 5mb
            if ($_FILES["foto_capa"]["size"] <= 5097152) {
                //nome unico, caso seja foto.png não sobreescreve a capa em mais de um perfil
                $nomeUnico = "capa_" . $id_usuario . "_" . time() . "." . $extensao;
                $caminhoFinal = $diretorio_destino . $nomeUnico;

                if (move_uploaded_file($_FILES["foto_capa"]["tmp_name"], $caminhoFinal)) {
                    $novaFotoCapa = $caminhoFinal;

                    if($fotoCapaAntiga !== '' && $fotoCapaAntiga !== './img/PLACEHOLDERpfp.png'){
                        if(file_exists($fotoCapaAntiga)){
                            unlink($fotoCapaAntiga);
                        }
                    }
                }
                echo "A foto de capa excede o tamanho máximo de 5MB.<br>";
            }
        } else {
            echo "Formato de foto de capa inválido.<br>";
        }
    }

    $novoNome = mysqli_real_escape_string($conexao, $_POST['nome'] ?? '');
    $novoNomeUsuario = mysqli_real_escape_string($conexao, $_POST['nome_usuario'] ?? '');
    $novaBiografia = mysqli_real_escape_string($conexao, $_POST['biografia'] ?? '');

    $qtdSeguindo = mysqli_real_escape_string($conexao, $_POST['qtdSeguindo'] ?? '0');
    $qtdSeguidores =  mysqli_real_escape_string($conexao, $_POST['qtdSeguidores'] ?? '0');

    //VERIFICA SE O NOME DE USUARIO É VALIDO (letras, números, underline e sem espaço)
    if(!preg_match('/^[A-Za-z0-9_]+$/',$novoNomeUsuario)){
        $_SESSION['erro_nomeusuario'] = "Nome de usuário pode ter somente letra e números, não pode ter espaços";
        header("Location: perfil.php?username=" . $nomeUsuario);
        exit;
    }

    //ADICIONA NO BANCO DE DADOS
    $sqlEditarPerfil = "UPDATE usuario
    SET nome = '$novoNome',
    nome_usuario = '$novoNomeUsuario',
    biografia = '$novaBiografia',
    foto_perfil = '$novaFotoPerfil',
    foto_capa = '$novaFotoCapa',
    numero_seguindo = '$qtdSeguindo',
    numero_seguidores = '$qtdSeguidores'
    WHERE id = '$id_usuario'";

    if (mysqli_query($conexao, $sqlEditarPerfil)) {
        header("Location: perfil.php?username=" . $novoNomeUsuario);
        exit;
    } else {
        echo "Erro ao atualizar perfil no banco de dados." . mysqli_error($conexao);
    }
}