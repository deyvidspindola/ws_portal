<?php 
cabecalho();
?>
	<link rel="stylesheet" href="calendar/calendar.css" type="text/css"  />    
    <link type="text/css" rel="stylesheet" href="includes/css/base_form.css">
    <link rel="stylesheet" href="modulos/web/css/cad_tipos_segmento_mercado.css" type="text/css"  />
    <script type="text/javascript" src="modulos/web/js/lib/jQuery.js"></script>       
    <script type="text/javascript" src="js/jquery.validate.js"></script>  
    <script language="Javascript" type="text/javascript" src="includes/js/auxiliares.js"></script>
    <script type="text/javascript" src="modulos/web/js/cad_tipos_segmento_mercado.js"></script>	
    <script>
		<?php if ($mensagem != '' ) { ?>
		exibeAlertaMsg('<?php echo $mensagem;?>');
		<?php } ?>
	</script>
	<br />

		<div align="center">
			     <form id="form" name="form" action="<?php echo $_SERVER['PHP_SELF'];?>" method="post" encType="multipart/form-data">
                <input type="hidden" name="acao" id="acao" value="<?php echo $acao;?>">
                <input type="hidden" name="segoid" id="segoid" value="<?php echo $segoid; ?>">
                
                <table width="98%" class="tableMoldura">
                    <tr class="tableTitulo">
                        <td><h1>Tipo de Segmento de Mercado</h1></td>
                    </tr>
                    <tr>
		                                	<td>&nbsp;</td>
                    </tr>
                    <tr>
                        <td align="center">
                        	<?php
                        	if( $acao == "novo" ||  $acao == "editar" ){ ?>

	                      		<table width="98%"  class="tableMoldura">
		                            	<tr class="tableSubTitulo">
		                                	<td colspan="2"><h2><?php echo ($acao == 'novo') ? 'Novo ' : ''?>Registro</h2></td>
		                                </tr>

		                                <tr>
		                                	<td>&nbsp;</td>
		                                </tr>	                                    
		                                <tr>
		                                	<td nowrap width="12%"><label>Descrição: *</label></td>
		                                	<td>
		                                		<input type="text" name="segdescricao" id="segdescricao" value="<?php echo $segdescricao; ?>" size="40">		                                    </td>
		                                </tr>
		                                <tr>
		                                	<td>&nbsp;</td>
		                                </tr>
		                                 <tr>
					                    	<td colspan="10">
					                    		<label style="margin-bottom: 4px;">(*) Campo com preenchimento obrigatório</label>
					                    	</td>
					                    </tr>
					                    <tr>
		                                	<td>&nbsp;</td>
		                                </tr>
										<tr class="tableRodapeModelo1">
					                    	<td colspan="2" align="center">
					                    		<input type="button" name="<?php echo ($acao == 'novo') ? 'cadastrar' : 'atualizar' ?>" value="<?php echo ($acao == 'novo') ? 'Salvar' : 'Atualizar' ?>" class="botao">	
					                        					                        	
					                        	<input type="button" name="<?php echo $_SERVER['PHP_SELF']?>" value="Cancelar" class="botao">

					                        	<?php if ($acao == 'editar') {
					                        	?>
					                        	<input type="button" name="excluir" value="Excluir" class="botao" id="botao_excluir">
					                        	<?php
					                        	}
					                        	?>

					                        </td>
					                    </tr>
					                </table>
	                            <?php
                            	}
          						?>
                        </td>
                    </tr>                    
                </table>
            </form>
		</div>
<?php 
include ("lib/rodape.php");