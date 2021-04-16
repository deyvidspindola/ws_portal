<div class="bloco_titulo">Dados Principais</div>
<div class="bloco_conteudo">
    <div class="formulario filtros">
        <input type="hidden" value="<?echo $this->view->iesoid;?>">

      <div id="form-iesotitipo" class="campo medio">
            <label for="iesotitipo">Item de Ordem de Serviço *</label>
            <select id="iesotitipo" name="iesotitipo">
                <option value="">Escolha</option>
                <option value="A" <?php echo ($this->view->parametros->iesotitipo == 'A' ? "SELECTED" : "") ?>>ACESSÓRIOS</option>
                <option value="E" <?php echo ($this->view->parametros->iesotitipo == 'E' ? "SELECTED" : "") ?>>EQUIPAMENTO</option>                   
            </select>
        </div>
        <div class="clear"></div>

        <div id="form-iesostoid" class="campo medio">
            <label for="iesostoid">Tipo de Ordem de Serviço *</label>
            <select id="iesostoid" name="iesostoid">
                <option value="">Escolha</option>
                <?foreach ($this->view->tipoOrdemServico as $row) { ?>
                    <option value="<?echo $row->ostoid;?>" <?echo ($this->view->parametros->iesostoid == $row->ostoid ? "SELECTED" : "") ?>><?echo $row->ostdescricao;?></option>
                <?}?>
            </select>
            <img class="carregando" src="modulos/web/images/ajax-loader-circle.gif">
        </div>
        <div id="form-ieseqcoid" class="campo medio">
            <label for="ieseqcoid">Classe do Equipamento</label>
            <select id="ieseqcoid" name="ieseqcoid">
                <option value="">Escolha</option>
                <?foreach ($this->view->getClasseEquipamento as $row) { ?>
                    <option value="<?echo $row->eqcoid;?>" <?echo ($this->view->parametros->ieseqcoid == $row->eqcoid ? "SELECTED" : "") ?>><?echo $row->eqcdescricao;?></option>
                <?}?>
            </select>
        </div>
        <div id="form-ieseproid" class="campo menor">
            <label for="ieseproid">Equipamento</label>
            <select id="ieseproid" name="ieseproid">
                <option value="">Escolha</option>
                <?if (isset($this->view->getEquipamento)) {
                    foreach ($this->view->getEquipamento as $row) { ?>
                    <option value="<?echo $row['eproid'];?>" <?echo ($this->view->parametros->ieseproid == $row['eproid'] ? "SELECTED" : "") ?>><?echo utf8_decode($row['eprnome']);?></option>
                <?  }
                }?>
            </select>
            <img class="carregando" src="modulos/web/images/ajax-loader-circle.gif">
        </div>
        <div id="form-ieseveoid" class="campo menor">
            <label for="ieseveoid">Versão</label>
            <select id="ieseveoid" name="ieseveoid">
                <option value="">Escolha</option>
                <?if (isset($this->view->getVersao)) {
                    foreach ($this->view->getVersao as $row) { ?>
                    <option value="<?echo $row['eveoid'];?>" <?echo ($this->view->parametros->ieseveoid == $row['eveoid'] ? "SELECTED" : "") ?>><?echo utf8_decode($row['eveversao']);?></option>
                <?  }
                }?>
            </select>
            <img class="carregando" src="modulos/web/images/ajax-loader-circle.gif">
        </div>
        <div id="form-iesotioid" class="campo medio">
            <label for="iesotioid">Motivo da Ordem de Serviço *</label>
            <select id="iesotioid" name="iesotioid">
                <option value="">Escolha</option>
                <?if (isset($this->view->getMotivoOrdemServico)) {
                    foreach ($this->view->getMotivoOrdemServico as $row) { ?>
                    <option value="<?echo $row['otioid'];?>" <?echo ($this->view->parametros->iesotioid == $row['otioid'] ? "SELECTED" : "") ?>><?echo utf8_decode($row['otidescricao']);?></option>
                <?  }
                }?>
            </select>
            <img class="carregando" src="modulos/web/images/ajax-loader-circle.gif">
        </div>
        <div id="form-iesmcaoid" class="campo medio">
            <label for="iesmcaoid">Marca do Veículo</label>
            <select id="iesmcaoid" name="iesmcaoid">
                <option value="">Escolha</option>
                <?foreach ($this->view->getMarcaVeiculo as $row) { ?>
                    <option value="<?echo $row->mcaoid;?>" <?echo ($this->view->parametros->iesmcaoid == $row->mcaoid ? "SELECTED" : "") ?>><?echo $row->mcamarca;?></option>
                <?}?>
            </select>
        </div>
        <div id="form-iesmlooid" class="campo medio">
            <label for="iesmlooid">Modelo do Veículo</label>
            <select id="iesmlooid" name="iesmlooid">
                <option value="">Escolha</option>
                <?if (isset($this->view->getModeloVeiculo)) {
                    foreach ($this->view->getModeloVeiculo as $row) { ?>
                    <option value="<?echo $row['mlooid'];?>" <?echo ($this->view->parametros->iesmlooid == $row['mlooid'] ? "SELECTED" : "") ?>><?echo utf8_decode($row['mlomodelo']);?></option>
                <?  }
                }?>
            </select>
            <img class="carregando" src="modulos/web/images/ajax-loader-circle.gif">
        </div>
        <div class="clear"></div>
    </div>
</div>
<div class="bloco_acoes">
    <button type="button" id="bt_pesquisar">Pesquisar</button>
    <button type="button" id="bt_novo">Novo</button>
</div>

<div class="separador"></div>