<div class="bloco_titulo">Dados para Pesquisa</div>
<div class="bloco_conteudo">
    <div class="formulario">
        <form action="" id="form_pesquisa" method="post">
            <input type="hidden" id="acao" name="acao" value="pesquisar" />

            <div class="campo data periodo">
                <div class="inicial">
                    <label for="data_inicial">Período</label>
                    <input id="data_inicial" name="data_inicial" maxlength="10" value="<?php echo $this->view->parametros->data_inicial;?>" class="campo" type="text" />
                </div>
                <div class="campo label-periodo">à</div>
                <div class="final">
                    <label for="data_final">&nbsp;</label>
                    <input id="data_final" name="data_final" maxlength="10" value="<?php echo $this->view->parametros->data_final;?>" class="campo" type="text" />
                </div>
            </div>
             <div class="campo medio">
                <label for="clinome_pesq">Cliente</label>
                <input id="clinome_pesq" name="clinome_pesq" value="<?php echo $this->view->parametros->clinome_pesq;?>" class="campo" type="text">
                <input id="clioid_pesq" name="clioid_pesq" value="<?php echo $this->view->parametros->clioid_pesq;?>" class="campo" type="hidden">
            </div>
            <div class="clear"></div>

            <div class="campo medio">
                <label for="eproid">Equipamento</label>
                <select id="eproid" name="eproid">
                    <option value="">Escolha</option>
                    <?php foreach ($this->view->comboEquipamentosProjeto as $equipamento): ?>
                        <option value="<?php echo $equipamento->eproid ?>" <?php echo ($this->view->parametros->eproid == $equipamento->eproid) ? 'selected="true"' : '' ; ?>>
                            <?php echo $equipamento->eprnome; ?>
                        </option>
                    <?php endForeach; ?>
                </select>
            </div>
            <div class="campo medio">
                <label for="placa">Placa</label>
                <input id="placa" name="placa" value="<?php echo $this->view->parametros->placa;?>" class="campo placa uppercase" type="text" maxlength="15" />
            </div>
            <div class="clear"></div>

        </form>
    </div>
</div>
<div class="bloco_acoes">
    <button type="button" id="btn_pesquisar">Pesquisar</button>
    <button type="button" onclick="history.back()">Voltar</button>
</div>
<div class="separador"></div>