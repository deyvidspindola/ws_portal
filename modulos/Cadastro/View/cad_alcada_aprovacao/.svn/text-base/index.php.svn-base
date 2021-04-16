<div class="modulo_titulo">Consulta Alçada de Aprovação</div>
    <div class="modulo_conteudo">        
        <div id="mensagem" class="mensagem <?php echo ($this->retorno != '') ? $this->retorno['status'] : '' ?>"><?php echo ($this->retorno != '') ? $this->retorno['mensagem'] : '' ?></div>

<div class="bloco_titulo">Consultar</div>

<form action="" name="pesquisa_alcada_compra" id="pesquisa_alcada_compra" method="post">
    <input type="hidden" name="acao" id="acao" value="pesquisar" />
	<div class="bloco_conteudo">
		<div class="conteudo">			
			<div class="campo medio">
				<label for="alcousuoid">Usuário Aprovador </label>			
				<select id="alcousuoid" name="alcousuoid" class="alcousuoid">
					<option value="">Selecione</option>
					<?php foreach($this->usuarioAprovador as $usuario):?>
					<option value="<?php echo $usuario['cd_usuario']?>" <?php if($this->filters['alcousuoid'] == $usuario['cd_usuario']) echo "selected"?>><?php echo $usuario['nm_usuario']?></option>
					<?php endforeach;?>
				</select>				
			</div>

			<div class="campo medio">
				<br/>
				<input type="checkbox" name="alcodupla_check" id="alcodupla_check" style="vertical-align: middle" <?php if(isset($this->filters['alcodupla_check'])) echo "checked";?> />
				<label for="alcodupla_check" style="display: inline">Checagem dupla </label>
			</div>

			<div class="campo medio">
				<br/>
				<input type="checkbox" name="alcodt_exclusao" id="alcodt_exclusao" style="vertical-align: middle" <?php if(isset($this->filters['alcodt_exclusao'])) echo "checked";?> />
				<label for="usuario_aprovador" style="display: inline">Registros excluídos </label>
			</div>

			<div class="clear" ></div>

			<div class="campo medio">
				<label for="alcovlr_inicio_pesq">Valor inicial da aprovação</label>		
				R$&nbsp;<input type="text" class="valorZerado numerico" name="alcovlr_inicio_pesq" id="alcovlr_inicio_pesq" value="<?php echo $this->filters['alcovlr_inicio_pesq'];?>" maxlength="13"/> 			
			</div>

			<div class="campo medio">
				<label for="alcovlr_fim_pesq">Valor final da aprovação</label>			
				R$&nbsp;<input type="text" class="valor numerico" name="alcovlr_fim_pesq" id="alcovlr_fim_pesq" value="<?php echo $this->filters['alcovlr_fim_pesq'];?>"maxlength="13"/>
			</div>
		
			<div class="clear" ></div>

			<div class="campo data">
				<label for="data_inicial">Data inicial </label>				
				<input type="text" id="data_inicial" name="data_inicial" value="<?php echo $this->filters['data_inicial'];?>" class="campo obrigatorio " >
			</div>
		
			<div class="campo data">
				<label for="data_final">Data final </label>				
				<input type="text" id="data_final" name="data_final" value="<?php echo $this->filters['data_final'];?>" class="campo obrigatorio " >
			</div>

			<div class="clear" ></div>
		</div>
	</div>
		
	<div class="bloco_acoes">
		<button type="button" value="pesquisar" id="buttonPesquisar" name="buttonPesquisar">Pesquisar</button>
		<button type="button" value="cadastrar" id="buttonNovo" name="buttonNovo">Inserir</button>
		<button type="button" value="limpar" id="buttonLimpar" name="buttonLimpar">Limpar</button>
	</div>
</form>
<div class="separador"></div>
<?php 
	if($this->acao == 'pesquisar'):
		$numResultado = count($this->dados);
?>

<div class="resultado_pesquisa">
	<div class="bloco_titulo">Resultado da Consulta</div>
	<?php if($numResultado > 0) {?>
	<div class="bloco_conteudo">
	    <div class="listagem">
	    	<form action="" method="post" name="excluirAlcada" id="excluirAlcada">
	    		<input type="hidden" name="acao" id="acao" value="excluirAlcada" />
	    		<input type="hidden" name="alcoid" id="alcoid" value="" />
		        <table>
		            <thead>
		            	<tr>	                	
		                    <th width="">Aprovador</th>
		                    <th width="">Vlr Inicial</th>
		                    <th width="">Vlr Fim</th>
		                    <th width="">Dupla Checagem</th>
		                    <th width="">Segundo Aprovador</th>
		                    <th width="">Vlr. Ini. Seg</th>
		                    <th width="">Vlr. Fim Seg.</th>
		                    <th width="">Data Cadastro</th>
		                    <th width="">Data Exclusão</th>
		                    <th width="">Usuário Exclusão</th>
		                    <th width="">Ações</th>
		                </tr>
		            </thead>
		            <tbody>
		            	<?php
		            	if($this->dados) :
		            		$cor = 'par';
		            		foreach($this->dados as $dado) :
		            			$cor = ($cor=="par") ? "" : "par";?>
			            	<tr class="<?php echo $cor?>">	                	
			                    <td>
			                    	<?php if($dado['alcodt_exclusao'] == "" && $dado['alcousuoid_exclusao'] == ''): ?>
			                    	<a href="?acao=editar&alcoid=<?php echo $dado['alcoid']?>"><?php echo $dado['alcousuoid']?></a>
			                    	<?php else:?>
			                    	<?php echo $dado['alcousuoid']?>
			                    	<?php endif;?>
			                    </td>
			                    <td>R$ <?php echo number_format($dado['alcovlr_inicio'],2,',','.')?></td>
			                    <td>R$ <?php echo number_format($dado['alcovlr_fim'],2,',','.')?></td>
			                    <td align="center"><?php echo ($dado['alcodupla_check'] == 'S') ? 'Sim': 'Não' ;?></td>
			                    <td><?php echo $dado['alcousuoid_dupla_check']?></td>
			                    <td><?php if($dado['alcovlr_inicio_dupla_check'] != '') echo "R$ ". number_format($dado['alcovlr_inicio_dupla_check'],2,',','.')?></td>
			                    <td><?php if($dado['alcovlr_fim_dupla_check'] != '') echo "R$ ". number_format($dado['alcovlr_fim_dupla_check'],2,',','.')?></td>
			                    <td align="center"><?php echo date('d/m/Y',strtotime($dado['alcodt_cadastro']))?></td>
			                    <td align="center"><?php echo ($dado['alcodt_exclusao'] != "") ? date('d/m/Y',strtotime($dado['alcodt_exclusao'])) : '';?></td>
			                    <td><?php echo $dado['alcousuoid_exclusao']?></td>
			                    <td class="menor centro">
			                    	<?php if($dado['alcodt_exclusao'] == "" && $dado['alcousuoid_exclusao'] == ''): ?>
			                    	<a href="javascript:return false;" class="excluirAlcada"  alcoid="<?php echo $dado['alcoid']?>" >
			                    		<img src="images/icon_error.png" />
			                    	</a>
			                    	<?php endif;?>
			                    </td>
			                </tr>
		            <?php endforeach; 
		            endif; ?>
		            </tbody>
		            <tfoot>
		                <tr>
		                    <td colspan="11">
		                        <?php 
		                            if($numResultado == 1){
	                                    echo '1 registro encontrado.';
	                                }else{
	                                    echo $numResultado.' registros encontrados.';    
	                                }
		                        ?>
		                    </td>
		                </tr>
		            </tfoot>
		        </table>
		    </form>
	    </div>
	</div>
    <?php } else {?>
    <div class="bloco_acoes"><p>Nenhum Resultado Encontrado.</p></div>
    <?php } ?>
</div>
<?php endif; ?>