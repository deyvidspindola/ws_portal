<div class="bloco_titulo">Dados para Pesquisa</div>
<div class="bloco_conteudo">
    <div class="formulario">
        
        <div class="campo maior">
			<label for="eproid_busca">Equipamento Projeto *</label>
			<select id="eproid_busca" name="eproid_busca" tabindex="1">
				<option value="">Escolha</option>
				<?php foreach ($this->view->equipamentoProjetoList as $id=>$projeto): ?>
					<option value="<?php echo $projeto['eproid'] ?>" <?php echo ($this->view->parametros->eproid_busca == $projeto['eproid']) ? 'selected="selected"' : '' ?> >
						<?php echo $projeto['eprnome'] ?>
					</option>
				<?php endforeach;?>
			</select>
		</div>

		<div class="clear"></div>
			
		<div class="campo maior">
			<label for="eqcoid_busca">Equipamento Classe</label>
			<select id="eqcoid_busca" name="eqcoid_busca" tabindex="2">
				<option value="">Escolha</option>
				<?php foreach ($this->view->equipamentoClasseList as $id=>$classe): ?>
					<option value="<?php echo $classe['eqcoid'] ?>" <?php echo ($this->view->parametros->eqcoid_busca == $classe['eqcoid']) ? 'selected="selected"' : '' ?> >
						<?php echo $classe['eqcdescricao'] ?>
					</option>
				<?php endforeach;?>
			</select>
		</div>
			
		<div class="clear"></div>
			
		<div class="campo maior">
			<label for="eveoid_busca">Equipamento Versão</label>
			<select id="eveoid_busca" name="eveoid_busca" tabindex="3">
				<option value="">Escolha um projeto</option>						
		        <?php if (isset($this->view->parametros->eproid_busca) && $this->view->parametros->eproid_busca != ''): ?>
		            <?php $versaoList = $this->dao->getEquipamentoVersaoList($this->view->parametros->eproid_busca); ?>
		            <?php foreach ($versaoList as $versao): ?>
		                <option value="<?php echo $versao['eveoid']; ?>" <?php echo ($this->view->parametros->eveoid_busca == $versao['eveoid']) ? 'selected="selected"' : '' ; ?>>
		                    <?php echo $versao['eveversao'];?>
						</option>
		            <?php endforeach;?>
				<?php endif;?>
			</select>
		</div>
			
		<div class="clear"></div>

	</div>
</div>

<div class="bloco_acoes">
    <button type="button" id="bt_pesquisar" tabindex="4">Pesquisar</button>
    <button type="button" id="bt_voltar" tabindex="5">Voltar</button>
</div>