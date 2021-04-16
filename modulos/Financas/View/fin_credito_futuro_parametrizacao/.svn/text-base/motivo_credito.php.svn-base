<?php
if (isset($this->parametros->cfmctipo) && trim($this->parametros->cfmctipo) != '') {
    $this->parametros->cfmctipo = intval($this->parametros->cfmctipo);
} else {
    $this->parametros->cfmctipo = $this->parametros->cfmctipo;
}

?>
<div class="modulo_titulo">Motivo do Crédito</div>
<div class="modulo_conteudo">

    <?php echo $this->exibirMensagem(); ?>

    <input type="hidden" name="cfmcoid" id="cfmcoid" />

    <div class="bloco_titulo">Dados para Pesquisa</div>
    <div class="bloco_conteudo">
        <div class="formulario">

            <div class="campo maior">
                <label id="lbl_cfmcdescricao" for="cfmcdescricao">Descrição</label>
                <input tabindex="1" type="text" id="cfmcdescricao" name="cfmcdescricao" class="campo" maxlength="80" value="<?php echo (isset($this->parametros->descricao)) ? htmlentities($this->parametros->descricao) : ''; ?>" />
            </div>     

            <div class="clear"></div>
            <div class="campo maior">
                <label id="lbl_cfmctipo" for="cfmctipo">Tipo do Motivo:</label>
                <select tabindex="2" id="cfmctipo" name="cfmctipo" class="combo_pesquisa">
                    <option value="">Todos</option>
                    <option value=0<?php echo $this->parametros->cfmctipo === 0 ? ' selected="selected"' : ''; ?>>Outros</option>
                    <option value=1<?php echo $this->parametros->cfmctipo === 1 ? ' selected="selected"' : ''; ?>>Contestação</option>
                    <option value=2<?php echo $this->parametros->cfmctipo === 2 ? ' selected="selected"' : ''; ?>>Indicação de Amigo</option>
                    <option value=3<?php echo $this->parametros->cfmctipo === 3 ? ' selected="selected"' : ''; ?>>Isenção de Monitoramento</option>
                    <option value=4<?php echo $this->parametros->cfmctipo === 4 ? ' selected="selected"' : ''; ?>>Débito Automático</option>
                    <option value=5<?php echo $this->parametros->cfmctipo === 5 ? ' selected="selected"' : ''; ?>>Cartão de Crédito</option>
                </select>
            </div>
            <div class="clear"></div>
        </div>
    </div>
    <div class="bloco_acoes">
        <button tabindex="4" type="button" id="pesquisarMotivoCredito">Pesquisar</button>
        <button tabindex="5" type="button" id="novoMotivoCredito">Novo</button>
    </div>
    <div class="separador"></div>

    <?php
    $totalResultados = count($this->resultados);
    if ($totalResultados > 0) {
        ?>

        <div class="bloco_titulo">Resultado da pesquisa</div>
        <div class="bloco_conteudo">
            <div class="listagem">
                <table>
                    <thead>
                        <tr>
                            <th id="th_descricao" width="300">Descrição</th>
                            <th id="th_tipomotivo" width="230">Tipo do Motivo</th>
                            <th id="th_observacao">Observação</th>
                            <th id="th_excluir" class="centro" width="50">Excluir</th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php
                        $class = '';
                        foreach ($this->resultados as $tipo) {
                            $class = ($class == 'par') ? '' : 'par';
                            ?>
                            <tr class="<?php echo $class; ?>">
                                <td><?php echo wordwrap($tipo->cfmcdescricao, 32, "<br />", true); ?></td>
                                <td><?php echo $tipo->cfmctipo; ?></td>
                                <td><?php echo wordwrap($tipo->cfmcobservacao, 60, "<br />", true); ?></td>
                                <td class="centro">
                                    <a href="#" onclick="excluirMotivoCredito('<?php echo $tipo->cfmcoid; ?>');">
                                        <img class="icone" src="images/icon_error.png">
                                    </a>
                                </td>
                            </tr>

                        <?php } ?>
                    </tbody>
                </table>
            </div>

        </div>
        <div class="bloco_acoes">
            <p>
                <?php echo ($totalResultados > 1) ? $totalResultados . ' registros encontrados.' : '1 registro encontrado.'; ?>
            </p>

        </div>
    <?php } ?>
</div>
