<?php cabecalho(); ?>

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
<script type="text/javascript" src="modulos/web/js/rel_analise_controle_falhas_acessorio.js"></script>

<style>
	.data {
		width: 119px!important;
	}
	
	.menor input, .menor select {
		width: 109px!important;
	}
	
	.medio select {
		width: 238px!important;
	}
</style>

<!--[if IE]>
	<style>
		.ie .data {
			width: 121px!important;
		}
		
		.ie .menor input, .ie .menor select {
			width: 121px!important;
		}
		
		.ie .medio select {
			width: 252px!important;
		}
	</style>
<![endif]-->

<form class="ie" id="form" action="rel_analise_controle_falhas_acessorio.php" method="post">
	<input id="acao" type="hidden" name="acao" value="pesquisar" />
	<div class="modulo_titulo">Relatório de Análise e Controle de Falhas Acessórios</div>
	<div class="modulo_conteudo">
		<div id="div-mensagem-info" class="mensagem info">
			Os campos com <strong>*</strong> são obrigatórios.
		</div>
		<div id="div-mensagem-erro" class="mensagem erro" style="display: none;">
			Houve um erro na comunicação com o servidor.
		</div>
		<div id="div-mensagem-formulario" class="mensagem alerta" style="display: none;">
			Existem campos obrigatórios não preenchidos.
		</div>
		<div class="bloco_titulo">Dados para pesquisa</div>
		<div class="bloco_conteudo">
			<div class="formulario">
				<div class="campo medio">
					<label for="imobserial">Serial</label>
					<input id="imobserial" type="text" name="imobserial" maxlength="15" value="" class="campo" />
				</div>
				<div class="campo medio">
					<label for="imobimotoid">Tipo</label>
					<select id="imobimotoid" name="imobimotoid">
						<option value="">Escolha</option>
						<?php if($comboTipo) : ?>
							<?php foreach($comboTipo as $indice => $registro) : ?>
								<option value="<?php echo $registro->imotoid; ?>"><?php echo $registro->imotdescricao; ?></option>
							<?php endforeach; ?>
						<?php endif; ?>
					</select>
				</div>
				<div class="campo medio">
					<label for="imobprdoid">Produto</label>
					<select id="imobprdoid" name="imobprdoid" class="carregando">
						<option value="">Escolha</option>
						<?php if($comboProduto) : ?>
							<?php foreach($comboProduto as $indice => $registro) : ?>
								<option value="<?php echo $registro->prdoid; ?>"><?php echo $registro->prdproduto; ?></option>
							<?php endforeach; ?>
						<?php endif; ?>
					</select>
					<img id="img-imobprdoid-loader" src="modulos/web/images/ajax-loader-circle.gif" class="carregando" style="display: none;" />
				</div>
				<div class="clear"></div>
				<div class="campo menor">
					<label for="veiplaca">Placa</label>
					<input id="veiplaca" type="text" name="veiplaca" maxlength="7" value="" class="campo" />
				</div>
				<div class="campo menor">
					<label for="ostoid">Tipo O.S.</label>
					<select id="ostoid" name="ostoid">
						<option value="">Todos</option>
						<option value="4">Assistência</option>
						<option value="3">Retirada</option>
					</select>
				</div>
				<div class="campo medio">
					<label for="ositosdfoid_alegado">Defeito Alegado</label>
					<select id="ositosdfoid_alegado" name="ositosdfoid_alegado">
						<option value="">Escolha</option>
						<?php if($comboDefeitoAlegado) : ?>
							<?php foreach($comboDefeitoAlegado as $indice => $registro) : ?>
								<option value="<?php echo $registro->otdoid; ?>"><?php echo $registro->otddescricao; ?></option>
							<?php endforeach; ?>
						<?php endif; ?>
					</select>
				</div>
				<div class="campo medio">
					<label for="ositosdfoid_analisado">Defeito Constatado</label>
					<select id="ositosdfoid_analisado" name="ositosdfoid_analisado">
						<option value="">Escolha</option>
						<?php if($comboDefeitoConstatado) : ?>
							<?php foreach($comboDefeitoConstatado as $indice => $registro) : ?>
								<option value="<?php echo $registro->otdoid; ?>"><?php echo $registro->otddescricao; ?></option>
							<?php endforeach; ?>
						<?php endif; ?>
					</select>
				</div>
				<div class="clear"></div>
				<div class="campo data">
					<label for="orddt_ordem_ini">Data Abertura O.S.</label>
					<input id="orddt_ordem_ini" type="text" name="orddt_ordem_ini" maxlength="10" value="" class="campo" />
				</div>
				<p class="campo label-periodo">a</p>
				<div class="campo data">
					<label for="orddt_ordem_fim">&nbsp;</label>
					<input id="orddt_ordem_fim" type="text" name="orddt_ordem_fim" maxlength="10" value="" class="campo" />
				</div>
				<div class="campo data">
					<label for="cmidata_ini">Data Conclusão O.S.</label>
					<input id="cmidata_ini" type="text" name="cmidata_ini" maxlength="10" value="" class="campo" />
				</div>
				<p class="campo label-periodo">a</p>
				<div class="campo data">
					<label for="cmidata_fim">&nbsp;</label>
					<input id="cmidata_fim" type="text" name="cmidata_fim" maxlength="10" value="" class="campo" />
				</div>
				<div class="clear"></div>
				<div class="campo menor">
					<label for="entnota">Nota Fiscal</label>
					<input id="entnota" type="text" name="entnota" maxlength="9" value="" class="campo" />
				</div>
				<div class="campo menor">
					<label for="entserie">Série</label>
					<input id="entserie" type="text" name="entserie" maxlength="10" value="" class="campo" />
				</div>
				<div class="campo menor">
					<label for="conmodalidade">Modalidade</label>
					<select id="conmodalidade" name="conmodalidade">
						<option value="">Escolha</option>
						<option value="L">Locação</option>
						<option value="V">Revenda</option>
					</select>
				</div>
				<div class="campo menor">
					<label for="imobh">1&ordm; Instalação?</label>
					<select id="imobh" name="imobh">
						<option value="">Escolha</option>
						<option value="S">Sim</option>
						<option value="N">Não</option>
					</select>
				</div>
				<div class="clear"></div>
				<div class="campo data">
					<label for="imobentrada_ini">Data Entrada NF</label>
					<input id="imobentrada_ini" type="text" name="imobentrada_ini" maxlength="10" value="" class="campo" />
				</div>
				<p class="campo label-periodo">a</p>
				<div class="campo data">
					<label for="imobentrada_fim">&nbsp;</label>
					<input id="imobentrada_fim" type="text" name="imobentrada_fim" maxlength="10" value="" class="campo" />
				</div>
				<div class="campo data">
					<label for="imobemissao_ini">Data Emissão NF</label>
					<input id="imobemissao_ini" type="text" name="imobemissao_ini" maxlength="10" value="" class="campo" />
				</div>
				<p class="campo label-periodo">a</p>
				<div class="campo data">
					<label for="imobemissao_fim">&nbsp;</label>
					<input id="imobemissao_fim" type="text" name="imobemissao_fim" maxlength="10" value="" class="campo" />
				</div>
				<div class="clear"></div>
				<div class="campo medio">
					<label for="cfaifdoid">Defeito Lab.</label>
					<select id="cfaifdoid" name="cfaifdoid" class="carregando">
						<option value="">Escolha</option>
					</select>
					<img id="img-cfaifdoid-loader" src="modulos/web/images/ajax-loader-circle.gif" class="carregando" style="display: none;" />
				</div>
				<div class="campo medio">
					<label for="cfaifaoid">Ação Lab.</label>
					<select id="cfaifaoid" name="cfaifaoid" class="carregando">
						<option value="">Escolha</option>
					</select>
					<img id="img-cfaifaoid-loader" src="modulos/web/images/ajax-loader-circle.gif" class="carregando" style="display: none;" />
				</div>
				<div class="campo medio">
					<label for="cfaifcoid">Componente Afetado Lab.</label>
					<select id="cfaifcoid" name="cfaifcoid" class="carregando">
						<option value="">Escolha</option>
					</select>
					<img id="img-cfaifcoid-loader" src="modulos/web/images/ajax-loader-circle.gif" class="carregando" style="display: none;" />
				</div>
				<div class="clear"></div>
				<div class="campo data">
					<label for="lahdt_entrada_ini">Data Entrada Lab. *</label>
					<input id="lahdt_entrada_ini" type="text" name="lahdt_entrada_ini" maxlength="10" value="" class="campo" />
				</div>
				<p class="campo label-periodo">a</p>
				<div class="campo data">
					<label for="lahdt_entrada_fim">&nbsp;</label>
					<input id="lahdt_entrada_fim" type="text" name="lahdt_entrada_fim" maxlength="10" value="" class="campo" />
				</div>
				<div class="campo data">
					<label for="lahdt_saida_ini">Data Saída Lab.</label>
					<input id="lahdt_saida_ini" type="text" name="lahdt_saida_ini" maxlength="10" value="" class="campo" />
				</div>
				<p class="campo label-periodo">a</p>
				<div class="campo data">
					<label for="lahdt_saida_fim">&nbsp;</label>
					<input id="lahdt_saida_fim" type="text" name="lahdt_saida_fim" maxlength="10" value="" class="campo" />
				</div>
				<div class="clear"></div>
			</div>
		</div>
		<div class="bloco_acoes">
			<button type="submit" id="button-gerar-arquivo">Arquivo de Exportação</button>
		</div>
		<div class="separador" style="display: none;"></div>
		<div class="carregando" style="display: none;"></div>
		<div id="div-mensagem-resultado" class="mensagem alerta" style="display: none; margin-bottom: 0px;">
			Nenhum registro encontrado.
		</div>
		<div class="resultado bloco_titulo" style="display: none;">Download do arquivo</div>
		<div class="resultado bloco_conteudo" style="display: none;">
			<div class="conteudo centro">
				<a href="downloads.php?arquivo=docs_temporario/rel_analise_controle_falhas_acessorio.csv">
					<img src="images/icones/t3/caixa2.jpg" alt="Download" style="margin-bottom: 5px;" /><br />
					Download do arquivo CSV
				</a>
			</div>
		</div>
	</div>
</form>