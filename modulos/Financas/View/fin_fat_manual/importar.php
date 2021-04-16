<?php include_once '_header.php'; ?>
<head>
    <script type="text/javascript" src="modulos/web/js/fin_fat_manual_importar.js" charset="utf-8"></script>
</head>

<form name="frm_importar" id="frm_importar" method="POST" action="fin_fat_manual.php?acao=telaImportacao" enctype="multipart/form-data">
    <div class="modulo_titulo">Faturamento Manual</div>
    <div class="modulo_conteudo">

        <?php include_once '_msgPrincipal.php'; ?>

        <ul class="bloco_opcoes">
            <li><a href="fin_fat_manual.php">Gerar NF a partir de NF's já emitidas</a></li>
            <li style="width: 100px;"><a href="fin_fat_manual.php?acao=gerarNfNova">Gerar NF</a></li>
            <li class="ativo"><a href="fin_fat_manual.php?acao=telaImportacao">"Importação de arquivo"</a></li>
        </ul>

        <div class="bloco_titulo">Importar Arquivo</div>
        <div class="bloco_conteudo">
            <div class="formulario"> 

                <fieldset class="maior">
                    <legend><?php echo "Operação";?></legend>

                    <input type="radio" <?= (($_POST["operacao"] == '1' || !$_POST["operacao"]) ? 'checked="checked"' : '') ?> value="1" name="operacao" id="operacao1">
                    <label for="operacao1">Inserir item(ns) em nota(s) fiscal(ais) emitida(s)</label>

                    <div class="clear"></div> 

                    <input type="radio" <?= ($_POST["operacao"] == '2' ? 'checked="checked"' : '') ?> value="2" name="operacao" id="operacao2">
                    <label for="operacao2">Emitir nova(s) nota(s) fiscal(ais)</label>

                </fieldset>
                <div class="clear"></div>

                <div class="campo maior">					
                    <label for="arquivo_csv">Arquivo *</label>
                    <input type="file" id="arquivo_csv" name="arquivo_csv" />
                </div>					
                <div class="clear"></div>				

            </div>
            <div class="conteudo">
                <?php echo "(*) Campos de preenchimento obrigatório";?>
                <div class="separador"></div>
            </div>						
        </div>

        <div class="bloco_acoes">
            <button type="button" id="importar">Importar</button>
        </div>
    </div>

</form>
