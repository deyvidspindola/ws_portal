<?php require_once '_header.php'; ?>
<head>
    <script type="text/javascript" src="modulos/web/js/fin_fat_manual_pesquisa.js" charset="utf-8"></script>
    <script type="text/javascript" src="modulos/web/js/lib/jquery.maskedinput-1.3.min.js" charset="utf-8"></script> 
</head>

<form name="frm_pesquisar" id="frm_pesquisar" method="POST" action="">
    <input type="hidden" name="acao" id="acao" value="pesquisa" />

    <div class="modulo_titulo">Faturamento Manual</div>
    <div class="modulo_conteudo">

        <?php require_once '_msgPrincipal.php'; ?>

        <ul class="bloco_opcoes">
            <li class="ativo"><?php echo "Gerar NF a partir de NF's já emitidas";?></li>
            <li style="width: 100px;"><a href="fin_fat_manual.php?acao=gerarNfNova">Gerar NF</a></li>
            <li><a href="fin_fat_manual.php?acao=telaImportacao"><?php echo "Importação de arquivo";?></a></li>
        </ul>
        <div class="bloco_titulo">Dados para pesquisa de notas fiscais</div>
        <div class="bloco_conteudo">

            <div class="formulario">
                <?php $this->comp_cliente->render() ?>
                <div class="clear"></div>

                <div class="campo menor">
                    <label for="nflno_numero">Nota Fiscal</label>
                    <input type="text" id="nflno_numero" name="nflno_numero" value="" class="campo"  maxlength="9" />
                </div>

                <div class="campo menor">
                    <label for="nflserie"><?php echo "Série";?></label>
                    <select id="nflserie" name="nflserie">
                        <option value="">Escolha</option>
                        <?php foreach ($this->dao->getClassesSeries() as $classe): ?>
                            <option value="<?= $classe['nfsserie'] ?>"><?= $classe['nfsserie'] ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="clear"></div>

                <div class="campo data">
                    <label><?php echo "Data Emissão *";?></label>
                    <input class="campo"  type="text" name="dt_ini" id="dt_ini" maxlength="10" value="<?php echo $dt_ini; ?>" />
                </div>
                <div style="margin-top: 23px !important;" class="campo label-periodo">a</div>
                <div class="campo data">
                    <label>&nbsp;</label>
                    <input  class="campo"  type="text" name="dt_fim" id="dt_fim" maxlength="10" value= "<?php echo $dt_fim; ?>" />
                </div>
                <div class="clear"></div>

            </div>
            <div class="conteudo">
               <?php echo " (*) Campos de preenchimento obrigatório";?>
                <div class="separador"></div>
            </div>
        </div>
        <div class="bloco_acoes">
            <button type="button" id="pesquisar">Pesquisar Nota Fiscal</button>
        </div>
        <div class="separador"></div>

        <div id="frame01"></div>


    </div>

</form>