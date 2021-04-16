<script type="text/javascript">
jQuery(document).ready(function() {
    jQuery("#valor_negociado").maskMoney({thousands:'', decimal:',', allowZero:true, allowNegative:false, defaultZero:true});

    $('#manter_valor').click(function() {
        if ($(this).is(':checked')) {
            $('#valor_negociado').prop( 'disabled', true );
            $('#valor_negociado').addClass('desabilitado');
            $('#valor_negociado').val('0,00');
        }else{
            $('#valor_negociado').prop( 'disabled', false );
            $('#valor_negociado').removeClass('desabilitado');
        }

    });
});
</script>

<table>
<tr class="menor">
    <td><label for="cpvoid">Parcelamento:</label></td>
    <td>
        <select id="cpvoid" name="cpvoid" disabled="disabled" class="desabilitado">
            <option value="">999 x</option>
        </select>
    </td>
</tr>
<tr  class="menor">
    <td><label for="situacao">Situação:</label></td>
    <td>
        
        <table>
            <tr>
                <td>
                    <select id="situacao" name="situacao" disabled="disabled" class="desabilitado">
                        <option value="">Locação</option>
                    </select>
                </td>
                <td style="padding-left:10px"><input id="parcelas_acessorios" name="parcelas_acessorios" type="checkbox" disabled="disabled"></td>
                <td>
                     <label for="parcelas_acessorios"> Reativar parcelas de acessórios (se houver)</label>
                </td>
            </tr>
        </table>
       
    </td>

</tr>

<tr>
    <td><label>Valor Negociado R$:</label></td>
    <td>
        
        <table>
            <tr>
                <td><input id="valor_negociado" class="numeric" name="valor_negociado" value="0,00" class="campo" type="text" disabled="disabled" maxlength="8"  style="width:120px !important"></td>
                <td style="padding-left:10px"><input id="manter_valor" name="manter_valor" value="0" type="checkbox" disabled="disabled"></td>
                <td>
                     <label for="manter_valor"> Manter Valor</label>
                </td>
            </tr>
        </table>
       
    </td>
</tr>

<tr>
    <td valign="top"><label for="justificativa" disabled="disabled">Justificativa:</label></td>
    <td><textarea id="justificativa" name="justificativa" cols="60" maxlength="50" disabled="disabled"></textarea></td>
</tr>

</table>
