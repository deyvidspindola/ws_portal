    <div class="bloco_titulo" style="cursor: default; ">Resultado da Pesquisa</div>
    <div class="bloco_conteudo">
        <div class="listagem">
            <table>
                <thead>
                    <tr>
                        <th style="cursor: default; " class="maior centro">Funcionário</th>
	                    <th class="medio esquerda" style="cursor: default; ">Depto.</th>
	                    <th class="menor centro" style="cursor: default; ">Importação</th>
	                    <th class="medio centro" style="cursor: default; ">Criar PA (Plano de Ação)</th>
	                    <th class="medio centro" style="cursor: default; ">Criar Ação</th>
	                    <th class="medio centro" style="cursor: default; ">Super Usuário</th>
                	</tr>
                </thead>
                <tbody>
                	<?php $ids = array(); ?>
                	<?php foreach($this->view->dados->funcionarios as $indice => $funcionario) :?>
                		<?php $ids[] = $funcionario->funoid; ?>
	                    <tr class="<?php echo ($indice + 1) % 2 == 0 ? 'par' : 'impar'; ?>">
	                        <td style="cursor: default; ">
	                        	<?php echo $funcionario->funcionario; ?>
	                        </td>
			                <td style="cursor: default; ">
			                	<?php echo $funcionario->departamento; ?>
			                </td>
			                 <td class="centro" style="cursor: default; ">
			                 	<?php if(empty($funcionario->importacao)) :?>
			                		<input class="checkbox" id="importacao_<?php echo $funcionario->funoid; ?>" name="importacao[]" type="checkbox" value="0">
			                	<?php else : ?>
			                		<?php $checked = ($funcionario->importacao == 1) ? 'checked' : ''; ?>
			                		<input class="checkbox" id="importacao_<?php echo $funcionario->funoid; ?>" name="importacao[]" type="checkbox" value="1" <?php echo $checked; ?>>
			                	<?php endif ?>
			                </td>
			                <td class="centro" style="cursor: default; ">
			                	<?php if(empty($funcionario->criar_pa)) :?>
			                		<input class="checkbox" id="criar_pa_<?php echo $funcionario->funoid; ?>" name="criar_pa[]" type="checkbox" value="0">
			                	<?php else : ?>
			                		<?php $checked = ($funcionario->criar_pa == 1) ? 'checked' : ''; ?>
			                		<input class="checkbox" id="criar_pa_<?php echo $funcionario->funoid; ?>" name="criar_pa[]" type="checkbox" value="1" <?php echo $checked; ?>>
			                	<?php endif ?>
			                </td>
			                <td class="centro" style="cursor: default; ">
			                	<?php if(empty($funcionario->criar_acao)) :?>
			                		<input class="checkbox" id="criar_acao_<?php echo $funcionario->funoid; ?>" name="criar_acao[]" type="checkbox" value="0">
			                	<?php else : ?>
			                		<?php $checked = ($funcionario->criar_acao == 1) ? 'checked' : ''; ?>
			                		<input class="checkbox" id="criar_acao_<?php echo $funcionario->funoid; ?>" name="criar_acao[]" type="checkbox" value="1" <?php echo $checked; ?>>
			                	<?php endif ?>
			                </td>
			                <td class="centro" style="cursor: default; ">
			                	<?php if(empty($funcionario->super_usuario)) :?>
			                		<input class="checkbox" id="super_usuario_<?php echo $funcionario->funoid; ?>" name="super_usuario[]" type="checkbox" value="0">
			                	<?php else : ?>
			                		<?php $checked = ($funcionario->super_usuario == 1) ? 'checked' : ''; ?>
			                		<input class="checkbox" id="super_usuario_<?php echo $funcionario->funoid; ?>" name="super_usuario[]" type="checkbox" value="1" <?php echo $checked; ?> >
			                	<?php endif ?>
			                </td>
	                    </tr>
                    <?php endforeach;?>  
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="6" style="cursor: default; ">
                        	<button id="bt_atualizar" type="button" style="cursor: default; ">Atualizar</button>
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
    <input type="hidden" id="ids" value="<?php echo implode(',', $ids); ?>">
