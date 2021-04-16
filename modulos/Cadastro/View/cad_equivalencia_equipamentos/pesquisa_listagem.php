<div id="resultado_pesquisa">
    <div class="bloco_titulo">Resultado da Pesquisa</div>
    <div class="bloco_conteudo">					        
        <div class="listagem">
            <table>
                <thead>
                    <tr>
                        <?php if (isset($this->parametros->classes_sem_cadastro) && $this->parametros->classes_sem_cadastro == 1) : ?>
                            <th class="menor centro">Classe</th>
                        <?php else: ?>
                            <th class="menor centro" style="width: 90px">Modalidade</th>
                            <th class="menor centro" style="width: 200px">Classe</th>
                            <th class="medio centro" style="width: 200px">Tipo</th>
                            <th class="maior centro" style="width: 90px" >Data de Cadastro</th>														
                            <th class="maior centro" style="width: 90px" >Última Alteração</th>	
                            <th class="maior centro" style="width: 200px">Responsável pelo Cadastro</th>	
                            <th class="maior centro" style="width: 200px">Responsável pela Alteração</th>	
                            <th class="centro" style="width: 50px">Ações</th>
                        <?php endif; ?>
                    </tr>
                </thead>
                <tbody id="conteudo_listagem">
                    <?php foreach ($this->listagem as $item) : ?>
                    <?php $class = $class == 'impar' ? 'par' : 'impar'; ?>
                    <tr class="<?php echo $class ?>">
                        <?php if (isset($this->parametros->classes_sem_cadastro) && $this->parametros->classes_sem_cadastro == 1) : ?>
                            <td><?php echo $item->eqcdescricao; ?></td>
                        <?php else: ?>
                            <td><?php echo $item->modalidade; ?></td>
                            <td><?php echo $item->eqcdescricao; ?></td>
                            <td><?php echo is_null($item->tpcdescricao) ? 'TODOS' : $item->tpcdescricao; ?></td>
                            <td class="centro"><?php echo $item->eeqdt_cadastro; ?></td>
                            <td class="centro"><?php echo $item->leidt_alteracao; ?></td>
                            <td><?php echo $item->nm_usuario; ?></td>
                            <td><?php echo $item->nm_usuario_2; ?></td>
                            <td class="centro">
                            <span>
                                <a title="Editar" href="cad_equivalencia_equipamentos.php?acao=cadastrar&id=<?php echo $item->id; ?>">
                                    <img class="icone" src="images/edit.png" width="18">
                                </a>
                            </span>
                            </td>
                        <?php endif; ?>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
                <tfoot>
                    <tr>
                        <?php if (isset($this->parametros->classes_sem_cadastro) && $this->parametros->classes_sem_cadastro == 1) : ?>
                        <td class="centro">
                        <?php else: ?>
                        <td colspan="8" class="centro">
                        <?php endif; ?>
                            
                        <?php $s = count($this->listagem) > 1 ? 's' : ''; ?>
                        <?php echo count($this->listagem); ?> registro<?php echo $s ?> encontrado<?php echo $s ?>.
                        </td>
                            
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

</div>
