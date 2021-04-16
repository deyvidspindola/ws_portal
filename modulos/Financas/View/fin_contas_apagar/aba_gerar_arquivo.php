<input type="hidden" id="acao" name="acao" value="pesquisarGeraArquivo"/>

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
            <select id="consultar_busca" name="consultar_busca">
                <option value="apgdt_pagamento"  <?php echo ($this->view->filtros->consultar_busca == "apgdt_pagamento"  ? 'selected="selected"' : ""); ?> >Data de Pagamento</option>
                <option value="apgdt_vencimento" <?php echo ($this->view->filtros->consultar_busca == "apgdt_vencimento" ? 'selected="selected"' : ""); ?> >Data de Vencimento</option>
                <option value="apgdt_entrada"    <?php echo ($this->view->filtros->consultar_busca == "apgdt_entrada"    ? 'selected="selected"' : ""); ?> >Data de Entrada</option>
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
            <label for="apresentar_valor">Apresentar valor por:</label> 
            <select id="apresentar_valor" name="apresentar_valor">
                <option value="1" <?php echo ($this->view->filtros->apresentar_valor == "1" ? 'selected="selected"' : "" ); ?> >Documento</option>
                <option value="2" <?php echo ($this->view->filtros->apresentar_valor == "2" ? 'selected="selected"' : "" ); ?> >Conta Contábil</option>
            </select>
        </div>

        <div class="clear"></div>

        <div class="campo medio">
            <label for="tiponf_busca">Tipo NF :</label>
            <select id="tiponf_busca" name="tiponf_busca">
                <option value="">Escolha</option>
                <option value="not null" <?php echo ($this->view->filtros->tiponf_busca == "not null" ? 'selected="selected"' : ""); ?> >Com NF</option>
                <option value="null"     <?php echo ($this->view->filtros->tiponf_busca == "null" ? 'selected="selected"' : ""); ?>     >Sem NF</option>
            </select>
        </div>

        <div class="campo medio">
            <label for="retencao">Apresentar retenção:</label>
            <select id="retencao" name="retencao">
                <option value='SIM' <?php echo ($this->view->filtros->retencao == "SIM" ? 'selected="selected"' : ""); ?> >SIM</option>
                <option value='NAO' <?php echo ($this->view->filtros->retencao == "NAO" ? 'selected="selected"' : ""); ?> >NAO</option>
            </select>
        </div>

        <div class="clear"></div>
    
    </div>
</div>

<div class="bloco_acoes">
    <button type="submit" id="bt_pesquisar">Pesquisar</button>
</div>


<div id="resultado_pesquisa" >
    
    <?php 
        if (count($this->view->dados['geraArquivo']) > 0 || count($this->view->dados['titulosPagos']) > 0 || count($this->view->dados['adiantamento']) > 0) { 
            require_once 'aba_gerar_arquivo_resultado.php'; 
        } 
    ?>
        
</div>







