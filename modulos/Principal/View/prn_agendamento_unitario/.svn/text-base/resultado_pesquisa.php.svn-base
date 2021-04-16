<div class="separador"></div>
<div class="resultado bloco_titulo">Resultado da Pesquisa</div>
<div id="bloco_itens" class="bloco_conteudo">
    <?php echo $this->view->ordenacao; ?>
    <div class="listagem">
        <table>
            <thead>
                <tr>
                    <th class="medio">Tipo de Serviço</th>
                    <th class="medio">Ordem de Serviço</th>
                    <th class="medio">Contrato</th>
                    <th class="medio">Placa</th>
                    <th class="medio">Chassi</th>
                    <th class="medio">Data/Hora<br />Agendamento</th>
                    <th class="medio">Data O.S.</th>
                    <th class="medio">Nome Cliente</th>
                    <th class="acao">Ação</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($this->view->dados as $chave => $dados): ?>
                <tr id="result_<?php echo $dados->ordoid; ?>" class="<?php if ($chave % 2): ?>par<?php else: ?>impar<?php endif; ?>">
                    <td class="centro"><?php echo $dados->ostdescricao; ?></td>
                    <td class="centro"><a href="<?php echo _PROTOCOLO_ . _SITEURL_; ?>prn_ordem_servico.php?ESTADO=cadastrando&acao=editar&ordoid=<?php echo $dados->ordoid; ?>" target="_blank"><?php echo $dados->ordoid; ?></a></td>
                    <td class="centro"><a href="<?php echo _PROTOCOLO_ . _SITEURL_; ?>contrato_servicos.php?connumero=<?php echo $dados->ordconnumero; ?>&acao=consultar" target="_blank"><?php echo $dados->ordconnumero; ?></a></td>
                    <td class="centro"><?php echo $dados->veiplaca; ?></td>
                    <td class="centro"><?php echo $dados->veichassi; ?></td>
                    <td class="centro"><?php echo $dados->osadata; ?> <?php echo $dados->osahora; ?>-<?php echo $dados->osahora_final; ?></td>
                    <td class="centro"><?php echo $dados->orddt_ordem; ?></td>
                    <td class="centro"><?php echo $dados->clinome; ?></td>
                    <td class="centro">
                        <?php if (empty($dados->osadata)): ?>
                        <span>
                            <a href="<?php echo _PROTOCOLO_ . _SITEURL_; ?>prn_agendamento_unitario.php?acao=detalhe&operacao=agendar&id=<?php echo $dados->ordoid; ?>">
                                <img class="icone" src="<?php echo _PROTOCOLO_ . _SITEURL_; ?>images/calendar_cal.gif" title="Agendar">
                            </a>
                        </span>
                        <?php else: ?>
                        <span>
                             <?php if( $dados->osaasaoid == 2 || $dados->osaasaoid == 7): ?>
                                <a href="<?php echo _PROTOCOLO_ . _SITEURL_; ?>prn_agendamento_unitario.php?acao=detalhe&operacao=reagendar&id=<?php echo $dados->ordoid; ?>">
                                    <img class="icone" src="<?php echo _PROTOCOLO_ . _SITEURL_; ?>images/edit.png" title="Reagendar">
                                </a>
                            <?php endif; ?>
                        </span>
                        <span>
                            <?php if( $dados->osaasaoid == 2 || $dados->osaasaoid == 7): ?>
                            <a href="javascript:void(0);">
                                <img class="icone cancelar" data="{ordoid: <?php echo $dados->ordoid; ?>, osaoid: <?php echo $dados->osaoid; ?>  }" src="<?php echo _PROTOCOLO_ . _SITEURL_; ?>images/icon_error.png" title="Cancelar">
                            </a>
                            <?php endif; ?>
                        </span>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
            <tfoot>
                <tr>

                  <?php if($this->view->totalResultados == 1){
                     $msg_reg = ' registro encontrado.';
                  }else{
                  	 $msg_reg = ' registros encontrados.';
                  }?>

                    <td id="registros_encontrados" colspan="9"><?php echo $this->view->totalResultados.$msg_reg?></td>
                </tr>
            </tfoot>
        </table>
    </div>
    <?php echo $this->view->paginacao; ?>
</div>

<div id="cancelar-agendamento" title="Cancelar Agendamento">

    <div id='info'>

        <div id='alerta-modal' class="mensagem-modal mensagem alerta invisivel">Verifique os campos em destaque e preencha corretamente.</div>

        <div class="mensagem-modal mensagem info">(*) Campos de preenchimento obrigatório.</div>

        <div id="mensagem-relacionamento" class="invisivel"></div>

	    <form id="form-cancelar-agendamento">
	        <div>
	            <div class="campo maior">
	                <label id="lbl_motivo" for="cmp_cliente">Motivo do Cancelamento *</label>
	                <select id="cmp_motivo" name="cmp_cidade">
	                    <option value="0">Escolha</option>
                        <?php foreach ($this->view->motivosCancelamentoAgendamento as $motivos) {?>
                            <option value="<?echo $motivos->omnoid;?>" ><?echo $motivos->omndescricao;?></option>
                        <?php } ?>
	                </select>
	            </div>

	            <div class="clear"></div>

	            <div class="campo maior">
	                <label id="lbl_observacoes" for="cmp_cpf_cnpj">Observações *</label>
	                <textarea rows="5" id="cmp_observacao" name="cmp_observacao"></textarea>
	            </div>

	            <div class="clear"></div>
	        </div>

        <!-- Permite o envio do formulário sem duplicar a caixa de dialogo -->
        <input type="submit" tabindex="-1" style="position:absolute; top:-1000px">

      </div>

      <span id='loading' class="invisivel" style="float: right"><img src="images/loading.gif" alt="Carregando...." /></span>

    </form>
</div>

<div id="cancelar-agendamento-confirma" title="Confirmação">

    <div id='conteudo'>

        <p id="mensagem-confirma">
            Esta O.S. de <span id="tipo-os-1"></span> possui relacionamento com uma O.S. de <span id="tipo-os-2"></span> Nr. <strong id="num-os-modal"></strong> agendada. <br>
            Deverão ser cancelados os dois agendamentos. <br>
            Concorda?
        </p>

    </div>
</div>
