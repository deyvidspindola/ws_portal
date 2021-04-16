<?php 
require_once _MODULEDIR_ . "Financas/View/fin_cobranca_registrada/bloco_opcoes.php"; 
use module\Parametro\ParametroIntegracaoTotvs;
?>   

<div id="remessa" <?php echo ($this->view->parametros->acao == 'remessa' || trim($this->view->parametros->acao) == '' ) ?  '' : 'style="display:none;"' ?>>
    <div class="bloco_titulo">Remessa</div>

    <div class="bloco_conteudo">
        <div class="formulario">
            <form id="form_remessa"  method="post" action="">
                <input type="hidden" id="acao" name="acao" value="remessa"/>
                <input type="hidden" id="origem" name="origem" value="remessa"/>
                <input type="hidden" id="respostaSucesso" name="respostaSucesso" value=""/>
                <input type="hidden" id="respostaErro" name="respostaErro" value=""/>

                <div class="campo medio">
                    <label for="lbl_forma_cobranca">Forma de Cobrança</label>
                    <select id="ddl_forma_cobranca_remessa" name="ddl_forma_cobranca_remessa">
                        <?php foreach($this->view->comboFormaCobrancaRemessa as $item) { ?>
                            <option value="<?php echo $item->forcoid; ?>" <?php echo $this->view->parametros->ddl_forma_cobranca_remessa == $item->forcoid ? 'selected' : ''; ?>><?php echo $item->forcnome; ?></option>
                        <?php } ?>
                    </select>
                </div>

                <div class="clear"></div>

                <fieldset class="medio">
                    <label for="lbl_titulos_sem_remessa">Títulos Sem Remessa</label>
                    <input type="checkbox" id="chk_titulos_sem_remessa" name="chk_titulos_sem_remessa" <?php echo $this->view->parametros->chk_titulos_sem_remessa ? 'checked' : ''; ?> />
                </fieldset>

                <div class="clear"></div>

                <div class="campo medio">
                    <label for="lbl_num_remessa">Número Remessa</label>
                    <input id="txt_num_remessa" maxlength="10" name="txt_num_remessa" value="<?php echo $this->view->parametros->txt_num_remessa?>" class="campo" type="text" <?php echo $this->view->parametros->chk_titulos_sem_remessa ? 'disabled' : ''; ?>>
                </div>
                
                <div class="clear"></div>

                <div class="campo medio">
                    <label for="lbl_status">Status</label>
                    <select id="ddl_status_remessa" name="ddl_status_remessa" <?php echo $this->view->parametros->chk_titulos_sem_remessa ? 'disabled' : ''; ?>>
                        <option value="TO" <?php echo $this->view->parametros->ddl_status_remessa == '0' ? 'selected' : ''; ?>>Todos</option>
                        <option value="PA" <?php echo $this->view->parametros->ddl_status_remessa == 'PA' ? 'selected' : ''; ?>>Processada</option>
                        <option value="AG" <?php echo $this->view->parametros->ddl_status_remessa == 'AG' ? 'selected' : ''; ?>>Aguardando Registro</option>
                    </select>
                </div>

                <div class="clear"></div>

                <div class="campo data periodo">
                    <label id="lbl_periodo_dt_vencimento" style="<?php echo $this->view->parametros->chk_titulos_sem_remessa ? 'display:block;': 'display:none;'; ?>">Periodo Dt Vencimento *</label>
                    <label id="lbl_periodo_envio" style="<?php echo $this->view->parametros->chk_titulos_sem_remessa ? 'display:none;': 'display:block;'; ?>">Periodo de Envio *</label>
                    <div class="inicial">
                        <input class="campo"  type="text" name="dt_ini_remessa" id="dt_ini_remessa" maxlength="10" value="<?php echo !isset($this->view->parametros->dt_ini_remessa) ? date('d/m/Y') : $this->view->parametros->dt_ini_remessa; ?>" />
                    </div>
                    <div class="campo label-periodo">a</div>
                    <div class="final">
                        <input  class="campo"  type="text" name="dt_fim_remessa" id="dt_fim_remessa" maxlength="10" value= "<?php echo !isset($this->view->parametros->dt_fim_remessa) ? date('d/m/Y') : $this->view->parametros->dt_fim_remessa; ?>" />
                    </div>
                </div>

                <div class="clear"></div>
            </form>
        </div>
    </div>
    <div class="bloco_acoes">
        <button style="cursor: default;" type="button" id="btn_pesquisar_remessa">Pesquisar</button>
        <button style="cursor: default; display: none;" type="button" id="btn_gerar_csv_remessa">Gerar CSV</button>
    </div>

</div>

<div id="rejeitado" <?php echo ($this->view->parametros->acao == 'rejeitado') ?  '' : 'style="display:none;"' ?>>

    <div class="bloco_titulo">Títulos Rejeitados</div>
    <div class="bloco_conteudo">

        <div class="formulario">
            <form id="form_rejeitado"  method="post" action="">
                <input type="hidden" id="acao" name="acao" value="rejeitado"/>
                <input type="hidden" id="origem" name="origem" value="rejeitado"/>
                <input type="hidden" id="respostaSucesso" name="respostaSucesso" value=""/>
                <input type="hidden" id="respostaErro" name="respostaErro" value=""/>

                <div class="campo medio">
                    <label for="lbl_forma_cobranca">Forma de Cobrança</label>
                    <select id="ddl_forma_cobranca_rejeitado" name="ddl_forma_cobranca_rejeitado">
                        <?php foreach($this->view->comboFormaCobrancaRejeitado as $item) { ?>
                            <option value="<?php echo $item->forcoid; ?>" <?php echo $this->view->parametros->ddl_forma_cobranca_rejeitado == $item->forcoid ? 'selected' : ''; ?>><?php echo $item->forcnome; ?></option>
                        <?php } ?>
                    </select>
                </div>

                <div class="clear"></div>

                <div class="campo medio">
                    <label for="lbl_num_titulo">Número Título</label>
                    <input id="txt_num_titulo_rejeitado" maxlength="15" name="txt_num_titulo_rejeitado" value="<?php echo $this->view->parametros->txt_num_titulo_rejeitado?>" class="campo" type="text">
                </div>

                <div class="clear"></div>

                <div class="campo medio">
                    <label for="lbl_nome_cliente">Nome Cliente</label>
                    <input id="txt_nome_cliente_rejeitado" maxlength="255" name="txt_nome_cliente_rejeitado" value="<?php echo $this->view->parametros->txt_nome_cliente_rejeitado?>" class="campo" type="text">
                </div>

                <div class="clear"></div>

                <div class="campo data periodo">
                    <div class="inicial">
                        <label><?php echo "Periodo envio *";?></label>
                        <input class="campo"  type="text" name="dt_ini_rejeitado" id="dt_ini_rejeitado" maxlength="10" value="<?php echo !isset($this->view->parametros->dt_ini_rejeitado) ? date('d/m/Y') : $this->view->parametros->dt_ini_rejeitado; ?>" />
                    </div>
                    <div class="campo label-periodo">a</div>
                    <div class="final">
                        <label>&nbsp;</label>
                        <input  class="campo"  type="text" name="dt_fim_rejeitado" id="dt_fim_rejeitado" maxlength="10" value= "<?php echo !isset($this->view->parametros->dt_fim_rejeitado) ? date('d/m/Y') : $this->view->parametros->dt_fim_rejeitado; ?>" />
                    </div>
                </div>

                <div class="clear"></div>

            </form>
        </div>

    </div>
    <div class="bloco_acoes">
        <button style="cursor: default;" type="button" id="btn_pesquisar_rejeitado">Pesquisar</button>
        <button style="cursor: default;" type="button" id="btn_gerar_csv_rejeitado">Gerar CSV</button>
    </div>
</div>
<?php 
 //[ORGMKTOTVS-837] - bloqueio CRIS
    if(!INTEGRACAO_TOTVS_ATIVA){
   ?>
<div id="arquivo" <?php echo ($this->view->parametros->acao == 'arquivo' || $this->view->parametros->acao == 'arquivoCron') ?  '' : 'style="display:none;"' ?> >
    <div class="bloco_titulo">Gerar Arquivo Remessa</div>
        <div class="bloco_conteudo">
<?php
    if (isset($dados)) {
        var_dump($dados);
    }
?>
            <div class="formulario">
                <form id="form_arquivo"  method="post" action="">

                    <input type="hidden" id="acao" name="acao" value="arquivo"/>
                    <input type="hidden" id="origem" name="origem" value="arquivo"/>
                    <input type="hidden" id="respostaSucesso" name="respostaSucesso" value=""/>
                    <input type="hidden" id="respostaErro" name="respostaErro" value=""/>
                    
                    <div class="campo medio">
                        <label for="lbl_forma_cobranca">Forma de Cobrança</label>
                        <select id="ddl_forma_cobranca_arquivo" name="ddl_forma_cobranca_arquivo">
                            <?php foreach($this->view->comboFormaCobrancaArquivo as $item) { ?>
                                <option value="<?php echo $item->forcoid; ?>" <?php echo $this->view->parametros->ddl_forma_cobranca_arquivo == $item->forcoid ? 'selected' : ''; ?>><?php echo $item->forcnome; ?></option>
                            <?php } ?>
                        </select>
                    </div>

                    <div class="clear"></div>

                    <div class="campo medio">
                        <label for="lbl_qtde_titulos">Qtde. Títulos (Máx. 99999)*</label>
                        <input id="txt_qtde_titulos" maxlength="5" name="txt_qtde_titulos" value="<?php echo $this->view->parametros->txt_qtde_titulos?>" maxlength="4" class="campo" type="text">
                    </div>

                 <div class="clear"></div>
                </form>
            </div>

        </div>
    <div class="bloco_acoes">
        <button style="cursor: default;" type="button" class="btn_gerar_csv_arquivo">Gerar Arquivo</button>
        <div id="response"></div>
    </div>
    <div id="modal_confirmacao" title="Modal Tipo de Envio">
        <script type="text/javascript" src="lib/layout/1.1.0/bootstrap.js"></script>
        <link type="text/css" rel="stylesheet" href="lib/layout/1.1.0/style.css" />
        <script>
            jQuery('.btn_gerar_csv_arquivo').click(function() { 
                if ($('#txt_qtde_titulos').val().length == 0 || $('#txt_qtde_titulos').val() < 1) {
                    $('#mensagem_alerta').show().html('Existem campos obrigatórios não preenchidos.');
                } else {
                    $('#mensagem_alerta').hide().html('');
                    jQuery("#modal_confirmacao").dialog('open');
                }
            })

            var modal = jQuery("#modal_confirmacao").dialog({
                    autoOpen: false,
                    minHeight: 100,
                    width: 190,
                    modal: true,
                    buttons: {
                        "Ok": function() {
                            }
                        },
                        "Cancelar": function() {
                        }
            }).dialog('open');

            function gerarArquivo() {
                /* sti 86972 */
                var tipoEnvio = jQuery('input[name=tipoEnvio]:checked', '#modal').val();

                if (tipoEnvio === 'manual') {
                    jQuery('#form_arquivo #acao').val('arquivo');
                    jQuery('#form_arquivo #origem').val('arquivo');
                    jQuery('#form_arquivo').submit();
                }

                if (tipoEnvio === 'aft') {
                    jQuery('#form_arquivo #acao').val('arquivoCron');
                    jQuery('#form_arquivo #origem').val('arquivo');
                    jQuery('#form_arquivo').submit();
					
					/*
                    jQuery('#loading').css('display','block');

                    jQuery.ajax({
                        'url': 'CronProcess/crn_enviar_arquivo_aft.php',
                    }).done(function(response) {
                        console.log(response);
                        jQuery('#mensagem_alerta').html(response).show();                        
                        jQuery('#modal_confirmacao').dialog('close');                        
                        jQuery('#loading').css('display','none');
                    });
					*/
                }

            };

        </script>
        <form id="modal">
        <label for="data_vencimento" style="margin-left: 0px;">Como deseja enviar o arquivo?*</label>
            <div>
                <div>
                    <input type="radio" id="tipoEnvio_aft" value="aft" name="tipoEnvio">
                    <label for="tipoEnvio_aft" style="margin-left: 0px; display: inline-block;">AFT</label>
                </div>
                <div>
                    <input type="radio" id="tipoEnvio_manual" value="manual" name="tipoEnvio">
                    <label for="tipoEnvio_manual" style="margin-left: 0px; display: inline-block;">Manual</label>
                </div>
                <button style="cursor: default; margin: 10px auto; display:block" type="button" onclick="gerarArquivo()">Ok</button>
                <span id="loading" style="display: none;"><img style="display: block; margin: 0 auto;" src="images/loading.gif" alt="" /></span>
            </div>
        </form>
    </div>
</div>
<?php
    }else{
        echo '<br/>';
        echo ParametroIntegracaoTotvs::message('A ABA "Gerar Arquivo Remessa"'); 
    }
    //FIm - [ORGMKTOTVS-837] - bloqueio CRIS
     ?>

<?php if($this->view->dados->mostraDownloadRemessa) { ?>
    <div id="resultado_remessa">

        <div class="separador"></div>

        <div class="bloco_titulo resultado">Download</div>
        <div class="bloco_conteudo">

            <div class="conteudo centro">
                <a href="download.php?arquivo=<?php echo $this->view->dados->caminhoDownloadRemessa ?>" target="_blank">
                    <img src="images/icones/t3/caixa2.jpg"><br><?php echo basename($this->view->dados->nomeArquivoRemessa) ?>
                </a>
            </div>
        </div>
    </div>
<?php } ?>

<?php if($this->view->dados->mostraDownloadRejeitado) { ?>
    <div id="resultado_rejeitado">

        <div class="separador"></div>

        <div class="bloco_titulo resultado">Download</div>
        <div class="bloco_conteudo">

            <div class="conteudo centro">
                <a href="download.php?arquivo=<?php echo $this->view->dados->caminhoDownloadRejeitado ?>" target="_blank">
                    <img src="images/icones/t3/caixa2.jpg"><br><?php echo basename($this->view->dados->nomeArquivoRejeitado) ?>
                </a>
            </div>
        </div>
    </div>
<?php } ?>

<?php if($this->view->dados->mostraDownloadArquivo) { ?>
    <div id="resultado_arquivo">

        <div class="separador"></div>

        <div class="bloco_titulo resultado">Download</div>
        <div class="bloco_conteudo">

            <div class="conteudo centro">
                <a href="download.php?arquivo=<?php echo $this->view->dados->caminhoDownloadArquivo ?>" target="_blank">
                    <img src="images/icones/t3/caixa2.jpg"><br><?php echo basename($this->view->dados->nomeArquivo) ?>
                </a>
            </div>
        </div>
    </div>
<?php } ?>

<div id="resultadoPesquisa">
    <?php if($this->view->dados->mostraPesquisaRemessa) {
        require_once _MODULEDIR_ . "Financas/View/fin_cobranca_registrada/resultado_pesquisa_remessa.php";
    } ?>

    <?php if($this->view->dados->mostraPesquisaRejeitado) {
        require_once _MODULEDIR_ . "Financas/View/fin_cobranca_registrada/resultado_pesquisa_rejeitado.php";
    } ?>
</div>

<?php if (count($this->view->dados) > 0) : ?>
    <!--  Caso contenha erros, exibe os campos destacados  -->
    <script type="text/javascript" >jQuery(document).ready(function() {
        showFormErros(<?php echo json_encode($this->view->dados); ?>); 
    });
    </script>
<?php endif; ?>