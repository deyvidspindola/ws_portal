<?php
if(!empty($arquivo)){
?>
<div class="bloco_titulo">Download</div>
<div class="bloco_conteudo">
    <div class="conteudo centro">
        <a target="_blank" href="download.php?arquivo=<?php echo $arquivo ?>">
            <img src="images/icones/t3/caixa2.jpg">
            <br>
            <?php echo "Simulação Rescisão" ?>
        </a>
    </div>
</div>
<?php } ?>