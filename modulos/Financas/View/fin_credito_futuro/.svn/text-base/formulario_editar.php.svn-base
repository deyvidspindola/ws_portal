<div class="modulo_conteudo">
    <div class="bloco_titulo">Dados Gerais</div>
    <div class="bloco_conteudo">
        <div class="conteudo">
            <table style="width:95% !important">
                <tbody>
                    <tr>

                        <td class="label menor">Nome do Cliente</td>
                        <td colspan="3"><?php echo $this->view->parametros->cadastro['clinome'] ?></td>
                        <td class="label menor"><?php echo $this->view->parametros->cadastro['tipo'] == 'J'  ? 'CNPJ' : 'CPF'; ?></td>
                        <td><?php echo $this->view->parametros->cadastro['doc'] ?></td>
                    </tr>
                    <tr>

                        <td class="label menor">Motivo de Crédito</td>
                        <td colspan="3"><?php echo $this->view->parametros->cadastro['motivo_credito_descricao'] ?></td>
                        <td class="label menor">Status</td>
                        <td class="medio"><?php echo $this->view->parametros->cadastro['status_descricao'] ?> <?php echo !is_null($this->view->parametros->cadastro['cfodt_exclusao']) ? '(Excluído)' : '' ?> <?php echo !is_null($this->view->parametros->cadastro['cfodt_encerramento']) ? '(Encerrado)' : '' ?></td>
                    </tr>
                    <tr>
                        <td class="label">Forma de inclusão</td>

                        <td><?php echo $this->view->parametros->cadastro['forma_inclusao'] ?></td>
                        <td class="label menor"></td>
                        <td class="menor"></td>
                        <td class="label menor">Saldo</td>
                        <td class="medio"><?php echo $this->view->parametros->cadastro['saldo_literal'] ?></td>
                    </tr>
                    <tr>

                        <td class="label">Contrato Indicado</td>
                        <td><?php echo $this->view->parametros->cadastro['cfoconnum_indicado'] ?></td>
                        <td class="label"></td>
                        <td></td>
                        <td class="label">Campanha Promocional</td>
                        <td><?php echo $this->view->parametros->cadastro['campanha'] ?></td>
                    </tr>

                    <tr>
                        <td class="label">Protocolo</td>
                        <td colspan="2"><?php echo $this->view->parametros->cadastro['cfoancoid'] ?></td>
                        <td class="label"></td>
                        <td class="label">Valor Total NF(s) Contestada(s)</td>
                        <td colspan="2"><?php echo $this->view->parametros->cadastro['valor_total_contestadas'] ?></td>
                    </tr>
                </tbody>

            </table>
        </div>
    </div>

    <div class="bloco_acoes" id="bloco_acoes_visualizar" >
        
        
        <?php        
        /**
         * Verificação para mostra o botão Excluir
         *
         * Se nao possui  movimentação ativa
         * Se a forma de inclusão for manual ( = 1)
         * Se o status for Aprovado ou Pendente (1 || 3)
        **/        
        if (
                is_null($this->view->parametros->cadastro['cfodt_avaliacao']) &&
                is_null($this->view->parametros->cadastro['credito_futuro_movimentacao_ativa']) &&
                trim($this->view->parametros->cadastro['cfoforma_inclusao']) == '1' &&
                in_array($this->view->parametros->cadastro['cfostatus'], array('1', '3'))
                
                && is_null($this->view->parametros->cadastro['cfodt_exclusao'])
                && is_null($this->view->parametros->cadastro['cfodt_encerramento'])
        ) : ?>                
            <button id="bt_excluir" type="button">Excluir</button>
        <?php endif; ?>
         
        <?php
            /**
             * Verificação para mostrar botão encerrar
             * 
             * Se a forma de inclusão for automática ( =2)
             * OU
             * Se a forma de inclusão for manual ( = 1) e possuir movimentacao ativa.
             */
            if (
                    trim($this->view->parametros->cadastro['cfoforma_inclusao']) == '2' ||
                    (
                    trim($this->view->parametros->cadastro['cfoforma_inclusao']) == '1' &&
                    !is_null($this->view->parametros->cadastro['credito_futuro_movimentacao_ativa'])
                    )
                    
                    && is_null($this->view->parametros->cadastro['cfodt_exclusao'])
                    && is_null($this->view->parametros->cadastro['cfodt_encerramento'])
            ) : ?>
                <button id="bt_encerrar" type="button">Encerrar</button>
            <?php endif; ?>    
        
        <button id="bt_retornar" type="button">Retornar</button>
    </div>


    <div class="separador"></div>

    <div class="bloco_titulo">Valores do Crédito</div>
    <div class="bloco_conteudo">
        <div class="formulario">

            <form id="formulario_editar" action="" method="POST">
                
                <input type="hidden" id="cfooid" name="cadastro[cfooid]" value="<?php echo $this->view->parametros->cadastro['cfooid'] ?>"/>
                
                <?php //tipo_motivo_credito
                    switch ($this->view->parametros->cadastro['tipo_motivo_credito']) {
                        
                        //contestação
                        case '1':
                            require_once _MODULEDIR_ . "Financas/View/fin_credito_futuro/editar/contestacao_formulario_editar.php";
                            break;
                        
                        //indicação de amigo
                        case '2':
                            require_once _MODULEDIR_ . "Financas/View/fin_credito_futuro/editar/indicacao_formulario_editar.php";
                            break;
                        
                        //isenção
                        case '3':
                            require_once _MODULEDIR_ . "Financas/View/fin_credito_futuro/editar/isencao_formulario_editar.php";
                            break;
                        
                        default:
                            require_once _MODULEDIR_ . "Financas/View/fin_credito_futuro/editar/default_formulario_editar.php";
                            break;
                    }
                
                ?>
                <div class="clear"></div>
            </form>

            
        </div>
    </div>
    
     <div class="bloco_acoes">
        <button id="bt_concluir" type="button">Confirmar</button>			
    </div>

    <div class="separador"></div>

    <div class="bloco_titulo">Histórico</div>

    <div class="bloco_conteudo">
        <div class="listagem">
            <table>
                <thead>
                    <tr>
                        <th>Operação</th>
                        <th>Data / Hora</th>
                        <th>Usuário</th>
                        <th>Origem</th>
                        <th>Observação/Justificativa</th>
                        <th>Ação</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($this->view->parametros->historico as $operacao) : ?>
                    <?php $class =  $class == 'impar' ? 'par' : 'impar' ?>
                    <tr class="<?php echo $class; ?>">
                        <td class="centro" ><?php echo $operacao->operacao; ?></td>
                        <td class="centro" ><?php echo $operacao->data_hora; ?></td>
                        <td><?php echo $operacao->usuario; ?></td>
                        <td><?php echo $operacao->origem; ?></td>

                        <?php 
                        $texto = '<center>-</center>';
                        if ( trim($operacao->observacao) != '' ) {
                          $texto = $operacao->observacao;
                        } else if ( trim($operacao->justificativa) != '' ) {
                          $texto = $operacao->justificativa;
                        }
                        ?>

                        <td><?php echo wordwrap(trim($texto),30,'<br/>',true); ?></td>
                        <td class="centro">
                            <a class="detalhes_historico" data-cfhoid="<?php echo $operacao->cfhoid; ?>" href="javascript:void(0)">
                                <img src="images/detalhes.png" class="icone" />
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
                <tfoot>
                    <tr>
                        <?php $textoCount =  count($this->view->parametros->historico) > 1 ? 'registros encontrados.' : 'registro encontrado.'?>
                        <td colspan="6"><?php echo count($this->view->parametros->historico) . ' ' . $textoCount; ?> </td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
    
    
    <div id="dialog-excluir-credito-futuro" title="EXCLUIR CRÉDITO FUTURO?" class="invisivel">
        
        <div class="separador"></div>
            
        <div id="excluir_mensagem"></div>
            
        <div class="formulario">
            <form id="form_excluir" action="fin_credito_futuro.php" method="POST">
                <input type="hidden" name="acao" value="excluir">
                <input type="hidden" id="cfooid" name="cfooid" value="<?php echo $this->view->parametros->cadastro['cfooid'] ?>"/>
                <label style="font-size: 10px; color: gray">
                    Campos com <b>*</b> são obrigatórios.
                </label>
                <div class="separador"></div>
                <div class="campo maior">
                    <label>Justificativa *</label>
                    <textarea id="justificativa_exclusao" name="justificativa" rows="4" cols="25"></textarea>
                </div>
                <div style="clear: both"></div>
            </form>
                
        </div>
        <div class="separador"></div>
            
    </div>
    
    
    <div id="dialog-encerrar-credito-futuro" title="ENCERRAR CRÉDITO FUTURO?" class="invisivel">
        
        <div class="separador"></div>
            
        <div id="encerrar_mensagem"></div>
            
        <div class="formulario">
            <form id="form_encerrar" action="fin_credito_futuro.php" method="POST">
                <input type="hidden" name="acao" value="encerrar">
                <input type="hidden" id="cfooid" name="cfooid" value="<?php echo $this->view->parametros->cadastro['cfooid'] ?>"/>
                <label style="font-size: 10px; color: gray">
                    Campos com <b>*</b> são obrigatórios.
                </label>
                <div class="separador"></div>
                <div class="campo maior">
                    <label>Justificativa *</label>
                    <textarea id="justificativa_encerramento" name="justificativa" rows="4" cols="25"></textarea>
                </div>
                <div style="clear: both"></div>
            </form>
        </div>
        <div class="separador"></div>
            
    </div>

    <div id="dialog-reprovar-credito-futuro" title="REPROVAR CRÉDITO FUTURO?" class="invisivel">
        
        <div class="separador"></div>
            
        <div id="reprovar_mensagem"></div>
            
        <div class="formulario">
            <form id="form_reprovar" action="fin_credito_futuro_pendentes.php" method="POST">
                <input type="hidden" name="acao" value="reprovar">
                <input type="hidden" id="cfooid" name="cfooid" value="<?php echo $this->view->parametros->cadastro['cfooid'] ?>"/>
                <input type="hidden" id="excluir_listagem" name="excluir_listagem" value="1"/>
                <label style="font-size: 10px; color: gray">
                    Campos com <b>*</b> são obrigatórios.
                </label>
                <div class="separador"></div>
                <div class="campo maior">
                    <label>Justificativa *</label>
                    <textarea id="justificativa_reprova" name="justificativa" rows="4" cols="25"></textarea>
                </div>
                <div style="clear: both"></div>
            </form>
                
        </div>
        <div class="separador"></div>
            
    </div>
    
    <div id="dialog-detalhes-historico-credito-futuro" title="DETALHES" class="invisivel">
                    <div class="formulario">
                           <div class="campo menor">
                                  <label>Operação:</label>
                           </div>
                           <div class="campo">
                                  <label><b class="detalhe_historico_valor historico_detalhe_operacao" >Inclusão</b></label>
                           </div>
                           
                           <div class="clear"></div>
                           
                           <div class="campo menor">
                                  <label>Usuário:</label>
                           </div>
                           <div class="campo" style="text-align: right">
                                  <label><b class="detalhe_historico_valor historico_detalhe_usuario" >Beltrano Filho Jr</b></label>
                           </div>
                           
                           <div class="clear"></div>
                           
                           <div class="campo menor">
                                  <label>Data / Hora:</label>
                           </div>
                           <div class="campo">
                                  <label><b class="detalhe_historico_valor historico_detalhe_data_hora" >10//09/2013 10:23</b></label>
                           </div>
                           
                           
                           <div class="clear"></div>
                           
                           <div class="campo menor">
                                  <label>Origem:</label>
                           </div>
                           <div class="campo">
                                  <label><b class="detalhe_historico_valor historico_detalhe_origem" >Crédito Futuro</b></label>
                           </div>

                           <div class="clear"></div>
                           
                           <div class="campo menor">
                                  <label>Status:</label>
                           </div>
                           <div class="campo">
                                  <label><b class="detalhe_historico_valor historico_detalhe_status" ></b></label>
                           </div>
                           
                           <div class="clear"></div>
                           
                           <div class="clear"></div>
                           
                           <div class="campo menor">
                                  <label>Obrigação Financeira de Desconto:</label>
                           </div>
                           <div class="campo">
                                  <label><b class="detalhe_historico_valor historico_detalhe_obrigacao_fincanceira_desconto_descricao" >Desconto</b></label>
                           </div>
                           
                           <div class="clear"></div>
                           
                           <div class="campo menor">
                                  <label>Tipo de Desconto:</label>
                           </div>
                           <div class="campo">
                                  <label><b class="detalhe_historico_valor historico_detalhe_tipo_desconto_descricao" >Percentual</b></label>
                           </div>
                           
                           <div class="clear"></div>
                           
                           <div class="campo menor">
                                  <label>Aplicação:</label>
                           </div>
                           <div class="campo">
                                  <label><b class="detalhe_historico_valor historico_detalhe_forma_aplicacao_descricao" >Integral</b></label>
                           </div>
                           
                           <div class="clear"></div>
                           
                           <div class="campo menor">
                                  <label>Aplicado sobre:</label>
                           </div>
                           <div class="campo">
                                  <label><b class="detalhe_historico_valor historico_detalhe_aplicar_desconto_descricao" >Monitoramento</b></label>
                           </div>
                           
                           <div class="clear"></div>
                           
                           <div class="campo menor">
                                  <label>Saldo:</label>
                           </div>
                           <div class="campo">
                                  <label><b class="detalhe_historico_valor historico_detalhe_saldo" >1 x 100%</b></label>
                           </div>
                           
                           <div class="clear"></div>
       
                    
                           <div class="campo menor">
                                  <label>Nota Fiscal:</label>
                           </div>
                           <div class="campo">
                                  <label><b class="detalhe_historico_valor historico_detalhe_nota_fiscal" >-</b></label>
                           </div>
                           
                           <div class="clear"></div>
                           
                           <div class="campo menor">
                                  <label>Dt. Emissão:</label>
                           </div>
                           <div class="campo">
                                  <label><b class="detalhe_historico_valor historico_detalhe_nota_fiscal_data_emissao" >-</b></label>
                           </div>
                           
                           <div class="clear"></div>
                           
                           <div class="campo menor">
                                  <label>Vl. Total Itens:</label>
                           </div>
                           <div class="campo">
                                  <label><b class="detalhe_historico_valor historico_detalhe_nota_fiscal_valor_total_item">-</b></label>
                           </div>
                           
                           <div class="clear"></div>
                           
                           <div class="campo menor">
                                  <label>Vl. Total:</label>
                           </div>
                           <div class="campo">
                                  <label><b class="detalhe_historico_valor historico_detalhe_nota_fiscal_valor_total" >-</b></label>
                           </div>
                           
                           <div class="clear"></div>
                           
                           <div class="campo menor">
                                  <label>Valor Desconto Concedido:</label>
                           </div>
                           <div class="campo">
                                  <label><b class="detalhe_historico_valor historico_detalhe_nota_fiscal_desconto_aplicado" >-</b></label>
                           </div>
                           
                           <div class="clear"></div>
                           <div class="separador"></div>
                           
                           <div class="campo menor">
                                  <label>Observação:</label>
                                  <textarea class="detalhe_historico_valor historico_detalhe_observacao desabilitado" width="500px" rows="3" cols="55" style="width: 330px !important" readonly="readonly"></textarea>
                           </div>
                           
                           <div class="clear"></div>
                           
                           <div class="campo menor">
                                  <label>Justificativa:</label>
                                  <textarea class="detalhe_historico_valor historico_detalhe_justificativa desabilitado" rows="3" cols="55" style="width: 330px !important" readonly="readonly">Lorem ipsum dolor</textarea>
                           </div>
                    </div>
        <div class="clear"></div>
                           <div class="separador"></div>
        
             </div>