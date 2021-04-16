<input type="hidden" id="acao" name="acao" value="pesquisarTitulosProcessados"/>

<div class="bloco_titulo">Dados para pesquisa</div>
<div class="bloco_conteudo">

    <div class="formulario">


        <div class="campo data periodo">
            <div class="inicial">
                <label>Período</label> 
                <input class="campo" type="text" name="periodo_inicial_busca" id="periodo_inicial_busca" maxlength="10" value="<?=$this->view->filtros->periodo_inicial_busca;?>" />
            </div>

            <div class="campo label-periodo">à</div>

            <div class="final">
                <label>&nbsp;</label> 
                <input class="campo" type="text" name="periodo_final_busca" id="periodo_final_busca" maxlength="10" value="<?=$this->view->filtros->periodo_final_busca;?>" />
            </div>
        </div>

        <div class="campo medio">
            <label for="consultar_busca">Pesquisa Por</label>
            <select id="consultar_busca" name="consultar_busca" style="width: 280px;">
                <option value="apcedt_envio" <?php echo ($this->view->filtros->consultar_busca == "apcedt_envio" ? 'selected="selected"' : ""); ?> >Processamento</option>
                <option value="apgdt_vencimento" <?php echo ($this->view->filtros->consultar_busca == "apgdt_vencimento" ? 'selected="selected"' : ""); ?> >Vencimento</option>
                <option value="apgdt_entrada" <?php echo ($this->view->filtros->consultar_busca == "apgdt_entrada" ? 'selected="selected"' : ""); ?> >Data de Entrada</option>
            </select>
        </div>

        <div class="clear"></div>

        <div class="campo medio">
            <label for="banco">Banco</label> 
            <select id="banco" name="banco">
                <?php foreach ($this->view->bancos as $bancoId => $banco) :?>
                    <option value="<?php echo $banco->bancodigo; ?>" <?php echo ($this->view->filtros->banco == $banco->bancodigo ? 'selected="selected"' : ""); ?> ><?php echo $banco->bannome; ?></option>
                <?php endforeach;?>
            </select>
        </div>

        <div class="campo medio">
            <label for="status">Status:</label>
            <select id="status" name="status" style="width: 280px;">
            	<option value="" <?php echo ($this->view->filtros->status == "") ? 'selected="selected"' : ''; ?> >Todos</option>
                <?php foreach ($this->view->statuss as $statusId => $status) :?>
                    <? if($status->apgsoid != 2 && $status->apgsoid != 1) { ?>
                    <option value="<?php echo $status->apgsoid; ?>" <?php echo ($this->view->filtros->status == $status->apgsoid ? 'selected="selected"' : ""); ?> ><?php echo (($status->apgsoid == 5 ) ? $status->apgsdescricao . " / Rejeitado" : $status->apgsdescricao) ; ?></option>
                    <? } ?>
                <?php endforeach;?>
            </select>
        </div>
        
        <div class="clear"></div>

        <div class="campo medio">
            <label id="lbl_num_remessa" for="num_remessa">Nº Remessa</label>
            <input id="num_remessa" class="campo descricao" type="text" value="<?=$this->view->filtros->num_remessa;?>" name="num_remessa" maxlength="11">
        </div>

        <div class="campo maior">
            <label id="lbl_fornecedor" for="cmp_cliente">Fornecedor <img class="btn-help" src="images/help10.gif" style="cursor: pointer" onclick="mostrarHelpComment(this,'Mínimo três letras para a auto pesquisa.','D' , '');"></label>
            <input id="cmp_fornecedor_autocomplete" class="campo" type="text" value="<?php echo $this->view->parametros->cmp_fornecedor_autocomplete; ?>" name="cmp_fornecedor_autocomplete">
            <input id="cmp_fornecedor" type="hidden" value="<?php echo $this->view->parametros->cmp_fornecedor; ?>" class="validar" name="cmp_fornecedor">
        </div>

        <div class="clear"></div>
    
    </div>
</div>

<div class="bloco_acoes">
    <button type="submit" id="bt_pesquisar">Pesquisar</button>
    <button type="submit" id="bt_limpar_titulos_processados">Limpar</button>
</div>


<div id="resultado_pesquisa" >
    
    <?php 
        if (count($this->view->dados['titulosProcessados']) > 0) { 
            require_once 'aba_titulos_processados_resultado.php'; 
            
            if (isset($this->view->csv) && $this->view->csv === true){
            	require_once 'csv.php';
            }
        } 
    ?>
        
</div>







