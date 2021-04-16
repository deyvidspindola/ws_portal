<div class="bloco_titulo">Dados para Pesquisa</div>
<div class="bloco_conteudo">
    <div class="formulario">
        
    	<div class="campo medio">
    		<label for="filtro_gmeano">Ano de referência *</label>
            <select id="filtro_gmeano" name="filtro_gmeano">
                <?php $ano = 2014; ?>
                <?php 
                    $anoReferencia = intval(date('Y'));
                    if ($anoReferencia < '2014') {
                        $anoReferencia = 2014;
                    }
                 ?>
                <?php $anoMax = $anoReferencia + 1; ?>
                <?php for ($ano; $ano <= $anoMax; $ano++ ) : ?>
                    <option <?php echo $this->view->parametros->filtro_gmeano == $ano ? 'selected="selected"' : '' ?> value="<?php echo $ano ?>"><?php echo $ano ?></option>
                <?php endfor; ?>
            </select>
    	</div>

    	<div class="campo medio">
    		<label for="filtro_gmeoid">Nome da meta</label>
    		<select id="filtro_gmeoid" name="filtro_gmeoid">
    			<option value="">-- Escolha --</option>
    			<?php if (isset($this->view->parametros->listarMetas) && !empty($this->view->parametros->listarMetas)) : ?>
    				<?php foreach ($this->view->parametros->listarMetas AS $meta) : ?>
    					<option <?php echo $this->view->parametros->filtro_gmeoid == $meta['id'] ? 'selected="selected"' : '' ?> value="<?php echo $meta['id'] ?>"><?php echo $meta['label'] ?></option>
    				<?php endforeach; ?>
    			<?php endif; ?>
    		</select>
    		<img class="carregando invisivel" src="images/ajax-loader-circle.gif">
    	</div>

    	<div class="clear"></div><div class="campo medio">
    	<label for="filtro_cargo">Cargo</label>
    	<select id="filtro_cargo" name="filtro_cargo">
    		<option value="">-- Escolha --</option>
			<?php if (isset($this->view->parametros->listarCargos) && !empty($this->view->parametros->listarCargos)) : ?>
				<?php foreach ($this->view->parametros->listarCargos AS $cargo) : ?>
					<option <?php echo $this->view->parametros->filtro_cargo == $cargo['id'] ? 'selected="selected"' : '' ?> value="<?php echo $cargo['id'] ?>"><?php echo $cargo['label'] ?></option>
				<?php endforeach; ?>
			<?php endif; ?>    		
    	</select>
    </div>

    <div class="campo medio">
    	<label for="filtro_gmefunoid_responsavel">Funcionário</label>
    	<select id="filtro_gmefunoid_responsavel" name="gmefunoid_responsavel">
    		<option value="">-- Escolha --</option>
    		<?php if (isset($this->view->parametros->listarFuncionarios) && !empty($this->view->parametros->listarFuncionarios)) : ?>
				<?php foreach ($this->view->parametros->listarFuncionarios AS $funcionario) : ?>
					<option <?php echo $this->view->parametros->gmefunoid_responsavel == $funcionario['id'] ? 'selected="selected"' : '' ?> value="<?php echo $funcionario['id'] ?>"><?php echo $funcionario['label'] ?></option>
			<?php endforeach; ?>    
			<?php endif; ?>		
    	</select>
    	<img class="carregando invisivel" src="images/ajax-loader-circle.gif">
    </div>

    <div class="clear"></div>


    </div>
</div>

<div class="bloco_acoes">
    <button type="submit" id="bt_pesquisar">Pesquisar</button>
    <button type="button" id="bt_novo">Novo</button>
    <button type="button" id="bt_exportar">Exportar</button>
</div>







