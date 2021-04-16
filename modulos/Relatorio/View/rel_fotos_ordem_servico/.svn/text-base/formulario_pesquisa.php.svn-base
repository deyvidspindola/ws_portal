<form id="form"  method="post" action="rel_fotos_ordem_servico.php">
    <input type="hidden" id="acao" name="acao" value="pesquisar"/>
    <input type="hidden" id="ordoid" name="ordoid" value=""/>
    <div class="bloco_titulo">Dados da Pesquisa</div>
    <div class="bloco_conteudo">
        <div class="formulario">
            
            <div class="campo data periodo">
                <div class="inicial">
                    <label for="data_inicial">Período </label>
                    <input id="data_inicial" type="text" name="data_inicial" maxlength="10" value="<?php echo $this->view->parametros->data_inicial;?>" class="campo">
                </div>

                <div class="campo label-periodo">a</div>

                <div class="final">
                    <label for="data_final">&nbsp;</label>
                    <input id="data_final" type="text" name="data_final" maxlength="10" value="<?php echo $this->view->parametros->data_final;?>" class="campo">
                </div>
            </div>

            <div class="campo medio">
                <label for="combo_visualizar">Visualizar</label>
                <select id="combo_visualizar" name="combo_visualizar">
                    <option value="">Escolha</option>
                    <option value="O" <?php echo $this->view->parametros->combo_visualizar == 'O' ? 'selected' : '' ?>>OS Concluída</option>
                    <option value="C"<?php echo $this->view->parametros->combo_visualizar == 'C' ? 'selected' : '' ?>>Cadastro de Equipamento</option>
                </select>
            </div>

            <div class="clear"></div>

            <div class="campo medio">
                <label for="ordoid">Ordem de Serviço</label>
                <input id="ordoid" type="text" name="ordoid" value="<?php echo isset($this->view->parametros->ordoid) ?  $this->view->parametros->ordoid : '' ?>" class="campo numeric">
            </div>

            <fieldset class="medio opcoes-inline">
                <legend>Opções</legend>
                <input id="gera_csv" type="checkbox" name="gera_csv" 
                <?php echo $this->view->parametros->gera_csv == 'on' ?  'checked="true"' : '' ?>>
                <label for="gera_csv">Gerar CSV</label>
                <input id="os_sem_foto" type="checkbox" name="os_sem_foto" 
                <?php echo $this->view->parametros->os_sem_foto == 'on' ?  'checked="true"' : '' ?>>
                <label for="os_sem_foto">O. S. Sem Foto</label>
            </fieldset>

            <div class="clear"></div>

        </div>
    </div>

    <div class="bloco_acoes">

        <button type="submit" id="bt_pesquisar">Pesquisar</button>

    </div>
</form>

