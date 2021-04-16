<div class="bloco_titulo">Informe de valores do crédito concedido</div>
<div class="bloco_conteudo" style="height: 530px">
    <div class="formulario">
        <form id="form_cadastrar"  method="post" action="">
            <input type="hidden" id="acao" name="acao" value="cadastrar"/>
            <input type="hidden" id="step" name="step" value = "step_3" />
            <input type="hidden" id="voltar" name="voltar" value = "0" />
            <input type="hidden" id="post_realizado_step_3" name="postStep3" value = "1" />

            <div class="campo maior">
                <label style="color: gray">
                    Informe de <strong>valores/parcelas</strong> do crédito concedido para o cliente.
                </label>
            </div>

            <div class="clear"></div>
            <?php
            switch ($_SESSION['credito_futuro']['step_2']['tipo_motivo']) {
                case '1':
                    require_once _MODULEDIR_ . "Financas/View/fin_credito_futuro/cadastrar/contestacao_formulario_cadastro_step_3.php";
                    break;

                case '2':
                    require_once _MODULEDIR_ . "Financas/View/fin_credito_futuro/cadastrar/indicacao_formulario_cadastro_step_3.php";
                    break;

                case '3':
                    require_once _MODULEDIR_ . "Financas/View/fin_credito_futuro/cadastrar/insencao_monitoramento_formulario_cadastro_step_3.php";
                    break;

                default:
                    require_once _MODULEDIR_ . "Financas/View/fin_credito_futuro/cadastrar/default_formulario_cadastro_step_3.php";
                    break;
            }
            ?>
            <div style="clear: both"></div>
        </form>
    </div>
</div>
