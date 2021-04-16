<div class="bloco_titulo">Agendamento Unitário</div>
<div class="bloco_conteudo">
    <div class="formulario">

        <div class="campo medio">
            <label id="lbl_duracao_padrao_atividade_ofsc" for="duracao_padrao_atividade_ofsc">Duração Padrão de Serviço
                <img class="btn-help" src="images/help10.gif" style="cursor: pointer"
                    onclick="mostrarHelpComment(this,'<?php echo $this->view->dados['DURACAO_PADRAO_ATIVIDADE_OFSC']['legenda']; ?>','D' , '');">
            </label>
            <input id="duracao_padrao_atividade_ofsc" class="campo numero obrigatorio" type="text" name="duracao_padrao_atividade_ofsc" maxlength="3"
                value="<?php echo $this->view->dados['DURACAO_PADRAO_ATIVIDADE_OFSC']['valor']; ?>" >
        </div>

         <div class="campo medio">
            <label id="lbl_considera_tempo_atividade_ofsc" for="considera_tempo_atividade_ofsc">Considerar tempo do OFSC
                <img class="btn-help" src="images/help10.gif" style="cursor: pointer"
                    onclick="mostrarHelpComment(this,'<?php echo $this->view->dados['CONSIDERA_TEMPO_ATIVIDADE_OFSC']['legenda']; ?>','D' , '');">
            </label>
            <select id="considera_tempo_atividade_ofsc" name="considera_tempo_atividade_ofsc" class="">
                <option value="S" <?php echo ($this->view->dados['CONSIDERA_TEMPO_ATIVIDADE_OFSC']['valor'] == 'S') ? ' selected' : ''; ?> >Sim</option>
                <option value="N" <?php echo ($this->view->dados['CONSIDERA_TEMPO_ATIVIDADE_OFSC']['valor'] == 'N') ? ' selected' : ''; ?> >Não</option>
            </select>
        </div>

         <div class="campo medio">
            <label id="lbl_fator_calculo_tempo_peso" for="fator_calculo_tempo_peso">Fator Multiplicador por Pesos
                <img class="btn-help" src="images/help10.gif" style="cursor: pointer"
                onclick="mostrarHelpComment(this, '<?php echo $this->view->dados['FATOR_CALCULO_TEMPO_PESO']['legenda']; ?>','D' , '');">
            </label>
            <input id="fator_calculo_tempo_peso" class="campo numero obrigatorio" type="text" name="fator_calculo_tempo_peso" maxlength="2"
            value="<?php echo $this->view->dados['FATOR_CALCULO_TEMPO_PESO']['valor']; ?>" >
        </div>
		<div class="clear"></div>

          <div class="campo medio">
            <label id="lbl_semanas_limite_pesquisa" for="semanas_limite_pesquisa">Limite de Semanas na Pesquisa
                <img class="btn-help" src="images/help10.gif" style="cursor: pointer"
                    onclick="mostrarHelpComment(this, '<?php echo $this->view->dados['SEMANAS_LIMITE_PESQUISA']['legenda']; ?>','D' , '');">
            </label>
            <input id="semanas_limite_pesquisa" class="campo numero obrigatorio" type="text" name="semanas_limite_pesquisa" maxlength="1"
                value="<?php echo $this->view->dados['SEMANAS_LIMITE_PESQUISA']['valor']; ?>" >
        </div>

         <div class="campo medio">
            <label id="lbl_semanas_calendario" for="semanas_calendario">Limite de Semanas no Calendário
                <img class="btn-help" src="images/help10.gif" style="cursor: pointer"
                    onclick="mostrarHelpComment(this, '<?php echo $this->view->dados['SEMANAS_CALENDARIO']['legenda']; ?>','D' , '');">
            </label>
            <input id="semanas_calendario" class="campo numero obrigatorio" type="text" name="semanas_calendario" maxlength="1"
                    value="<?php echo $this->view->dados['SEMANAS_CALENDARIO']['valor']; ?>" >
        </div>
        <div class="campo medio">
            <label id="lbl_antecipacao_reserva_material" for="antecipacao_reserva_material">Ativar Antecipação de Reserva de Materiais
                <img class="btn-help" src="images/help10.gif" style="cursor: pointer"
                     onclick="mostrarHelpComment(this, '<?php echo $this->view->dados['ANTECIPACAO_RESERVA_MATERIAL']['legenda']; ?>','D' , '');">
            </label>
            <select id="antecipacao_reserva_material" name="antecipacao_reserva_material" class="obrigatorio">
                <option value="2" <?php echo ( $this->view->dados['ANTECIPACAO_RESERVA_MATERIAL']['valor'] == 0) ? 'selected="true"' : '' ?>>Não</option>
                <option value="1" <?php echo ( $this->view->dados['ANTECIPACAO_RESERVA_MATERIAL']['valor'] == 1) ? 'selected="true"' : '' ?>>Sim</option>
            </select>
        </div>
        <div class="clear"></div>

         <div class="campo maior">
            <label>Status de O.S.Prestador para Falta Crítica
                 <img class="btn-help" src="images/help10.gif" style="cursor: pointer"
                    onclick="mostrarHelpComment(this, '<?php echo $this->view->dados['STATUS_OS_PESQUISA']['legenda']; ?>','D' , '');">
            </label>
            <select  id="status_os_pesquisa" name="status_os_pesquisa[]" multiple="multiple" size="15" class="obrigatorio">
            <?php foreach ($this->view->listaStatusOrdemServico as $chave => $valor) : ?>

                    <option value="<?php echo $valor->ossoid?>"
                        <?php echo in_array($valor->ossoid, $this->view->dados['STATUS_OS_PESQUISA']['valor'] ) ? ' selected="true"' : '' ;?> >
                        <?php echo $valor->ossdescricao; ?></option>

            <?php endforeach; ?>
            </select>
        </div>

        <div class="campo maior">
            <label>Status de Motivos de O.S.
                 <img class="btn-help" src="images/help10.gif" style="cursor: pointer"
                      onclick="mostrarHelpComment(this, '<?php echo $this->view->dados['STATUS_ITEM_OS']['legenda']; ?>','D' , '');">
            </label>
            <select  id="status_item_os" name="status_item_os[]" multiple="multiple" size="15" class="obrigatorio">
                 <?php foreach ($this->view->listaStatusItem as $chave => $valor) : ?>

                    <option value="<?php echo $chave?>"
                        <?php echo ( in_array( $chave, $this->view->dados['STATUS_ITEM_OS']['valor']) ) ? ' selected="true"' : '' ;?> >
                        <?php echo $valor . ' ['.$chave.']'; ?></option>

                 <?php endforeach; ?>
            </select>
        </div>
        <div class="clear"></div>

         <div class="campo">
            <label>Horário de Corte (manhã)
                 <img class="btn-help" src="images/help10.gif" style="cursor: pointer"
                    onclick="mostrarHelpComment(this, '<?php echo $this->view->dados['PERIODO_DZERO_MANHA']['legenda']; ?>','D' , '');">
             </label>
             <fieldset class="opcoes-display-block">

                <div class="campo">
                     <label>Início Corte</label>
                     <input id="periodo_dzero_manha_inicio" class="campo hora menor obrigatorio" type="text" name="periodo_dzero_manha_inicio"
                            value="<?php echo $this->view->dados['PERIODO_DZERO_MANHA_INICIO']['valor']; ?>">
               </div>
                <div class="campo">
                 <label>Fim Corte </label>
                 <input id="periodo_dzero_manha_fim" class="campo hora menor obrigatorio" type="text" name="periodo_dzero_manha_fim"
                        value="<?php echo $this->view->dados['PERIODO_DZERO_MANHA_FIM']['valor']; ?>">
                 </div>

                 <div class="campo">
                 <label>Início Agenda</label>
                 <input id="periodo_dzero_manha_agenda" class="campo hora menor obrigatorio" type="text" name="periodo_dzero_manha_agenda"
                        value="<?php echo $this->view->dados['PERIODO_DZERO_MANHA_AGENDA']['valor']; ?>">
                 </div>
             </fieldset>
        </div>
         <div class="clear"></div>

         <div class="campo">
            <label>Horário de Corte (tarde)
                 <img class="btn-help" src="images/help10.gif" style="cursor: pointer"
                        onclick="mostrarHelpComment(this, '<?php echo $this->view->dados['PERIODO_DZERO_TARDE']['legenda']; ?>','D' , '');">
             </label>
             <fieldset class="opcoes-display-block">

                <div class="campo">
                     <label>Início Corte</label>
                     <input id="periodo_dzero_tarde_inicio" class="campo hora menor obrigatorio" name="periodo_dzero_tarde_inicio" type="text"
                        value="<?php echo $this->view->dados['PERIODO_DZERO_TARDE_INICIO']['valor']; ?>">
               </div>
                <div class="campo">
                 <label>Fim Corte </label>
                 <input id="periodo_dzero_tarde_fim" class="campo hora menor obrigatorio" type="text" name="periodo_dzero_tarde_fim"
                        value="<?php echo $this->view->dados['PERIODO_DZERO_TARDE_FIM']['valor']; ?>">
                 </div>

                 <div class="campo">
                 <label>Início Agenda</label>
                 <input id="periodo_dzero_tarde_agenda" class="campo hora menor obrigatorio" type="text" name="periodo_dzero_tarde_agenda"
                        value="<?php echo $this->view->dados['PERIODO_DZERO_TARDE_AGENDA']['valor']; ?>">
                 </div>
             </fieldset>
        </div>
         <div class="clear"></div>

         <div class="campo">
            <label>Horário de Corte (noite)
                 <img class="btn-help" src="images/help10.gif" style="cursor: pointer"
                        onclick="mostrarHelpComment(this, '<?php echo $this->view->dados['PERIODO_DZERO_NOITE']['legenda']; ?>','D' , '');">
             </label>
             <fieldset class="opcoes-display-block">

                <div class="campo">
                     <label>Início Corte</label>
                     <input id="periodo_dzero_noite_inicio" class="campo hora menor obrigatorio" type="text" name="periodo_dzero_noite_inicio"
                            value="<?php echo $this->view->dados['PERIODO_DZERO_NOITE_INICIO']['valor']; ?>">
               </div>
                <div class="campo">
                 <label>Fim Corte </label>
                 <input id="periodo_dzero_noite_fim" class="campo hora menor obrigatorio" type="text" name="periodo_dzero_noite_fim"
                        value="<?php echo $this->view->dados['PERIODO_DZERO_NOITE_FIM']['valor']; ?>">
                 </div>

                 <div class="campo">
                 <label>Início Agenda</label>
                 <input id="periodo_dzero_noite_agenda" class="campo hora menor obrigatorio" type="text" name="periodo_dzero_noite_agenda"
                        value="<?php echo $this->view->dados['PERIODO_DZERO_NOITE_AGENDA']['valor']; ?>">
                 </div>
             </fieldset>
        </div>
         <div class="clear"></div>

    </div>
</div>
 <div class="separador"></div>

<div class="bloco_titulo">Logística</div>
<div class="bloco_conteudo">
    <div class="formulario">

        <div class="campo maior">
            <label id="lbl_repoid_solicitacao_falsa" for="repoid_solicitacao_falsa">Prestador para Falta Crítica
                <img class="btn-help" src="images/help10.gif" style="cursor: pointer"
                        onclick="mostrarHelpComment(this, '<?php echo $this->view->dados['REPOID_SOLICITACAO_FALSA']['legenda']; ?>','D' , '');">
            </label>
            <select id="repoid_solicitacao_falsa" name="repoid_solicitacao_falsa" class="obrigatorio">
                <option value="">Escolha</option>
                <?php foreach ($this->view->listaPrestador as $chave => $valor) : ?>
                    <option value="<?php echo $valor->repoid; ?>"
                            <?php echo ( $this->view->dados['REPOID_SOLICITACAO_FALSA']['valor'] == $valor->repoid) ? 'selected="true"' : '' ?>>
                            <?php echo $valor->repnome; ?></option>
                <?php endforeach;    ?>
            </select>
        </div>
        <div class="clear"></div>

        <div class="campo medio">
            <label id="lbl_tempo_preparacao_remessa" for="tempo_preparacao_remessa">Tempo de Preparação da Remessa
                <img class="btn-help" src="images/help10.gif" style="cursor: pointer"
                    onclick="mostrarHelpComment(this, '<?php echo $this->view->dados['TEMPO_PREPARACAO_REMESSA']['legenda']; ?>','D' , '');">
            </label>
            <input id="tempo_preparacao_remessa" class="campo numero obrigatorio" type="text" name="tempo_preparacao_remessa" maxlength="2"
                    value="<?php echo $this->view->dados['TEMPO_PREPARACAO_REMESSA']['valor']; ?>" >
        </div>

         <div class="campo medio">
            <label id="lbl_tempo_recebimento_remessa" for="tempo_recebimento_remessa">Tempo de Recebimento da Remessa
                <img class="btn-help" src="images/help10.gif" style="cursor: pointer"
                    onclick="mostrarHelpComment(this, '<?php echo $this->view->dados['TEMPO_RECEBIMENTO_REMESSA']['legenda']; ?>','D' , '');">
            </label>
            <input id="tempo_recebimento_remessa" class="campo numero obrigatorio" type="text" name="tempo_recebimento_remessa" maxlength="2"
                value="<?php echo $this->view->dados['TEMPO_RECEBIMENTO_REMESSA']['valor']; ?>">
        </div>
        <div class="clear"></div>

   </div>
</div>

<div class="separador"></div>

<div class="bloco_acoes">
    <button type="button" id="bt_gravar" name="bt_gravar" value="gravar">Salvar</button>
</div>