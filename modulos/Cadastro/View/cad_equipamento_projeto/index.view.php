<?php
/**
 * @author	Emanuel Pires Ferreira
 * @email	epferreira@brq.com
 * @since	11/01/2013
 */
?>
<tr>
    <td align="center">
        <form name="busca_equipamentos" id="busca_equipamentos" method="post" action="">
            <input type="hidden" name="acao" id="acao" value="pesquisar" />
            <table class="tableMoldura dados_pesquisa">
                <tr class="tableSubTitulo">
                    <td colspan="4"><h2>Dados para Pesquisa</h2></td>
                </tr>
                <tr>
                    <td width="20%"><label for="eprnome">Equipamento (Projeto):</label></td>
                    <td colspan="3">
                        <input type="text" name="eprnome" id="eprnome" value="<?=(isset($_POST['eprnome']) && $_POST['eprnome'] != "")?$_POST['eprnome']:''?>" style="width: 350px;" />
                    </td>
                </tr>
                <tr>
                    <td><label for="eprtipo">Tipo Portal:</label></td>
                    <td colspan="3">
                        <select name="eprtipo" id="eprtipo" style="width: 350px;">
                            <option value="">Selecione</option>
                            <option <?=(isset($_POST['eprtipo']) && $_POST['eprtipo'] == "CO")?'selected="selected"':''?> value="CO">Casco</option>
                            <option <?=(isset($_POST['eprtipo']) && $_POST['eprtipo'] == "CG")?'selected="selected"':''?> value="CG">Carga</option>
                        </select>
                    </td>
                </tr>
                <tr class="tableRodapeModelo1" >
                    <td align="center" colspan="4">
                        <input type="button" name="bt_pesquisar" id="bt_pesquisar" value="Pesquisar" class="botao" onclick="jQuery('#busca_equipamentos').submit();" style="width:70px;">
                        <input type="button" name="bt_cadastrar" id="bt_cadastrar" value="Novo" class="botao" onclick="exibeCadastro();" style="width:70px;">
                    </td>
                </tr>
            </table>
        </form>
    </td>
</tr>