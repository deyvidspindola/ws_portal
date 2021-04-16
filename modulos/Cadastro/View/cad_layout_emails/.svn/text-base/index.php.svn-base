<? require_once '_header.php' ?>

<center>
	<table class="tableMoldura">
		<tr class="tableTitulo">
	        <td><h1>Layout de Emails</h1></td>
	    </tr>
	    
	    <tr height="20">
			<td><span id="div_msg" class="msg">
				<? echo ($this->hasFlashMessage()) ? $this->flashMessage() : '' ?>
			</span></td>
		</tr>

		<tr>
	        <td align="center">
	            <table class="tableMoldura">
	            	<form method="get" id="pesquisa_layout_emails">
	            	
	            		            		
		                <tr class="tableSubTitulo">
		                    <td colspan="4"><h2>Dados para pesquisa</h2></td>
		                </tr>
		                
		             	<tr>
		            		<td><br></td>
		            	</tr>
		               	
		            	<tr>
		            	    <td width="15%"><label>Tipo de Envio: </label></td>
						    <td >
						    
						       <?
                      			 $selected = ' selected="selected"';                       
								?>					
						        <select name="seetipo" id="seetipo">
							         <option value="E" <?if($seetipo == "E") echo $selected;?> >Emails</option>
							         <option value="S" <?if($seetipo == "S") echo $selected;?>>SMS</option>
						        </select>
					     	 </td>
					    </tr> 
					    		    
		                <tr>
		                    <td width="15%"><label>Funcionalidade:</label></td>
		                    <td>
		                    	<select name="seeseefoid" id="seeseefoid">
		                    		<option value=" ">Todos</option>
		                    		<? foreach ($funcionalidades as $item): ?>
		                    			<option value="<?= $item['seefoid'] ?>" <?= ($_REQUEST['seeseefoid'] === $item['seefoid']) ? 'selected="selected"' : '' ?>>
		                    				<?= $item['seefdescricao']?>
		                    			</option>
		                    		<? endforeach ?>
		                    	</select>
		                    </td>
		                    
		                    <td width="15%"><label>Tipo de Proposta:</label></td>
		                    <td>
		                    	<select name="tppoid" id="tppoid">
		                    		<option value="">Selecione</option>
		                    		<? foreach ($tipoProposta as $item): ?>
		                    			<option value="<?= $item['tppoid'] ?>" <?= ($_REQUEST['tppoid'] === $item['tppoid']) ? 'selected="selected"' : '' ?>>
		                    				<?= $item['tppdescricao']?>
		                    			</option>
		                    		<? endforeach ?>
		                    	</select>
		                    </td>
		                    
		                </tr>
		                
		                <tr>	                	
		                    <td width="15%"><label class="titulo_layout" style="display:none;">Título do Layout:</label></td>	      
		                    <td>
		                    	<!-- OLD <input type="text" name="seecabecalho" size="50" value="<?= $titulo ?>" maxlength="50" />-->
		                    	<input type="hidden" name="titulo_layout_select" id="titulo_layout_select" value="<?= ($_REQUEST['seeseetoid'] ? $_REQUEST['seeseetoid'] : ""); ?>">
		                    	<select name=seeseetoid id="seeseetoid" class="titulo_layout" style="display:none;">
		                    		<option value=""></option> 
		                    	</select><img src="images/progress4.gif" id="loading_titulo" style="display:none;" />
		                    </td>   		                    
		                    
		                    <td width="15%"><label for="lconftppoid_sub"  class="sub_tipo_pro" style="display:none;">Subtipo de Proposta:</label></td>
		                    <td>
		                    	<input type="hidden" name="sub_tipo_pro_select" id="sub_tipo_pro_select" value="<?= ($_REQUEST['lconftppoid_sub'] ? $_REQUEST['lconftppoid_sub'] : ""); ?>">
		                    	<select name="lconftppoid_sub" id="lconftppoid_sub" class="sub_tipo_pro" style="display:none;">
		                    		<option value=""></option>
		                    	</select><img src="images/progress4.gif" id="loading_tipo" style="display:none;" />
		                    </td>           
		                </tr>
		                <?
            				if($seetipo=='E' || $seetipo==''){
                        ?>
		                <tr >
		                	<td id="usuarioSms" width="15%"><label>Usuário:</label></td>	      
		                    <td id="usuarios">
		                    	<input type="text"     name="usuario" size="50" value="<?= $usuario ?>" maxlength="50" />
		                    </td>  	                    
		                    
		                    <td width="15%"><label>Tipo de Contrato:</label></td>
		                    <td>
		                    	<select name="tpcoid" id="tpcoid">
		                    		<option value="">Selecione</option>
		                    		<? foreach ($tipoContrato as $item): ?>
		                    			<option value="<?= $item['tpcoid'] ?>" <?= ($_REQUEST['tpcoid'] === $item['tpcoid']) ? 'selected="selected"' : '' ?>>
		                    				<?= $item['tpcdescricao']?>
		                    			</option>
		                    		<? endforeach ?>
		                    	</select>
		                    </td>      
		                </tr>
		                <?
		                	}
		                ?>
		           	   <?
            			if($seetipo=='E'|| $seetipo==''){
                       ?>
		                <tr id ="srvoidSms">
		                    <td width="15%"><label>Servidor:</label></td>
		                    <td colspan="3">
		                    	<select name="srvoid" id="srvoid">
		                    		<option value="">Selecione</option>
		                    		<? foreach ($servidores as $item): ?>
		                    			<option value="<?= $item['srvoid'] ?>" <?= ($_REQUEST['srvoid'] === $item['srvoid']) ? 'selected="selected"' : '' ?>>
		                    				<?= $item['srvdescricao']?>
		                    			</option>
		                    		<? endforeach ?>
		                    	</select>
		                    </td>      
		                </tr>
		                <?
		                	} 
		                ?>
		                <tr>
		            		<td><br></td>
		            	</tr>
		                <tr class="tableRodapeModelo1" style="height:23px;">
		                    <td align="center" colspan="4">
		                        <input type="submit" name="bt_pesquisar" id="bt_pesquisar" value="Pesquisar" class="botao" >
		                        <a id="bt_novo" class="botao" href="<?= $_SERVER['SCRIPT_NAME'] . '?acao=novo' ?> ">Novo</a>
		                    </td>
		                </tr>
		            </form>
	            </table>
	            
	            <? if ($resultado): ?>
	            
	           
	            
	            <table class="tableMoldura" id="tabela_resultados">
	            	<tr class="tableSubTitulo">
	            		<td colspan="8"><h2>Resultado da pesquisa</h2></td>
	            	</tr>
	            
	            	
		           	<tr class="tableTituloColunas">
		           		
		           			<?
            				    if($seetipo=='S'){
                   			 ?>
		         
		           			<td width="20%" align="center" ><h3>Título/Funcionalidade</h3></td>
		           		     <?
				                }elseif ($seetipo=='E'){
				            ?>
		           		
		           			<td width="20%" align="center"><h3>Assunto do E-mail</h3></td>
		     			  <?php 
		     			    }
		     			  ?>
		     			 
		           		<td width="10%" align="center"><h3>Data de Cadastro</h3></td>
		           		<td width="25%" align="center"><h3>Usuário</h3></td>
		           		<td width="5%" align="center"><h3>Principal</h3></td>
		           		<td width="15%" align="center"><h3>Tipo de Proposta</h3></td>
		           		<td width="15%" align="center"><h3>Subtipo de Proposta</h3></td>
		           		<td width="20%" align="center"><h3>Tipo de Contrato</h3></td>
		           		<td width="10%" align="center"><h3>Excluir</h3></td>
		           	</tr>
	            	
	            	<? foreach ($resultado as $item): ?>
	          	
	            	 	            	
		            	<tr class="item">	            
		                 
		                   		<?
            				   	  if($seetipo=='S'){
                   			 	?>
		                 	   <td align="left" >
		                 	 	  <a href="<?= $_SERVER['PHP_SELF'] ?>?acao=editar&seeoid=<?= $item['seeoid'] ?>">
		                 	   		<?= $item['funcionalidade'] ?>/<?= $item['seetdescricao'] ?>
		                 	   	  </a>	
		                 	   	</td>
		                       <?
				               	 }elseif ($seetipo=='E'){
			          		  ?>
		           		
		                    <td align="left">
		                    	<a href="<?= $_SERVER['PHP_SELF'] ?>?acao=editar&seeoid=<?= $item['seeoid'] ?>">
		                    		<?= $item['seecabecalho'] ?>
		                    	</a>
		                    </td>
		                     <?php 
		     			  		  }
		     				  ?>
		                     
		                    <td align="center"><?= date('d/m/Y', strtotime($item['seedt_cadastro'])) ?></td>
		                    <td align="left"><?= $item['nm_usuario'] ?></td>
		                    <td align="left"><?= ($item['seepadrao'] == 't') ? 'Sim' : 'Não' ?></td>
		                    <td align="left"><?= $item['tipo_proposta'] ?></td>
		                    <td align="left"><?= $item['subtipo_proposta'] ?></td>
		                    <td align="left"><?= $item['tpcdescricao'] ?></td>
		                    <td align="center">[<a class="excluir" data-seeoid="<?= $item['seeoid'] ?>" data-seepadrao="<?= $item['seepadrao'] ?>">X</a>]</td>
		                </tr>
	                <? endforeach ?>
	                        
	                <tr class="tableRodapeModelo3">
	                	<td colspan="8" align="center">A pesquisa retornou <b><?= count($resultado) ?></b> registro(s).</td>
	                </tr>
	            </table>
	            <? endif ?>
	            
	            <? if ($resultado === false): ?>
	            <table class="tableMoldura">
	            	<tr class="tableRodapeModelo3">
	                	<td colspan="3" align="center">Nenhum resultado encontrado.</td>
	                </tr>
	            </table>
	            <? endif ?>
	        </td>
	    </tr>
	</table>
</center>


<? @include_once "lib/rodape.php" ?>