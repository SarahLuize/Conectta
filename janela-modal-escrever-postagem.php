<!--MODAL CAIXA POSTAGEM-->
<!-- Modal -->
<div class="modal fade" id="modalPostarPost" tabindex="-1" aria-labelledby="modalPostarPost" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content bg-dark text-white border-secondary">
            <div class="modal-header border-secondary">
                <h5 class="modal-title fs-5" id="modalPostarPost">Publicar um novo post</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Publicar post"></button>
            </div>
            <form action="recebe-postar-post.php" id="caixaPostagemModal" method="post" enctype="multipart/form-data">
                <div class="modal-body">
                    <!--ID DO USUÁRIO-->
                    <input type="hidden" name="id" value="<?php echo $_SESSION['usuario_id']; ?>">
                    <input type="hidden" name="idPost" id="idPost" value="">
                    <input type="hidden" name="paginaRedirecionar" value="<?php echo $_SERVER['REQUEST_URI']; ?>">
                    <input type="hidden" name="tipo" id="idTipo" value="postagem">

                    <label for="PostarTexto" class="form-label textoDarkMode fw-bold text-center"></label>
                    <textarea name="PostarTexto" id="PostarTexto" class="form-control post-bg-color" rows="3" cols="40" maxlength="140" style="resize: none;" placeholder="O que está acontecendo?"></textarea>
                    <!--DIV PARA IMAGEM/GIF/ANEXOS-->
                    <div id="previaAnexosModal" class="d-flex"></div>
                    <input type="hidden" name="apagarAnexo1" id="apagarAnexo1" value="0">
                    <input type="hidden" name="apagarAnexo2" id="apagarAnexo2" value="0">

                    <label for="post-imagem-modal">
                        <svg xmlns="http://www.w3.org/2000/svg" class="bi bi-card-image icone-img" fill="currentColor" viewBox="0 0 16 16">
                            <path d="M6.002 5.5a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0" />
                            <path d="M1.5 2A1.5 1.5 0 0 0 0 3.5v9A1.5 1.5 0 0 0 1.5 14h13a1.5 1.5 0 0 0 1.5-1.5v-9A1.5 1.5 0 0 0 14.5 2zm13 1a.5.5 0 0 1 .5.5v6l-3.775-1.947a.5.5 0 0 0-.577.093l-3.71 3.71-2.66-1.772a.5.5 0 0 0-.63.062L1.002 12v.54L1 12.5v-9a.5.5 0 0 1 .5-.5z" />
                        </svg>
                    </label>
                    <input class="form-control" type="file" id="post-imagem-modal" name="anexos[]" accept=".png, .jpg, .jpeg" multiple hidden>

                    <label for="post-gif">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="bi bi-card-image icone-gif">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12.75 8.25v7.5m6-7.5h-3V12m0 0v3.75m0-3.75H18M9.75 9.348c-1.03-1.464-2.698-1.464-3.728 0-1.03 1.465-1.03 3.84 0 5.304 1.03 1.464 2.699 1.464 3.728 0V12h-1.5M4.5 19.5h15a2.25 2.25 0 0 0 2.25-2.25V6.75A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25v10.5A2.25 2.25 0 0 0 4.5 19.5Z" />
                        </svg>
                    </label>
                    <input class="form-control" type="file" id="post-gif-modal" name="anexos[]" accept=".gif" multiple hidden>

                    <div class="d-flex justify-content-end mt-2">
                        <button class="btn btn-primary px-4 py-2 fw-bold" type="submit" id="modalBotaoSubmit">Postar</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>