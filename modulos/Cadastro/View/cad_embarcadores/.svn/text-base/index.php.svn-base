<center> 
	<form name="form" id="form" method="post" action="">
	<?php echo $this->formPesquisaObj->desenhaCampo('acao', array('open_tr' => false,)); 
		  echo $this->formPesquisaObj->desenhaCampo('emboid', array('open_tr' => false,)); ?>	
	<table class="tableMoldura" width="98%">
		<tr class="tableTitulo">
            <td><h1>Cadastro de Embarcadores</h1></td>
        </tr>
        <tr>
        	<td align="center">
        		<br />
	        	<table class="tableMoldura" width="98%">        	       	
					<tbody>
						<tr class="tableSubTitulo">
							<td colspan="2"><h2>Dados para Pesquisa</h2></td>
						</tr>
						<tr>
							<td class="camposPesquisa">
								<?php echo $this->formPesquisaObj->desenhaCampo('embnome', array('open_tr' => false,)); ?>									
							</td>
							<td class="camposPesquisa">
								<?php echo $this->formPesquisaObj->desenhaCampo('embcnpj', array('open_tr' => false,)); ?>									
							</td>
						</tr>
						<tr>
							<td class="camposPesquisa">
								<?php echo $this->formPesquisaObj->desenhaCampo('embsegoid', array('open_tr' => false,)); ?>									
							</td>
							<td class="camposPesquisa">
								<?php echo $this->formPesquisaObj->desenhaCampo('embuf', array('open_tr' => false,)); ?>									
							</td>
						</tr>
						<tr>
							<td class="camposPesquisa">
								<?php echo $this->formPesquisaObj->desenhaCampo('embcidade', array('open_tr' => false,)); ?>									
							</td>
							<td class="camposPesquisa">
								<?php echo $this->formPesquisaObj->desenhaCampo('embfrota', array('open_tr' => false,)); ?>									
							</td>
						</tr>
						<tr class="tableRodapeModelo1">
							<td colspan="2" align="center">
								<?php echo $this->formPesquisaObj->desenhaCampo('pesquisar', array('open_tr' => false)); ?>
								<?php echo $this->formPesquisaObj->desenhaCampo('novo', array('open_tr' => false)); ?>
							</td>
						</tr>
					</tbody>
				</table>
			</td>
		</tr>
	</table>
</center>				
<?php
	if($acao == 'pesquisar'): ?>
<div align="center">
	<table width="98%"  class="tableMoldura">
		<tr class="tableSubTitulo">
		   	<td colspan="7"><h2>Resultados</h2></td>
		</tr>
		<tr class="tableTituloColunas">
			<td align="center"><h3>Nome</h3></td>
	    	<td align="center"><h3>Segmento</h3></td>
	    	<td align="center"><h3>CNPJ</h3></td>
	    	<td align="center"><h3>Cidade</h3></td>
	    	<td align="center"><h3>Estado</h3></td>
	    	<td align="center"><h3>Fone</h3></td>
	    	<td align="center"><h3>Frota Própria</h3></td>
	    </tr>
	    
	    <?php 
	   	if(count($resultadoPesquisa) > 0):
	    	foreach ($resultadoPesquisa as $rs):
	    		$class = ($class == "tdc") ? "tde" : "tdc";		                                		
	    			?>
			<tr class="<?php echo $class; ?>">
	        	<td align="left">
	        		<a href="javascript:void(0);" class="editar" emboid="<?php echo $rs['emboid']; ?>">
	            		<?php echo $rs['embnome']; ?>
	            	</a>
				</td>
			    <td>
	            	<?php echo $rs['segdescricao']; ?>							                        	
				</td>
				<td align="center">
	            	<?php echo $this->dao->formatarCNPJ($rs['embcnpj']); ?>							                        	
				</td>
				<td>
	            	<?php echo $rs['embcidade']; ?>							                        	
				</td>
				<td align="center">
	            	<?php echo $rs['embuf']; ?>							                        	
				</td>
				<td align="center">
	            	<?php echo $rs['embtelefone1']; ?>							                        	
				</td>
				<td align="center">
	            	<?php echo $rs['frota_propria']; ?>							                        	
				</td>
	        </tr>
	    	<?php endforeach; ?>
			<tr class="tableRodapeModelo3">
		    	<td colspan="7" align="center"><b><?php echo count($resultadoPesquisa)?> Registro(s) encontrado(s)</b></td>
		    </tr>
	    <?php
	    else: ?>
	    	<tr class="tableRodapeModelo3">
	    		<td colspan="7" align="center"><b>Nenhum Registro Encontrado</b></td>
	    	</tr>
	    <?php endif; ?>
	</table>
</div>
<?php endif; ?>
<script type="text/javascript" src="modulos/web/js/lib/jquery.maskedinput-1.3.min.js"></script>
<script type="text/javascript" src="modulos/web/js/cad_embarcadores.js"></script>
<script>
	$(document).ready(function(){
		//jQuery('#not_id_embcnpj').mask('99.999.999/9999-99');
		$('#not_form_div_ckbox_embfrota').parent().parent().parent().parent().css({'display': 'inline', 'width': '150px'});
	});
	<?php if($mensagem != ''): ?>
	exibeAlertaMsg('<?php echo $mensagem?>');
	<?php endif; ?>
</script>
<style>
	input, select, textarea {
	  box-sizing: content-box;
	  -moz-box-sizing: content-box;
	  -webkit-box-sizing: content-box;
	}	
	.camposPesquisa {width: 300px;}
	.camposPesquisa label {width: 130px; display: inline-block;}
	.camposPesquisa span {display: inline-block;}	
	.botao {width: 100px;}

</style>
<?php 
include ("lib/rodape.php");