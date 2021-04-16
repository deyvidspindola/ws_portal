<?php 
require_once '_header.php';
function formata_cgc_cpf($numero,$tipo){
	if($tipo =='F'){
		$buf=@str_repeat("0",11-strlen($numero)).$numero;
		$buf=substr($buf,0,3).".".substr($buf,3,3).".".substr($buf,6,3)."-".substr($buf,9,2);
	}else{
		$buf=@str_repeat("0",14-strlen($numero)).$numero;
		$buf=substr($buf,0,2).".".substr($buf,2,3).".".substr($buf,5,3)."/".substr($buf,8,4)."-".substr($buf,12,2);
	}
	return $buf;
}
?>
<head>
    <script type="text/javascript" src="modulos/web/js/fin_fat_manual_editar.js" charset="utf-8"></script>

    <style type="text/css">

    td.agrupamento {
        background: none repeat scroll 0 0 #BAD0E5;
        font-weight: bold;
        text-align: center;
    }

    </style>
</head>

<div id="div_busca_obrigacao_financeira_background" style="display:none; position: fixed; width:100%; height:100%; left:0; top:0; background-color:#FFF; opacity:0.65; -moz-opacity: 0.65; filter: alpha(opacity=0.65); z-index: 9000"></div>
<div id="div_busca_obrigacao_financeira_overlay" style="display:none; position: fixed; left:50%; top:50%; margin-left:-300px; margin-top:-205px; z-index: 9001"></div>

<div id="dialog-form" style="display:none;">
        	<div class="bloco_titulo">Pesquisar</div>
			<div class="bloco_conteudo">

				<div class="campo menor">					
					<label for="busca_obrigacao_financeira_campo_obroid">ID da Obrigação</label>
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


<form name="frm_editar" id="frm_editar" method="POST" action="">
<input type="hidden" name="acao" id="acao" value="edita" />
<input type="hidden" name="conceder_creditos" id="conceder_creditos" value="0" />
<input type="hidden" name="ids_notas" id="ids_notas" value="<?=$this->vo->ids_notas?>" />

	<div class="modulo_titulo">Faturamento Manual</div>
	<div class="modulo_conteudo">
	
		<?php require_once '_msgPrincipal.php';?>
		
		<div class="bloco_titulo">Cliente</div>
		<div class="bloco_conteudo">
		
            <div class="formulario">
                <?=$this->vo->cliente->clinome?>
                <div class="campo maior">
                    <input type="hidden" id="clioid" name="clioid" value="<?=$this->vo->cliente['clioid']?>" />
                    <label for="clinome">Nome do Cliente</label>
                    <input readonly="readonly" type="text" id="clinome" name="clinome" value="<?=$this->vo->cliente['clinome']?>" class="campo" />
                </div>
                <div class="campo medio">
                    <label for="clidoc">CPF/CNPJ</label>
                    <input readonly="readonly" type="text" id="clidoc"  name="clidoc"  value="<?=formata_cgc_cpf($this->vo->cliente[($this->vo->cliente['clitipo']=="J" ? 'clino_cgc':'clino_cpf')], $this->vo->cliente['clitipo'])?> " class="campo" />
                </div>
                <div class="clear"></div>
            </div>
            
		</div>
		<div class="separador"></div>
		
		<div class="mensagem alerta"  id="msgalerta2"   style="display:none;" ></div>
		<div class="mensagem sucesso" id="msgsucesso2"  style="display:none;" ></div>
		<div class="mensagem erro"    id="msgerro2"     style="display:none;" ></div>

		<!-- LISTA DE ITENS DAS NOTAS FISCAIS - CARREGA VIA AJAX -->
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
			<!-- PESQUISA DE CONTRATO PARA ADIÇÃO DE ITENS -->
				<?php require_once '_form_pesq_contrato.php';?>
			</div>
			<div id="edita_item_nf" style="display: none;">
				<?php require_once '_form_edita_item_nf.php';?>
			</div>
		</div>
		<div id="aba_outras_inf_nf" style="display:none;"> 
			<div class="bloco_titulo">Outras informações da nota fiscal</div>
			<div class="bloco_conteudo">
				<!-- FRAME DE OUTRAS OPCOES - CARREGA VIA AJAX -->
				<div id="frame04">
					<?php require_once '_outras_inf_nota.php';?>
				</div>
			</div>		
			<div class="bloco_acoes">
				<button type="button" id="bt_criar_parcelas">Gerar Parcela</button>
			</div>
		</div>


		<!------Parametrização de parcelas aqui------>
        <?php require_once '_parametrizacao_parcelas.php'; ?>


        <div class="separador"></div>

		<!--Se existir credito futuro-->
		<?php if (isset($this->creditosFuturo) && count($this->creditosFuturo)) : ?>
		<?php require_once 'listar_creditos_futuros_a_conceder.php'; ?>
		<?php endif; ?>
		<!-- Fim se existir credito futuro-->
        

	</div>		
	<div class="bloco_acoes">
		<button type="button" id="bt_retorna_oinf" onclick="javascript:if(confirm('Confirma o cancelamento do Faturamento Manual?')){ window.location.href='fin_fat_manual.php' } ">Retornar</button>
	</div>	
	
</form>