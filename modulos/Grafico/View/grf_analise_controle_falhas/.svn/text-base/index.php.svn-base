<!-- CSS -->
<link type="text/css" rel="stylesheet" href="lib/css/style.css" />
<link type="text/css" rel="stylesheet" href="lib/css/cupertino/jquery-ui-1.10.0.custom.min.css" />

<!-- JAVASCRIPT -->
<script type="text/javascript" src="includes/js/mascaras.js"></script>
<script type="text/javascript" src="includes/js/auxiliares.js"></script>
<script type="text/javascript" src="includes/js/validacoes.js"></script>
<script type="text/javascript" src="modulos/web/js/lib/jQuery.js"></script>
<script type="text/javascript" src="lib/js/jquery-ui-1.10.0.custom.min.js"></script>
<script type="text/javascript" src="lib/js/bootstrap.js"></script>
<script type="text/javascript" src="modulos/web/js/lib/jquery.maskedinput-1.3.min.js"></script>
<script type="text/javascript" src="modulos/web/js/grf_analise_controle_falhas.js"></script>

<form id="frm" name="frm" method="POST" action="grf_analise_controle_falhas.php">
	<input type="hidden" id="acao" name="acao" value="pesquisar" />
	<div class="modulo_titulo">Gráficos para Análise e Controle de Falhas</div>
    <div class="modulo_conteudo">
    	<div class="mensagem" style="display: none;"></div>
    	<div class="bloco_titulo">Dados para Pesquisa</div>
    	<div class="bloco_conteudo">
    		<div class="formulario">
    			<p class="campo maior">
    				<label for="tipo_grafico">Tipo Gráfico: (*)</label>
    				<select id="tipo_grafico" name="tipo_grafico">
    					<?php if(empty($tipo_grafico)) : ?>
    						<option value=""></option>
    					<?php else : ?>
    						<?php foreach($tipo_grafico as $chave => $titulo) : ?>
    							<option value="<?php echo $chave; ?>"><?php echo $titulo; ?></option>
    						<?php endforeach; ?>
    					<?php endif; ?>
    				</select>
    			</p>
    			<p class="campo data">
    				<label for="data_inicio">Período: (*)</label>
    				<input type="text" name="data_inicio" id="data_inicio" maxlength="10" value="" class="campo" />
    			</p>
    			<p class="campo label-periodo">a</p>
    			<p class="campo data">
    				<label for="data_fim">&nbsp;</label>
    				<input type="text" name="data_fim" id="data_fim" maxlength="10" value="" class="campo" />
    			</p>
    			<div class="clear"></div>
    		</div>
    	</div>
    	<div class="bloco_acoes">
    		<button type="submit" id="btn_pesquisar" name="btn_pesquisar">Gerar Gráfico</button>
    	</div>
    	<div class="separador" style="display: none;"></div>
    	<div class="carregando" style="display: none;"></div>
    	<div class="resultado bloco_titulo" style="display: none;">Resultado da pesquisa</div>
    	<div class="resultado bloco_conteudo" style="display: none;">
    		<div class="grafico"></div>
    		<div class="listagem"></div>
    	</div>
    </div>
</form>