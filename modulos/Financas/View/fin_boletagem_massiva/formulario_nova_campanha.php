<?php
 require_once '_header.php';
$finBoletagemMassiva = new FinBoletagemMassiva();

?>

	<div class="modulo_titulo">Boletagem Massiva</div>
	
	<div class="modulo_conteudo">
	
		<div class="mensagem info">(*) Campos de preenchimento obrigatório.</div>
		
		<div id="mensagem"   
		
			<?php if($msg == ''){?> 
			         style='display:none'
			<?php }else{ 
				    echo "class='$classe' ";
			      } ?>>
			
			<?php if($msg != '') {echo $msg;}?>
		
		</div>
		
		<div class="bloco_titulo">Cadastro de Campanha</div>
		<div class="bloco_conteudo">
		
			<div class="formulario">
			
			<form name="frm_cadastro" id="frm_cadastro" method="POST" action="">

	           <input type="hidden" name="acao" id="acao" value="" />
			
				<div class="campo maior">
					<label for="cad_nome_campanha">Nome da Campanha *</label> 
					<input id="cad_nome_campanha" name="cad_nome_campanha"  maxlength="62" value="" class="campo" type="text"/>
				</div>
				
				<div class="clear"></div>
				
				<div class="campo medio">
					<label for="aging_divida">Aging da Dívida *</label> 
					<select id="aging_divida" name="aging_divida">
					   <option value="">Escolha</option>
					   <?php foreach ($finBoletagemMassiva->getAgingPolitica() AS $aging):  ?>
						 <option value="<?php echo $aging['podoid']; ?>"><?php echo $aging['poddescricao_atraso'];?></option>
                       <?php endforeach;?>
					</select>
				</div>
				
				<div class="clear"></div>
				
				<div class="campo data">
					<label for="cad_vencimento">Vencimento *</label> 
					<input type="text" id="cad_vencimento" name="cad_vencimento" class="campo" value="" readOnly="readonly"/>
				</div>
				
				<div class="clear"></div>
				
				<div class="campo medio">
					<label for="formato_envio">Formato de Envio *</label> 
					<select id="formato_envio" name="formato_envio">
						<option value="">Escolha</option>
						<option value="G">Arquivo para gráfica</option>
						<option value="E">E-mail</option>
					</select>
				</div>
				
				<div class="clear"></div>
				
				<div class="">
					<label for="valor_divida_ini">Valor da Dívida Unificada</label> 
					<input id="valor_divida_ini" name="valor_divida_ini" value="" maxlength="12" class="campo valorZerado valor numerico" type="text">
				
				<div id='a'>&ensp;&ensp;&ensp;Até&ensp;&ensp;</div>
			
					<input id="valor_divida_fim" name="valor_divida_fim" value="" maxlength="12" class="campo valorZerado valor numerico" type="text">
				</div>
				
				
				<div class="clear"></div>
				
				<div class="campo medio">
					<label for="tipo_pessoa">Tipo de Pessoa</label> 
					<select id="tipo_pessoa" name="tipo_pessoa">
						<option value="">Escolha</option>
						<option value="F">Física</option>
						<option value="J">Jurídica</option>
					</select>
				</div>
				<div class="clear"></div>
				
				<div class="campo medio">
					<label for="tipo_cliente">Tipo de Cliente</label> 
					<select id="tipo_cliente" name="tipo_cliente" >
						<option value="">Escolha</option>
						<option value="siggo">SIGGO</option>
						<option value="sascar">SASCAR</option>
					</select>
				</div>
				
				<div class="clear"></div>
				
				
				<div class="campo medio">
					<label for="uf_cliente">UF do Cliente</label> 
					<select id="uf_cliente" name="uf_cliente[]" multiple="multiple">
						<?php foreach ($finBoletagemMassiva->getUf() AS $uf):?>
						
                            <option value="<?php echo $uf['uf']; ?>"><?php echo $uf['estado'];?></option>
                        
                        <?php endforeach;?>
                  
					</select>
				</div>
				
				<div class="clear"></div>
				
				<div class="campo menor">
					<label for="cod_cliente">Código do Cliente</label> 
					<select id="cod_cliente" name="cod_cliente[]" multiple="multiple">
						<option value="0">0</option>
						<option value="1">1</option>
						<option value="2">2</option>
						<option value="3">3</option>
						<option value="4">4</option>
						<option value="5">5</option>
						<option value="6">6</option>
						<option value="7">7</option>
						<option value="8">8</option>
						<option value="9">9</option>
					</select>
				</div>
				<div class="clear"></div>
			
           </form>	
				
			</div>
		</div>
		<div class="bloco_acoes">
			<button type="button" id='gerar_campanha'>Gerar Campanha</button>
			<button type="button" id='limpar_cadastro'>Limpar</button>
			<button type="button" id='voltar'>Voltar</button>
		</div>
	</div>
