<div class="bloco_titulo">Dados Principais</div>
	<div class="bloco_conteudo">
		<div class="formulario">
            <div class="campo data">
                <label for="data_referencia" <?php echo (empty($this->view->dados['data_referencia'])) ? '' : 'class="erro"'; ?> >Data Referência *</label>
                <input id="data_referencia" name="data_referencia" maxlength="10" value="" class="campo data <?php echo (empty($this->view->dados['data_referencia'])) ? '' : ' erro';  ?>" type="text"
                      <?php echo ($this->view->status) ? 'disabled="disabled"' : '';  ?> />
            </div>

            <div class="campo medio">
                <label for="arquivo" <?php echo (empty($this->view->dados['arquivo'])) ? '' : 'class="erro"';  ?> >Arquivo CSV *</label>
                <input id="arquivo" name="arquivo" value="" class="<?php echo (empty($this->view->dados['arquivo'])) ? '' : ' erro';  ?>" type="file" 
                       <?php echo ($this->view->status) ? 'disabled="disabled"' : '';  ?> />
            </div>
            <div class="clear"></div>
		</div>
	</div>
	<div class="bloco_acoes">
        <button type="button" id="btn_incluir" name="btn_incluir">Processar</button>
    </div>

    <?php if(isset($this->view->dados['ressalvas'])) {
        $caminho = $this->view->dados['ressalvas']->file_path . $this->view->dados['ressalvas']->file_name; ?>
      <div id="resultado_arquivo">

        <div class="separador"></div>

        <div class="bloco_titulo resultado">Download</div>
        <div class="bloco_conteudo">

            <div class="conteudo centro">
                <a href="download.php?arquivo=<?php echo $caminho; ?>" target="_blank">
                    <img src="images/icones/t3/caixa2.jpg"><br><?php echo basename($this->view->dados['ressalvas']->file_name); ?>
                </a>
            </div>
        </div>
    </div>
    <?php }  ?>