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

    $anexos = $_POST['anexos[]'] ?? 'anexos[]';
    $postagemTexto = isset($_POST['PostarTexto']) ? $_POST['PostarTexto'] : null;
    $caminhosAnexos = [];
    $diretorio_destino = "uploads/posts/";

    if (isset($_FILES['anexos']) && !empty($_FILES['anexos']['name'][0])) {
        // Cria a pasta uploads/posts/ se ela não existir
        if (!file_exists($diretorio_destino)) {
            mkdir($diretorio_destino, 0755, true);
        }

        $contagemAnexos = count($_FILES['anexos']['name']);

        for ($i = 0; $i < $contagemAnexos; $i++) {

            //PROCESSA ANEXO
            if ($_FILES['anexos']['error'][$i] === UPLOAD_ERR_OK) {
                $extensao = strtolower(pathinfo($_FILES['anexos']['name'][$i], PATHINFO_EXTENSION));
                $entensoesPermitidas = ['png', 'jpg', 'jpeg', 'gif'];

                //verificar se a extensão da imagem é de uma das extensões permitidas  
                $verificar = getimagesize($_FILES["anexos"]["tmp_name"][$i]);
                if ($verificar !== false && in_array($extensao, $entensoesPermitidas)) {
                    //verifica tamanho do arquivo
                    if ($_FILES["anexos"]["size"][$i] <= 2097152) {
                        //nome unico pro arquivo
                        $nomeUnico = "perfil_" . $id_usuario . "_anexo_" . time() . "." . $extensao;
                        $caminhoFinal = $diretorio_destino . $nomeUnico;

                        if (move_uploaded_file($_FILES["anexos"]["tmp_name"][$i], $caminhoFinal)) {
                            $caminhosAnexos[] = $caminhoFinal;
                        } else {
                            echo "Erro ao mover o arquivo para a pasta.<br>";
                        }
                    } else {
                        echo "O anexo " . ($i + 1) . " excede o tamanho máximo de 2MB.<br>";
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
    //Se tiver o texto e não os anexos
    if ($postagemTexto && !$temAnexo) {
        $stmtPostarTexto = mysqli_prepare($conexao, "INSERT INTO postagem(id_usuario, texto)
        VALUES (?, ?)");
        mysqli_stmt_bind_param($stmtPostarTexto, "is", $id_usuario, $postagemTexto);
        $sucesso = mysqli_stmt_execute($stmtPostarTexto);
    }
    //Se tiver os anexos e não texto
    elseif (!$postagemTexto && $temAnexo) {
        $stmtPostarAnexo = mysqli_prepare($conexao, "INSERT INTO postagem(id_usuario, anexo, anexo2)
        VALUES (?, ?, ?)");
        mysqli_stmt_bind_param($stmtPostarAnexo, "iss", $id_usuario, $anexo1, $anexo2);
        $sucesso = mysqli_stmt_execute($stmtPostarAnexo);
    }
    //Se tiver o texto + anexos
    elseif ($postagemTexto && $temAnexo) {
        $stmtPostarPost = mysqli_prepare($conexao, "INSERT INTO postagem(id_usuario, texto, anexo, anexo2)
        VALUES (?, ?, ?, ?)");
        mysqli_stmt_bind_param($stmtPostarPost, "isss", $id_usuario, $postagemTexto, $anexo1, $anexo2);
        $sucesso = mysqli_stmt_execute($stmtPostarPost);
    } else {
        echo "Postagem inválida. Tente novamente!";
    }

    if ($sucesso) {
        header("Location: feed.php");
        exit;
    } else {
        echo "Erro ao anexar imagem." . mysqli_error($conexao);
    }
}
