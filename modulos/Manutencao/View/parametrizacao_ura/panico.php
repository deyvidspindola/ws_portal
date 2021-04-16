<? require_once '_header.php' ?>

<script type="text/javascript" src="modulos/web/js/man_parametrizacao_ura.js"></script>

<form method="post" action="<?= $_SERVER['PHP_SELF'] ?>">
    <div class="bloco_titulo">Desconsiderar Pânicos</div>
    <div class="bloco_conteudo">
        <div class="conteudo">
            <div class="campo medio">
                <label>Tipo:</label>
                <div class="combo-check">
                    <ul>
                        <? foreach ($tiposPanico as $item): ?>
                        <li>
                            <label for="puppantoid_<?= $item['pantoid'] ?>"><?= $item['pantdescricao'] ?></label>
                            <input type="checkbox" id="puppantoid_<?= $item['pantoid'] ?>"
                                name="puppantoid[]" 
                                value="<?= $item['pantoid'] ?>" <? $this->checkArray($item['pantoid'], $form['puppantoid']) ?> />
                        </li>
                        <? endforeach ?>
                    </ul>
                </div>
            </div>
            
            <div class="campo maior2">
                <p>Mesma placa com acionamento de pânicos menor que: <input class="small-input" type="text" id="pupacionamento" name="pupacionamento"  value="<?= $form['pupacionamento'] ?>" /> minutos</p>
                <p>Pânicos de Instalação: <input type="checkbox" name="pupinstalado" value="true" <? $this->isChecked($form['pupinstalado']) ?> /></p>
               
            </div>
        </div>
    
        <div class="clear"></div>
    </div>
    
    <div class="bloco_titulo block-margin">Desconsiderar Contratos</div>
    <div class="bloco_conteudo">
        <div class="conteudo">
            <div class="left">
                <div class="campo maior">
                    <p>Possui Gerenciadora: <input type="checkbox" name="puppossui_gerenciadora" 
                        value="true" <? $this->isChecked($form['puppossui_gerenciadora']) ?> /></p>
                    <p>Possui Cadastro de Suspensão de Pânico GSM: <input type="checkbox" 
                        name="puppossui_suspensao" value="true" <? $this->isChecked($form['puppossui_suspensao']) ?> /></p>
                     <p>Pendência Financeira maior que: <input class="small-input" type="text" 
                        name="puppendencia_financeira"  id="puppendencia_financeira"value="<?= $form['puppendencia_financeira'] ?>" /> dias</p>
                </div>
                
                <div class="campo maior">
                    <label>Botão de Pânico:</label>
                    <div class="combo-check">
                        <ul>
                        	<li>
                                <label for="panico_99">&nbsp;</label>
                                <input type="checkbox" id="panico_99" name="pupporta_panico[]" value="99" 
                                    <? $this->checkArray(99, $form['pupporta_panico']) ?> />
                            </li>
                            <li>
                                <label for="panico_0">Não Instalado</label>
                                <input type="checkbox" id="panico_0" name="pupporta_panico[]" value="0" 
                                    <? $this->checkArray(0, $form['pupporta_panico']) ?> />
                            </li>
                            <li>
                                <label for="panico_1">1</label>
                                <input type="checkbox" id="panico_1" name="pupporta_panico[]" value="1" 
                                    <? $this->checkArray(1, $form['pupporta_panico']) ?> />
                            </li>
                            <li>
                                <label for="panico_9">9</label>
                                <input type="checkbox" id="panico_9" name="pupporta_panico[]" value="9" 
                                    <? $this->checkArray(9, $form['pupporta_panico']) ?> />
                            </li>
                        </ul>
                    </div>
                </div>
                
                <div class="borda-fix">
                    <p><b>Com Ordem de Serviço</b></p>
                    <div class="campo medio clear-left">
                        <label>Tipo:</label>
                        <div class="combo-check">
                            <ul>
                                <? foreach ($tiposOrdemServico as $item): ?>
                                <li>
                                    <label for="pupostoid_<?= $item['ostoid'] ?>"><?= $item['ostdescricao'] ?></label>
                                    <input type="checkbox" id="pupostoid_<?= $item['ostoid'] ?>"
                                        name="pupostoid[]" value="<?= $item['ostoid'] ?>"
                                        <? $this->checkArray($item['ostoid'], $form['pupostoid']) ?> disabled="disabled" />
                                </li>
                                <? endforeach ?>
                            </ul>
                        </div>
                    </div>
                    
                    <div class="campo medio">
                        <label>Item:</label>
                        <div class="combo-check">
                            <ul>
                                <li>
                                    <label for="pupitem_a">Acessórios</label>
                                    <input type="checkbox" id="pupitem_a" name="pupitem[]" value="A"
                                        <? $this->checkArray('A', $form['pupitem']) ?> disabled="disabled" />
                                </li>                            
                                <li>
                                    <label for="pupitem_e">Equipamento</label>
                                    <input type="checkbox" id="pupitem_e" name="pupitem[]" value="E"
                                        <? $this->checkArray('E', $form['pupitem']) ?> disabled="disabled" />
                                </li>
                            </ul>
                        </div>
                    </div>
                    
                    <div class="campo medio clear-left">
                        <label>Status:</label>
                        <div class="combo-check">
                            <ul>
                                <? foreach ($statusOrdemServico as $item): ?>
                                <li>
                                    <label for="pupossoid_<?= $item['ossoid'] ?>"><?= $item['ossdescricao'] ?></label>
                                    <input type="checkbox" id="pupossoid_<?= $item['ossoid'] ?>"
                                        name="pupossoid[]" value="<?= $item['ossoid'] ?>"
                                        <? $this->checkArray($item['ossoid'], $form['pupossoid']) ?> disabled="disabled" />
                                </li>
                                <? endforeach ?>
                            </ul>
                        </div>
                    </div>
                    
                    <div class="campo medio">
                        <label>Defeito Alegado:</label>
                        <div class="combo-check">
                            <ul>
                                <? foreach ($tiposDefeitoAlegado as $item): ?>
                                <li>
                                    <label for="pupotdoid_<?= $item['otdoid'] ?>"><?= $item['otddescricao'] ?></label>
                                    <input type="checkbox" id="pupotdoid_<?= $item['otdoid'] ?>" 
                                        name="pupotdoid[]" value="<?= $item['otdoid'] ?>"
                                        <? $this->checkArray($item['otdoid'], $form['pupotdoid']) ?> disabled="disabled" />
                                </li>
                                <? endforeach ?>
                            </ul>
                        </div>
                    </div>
                    <div class="clear"></div>
                </div>
            </div>
            
            <div class="right">
                <div class="campo medio">
                    <label>Tipo de Contrato:</label>
                    <div class="combo-check">
                        <ul>
                            <? foreach ($tiposContrato as $item): ?>
                            <li>
                                <label for="puptpcoid_<?= $item['tpcoid'] ?>"><?= $item['tpcdescricao'] ?></label>
                                <input type="checkbox" id="puptpcoid_<?= $item['tpcoid'] ?>"
                                    name="puptpcoid[]" value="<?= $item['tpcoid'] ?>" 
                                    <? $this->checkArray($item['tpcoid'], $form['puptpcoid']) ?> />
                            </li>
                            <? endforeach ?>
                        </ul>
                    </div>
                </div>
                
                <div class="campo medio">
                    <label>Status Contrato:</label>
                    <div class="combo-check">                    
                        <ul>
                            <? foreach ($statusContrato as $item): ?>
                            <li>
                                <label for="pupcsioid_<?= $item['csioid'] ?>"><?= $item['csidescricao'] ?></label>
                                <input type="checkbox" id="pupcsioid_<?= $item['csioid'] ?>" 
                                    name="pupcsioid[]" value="<?= $item['csioid'] ?>" 
                                    <? $this->checkArray($item['csioid'], $form['pupcsioid']) ?> />
                            </li>
                            <? endforeach ?>
                        </ul>
                    </div>
                </div>
                
                <div class="campo medio clear-left">
                    <label>Ocorrência com Status:</label>
                    <div class="combo-check">                
                        <ul>
                            <? foreach ($ocorrenciasStatus as $value => $label): ?>
                            <li>
                                <label for="pupocostatus_<?= $value ?>"><?= $label ?></label>
                                <input type="checkbox" id="pupocostatus_<?= $value ?>" 
                                    name="pupocostatus[]" value="<?= $value ?>" 
                                    <? $this->checkArray($value, $form['pupocostatus']) ?> />
                            </li>
                            <? endforeach ?>
                        </ul>
                    </div>
                </div>
            </div>        
        </div>
        
        <div class="clear"></div>
    </div>
   
    <div class="bloco_acoes">
        <button type="submit" name="acao" value="panicoSalvar">Salvar</button>  		         		
        <button type="submit" name="acao" value="panicoDefault"    onclick="javascript:if( confirm( 'Deseja gravar os valores padrão?' ) ) { document.frm.acao.value='delete';document.frm.submit();}else { return false; }"> Predefinidos</button>
            
    </div>
</form>

<? require_once '_footer.php' ?>