<?php
/**
 * @author  Emanuel Pires Ferreira
 * @email   epferreira@brq.com
 * @since   24/01/2013
 */
?>
<tr>
    <td align="center">
        <form name="novaRestricao" id="novaRestricao" method="post" action="">
            <input type="hidden" name="ravoid" id="ravoid" value="" />
            <table class="tableMoldura dados_pesquisa">
                <tr class="tableSubTitulo">
                    <td colspan="4"><h2>Incluir Nova Restrição de Acessório:</h2></td>
                </tr>
                <tr>
                    <td><label for="eptcfdescricao">Tipo de Restrição:</label></td>
                    <td>
                        <input type="radio" name="ravtipo_restricao[]" id="ravtipo_restricaoP" value="P" class="ravtipo_restricao" /><label for="ravtipo_restricaoP">Restrição por Projeto</label>
                        <input type="radio" name="ravtipo_restricao[]" id="ravtipo_restricaoV" value="V" class="ravtipo_restricao" /><label for="ravtipo_restricaoV">Restrição por Projeto e Versão</label>
                        <input type="radio" name="ravtipo_restricao[]" id="ravtipo_restricaoC" value="C" class="ravtipo_restricao" /><label for="ravtipo_restricaoC">Restrição por Classe</label>
                        <input type="radio" name="ravtipo_restricao[]" id="ravtipo_restricaoT" value="T" class="ravtipo_restricao" /><label for="ravtipo_restricaoC">Tipo de Contrato</label>
                    </td>
                </tr>
                <tr id="linha_projeto" style="display:none;">
                    <td><label for="eproid">Projeto:</label></td>
                    <td>
                        <select name="eproid" id="eproid" onchange="(jQuery('input:checked').val() == 'V' ) ? preencheVersao(jQuery(this).val(),0): ''">
                            <option value="">Selecione</option>
                            <?php
                                foreach($arrEquipamentosProjeto as $eProjeto) {
                                    $sel = (isset($_POST['eproid']) && $_POST['eproid'] == $eProjeto['eproid'])?"selected = 'selected'":"";
                                    print "<option value='".$eProjeto['eproid']."' $sel>".$eProjeto['eprnome']."</option>";
                                }
                            ?>
                        </select>
                    </td>
                </tr>
                <tr id="linha_versao" style="display:none;">
                    <td><label for="eptcfdescricao">Versão:</label></td>
                    <td>
                        <select name="eveoid" id="eveoid">
                            <option value="">Selecione</option>
                        </select>
                        <span id="loadVersao" style="display:none;"><img src="images/progress.gif" alt="" /></span>
                    </td>
                </tr>
                <tr id="linha_classe" style="display:none;">
                    <td><label for="eptcfdescricao">Classe:</label></td>
                    <td>
                        <select name="eqcoid" id="eqcoid">
                            <option value="">Selecione</option>
                            <?php
                                foreach($arrEquipamentosClasse as $eClasse) {
                                    $sel = (isset($_POST['eqcoid']) && $_POST['eqcoid'] == $eClasse['eqcoid'])?"selected = 'selected'":"";
                                    print "<option value='".$eClasse['eqcoid']."' $sel>".$eClasse['eqcdescricao']."</option>";
                                }
                            ?>
                        </select>
                    </td>
                </tr>
                <tr id="linha_tipo_contrato" style="display:none;">
                    <td><label for="tpcoid">Tipo de Contrato:</label></td>
                    <td>
                        <select name="tpcoid" id="tpcoid" style="width:340px;"> 
                            <option value="">Selecione</option>
                            <?php 
                                foreach($arrEquipamentosTipoContrato as $tpcoid => $tpcdescricao) {
                                    $sel = (isset($_POST['tpcoid']) && $_POST['tpcoid'] != '' && $_POST['tpcoid'] == $tpcoid) ? "selected = 'selected'" : "";
                                    print "<option value='".$tpcoid."' $sel>".$tpcdescricao."</option>";
                                }
                            ?>
                        </select>
                    </td>
                </tr>
                <tr id="linha_produto" style="display:none;">
                    <td><label for="nomedepara">Acessório (Produto):</label></td>
                    <td>
                        <input type="text" name="prdoid" id="prdoid" size="10" OnKeyUp="formatar(this,'@')" OnBlur="revalidar(this,'@');" />
                        <input type="text" name="nomedepara" id="nomedepara" size="50" />
                        <input type="button" name="btPesquisaProduto" id="btPesquisaProduto" class="botao" value="Pesquisar" style="width:70px;" />
                        <img align="absmiddle" onclick="mostrarHelpComment(this,'Digite ao menos três caracteres e clique em pesquisar para buscar os produtos.','D' , '');" onmouseout="document.body.style.cursor='default';" onmouseover="document.body.style.cursor='pointer';" src="images/help10.gif"> 
                        
                        <div id="div_img_pesquisa_produto" style="display:none;">
                            <img src="images/progress.gif">
                        </div>                                                      
                        
                    </td>
                </tr>
                <tr>
                    <td></td>
                    <td>
                        <div id="result_pesq_produto"></div>
                    </td>
                </tr>
                <tr class="tableRodapeModelo1" style="height:23px;">
                    <td align="center" colspan="4">
                        <input type="button" name="bt_salvar" id="bt_salvar" value="Salvar" class="botao" onclick="jQuery('#novaRestricao').submit();" style="width:70px;">
                        <input type="button" name="bt_voltar" id="bt_voltar" value="Voltar" class="botao" onclick="window.location.href='cad_restricoes_vinculacao_acessorio.php';" style="width:70px;">
                    </td>
                    <span id="loading" class="msg"></span>
                </tr>
            </table>
        </form>
    </td>
</tr>

<form id="redirecionar" method="post">
    <input type="hidden" name="mensagem" id="mensagem" value="" /> 
</form>
