<?php 	cabecalho(); ?>

<head>
    <!-- CSS -->
    <link type="text/css" rel="stylesheet" href="calendar/calendar.css"/>
    <link type="text/css" rel="stylesheet" href="includes/css/base_form.css">
    <link type="text/css" rel="stylesheet" href="modulos/web/js/lib/jQueryUI/themes/base/jquery.ui.all.css" > 
    <link type="text/css" rel="stylesheet" href="modulos/web/css/cad_info_controle_falhas.css">

    <!-- JAVASCRIPT -->
    <script type="text/javascript" src="includes/js/calendar.js"></script>
    <script type="text/javascript" src="includes/js/mascaras.js"></script>
    <script type="text/javascript" src="includes/js/auxiliares.js"></script>
    <script type="text/javascript" src="includes/js/validacoes.js"></script>
    <script type="text/javascript" src="modulos/web/js/lib/jQuery.js"></script>
    <script type="text/javascript" src="modulos/web/js/lib/jQueryUI/js/jquery-ui-1.8.24.custom.min.js"></script>
    <script type="text/javascript" src="modulos/web/js/cad_controle_falhas_acessorios.js"></script>    
</head>

<form name="form" id="form" method="POST" action="cad_controle_falhas_acessorios.php">
    <input type="hidden" name="acao" id="acao" />

    <center>
        
        <table class="tableMoldura">
            
            <tr class="tableTitulo">
                <td><h1>Cadastro - Nova Info. Controle de Falhas Acessório</h1></td>
            </tr>
            
            <tr  height="10"> 
                <td >
                    <span id="msg" class="msg"><?php echo isset($_SESSION['flash_message']) ? $_SESSION['flash_message'] : '';  $_SESSION['flash_message'] = ""; ?></span>
                </td>
            </tr>	
             
            <tr> 
                <td align="center">
                     
                    <table class="tableMoldura">
                        
                        <tr class="tableSubTitulo">
                            <td colspan="2"><h2>Dados para pesquisa</h2></td>                       
                        </tr> 
                       
                       <tr>
                       		<td colspan="2" style="height:6px !important;"></td>
                       </tr>                       
                        <tr>                        	
		                    <td width="15%"><label>Acessório *</label></td>
		                    <td> 
		                    	<select id="acessorio" name="acessorio" style="width:170px !important;">
		                    		<option value="0">Selecione</option>		                    		
		                    		<option value="4"  <?php  if((isset($_POST['acessorio'])) && ($_POST['acessorio'] == 4)) { echo "selected";} ?>>Antena Satelital</option>		                    			                    		
		                    		<option value="23" <?php  if((isset($_POST['acessorio'])) && ($_POST['acessorio'] == 23)){ echo "selected";} ?>>Computador Bordo</option>
		                    		<option value="21" <?php  if((isset($_POST['acessorio'])) && ($_POST['acessorio'] == 21)){ echo "selected";} ?>>Teclado</option>	
		                    		<option value="11" <?php  if((isset($_POST['acessorio'])) && ($_POST['acessorio'] == 11)){ echo "selected";} ?>>Trava Baú</option>
		                    		<option value="13" <?php  if((isset($_POST['acessorio'])) && ($_POST['acessorio'] == 13)){ echo "selected";} ?>>Trava 5ª Roda</option>
								</select>
		                    </td>
		                </tr>
                        <tr>	                	
		                    <td width="15%"><label>Item Falhas *</label></td>	      
		                    <td>
		                    	<select id="item_falha" name="item_falha" style="width:170px !important;">
		                    		<option value="0">Selecione</option>
		                    		<option value="1" <?php  if((isset($_POST['item_falha'])) && ($_POST['item_falha'] == 1)){ echo "selected";} ?>>Ação Lab.</option>
		                    		<option value="2" <?php  if((isset($_POST['item_falha'])) && ($_POST['item_falha'] == 2)){ echo "selected";} ?>>Componente Afetado</option>
		                    		<option value="3" <?php  if((isset($_POST['item_falha'])) && ($_POST['item_falha'] == 3)){ echo "selected";} ?>>Defeito Lab.</option>	
		                    	</select>
		                    </td>              
		                </tr>
		                <tr>
		                	<td width="15%"><label>Descrição</label></td>
		                	<td><input style="width:170px !important;" id="descricao" name="descricao" value="<?php echo (isset($_POST['descricao']) ? htmlentities( $_POST['descricao'] ): ''); ?>" type="text" maxlength="150"></td>
		                </tr>		                
		                <tr height="24">
		                    <td colSpan="2"><label>(*) Campos com preenchimento obrigatório.</label></td>
		                </tr>                      
                        <tr style="height: 23px;" class="tableRodapeModelo1">
		                    <td colSpan="2" align="center">
		                        <input id="bt_pesquisar" class="botao" name="bt_pesquisar" value="Pesquisar" type="button" data-action="/sistemaWeb/cad_info_controle_falhas.php">
		                        <input id="bt_novo" class="botao" name="bt_novo" value="Novo" type="button" data-action="/sistemaWeb/cad_info_controle_falhas.php">
		                    </td>
		                </tr
                        
                    </table>
                    
                </td>
            </tr> 

                        
        </table>
           <tr> 
		        <td align="center">		        
		          <table class="tableMoldura">		                 
		                   <?php
		                   		if($totalRegistros > 0){?>
		                   		
			                  <tr class="tableSubTitulo">
			                     <td colspan="3"><h2>Resultado</h2></td>
			                     <tr class="tableTituloColunas">
			                     	<td align="center" width="5%">
			                     		<input title="Excluir" type="checkbox" id="check_all" >
						            </td>
	                	          	<td width="60%"><h3 id="descricao">
	                	          	<?php
	                	          		echo isset($_SESSION['descricao_acessorio']) ? $_SESSION['descricao_acessorio'] : '';  $_SESSION['descricao_acessorio'] = ""; 									
	                	          	
	                	          	?>	                	          	
	                	          	</h3></td>
	                	          	<td width="35%" align="center"><h3>Código</h3></td>                       
			                  </tr>								
		
									<?php 
										foreach($resultado as $itens => $k){

                                		$class = ($class=="tdc") ? "tde" : "tdc"; ?>
		                                <tr class="<?php echo $class; ?>">
		                                	<td width="5%" align="center"><input title="Excluir" type="checkbox" class="chk_codigo" name="chk_codigo[]" value='<?php echo $k['codigo'];?>'></td>
		                                	<td width="60%" ><?php echo wordwrap($k['descricao'],95, '<br />', true);?></td>
		                    	            <td width="35%" align="right"><?php echo $k['codigo'];?></td>
		                                </tr>												
									
                            <?php }?>
                    	  
                    	  <tr class="tableTituloColunas">
                                   <td colspan="3" align="center"><b>
                                   	A pesquisa retornou
                                    <?php 
                                    if($totalRegistros == 1){
                                    	echo $totalRegistros . " registro.";
                                    }else{                                    
                                    	echo $totalRegistros . " registros.";
                                    }
                                    ?>
                                    </b></td>
                          </tr>
                        <tr style="height: 23px;" class="tableRodapeModelo1">
		                    <td colSpan="3" align="center">
		                        <input id="bt_excluir" class="botao" name="bt_excluir" value="Excluir" type="button" data-action="/sistemaWeb/cad_info_controle_falhas.php">
		                   </td>
		               </tr>
		               <?php }else if(isset($_POST['acao']) && $_POST['acao']!= "index") { ?>
		               <tr class="tableRodapeModelo1">
                            <td colspan="3" align="center">Nenhum Registro Encontrado.</td>
                        </tr>
                        <?php }?>
		          </table>
		     </td>       
           </tr>
    </center>
</form>