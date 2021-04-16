<div class="bloco_titulo">Cadastro</div>
<div class="modulo_conteudo">
    <input type="hidden" id="ordraoid" name="ordraoid" value="">
    <input type="hidden" id="editar" name="editar" value="<?php echo $_GET['acao']; ?>">
    <div id="dados_principais">
        <div class="bloco_titulo">Dados Principais</div>
        <div class="bloco_conteudo">
            <div class="formulario">
                <div class="campo medio">
                    <label for="ordraostoid">Tipo *</label>
                    <select id="ordraostoid" name="ordraostoid" <? if($this->view->parametros->ordraoid != null) echo "disabled" ?>>
                        <option value="">Escolha</option>
                        <? foreach ($this->view->dados->tipoCadastro as $valor) { ?>
                            <option <? if($this->view->parametros->ordraostoid == $valor->ostoid) echo "selected" ?> value="<? echo $valor->ostoid; ?>"><? echo $valor->ostdescricao; ?></option>
                        <? } ?>
                    </select>
                </div>
    
                <div class="clear"></div>
                
                <fieldset class="maior opcoes-display-block">
                    <legend>Opções</legend>
                    <input id="ordrapermite_ordens_simultaneas" name="ordrapermite_ordens_simultaneas" type="checkbox" <? if($this->view->parametros->ordrapermite_ordens_simultaneas == "t") echo "checked" ?>>
                    <label for="ordrapermite_ordens_simultaneas">Permite O.S. Simultânea</label>
                    <input id="ordrapermite_tipo_motivo_distinto" name="ordrapermite_tipo_motivo_distinto" type="checkbox" <? if($this->view->parametros->ordrapermite_tipo_motivo_distinto == "t") echo "checked" ?>>
                    <label for="ordrapermite_tipo_motivo_distinto">Permite Tipos de Motivos Distintos</label>
                </fieldset>
    
                <div class="clear"></div>
                
            </div>
        </div>
        <div class="bloco_acoes">
            <button id="btn_confirmar"  type="button">Confirmar</button>
            <button id="bt_voltar"      type="button">Voltar</button>
        </div>
    </div>
    
    <div id="os_simultanea">
        <div class="separador"></div>
        <div class="bloco_titulo">O.S. Simultânea</div>
        <div class="bloco_conteudo">
            <div class="formulario">
                <div class="campo medio">
                    <label for="simultanea_tipo_permitido">Tipos Permitidos *</label>
                    <select id="simultanea_tipo_permitido" name="simultanea_tipo_permitido">
                        <option value="">Escolha</option>
                        <? foreach ($this->view->dados->tipo as $valor) { ?>
                            <option value="<? echo $valor->ostoid; ?>"><? echo $valor->ostdescricao; ?></option>
                        <? } ?>
                    </select>
                </div>
                
                <div class="campo medio">
                    <label for="simultanea_agendada">Agendada *</label>
                    <select id="simultanea_agendada" name="simultanea_agendada">
                        <option value="">Escolha</option>
                        <option value="0">Não</option>
                        <option value="1">Sim</option>
                    </select>
                </div>
                    
                <div class="campo medio">
                    <label for="simultanea_situacao">Situação *</label>
                    <select id="simultanea_situacao" name="simultanea_situacao" disabled="">
                        <option value="">Escolha</option>
                        <option value="0">Antes de D0</option>
                        <option value="1">D0</option>
                    </select>
                </div>
                
                <div class="clear"></div>
                
            </div>
        </div>
        <div class="bloco_acoes">
            <button id="simultanea_adicionar" name="simultanea_adicionar" type="button">Adicionar</button>
        </div>
    </div>
    
    

    <div id="motivos_distintos">
        <div class="separador"></div>
        <div class="bloco_titulo">Motivos</div>
        <div class="bloco_conteudo">
            <div class="formulario">
                
                <div class="campo medio">
                    <label for="motivo_tipo_permitido">Tipos Permitidos *</label>
                    <select id="motivo_tipo_permitido" name="motivo_tipo_permitido">
                        <option value="">Escolha</option>
                        <? foreach ($this->view->dados->tipo as $valor) { ?>
                            <option value="<? echo $valor->ostoid; ?>"><? echo $valor->ostdescricao; ?></option>
                        <? } ?>
                    </select>
                </div>             
                
                <div class="campo medio">
                    <label for="motivo_agendada">Agendada *</label>
                    <select id="motivo_agendada" name="motivo_agendada">
                        <option value="">Escolha</option>
                        <option value="0">Não</option>
                        <option value="1">Sim</option>
                    </select>
                </div>

                <div id="campo_motivo_situacao" class="campo medio">
                    <label for="motivo_situacao">Situação *</label>
                    <select id="motivo_situacao" name="motivo_situacao" disabled="">
                        <option value="">Escolha</option>
                        <option value="0">Antes de D0</option>
                        <option value="1">D0</option>
                    </select>
                </div>
                
                <div class="clear"></div>
            </div>
        </div>
        <div class="bloco_acoes">
            <button id="motivos_adicionar" name="motivos_adicionar" type="button">Adicionar</button>
        </div>
    </div>
    
    <div id="regras_cadastradas">
        <div class="separador"></div>
        <div class="bloco_titulo">Regras Cadastradas</div>
        <div class="bloco_conteudo">
            <div class="listagem">
                <table id="tabela_regras">
                    <thead>
                        <tr>
                            <th>Tipo Parametrização</th>
                            <th>Tipo Permitido</th>
                            <th>Situação</th>
                            <th>Agendada</th>
                            <th>Ação</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                    <tfoot>
                        <tr>
                            <td id="footer_regras" colspan="6"></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>

</div>