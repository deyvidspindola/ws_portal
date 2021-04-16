

<?php require_once _MODULEDIR_ . "Relatorio/View/rel_pagina_funcao_user/cabecalho.php"; ?>    

<style type="text/css">
            <!--
            @import url("includes/css/base_form.css");
            @import url("includes/css/calendar.css");
            -->            
        </style>
        <script language="javascript" type="text/javascript" src="includes/js/calendar.js"></script>

    <!-- Mensagens-->
    <?php
        if(isset($this->view['mensagem']) )
        {
            echo "<div class='mensagem erro'>". $this->view['mensagem'] . "</div>";
        }
        if(isset($this->view['resultado']['erro']))
        {
            echo "<div class='mensagem erro'>". $this->view['resultado']['erro'] . "</div>";
        }
    ?>

    <form id="frm_pesquisar" name="frm_pesquisar"  method="post" action="<?php echo $_SERVER['PHP_SELF']?>">
        <div class="bloco_titulo">Filtros</div>
        <div class="bloco_conteudo">
            <div class="formulario">
                <div class="campo maior">
                    <span>Buscar Por:</span>
                    <select class="campo" id="tipo" name="tipo">
                        <option value="">-- Escolha --</option>
                        <option value="P">PÁGINAS</option>
                        <option value="F">FUNÇÕES ESPECIAIS</option>
                        
                    </select>
                </div>
               
                <div class="clear"></div>
                <div class="campo maior">
                    <button name="consultar" value="consultar"><i class="ui-icon ui-icon-search" style="float: left; color: #333;"></i>Consultar</button>
                    <button name="exportar" value="exportar"><i class="ui-icon ui-icon-script" style="float: left; color: #333;"></i>Exportar consulta</button>
                </div>
                <div class="clear"></div>

            </div>
        </div>

    </form>

    <br class="clear">
    <br class="clear">
    <?php //print_r($param);?>
    <?php if(isset($param['options'])){?>
        <div class="bloco_titulo">Resultado da pesquisa</div>
        <div class="bloco_conteudo">
            
	            <form method ="post" name = 'confirmar' action = '<?php echo $_SERVER['PHP_SELF']."?acao=gerar"?>'>
	            	<?php if ($_POST['tipo'] == 'F') 
		            {
			             foreach ($param['options'] as $k => $v)
			             {	          	             
			         ?>
			               
			                <div style = 'width:270px;float:left;'>
			                    	<input type = "checkbox" name="options[]" value= '<?php print_r($v['funcoid']);?>'><?php echo($v['funcdescricao']);?>
			                </div>
	               <?php }
		              
		            }             
		            else 
		            {
		             
		                 foreach ($param['options'] as $k => $v)
		                 {
		                 ?>
		               
		                	<div style = 'width:270px;float:left;'>
		                    	<input type = "checkbox" name="options[]" value= '<?php print_r($v['pagoid']);?>'><?php echo($v['pagdescricao']);?>
		               	 	</div>
		            <?php }
		              
	             	  } 
		            ?>
		              
		            <br style = 'clear:both'>
		            <br style = 'clear:both'>
		            <br style = 'clear:both'>
		              
		            <input type = hidden name = 'tipo_acao' value = <?php echo $_POST['tipo']?>>
		            <input type="submit" name="btn_confirmar" id="btn_confirmar" value="Confirmar" class="botao">
	            </form>
              
       </div>
    <?php } ?>
    
<?php require_once _MODULEDIR_ . "Relatorio/View/rel_pagina_funcao_user/rodape.php"; ?>