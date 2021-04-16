<?php
/**
 * @author	Emanuel Pires Ferreira
 * @email	epferreira@brq.com
 * @since	11/01/2013
 */
?>
<tr>
    <td align="center">
    	<table class="tableMoldura dados_pesquisa">
            <tr class="tableSubTitulo">
                <td colspan="2"><h2>Dados de Origem da Copia</h2></td>
            </tr>
            <tr>
            	<td width="15%"><label for="eproid">Equipamento Projeto:</label></td>
                <td>
                	<?php
                        foreach($arrEquipamentosProjeto as $eProjeto) {
							if($view['eproid'] == $eProjeto['eproid'])
                            	print $eProjeto['eprnome'];
                        }
                    ?>
                </td>
            </tr>
            <tr>
                <td><label for="eqcoid">Equipamento Classe:</label></td>
                <td>
                    <?php
                        foreach($arrEquipamentosClasse as $eClasse) {
							if($view['eqcoid'] == $eClasse['eqcoid'])
                            	print $eClasse['eqcdescricao'];
                        }
                    ?>
                </td>
            </tr>
            <tr>
                <td><label for="eveoid">Equipamento Versão:</label></td>
                <td>
                	<?php
                        foreach($arrEquipamentosVersao['versoes'] as $eVersao) {
							if($view['eveoid'] == $eVersao['eveoid'])
                            	print $eVersao['eveversao'];
                        }
                    ?>
                </td>
            </tr>
        </table>
    </td>
</tr>
<tr>
    <td align="center">
        <form name="copiar_teste" id="copiar_teste" method="post" action="">
            <input type="hidden" name="epcvoid_ref" id="epcvoid_ref" value="<?=$_POST['epcvoid']?>" />
            <input type="hidden" name="eproid_ref" id="eproid_ref" value="<?=$view['eproid']?>" />
            <input type="hidden" name="eqcoid_ref" id="eqcoid_ref" value="<?=$view['eqcoid']?>" />
            <input type="hidden" name="eveoid_ref" id="eveoid_ref" value="<?=$view['eveoid']?>" />
            <table class="tableMoldura dados_pesquisa">
                <tr class="tableSubTitulo">
                    <td colspan="4"><h2>Dados do Destino da Copia</h2></td>
                </tr>
                <tr>
                    <td width="15%"><label for="eproid">Equipamento Projeto: *</label></td>
                    <td>
                        <select name="eproid" id="eproid" style="width: 350px;" onchange="preencheVersao(jQuery(this).val(),0);">
                            <option value="">Selecione</option>
                            <?php
                                foreach($arrEquipamentosProjeto as $eProjeto) {
                                    print "<option value='".$eProjeto['eproid']."'>".$eProjeto['eprnome']."</option>";
                                }
                            ?>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td><label for="eqcoid">Equipamento Classe: *</label></td>
                    <td>
                        <select name="eqcoid" id="eqcoid" style="width: 350px;">
                            <option value="">Selecione</option>
                            <?php
                                foreach($arrEquipamentosClasse as $eClasse) {
                                    print "<option value='".$eClasse['eqcoid']."'>".$eClasse['eqcdescricao']."</option>";
                                }
                            ?>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td><label for="eveoid">Equipamento Versão: *</label></td>
                    <td>
                        <select name="eveoid" id="eveoid" style="width: 350px;">
                            <option value="">Selecione</option>
                        </select>
                        <span id="loadVersao" style="display:none;"><img src="images/progress.gif" alt="" /></span>
                    </td>
                </tr>
                <tr class="tableRodapeModelo1" style="height:23px;">
                    <td align="center" colspan="4">
                        <input type="button" name="bt_salvar" id="bt_salvar" value="Salvar" class="botao" onclick="jQuery('#copiar_teste').submit();" style="width:70px;">
                        <input type="button" name="bt_voltar" id="bt_voltar" value="Voltar" class="botao" onclick="jQuery('#epcvoid').val(<?=$view['epcvoid']?>);jQuery('#form_edita').submit();" style="width:70px;">
                        <input type="button" name="bt_excluir" id="bt_excluir" value="Excluir" class="botao" onclick="jQuery('#busca_testes').submit();" style="display:none;width: 70px;">
                    </td>
                </tr>
            </table>
        </form>
        <form action="" method="post" name="form_edita" id="form_edita">
        	<input type="hidden" name="acao" id="acao" value="editar" />
	        <input type="hidden" name="epcvoid" id="epcvoid" value="" />
        </form>
    </td>
</tr>