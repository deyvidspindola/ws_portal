<div class="bloco_titulo">Dados para Pesquisa</div>
<div class="bloco_conteudo">
    <div class="formulario">

        <div class="campo maior">
            <label for="empresa">Empresa *</label>
            <select id="empresa" name="empresa">
                <option value="">Escolha</option>
                <?php foreach($this->view->parametros->todasEmpresas as $empresa) : ?>
                    <option value="<?php echo $empresa->tecoid ?>" <?php echo (isset($this->view->parametros->empresa) && ($this->view->parametros->empresa == $empresa->tecoid)) ? 'selected="selected"' : '' ?>>
                        <?php echo $empresa->tecrazao ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="clear"></div>

        <div class="campo maior">
            <input type="hidden" value="<?php echo isset($this->view->parametros->centroCusto) ? $this->view->parametros->centroCusto : '' ?>" id="postCentroCusto">
            <label for="centroCusto">Centro de Custo</label>
            <select id="centroCusto" name="centroCusto">
                <option value="">Escolha</option>
                <!-- Opções deste campo serão preenchidos por ajax -->
            </select>
        </div>
        <div class="clear"></div>

        <div class="campo medio">
            <label for="statusSolicitacao">Status da Solicitação</label>
            <select id="statusSolicitacao" name="statusSolicitacao">
                <option value="">Escolha</option>
                <option value="P" <?php echo (isset($this->view->parametros->statusSolicitacao) && $this->view->parametros->statusSolicitacao  == 'P') ? 'selected="selected"' : '' ?>>Pendente de aprovação</option>
                <option value="C" <?php echo (isset($this->view->parametros->statusSolicitacao) && $this->view->parametros->statusSolicitacao  == 'C') ? 'selected="selected"' : '' ?>>Requisição reprovada</option>
                <option value="A" <?php echo (isset($this->view->parametros->statusSolicitacao) && $this->view->parametros->statusSolicitacao  == 'A') ? 'selected="selected"' : '' ?>>Finalizada</option>
                <option value="S" <?php echo (isset($this->view->parametros->statusSolicitacao) && $this->view->parametros->statusSolicitacao  == 'S') ? 'selected="selected"' : '' ?>>Pendente de prestação de contas</option>
                <option value="R" <?php echo (isset($this->view->parametros->statusSolicitacao) && $this->view->parametros->statusSolicitacao  == 'R') ? 'selected="selected"' : '' ?>>Pendente aprovacao de reembolso</option>
                <option value="F" <?php echo (isset($this->view->parametros->statusSolicitacao) && $this->view->parametros->statusSolicitacao  == 'F') ? 'selected="selected"' : '' ?>>Pendente conferencia de prestacao de contas</option>
                <option value="D" <?php echo (isset($this->view->parametros->statusSolicitacao) && $this->view->parametros->statusSolicitacao  == 'D') ? 'selected="selected"' : '' ?>>Aguardando devolução</option>
            </select>
        </div>
        <div class="campo medio">
            <label for="tipoRequisicao">Tipo da Requisição</label>
            <select id="tipoRequisicao" name="tipoRequisicao">
                <option value="">Escolha</option>
                <option value="C" <?php echo (isset($this->view->parametros->tipoRequisicao) && $this->view->parametros->tipoRequisicao  == 'C') ? 'selected="selected"' : '' ?>>Combustível - ticket car</option>
                <option value="A" <?php echo (isset($this->view->parametros->tipoRequisicao) && $this->view->parametros->tipoRequisicao  == 'A') ? 'selected="selected"' : '' ?>>Adiantamento</option>
                <option value="L" <?php echo (isset($this->view->parametros->tipoRequisicao) && $this->view->parametros->tipoRequisicao  == 'L') ? 'selected="selected"' : '' ?>>Reembolso</option>
            </select>
        </div>
        <div class="clear"></div>

        <div class="campo medio">
            <label for="numeroRequisicao">Número da Requisição</label>
            <input id="numeroRequisicao" maxlength="10" class="campo" type="text" value="<?php echo isset($this->view->parametros->numeroRequisicao) ? $this->view->parametros->numeroRequisicao : '' ?>" name="numeroRequisicao">
        </div>
        <div class="campo medio">
            <label for="solicitante">Solicitante</label>
            <input id="solicitante" maxlength="30" class="campo" type="text" value="<?php echo isset($this->view->parametros->solicitante) ? $this->view->parametros->solicitante : '' ?>" name="solicitante">
        </div>
        <div class="clear"></div>


    </div>
</div>

<div class="bloco_acoes">
    <button type="submit" id="bt_pesquisar">Pesquisar</button>
    <button type="button" id="bt_limpar">Limpar</button>
    <button type="button" id="bt_novo">Novo</button>
</div>







