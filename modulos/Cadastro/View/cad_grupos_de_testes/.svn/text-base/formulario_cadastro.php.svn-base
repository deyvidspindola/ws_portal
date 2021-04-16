<div class="separador"></div>

<?php if (count($this->view->parametrosEdicao) > 0): ?>
<div class="bloco_titulo">Dados Principais</div>
<div class="bloco_conteudo">
	<div class="listagem">
		<table>
			<thead>
				<tr>
					<th width="5%">Selecionar</th>
					<th class="medio centro">Comando</th>
					<th class="medio centro">Teste</th>
					<th class="medio centro">Depende do Teste</th>
				</tr>
			</thead>
			<tbody>
                <?php $classeLinha = "par"; ?>
                <?php $tabindex = 5; ?>
                    <?php foreach ($this->view->parametrosEdicao as $resultado) : ?>
                        <?php $classeLinha = ($classeLinha == "") ? "par" : ""; ?>
                    	<?php $tabindex ++; ?>
                    	<?php $checked = ($resultado->checked > 0) ? 'checked="checked"' : ""; ?>
						<tr class="<?php echo $classeLinha; ?>">
                        	<td class="centro">
                        		<input type="checkbox" class="selecao" name="check[]" title="Selecionar" value="<?php echo $resultado->eptpoid; ?>" tabindex="<?php echo $tabindex ?>" <?php echo $checked; ?> />
                        	</td>
                    		<td><?php echo $resultado->comando; ?></td>
                    		<td><?php echo $resultado->instrucao; ?></td>
                    		<td><?php echo $resultado->depende; ?></td>
						</tr>
                    <?php endforeach; ?>
			</tbody>
			<tfoot>
				<tr>
					<td colspan="4">&nbsp;</td>
				</tr>
			</tfoot>
		</table>
	</div>
	<div class="formulario">
		<div class="campo maior">
			<label for="egtnome">Nome *</label>
			<?php $tabindex ++; ?>
			<?php $this->view->parametros->egtnome = ($resultado->egtnome != "") ? trim($resultado->egtnome) : ""; ?>
			<input id="egtnome" name="egtnome" value="<?php echo $this->view->parametros->egtnome; ?>" class="campo" type="text" tabindex="<?php echo $tabindex ?>"/>
		</div>
		
		<div class="clear"></div>
		
	</div>
</div>
<div class="bloco_acoes">
	<?php if ($_SESSION['funcao']['manter_grupo_teste'] == 1): ?>
	<?php $tabindex ++; ?>
	<button type="button" id="bt_salvar" tabindex="<?php echo $tabindex ?>">Salvar Grupo</button>
	<?php endif; ?>
</div>
<?php endif; ?>

<?php if (count($this->view->parametrosGrupo) > 0 && count($this->view->parametrosEdicao) > 0): ?>
<div class="separador"></div>
<?php endif; ?>

<?php if (count($this->view->parametrosGrupo) > 0): ?>
<div class="bloco_titulo">Grupos Cadastrados</div>
<div class="bloco_conteudo">
    <div class="listagem">
        <table>
            <?php $classeLinha = "par"; ?>
            <?php $grupoIdAnterior = ""; ?>
            <?php foreach ($this->view->parametrosGrupo as $resultadoGrupo) : ?>
            <?php $classeLinha = ($classeLinha == "") ? "par" : ""; ?>
            <?php $tabindex ++; ?>
            <thead>
            	<?php if($grupoIdAnterior != $resultadoGrupo->egtoid):?>
              	<tr>
                    <th class="esquerda" colspan="2"><?php echo $resultadoGrupo->grupo; ?></th> 
                  	<th class="direita">
						<?php if ($_SESSION['funcao']['manter_grupo_teste'] == 1): ?>
                   		<a href="javascript:return false;" class="editarGrupo" title="Editar Grupo" egtoid="<?php echo $resultadoGrupo->egtoid; ?>" >
                   			<img alt="Editar" src="images/edit.png" class="icone" tabindex="<?php echo $tabindex ?>" />
                   		</a>
                   		
                   		<a href="javascript:return false;" class="excluirGrupo" title="Excluir Grupo" egtoid="<?php echo $resultadoGrupo->egtoid; ?>" >
                   			<img alt="Excluir" src="images/icon_error.png" class="icone" tabindex="<?php echo $tabindex ?>" />
                   		</a>
                   		<?php endif;?>
                   	</th>
               	</tr>
                <tr>
                    <th class="medio centro">Comando</th>
                   	<th class="medio centro">Teste</th>
                   	<th class="medio centro">Depende do Teste</th>
                </tr>
            	<?php $grupoIdAnterior = $resultadoGrupo->egtoid; ?>
               	<?php endif;?>
            </thead>
            <tbody>
                <tr class="<?php echo $classeLinha; ?>">
                	<td><?php echo $resultadoGrupo->comando; ?></td>
                    <td><?php echo $resultadoGrupo->instrucao; ?></td>
                    <td><?php echo $resultadoGrupo->depende; ?></td>
                </tr>
            </tbody>
        	<?php endforeach; ?>
        </table>
    </div>
</div>
<?php endif; ?>
			





