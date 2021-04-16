<div class="bloco_titulo">Importar</div>

<form action="" name="form" id="form" method="post" enctype="multipart/form-data" onsubmit="carregando();">
    <input type="hidden" name="acao" id="acao" value="importar" />
	<div class="bloco_conteudo">
		<div class="conteudo">			
			<fieldset>
				<legend>Importar</legend>
				
				<div class="campo pesquisaMaior">
					<label for="fornecedor_busca">Fornecedor </label><br />
					<input type="text" id="fornecedor_busca" name="fornecedor_busca" value="<?=$this->fornecedor_busca?>" class="campo obrigatorio" style="width:287px" onblur="jQuery(this).val(jQuery.trim(jQuery(this).val()));"/>
				    <input type="hidden" name="foroid" id="foroid" value="<?=$this->foroid?>" />
					<button type="button" id="buttonPesquisarFornecedor" name="buttonPesquisarFornecedor">Pesquisar</button>
					<div class="carregando" id="carregando_fornecedor" style="display: none;"></div>
				</div>
				
				<div class="clear" ></div>
			
				<div class="campo maior" style="display: none">
				 	<label for="fornecedoresEncontrados">Fornecedores Encontrados </label>
				 	<select id="fornecedoresEncontrados" multiple="multiple"></select>
				</div>
				
				<div class="clear" ></div>				
				<div class="campo menor">
					<label for="nfloid">Nota Fiscal </label>
					<input type="text" id="nfloid" name="nfloid" class="campo obrigatorio numerico" />
				</div>
				
				<div class="clear" ></div>				
				<div class="campo maior">
					<label for="versao">Versão </label>
					<select id="versao" name="versao"class="obrigatorio">
						<option value="">Selecione</option>
						<?php foreach($this->versao as $versao){?>
							<option value="<?php echo $versao['eveoid']?>"><?php echo $versao['eveversao']?></option>
						<?php }?>
					</select>
				</div>
				
				<div class="clear"></div>
				<div class="campo medio">
					<label for="importFile">Arquivo </label>
					<input type="file" id="importFile" name="importFile" class="obrigatorio" />
				</div>
				
				<div class="clear"></div>
				<div class="campo grande">
					<br />
					<strong>
					<label style="color:#ff0000" class="blinking">ATENÇÃO! Certifique-se de que o arquivo atenda as seguintes regras:</label>
					<br /><br />
					<label>
					A primeira linha do arquivo deve ser o cabeçalho; <br />
					Não tenha nenhuma linha em branco no início e no final do arquivo;<br />
					Não tenha nenhum espaço em branco entre as informações, apenas a quebra de linha.
					</label>
					</strong>
					<br /><br />
					<label>
					Ex:<br />
					Modelo do Equipamento;Número de Série;CCID;IMEI;RF<br />
					RASTREADOR CT MODELO S;5952115498749;89550021365999885210;5022115549;318
					</label>
				</div>
				
				<div class="clear" ></div>
			</fieldset>	
	    </div>
	</div>
	
	<div class="bloco_acoes">
		<button type="button" value="importar" id="btImportar" name="btImportar" onclick="return true;" class="validacao">Importar</button>
		<div class="carregando" id="carregando_importacao" style="display: none;"></div>
	</div>
	
</form>
<div class="separador"></div>