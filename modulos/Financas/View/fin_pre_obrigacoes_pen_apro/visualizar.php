<!--lista-->

<script>
  $(function() {
    $( "#dialog" ).dialog({
      autoOpen: false,
      show: {},
      hide: {}
    });
  });
 
  </script>
<div id="dialog" title="<?php print utf8_decode("Tag SasWeb");?>">
 <link type="text/css" rel="stylesheet" href="modulos/web/css/prn_modificacao_contrato.css" />
 <div id="mensagem_previsao_iniciada" class="mensagem info <?php if (empty($this->view->mensagemPrevisao)): ?>invisivel<?php endif; ?>">
	    <?php echo $this->view->mensagemPrevisao; ?>
	</div>
	
    <div id="mensagem_erro" class="mensagem erro <?php if (empty($this->view->mensagemErro)): ?>invisivel<?php endif;?>">
        <?php echo $this->view->mensagemErro; ?>
    </div>
	
    <div id="mensagem_alerta" class="mensagem alerta <?php if (empty($this->view->mensagemAlerta)): ?>invisivel<?php endif;?>">
        <?php echo $this->view->mensagemAlerta; ?>
    </div>
	
    <div id="mensagem_sucesso" class="mensagem sucesso <?php if (empty($this->view->mensagemSucesso)): ?>invisivel<?php endif;?>">
        <?php echo $this->view->mensagemSucesso; ?>
    </div>
  <div class="formulario">         
		<div class="clear"></div>
		<div class="bloco_titulo"><?php print utf8_encode($visualizarDetalhe[0]['tipo']);?></div>		
		<div class="bloco_conteudo">
            	<div class="formulario">
	            	<div class="conteudo">
	            	<?php if($this->view->parametros->status == "F"){?>
	            		<div class="label coluna-a"><?php print utf8_encode('Pacote:'); ?></div><span class="medio"><?php print utf8_encode($visualizarDetalhe[0]['pacote']);?></span>
	            		<?php } ?>
	            		<div class="label coluna-a"><?php print utf8_encode('Descrição:'); ?></div><span> <?php print utf8_encode($visualizarDetalhe[0]['descricao']);?></span>
	            			<div class="clear"></div>
	            		<div class="label coluna-a"><?php print utf8_encode('Tag:'); ?></div><span class="medio"><?php print utf8_encode($visualizarDetalhe[0]['tag']);?></span>
	            		<div class="label coluna-a"><?php print utf8_encode('Obrigação Financeira:'); ?></div><span class="medio"><?php  print utf8_encode($visualizarDetalhe[0]['obrigacao_financeira']);?></span>
	            	    <div class="clear"></div>
	            	    <?php if($visualizarDetalhe[0]['status'] =="Aprovada"){?>	            	
	            		<div class="label coluna-a"><?php print utf8_encode('Aprovado por:'); ?></div><span class="medio"><?php print utf8_encode($visualizarDetalhe[0]['aprovador']);?>   </span>
	        			<div class="label coluna-a"><?php print utf8_encode('Data de Aprovação:'); ?></div><span class="medio"> <?php print $var = $visualizarDetalhe[0]['data_aprovacao']!=""? implode("/",array_reverse(explode("-",$visualizarDetalhe[0]['data_aprovacao']))) : ""; ?></span>
	            		<div class="clear"></div>
	            		<?php } ?>
	            	    <div class="label coluna-a"><?php print utf8_encode('Status:'); ?></div><span class="medio"><?php print utf8_encode($visualizarDetalhe[0]['status']);?></span>
	            	    <div class="label coluna-a"><?php print utf8_encode('Obrigação Única por Cliente'); ?></div><span class="medio"><input type="checkbox" id="chkObr_Unica" name="chkObr_Unica" disabled <?php if($visualizarDetalhe[0]['rastobrigacao_unica_cliente'] === 't') { echo 'checked'; } ?>/>
	            	</div>	            	
	            	 <?php 	            	 
						if ( $this->view->status && count($this->view->dados) > 0) {
							require 'pacote.php';
						}
					?>
            	</div>
            </div>
	</div>
</div>
<!-- fim lista-->