<? require_once '_header.php' ?>

<form method="get" action="<?= $_SERVER['PHP_SELF'] ?>">

    <? if ($message) echo $message ?>

    <div class="bloco_titulo">Pesquisa</div>
    <div class="bloco_conteudo">    
        <div class="formulario">   
            <div class="left">
                <div class="campo data">      
                    <label for="data_inicial">Período</label>
                    <input type="text" id="data_inicial" name="data_inicial" 
                        value="<?= $dataInicial ?>" class="campo" />
                </div>
                <div class="campo label-periodo">a</div>
                <div class="campo data">         
                    <label for="data_final">&nbsp;</label>
                    <input type="text" id="data_final" name="data_final" 
                        value="<?= $dataFinal ?>" class="campo" />
                </div>
                
                <div class="clear"></div>
                
                <div class="campo maior">
                    <label for="cliente">Cliente</label>
                    <input type="text" class="campo" id="cliente" name="cliente" 
                        style="margin-left: 0 !important;"
                        value="<?= $this->populate('cliente') ?>" />
                </div>
                
                <div class="clear"></div>
                
                <div class="campo medio">
                    <label for="placa">Placa</label>
                    <input type="text" class="campo" id="placa" name="placa"
                        maxlength="9" style="margin-left: 0 !important;"
                        value="<?= $this->populate('placa') ?>" />
                </div>
                
                <div class="clear"></div>
                
                <div class="campo medio">
                    <label for="motivo">Motivo</label>
                    <select name="motivo" id="motivo">
                        <option value="0">Escolha</option>
                        <? foreach ($listaMotivos as $motivo): ?>
                            <option value="<?= $motivo['mrescoid'] ?>" 
                                <?= ($this->populate('motivo') == $motivo['mrescoid']) ? 'selected="selected"' : '' ?>>
                                <?= $motivo['mrescdescricao'] ?>
                            </option>
                        <? endforeach ?>
                    </select>
                </div>
                
                <div class="clear"></div>
                
                <div class="campo medio">
                    <label for="contratos">Status Contrato</label>
                    <select name="contratos" id="contratos">
                        <option value="0">Escolha</option>
                        <? foreach ($listaTiposContrato as $valor => $desc): ?>
                            <option value="<?= $valor ?>"
                                <?= ($this->populate('contratos') == $valor) ? 'selected="selected"' : '' ?>>
                                <?= $desc ?>
                            </option>
                        <? endforeach ?>
                    </select>
                </div>
                
                <div class="clear"></div>
                
                <div class="campo medio">
                    <label for="classe_termo">Classe Equipamento</label>
                    <select name="classe_termo" id="classe_termo">
                        <option value="0">Escolha</option>
                        <? foreach ($listaClasseTermos as $classeTermo): ?>
                            <option value="<?= $classeTermo['eqcoid'] ?>"
                                <?= ($this->populate('classe_termo') == $classeTermo['eqcoid']) ? 'selected="selected"' : '' ?>>
                                <?= $classeTermo['eqcdescricao'] ?>
                            </option>
                        <? endforeach ?>
                    </select>
                </div>
            </div>
            
            <div class="right">
                <div class="campo medio">
                    <label for="termo">Contrato</label>
                    <input type="text" name="termo" id="termo"
                        class="mask-numbers"
                        value="<?= $this->populate('termo') ?>" />
                </div>
                
                <div class="clear"></div>
                
                <div class="campo medio">
                    <label for="responsavel">Responsável</label>
                    <select name="responsavel" id="responsavel">
                        <option value="0">Escolha</option>                        
                        <? foreach ($listaResponsaveis as $responsavel): ?>
                            <option value="<?= $responsavel['cd_usuario'] ?>"
                                <?= ($this->populate('responsavel') == $responsavel['cd_usuario']) ? 'selected="selected"' : '' ?>>
                                <?= $responsavel['ds_login'] ?>
                            </option>
                        <? endforeach ?>
                    </select>
                </div>
                
                <div class="clear"></div>
                
                <div class="campo menor">
                    <label for="valor_inicial">Valor</label>
                    <input type="text" name="valor_inicial" id="valor_inicial" 
                        class="mask-money" size="12"
                        value="<?= $this->populate('valor_inicial') ?>" /> 
                </div>
                <div class="campo label-periodo">a</div>

                <div class="campo menor">          
                    <label for="valor_inicial">&nbsp;</label> 
                    <input type="text" name="valor_final" id="valor_final" 
                        class="mask-money" size="12"
                        value="<?= $this->populate('valor_final') ?>" />
                </div>
                
                <div class="clear"></div>

                <div class="campo medio">
                    <label for="status">Status Rescisão</label>
                    <select name="status" id="status">
                        <option value="0">Escolha</option>
                        <? foreach ($listaStatusRescisao as $valor => $desc): ?>
                            <option value="<?= $valor ?>"                                
                                <?= ($this->populate('status') == $valor) ? 'selected="selected"' : '' ?>>
                                <?= $desc ?>
                            </option>
                        <? endforeach ?>
                    </select>
                </div>   
                
                <div class="clear"></div>

                <div class="campo medio">
                    <label for="campo_2">UF</label>
                    <select name="uf" id="uf">
                        <option value="0">Escolha</option>
                        <? foreach ($listaUfs as $uf): ?>
                            <option value="<?= $uf['endvuf'] ?>"                                                                
                                <?= ($this->populate('uf') == $uf['endvuf']) ? 'selected="selected"' : '' ?>>
                                <?= $uf['endvuf'] ?>
                            </option>
                        <? endforeach ?>
                    </select>
                </div>                
            </div>         
        </div>
    
        <div class="clear"></div>
    </div>
   
    <div class="bloco_acoes">
        <button type="submit" name="acao" value="Pesquisar">Pesquisar</button>
        <button type="button" id="novo-contrato">Novo</button>
    </div>
</form> 

<? require_once '_pesquisa.php' ?>

<? require_once '_footer.php' ?>