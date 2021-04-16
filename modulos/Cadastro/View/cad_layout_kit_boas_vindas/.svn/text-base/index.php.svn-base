<?php

/**
 * @file index.php
 * @author Keidi Nienkotter
 * @version 16/01/2013 10:58:42
 * @since 16/01/2013 10:58:42
 * @package SASCAR index.php 
 */


?>


<!-- CSS -->
<link type="text/css" rel="stylesheet" href="includes/css/base_form.css">
<link type="text/css" rel="stylesheet" href="includes/css/calendar.css">
<link type="text/css" rel="stylesheet" href="modulos/web/css/cad_layout_kit_boas_vinda.css">

<!-- JAVASCRIPT -->
<script type="text/javascript" src="includes/js/calendar.js"></script>
<script type="text/javascript" src="includes/js/mascaras.js"></script>
<script type="text/javascript" src="includes/js/auxiliares.js"></script>  
<script type="text/javascript" src="includes/js/validacoes.js"></script>
<script language="Javascript" type="text/javascript" src="js/jquery-1.7.1.js"></script>
<script language="Javascript" type="text/javascript" src="modulos/web/js/json2.js"></script>
<script language="Javascript" type="text/javascript" src="modulos/web/js/cad_layout_kit_boas_vindas.js"></script>

<div id="conteudo">
<!-- Início do bloco ação cadastrar Configuração  -->
<form name="form" id="form" enctype="multipart/form-data" method="POST" action="<?=$_SERVER['PHP_SELF'];?>">
<center>
	<table class="tableMoldura">
		<tr class="tableTitulo">
			<td><h1>Cadastro de configuração de Tipo de Proposta X Contrato</h1></td>
		</tr>
		
		
		<tr height="20" class="div_acao_cadastrar" >
			<td>
				<span id="div_msg_cadastro" class="msg"> <?=$this->return['retorno']['msg'];?></span>
		    </td>
		</tr>
	    <tr class="div_acao_cadastrar">
	        <td align="center">
	            <table class="tableMoldura">
	                <tr class="tableSubTitulo">
	                    <td colspan="2"><h2></h2></td>
	                </tr>
	                <tr>
	                    <td class="td_label"><label for="comboConfiguracao">Configuração*:</label></td>
	                    <td width="85%">
	                    	<select name="comboConfiguracao" id="comboConfiguracao">
	                    		<option value="">Selecione</option>
	                    		<option value="0">** Novo **</option>
                                <?php foreach($this->comboConfiguracoes as $configuracao ): ?>
                                <option value="<?php echo $configuracao['confoid'] ?>"><?php echo utf8_decode($configuracao['confdescricao']) ?></option>  
                                <?php endforeach; ?> 
	                    	</select>
	                    </td>
	                </tr>
	                <tr>
	                    <td colspan="2"><hr></td>
	                </tr>
	                <tr>
	                    <td class="td_label"><label for="comboPropostas">Tipo de proposta:</label></td>
	                    <td width="85%">
	                    	<select name="comboPropostas" id="comboPropostas">
	                    		<option value="">Selecione</option>
                                <?php foreach($this->comboPropostas as $proposta): ?>
                                <option value="<?php echo $proposta['tppoid'] ?>"><?php echo utf8_decode($proposta['tppdescricao']) ?></option>  
                                <?php endforeach; ?> 
	                    	</select>
	                    </td>
	                </tr>
	                <tr>
	                    <td class="td_label"><label for="comboSubpropostas">Subtipo de proposta:</label></td>
	                    <td width="85%">
	                    	<select name="comboSubpropostas" id="comboSubpropostas">
	                    		<option value="">Selecione</option>                                
	                    	</select>
	                    </td>
	                </tr>
	                <tr>
	                    <td class="td_label"><label for="comboContratos">Tipo de contrato:</label></td>
	                    <td width="85%">
	                    	<select name="comboContratos" id="comboContratos">
	                    		<option value="">Selecione</option>
                                <?php foreach($this->comboContratos as $contrato): ?>
                                <option value="<?php echo $contrato['tpcoid'] ?>"><?php echo utf8_decode($contrato['tpcdescricao']) ?></option>  
                                <?php endforeach; ?> 
	                    	</select>
	                    </td>
	                </tr>
	                <!-- STI 81455 -->
	                <tr>
	                    <td class="td_label"><label for="comboClasse">Classe de Contrato:</label></td>
	                    <td width="85%">
	                    	<select name="comboClasse" id="comboClasse">
	                    		<option value="">Selecione</option>
                                <?php foreach($this->comboClasses as $classe): ?>
                                <option value="<?php echo $classe['eqcoid'] ?>"><?php echo utf8_decode($classe['eqcdescricao']) ?></option>  
                                <?php endforeach; ?> 
	                    	</select>
	                    </td>
	                </tr>
	                <tr>
	                    <td colspan="2"><hr></td>
	                </tr>
	                <tr>
	                    <td class="td_label"><label for="comboServidor">Servidor:</label></td>
	                    <td width="85%">
	                    	<select name="comboServidor" id="comboServidor">
	                    		<option value="">Selecione</option>
                                <?php foreach($this->comboServidores as $servidor): ?>
                                <option value="<?php echo $servidor['srvoid'] ?>"><?php echo utf8_decode($servidor['srvdescricao']) ?></option>  
                                <?php endforeach; ?> 
	                    	</select>
	                    </td>
	                </tr>
	                <tr>
	                    <td class="td_label"><label for="inputAnexo">Anexo:</label></td>
	                    <td width="85%">
	                    	
	                    	<input type="text" name="inputAnexo" id="inputAnexo" style="display: none">
	                    		                    	
	                    	<input type="button" name="bt_ver_anexo" id="bt_ver_anexo" class="botao" value="Visualizar Anexo" style="display:none; width: 150px">
	                    	
 	                    	<input type="button" name="selecionarAnexo" id="selecionarAnexo" value="Selecionar Anexo" class="botao" disabled="disabled" style="width: 150px"/>
	                    	
	                    </td>
	                </tr>
	                 <tr>
	                    <td class="td_label"><label for="comboTipoLayout">Tipo de Layout*:</label></td>
	                    <td width="85%">
	                    	<select name="comboTipoLayout" id="comboTipoLayout">
	                    		<option value="">Selecione</option>
                                <?php foreach($this->comboTipoLayouts as $layout): ?>
                                    <option value="<?php echo $layout['tcloid'] ?>"><?php echo utf8_decode($layout['tcldescricao']) ?></option>  
                                <?php endforeach; ?> 
	                    	</select>
	                    </td>
	                </tr>	 
	                 <tr>
	                    <td colspan="2"><hr></td>
	                </tr>
	                <tr>
	                    <td class="td_label"><label for="comboLayout">Layout*:</label></td>
	                    <td width="85%">
	                    	<select name="comboLayout" id="comboLayout">
	                    		<option value="">Selecione</option>
                                <?php foreach($this->comboLayouts as $layout): ?>
                                <option value="<?php echo $layout['lwkoid'] ?>"><?php echo utf8_decode($layout['lwkdescricao']) ?></option>  
                                <?php endforeach; ?> 
	                    	</select>
	                    	<span><a id="criarNovoLayout">Cadastro Layout</a></span>
	                    </td>
	                </tr>	                
	                <tr>
	                    <td colspan="2" width="100%">
	                    	<div id="htmlLayout" style="display: none;"></div>
	                    </td>
	                </tr>
	                <tr class="tableRodapeModelo1" style="height:23px;">
	                    <td align="center" colspan="2">
	                        <input type="button" name="bt_gravar" id="bt_gravar" value="Gravar" class="botao" style="width:90px;">
	                        <input type="button" name="bt_limpar" id="bt_limpar" value="Limpar" class="botao" style="width:90px;">
	                        <input type="button" name="bt_excluir" id="bt_excluir" value="Excluir" class="botao" style="width:90px;">
	                    </td>
	                </tr>
	            </table>
	            <div id="processando" style="width: 100%; text-align:center;"></div>	
	        </td>
	    </tr>
	    <tr>
	    	<td>
	    		<center>
				    <div class="processando_excluir" style="display: none;"><img src="images/loading.gif" alt="" /></div>
				</center>
	    	</td>
	    </tr>
		
	</table>
</center>
</form>
<!-- Fim do bloco ação cadastrar Configuração -->

<?
include 'lib/rodape.php';
?>