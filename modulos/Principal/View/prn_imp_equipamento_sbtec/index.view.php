<?php include 'header.php';?>
<form name="frm_importacao" id="frm_importacao" method="POST" action="#" enctype="multipart/form-data">
	<input type="hidden" name="acao" id="acao" value=""/>
    <div class="modulo_titulo">Importação de Equipamentos SBTEC</div>		
    <div class="modulo_conteudo">
    	<?php if($dados['msgalerta'] != ''){?>
        	<div class="mensagem alerta" id="msgalerta" style="display:block;"><?=$dados['msgalerta']?></div>
        <?php } else{?>
        	<div class="mensagem alerta" id="msgalerta" style="display:none;"></div>
        <?php }?>
        
        <?php if($dados['msgsucesso'] != ''){?>
        	<div class="mensagem sucesso" id="msgsucesso" style="display:block;"><?=$dados['msgsucesso']?></div>
        <?php } else{?>
        	<div class="mensagem sucesso" id="msgsucesso" style="display:none;"></div>
        <?php }?>
        
        <?php if($dados['msgerro'] != ''){?>
        	<div class="mensagem erro" id="msgerro" style="display:block;"><?=$dados['msgerro']?></div>
        <?php } else{?>
        	<div class="mensagem erro" id="msgerro" style="display:none;"></div>
        <?php }?>
        
        <?php if($dados['msginfo'] != ''){?>
        	<div class="mensagem info" id="msginfo" style="display:block;"><?=$dados['msginfo']?></div>
        <?php } else{?>        	
        	<div class="mensagem info" id="msginfo" style="display:none;"></div>
        <?php }?>
        
        <ul class="bloco_opcoes">
            <!-- adicionar aqui as abas -->
        </ul>

        <div id="frame_content">
            <div class="bloco_titulo">Importação</div>
            <div class="bloco_conteudo">
                <div class="formulario">
                    <table width="100%">
                        <tr>
                            <td>
                            	<div class="campo maior">
                            		<label>Arquivo:</label>
                                	<input type="file" class="campo" name="file" id="file">
                            	</div>                                
                            </td>                       
                        </tr>
                        <tr>
                            <td>
                            	<div class="campo maior">
                            		<label>Somente arquivos <b>.csv</b> são permitidos.</label>
                            	</div>                                
                            </td>                        
                        </tr>
                        <tr>
                            <td>
                            	<div class="campo">                            		
                            		<label>Não conter linha em branco no início e no final do arquivo.</label>
                            		<label>Não conter espaço em branco entre as informações, apenas a quebra de linha.</label>
                            	</div>                                
                            </td>                        
                        </tr>
                    </table>		
                </div>
            </div>		

            <div class="bloco_acoes">
            	<button type="button" id="processar">Processar</button>
            </div>
        </div>

        <div id="resultado_progress" style="display: none;" align="center">
            <img src="modulos/web/images/loading.gif" alt="Carregando...">
        </div>

        <div class="separador"></div>

        <div id="frame01">
        	<?php
	        	if($dados['html'] != ''){
	        		echo $dados['html'];
	        	}
        	?>
        </div>        
    </div>	
</form>