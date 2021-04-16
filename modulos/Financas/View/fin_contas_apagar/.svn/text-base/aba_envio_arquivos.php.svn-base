<input type="hidden" id="acao" name="acao" value="pesquisarEnvioArquivos"/>

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
            <label id="lbl_num_remessa" for="num_remessa">Nº Remessa</label>
            <input id="num_remessa" class="campo descricao" type="text" value="<?=$this->view->filtros->num_remessa;?>" name="num_remessa" maxlength="11">
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
            <label for="status">Status do Arquivo:</label>
            <select id="status" name="status">
                <option value='1' <?php echo ($this->view->filtros->status == "1" ? 'selected="selected"' : ""); ?> >Ambos</option>
                <option value='2' <?php echo ($this->view->filtros->status == "2" ? 'selected="selected"' : ""); ?> >Aguardando Processamento</option>
                <option value='3' <?php echo ($this->view->filtros->status == "3" ? 'selected="selected"' : ""); ?> >Processado</option>
            </select>
        </div>

        <div class="clear"></div>
    
    </div>
</div>

<div class="bloco_acoes">
    <button type="submit" id="bt_pesquisar">Pesquisar</button>
    <button type="submit" id="bt_limpar_envio_arquivos">Limpar</button>
</div>


<div id="resultado_pesquisa" >
    
    <?php 
        if (count($this->view->dados['envioArquivos']) > 0) { 
            require_once 'aba_envio_arquivos_resultado.php'; 
        } 
    ?>
        
</div>







