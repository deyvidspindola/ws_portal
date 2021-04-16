<?php require_once '_header.php'; ?>
<head>
    <script type="text/javascript" src="modulos/web/js/fin_fat_manual_pesquisa.js" charset="utf-8"></script>
    <script type="text/javascript" src="modulos/web/js/fin_fat_manual_editar.js" charset="utf-8"></script>
    <script type="text/javascript" src="modulos/web/js/lib/jquery.maskedinput-1.3.min.js" charset="utf-8"></script>

    <style type="text/css">

    td.agrupamento {
        background: none repeat scroll 0 0 #BAD0E5;
        font-weight: bold;
        text-align: center;
    }

    </style>
</head>

<form action="" name="frm_editar" id="frm_editar" method="POST">
    <input type="hidden" name="acao" id="acao" value="editarItens" />
    <input type="hidden" name="conceder_creditos" id="conceder_creditos" value="0" />
    <input type="hidden" name="ids_notas" id="ids_notas" value="null" />

    <div class="modulo_titulo">Faturamento Manual</div>
    <div class="modulo_conteudo">

        <?php require_once '_msgPrincipal.php'; ?>

        <ul class="bloco_opcoes">
            <li><a href="fin_fat_manual.php">Gerar NF a partir de NF's já emitidas</a></li>
            <li class="ativo" style="width: 100px;">Gerar NF</li>
            <li><a href="fin_fat_manual.php?acao=telaImportacao">Importação de arquivo</a></li>
        </ul>
        <div class="bloco_titulo">Dados para pesquisa</div>
        <div class="bloco_conteudo">
            <div class="formulario">
                <?php $this->comp_cliente->render() ?>
                <div class="clear"></div>
            </div>
        </div>
        <div class="separador"></div>

		<div class="mensagem alerta"  id="msgalerta2"   style="display:none;" ></div>
		<div class="mensagem sucesso" id="msgsucesso2"  style="display:none;" ></div>
		<div class="mensagem erro"    id="msgerro2"     style="display:none;" ></div>

        <!-- LISTA DE ITENS DAS NOTAS FISCAIS - CARREGA VIA AJAX  -->
        <ul class="bloco_opcoes">
            <li id="itens_nf" class="ativo"><a href="javascript:void(0);">Itens da NF</a></li>
            <li id="outras_nf"><a href="javascript:void(0);">Formas de Pagamento</a></li>
        </ul>
        <div id="aba_itens_nf">
            <div class="bloco_titulo">Itens da nota fiscal</div>
            <div class="bloco_conteudo">
                <div id="frame01"></div>
            </div>
            <div class="bloco_acoes">
                <button type="button" id="incluir_item">Incluir Novo Item</button>
                <button type="button" class="exclui_item" name="exclui_item" id="exclui_item" >Excluir</button>
            </div>

            <div class="separador"></div>
            <div class="mensagem alerta"  id="msgalerta3"   style="display:none;" ></div>
            <div class="mensagem sucesso" id="msgsucesso3"  style="display:none;" ></div>
            <div class="mensagem erro"    id="msgerro3"     style="display:none;" ></div>

            <div id="pesquisa_contrato" style="display: none;">
                <!-- PESQUISA DE CONTRATO PARA ADIï¿½ï¿½O DE ITENS -->
                <?php require_once '_form_pesq_contrato.php'; ?>
            </div>
            <div id="edita_item_nf" style="display: none;">
                <?php require_once '_form_edita_item_nf.php'; ?>
            </div>
        </div>
        <div id="aba_outras_inf_nf" style="display:none;">
            <div class="bloco_titulo">Formas de Pagamento</div>
            <div class="bloco_conteudo">
                <!-- FRAME DE OUTRAS OPCOES - CARREGA VIA AJAX -->
                <div id="frame04">
                    <?php require_once '_outras_inf_nota.php'; ?>
                </div>
            </div>
            <div class="bloco_acoes">
                <button type="button" id="bt_criar_parcelas">Gerar Parcela</button>
            </div>
        </div>
        
        <!------Parametrizaï¿½ï¿½o de parcelas aqui------>
        <?php require_once '_parametrizacao_parcelas.php'; ?>

        <div class="separador"></div>

        <!--Se existir credito futuro-->
        <div id="area_creditos_a_conceder"></div>
        <!-- Fim se existir credito futuro-->

    </div>
	<div class="bloco_acoes">
		<button type="button" id="bt_retorna_oinf" onclick="javascript:if(confirm('Confirma o cancelamento do Faturamento Manual?')){ window.location.href='fin_fat_manual.php' } ">Retornar</button>
	</div>	

</form>


<div id="dialog-form" style="display:none;">
    <div class="bloco_titulo">Pesquisar</div>
    <div class="bloco_conteudo">

        <div class="campo menor">
            <label for="busca_obrigacao_financeira_campo_obroid"><?php echo "ID da Obrigação";?></label>
            <input id="busca_obrigacao_financeira_campo_obroid" name="busca_obrigacao_financeira_campo_obroid" type="text" size="15" maxlength="10" value="" class="campo"   onKeyup="formatar(this, '@');" onBlur="revalidar(this, '@', '');"  />
        </div>
        <div class="campo maior">
            <label for="busca_obrigacao_financeira_campo_obrobrigacao">Obr. Financeira</label>
            <input id="busca_obrigacao_financeira_campo_obrobrigacao" name="busca_obrigacao_financeira_campo_obrobrigacao" type="text" size="15" maxlength="50" value="" class="campo" />
        </div>
        <div class="clear"></div>
    </div>
         
    <div id="div_img_pesquisa_obrigacao_financeira" style="display:none; text-align:center; width:90%; padding:10px;">
        <img src="images/progress.gif">
    </div>
    <div id="result_pesq_obrigacao_financeira" style="display:none;">
    </div>
</div>