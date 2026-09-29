//para aparecer a imagem na caixa de escrever post/postagem.
const previaFeed = document.getElementById("previaAnexosFeed");
const previaModal = document.getElementById("previaAnexosModal");

const botaoImagemFeed = document.getElementById("post-imagem-feed");
const botaoGifFeed = document.getElementById("post-gif-feed");

const botaoImagemModal = document.getElementById("post-imagem-modal");
const botaoGifModal = document.getElementById("post-gif-modal");

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

//botões caixa de postagem do feed
if (botaoImagemFeed) {
    botaoImagemFeed.addEventListener("change", function () {
        adicionarArquivos(this, previaFeed);
    });
}

if (botaoGifFeed) {
    botaoGifFeed.addEventListener("change", function () {
        adicionarArquivos(this, previaFeed);
    });
}

//botões caixa de postagem janela modal
if (botaoImagemModal) {
    botaoImagemModal.addEventListener("change", function () {
        adicionarArquivos(this, previaModal);
    });
}

if (botaoGifModal) {
    botaoGifModal.addEventListener("change", function () {
        adicionarArquivos(this, previaModal);
    });
}