<div class="bloco_titulo"><?php echo utf8_encode('Informe o número do protocolo da contestação') ?></div>
<div class="bloco_conteudo">
    <div class="formulario">
        <form id="form_cadastrar"  method="post" action="">
            <input type="hidden" id="acao" name="acao" value="cadastrar"/>
            <input type="hidden" id="step" name="step" value = "step_2" />
            <input type="hidden" id="motivo_credito_id" name="cadastro[cfocfmcoid]" value = "" />
            <input type="hidden" id="tipo_motivo" name="cadastro[tipo_motivo]" value = "" />
            <input type="hidden" id="motivo_descricao" name="cadastro[motivo_descricao]" value = "" />
            <input type="hidden" id="voltar" name="voltar" value = "0" />
            
            <div class="campo maior">
                <label style="color: gray">
                    <?php echo utf8_encode('Para motivos de crédito do tipo <strong>Contestação</strong> é necessário informar o número do protocolo da contestação para validação.') ?>
                </label>
            </div>
            <div class="clear"></div>
            <div class="campo medio ">
                <label for="valor_tipo_desconto">Protocolo *</label>
                <input class="campo" type="text" name="cadastro[cfoancoid]"  id="protocolo" value=""/> 
            </div>

            <div class="campo menor" ></div>

            <div class="campo maior">
                <label>
                    Nome do Cliente:
                </label>
                <input readonly class="campo" id="" type="text" value="<?php echo utf8_encode(!empty($_SESSION['credito_futuro']['step_1']['razao_social']) ? trim($_SESSION['credito_futuro']['step_1']['razao_social']) : '') ?>" >
            </div>

            <div class="clear"></div>

            <div class="campo medio ">
                <label for="valor_tipo_desconto">Valor Total NF(s) Contestada(s) </label>
                <input class="campo desabilitado" type="text"  id="valor_tipo_desconto" value="0,00" name="cadastro[valor]"/>
            </div>

            <div style="clear: both"></div>
        </form>
    </div>
</div>