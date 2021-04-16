<?php include "head.php"; ?>

<?php echo isset($this->parametroCadastro->registro->coluna) && trim($this->parametroCadastro->registro->coluna) != '' ? trim($this->parametroCadastro->registro->coluna) : '' ?>

<div class="mensagem info">Campos com * são obrigatórios.</div>
<div id="msg_responsavel" class="mensagem invisivel"></div>

<?php echo $this->exibirMensagens('mensagens'); ?>

<script>
    var existeParametroEmail = <?php echo $this->existeParametroEmail ? 1 : 0; ?>;
<?php if (isset($this->parametroCadastro->registro->cfcpoid) && trim($this->parametroCadastro->registro->cfcpoid) != '') : ?>
        var campanha_id = <?php echo isset($this->parametroCadastro->registro->cfcpoid) && trim($this->parametroCadastro->registro->cfcpoid) != '' ? trim($this->parametroCadastro->registro->cfcpoid) : '' ?>;
<?php endif; ?>
</script>

<div class="bloco_titulo">Dados Principais</div>
<div class="bloco_conteudo">
    <div class="formulario">
        <form method="POST" name="form" id="form" action="fin_credito_futuro_parametrizacao_campanha.php">
            <input id="acao" type="hidden" value="salvar" name="acao">
            <input id="cfcpoid" type="hidden" value="<?php echo isset($this->parametroCadastro->registro->cfcpoid) && trim($this->parametroCadastro->registro->cfcpoid) != '' ? trim($this->parametroCadastro->registro->cfcpoid) : '' ?>" name="cfcpoid">

            <div class="campo data">
                <label for="cfcpdt_inicio_vigencia">Período de Vigência *</label>
                <input id="cfcpdt_inicio_vigencia" tabindex="1" type="text" name="cfcpdt_inicio_vigencia" maxlength="10" value="<?php echo isset($this->parametroCadastro->registro->cfcpdt_inicio_vigencia) && trim($this->parametroCadastro->registro->cfcpdt_inicio_vigencia) != '' ? trim($this->parametroCadastro->registro->cfcpdt_inicio_vigencia) : '' ?>" class="campo" />
            </div>

            <p class="campo label-periodo">a</p>

            <div class="campo data">
                <label for="cfcpdt_fim_vigencia">&nbsp;</label>
                <input id="cfcpdt_fim_vigencia" type="text" tabindex="2" name="cfcpdt_fim_vigencia" maxlength="10" value="<?php echo isset($this->parametroCadastro->registro->cfcpdt_fim_vigencia) && trim($this->parametroCadastro->registro->cfcpdt_fim_vigencia) != '' ? trim($this->parametroCadastro->registro->cfcpdt_fim_vigencia) : '' ?>" class="campo" />
            </div>

            <div class="clear"></div>

            <div class="campo medio">
                <label for="cfcpcftpoid">Tipo de Campanha Promocional *</label>
                <select id="cfcpcftpoid" name="cfcpcftpoid" tabindex="3">
                    <option value="">Selecione</option>
                    <?php foreach ($this->listarTipoCampanha as $item) : ?>
                        <?php $selected = isset($this->parametroCadastro->registro->cfcpcftpoid) && trim($this->parametroCadastro->registro->cfcpcftpoid) != '' && $this->parametroCadastro->registro->cfcpcftpoid == $item->cftpoid ? 'selected="selected"' : '' ?>
                        <option <?php echo $selected; ?> value="<?php echo $item->cftpoid; ?>"><?php echo $item->cftpdescricao; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="clear"></div>

            <div class="campo medio">
                <label for="cfcpcfmccoid">Motivo do Crédito *</label>
                <select id="cfcpcfmccoid" name="cfcpcfmccoid" tabindex="4">
                    <option value="">Selecione</option>
                    <?php foreach ($this->listarMotivo as $item) : ?>
                        <?php $selected = isset($this->parametroCadastro->registro->cfcpcfmccoid) && trim($this->parametroCadastro->registro->cfcpcfmccoid) != '' && $this->parametroCadastro->registro->cfcpcfmccoid == $item->cfmcoid ? 'selected="selected"' : '' ?>
                        <option <?php echo $selected; ?> value="<?php echo $item->cfmcoid; ?>"><?php echo $item->cfmcdescricao; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="clear"></div>

            <fieldset class="medio">
                <legend>Tipo do Desconto *</legend>

                <?php
                if (isset($this->parametroCadastro->registro->cfcptipo_desconto) && $this->parametroCadastro->registro->cfcptipo_desconto != '' && $this->parametroCadastro->registro->cfcptipo_desconto == 'P') {
                    $percentual_check = 'checked="checked"';
                    $valor_check = '';

                    $percentualDesconto = isset($this->parametroCadastro->registro->cfcpdesconto) && !empty($this->parametroCadastro->registro->cfcpdesconto) ? number_format($this->parametroCadastro->registro->cfcpdesconto, '2', ',', '.') : '';
                    $valorDesconto = '';
                } else if (isset($this->parametroCadastro->registro->cfcptipo_desconto) && $this->parametroCadastro->registro->cfcptipo_desconto != '' && $this->parametroCadastro->registro->cfcptipo_desconto == 'V') {
                    $percentual_check = '';
                    $valor_check = 'checked="checked"';

                    $percentualDesconto = '';
                    $valorDesconto = isset($this->parametroCadastro->registro->cfcpdesconto) && !empty($this->parametroCadastro->registro->cfcpdesconto) ? number_format($this->parametroCadastro->registro->cfcpdesconto, '2', ',', '.') : '';
                }
                ?>

                <input id="cfcptipo_desconto_1" class="tipo_desconto_cadastro" type="radio" <?php echo isset($percentual_check) && !empty($percentual_check) ? $percentual_check : !isset($valor_check) ? 'checked="checked"' : ''  ?>  tabindex="5" value="P" name="cfcptipo_desconto">
                <label for="cfcptipo_desconto_1">Percentual</label>
                <input id="cfcptipo_desconto_2" class="tipo_desconto_cadastro" type="radio" <?php echo isset($valor_check) && !empty($valor_check) ? $valor_check : '' ?> value="V" tabindex="6" name="cfcptipo_desconto">
                <label for="cfcptipo_desconto_2">Valor</label>
            </fieldset>

            <div id="cfcpdescont_percentual" class="campo menor tipo_desconto_box none">
                <label for="cfcpdesconto_percentual">Desconto *</label>
                <input id="cfcpdesconto_percentual" name="cfcpdesconto_percentual" maxlength="6" class="campo desconto_percentual" type="text" value="<?php echo isset($percentualDesconto) && !empty($percentualDesconto) ? $percentualDesconto : ''; ?>">
                <div class="percento-label">%</div>
            </div>


            <div id="cfcpdescont_valor" class="campo menor tipo_desconto_box none">
                <label for="cfcpdesconto_valor">Desconto *</label>
                <input id="cfcpdesconto_valor" maxlength='9' class="campo desconto_valor " name="cfcpdesconto_valor" type="text" value="<?php echo isset($valorDesconto) && !empty($valorDesconto) ? $valorDesconto : ''; ?>">
            </div>

            <div class="clear"></div>

            <fieldset class="medio">
                <legend>Forma de Aplicação *</legend>
<?php
if (isset($this->parametroCadastro->registro->cfcpaplicacao) && $this->parametroCadastro->registro->cfcpaplicacao != '' && $this->parametroCadastro->registro->cfcpaplicacao == 'I') {
    $integral_check = 'checked="checked"';
    $parcela_check = '';
} else if (isset($this->parametroCadastro->registro->cfcpaplicacao) && $this->parametroCadastro->registro->cfcpaplicacao != '' && $this->parametroCadastro->registro->cfcpaplicacao == 'P') {
    $integral_check = '';
    $parcela_check = 'checked="checked"';
}
?>
                <input id="cfcpaplicacao_1" type="radio" class="forma_aplicacao" <?php echo isset($integral_check) && !empty($integral_check) ? $integral_check : '' ?> value="I" tabindex="8" name="cfcpaplicacao">
                <label for="cfcpaplicacao_1">Integral</label>
                <input id="cfcpaplicacao_2" type="radio" class="forma_aplicacao" <?php echo isset($parcela_check) && !empty($parcela_check) ? $parcela_check : !isset($integral_check) ? 'checked="checked"' : ''  ?> value="P" tabindex="9" name="cfcpaplicacao">
                <label for="cfcpaplicacao_2">Parcela</label>
            </fieldset>

            <div id="div_cfcpqtde_parcelas" class="campo menor none">
                <label for="cfcpqtde_parcelas">Qtde. Parcelas *</label>
                <input id="cfcpqtde_parcelas" class="campo" name="cfcpqtde_parcelas"  type="text" value="<?php echo isset($this->parametroCadastro->registro->cfcpqtde_parcelas) && trim($this->parametroCadastro->registro->cfcpqtde_parcelas) != '' ? trim($this->parametroCadastro->registro->cfcpqtde_parcelas) : '' ?>">
            </div>

            <div class="clear"></div>

            <!--verificar a qual coluna pertence esse campo-->

            <fieldset class="medio">
                <legend>Aplicar o desconto sobre o valor<br/> total de *</legend>
<?php
if (isset($this->parametroCadastro->registro->cfcpaplicar_sobre) && $this->parametroCadastro->registro->cfcpaplicar_sobre != '' && $this->parametroCadastro->registro->cfcpaplicar_sobre == 'M') {
    $monitoramento_check = 'checked="checked"';
    $locacao_check = '';
} else if (isset($this->parametroCadastro->registro->cfcpaplicar_sobre) && $this->parametroCadastro->registro->cfcpaplicar_sobre != '' && $this->parametroCadastro->registro->cfcpaplicar_sobre == 'L') {
    $monitoramento_check = '';
    $locacao_check = 'checked="checked"';
}
?>
                <input id="cfcpaplicar_sobre_1" type="radio" class="desconto_sobre" <?php echo isset($monitoramento_check) && !empty($monitoramento_check) ? $monitoramento_check : !isset($locacao_check) ? 'checked="checked"' : ''  ?>  value="M" tabindex="11" name="cfcpaplicar_sobre">
                <label for="cfcpaplicar_sobre_1">Monitoramento</label>
                <input id="cfcpaplicar_sobre_2" type="radio" class="desconto_sobre" <?php echo isset($locacao_check) && !empty($locacao_check) ? $locacao_check : '' ?> value="L" tabindex="12" name="cfcpaplicar_sobre">
                <label for="cfcpaplicar_sobre_2">Locação</label>
            </fieldset>

            <div class="clear"></div>

            <div class="campo medio">
                <label for="cfcpobroid">Obrigação Financeira de Desconto *</label>
                <select id="cfcpobroid" name="cfcpobroid" tabindex="13">
                    <option value="">Selecione</option>
<?php foreach ($this->listaObrigacaoFinanceira as $item) : ?>
                        <?php if (isset($this->parametroCadastro->registro->cfcpobroid) && !empty($this->parametroCadastro->registro->cfcpobroid)) : ?>
                            <?php $selected = $this->parametroCadastro->registro->cfcpobroid == $item->obroid ? 'selected="selected"' : ''; ?>
                        <?php else: ?>
                            <?php $selected = (isset($this->parametroEmailParametrizacao->cfeaobroid_campanha) && $this->parametroEmailParametrizacao->cfeaobroid_campanha != '' && $this->parametroEmailParametrizacao->cfeaobroid_campanha == $item->obroid) ? 'selected="selected"' : '' ?>
                        <?php endif; ?>
                        <option <?php echo $selected ?> value="<?php echo $item->obroid; ?>"><?php echo $item->obroid . ' - ' . $item->obrobrigacao; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="clear"></div>

            <div class="campo medio">
                <label for="cfcpobservacao">Observação</label>
                <textarea tabindex="14" style="resize:none" id="cfcpobservacao"  name="cfcpobservacao" rows="5"><?php echo isset($this->parametroCadastro->registro->cfcpobservacao) && !empty($this->parametroCadastro->registro->cfcpobservacao) ? $this->parametroCadastro->registro->cfcpobservacao : ''; ?></textarea>
            </div>

            <div class="clear"></div>


        </form>
    </div>
</div>

<div class="bloco_acoes">
    <button tabindex="15" type="submit" id="confirmar">Confirmar</button>
<?php if (isset($this->parametroCadastro->registro->cfcpoid) && trim($this->parametroCadastro->registro->cfcpoid) != '') : ?>
        <button tabindex="17" type="submit" id="excluir">Excluir</button>
    <?php endif; ?>
    <button tabindex="16" type="button" id="retornar">Retornar</button>        
</div>

<!--Se for edição de cadastro realiza o include abaixo-->    
<?php include "historico_edicao.php" ?>    

<?php include "footer.php"; ?> 

<style>
    .periodo img{
        margin-right: 18px !important;
    }
    .data, .mes_ano{
        width: 125px !important;
    }

    fieldset .menor, fieldset .menor input {
        width: 88px !important;
    }
    .none{
        display: none;
    }
    .percento-label{
        font-size: 14px;
        position: absolute;
        right: -7px;
        top: 16px;
        border: 0px !important;
        background: none !important;
        color: #000 !important;
        right: -16px\9; /* IE8+9  */

    }
    div.campo{
        position: relative;
    }

    #cfcpdt_inicio_vigencia{
        width: 88px\9 !important; /* IE8+9  */
    }

    #cfcpdt_fim_vigencia{
        width: 88px\9 !important; /* IE8+9  */
    }

    .periodo img{
        margin-right: 7px\9 !important; /* IE8+9  */
    }

</style>

<script>
    jQuery(document).ready(function() {

<?php if (isset($this->parametroCadastro->registro->cfcpoid) && trim($this->parametroCadastro->registro->cfcpoid) != '') : ?>
            jQuery('#excluir').click(function() {
                var exclui = confirm("Deseja realmente excluir o registro?");
                if (exclui) {
                    var id = jQuery('#cfcpoid').val();

                    var campanha_id = jQuery('#cfcpcftpoid').val();

                    window.location = '?acao=excluir&id=' + id + '&campanha_id=' + campanha_id;
                }
            });
<?php endif; ?>

    });
</script>    