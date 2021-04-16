<?php
@cabecalho();
@require_once ("lib/funcoes.js");
?>

<head>            
    <link type="text/css" rel="stylesheet" href="includes/css/base_form.css">
    <link type="text/css" rel="stylesheet" href="modulos/web/css/cad_info_controle_falhas.css">
    <link type="text/css" rel="stylesheet" href="modulos/web/css/lib/loading.css">
    
    <script type="text/javascript" src="modulos/web/js/lib/jQuery.js"></script>
    <script type="text/javascript" src="modulos/web/js/lib/jquery.maskedinput-1.3.min.js"></script>    
    <script type="text/javascript" src="includes/js/validacoes.js"></script> 
    <script language="Javascript" type="text/javascript" src="includes/js/auxiliares.js"></script>
    <script type="text/javascript" src="modulos/web/js/lib/classy.js"></script>
    <script type="text/javascript" src="modulos/web/js/lib/loading.js"></script>
    
    <!-- jQuery UI -->
    <link type="text/css" rel="stylesheet" href="modulos/web/js/lib/jQueryUI/themes/base/jquery.ui.all.css">        
    <script src="modulos/web/js/lib/jQueryUI/ui/jquery.effects.core.js"></script>
    <script src="modulos/web/js/lib/jQueryUI/ui/jquery.effects.highlight.js"></script>
    
    <script type="text/javascript" src="modulos/web/js/cad_info_controle_falhas.js"></script>
    
</head>

<center>
	<table class="tableMoldura">
		<tr class="tableTitulo">
	        <td><h1>Cadastro – Nova Info. Controle de Falhas</h1></td>
	    </tr>

		<tr height="20">
			<td><span id="div_msg" class="msg">
				<? echo ($this->hasFlashMessage()) ? $this->flashMessage() : '' ?>
			</span></td>
		</tr>

		<tr>
	        <td align="center">
	            <table class="tableMoldura">
	            	<form method="post" data-action="<?= $_SERVER['SCRIPT_NAME']?>" id="pesquisa_controle_falhas">
	            		
	            		
		                <tr class="tableSubTitulo">
		                    <td colspan="3"><h2>Dados para pesquisa</h2></td>
		                </tr>
		                
		                <? if ($this->hasFlashMessage()): ?>
		                <tr>
		                	<td><div class="erro-flash"><? echo $this->flashMessage() ?></div></td>
					    </tr>
					    <? endif ?>
					    
		                <tr>
		                    <td width="15%"><label>Equipamento *</label></td>
		                    <td>
		                    	<select name="item_produto_id" id="item_produto_id">
		                    		<option value="0">Selecione</option>
		                    		<? foreach ($equipamentos as $item): ?>
		                    			<option value="<?= $item['eproid'] ?>" <?= ($_REQUEST['item_produto_id'] === $item['eproid']) ? 'selected="selected"' : '' ?>>
		                    				<?= $item['eprnome']?>
		                    			</option>
		                    		<? endforeach ?>
		                    	</select>
		                    </td>
		                </tr>
		                
		                <tr>	                	
		                    <td width="15%"><label>Item Falhas *</label></td>	      
		                    <td>
		                    	<select name="item_falha_id" id="item_falha_id">
		                    		<option value="0">Selecione</option>
		                    		<? foreach ($falhas as $id => $item): ?>
		                    			<option value="<?= $id ?>" <?= ($_REQUEST['item_falha_id'] == $id) ? 'selected="selected"' : '' ?>>
		                    				<?= $item ?>
		                    			</option>
		                    		<? endforeach ?>
		                    	</select>
		                    </td>              
		                </tr>
		                
		                <tr>
		                	<td width="15%"><label>Descrição</label></td>
		                	<td><input type="text" name="item_descricao" id="item_descricao" value="<?= (isset($_REQUEST['item_descricao'])) ? $_REQUEST['item_descricao'] : '' ?>"></td>
		                </tr>
		                
		                <tr height="24">
		                    <td colspan="2"><label>(*) Campos com preenchimento obrigatório</label></td>
		                </tr>
		                
		                <tr class="tableRodapeModelo1" style="height:23px;">
		                    <td align="center" colspan="2">
		                        <input type="submit" name="bt_pesquisar" id="bt_pesquisar" value="Pesquisar" class="botao" data-action="<?= $_SERVER['SCRIPT_NAME'] ?>">
		                        <input type="submit" name="bt_novo" id="bt_novo" value="Novo" class="botao" data-action="<?= $_SERVER['SCRIPT_NAME'] ?>">
		                    </td>
		                </tr>
		            </form>
	            </table>
	            
	            <? if ($resultado): ?>
	            <table class="tableMoldura" id="tabela_resultados">
	            	<tr class="tableSubTitulo">
	            		<td colspan="5"><h2>Resultado</h2></td>
	            	</tr>
	            	
		           	<tr class="tableTituloColunas">
		           		<td width="5%" align="center"><input type="checkbox" id="check_all"/></td>
		           		<td width="80%" align="left"><h3><?= $itemFalha ?></h3></td>
		           		<td width="15%" align="center"><h3>Código</h3></td>
		           	</tr>
	            	
	            	<? foreach ($resultado as $item): ?>		            	
		            	<tr class="item">	            
		            		<td align="center"><input type="checkbox" class="item_id_del" name="item_id_del[]" value="<?= $item['item_id'] ?>"/></td>
		                    <td align="left"><?= $item['item_descricao'] ?></td>
		                    <td align="right"><?= $item['item_id'] ?></td>
		                </tr>
	                <? endforeach ?>
	                        
	                <tr class="tableRodapeModelo3">
	                	<td colspan="3" align="center">A pesquisa retornou <b><?= count($resultado) ?></b> <?= (count($resultado) === 1) ? 'registro' : 'registros' ?>.</td>
	                </tr>
	                
	                <tr class="tableRodapeModelo1">
	                    <td align="center" colspan="3">
	                        <input type="button" name="bt_excluir" id="bt_excluir" value="Excluir" class="botao" data-action="<?= $_SERVER['SCRIPT_NAME']?>" data-item-falha-id="<?= $itemFalhaId ?>">
	                    </td>
	                </tr>
	            </table>
	            <? endif ?>
	            
	            <? if ($resultado === false): ?>
	            <table class="tableMoldura">
	            	<tr class="tableRodapeModelo3">
	                	<td colspan="3" align="center">Nenhum Registro Encontrado.</td>
	                </tr>
	            </table>
	            <? endif ?>
	        </td>
	    </tr>
	</table>
</center>


<?php
@include_once "lib/rodape.php";
?>