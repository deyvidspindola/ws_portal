<?php include 'header.php';?>
<form name="frm_pesquisar " id="frm_pesquisar" method="POST" action="#">
	<div class="modulo_titulo">Nota Fiscal Monitoramento Diferido</div>		
	<div class="modulo_conteudo">
		<div class="mensagem alerta" id="msgalerta" style="display:none;"></div>
		<div class="mensagem sucesso" id="msgsucesso" style="display:none;"></div>
		<div class="mensagem erro" id="msgerro" style="display:none;"></div>
        <div class="mensagem info" id="msginfo" style="display:none;"></div>
		
		<ul class="bloco_opcoes">
			<!--<li class="ativo">
				<a href="javascript:void(0);" id="aba_pesquisar">Pesquisar</a>
			</li>
			<li class="">
				<a href="javascript:void(0);" id="aba_novo">Novo</a>
			</li>-->
		</ul>
        
		<div id="frame_content">
            <div class="bloco_titulo">Dados da Pesquisa</div>
            <div class="bloco_conteudo">
                <div class="formulario">
                    <table width="100%">
                        <tr>
                            <td>
                                <div  class="campo mes_ano">
                                    <label>Período:</label> 	      
                                    <input class="campo"  type="text" name="dt_ini" id="dt_ini" maxlength="7" value="" />  
                                </div>
                                
                                <div class="campo medio">					
                                    <label for="nota">Nota (N&uacute;mero refer&ecirc;ncia):</label>
                                    <input type="text" id="nota" name="nota" value="" class="campo" />
                                </div>
                                
                                <div class="campo menor">					
                                    <label for="serie">S&eacute;rie:</label>
                                    <select name="serie" id="serie">
                                        <option value="">Escolha</option>
                                        <?php
                                            if(!empty($serie)){
                                                foreach($serie as $row){
                                                     echo "<option value='".$row['nfsserie']."'>".$row['nfsserie']."</option>";
                                                }
                                            }
                                        ?>
                                    </select>
                                </div>
                            </td>
                        </tr>
                    </table>		
                </div>
            </div>		
            
            <div class="bloco_acoes">
                <button type="button" id="pesquisar">Pesquisar</button>
                <button type="button" id="novo">Novo</button>
            </div>
        </div>
        
        <div id="resultado_progress" style="display: none;" align="center">
            <img src="modulos/web/images/loading.gif" alt="Carregando...">
        </div>
        
        <div class="separador"></div>
        
        <div id="frame01"></div>        
    </div>	
</form>
<!-- FIM - Nota Fiscal Monitoramento Diferido -->