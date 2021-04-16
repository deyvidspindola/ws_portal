<?php
 require_once '_header.php';
?>

<div class="modulo_titulo">Histórico de Alteração Vencimentos de Títulos</div>
	<div class="modulo_conteudo">
	
		<div class="mensagem info">(*) Campos de preenchimento obrigatório.</div>
		
		<div id="mensagem"   
		
			<?php if($msg == ''){?> 
			         style='display:none'
			<?php }else{ 
				    echo "class='$classe' ";
			      } ?>>
			
			<?php if($msg != '') {echo $msg;}?>
		
		</div>
		
		<div class="bloco_titulo">Dados para Pesquisa</div>
		<div class="bloco_conteudo">
			<div class="formulario">
			
				<form name="busca_dados" id="busca_dados" method="post" action=""> 
				 	<input type="hidden" name="acao" id="acao" value="pesquisar" />				

					<div class="campo data periodo">
                        <div class="inicial">
                            <label for="data_ini">Data inicial * </label>
                            <input type="text" id="data_ini" name="data_ini"  class="campo" value="<?php echo  isset($_POST['data_ini']) && $_POST['data_ini'] != '' ? $_POST['data_ini'] : $_GET['data_ini'];?>" />
                        </div>
                        <div class="campo label-periodo">a</div>
                        <div class="final">
                            <label for="data_fim">Data final * </label>
                            <input type="text" id="data_fim" name="data_fim" class="campo" value="<?php echo isset($_POST['data_ini']) && $_POST['data_fim'] != '' ? $_POST['data_fim'] : $_GET['data_fim']; ?>"/>
                        </div>
	                </div>
					
					<div class="clear" ></div>
					
					<div class="campo maior">
                        <label for="nome">Cliente: </label> 	      
                        <input name="usuario_nome" id="usuario_nome" value="<?php echo isset($_POST['usuario_nome']) && $_POST['usuario_nome'] != '' ? $_POST['usuario_nome'] : $_GET['usuario_nome'];?>" class="campo" type="text" />                            
                    </div>
                    <div class="clear"></div>
                    <div class="campo medio">
                        <label for="cpf_cgc">CNPJ / CPF:</label> 	      
                        <input type="text" name="filter_cpf_cnpj_gerador" id="filter_cpf_cnpj_gerador" class="campo" value="<?php echo isset($_POST['filter_cpf_cnpj_gerador']) && $_POST['filter_cpf_cnpj_gerador'] != '' ? $_POST['filter_cpf_cnpj_gerador'] : $_GET['filter_cpf_cnpj_gerador'];?>" />
                    </div>
					
					<div class="clear"></div>
				
				</form>
				
			</div>
		</div>
		
		 <div id="loader_1" class="carregando" style="display:none;"></div>  
		
		<div class="bloco_acoes">
			<button type="button" name="buscar" id="buscar">Pesquisar</button>
			<!--button type="button" name="limpar_pesquisa" id="limpar_pesquisa">Limpar</button-->
		</div>
	</div>
	
    <div class="separador"></div>    
