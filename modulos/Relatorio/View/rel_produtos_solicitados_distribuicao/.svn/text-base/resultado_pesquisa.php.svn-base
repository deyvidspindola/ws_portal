    <div class="separador"></div>
    <div class="bloco_titulo">Resultado da Pesquisa</div>
    <div class="bloco_conteudo">
        <div class="listagem">
            <table>
			    <thead>
                    <tr>
                        <th class="centro">Data da Solicita&ccedil;&atilde;o</th>
                        <th class="medio centro">Status da Solicita&ccedil;&atilde;o</th>
                        <th class="medio centro">N&ordm; O.S.</th>
                        <th class="medio centro">Data de Agendamento</th>
                        <th class="medio centro">Tipo O.S.</th>
                        <th class="medio centro">Classe do Cliente</th>
                        <th class="menor centro">UF</th>
                        <th class="medio centro">Cidade</th>
                        <th class="medio centro">Representante</th>
                        <th class="medio centro">Usu&aacute;rio Solicita&ccedil;&atilde;o</th>
                        <th class="menor centro">Remessa</th>
                        <th class="menor centro">A&ccedil;&atilde;o</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($this->view->dados->pesquisa as $indice => $registro) : ?>
                        <tr class="<?php echo ($indice + 1) % 2 == 0 ? 'par' : 'impar'; ?>">
                            <td class="centro"><?php echo $registro->dt_solicitacao ? date('d/m/Y', strtotime($registro->dt_solicitacao)) : ''; ?></td>
                            <td id="status_solicitacao_<?php echo $registro->sagoid; ?>" class="esquerda"><?php echo $registro->status_solicitacao; ?></td>
                            <td class="direita"><?php echo $registro->num_os; ?></td>
                            <td class="centro"><?php if($registro->repoid != 1624) { echo $registro->dt_agendamento ? date('d/m/Y', strtotime($registro->dt_agendamento)) : ''; } ?></td>
                            <td class="esquerda"><?php echo $registro->tipo_os; ?></td>
                            <td class="esquerda"><?php echo $registro->classe_cliente; ?></td>
                            <td class="esquerda"><?php echo $registro->estado; ?></td>
                            <td class="esquerda"><?php echo $registro->cidade; ?></td>
                            <td class="esquerda"><?php echo $registro->representante; ?></td>
                            <td class="esquerda"><?php echo $registro->usuario; ?></td>
                            <td class="esquerda"><?php echo $registro->nr_remessa; ?></td>
                            <td class="centro">
                                <?php if($registro->repoid != 1624) {?>
                                     <a href="javascript:atender(<?php echo $registro->repoid; ?>, <?php echo $registro->sagoid; ?>,<?php echo $registro->num_os; ?>, '<?php echo $registro->tipo_os; ?>', '<?php echo $registro->classe_cliente; ?>', '<?php echo $registro->flag_agendamento; ?>');">
                                        <img title="Gerenciar Solicita&ccedil;&atilde;o" src="images/icones/file.gif" class="icone">
                                    </a>
                                <?php } else{ ?>
                                        <a href="javascript:atenderCampinas(<?php echo $registro->repoid; ?>, <?php echo $registro->sagoid; ?>,<?php echo $registro->num_os; ?>, '<?php echo $registro->tipo_os; ?>', '<?php echo $registro->classe_cliente; ?>');">
                                        <img title="Gerenciar Solicita&ccedil;&atilde;o" src="images/icones/lupa.gif" class="icone">
                                    </a>
                                <?php } ?>
                            </td>
                        </tr>
                    <?php endforeach;?>        
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="12">
                            <?php if (count($this->view->dados->pesquisa) == 1) : ?>
                                1 registro encontrado.
                            <?php else : ?>
                                <?php echo count($this->view->dados->pesquisa); ?> registros encontrados.
                            <?php endif; ?>
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>

</div> 
<div id="solicitar-produtos-form" style="display:none" title="Gerenciar Solicita&ccedil;&atilde;o ">
</div>    


