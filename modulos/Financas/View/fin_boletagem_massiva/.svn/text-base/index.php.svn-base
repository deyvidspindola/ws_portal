<?php
 require_once '_header.php';
?>

<div class="modulo_titulo">Boletagem Massiva</div>
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
		
		<div class="bloco_titulo">Busca de Campanha Cadastrada</div>
		<div class="bloco_conteudo">
			<div class="formulario">
			
			 <form name="busca_dados" id="busca_dados" method="post" action=""> 
			 
			  <input type="hidden" name="acao" id="acao" value="pesquisar" />
			
				<div class="campo maior">
					<label for="nome_campanha">Nome da Campanha</label> 
					<input id="nome_campanha" name="nome_campanha" maxlength="62" value="<?php echo  isset($_POST['nome_campanha']) && $_POST['nome_campanha'] != '' ? $_POST['nome_campanha'] : $_GET['nome_campanha']; ?>" class="campo" type="text">
				</div>
				
				<div class="clear" ></div>
				
				 <div class="campo data periodo">
                            <div class="inicial">
                                <label for="data_ini">Data de Cadastro* </label>
                                <input type="text" id="data_ini" name="data_ini"  class="campo" value="<?php echo  isset($_POST['data_ini']) && $_POST['data_ini'] != '' ? $_POST['data_ini'] : $_GET['data_ini'];?>" />
                            </div>
                            <div class="campo label-periodo">a</div>
                            <div class="final">
                                <label for="data_fim">&ensp;</label>
                                <input type="text" id="data_fim" name="data_fim" class="campo" value="<?php echo isset($_POST['data_ini']) && $_POST['data_fim'] != '' ? $_POST['data_fim'] : $_GET['data_fim']; ?>"/>
                            </div>
                 </div>
				
				<div class="clear" ></div>
				
				<div class="campo data periodo">
					<label for="data_vencimento">Vencimento da Campanha</label> 
					 <input type="text" id="data_vencimento" name="data_vencimento" class="campo" value="<?php echo  isset($_POST['data_vencimento']) && $_POST['data_vencimento'] != '' ? $_POST['data_vencimento'] : $_GET['data_vencimento'];?>"/>
				</div>
				<div class="clear"></div>
				
			</form>
				
			</div>
		</div>
		
		 <div id="loader_1" class="carregando" style="display:none;"></div>  
		
		<div class="bloco_acoes">
			<button type="button" name="buscar" id="buscar">Buscar</button>
			<button type="button" name="limpar_pesquisa" id="limpar_pesquisa">Limpar</button>
			
			<?php if($funcao_botao_novo){?>
			    <button type="button" name="novo"  <?php echo $botao;?>  id="novo">Novo</button>
			<?php }?>
			
			
		</div>
	</div>
	
    <div class="separador"></div>    
