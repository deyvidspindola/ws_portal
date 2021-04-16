<?php
/**
 * @author	Emanuel Pires Ferreira
 * @email	epferreira@brq.com
 * @since	24/01/2013
 */
?>
<tr>
    <td align="center">
        <form name="novoEquipamentoProjeto" id="novoEquipamentoProjeto" method="post" action="">
            <input type="hidden" name="eproid" id="eproid" value="<?=$view['eproid']?>" />
            <table class="tableMoldura dados_pesquisa">
                <tr class="tableSubTitulo">
                    <td colspan="4"><h2>Dados Principais</h2></td>
                </tr>
                <tr class="linha_vazia"></tr>                
                <tr>
                    <td width="20%"><label for="eprnome">* Projeto:</label></td>
                    <td colpsan="3">
                        <input name="eprnome" id="eprnome" style="width: 350px;" value="<?=$view['eprnome']?>" />
                    </td>
                </tr>
                <tr>
                    <td><label for="eprmotivo">Motivo do Projeto:</label></td>
                    <td>
                        <textarea name="eprmotivo" id="eprmotivo" style="width: 350px;"><?=$view['eprmotivo']?></textarea>
                    </td>
                    <?php if($view['eprdt_alteracao'] != "") : ?>
                        <td align="right">Última Alteração:<br />Usuário:</td>
                        <td align="left">
                            <?php $arrData = explode(" ",$view['eprdt_alteracao']); ?>
                            <?php $data = implode("/",array_reverse(explode("-",$arrData[0])));?> 
                            <span style="padding-left:15px"><?=$data?></span><br />
                            <span style="padding-left:15px"><?=$view['nm_usuario']?></span>
                        </td>
                    <?php else : ?>
                        <td colspan="2"></td>
                    <?php endif; ?>
                </tr>
                <tr>
                    <td><label for="eprdescricao_tecnica">Descrição Técnica:</label></td>
                    <td colspan="3">
                        <textarea name="eprdescricao_tecnica" id="eprdescricao_tecnica" style="width: 350px;"><?=$view['eprdescricao_tecnica']?></textarea>
                    </td>
                </tr>
                <tr>
                    <td><label for="nomedepara">Produto:</label></td>
                    <td colspan="3">
                        <input type="text" name="eprprdoid" id="eprprdoid" size="10" value="<?=$view['eprprdoid']?>" OnKeyUp="formatar(this,'@')" OnBlur="revalidar(this,'@');" <?=(($view['nomeProduto'] && $view['eprprdoid'])?"style='background: #F0F0F0;'readonly='true'":"")?>>
                        <input type="text" name="nomedepara" id="nomedepara" size="50" value="<?=$view['nomeProduto']?>" <?=(($view['nomeProduto'] && $view['eprprdoid'])?"style='background: #F0F0F0;'readonly='true'":"")?>>
                        <input type="button" name="btPesquisaProduto" id="btPesquisaProduto" class="botao"  <?php if($view['nomeProduto'] && $view['eprprdoid']){ echo " value='Limpar'"; }else{ echo " value='Pesquisar'"; }?> style="width:70px;">
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
                <tr>
                    <td><label for="eprquadriband">* Quadriband:</label></td>
                    <td colspan="3">
                        <select name="eprquadriband" id="eprquadriband">
                            <option value="">Selecione</option>
                            <option <?=(isset($view['eprquadriband']) && $view['eprquadriband'] == "t")?'selected="selected"':''?> value="t">Sim</option>
                            <option <?=(isset($view['eprquadriband']) && $view['eprquadriband'] == "f")?'selected="selected"':''?> value="f">Não</option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td><label for="eprcompativel_jamming">* Compatível Jamming:</label></td>
                    <td colspan="3">
                        <select name="eprcompativel_jamming" id="eprcompativel_jamming">
                            <option value="">Selecione</option>
                            <option <?=(isset($view['eprcompativel_jamming']) && $view['eprcompativel_jamming'] == "t")?'selected="selected"':''?> value="t">Sim</option>
                            <option <?=(isset($view['eprcompativel_jamming']) && $view['eprcompativel_jamming'] == "f")?'selected="selected"':''?> value="f">Não</option>
                        </select>
                    </td>
                </tr>
                <tr class="linha_vazia"></tr>
                <tr class="tableSubTitulo">
                    <td colspan="4"><h2>Configurações Portal:</h2></td>
                </tr>
                <tr class="linha_vazia"></tr>
                <tr>
                    <td><label for="eprteste_portal">* Executa Testes no Portal:</label></td>
                    <td colspan="3">
                        <select name="eprteste_portal" id="eprteste_portal">
                            <option <?=(isset($view['eprteste_portal']) && $view['eprteste_portal'] == "t")?'selected="selected"':''?> value="t">Sim</option>
                            <option <?=(isset($view['eprteste_portal']) && $view['eprteste_portal'] == "f")?'selected="selected"':''?> value="f">Não</option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td><label for="eprtipo">* Tipo Portal:</label></td>
                    <td colspan="3">
                        <select name="eprtipo" id="eprtipo" style="width:100px;">
                            <option <?=(isset($view['eprtipo']) && $view['eprtipo'] == "CG")?'selected="selected"':''?> value="CG">Carga</option>
                            <option <?=(isset($view['eprtipo']) && $view['eprtipo'] == "CO")?'selected="selected"':''?> value="CO">Casco</option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td><label for="eprprecisao_odometro_portal">Precisão Odômetro:</label></td>
                    <td colspan="3">
                        <input name="eprprecisao_odometro_portal" id="eprprecisao_odometro_portal" style="width: 100px;" value="<?=$view['eprprecisao_odometro_portal']?>" />
                        <span style="color:red; font-size:10px;">* Quantidade de casas decimais a ser usadas no Portal para o usuário informar o KM do Odômetro</span>
                    </td>
                </tr>
                <tr>
                    <td><label for="eprmultiplicador_odometro_posicao">Multiplicador Odômetro:</label></td>
                    <td colspan="3">
                        <input name="eprmultiplicador_odometro_posicao" id="eprmultiplicador_odometro_posicao" style="width: 100px;" value="<?=$view['eprmultiplicador_odometro_posicao']?>" />
                        <span style="color:red; font-size:10px;">* Multiplicador a ser usado na conversão do KM retornado pelo Equipamento</span>
                    </td>
                </tr>
                <tr>
                    <td><label for="eprtolerancia_odometro">Tolerância Odômetro:</label></td>
                    <td colspan="3">
                        <input name="eprtolerancia_odometro" id="eprtolerancia_odometro" style="width: 100px;" value="<?=$view['eprtolerancia_odometro']?>" />
                        <span style="color:red; font-size:10px;">* Tolerância do Odômetro a ser usado no Teste de Odômetro final. Informar a tolerância conforme retorno do equipamento (KM, metros, etc)</span>
                    </td>
                </tr>
                <tr>
                    <td><label for="eprqtd_testes_posicao">* Quantidade Testes Posição:</label></td>
                    <td colspan="3">
                        <input name="eprqtd_testes_posicao" id="eprqtd_testes_posicao" style="width: 100px;" value="<?=$view['eprqtd_testes_posicao']?>" />
                    </td>
                </tr>
                <tr>
                    <td><label for="eprintervalo_testes_posicao">* Intervalo entre Testes(Segundos):</label></td>
                    <td colspan="3">
                        <input name="eprintervalo_testes_posicao" id="eprintervalo_testes_posicao" style="width: 100px;" value="<?=$view['eprintervalo_testes_posicao']?>" />
                    </td>
                </tr>
                <tr>
                    <td><label for="eprtipo">Origem Informações Tela Resumo:</label></td>
                    <td colspan="3">
                        <select name="eprresumo_configuracoes" id="eprresumo_configuracoes">
                            <option <?=(isset($view['eprresumo_configuracoes']) && $view['eprresumo_configuracoes'] == "E")?'selected="selected"':''?> value="E">Setup Equipamento</option>
                            <option <?=(isset($view['eprresumo_configuracoes']) && $view['eprresumo_configuracoes'] == "C")?'selected="selected"':''?> value="C">Contrato Intranet</option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td><label for="eprorigem_ultima_posicao">* Origem Última Posição:</label></td>
                    <td colspan="3">
                        <select name="eprorigem_ultima_posicao" id="eprorigem_ultima_posicao">
                            <option <?=(isset($view['eprorigem_ultima_posicao']) && $view['eprorigem_ultima_posicao'] == "B")?'selected="selected"':''?> value="B">Binário</option>
                            <option <?=(isset($view['eprorigem_ultima_posicao']) && $view['eprorigem_ultima_posicao'] == "O")?'selected="selected"':''?> value="O">Oracle</option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td><label for="eprtempo_posicao_teste">Tempo Posicionamento Teste:</label></td>
                    <td colspan="3">
                        <input name="eprtempo_posicao_teste" id="eprtempo_posicao_teste" style="width: 100px;" value="<?=$view['eprtempo_posicao_teste']?>" />
                        <span style="color:red; font-size:10px;">* Tempo de Posicionamento do Equipamento a ser configurado durante período de Testes</span>
                    </td>
                </tr>
                <tr>
                    <td><label for="eprtempo_posicao_final">Tempo Posicionamento Final:</label></td>
                    <td colspan="3">
                        <input name="eprtempo_posicao_final" id="eprtempo_posicao_final" style="width: 100px;" value="<?=$view['eprtempo_posicao_final']?>" />
                        <span style="color:red; font-size:10px;">* Tempo de Posicionamento do Equipamento a ser configurado após a finalização dos Testes (Teste de Configuração de Tempo)</span>
                    </td>
                </tr>
                <tr>
                    <td><label for="eprtempo_expiracao_bloqueio">Tempo de Expiração Bloqueio:</label></td>
                    <td colspan="3">
                        <input name="eprtempo_expiracao_bloqueio" id="eprtempo_expiracao_bloqueio" style="width: 100px;" maxlength="4" value="<?=$view['eprtempo_expiracao_bloqueio']?>"/>
                        <span style="color:red; font-size:10px;">* Tempo de Expiração Bloqueio do comando de Bloqueio enviado pelo portal (minutos)</span>
                    </td>
                </tr>
                 <tr>
                    <td><label for="eprvalor_ajuste_rpm">Ajuste da Marcha Lenta:</label></td>
                    <td colspan="3">
                        <input name="eprvalor_ajuste_rpm" id="eprvalor_ajuste_rpm" maxlength="6" style="width: 100px;" value="<?=$view['eprvalor_ajuste_rpm']?>" />
                        <span style="color:red; font-size:10px;"> Valor para ajuste de RPM Máximo ou RPM Mínimo.</span>
                    </td>
                </tr>
                <tr>
                    <td>
                        <label for="egtgrupo">* Grupo Tecnologia:</label>
                    </td>
                    <td colspan="3">
                        <select name="egtgrupo" id="egtgrupo"  style="width: 350px;">
                            <option value="">Selecione</option>
                            <?php foreach($view['grupo_tecnologia'] as $dado): 

                                $selected = (intval($view['epregtoid']) == $dado->egtoid) ? ' selected="selected"' : '';

                            ?>
                            <option value="<?php echo $dado->egtoid; ?>" <?php  echo $selected; ?>><?php echo $dado->egtgrupo;  ?></option>
                            <?php endForeach; ?>
                        </select>
                        <button id="btn_grupo_tecnologia" class="botao botao_pad"> Grupo Tecnologia </button>
                    </td>
                </tr>
                 <tr class="linha_vazia"></tr>
                <tr class="tableRodapeModelo1" style="height:23px;">
                    <td align="center" colspan="4">
                        <input type="button" name="bt_salvar" id="bt_salvar" value="Salvar" class="botao" onclick="jQuery('#novoEquipamentoProjeto').submit();" style="width:70px;">
                        <input type="button" name="bt_voltar" id="bt_voltar" value="Voltar" class="botao" onclick="window.location.href='cad_equipamento_projeto.php';" style="width:70px;">
                    </td>
                </tr>
            </table>
            <?php require_once _MODULEDIR_ . 'Cadastro/View/cad_equipamento_projeto/_grupoTecnologia.php'; ?>
        </form>
    </td>
</tr>
