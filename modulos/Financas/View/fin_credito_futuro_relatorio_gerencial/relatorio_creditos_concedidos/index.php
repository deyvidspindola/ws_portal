<?php require_once _MODULEDIR_ . "Financas/View/fin_credito_futuro_relatorio_gerencial/cabecalho.php"; ?>

<div class="bloco_titulo">Relatório Gerencial de Descontos Concedidos</div>
<div class="bloco_conteudo">
	
	<div class="formulario">
		<div id="info_principal" class="mensagem info">Campos com * são obrigatórios.</div>
		
		<div id="mensagem_sucesso" class="mensagem sucesso <?php if (empty($this->view->mensagemSucesso)): ?>invisivel<?php endif;?>">
			<?php echo $this->view->mensagemSucesso; ?>
		</div>

		<div id="mensagem_erro" class="mensagem erro <?php if (empty($this->view->mensagemErro)): ?>invisivel<?php endif;?>">
			<?php echo $this->view->mensagemErro; ?>
		</div>

		<div id="mensagem_alerta" class="mensagem alerta <?php if (empty($this->view->mensagemAlerta)): ?>invisivel<?php endif;?>">
			<?php echo $this->view->mensagemAlerta; ?>
		</div>

		<form id="form"  method="post" action="fin_credito_futuro_relatorio_gerencial.php?acao=relatorioCreditosConcedidos">
    		<input type="hidden" id="acao" name="acao" value="relatorioCreditosConcedidos"/>
    		<input type="hidden" id="sub_acao" name="sub_acao" value=""/>
    		<!--Inclui o formulário de pesquisa-->
    		<?php require_once _MODULEDIR_ . "Financas/View/fin_credito_futuro_relatorio_gerencial/relatorio_creditos_concedidos/_formulario.php"; ?>
		</form>



		<div id="resultado_pesquisa" >
    
	    <?php 
        if ( $this->view->status && count($this->view->dados) > 0) { 
        	if ($this->view->parametros->tipo_relatorio == 'A') {

        		require_once _MODULEDIR_ . "Financas/View/fin_credito_futuro_relatorio_gerencial/relatorio_creditos_concedidos/_resultado_analitico_pesquisa.php";

        	} else {

        		if ($this->view->parametros->tipo_resultado == 'd') {

        			require_once _MODULEDIR_ . "Financas/View/fin_credito_futuro_relatorio_gerencial/relatorio_creditos_concedidos/_resultado_sintetico_diario_pesquisa.php";

        		} else {

        			require_once _MODULEDIR_ . "Financas/View/fin_credito_futuro_relatorio_gerencial/relatorio_creditos_concedidos/_resultado_sintetico_mensal_pesquisa.php";

        		}

        	}
        } 
        ?>
	    
		</div>

	</div>

</div>


<?php if (count($this->view->dados) > 0) : ?>
	<!--  Caso contenha erros, exibe os campos destacados  -->
	<script type="text/javascript" >jQuery(document).ready(function() {
		showFormErros(<?php echo json_encode($this->view->dados); ?>); 
	});
	</script>
<?php endif; ?>

<?php require_once _MODULEDIR_ . "Financas/View/fin_credito_futuro_relatorio_gerencial/rodape.php"; ?>