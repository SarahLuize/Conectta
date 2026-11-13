//para aparecer a imagem na caixa de escrever post/postagem.
const previaFeed = document.getElementById("previaAnexosFeed");
const previaModal = document.getElementById("previaAnexosModal");

const botaoImagemFeed = document.getElementById("post-imagem-feed");
const botaoGifFeed = document.getElementById("post-gif-feed");

const botaoImagemModal = document.getElementById("post-imagem-modal");
const botaoGifModal = document.getElementById("post-gif-modal");

const modalCaixaPostagem = document.getElementById("modalPostarPost");

const botaoComentar = document.getElementById("botaoComentar");

let arquivosSelecionados = [];

function atualizarPrevia(containerPrevia) {
    if (!containerPrevia) {
        return;
    }
    containerPrevia.innerHTML = "";

    if (arquivosSelecionados.length > 0) {
        containerPrevia.classList.add("anexo-arquivos");
        arquivosSelecionados.forEach((arquivo, index) => {
            const itemDiv = document.createElement("div");
            itemDiv.classList.add("divAnexo");
            const img = document.createElement("img");

            const botaoExcluir = document.createElement("button");
            botaoExcluir.classList.add("botaoExcluir");
            botaoExcluir.innerHTML = "x";
            botaoExcluir.type = "button";

            botaoExcluir.addEventListener("click", function() {
                arquivosSelecionados.splice(index, 1);
                atualizarPrevia(containerPrevia);
            });
            itemDiv.appendChild(img);
            itemDiv.appendChild(botaoExcluir);
            containerPrevia.appendChild(itemDiv);

            const leitor = new FileReader();

            leitor.onload = function (e) {
                img.src = e.target.result;
                    
            };
            leitor.readAsDataURL(arquivo);
        });
    } else {
        //esconde a div
        containerPrevia.classList.remove("anexo-arquivos");
    }
}

function adicionarArquivos(inputElemento, containerPrevia) {
    const novosArquivos = inputElemento.files;
    const quantidadeAtual = arquivosSelecionados.length;
    const quantidadeNovos = novosArquivos.length;
    if (quantidadeAtual + quantidadeNovos > 2) {
        alert("Você só pode anexar no máximo 2 mídias por post.");
        inputElemento.value = "";
        return;
    }

    Array.from(novosArquivos).forEach(arquivos => {
        arquivosSelecionados.push(arquivos);
    });
    
    containerPrevia.innerHTML = "";
    atualizarPrevia(containerPrevia);
};

//Salvar anexos num array/vetor
function salvarAnexosVetor(anexosCaixaPostagem){
    let inputAnexos = new DataTransfer();

    anexosCaixaPostagem.addEventListener("change", (e) =>{ 
        for (let i = 0; i < e.target.files.length; i++) {
            if (inputAnexos.files.length < 2) {
                inputAnexos.items.add(e.target.files[i]);
            } else {
                alert("Você só pode anexar no máximo 2 mídias por post.");
                return;
            }
        }
        e.target.files = inputAnexos.files;
    });
}

if(modalCaixaPostagem){
    modalCaixaPostagem.addEventListener('show.bs.modal', function (event) {
        const botao = event.relatedTarget;

        if (!botao) {
            return;
        }
        const acao = botao.getAttribute('data-acao');
        const modalTitulo = document.querySelector('#modalPostarPost .modal-title');
        const modalBotaoSubmit = document.getElementById('modalBotaoSubmit');
        const modalTextArea = document.getElementById('PostarTexto');
        const form = document.getElementById('caixaPostagemModal');
        const previaDiv = document.getElementById('previaAnexosModal');
        const anexo1 = botao.getAttribute('data-anexo');
        const anexo2 = botao.getAttribute('data-anexo2');
        const idPost = botao.getAttribute('data-id'); //id do post , dado vindo do botão
        const idComentario = botao.getAttribute('data-id-comentario'); //id do comentario , dado vindo do botão
        const inputIdPost = document.getElementById('idPost'); //pega input do post
        const inputIdComentario = document.getElementById('idComentario'); //pega input do comentario
        const tipo = document.getElementById('tipo');

        if (acao === 'editar-post') {
            modalTitulo.textContent = 'Editar post';
            modalBotaoSubmit.textContent ='Editar';
            inputIdPost.value = botao.getAttribute('data-id');
            modalTextArea.value = botao.getAttribute('data-texto');
            form.action = 'recebe-editar-post.php';

            previaDiv.innerHTML = '';
            previaDiv.classList.add("anexo-arquivos");

            if(anexo1 && anexo1.trim()){
                previaDiv.innerHTML +=
                `<div class="divAnexo" id="caixaAnexo1">
                    <img src="/Conectta/${anexo1}" class="img-thumbnail" style="width: 80px; height: 80px; object-fit: cover;" alt="Anexo 1">
                    <button type="button" class="botaoExcluir" onClick="removerAnexoAntigo(1)">x</button>
                </div>`;
                console.log("Anexo 1:", anexo1);
            }
            if(anexo2 && anexo2.trim()){
                previaDiv.innerHTML +=
                `<div class="divAnexo" id="caixaAnexo2">
                    <img src="/Conectta/${anexo2}" class="img-thumbnail" style="width: 80px; height: 80px; object-fit: cover;" alt="Anexo 2">
                    <button type="button" class="botaoExcluir" onClick="removerAnexoAntigo(2)">x</button>
                </div>`;
                console.log("Anexo 2:", anexo2);
            }

            if (idComentario) {
                tipo.value = 'comentario';
                document.getElementById('idComentario').value = idComentario;
                inputIdPost.value = ''; //limpa o id do post
            } else {
                tipo.value = 'postagem';
                document.getElementById('idPost').value = idPost;
                inputIdComentario.value = ''; //limpa o id do comenarios
            }
        } else {
            previaDiv.classList.remove("anexo-arquivos");
            modalTitulo.textContent = 'Publicar um novo post';
            modalBotaoSubmit.textContent ='Postar';
            inputIdPost.value = '';
            modalTextArea.value = '';
            form.action = 'recebe-postar-post.php';
            previaDiv.innerHTML = '';
        }
    });
}

function removerAnexoAntigo(numero){
    const caixa = document.getElementById(`caixaAnexo${numero}`);
    if (caixa) {
        caixa.remove();
    }

    const inputApagar = document.getElementById(`apagarAnexo${numero}`);
    if (inputApagar) {
        inputApagar.value = "1";
    }

    const previaDiv = document.getElementById('previaAnexosModal');
    if (previaDiv && previaDiv.children.length === 0) {
        previaDiv.classList.remove("anexo-arquivos");
    }
}

//botões caixa de postagem do feed
if (botaoImagemFeed) {
    botaoImagemFeed.addEventListener("change", function () {
        adicionarArquivos(this, previaFeed);
    });
    salvarAnexosVetor(botaoImagemFeed)
}

if (botaoGifFeed) {
    botaoGifFeed.addEventListener("change", function () {
        adicionarArquivos(this, previaFeed);
    });
    salvarAnexosVetor(botaoGifFeed)
}

//botões caixa de postagem janela modal
if (botaoImagemModal) {
    botaoImagemModal.addEventListener("change", function () {
        adicionarArquivos(this, previaModal);
    });
    salvarAnexosVetor(botaoImagemModal)
}
if (botaoGifModal) {
    botaoGifModal.addEventListener("change", function () {
        adicionarArquivos(this, previaModal);
    });
    salvarAnexosVetor(botaoGifModal)
}

//botão comentar
document.querySelectorAll('.abrir-modal-btn').forEach(botao => {
    botao.addEventListener('click', function () {
        const tipoAcao = this.getAttribute('data-tipo');

        const inputTipo = document.getElementById('tipo');
        if (inputTipo) {
            inputTipo.value = tipoAcao;
        }

        const idPost = this.getAttribute('data-id');

        const inputIdPost = document.getElementById('idPost');

        if (inputIdPost) {
            inputIdPost.value = idPost;
        }
    });
});