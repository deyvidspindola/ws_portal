
<div class="bloco_titulo">Dados da Rescisão</div>
<div class="bloco_conteudo">
    <div class="left">
        <dl class="dados-rescisao">
            <script> 
                if(jQuery('#clioid').val() == '') {
                    jQuery('#clioid').val("<?=$clioid?>");
                }
            </script>
            
            <label>Motivo *</label>
            <select name="" id="" class="rescisao-motivo">
                <option value="0">Escolha</option>
                <? foreach ($listaMotivos as $motivo): ?>
                    <option value="<?= $motivo['mrescoid'] ?>" selected>
                        <?= $motivo['mrescdescricao'] ?>
                    </option>
                <? endforeach ?>
            </select>
            
            <div class="separador"></div>

            <label>E-mail (envio do boleto)</label>
            <input type="text" name="email" id="email" onBlur="revalidar(this,'','email');" value="<?= strtolower($emailCliente)?>"/>

            <div class="separador"></div>

            <label>Total Multa Monitoramento</label>
           
            <input type="text" name="valorMultaMensalidade" id="valorMultaMensalidade"
            readonly="readonly" class="valor-total-faturas"
            value="<?= toMoney($totalRescisao['totalMonitoramento']) ?>" />
			 
            <div class="separador"></div>

            <label>Total Desconto Monitoramento</label>
          
            
             <input type="text" name="valorPagoIndevidoMonitoramentoTotal" id="valorPagoIndevidoMonitoramentoTotal"
            readonly="readonly" class=""
            value="<?= toMoney($totalRescisao['totalProRataMonitoramento']) ?>" />
            
            
            <div class="separador"></div>            

            <label>Valor Faltante Mensalidade (A ser pago pelo cliente) </label>
            <input type="text" name="valorMultaMensalidadeFaltante" id="valorMultaMensalidadeFaltante"
            readonly="readonly" class="mask-money"
            value="<?= toMoney(0) ?>" />
     
            <div class="separador"></div>

            <div class="campo medio data">
                <label>Vencimento *</label>
                <input type="text" style="margin: 3px 0 0 0 !important"
                class="rescisao-vencimento campo data datepicker"
                readonly="readonly"
                maxlength="10"/>
            </div>

        </dl>
    </div>

    <div class="right">
        <dl class="dados-rescisao">
            
            <label>Taxa de cancelamento</label>
            <input type="text" name="" id=""   readonly="readonly" 
            class="valor-total-taxas mask-money"
            value="<?= toMoney(0) ?>" />
            
            <div class="separador"></div>

            <label>Total Multa por não devolução</label>
            <input type="text" name="" id=""   readonly="readonly" 
            class="valor-total-multa-nao-devolucao mask-money"
            value="<?= toMoney(0) ?>" />
            
            <div class="separador"></div>

            <label>Status *</label>
            <select name="" id="" class="rescisao-status">
                <option value="0">Escolha</option>
                <? foreach ($listaStatusRescisao as $valor => $desc): ?>
                    <option value="<?= $valor ?>" selected
                        <?= ($this->populate('status') == $valor) ? 'selected="selected"' : '' ?>>
                            <?= $desc ?>
                    </option>
                <? endforeach ?>
            </select>
            
            <div class="separador"></div>

            <label>Total Multa Locação</label>
            
                      <input type="text" name="totalMensalidadeEquipamento" id="totalMensalidadeEquipamento"
            readonly="readonly" class="valor-total-multa"
            value="<?= toMoney($totalRescisao['totalLocacaoAcessorios']) ?>" />
            
            
            <div class="separador"></div>
             <label>Total Desconto Locação </label>    
		  
                        <input type="text" name="totalMensalidadeIndevido" id="totalMensalidadeIndevido"
            readonly="readonly" class=""
            value="<?= toMoney($totalRescisao['totalProRataLocacaoeAcessorios']) ?>" />
            
            <div class="separador"></div>

            <label>Valor Faltante Locação (A ser pago pelo cliente) </label>
            <input type="text" name="totalDiferencaIndevido" id="totalDiferencaIndevido"
            readonly="readonly" class=""
            value="<?= toMoney(0) ?>" />
            
            <div class="separador"></div>           

        </dl>
    </div>

    <div class="clear"></div>
</div>


<div class="bloco_acoes">
    <p>
        <strong>Total rescisão R$</strong>
        <input type="text" name="totalRescisao"
            readonly="readonly" class="total-rescisao"
            value="<?= toMoney(0) ?>" 
            id="totalRescisao" />
    </p>
</div>

<div class="bloco_acoes" id="btnFinalizarRescisao">
 <?php 

if ($_SESSION['funcao']['finaliza_rescisao'] == 1){ ?>
    <!--<p style="color: red" ><strong>Botão "Finalizar" desabilitado, enquanto o módulo não está finalizado.</strong></p>-->
    <a class="botao rescisao-finalizar">Finalizar</a>
   <?php }?>
    
</div>
