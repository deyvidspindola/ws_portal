<div class="bloco_titulo">Cadastro</div>
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

    <!-- Segundo Bloco -->
    <div class="bloco_titulo">Materiais e Acessórios Essenciais</div>
    <div class="bloco_conteudo">
        <div class="formulario">
            <div id="form-iespprdoid" class="campo maior-combo">
                <label for="iespprdoid">Materiais/Acessórios</label>
                <select id="iespprdoid" name="iespprdoid[]" multiple="multiple" size="15">
                    <?foreach ($this->view->getMateriais as $row) { ?>
                        <option value="<?echo $row->prdoid;?>" <?echo (isset($this->view->parametros->materiaisCadastrados[$row->prdoid]) ? "DISABLED" : "") ?>><?echo $row->prdproduto;?></option>
                    <?}?>
                </select>
            </div>
            <div class="clear"></div>
        </div>
    </div>
    <div class="bloco_acoes">
        <button type="button" id="bt_adicionar">Adicionar</button>
    </div>
    <div class="separador"></div>  

    <!-- Terceiro Bloco -->
    <div class="adicionado bloco_titulo">Materiais / Acessórios adicionados</div>
    <div class="adicionado bloco_conteudo">
        <div id="bloco_itens" class="listagem">
            <table id="itens_adicionados">
                <thead>
                    <tr>
                        <th class="medio">Materiais/Acessórios</th>
                        <th class="menor">Quantidade</th>
                        <th class="acao">Ação</th>
                    </tr>
                </thead>
                <tbody>
                    <input type="hidden" value="1" class="quantidade">
                    <?if(count($this->view->parametros->iespiesoid) > 0){
                        foreach ($this->view->parametros->iespiesoid as $row) {?>
                            <tr id="id_<?=$row->iespprdoid?>" class="linha">
                                <td> <?echo $row->prdproduto?> </td>
                                <td class="centro">
                                    <input id="item_<?=$row->iespprdoid?>" name="item_<?=$row->iespprdoid?>" type="text" value="<?echo $row->iespquantidade?>" maxlength="2" size="1" class="quantidade">
                                </td>
                                <td class="acao centro">
                                    <a title="Excluir" class="excluir-item" data-iespprdoid="<?=$row->iespprdoid?>" href="#"><img class="icone" src="images/icon_error.png"  alt="Excluir"></a>
                                </td>
                            </tr>
                    <?  }
                    } ?>
                </tbody>
            </table>
        </div>
    </div>
    <div class="separador"></div>  

</div>

<div class="bloco_acoes">
    <button type="button" id="bt_voltar">Voltar</button>
    <button type="button" id="bt_salvar" name="bt_salvar" value="salvar">Salvar</button>
</div>

<?if($_GET['acao'] != "editar"){?>

    <div class="separador"></div>

    <div class="bloco_titulo">Importar Arquivo</div>
    <div class="bloco_conteudo">
        <div class="formulario">
            <div class="campo medio">
                <input type="hidden" name="MAX_FILE_SIZE" value="1048576" />
                <input type="file" name="arquivo" id="arquivo" accept=".csv"> 
            </div>
            <div class="clear"></div>
        </div>
    </div>
    <div class="bloco_acoes">
        <button type="button" id="bt_importar">Importar</button>
    </div>

<?}?>

<?if($this->view->parametros->acao == "erro_importar"){?>

    <div class="separador"></div>

    <div class="download bloco_titulo">Download</div>
    <div class="download bloco_conteudo">
        <div class="formulario">
            <div class="download-log">
                <a target="_blank" href="download.php?arquivo=<?php echo  $this->view->arquivo; ?>">
                    <img src="images/icones/t3/caixa2.jpg">
                    <br>
                    <p>erros_importacao_itens_essenciais.txt</p>
                </a>
            </div>
            <div class="clear"></div>
        </div>
    </div>
<?}?>