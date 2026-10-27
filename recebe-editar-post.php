<?php
session_start();
require_once 'dbconexao.php';
require_once 'funcoes.php';
$conexao = obterConexao();

if (!isset($_SESSION['usuario_id'])) {
    header("Location: index.php");
    exit;
}

$id_perfil_logado = $_SESSION['usuario_id'];
$paginaRedirecionada = $_POST['paginaRedirecionar'] ?? 'feed.php';
$id_post = $_POST['idPost'] ?? 0;
$texto = $_POST['PostarTexto'] ?? '';

$anexo1banco = '';
$anexo2banco = '';

$sqlBuscaAntiga = mysqli_prepare($conexao, "SELECT anexo, anexo2 FROM postagem WHERE id = ? AND id_usuario = ?");
if ($sqlBuscaAntiga) {
    mysqli_stmt_bind_param($sqlBuscaAntiga, "ii", $id_post, $id_perfil_logado);
    mysqli_stmt_execute($sqlBuscaAntiga);
    $respostaAtual = mysqli_stmt_get_result($sqlBuscaAntiga);

    if ($dadosAntigos = mysqli_fetch_assoc($respostaAtual)) {
        $anexo1banco = $dadosAntigos['anexo'] ?? '';
        $anexo2banco = $dadosAntigos['anexo2'] ?? '';
    }
    mysqli_stmt_close($sqlBuscaAntiga);
}

$novoAnexo1 = $anexo1banco;
$novoAnexo2 = $anexo2banco;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $diretorio_destino = "uploads/posts/";

    if (!file_exists($diretorio_destino)) {
        mkdir($diretorio_destino, 0755, true);
    }

    $arquivosEnviados = [];

    if (!empty($_FILES['anexos']['name'])) {
        $contagemAnexos = count($_FILES['anexos']['name']);

        for ($i = 0; $i < min($contagemAnexos, 2); $i++) {
            //PROCESSA ANEXO
            if (isset($_FILES['anexos']['error'][$i]) && $_FILES['anexos']['error'][$i] === UPLOAD_ERR_OK) {
                $tmpName = $_FILES['anexos']['tmp_name'][$i];
                $nomeOriginal = $_FILES['anexos']['name'][$i];
                $extensao = strtolower(pathinfo($nomeOriginal, PATHINFO_EXTENSION));
                $entensoesPermitidas = ['png', 'jpg', 'jpeg', 'gif'];

                //verificar se a extensão da imagem é de uma das extensões permitidas  
                if (in_array($extensao, $entensoesPermitidas)) {
                    //verifica tamanho do arquivo // aceita até 5mb por causa dos gifs
                    if ($_FILES["anexos"]["size"][$i] <= 5242880) {

                        //nome unico pro arquivo
                        $nomeUnico = "perfil_" . $id_perfil_logado . "_anexo_" . uniqid() . "_" . $i . "." . $extensao;
                        $caminhoFinal = $diretorio_destino . $nomeUnico;

                        if (function_exists('comprimirAnexos') && $extensao !== 'gif') {
                            $caminhoComprimido = comprimirAnexos($tmpName, $caminhoFinal, 50);
                            if ($caminhoComprimido) {
                                $arquivosEnviados[] = $caminhoComprimido;
                                move_uploaded_file($_FILES["anexos"]["tmp_name"][$i], $caminhoComprimido);
                            } else {
                                if (move_uploaded_file($tmpName, $caminhoFinal)) {
                                    $arquivosEnviados[] = $caminhoFinal;
                                    move_uploaded_file($_FILES["anexos"]["tmp_name"][$i], $caminhoFinal);
                                }
                            }
                        } else {
                            if (move_uploaded_file($tmpName, $caminhoFinal)) {
                                $arquivosEnviados[] = $caminhoFinal;
                                move_uploaded_file($_FILES["anexos"]["tmp_name"][$i], $caminhoFinal);
                            }
                        }
                    } else {
                        echo "O anexo " . ($i + 1) . " excede o tamanho máximo de 5MB.<br>";
                    }
                }
                if ($_FILES["anexos"]["tmp_name"][$i] !== '' && $_FILES["anexos"]["tmp_name"][$i] !== $anexo1banco) {
                    if (file_exists($anexo1banco)) {
                        unlink($anexo1banco);
                    }
                } elseif ($_FILES["anexos"]["tmp_name"][$i] !== '' && $_FILES["anexos"]["tmp_name"][$i] !== $anexo2banco) {
                    if (file_exists($anexo2banco)) {
                        unlink($anexo2banco);
                    }
                }
            }
        }
    }

    if (isset($arquivosEnviados[0])) {
        $novoAnexo1 = $arquivosEnviados[0];
    } else {
        $novoAnexo1 = $anexo1banco;
    }
    if (isset($arquivosEnviados[1])) {
        $novoAnexo2 = $arquivosEnviados[1];
    } else {
        $novoAnexo2 = $anexo2banco;
    }

    $sqlEditarPost = mysqli_prepare($conexao, "UPDATE postagem SET texto = ?, anexo = ?, anexo2 = ? WHERE id = ? AND id_usuario = ?");

    if ($sqlEditarPost) {
        mysqli_stmt_bind_param($sqlEditarPost, "sssii", $texto, $novoAnexo1, $novoAnexo2, $id_post, $id_perfil_logado);
        $sucesso = mysqli_stmt_execute($sqlEditarPost);
        mysqli_stmt_close($sqlEditarPost);

        if ($sucesso) {
            header("Location: " . $paginaRedirecionada);
            exit;
        } else {
            echo "Erro ao atualizar post no banco: " . mysqli_error($conexao);
        }
    }
}
