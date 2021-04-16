<?php
/**
 * @author	Emanuel Pires Ferreira
 * @email	epferreira@brq.com
 * @since	13/03/2013
 */
?>
<tr>
    <td align="center">
        <form name="busca_bancos" id="busca_bancos" method="post" action="">
            <input type="hidden" name="acao" id="acao" value="pesquisar" />
            <table class="tableMoldura dados_pesquisa">
                <tr class="tableSubTitulo">
                    <td colspan="2"><h2>Empresa</h2></td>
                </tr>
                <tr>
                    <td><label for="tecoid">Empresa:</label></td>
                    <td>
                        <select name="tecoid" id="tecoid">
                            <option value="">Selecione</option>
                            <?php
                                foreach($arrEmpresas as $empresa) {
                                    $sel = (isset($_POST['tecoid']) && $_POST['tecoid'] == $empresa['tecoid'])?"selected = 'selected'":"";
                                    print "<option value='".$empresa['tecoid']."' $sel>".$empresa['tecrazao']."</option>";
                                }
                            ?>
                        </select>
                    </td>
                    
                </tr>
            </table>
            <table class="tableMoldura dados_pesquisa">
                <tr class="tableSubTitulo">
                    <td colspan="4"><h2>Dados para Pesquisa</h2></td>
                </tr>
                <tr>
                    <td><label for="cfbbanco">Código:</label></td>
                    <td>
                        <input type="text" name="cfbbanco" id="cfbbanco" value="<?=(isset($_POST['cfbbanco']) && $_POST['cfbbanco'] != "")?$_POST['cfbbanco']:''?>" style="width: 350px;" />
                    </td>
                    <td><label for="cfbnome">Descrição:</label></td>
                    <td>
                        <input type="text" name="cfbnome" id="cfbnome" value="<?=(isset($_POST['cfbnome']) && $_POST['cfbnome'] != "")?$_POST['cfbnome']:''?>" style="width: 350px;" />
                    </td>
                </tr>
                <tr class="tableRodapeModelo1" >
                    <td align="center" colspan="4">
                        <input type="button" name="bt_pesquisar" id="bt_pesquisar" value="Pesquisar" class="botao" style="width:90px;">
                        <input type="button" name="bt_cadastrar" id="bt_cadastrar" value="Novo" class="botao" onclick="exibeCadastro();" style="width:90px;">
                    </td>
                </tr>
            </table>
        </form>
    </td>
</tr>