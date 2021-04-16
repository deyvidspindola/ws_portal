 <?php require_once '_header.php'; 
$control = new FinControleFiscalRemessas();
$view =  $control->setRetornaView();


?> 
<head>
    <script language="Javascript" type="text/javascript" src="modulos/web/js/fin_controle_fiscal_remessas.js"></script> 
    <script type="text/javascript" src="modulos/web/js/lib/jquery.maskedinput-1.3.min.js"></script> 
</head>

<form name="frm_pesquisar" id="frm_pesquisar" method="POST" action="">
    <input type="hidden" name="acao" id="acao" value="pesquisa" />


        <?php require_once '_msgPrincipal.php'; ?>

        <ul class="bloco_opcoes">
            <li class="ativo"><a href="fin_controle_fiscal_remessas.php?acao=pesquisa">Remessas</a></li>
           
            <li><a href="fin_controle_fiscal_remessas.php?acao=equipamentoMovel">Equipamento Móvel</a></li>
        </ul>
        <div class="bloco_titulo">Remessa/Recebimento de Estoque</div>
        <div class="bloco_conteudo">

             <div class="formulario">
              
                <div class="clear"></div>


                <div class="campo data">
                    <label>Período(<font color="#FF0000">*</font>)</label>
                    <input class="campo"  type="text" name="dt_ini" id="dt_ini" maxlength="10" value="<?php echo $dt_ini; ?>" />
                </div>
                <div style="margin-top: 23px !important;" class="campo label-periodo">à</div>
                <div class="campo data">
                    <label>&nbsp;</label>
                    <input  class="campo"  type="text" name="dt_fim" id="dt_fim" maxlength="10" value= "<?php echo $dt_fim; ?>" />
                </div>


                <div class="campo menor">
                    <label for="nRemessa">Nº Remessa</label>
                    <input type="text" id="nRemessa" name="nRemessa" value="" class="campo"  maxlength="9" />
                </div>

                    <div class="campo menor">
                    <label for="nfRemessa">NF Remessa</label>
                    <input type="text" id="nfRemessa" name="nfRemessa" value="" class="campo"  maxlength="9" />
                </div>

                    <div class="campo menor">
                    <label for="tipoRelatorio">Tipo Relatório(<font color="#FF0000">*</font>)</label>
                    <select id="tipoRelatorio" name="tipoRelatorio">
                            <option value="NF">Remessa</option>
                            <option value="P">Produto</option>
                            <option value="S">Serial</option>
                    </select>
                </div>
                <div class="clear"></div>

                <div class="campo medio">
                    <label for="tipoMovimentacao">Tipo Movimentação(<font color="#FF0000">*</font>)</label>
                 <select id="tipoMovimentacao" name="tipoMovimentacao" style="width: 100%;">
                                                    <option value="">- Selecione -</option>
                                                    <?php foreach ($view->tiposMovimentacao as $tipo): ?>
                                                    <option value="<?php echo $tipo->key; ?>"><?php echo $tipo->value; ?></option>
                                                    <?php endforeach; ?>
                  </select>
                </div>

                      <div class="campo menor">
                 <label for="statusRemessa">Status</label>
                    <select id="statusRemessa" name="statusRemessa">
                                                    <option value="">- Selecione -</option>
                                                    <?php foreach ($view->estoqueremessaSatus as $tipo): ?>
                                                    <option value="<?php echo $tipo->key; ?>"><?php echo $tipo->value; ?></option>
                                                    <?php endforeach; ?>
                  </select>
                </div>

                <div class="campo menor">
                    <label for="nSerie">Nº de Série</label>
                    <input type="text" id="nSerie" name="nSerie" value="" class="campo"  maxlength="20" />
                </div>
                <div class="campo menor">
                    <label for="numero_pedido">Nº Pedido</label>
                    <input type="text" id="numero_pedido" name="numero_pedido" value="" class="campo"  maxlength="12" />
                </div>

                <div class="clear"></div>
               <fieldset>
                       <legend>Remetente:</legend>
                              <div class="campo maior">
                 <label for="repreRespRem">Representante Responsável</label>
                    <select id="repreRespRem" name="repreRespRem">
                                                    <option value="">- Selecione -</option>
                                                    <?php foreach ($view->retornaRepresentante as $tipo): ?>
                                                    <option value="<?php echo $tipo->key; ?>"><?php echo $tipo->value; ?></option>
                                                    <?php endforeach; ?>
                  </select>
                </div>           
                                        
                </fieldset>

                           
               <fieldset>
                    <legend>Destinatário:</legend>
                    <div class="campo maior">
                    <label for="repreRespDest">Representante Responsável</label>
                        <select id="repreRespDest" name="repreRespDest">
                            <option value="">- Selecione -</option>
                            <?php foreach ($view->retornaRepresentante as $tipo): ?>
                            <option value="<?php echo $tipo->key; ?>"><?php echo $tipo->value; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div> 
                    
                    <div class="campo maior">
                    <label for="repreRespDest">Fornecedor Responsável</label>
                        <select id="repreFornDest" name="repreFornDest">
                            <option value="">- Selecione -</option>
                            <?php foreach ($view->retornaFornecedor as $tipo): ?>
                            <option value="<?php echo $tipo->key; ?>"><?php echo $tipo->value; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div> 
                                        
                </fieldset>
                    

            </div>
              <div class="clear"></div>
            <div class="conteudo">
               
                <div class="separador"></div>
            </div>
        </div>
        <div class="bloco_acoes">
            <button type="button" id="pesquisar">Pesquisar</button>
        </div>
        <div class="separador"></div>

        <div class="separador"></div>
        <div id="frame01"></div>
    <div class="separador"></div>       
        <div id="frame04"></div>
        <div class="separador"></div>
        <div id="process" title="Mensagem"></div>

    </div>

</form>