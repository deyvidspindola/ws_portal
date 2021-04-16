<div class="bloco_titulo">Dados para Pesquisa</div>
<div class="bloco_conteudo">
    <div class="formulario">
<!--
		<div class="campo maior">
            <label id="lbl_clinome_psq" for="clinome_psq">Nome do Cliente * </label>
            <input id="clinome_psq" class="campo" type="text" value="<?php //echo $clinome_psq; ?>" name="clinome_psq" maxlength="255">

-->
		<div class="clear"></div>
 
			<?php $this->comp_cliente->render() ?>

        <div class="clear"></div>
        
<!--
        <div class="campo data">
            <label><?php //echo "Data Expiração";?></label>
            <input class="campo"  type="text" name="dt_ini" id="dt_ini" maxlength="10" value="<?php //echo $dt_ini; ?>" />
        </div>
        <div style="margin-top: 23px !important;" class="campo label-periodo">a</div>
        <div class="campo data">
            <label>&nbsp;</label>
            <input  class="campo"  type="text" name="dt_fim" id="dt_fim" maxlength="10" value= "<?php //echo $dt_fim; ?>" />
        </div>

        <div class="campo medio">
            <label id="lbl_token" for="token">Token</label>
            <input id="token" class="campo" type="text" value="" name="token">
        </div>
-->

		<div class="clear"></div> 

    </div>
</div>

<div class="bloco_acoes">
    <button type="submit" id="bt_pesquisar">Pesquisar</button>
    <button type="button" id="bt_novo">Novo</button>
</div>







