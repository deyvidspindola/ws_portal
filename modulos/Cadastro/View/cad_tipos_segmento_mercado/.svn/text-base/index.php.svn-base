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
			     <form id="form" name="form" action="<?=$_SERVER['PHP_SELF'];?>" method="post" encType="multipart/form-data">
                <input type="hidden" name="acao" id="acao" value="<?php echo $acao; ?>">
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
                        	if( $acao == "index" ||  $acao == "pesquisar" ){ ?>
	                        	<table width="98%" class="tableMoldura">
	                            	<tr class="tableSubTitulo">
	                                	<td colspan="4"><h2>Dados para pesquisa</h2></td>
	                                </tr>

		                                <tr>
		                                	<td>&nbsp;</td>
		                                </tr>
                                    <tr>
	                                	<td width="15%"><label>Descrição:</label></td>
	                                	<td>
	                                    	<input type="text" name="segdescricao" size="40" value="<?php echo $segdescricao; ?>">
	                                    </td>
	                                </tr>
	                                <tr>
	                                	<td>&nbsp;</td>
	                                </tr>
	                                <tr>
	                                	<td>&nbsp;</td>
	                                </tr>
	                                <tr class="tableRodapeModelo1">
	                                	<td colspan="2" align="center">
	                                    	<input type="button" name="pesquisar"  value="Pesquisar" class="botao">
	                                    	<input type="button" name="novo"  value="Novo" class="botao">	
	                                    </td>
	                                </tr>
	                            </table>
	                            
	                            <?
                                //Exibe o resultado
	                            if($acao == "pesquisar"){
	                            	?>
	                            	<br>
	                            	<table width="98%"  class="tableMoldura">
		                            	<tr class="tableSubTitulo">
		                                	<td colspan="2"><h2>Resultado da pesquisa</h2></td>
		                                </tr>
		                                
		                                <tr class="tableTituloColunas">
		                                	<td align="center" width="10%"><h3>Ações</h3></td>
		                                	<td align="center" width="90%"><h3>Descrição</h3></td>
		                                </tr>
		                                
		                                <?

		                                if(count($resultadoPesquisa) > 0){
		                                	foreach ($resultadoPesquisa as $rs) {
		                                		$class = ($class == "tdc") ? "tde" : "tdc";
		                                		$img = ($img == "images/icones/t1/x.jpg") ? "images/icones/tf1/x.jpg" : "images/icones/t1/x.jpg";
		                                	?>
		                                		<tr class="<?php echo $class; ?>">
				                                	<td width="10%" align="center">
							                        	[<a href="#" segoid="<?php echo $rs['segoid']; ?>" class="excluir">
							                        		<img src="<?php echo $img; ?>">
							                        	</a>]
													</td>
		                                		    <td>
							                        	<a href="#" segoid="<?php echo $rs['segoid']; ?>" class="editar">
							                        		<?php echo $rs['segdescricao']; ?>
							                        	</a>
													</td>
				                                </tr>
				                            <?php		                                	
		                                	}
		                                	?>
		                                	
		                                	<tr class="tableRodapeModelo3">
		                                		<td colspan="2" align="center"><b><?php echo count($resultadoPesquisa)?> Registro(s) encontrado(s)</b></td>
		                                	</tr>
		                                	<?
		                                }
		                                else {
		                                	?>
		                                	<tr class="tableRodapeModelo3">
		                                		<td colspan="2" align="center"><b>Nenhum Registro Encontrado</b></td>
		                                	</tr>
		                                	<?php
		                                }
		                                ?>
		                            </table>
		                            <?php
	                            	}
              						?>
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