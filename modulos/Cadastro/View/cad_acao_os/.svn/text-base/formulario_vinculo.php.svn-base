<ul class="bloco_opcoes">

    <li class="<?php echo ($this->view->parametros->acao == 'cadastrar' || trim($this->view->parametros->acao) == '') ?  'ativo' : '' ?>" id="aba_cadastrar" 
        <?php echo ($this->view->parametros->acao == 'cadastrar' || trim($this->view->parametros->acao) == '') ?  '' : 'style="background: url(images/fundo.gif);"' ?>>
        <a href="cad_acao_os.php">Cadastrar</a>
    </li>

    <li class="<?php echo ($this->view->parametros->acao == 'vincular') ?  'ativo' : '' ?>" id="aba_vincular" 
        <?php echo ($this->view->parametros->acao == 'vincular') ?  '' : 'style="background: url(images/fundo.gif);"' ?>>
        <a href="cad_acao_os.php?acao=vincular">Vincular Departamento</a>
    </li>

</ul>
<div class="bloco_titulo"></div>
<div class="bloco_conteudo">
    <div class="formulario">
        
        <div class="campo maior">
            <label id="lbl_mhcdescricao" for="mhcdescricao">Departamento *</label>
            <select id="depoid" name="depoid" class="obrigatorio">
                <option value="0">Escolha</option>
                <? foreach ($this->view->departamentos as $departamento) : ?>
                    <option value="<?=$departamento->depoid?>" <?= ($departamento->depoid == $this->view->parametros->depoid) ? 'selected' : ''?>><?=$departamento->depdescricao?></option>
                <? endforeach; ?>
            </select>
        </div>

        <div class="clear"><br><br></div>

        <div class="campo maior mcombos">
            <table>
                <tr>
                    <td align="right">
                        <label id="lbl_acoes_vinc" for="acoes_vinc">Ações Vinculadas:</label>
                        <select id="acoes_vinc" name="acoes_vinc[]" multiple class="mselect">
                        </select>
                    </td>
                    <td align="center">
                        <button id="btnadd" class="mbutton mbuttonadd" disabled>&lt;&lt;</button>
                        <button id="btnremove" class="mbutton" disabled>&gt;&gt;</button>
                    </td>
                    <td>
                        <label id="lbl_acoes_nao_vinc" for="acoes_nao_vinc">Ações Não Vinculadas:</label>
                        <select id="acoes_nao_vinc" name="acoes_nao_vinc[]" multiple class="mselect">
                        </select>
                    </td>
                </tr>
            </table>
        </div>

        <div class="clear"><br></div>
    </div>
</div>

<div class="bloco_acoes">
    <button type="submit" id="bt_gravar" name="bt_gravar" value="gravar">Salvar</button>
</div>