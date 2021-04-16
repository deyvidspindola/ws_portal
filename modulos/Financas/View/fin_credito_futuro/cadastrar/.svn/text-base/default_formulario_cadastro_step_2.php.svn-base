
<div class="bloco_titulo"><?php echo $this->verificarTipoRequisicao('Escolha o cliente para dar crédito'); ?></div>
    <div class="bloco_conteudo">
        <div class="formulario">
            <form id="form_cadastrar"  method="post" action="">
                <input type="hidden" id="acao" name="acao" value="cadastrar"/>
                <input type="hidden" id="step" name="step" value = "step_2" />
                
                <!--esses dois campos é usado no caso de link direto para o passo tres
                motivos de créditos co tipos diferentes de Constestação de contas e indicação de amigos -->
                <input type="hidden" id="motivo_credito_id" name="cadastro[cfocfmcoid]" value = "" />
                <input type="hidden" id="tipo_motivo" name="cadastro[tipo_motivo]" value = "" />
                <input type="hidden" id="motivo_descricao" name="cadastro[motivo_descricao]" value = "" />
                <input type="hidden" id="voltar" name="voltar" value = "0" />
                

                <div class="campo maior">
                    <label style="color: gray">
                        <?php echo $this->verificarTipoRequisicao('Clique no <strong>Motivo do Crédito</strong> para avançar.') ?>
                    </label>
                </div>

                <div class="campo maior">
                    <label>
                        Nome do Cliente:
                    </label>
                    <input readonly class="campo" type="text" value="<?php echo !empty($_SESSION['credito_futuro']['step_1']['razao_social']) ? $this->verificarTipoRequisicao(trim($_SESSION['credito_futuro']['step_1']['razao_social'])) : '' ?>" >
                </div>

                <div class="clear"></div>
                <div class="separador"></div>

                <div class="listagem_grande">
                    <div class="bloco_titulo"><?php echo $this->verificarTipoRequisicao('Motivos de Crédito');?></div>
                    <div class="bloco_conteudo nopadding">
                        
                        <?php if ( count($this->view->parametros->listaMotivoCredito) ) : ?>
                        
                        <ul class="listagem_itens nopadding nomargin">
                            
                            <?php foreach ($this->view->parametros->listaMotivoCredito as $motivo) : ?>
                                <?php
                                    $motivoParametrizar = '';
                                    
                                    if ($motivo->cfmctipo == '1') {
                                        $motivoParametrizar = 'contestacao_contas';
                                    }
                                    
                                    if ($motivo->cfmctipo == '2') {
                                        $motivoParametrizar = 'indicacao_amigo';
                                    }
                                ?>
                            <li>
                                <a data-tipo="<?php echo $motivo->cfmctipo ?>" data-descricao="<?php echo $this->verificarTipoRequisicao(trim($motivo->cfmcdescricao)); ?>" data-cfmcoid="<?php echo $motivo->cfmcoid ?>" class="<?php echo !empty($motivoParametrizar) ? $motivoParametrizar : 'motivoSemParametrizacao' ?>" href="javascript:void(0)"><?php echo wordwrap($this->verificarTipoRequisicao(trim($motivo->cfmcdescricao)),80, "<br/>", true); ?></a>
                            </li>
                            <?php endforeach; ?>
                        </ul>
                        
                        <?php endif; ?>
                    </div>
                </div>
                <div style="clear: both"></div>
            </form>
        </div>
    </div>

<script>
    jQuery(document).ready(function(){
        jQuery('#bt_avancar').addClass('invisivel');
    });
</script>