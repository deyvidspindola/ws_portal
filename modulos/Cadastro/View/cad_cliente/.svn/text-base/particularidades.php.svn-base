<div class="bloco_titulo">Particularidades</div>

<form action="" name="cad_cliente_particularidades" id="cad_cliente_particularidades" method="post">
	
	<input type="hidden" name="acao" id="acao" value="cadastroParticularidadesPerfil" />
	<input type="hidden" name="clioid" id="clioid" value="<?php echo $this->clioid; ?>" />
	
	<div class="bloco_conteudo">
		<div class="conteudo">
			
			<div class="campo medio">
				<label for="tipo">Tipo *: </label>
				<select id="tipo" name="tipo" class="campo obrigatorio">
					<option value="">Selecione</option>
					
					<?php foreach($this->particularidadesTipo as $idTipo=>$tipo):?>
					
						<option value="<?php echo $idTipo;?>"><?php echo $tipo;?></option>
						
					<?php endforeach;?>
					
				</select>
			 </div>
			 
			<div class="clear" ></div>
			
			<div class="campo maior">
				<label for="cliparticularidade">Particularidade *: </label>
				<textarea id="cliparticularidade" name="cliparticularidade" rows="5" class="obrigatorio"></textarea>
			</div>
			
			<div class="clear" ></div>
			
			<div class="campo">
				<fieldset>
					<legend>Apenas para os Contratos:</legend>				
					<table>
					<?php if(count($this->particularidadesContratos)>0):?>
						
						<?php 
							$countTd    	  = 1; // contador para quebrar linha a cada 6 contratos						
							$countTotal 	  = 1; // contador total para comparar no final
							$countTotalLimite = count($this->particularidadesContratos); 
							foreach($this->particularidadesContratos as $contrato): ?>
							<?php if($countTd == 1): //abre tr quando o contador for 1 ?>
								<tr>  
							<?php endif; ?>
									<td>
										<input type="checkbox" name="con_numero[]" value="<?php echo $contrato['connumero']?>" id="<?php echo $contrato['connumero']?>" />
										<label for="<?php echo $contrato['connumero']?>"><?php echo $contrato['connumero']?></label>
									</td>
							<?php // comparação para fechar tr ao final de 6 td's ou ao final do loop
								if($countTd == 6 
											|| $countTotal == $countTotalLimite):
									$countTd = 1; // volta contador de td para o começo
								?>
								</tr>
							<?php 
								else:
									$countTd++; // incrementa contador de td caso não tenha chego ao 6								
								endif; 
								$countTotal++; // incrementa o contador total a cada iteração do loop ?>
						<?php endforeach;?>	
					<?php else:?>
						<center>Todos</center>
					<?php endif;?> 
					</table>
				</fieldset>
			</div>
			
			<div class="clear" ></div>
		</div>
	</div>

	<div class="bloco_acoes">
		<button type="submit" value="Confirmar" id="buttonConfirmarParticularidades" name="buttonConfirmarParticularidades" class="validacao">Confirmar</button>
		<button type="button" value="Voltar" id="buttonVoltar" name="buttonVoltar" onclick="window.location.href='<?php echo str_replace('/', '', strrchr($_SERVER['SCRIPT_NAME'], '/'));?>'">Voltar</button>
	    <? if($this->retCliente!=""){ ?>
    		<button type="button" value="Voltar" id="buttonVoltar" name="buttonVoltar" onclick="window.location.href='<?=trata_retorno($this->retCliente,$clioid)?>'">Retornar ao Contrato</button>
        <? } ?>
	</div>
</form>

<div class="separador"></div>

<form action="" method="post" id="excluirParticularidadePerfil">
    <input type="hidden" name="acao" value="excluirParticularidadePerfil">
    <input type="hidden" name="clipfoid" id="clipfoid">
    <input type="hidden" name="clioid" id="clioid" value="<?php echo $this->clioid; ?>" />
    
	<div class="resultado_pesquisa">
		<div class="bloco_titulo">Particularidades Cadastradas</div>
		<div class="bloco_conteudo">
		    <div class="listagem">
		        <table>
		            <thead>
		                <tr>
		                    <th>Tipo</th>
		                    <th>Particularidades</th>
		                    <th>Contratos</th>
		                    <th>Excluir</th>
		                </tr>
		            </thead>
		            <tbody>
		            	<?php if(count($this->particularidadesPerfil) > 0) :?>
		            	
			            	<?php foreach($this->particularidadesPerfil as $perfil): ?>
			            	
				                <tr <?php if ($cor != '') { ?> class="<?=$cor?>" <?php } ?>> 
				                    <td><?php echo $perfil['tipo']?></td>
				                    <td><?php echo $perfil['clipfdescricao']?></td>
				                    <td><?php echo $perfil['connumero']?></td>
				                    <td align="center" class="td_acao_excluir">
				                        <a href="javascript:void(0);" class="excluirParticularidade"  clipfoid="<?php echo implode(",",$perfil['clipfoids'])?>"><img src="images/icon_error.png" /></a>
				                    </td>
				                </tr>
			                
			                <?php 
			                 $cor = ($cor=="par") ? "" : "par";
			                 endforeach; ?>
			                
			            <?php else : ?>
			            
			            	<tr>
			            		<td colspan="3">Nenhuma particularidade cadastrada</td>
			            	</tr>
			            	
		                <?php endif; ?>
		            </tbody>
		        </table>
		    </div>
		</div>
		<div class="bloco_acoes">
			<p><?php echo $this->getMensagemTotalRegistros(count($this->particularidadesPerfil));?></p>
		</div>
	</div>
</form>
