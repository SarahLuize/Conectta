<?php
session_start();
require_once 'dbconexao.php';
require_once 'funcoes.php';
$conexao = obterConexao();

if (!isset($_SESSION['usuario_id'])) {
    header("Location: index.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_usuario = $_SESSION['usuario_id'];

    $postagemTexto = isset($_POST['PostarTextoFeed']) ? $_POST['PostarTextoFeed'] : '';
    $caminhosAnexos = [];
    $diretorio_destino = "uploads/posts/";
    $somenteAnexos = [];

    if (isset($_FILES['anexos']) && !empty($_FILES['anexos']['name'])) {
        // Cria a pasta uploads/posts/ se ela não existir
        if (!file_exists($diretorio_destino)) {
            mkdir($diretorio_destino, 0755, true);
        }
        $contagemAnexos = count($_FILES['anexos']['name']);
        for ($i = 0; $i < $contagemAnexos; $i++) {

            if ($_FILES['anexos']['error'][$i] === UPLOAD_ERR_OK && $_FILES['anexos']['size'][$i] > 0) {
                $somenteAnexos[] = $i;
            }
        }

        if (!empty($somenteAnexos)) {
            if (count($somenteAnexos) > 2) {
                echo "Você só pode anexar no máximo 2 mídias por post.";
                exit;
            }

            foreach ($somenteAnexos as $indice) {
                //PROCESSA ANEXO
                $extensao = strtolower(pathinfo($_FILES['anexos']['name'][$indice], PATHINFO_EXTENSION));
                $entensoesPermitidas = ['png', 'jpg', 'jpeg', 'gif'];

                //verificar se a extensão da imagem é de uma das extensões permitidas  
                $verificar = getimagesize($_FILES["anexos"]["tmp_name"][$indice]);
                if ($verificar !== false && in_array($extensao, $entensoesPermitidas)) {
                    //verifica tamanho do arquivo // aceita até 5mb por causa dos gifs
                    if ($_FILES["anexos"]["size"][$indice] <= 5242880) {
                        //nome unico pro arquivo
                        $nomeUnico = "perfil_" . $id_usuario . "_anexo_" . uniqid() . "_" . $indice . "." . $extensao;
                        $informacaoArquivo = getimagesize($_FILES["anexos"]["tmp_name"][$indice]);
                        $mime = $informacaoArquivo['mime'];

                        if ($mime == 'image/gif') {
                            $caminhoFinal = $diretorio_destino . $nomeUnico;
                            move_uploaded_file($_FILES["anexos"]["tmp_name"][$indice], $caminhoFinal);
                            $caminhosAnexos[] = $caminhoFinal;
                        } elseif (in_array($mime, ['image/png', 'image/jpg', 'image/jpeg'])) {
                            $caminhoFinal = $diretorio_destino . $nomeUnico;
                            $destinoArquivoComprimido = comprimirAnexos($_FILES["anexos"]["tmp_name"][$indice], $caminhoFinal, 50);
                            move_uploaded_file($_FILES["anexos"]["tmp_name"][$indice], $destinoArquivoComprimido);
                            $caminhosAnexos[] = $destinoArquivoComprimido;
                        } else {
                            echo "Erro ao mover o arquivo para a pasta.<br>";
                        }
                    } else {
                        echo "O anexo " . ($indice + 1) . " excede o tamanho máximo de 5MB.<br>";
                    }
                } else {
                    echo "Formato de anexo inválido.<br>";
                }
            }
        }
    }
    $anexo1 = $caminhosAnexos[0] ?? null;
    $anexo2 = $caminhosAnexos[1] ?? null;

    $temAnexo = !empty($anexo1) || !empty($anexo2);
    $sucesso = '';
    
    $sqlPostarPost = mysqli_prepare($conexao, "INSERT INTO postagem
    (id_usuario, texto, anexo, anexo2)
    VALUES(?, ?, ?, ?)");

    if ($postagemTexto === null && $temAnexo) {
        $postagemTexto = "";
    } elseif ($postagemTexto === null && !$temAnexo) {
        $_SESSION['erro_post_null'] = "Erro ao publicar postagem! Postagens sem anexos não podem ser nulas!";
    }

    if ($sqlPostarPost) {
        mysqli_stmt_bind_param($sqlPostarPost, "isss", $id_usuario, $postagemTexto, $anexo1, $anexo2);
        $sucesso = mysqli_stmt_execute($sqlPostarPost);
        mysqli_stmt_close($sqlPostarPost);
    } else {
        echo "Postagem inválida. Tente novamente!";
    }

    if ($sucesso) {
        header("Location: feed.php");
        exit;
    } else {
        echo "Erro ao salvar post: " . ($erroStmt ?? 'Condição não atendida ou erro desconhecido.');
    }
}
