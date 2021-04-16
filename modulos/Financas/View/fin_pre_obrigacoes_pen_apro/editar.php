<?php require_once _MODULEDIR_ . "Financas/View/fin_pre_obrigacoes_pen_apro/cabecalho.php";
	$statusAtual = $recuperarDados[0]['status'];

	$codStatusNovo = 0;
	$descStatusNovo = '';
	$disabledStatus = '';
	if(in_array((int)$statusAtual, array(9, 10, 11))) {
		$disabledStatus = 'disabled';
	}

	switch($statusAtual) {
        case 9:
            $codStatusNovo = 6;
            $descStatusNovo = 'Em Cancelamento';
            break;
        case 10:
            $codStatusNovo = 5;
            $descStatusNovo = 'Depreciado';
            break;
        case 11:
            $codStatusNovo = 8;
            $descStatusNovo = 'Em Reativação';
            break;
        default:
            $codStatusNovo = 0;
            $descStatusNovo = 'Selecione';
            break;
    }
 ?>
<form id="form"  method="post" action="">
<input type="hidden" id="acaoEditar" name="acaoEditar" value="<?php
																	if(in_array((int)$statusAtual, array(9, 10, 11))) {
																		echo 'alterarStatus';
																	} else {
																		echo 'editarPrevisao';
																	} ?>"/>
<div class="modulo_titulo">Aprovação e Criação da Obrigação Financeira</div>
 <div class="bloco_conteudo">
	<div class="formulario">
		<!-- Mensagens-->

    <div id="mensagem_info1" class="mensagem info">
        Os campos com * são obrigatórios.
    </div>
    
	<div id="mensagem_previsao_iniciada" class="mensagem info <?php if (empty($this->view->mensagemPrevisao)): ?>invisivel<?php endif; ?>">
	    <?php echo $this->view->mensagemPrevisao; ?>
	</div>
	
    <div id="mensagem_erro" class="mensagem erro <?php if (empty($this->view->mensagemErro)): ?>invisivel<?php endif;?>">
        <?php echo $this->view->mensagemErro; ?>
    </div>
	
    <div id="mensagem_alerta" class="mensagem alerta <?php if (empty($this->view->mensagemAlerta)): ?>invisivel<?php endif;?>">
        <?php echo $this->view->mensagemAlerta; ?>
    </div>
	
    <div id="mensagem_sucesso" class="mensagem sucesso <?php if (empty($this->view->mensagemSucesso)): ?>invisivel<?php endif;?>">
        <?php echo $this->view->mensagemSucesso; ?>
    </div>
    
		  <div class="modulo_titulo"><?php print $var;?></div>
			<div class="bloco_conteudo">
		      <div class="formulario">
		      	<?php
		      	if(in_array((int)$statusAtual, array(9, 10, 11))) {
		      	?>
			      	<div class="campo maior">
			      		<label for="cbb_status">Status:</label>
			      		<input type="hidden" name="cbb_status" id="cbb_status" value="<?php echo $codStatusNovo; ?>"/>
			      		<select name="cbb_status" id="cbb_status" disabled>
			      			<option value="<?php echo $codStatusNovo; ?>"><?php echo $descStatusNovo; ?></option>
			      		</select>
			      		
			      	</div>
			      	<label for="cbb_status">&nbsp;</label>
			      	<input type="button" name="alterarStatus" id="alterarStatus" value="Alterar Status"/>
			      	<div class="clear"></div>
		      	<?php
		      	}
		      	?>
				<div class="campo maior">
					<label for="texto">Descrição:</label>
					<input id="descr" name="descr" value="<?php print $recuperarDados[0]['descricao'];?>" <?php if ($recuperarDados[0]['descricao']) print 'disabled="disabled"'; ?> class="campo" type="text">
				    <input type="hidden" name="descr" value="<?php print $recuperarDados[0]['descricao'];?>"/>
				</div>
				<div class="clear"></div>
				<?php if($var == "Funcionalidade"){?>
				<div class="campo maior">
					<label for="texto">Tag Funcionalidade:</label>
					<input id="rasttag_pacote" name="rasttag_pacote" value="<?php print $recuperarDados[0]['tag'];?>" <?php if ($recuperarDados[0]['tag']) print 'disabled="disabled"'; ?> class="campo" type="text">
				    <input type="hidden" name="rasttag_pacote" value="<?php print $recuperarDados[0]['tag'];?>"/>
				</div>
				<div class="clear"></div>
				<?php }?>
				<div class="campo maior">
					<label for="texto">Tag Pacote:</label>
					<input id="tagPacote" name="tagPacote" value="<?php print $vl = $var == "Pacote"? $recuperarDados[0]['tag']:$recuperarDados[0]['rasttag_pacote'];?>" <?php if ($vl = $var == "Pacote"? $recuperarDados[0]['tag']:$recuperarDados[0]['rasttag_pacote']) print 'disabled="disabled"'; ?> class="campo" type="text">
				    <input type="hidden" name="tagPacote" value="<?php print $vl = $var == "Pacote"? $recuperarDados[0]['tag']:$recuperarDados[0]['rasttag_pacote'];?>"/>
				</div>
				<div class="clear"></div>
				<div class="campo maior">
					<label>
						<input type="hidden" name="obrigacao_unica" id="obrigacao_unica" value="<?php echo $recuperarDados[0]['rastobrigacao_unica_cliente']; ?>"/>
						<input type="checkbox" value="obrUnica" disabled <?php if($recuperarDados[0]['rastobrigacao_unica_cliente'] === 't') { echo 'checked'; } ?>/>
						Obrigação Única por Cliente
					</label>
				</div>
				<div class="clear"></div>
				<div class="campo maior">
					<label for="combo">Classificação*:</label>
					<select id="classificacao" name="classificacao" <?php echo $disabledStatus ?>>
						<option value="">Escolha</option>
						<option value="H" <?php  print $value = $this->view->parametros->classificacao == "H"?'selected="selected"':""; ?>>Hardware</option>
						<option value="S" <?php  print $value = $this->view->parametros->classificacao == "S"?'selected="selected"':""; ?>>Software</option>
						<option value="A" <?php  print $value = $this->view->parametros->classificacao == "A"?'selected="selected"':""; ?>>Assistência</option>
						<option value="V" <?php  print $value = (empty($this->view->parametros->classificacao) && $var == "Funcionalidade") || $this->view->parametros->classificacao == "V"?'selected="selected"':""; ?>>Serviço</option>
						<option value="P" <?php  print $value = (empty($this->view->parametros->classificacao) && $var == "Pacote") ||  $this->view->parametros->classificacao == "P"? 'selected="selected"':"";?>>Pacote</option>
						<option value="T" <?php  print $value = $this->view->parametros->classificacao == "T"?'selected="selected"':""; ?>>Taxa</option>
					</select>
				</div>
				<div class="clear"></div>
				<div class="campo maior">
					<label for="combo">Tipo*:</label>
					<select id="tipo" name="tipo" <?php echo $disabledStatus ?>>
						<option value="">Escolha</option>
						<?php if (!empty($dadosTipo)){							  
								foreach ($dadosTipo as $value) { 
									$selected ="";
                                   if((empty($this->view->parametros->classificacao) && $value['oftdescricao'] =="Serviço") || $this->view->parametros->tipo == $value['oftoid']){
										$selected = 'selected="selected"';
									}
									?>
							    <option value="<?php print $value['oftoid'];?>" <?=$selected?>><?php print $value['oftdescricao'];?></option>
						<?php } }?>
					</select>
				</div>
				<div class="clear"></div>
				
				<div class="campo maior">
					<label for="texto">Obrigação Financeira*:</label>
					<input id="obrigFinanceira" name="obrigFinanceira" value="<?php
						if($this->view->parametros->obrigFinanceira == "" && $statusAtual == 2) {
							echo $recuperarDados[0]['descricao'];
						} else {
							echo $this->view->parametros->obrigFinanceira;
						} ?>" 
						class="campo" type="text" <?php echo $disabledStatus ?>>
				</div>
				<div class="clear"></div>
				
				<div class="campo maior">
					<label for="combo">Codigo do Serviço*:</label>
					<select id="codServico" name="codServico" <?php echo $disabledStatus ?>>
						<option value="">Escolha</option>
						<?php if (!empty($codigoServico)){
								foreach ($codigoServico as $value) { ?>
							    <option <?php if ($this->view->parametros->codServico == $value['aliseroid']) echo 'selected="selected"'; ?> value="<?php print $value['aliseroid'];?>"><?php print $value['descricao'];?></option>
						<?php } }?>
					</select>
				</div>
				<div class="clear"></div>
				
				 <div class="modulo_titulo">Dados para Faturamento</div>
					<div class="bloco_conteudo">
				      <div class="formulario">	
				      
				        <div class="campo maior">
							<label for="texto">Grupo de Faturamento*:</label>
							<select id="grupoFaturamento" name="grupoFaturamento" <?php echo $disabledStatus ?>>
								<option value="">Escolha</option>
								<?php if (!empty($grupoFaturamento)){
										foreach ($grupoFaturamento as $value) { 
											$selected ="";
											if((empty($this->view->parametros->grupoFaturamento) && $value['ofgdescricao'] =="Faturamento Mensal de Serviço") || $this->view->parametros->grupoFaturamento == $value['ofgoid']){
												$selected = 'selected="selected"';
											}?>
							    <option value="<?php print $value['ofgoid'];?>" <?=$selected?>><?php print $value['ofgdescricao'];?></option>
								<?php } }?>
						    </select>
						</div>
						<div class="clear"></div>
				      
				      	<div class="campo maior">
							<label for="texto">Pró-Rata:</label>
							<select id="proRata" name="proRata" style="width:82% !important" <?php echo $disabledStatus ?>>
									<option value="">Escolha</option>
								<?php if (!empty($proRata)){
									foreach ($proRata as $value) { ?>
								    <option <?php if ($this->view->parametros->proRata == $value['obroid']) echo 'selected="selected"'; ?> value="<?php print $value['obroid'];?>"><?php print $value['obrobrigacao'];?></option>
							   <?php } }?>
							</select>
						</div>
						<div class="clear"></div>    		
			            <div class="campo maior">
							<label for="texto">Valor*:</label>
							<input id="valor" name="valor" value="<?php print $vl = $this->view->parametros->valor !="" ? $this->view->parametros->valor:""; ?>"  class="campo" type="text" style="width:32% !important" <?php echo $disabledStatus ?>>
						</div>
		            	<div class="clear"></div>	       	
				    </div>
	             </div>
				 <div class="clear"></div>
			    </div>
	        </div>
	        <div class="bloco_acoes">
	        <?php
	        if(!in_array((int)$statusAtual, array(9, 10, 11))) {
	        ?>
	         <button type="button" id="bt_gerarRevisao">Confirmar</button> 
	        <?php
	    	}
	    	?>
	         <button type="button" id="voltar">Voltar</button></div>
		  <div class="clear"></div>
		  	
	 </div>
	</div>
</form>
 <?php if (count($this->view->dados) > 0) : ?>
		<!--  Caso contenha erros, exibe os campos destacados  -->
		<script type="text/javascript" >jQuery(document).ready(function() {
			showFormErros(<?php echo json_encode($this->view->dados); ?>);
		});
		</script>
    <?php endif; ?>
<?php require_once _MODULEDIR_ . "Financas/View/fin_pre_obrigacoes_pen_apro/rodape.php"; ?> 