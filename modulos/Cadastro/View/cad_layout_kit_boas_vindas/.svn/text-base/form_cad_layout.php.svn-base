<?php

/**
 * @file form_cad_layout.php
 * @author knienkotter
 * @version 28/01/2013 13:00:11
 * @since 28/01/2013 13:00:11
 * @package SASCAR form_cad_layout.php 
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


<!-- Formulário para inclusão de novo layout  -->
<form name="form" id="formLayout" method="POST" action="<?=$_SERVER['PHP_SELF'];?>" >
<center>
	<table class="tableMoldura">
		<tr class="tableTitulo">
			<td><h1>Cadastro Layout</h1></td>
		</tr>
		<tr height="20" class="div_acao_cadastrar" >
			<td>
				<span id="div_msg_layout" class="msg"></span>
		    </td>
		</tr>
	    <tr class="div_acao_cad_layout">
	        <td align="center">
	            <table class="tableMoldura">
	                <tr class="tableSubTitulo">
	                    <td colspan="4"><h2></h2></td>
	                </tr>
	                <tr>
	                    <td class="td_label"><label for="nomeLayout">Nome*:</label></td>
	                    <td width="30%">
	                    	<input type="hidden" name="idLayout" id="idLayout" />
	                    	<input type="text" size="50" maxlength="50" name="nomeLayout" id="nomeLayout" />
	                    </td>
	                    <td class="td_label"><label for="padraoLayout">Padrão:</label></td>
	                    <td width="25%">
	                    	<input type="checkbox" id="padraoLayout" value="padrao" > 
	                    </td>
	                </tr>
	                <tr>
	                    <td class="td_label"><label for="nomeLayout">Assunto*:</label></td>
	                    <td width="30%" colspan="3">
	                    	<input type="text" size="50" maxlength="50" name="assuntoLayout" id="assuntoLayout" />
	                    </td>
	                </tr>
	                <tr>
	                    <td class="td_label"><label class="td_label" for="htmlLayoutEdicao">Layout*:</label></td>
	                    <td colspan="3"></td>
	                </tr>
	                <tr>
	                    <td colspan="4" align="center">
	                    	<textarea name="htmlLayoutEdicao" id="htmlLayoutEdicao" cols="125" rows="15" ></textarea>
	                    	<br><img src="images/help10.gif" style="margin: 5px 10px 0 15px"> <span style="font-size:10px">Para incluir os dados "Nome", "Login" e "Senha" do cliente, utilize as respectivas vari&aacuteveis: $usulnome, $usullogin e $usulsenha</span>
	                    </td>
	                </tr>
	                
	                <tr><td colspan="4"><br></td></tr>
	                
	                <tr class="tableRodapeModelo1" style="height:23px;">
	                    <td align="center" colspan="4">
	                        <input type="button" name="bt_gravar_layout" id="bt_gravar_layout" value="Gravar" class="botao" style="width:90px;">
	                        <input type="button" name="bt_limpar_layout" id="bt_limpar_layout" value="Limpar" class="botao" style="width:90px;">
	                        <input type="button" name="bt_excluir_layout" id="bt_excluir_layout" value="Excluir" class="botao" style="width:90px;">
	                        <input type="button" name="bt_voltar" id="bt_voltar" value="Voltar" class="botao" style="width:90px;">
	                    </td>
	                </tr>
	            </table>
	            <div id="processandoLayout" style="width: 100%; text-align:center;"></div>	
	        </td>
	    </tr>
	    <tr>
	    	<td colspan="4">
	    		<center>
				    <div class="processando_excluir" style="display: none;"><img src="images/loading.gif" alt="" /></div>
				</center>
	    	</td>
	    </tr>
		
		<!-- Fim do bloco ação -->
		
		<tr id="div_lista_layout">
	        <td align="center">
	            <table class="tableMoldura">
	                <tr class="tableSubTitulo">
	                    <td width="40%"><h2>Nome </h2></td>
	                    <td><h2>Assunto </h2></td>
	                    <td><h2>Padrão </h2></td>
	                </tr>
	                <?php foreach($this->comboLayouts as $layout): ?>
	                <tr id="idLayout_<?php echo $layout['lwkoid'] ?>" class="editaLayout <?php echo $layout['lwkpadrao']=='t'?'layoutPadrao':'' ?>">	                    
	                    <td><?php echo utf8_decode($layout['lwkdescricao']) ?></td>                    
	                    <td><?php echo utf8_decode($layout['lwkassunto_email']) ?></td>
	                    <td><input type="checkbox" <?php echo $layout['lwkpadrao']=='t'?'checked="checked"':'' ?> disabled="disabled" ></td>	                    
	                </tr>
	                <?php endforeach; ?> 
	            </table>
	        </td>
	    </tr>  
	    
	</table>
</center>
</form>
<?
include 'lib/rodape.php';
?>