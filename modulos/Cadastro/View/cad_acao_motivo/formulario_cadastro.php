<style>
    div.listagem table th {  
        text-align: center!important;  
    }
</style>


<form id="form_cadastrar"  method="post" action="cad_acao_motivo.php">
    <input type="hidden" id="acao" name="acao" value="cadastrar"/>
    <input type="hidden" id="aoamoid" name="aoamoid" value="<?php echo $this->view->parametros->aoamoid; ?>"/>
    <input type="hidden" id="motivo_id" name="motivo_id" value=""/>
    <input type="hidden" id="postTelaEdicao" name="postTelaEdicao" value="" />
    <div class="bloco_titulo">Ações</div>
    <div class="bloco_conteudo">
        <div class="formulario">

            <div class="campo medio">
                <label for="aoamdescricao">Descrição *</label>
                <input type="text" id="aoamdescricao" name="aoamdescricao" class="campo" maxLength="50" value="<?php echo $this->view->parametros->aoamdescricao; ?>" />
            </div> 

            <div class="clear"></div>

        </div>
    </div>

    <div class="bloco_acoes">
        <button type="submit" id="bt_confirmar" name="bt_confirmar" value="gravar">Confirmar</button>
        <button type="button" id="bt_cancelar">Cancelar</button>
    </div>
</form>

<!-- Bloco Motivos -->
<div id="cadastroMotivo" class="<?php echo ($this->view->parametros->aoamoid > 0) ? '' : 'invisivel'; ?>">
    <form id="form_cadastrar_motivo"  method="post" action="cad_acao_motivo.php">
        <input type="hidden" id="acao_motivo" name="acao" value="cadastrarMotivo"/>
        <input type="hidden" id="aoampai" name="aoampai" value="<?php echo $this->view->parametros->aoamoid; ?>"/>
        <div class="separador"></div>
        <div class="bloco_titulo">Motivos</div>
        <div class="bloco_conteudo">
            <div class="formulario">
                <div class="campo medio">
                    <label for="descricao">Descrição *</label>
                    <input type="text" id="aoamdescricao_motivo" name="aoamdescricao" class="campo" maxlength="50" />
                </div>
                <div class="clear"></div>
            </div><!-- bl_formulario -->
        </div>
        <div class="bloco_acoes">
            <button type="submit" id="bt_incluir" name="bt_incluir" value="incluir">Incluir</button>
        </div>
    </form>
</div>

<!-- Bloco Ações/Motivos Cadastrados -->
<div id="motivoCadastrados" class="<?php echo (isset($this->view->parametros->motivos) && count($this->view->parametros->motivos) > 0) ? '' : 'invisivel'; ?>">
    <div class="separador"></div>
    <div class="bloco_titulo">Motivos Cadastrados</div>
    <div class="bloco_conteudo">
        <div class="listagem">
            <table>
                <thead>
                    <tr>
                        <th>Descrição</th>
                        <th class="menor">Cadastro</th>
                        <th class="menor">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($this->view->parametros->motivos as $motivo) : ?>
                        <?php $classeLinha = ($classeLinha == "") ? "par" : ""; ?>
                        <tr class="<?php echo $classeLinha; ?>">
                            <td><?php echo $motivo->aoamdescricao; ?></td>
                            <td class="centro"><?php echo $motivo->aoamdt_cadastro; ?></td>
                            <td class="centro">
                                <a class="deletarMotivo" href="#" rel="<?php echo $motivo->aoamoid; ?>"><img src='images/icon_error.png' title='Excluir' class="icone" /></a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
                <tfoot>
                <td colspan="5" class="centro">
                    <?php
                    $quantidade = count($this->view->parametros->motivos);
                    echo ($quantidade > 1) ? $quantidade . ' registros encontrados.' : '1 registro encontrado.';
                    ?>
                </td>
                </tfoot>
            </table>
        </div>
    </div>
</div><!-- Div MotivoCadastrados -->

