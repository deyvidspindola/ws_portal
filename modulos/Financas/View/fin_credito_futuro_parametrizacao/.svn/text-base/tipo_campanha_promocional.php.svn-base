<div class="modulo_titulo">Tipo de Campanha Promocional</div>
<div class="modulo_conteudo">

    <?php echo $this->exibirMensagem(); ?>

    <input type="hidden" name="cftpoid" id="cftpoid" />

    <div class="bloco_titulo">Dados para pesquisa</div>
    <div class="bloco_conteudo">
        <div class="formulario">

            <div class="campo maior">
                <label for="cftpdescricao">Descrição</label>
                <input type="text" id="cftpdescricao" name="cftpdescricao" class="campo" maxlength="80" value="<?php echo (isset($filtros->descricao)) ? htmlentities($filtros->descricao) : ''; ?>" />
            </div>     

            <div class="clear"></div>

        </div>
    </div>
    <div class="bloco_acoes">
        <button type="button" id="pesquisarTipoCampanhaPromocional">Pesquisar</button>
        <button type="button" id="novoTipoCampanhaPromocional">Novo</button>
    </div>

    <div class="separador"></div>

    <?php 
        $totalResultados = count($this->resultados);
        if ( $totalResultados > 0 ){ 
    ?>

    <div class="bloco_titulo">Resultado da pesquisa</div>
    <div class="bloco_conteudo">
        <div class="listagem">
            <table>
                <thead>
                    <tr>
                        <th>Descrição</th>
                        <th class="centro" width="50">Excluir</th>
                    </tr>
                </thead>

                <tbody>
                <?php 
                $class = '';
                foreach($this->resultados as $tipo){
                    $class = ($class == 'par') ? '' : 'par';
                ?>
                    <tr class="<?php echo $class; ?>">
                        <td><?php echo $tipo->cftpdescricao; ?></td>
                        <td class="centro">
                            <a href="#" onclick="excluirTipoCampanha('<?php echo $tipo->cftpoid; ?>');">
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
