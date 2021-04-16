<?php include 'header.php';?>
<form name="frm_pesquisar " id="frm_pesquisar" method="POST" action="#">
	<div class="modulo_titulo">Termo Aditivo de Serviços/Software</div>		
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
                                <div  class="campo data">
                                    <label>Período:</label> 	      
                                    <input class="campo"  type="text" name="dt_ini" id="dt_ini" maxlength="10" value="" />  
                                </div> 
                                <div class="campo label-periodo">a</div>  
                                <div class="campo data">
                                    <label>&nbsp;</label>            
                                    <input  class="campo"  type="text" name="dt_fim" id="dt_fim" maxlength="10" value= "" />
                                </div>
                                
                                <div class="clear"></div>
                                
                                <div class="campo maior">
                                    <label id="cpx_pesquisa_cliente_nome_label" for="cpx_pesquisa_cliente_nome">Cliente:</label>
                                    <input type="text" class="campo" style="z-index: 1" name="cpx_pesquisa_cliente_nome" id="cliente">
                                    <label style="display: none;font-size: 10px; color: #555;font-weight: bold;" class="componente_nenhum_cliente">Nenhum cliente encontrado.</label>
                                </div>
                            </td>
                            <td>
                                <div class="campo medio">					
                                    <label for="placa">Placa:</label>
                                    <input type="text" id="placa" name="placa" value="" class="campo" />
                                </div>
                                <div class="campo medio">					
                                    <label for="status">Status:</label>
                                    <select name="status" id="status">
                                        <option value="">Escolha</option>
                                        <?php
                                            if($status != null){
                                                foreach($status as $row){			
                                                     echo "<option value='".$row['tasesoid']."'>".$row['tasesdescricao']."</option>";
                                                }
                                            }
                                        ?>
                                    </select>
                                </div>
                                
                                <div class="clear"></div>
                                
                                <div class="campo medio">					
                                    <label for="nm_aditivo">Nº do Aditivo:</label>
                                    <input type="text" id="nm_aditivo" name="nm_aditivo" value="" class="campo" />
                                </div>
                                <div class="campo medio">					
                                    <label for="servico">Serviço:</label>
                                    <select name="servico" id="servico">
                                        <option value="">Escolha</option>
                                        <?php
                                            if($servico != null){		
                                                foreach($servico as $row){			
                                                   echo "<option value='".$row['obroid']."'>".$row['obrobrigacao']."</option>";
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
<!-- FIM - Termo Aditivo de Serviços -->