<?php 
cabecalho();
include("calendar/calendar.js");
include("lib/funcoes.js");
include("lib/funcoes.php");
// echo "<pre>".print_r($_POST, 1)."</pre>";
?>
<head>

     <!-- CSS -->
    <link type="text/css" rel="stylesheet" href="lib/layout/1.1.0/style.css" />
    <link type="text/css" rel="stylesheet" href="calendar/calendar.css"/>
    <link type="text/css" rel="stylesheet" href="lib/layout/1.1.0/jquery-ui/jquery-ui.custom.min.css" />
  
  	<style type="text/css">
		.disabled {
			font-weight: bold;
			color: silver !important;
			background-color: #efefef;
		}
     </style>
  
    <!-- JAVASCRIPT -->
    <script type="text/javascript" src="lib/layout/1.1.0/jquery/jquery.min.js"></script>
    <script type="text/javascript" src="js/jquery.maskedinput.js"></script>
    <script type="text/javascript" src="lib/layout/1.1.0/jquery-ui/jquery-ui.custom.min.js"></script>
    <script type="text/javascript" src="lib/layout/1.1.0/bootstrap.js"></script>
    <script type="text/javascript" src="includes/js/validacoes.js"></script>    
    <script type="text/javascript" src="includes/js/calendar.js"></script>
    <script type="text/javascript" src="modulos/web/js/fin_macros_micros_motivos_insere_edita.js?rand=<?=rand(1, 9999);?>"></script>
    <script type="text/javascript" src="modulos/web/js/lib/jquery.maskMoney.js"></script> 
    <script type="text/javascript" src="modulos/web/js/lib/jquery.maskedinput-1.3.min.js"></script> 

</head>


<body>

    <div class="modulo_titulo">Macros Micros Motivos</div>
    
        <div class="modulo_conteudo">
        
            <?php if(!empty($mensagemInformativaNaoIncluido)){
                foreach ($mensagemInformativaNaoIncluido as $msg){
                    echo '<div id="mensagem" class="mensagem alerta"> '. $msg . '</div>';
                }
            }
            ?>
            
        <?php if (!empty($mensagemInformativa) ): 
        
                    $class_msg =  $mensagemInformativa['status'] === "OK" ? 'mensagem sucesso' : 'mensagem alerta' ;  ?>

         	<div id="mensagem" class="<?php echo $class_msg;?>"><?php echo $mensagemInformativa['msg']; ?></div>
        
		<?php else :?>
           
            <div id="mensagem"></div>
            
       <?php endif;?>    

<!--             <div class="mensagem info">(*) Campos de preenchimento obrigatório.</div>  -->
            
            <div class="bloco_titulo">Inserir Macros Micros Motivos</div>
      
                <div class="bloco_conteudo">
                            
                     <div class="formulario">
                            
                           <form name="frm" id="frm" method="post" action="" enctype="multipart/form-data"> 
                            
                            <input type="hidden" name="acao" id="acao" />
                            
                            <!-- Caso o parfoid seja diferente de vazio, o parâmetro será atualizado, senão, o parâmetro será inserido.-->
							<input type="hidden" id="pfmoid" name="pfmoid" value="<?php echo $_POST['pfmoid']; ?>" />

                            <fieldset style="width:1000px; background: none; border: 1px solid silver; padding: 6px; margin-bottom: 10px">
					         	<legend>Nível *</legend>

                                <label>
                                    <input <?php echo (isset($_POST['pfmoid']) &&  !empty($_POST['pfmoid'])) ; ?> type="radio" name="nivel"  value="'MACRO'"  class="radio" <?php echo $_POST['nivel'] == 'MACRO'  || !isset($_POST['nivel']) ? 'checked="checked"' : '' ; ?> />
                                    Macro Motivo
                                </label>
								<label>
									<input <?php echo (isset($_POST['pfmoid']) &&  !empty($_POST['pfmoid'])); ?> type="radio" name="nivel"  value="'MICRO'"  class="radio" <?php echo ($_POST['nivel'] == 'MICRO') ? 'checked="checked"' : '' ;?> />
                                    Micro Motivo
								</label>

                                <div class="clear"></div>
                                <div class="campo maior">
                                    <label for="cliente">Descrição</label>

                                    <input class="campo" type="text" id="descricao" name="descricao" value="<?php echo $_POST['descricao']; ?>"size="25" maxlength="50" />
                                </div>
                            </fieldset>

                             <div class="clear"></div>

                      </form>
                    </div>
                </div>  
           
      <div id="loader_1" class="carregando" style="display:none;"></div>     
           
      <div  class="bloco_acoes">
         <button type="button" id="btn_confirmar" name="btn_confirmar">Confirmar</button>
         <button type="button" id="btn_retornar" name="btn_retornar">Retornar</button>
		<!-- Botão excluir somente ficará visível quando o usuário estiver editando -->
		<?php if (isset($_POST['pfmoid']) && !empty($_POST['pfmoid'])):?>
		    <button type="button" id="btn_excluir" name="btn_excluir">Excluir</button>
		<?php endif;?>
      </div>                  

      
      
	</div>
</body>

<?php 
//include ("lib/rodape.php");