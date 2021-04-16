<script language="Javascript" type="text/javascript">

function pesquisarVeiculo(){
	$("#acao").val('pesquisar');
	$('#cadVeiculoRoubado').submit();
}

function sinalizarRoubo(veioid, veiplaca){
	if (confirm('Você está prestes a sinalizar que este veículo placa ' + veiplaca + '  foi roubado. Está certo disso?')){
		$("#acao").val('sinalizarVeiculoRoubado');
		$("#veioid").val(veioid);
		$('#cadVeiculoRoubado').submit();
	}
}
		
</script>


<link type="text/css" rel="stylesheet" href="includes/css/base_form.css">
<link type="text/css" rel="stylesheet"
	href="includes/css/base_relatorio.css">

<!-- Mensagens Erro -->
<div id="mensagem_erro"
	class="mensagem erro <?php if (empty($this->mensagemErro)): ?>invisivel<?php endif;?>">
    	<?php echo $this->mensagemErro; ?>
    </div>

<!-- Mensagens Sucesso -->
<div id="mensagem_sucesso"
	class="mensagem sucesso <?php if (empty($this->mensagemSucesso)): ?>invisivel<?php endif;?>">
    	<?php echo $this->mensagemSucesso; ?>
    </div>

<div align="center" width="100%">

	<table width="100%" class="tableMoldura">
		<tr class="tableTitulo">
			<td><h1>Cadastro de Veículos Roubados</h1></td>
		</tr>

		<tr class="msg">
			<td>
				<div style="margin: 10px;" id="div_msg"><?=$msg?></div>
			</td>
		</tr>

		<tr>
			<td align="center"><br>
				<form action="" name="cadVeiculoRoubado" id="cadVeiculoRoubado"
					method="post">
					<input type="hidden" name="acao" id="acao" value="" />
					<input type="hidden" name="veioid" id="veioid" value="" />
					<table width="98%" class="tableMoldura">
						<tr class="tableSubTitulo">
							<td><h1>Pesquisa</h1></td>
						</tr>
						<tr>
							<td>
								<table width="100%">
									<tr>
										<td width="80"><label>Placa:</label></td>
										<td align="left"><input style="text-transform: uppercase;"
											type="text" name="veiplaca" value="<?=$_POST['veiplaca'] ?>"
											size="10" maxlength="15"></td>
									</tr>

									<tr class="tableRodapeModelo3">
										<td colspan="2" align="center"><input type="submit"
											name="acao" value="pesquisar" class="botao"
											onclick="pesquisarVeiculo()"></td>
									</tr>
								</table>
							</td>
						</tr>
					</table>
					
					<?php if($this->params['acao'] == 'pesquisar') { ?>
                    	<?php if(count($this->resultadoPesquisa) > 0) { ?>
        					<table width="98%" class="tableMoldura">
        						<tr class="tableSubTitulo">
        							<td><h1>Resultado da Pesquisa</h1></td>
        						</tr>
        						<tr>
        							<td>
        								<table width="100%">
            									<tr class="tableTituloColunas">
                        						<td><h3>ID Veículo</h3></td>
                        						<td><h3>Placa</h3></td>
                        						<td><h3>Contrato</h3></td>
                        						<td width="180" nowrap="nowrap"><h3>Status</h3></td>
                    						</tr>
                    						<?php foreach ($this->resultadoPesquisa as $linha) { ?>
                    							<td><?php echo $linha->veioid ?></td>
                        						<td><?php echo $linha->veiplaca ?></td>
                        						<td><?php echo $linha->veioid ?></td>
                            					<td nowrap="nowrap" align="center">
                                                    <?php if($linha->jasinalizado === 'N'){ ?>
                                                        <button onclick="sinalizarRoubo('<?php echo $linha->veioid ?>', '<?php echo $linha->veiplaca ?>')"
                            								class="botao" type="button">Sinalizar Roubo</button>
                            						<?php } else { ?> 
                            							<b>Roubo já sinalizado</b>
                            						<?php } ?>
                                                </td>
                    						<?php } ?>
        								</table>
        							</td>
        						</tr>
        					</table>
						<?php } ?>
            		<?php } ?>
			</form>
		</tr>
	</table>
</div>