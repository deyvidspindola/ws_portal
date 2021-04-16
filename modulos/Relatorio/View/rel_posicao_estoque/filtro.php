<?php 
cabecalho();
//Carrega bibliotecas básicas
require_once "lib/config.php";
require_once "lib/init.php"; 

include("calendar/calendar.js");
include("lib/funcoes.js");
?>

<!-- CSS -->
<link type="text/css" rel="stylesheet" href="lib/layout/1.1.0/style.css" />
<link rel="stylesheet" href="modulos/web/css/rel_posicao_estoque.css" type="text/css"  />
<link type="text/css" rel="stylesheet" href="lib/layout/1.1.0/jquery-ui/jquery-ui.custom.min.css" />
<link type="text/css" rel="stylesheet" href="includes/css/base_form.css"> 
<link rel="stylesheet" href="modulos/web/css/lib/loading.css" type="text/css"  /> 
<link type="text/css" rel="stylesheet" href="lib/css/style.css" />
<!-- JAVASCRIPT --> 
<script type="text/javascript" src="lib/layout/1.1.0/jquery/jquery.min.js"></script>
<script type="text/javascript" src="lib/layout/1.1.0/jquery-ui/jquery-ui.custom.min.js"></script>
<script type="text/javascript" src="modulos/web/js/lib/jquery.maskMoney.js"></script>
<script type="text/javascript" src="lib/layout/1.1.0/jquery/jquery.maskedinput.min.js"></script>
<script type="text/javascript" src="lib/layout/1.1.0/bootstrap.js"></script>
<script type="text/javascript" src="modulos/web/js/lib/loading.js"></script>       
<script type="text/javascript" src="js/jquery.validate.js"></script>   
<script type="text/javascript" src="includes/js/mascaras.js"></script>
<script type="text/javascript" src="includes/js/validacoes.js"></script> 
<script type="text/javascript" src="includes/js/auxiliares.js"></script>
<script type="text/javascript" src="modulos/web/js/rel_posicao_estoque.js"></script>


<div class="modulo_titulo">Posição Estoque</div>
<div class="modulo_conteudo">
   
    <div class="mensagem sucesso <?php if (empty($this->mensagem_sucesso)){echo "invisivel"; }?>"><? echo $this->mensagem_sucesso; ?></div>
     
    <div class="mensagem info">Campos com * são obrigatórios.</div>
   
    <div id="mensagem_alerta"  style="display:none" class="mensagem alerta "><? echo "Existem campos obrigatórios não preenchidos." ?></div>
 
  	<div id="mensagem_nenhum_registro"  style="display:none" class="mensagem alerta "><? echo "Nenhum registro encontrado." ?></div>
 

    <div class="bloco_titulo">Dados para Pesquisa</div>
    <div class="bloco_conteudo">
    <form name="filtro">
	<!--  Controlador de Ações -->
    <input type="hidden" id="acao" name="acao" />
        <div class="formulario">
			<div class="campo menor">
				<label for="data_posicao" id="label_data_posicao">Data Posição *</label>
				<select name="data_posicao" id="data_posicao" tabindex="1">
				<option value="">Escolha</option>
				<?php foreach ($dataPosicaoList as $data): ?>
					<option value="<?php echo $data['data']?>"><?php echo $data['data']?></option>
				<?php endforeach;?>
			    </select>
			</div>
			<div class="campo menor">
				<label for="combo">UF</label>
				<select name="uf" id="uf"  tabindex="2">
				<option value="">Escolha</option> 
				<?php foreach ($ufList as $uf): ?>
				<option value="<?php echo $uf['uf']?>"><?php echo $uf['uf']?></option>
				<?php endforeach;?>
		    </select>
			</div>
			<div class="campo maior">
			<div style="float:left;width:1px;height:1px;">		 
	   	        <img class="img_progress" id="cid_progress" alt="Carregando..." src="images/progress4.gif"  />
   	        </div>
			<label for="combo">Cidade</label>
			    <select id="cidade" name="cidade" tabindex="3">
 			    <option value="">Escolha UF</option>
                </select>
			</div>
			<div class="clear"></div>

			<div class="campo maior">
				<label for="combo">Status Representante</label>
				<select name="status_representante" id="status_representante" tabindex="4">
					<option value="">Escolha</option>
					<?php foreach ($statusRepresentanteList as $id=>$statusRepresentante): ?>
						<option value="<?php echo $id?>"><?php echo $statusRepresentante ?></option>
					<?php endforeach;?>
			   </select>
			</div>
			<div class="campo maior">	
				<div style="float:left;width:1px;height:1px;">		 
	   	        <img class="img_progress" id="rep_progress" alt="Carregando..." src="images/progress4.gif"  />
	   	        </div>
		       <label for="combo">Representante</label>
				
				<select name="representante" id="representante" tabindex="5">
					<option value="">Escolha</option>
					<?php foreach ($representanteList as $representante): ?>
						<option value="<?php echo $representante['id']?>"><?php echo utf8_decode($representante['nome']) ?></option>
					<?php endforeach;?>
   				</select>											
   
			</div>
			<div class="clear"></div>
			
			<div class="campo maior">
				<label for="combo">Tipo Item</label>
		          <select name="tipo_item" id="tipo_item" tabindex="6">
					<option value="">Escolha</option>
					<?php foreach ($tipoItemList as $id=>$tipoItem): ?>
						<option value="<?php echo $id?>"><?php echo $tipoItem?></option>
					<?php endforeach;?>
				  </select>
			</div>
			<div class="clear"></div>
			
        </div>
        </form>
    </div>
    <div class="bloco_acoes">
    <input type="button" class="botao" name="pesquisar" id="pesquisar" value="Pesquisar" tabindex="7" /> 
    <input type="button" class="botao" name="gerar_csv" id="gerar_csv" value="Gerar CSV" tabindex="8" />  
    </div>
    <div id="resultado_progress" align="center" style="display:none">
		<img src="modulos/web/images/loading.gif" alt="Carregando..." />
	</div>
    <div class="separador"></div>
    <div id="resultado_relatorio">
								
	</div>
	<div id="caixa">
	<div class="bloco_titulo">Download</div>
	<div class="bloco_conteudo">
		<div class="conteudo centro">
			<a href="#" id="linkdownload" tabindex="9">
				<img src="images/icones/t3/caixa2.jpg">
				<br> 
				<span id="datainversa"></span>
			</a>
		</div>
	</div>
	</div>   
	
      
</div>
<div class="separador"></div>

<br/>	

<?php 
include ("lib/rodape.php");
?>