<?php if ( $this->countRelatorio > 0 ): ?>
<div class="resultado">
    <div class="separador"></div>
    <div class="bloco_titulo resultado">Download</div>

    <div class="bloco_conteudo">
        <div class="conteudo centro">
            <a href="download.php?arquivo=/var/www/docs_temporario/<?php echo $nome_arquivo; ?>" target="_blank">
                <img src="images/icones/t3/caixa2.jpg"><br><?php echo $nome_arquivo; ?>
            </a>
        </div>
    </div>
</div>
<?php endif; ?>