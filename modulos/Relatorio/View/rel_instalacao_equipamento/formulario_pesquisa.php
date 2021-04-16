<div class="bloco_titulo">Dados Principais</div>
<div class="bloco_conteudo">
    <div class="formulario">
        
        <div class="campo data periodo">
            <div class="inicial">
                <label for="dataInicial">Período *</label>
                <input type="text" id="dataInicial" name="dataInicial" value="" class="campo campoObrigatorio" />
            </div>
            <div class="campo label-periodo">a</div>
            <div class="final">
                <label for="dataFinal">&nbsp;</label>
                <input type="text" id="dataFinal" name="dataFinal" value="" class="campo campoObrigatorio" />
            </div>
        </div>

        <div class="campo menor">
            <label for="tipoPesquisa">Pesquisar Por *</label>
            <select class="campoObrigatorio" id="tipoPesquisa" name="tipoPesquisa">
                <option value="">- Selecione -</option>
                <option value="entdt_entrada">Data de entrada</option>
                <option value="entdt_emissao">Data de emissão</option>
                <option value="cmipagamento">Data de pagamento</option>
            </select>
        </div>
        <div class="clear"></div>

        <div class="campo maior">
            <label for="nomeFornecedor">Fornecedor</label>
            <input id="nomeFornecedor" class="campo" type="text" value="" name="nomeFornecedor">
        </div>
		<div class="clear"></div>

        <div class="campo maior">
            <label for="tipoServico">Tipo de Serviço</label>
            <select id="tipoServico" name="tipoServico">
                <option value="">- Selecione -</option>
                <?php foreach($this->view->parametros->tiposServico as $tipoServico) : ?>
                    <option value="<?php echo $tipoServico->ostoid ?>"><?php echo $tipoServico->ostdescricao ?></option>
                <?php endforeach; ?>
            </select>
        </div>
		<div class="clear"></div>

        <div class="campo maior">
            <label for="tipoInstalacao">Tipo de Instalação</label>
            <select id="tipoInstalacao" name="tipoInstalacao">
                <option value="">- Selecione -</option>
                <?php foreach($this->view->parametros->tiposInstalacao as $tipoInstalacao) : ?>
                    <option value="<?php echo $tipoInstalacao->tpcoid ?>"><?php echo $tipoInstalacao->tpcdescricao ?></option>
                <?php endforeach; ?>
            </select>
        </div>
		<div class="clear"></div>

    </div>
</div>

<div class="bloco_acoes">
    <button type="button" id="bt_gerar">Gerar</button>
</div>







