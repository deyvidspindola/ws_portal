  <body>
 
	 <div class="modulo_titulo">Conciliação de Transações de Débito Automático</div>	
		
		<div class="modulo_conteudo">
				
		<?php if ($retorno['tipo'] !='') { ?>		
			 <div class="mensagem <?php print $retorno['tipo']; ?>" id="composicao"><?php print $retorno['msg']; ?></div>
	   <?php }else{ ?>	
	   
	          <div class="mensagem" id="composicao" style='display:none;'></div>
	   <?php }?>
	
	    <div class="mensagem info">(*) Campos de preenchimento obrigatório.</div> 
	   
		<form name="form" action="fin_da_conciliacao_transacoes.php" id="form" method="POST" enctype="multipart/form-data">
		
			<div class="bloco_titulo">Arquivo de Conciliação</div>
				<div class="bloco_conteudo" >	
				    <div class="formulario">
				    
				        <div class="campo data periodo">
				           <div class="dt_credito">
				             <label for="data_credito">Dt Cr&eacute;dito em C/C *</label>
				             <input type="text" id="data_credito" name="data_credito"  class="campo" />
				          </div>
				        </div>
				    
				        <div class="clear"></div>
				    
				        <div class="campo medio" id="p_scents" style="width: 445px;" >
				            <label for="autocomplete_1">Arquivo *</label>
				            <input class="upload" style="border: 0px solid #C0C0C0;" id="upload" type="file" name="upload_0" placeholder="Input Value" size="40" />	            
				        </div>
				        <div class="clear"></div> <br />
				        <button id="addScnt" type="button">Adicionar</button>      
				    </div>	 
				</div>
				
				
			<div class="bloco_acoes">
			    <button type="submit" id='enviar_arquivo'>Processar Arquivo(s)</button>    
			</div>
				
			<div id="loader_1" class="carregando" style="display:none;"></div> 	
			
		</form>
		
	</div>
<body>




