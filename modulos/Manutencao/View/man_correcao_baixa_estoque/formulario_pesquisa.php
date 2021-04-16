<div class="bloco_titulo noprint">Dados para Pesquisa</div>
<div class="bloco_conteudo noprint">
    <div class="formulario">
				
        <div class="campo data periodo">
			<div class="inicial">
    			<label>Período Concl. *</label>
                <input type="text" id="dataInicial"
                       name="dataInicial"
                       value="<?php echo $this->view->parametros->dataInicial?>"
                       class="campo"  tabindex="1" />
            </div>
            <div class="campo label-periodo">a</div>
            <div class="final">
                <label>&nbsp;</label>
                <input type="text" id="dataFinal"
                       name="dataFinal"
                       value="<?php echo $this->view->parametros->dataFinal?>"
                       class="campo"  tabindex="2" />
            </div>
        </div>
		<div class="campo maior">
			<label for="nomeusuoid_concl_busca">Usuário Conclusão</label>
			<input id="nomeusuoid_concl_busca" name="nomeusuoid_concl_busca" value="<?php echo $this->view->parametros->nomeusuoid_concl_busca?>" class="campo" type="text"  tabindex="3">
		</div>
		
		<div class="clear"></div>
		
		<div class="campo maior">
			<label for="repoid_busca">Representante Responsável</label>
			<select id="repoid_busca" name="repoid_busca" tabindex="4">
				<option value="">Escolha</option>
				<?php foreach ($this->view->representanteResponsavelList as $id=>$representante): ?>
					<option value="<?php echo $representante['id'] ?>" <?php echo ($this->view->parametros->repoid_busca == $representante['id']) ? 'selected="selected"' : '' ?> >
						<?php echo $representante['representante'] ?>
					</option>
				<?php endforeach;?>
			</select>
		</div>
		<div class="campo maior">
			<label for="itloid_busca">Instalador</label>
			<select id="itloid_busca" name="itloid_busca" tabindex="5">
				<option value="">Escolha um representante</option>						
		        <?php if (isset($this->view->parametros->repoid_busca) && $this->view->parametros->repoid_busca != ''): ?>
		            <?php $instaladorList = $this->dao->getInstaladorList($this->view->parametros->repoid_busca); ?>
		            <?php foreach ($instaladorList as $instalador): ?>
		                <option value="<?php echo $instalador['id']; ?>" <?php echo ($this->view->parametros->itloid_busca == $instalador['id']) ? 'selected="selected"' : '' ; ?>>
		                    <?php echo $instalador['instalador'];?>
						</option>
		            <?php endforeach;?>
				<?php endif;?>
			</select>
		</div>
		
		<div class="clear"></div>
		
		<div class="campo medio">
			<label for="ftcoid_busca">Região</label>
			<select id="ftcoid_busca" name="ftcoid_busca" tabindex="6">
				<option value="">Escolha</option>
				<?php foreach ($this->view->regiaoList as $regiao): ?>
					<option value="<?php echo $regiao['id'] ?>" <?php echo ($this->view->parametros->ftcoid_busca == $regiao['id']) ? 'selected="selected"' : '' ?> >
						<?php echo $regiao['regiao'] ?>
					</option>
				<?php endforeach;?>
			</select>
		</div>
		<div class="campo medio">
			<label for="otioid_busca">Item</label>
			<select id="otioid_busca" name="otioid_busca" tabindex="7">
				<option value="">Escolha</option>
				<?php foreach ($this->view->itemList as $id=>$item): ?>
					<option value="<?php echo $id ?>" <?php echo ($this->view->parametros->otioid_busca == $id) ? 'selected="selected"' : '' ?> >
						<?php echo $item ?>
					</option>
				<?php endforeach;?>
			</select>
		</div>
		<div class="campo medio">
			<label for="ostoid_busca">Tipo</label>
			<select id="ostoid_busca" name="ostoid_busca" tabindex="8">
				<option value="">Escolha</option>
				<?php foreach ($this->view->tipoList as $tipo): ?>
					<option value="<?php echo $tipo['id'] ?>" <?php echo ($this->view->parametros->ostoid_busca == $tipo['id']) ? 'selected="selected"' : '' ?> >
						<?php echo $tipo['tipo'] ?>
					</option>
				<?php endforeach;?>
			</select>
		</div>
		
		<div class="clear"></div>
		
		<div class="campo maior">
			<label for="eqcoid_busca">Classe Contrato</label>
			<select id="eqcoid_busca" name="eqcoid_busca" tabindex="9">
				<option value="">Escolha</option>
				<?php foreach ($this->view->classeContratoList as $classeContrato): ?>
					<option value="<?php echo $classeContrato['id'] ?>" <?php echo ($this->view->parametros->eqcoid_busca == $classeContrato['id']) ? 'selected="selected"' : '' ?> >
						<?php echo $classeContrato['classe'] ?>
					</option>
				<?php endforeach;?>
			</select>
		</div>
		<div class="campo maior">
			<label for="conmodalidade_busca">Modalidade Contrato</label>
			<select id="conmodalidade_busca" name="conmodalidade_busca" tabindex="10">
				<option value="">Escolha</option>
				<?php foreach ($this->view->modalidadeContratoList as $id=>$modalidadeContrato): ?>
					<option value="<?php echo $id ?>" <?php echo ($this->view->parametros->conmodalidade_busca == $id) ? 'selected="selected"' : '' ?> >
						<?php echo $modalidadeContrato ?>
					</option>
				<?php endforeach;?>
			</select>
		</div>
		
		<div class="clear"></div>

    </div>
</div>
<div class="bloco_acoes">
	<button type="submit" id="bt_pesquisar" tabindex="11" <?php if ($this->view->processoExecutando) echo 'disabled="disabled"';?> >Pesquisar</button>
	<button type="button" id="bt_diferenca" tabindex="12" <?php if ($this->view->processoExecutando) echo 'disabled="disabled"';?> >Diferença de Baixa</button>
</div>
<div class="separador"></div>