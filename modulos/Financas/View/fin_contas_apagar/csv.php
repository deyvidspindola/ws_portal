<div class="separador"></div>
<div class="bloco_titulo resultado">Download</div>
<?php if ( $this->view->csv === true ): ?>
<div class="bloco_conteudo">
    <div class="conteudo centro">
        <a href="download.php?arquivo=/var/www/docs_temporario/<?php echo $this->view->nomeArquivo; ?>" target="_blank">
            <img src="images/icones/t3/caixa2.jpg"><br><?php echo $this->view->nomeArquivo; ?>
        </a>
    </div>
</div>
<?php endif; ?>