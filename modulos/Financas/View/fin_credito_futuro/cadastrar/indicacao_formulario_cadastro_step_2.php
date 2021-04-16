<div class="bloco_titulo"><?php echo utf8_encode('Informe o contrato indicado para o crédito'); ?></div>
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
                    <?php echo utf8_encode('Para motivos de crédito do tipo <strong>Indicação de Amigo</strong> é necessário informar o contrato indicado para validação do crédito.') ?>
                </label>
            </div>


            <div class="clear"></div>

            <div id="campo_contrato" class="campo medio">
                <label for="valor_tipo_desconto">Contrato Indicado *</label>
                <input class="campo" type="text" name="cadastro[cfoconnum_indicado]"  id="contrato_indicado" value="" style="text-align: left" />                 
            </div>

            <div class="campo menor"></div>

            <div class="campo maior">
                <label>
                    Nome do Cliente:
                </label>
                <input readonly class="campo" id="" type="text" value="<?php echo utf8_encode(!empty($_SESSION['credito_futuro']['step_1']['razao_social']) ? trim($_SESSION['credito_futuro']['step_1']['razao_social']) : '') ?>" >
            </div>

            <div style="clear: both"></div>
        </form>
    </div>
</div>