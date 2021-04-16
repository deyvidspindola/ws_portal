<div class="bloco_titulo">Dados Para Pesquisa</div>
<div class="bloco_conteudo">
    <div class="formulario">
        <form id="form_rel_calculo_deslocamento_tecnico" method="post">
            <input type="hidden" name="acao" id="acao" value=" " />

            <div class="campo data data-intervalo">
                <label id="label_dt_inicio" for="dt_inicio">Período de Pesquisa *</label>
                <div class="calendario float-left">
                    <input id="dt_inicio" class="campo validar" type="text" value="<?php echo $this->param->dt_inicio?>" name="dt_inicio">
                </div>
                <div class="ate float-left">a</div>
                <div class="calendario float-left">
                    <input id="dt_fim" class="campo validar" type="text" value="<?php echo $this->param->dt_fim?>" name="dt_fim">
                </div>
            </div>

            <div class="clear"></div>

            <div class="campo maior">
                <label for="repoid">Representante</label>
                <select id="repoid" name="repoid">
                    <option value="">Escolha</option>
                    <?php foreach($this->view->dados->representates as $representante) : ?>
                        <option value="<?php echo $representante->repoid ?>" 
                            <?php if ($this->param->repoid == $representante->repoid): ?>
                                selected="selected"
                            <?php endif; ?> >
                            <?php echo $representante->repnome ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="clear"></div>

            <div class="campo maior">
                <label for="itloid">Técnico</label>
                <select id="itloid" name="itloid">
                    <option value="">Escolha</option>
                    <?php foreach($this->view->dados->tecnicos as $tecnico) : ?>
                        <option value="<?php echo $tecnico->itloid ?>" 
                            <?php if ($this->param->itloid == $tecnico->itloid): ?>
                                selected="selected"
                            <?php endif; ?> >
                            <?php echo $tecnico->itlnome ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="clear"></div>

        </form>
    </div>
</div>
<div class="bloco_acoes">
        <button <?php echo ($this->view->dados->bloquearPesquisa) ? 'disabled="disabled"' : '' ; ?> type="button" name="pesquisar" id="pesquisar">Pesquisar</button>
</div>
