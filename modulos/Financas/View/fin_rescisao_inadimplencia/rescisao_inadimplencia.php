<?php 

include_once '_header.php';

$displayAlerta 	= 	($this->msg['tipo'] == 'alerta') 	? 'block' : 'none';
$displaySucesso = 	($this->msg['tipo'] == 'sucesso') 	? 'block' : 'none';
$displayErro 	= 	($this->msg['tipo'] == 'erro') 		? 'block' : 'none';

?>

<div class="modulo_titulo">Rescisão por Inadimplência</div>

<div class="modulo_conteudo">
       	
<div class="mensagem info" id="msginfo">Campos com * são obrigatórios.</div>
<div class="mensagem alerta invisivel"  id="msgalerta" style="display: <?php echo $displayAlerta?>;"><?php echo $this->msg['mensagem'] ?></div>
<div class="mensagem sucesso invisivel" id="msgsucesso" style="display: <?php echo $displaySucesso?>;"><?php echo $this->msg['mensagem'] ?></div>
<div class="mensagem erro invisivel"    id="msgerro" style="display: <?php echo $displayErro?>;"><?php echo $this->msg['mensagem'] ?></div>

	<div class="bloco_titulo">Dados Principais</div>
	<div class="bloco_conteudo">		
    	<div class="formulario"> 
        	<form name="frm_importar" id="frm_importar" method="POST" action="" enctype="multipart/form-data">
        		<input type="hidden" id="acao" name="acao" value=""/>
        		
	        	<div class="campo maior">					
	            	<label for="arquivo_csv">Arquivo: *</label>
	                <input type="file" id="arquivo_csv" name="arquivo_csv" size="40"/>
				</div>					
	            <div class="clear"></div>				
            </form>      
       	</div>
       						
	</div>
        
	<div class="bloco_acoes">
		<button type="button" id="confirmar" name="confirmar">Confirmar</button>
	</div>
	<div class="separador"></div>
	<div style="margin: 0 auto; text-align: center; display: none;" class="loading">
		<img src="images/loading.gif" alt="" />
	</div>