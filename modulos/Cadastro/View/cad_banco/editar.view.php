<?php
/**
 * @author  Emanuel Pires Ferreira
 * @email   epferreira@brq.com
 * @since   24/01/2013
 */
?>
<style type="text/css">
    .input_error {
        background: none repeat scroll 0 0 #FFFFC0;
    }
</style>
<tr>
    <td align="center">
        <form name="editarBanco" id="editarBanco" method="post" action="">
            <input type="hidden" name="status" id="status" value="editar" />
            <table class="tableMoldura dados_pesquisa">
                <tr class="tableSubTitulo">
                    <td colspan="4"><h2>Dados Principais</h2></td>
                </tr>
                <tr>
                    <td><label for="cfbbanco">Código: *</label></td>
                    <td>
                        <input name="cfbbanco" id="cfbbanco" style="width: 75px;" maxlength="6" readonly="true" value="<?=$view['cfbbanco']?>"/>
                    </td>
                </tr>
                <tr>
                    <td><label for="cfbtecoid">Empresa: *</label></td>
                    <td>
                        <?php
                            foreach($arrEmpresas as $empresa) {
                                if ($empresa['tecoid'] == $view['cfbtecoid'])
                                    print '<input style="width: 350px;" disabled="true" value="'.$empresa['tecrazao'].'"/>';
                            }
                        ?>
                    </td>
                </tr>
                <tr>
                    <td><label for="cfbnome">Descrição: *</label></td>
                    <td>
                        <input name="cfbnome" id="cfbnome" style="width: 350px;" value="<?=$view['cfbnome']?>" />
                    </td>
                </tr>
                <tr>
                    <td><label for="cfbconvenio">Convênio:</label></td>
                    <td>
                        <input name="cfbconvenio" id="cfbconvenio" style="width: 350px;" value="<?=$view['cfbconvenio']?>"/>
                    </td>
                </tr>
                <tr>
                    <td><label for="cfblimite">Limite:</label></td>
                    <td>
                        <input name="cfblimite" id="cfblimite" style="width: 350px;" value="<?=number_format($view['cfblimite'],0,"","")?>" />
                    </td>
                </tr>
                <tr>
                    <td><label for="cfbplcoid">Conta Contábil: *</label></td>
                    <td>
                        <select name="cfbplcoid" id="cfbplcoid">
                            <option value="">Selecione</option>
                            <?php foreach($arrPlanoContabil['planos'] as $plano) {
                                if($plano['plctecoid'] == $view['cfbtecoid']) {
                                    $sel = ($plano['plcoid'] == $view['cfbplcoid'])?"selected='selected'":"";
                                    print '<option value="'.$plano['plcoid'].'" '.$sel.'>'.$plano['plcdescricao'].'</option>';
                                }
                            } ?>
                        </select>
                        <span id="loadConta" style="display:none;"><img src="images/progress.gif" alt="" /></span>
                    </td>
                </tr>
                <tr>
                    <td><label for="cfbagencia">Agência: *</label></td>
                    <td>
                        <input name="cfbagencia" id="cfbagencia" style="width: 350px;" value="<?=$view['cfbagencia']?>"/>
                    </td>
                </tr>
                <tr>
                    <td><label for="cfbconta_corrente">Conta: *</label></td>
                    <td>
                        <input name="cfbconta_corrente" id="cfbconta_corrente" style="width: 350px;" value="<?=$view['cfbconta_corrente']?>" />
                    </td>
                </tr>
                <tr>
                    <td><label for="cfbagencia_convenio">Agência Convênio:</label></td>
                    <td>
                        <input name="cfbagencia_convenio" id="cfbagencia_convenio" style="width: 350px;" value="<?=$view['cfbagencia_convenio']?>"/>
                    </td>
                </tr>
                <tr>
                    <td><label for="cfbconta_corrente_convenio">Conta Corrente Convênio:</label></td>
                    <td>
                        <input name="cfbconta_corrente_convenio" id="cfbconta_corrente_convenio" style="width: 350px;" value="<?=$view['cfbconta_corrente_convenio']?>" />
                    </td>
                </tr>
                <tr>
                    <td><label for="cfbtipo">Tipo: *</label></td>
                    <td>
                        <select name="cfbtipo" id="cfbtipo">
                            <option value="">Selecione</option>
                            <option <?=($view['cfbtipo'] == 'A')?"selected='selected'":""?> value="A">A - Ativo</option>
                            <option <?=($view['cfbtipo'] == 'C')?"selected='selected'":""?> value="C">C - Caixa</option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td><label for="cfbfluxo">Consta em Fluxo de Caixa:</label></td>
                    <td>
                        <input type="checkbox" name="cfbfluxo" id="cfbfluxo" value="true" <?=($view['cfbfluxo']==true)?'checked="checked"':''?>/>
                    </td>
                </tr>
                <tr>
                    <td colspan="2"><label>(*) Campos de preenchimento obrigatório.</label></td>
                </tr>
                <tr class="tableRodapeModelo1" style="height:23px;">
                    <td align="center" colspan="4">
                        <input type="button" name="bt_salvar" id="bt_salvar" value="Salvar" class="botao" onclick="jQuery('#editarBanco').submit();" style="width:70px;">
                        <input type="button" name="bt_voltar" id="bt_voltar" value="Voltar" class="botao" onclick="window.location.href='cad_banco.php';" style="width:70px;">
                    </td>
                </tr>
            </table>
        </form>
    </td>
</tr>