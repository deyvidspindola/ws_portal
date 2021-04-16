<div class="bloco_titulo">Dados para Pesquisa</div>
<div class="bloco_conteudo">
    <div class="formulario">

        <div class="campo medio">
            <label id="lbl_cavmcaoid_busca" for="cavmcaoid_busca">Marca Veículo</label>
            <select id="cavmcaoid_busca" name="cavmcaoid_busca" tabindex="1">
				<option value="">Escolha</option>
				<?php foreach ($this->view->marcaList as $id=>$marca): ?>
					<option value="<?php echo $marca['mcaoid'] ?>" <?php echo ($this->view->parametros->cavmcaoid_busca == $marca['mcaoid']) ? 'selected="selected"' : '' ?> >
						<?php echo $marca['mcamarca'] ?>
					</option>
				<?php endforeach;?>
            </select>
        </div>

        <div class="campo medio">
            <label id="lbl_cavmlooid_busca" for="cavmlooid_busca">Modelo Veículo</label>
            <select id="cavmlooid_busca" name="cavmlooid_busca" tabindex="2">
                <option value="">Escolha uma marca</option>						
		        <?php if (isset($this->view->parametros->cavmcaoid_busca) && $this->view->parametros->cavmcaoid_busca != ''): ?>
		            <?php $modeloList = $this->dao->getModeloList($this->view->parametros->cavmcaoid_busca); ?>
		            <?php foreach ($modeloList as $modelo): ?>
		                <option value="<?php echo $modelo['mlooid']; ?>" <?php echo ($this->view->parametros->cavmlooid_busca == $modelo['mlooid']) ? 'selected="selected"' : '' ; ?>>
		                    <?php echo $modelo['mlomodelo'];?>
						</option>
		            <?php endforeach;?>
				<?php endif;?>
            </select>
        </div>

        <div class="campo menor">
            <label id="lbl_cavano_busca" for="cavano_busca">Ano</label>
            <input id="cavano_busca" class="campo" type="text" value="<?php echo $this->view->parametros->cavano_busca; ?>" name="cavano_busca" maxlength="4" tabindex="3">
        </div>

		<div class="clear"></div>

        <div class="campo medio">
            <label id="lbl_cavcbmooid_busca" for="cavcbmooid_busca">Modelo Acessório</label>
            <select id="cavcbmooid_busca" name="cavcbmooid_busca" tabindex="4">
                <option value="">Escolha</option>
				<?php foreach ($this->view->modeloCBList as $id=>$modeloCB): ?>
					<option value="<?php echo $modeloCB['cbmooid'] ?>" <?php echo ($this->view->parametros->cavcbmooid_busca == $modeloCB['cbmooid']) ? 'selected="selected"' : '' ?> >
						<?php echo $modeloCB['cbmodescricao'] ?>
					</option>
				<?php endforeach;?>
            </select>
        </div>

		<div class="clear"></div>
        
        <div class="campo medio">
            <label id="lbl_cavstatus_busca" for="cavstatus_busca">Status</label>
            <select id="cavstatus_busca" name="cavstatus_busca" tabindex="5">
                <option value="">Escolha</option>
                <option value="NULL"  <?php echo ($this->view->parametros->cavstatus_busca == "NULL" ) ? 'selected="selected"' : '' ?>>Aguardando homologação</option>
                <option value="TRUE"  <?php echo ($this->view->parametros->cavstatus_busca == "TRUE" ) ? 'selected="selected"' : '' ?>>Compatível</option>
                <option value="FALSE" <?php echo ($this->view->parametros->cavstatus_busca == "FALSE") ? 'selected="selected"' : '' ?>>Incompatível</option>
            </select>
        </div>
        
		<div class="clear"></div>

    </div>
</div>

<div class="bloco_acoes">
    <button type="submit" id="bt_pesquisar" tabindex="6">Pesquisar</button>
	<?php if ($_SESSION['funcao']['manter_compatibilidade_cav'] == 1): ?>
    <button type="button" id="bt_novo" tabindex="7">Novo</button>
    <?php endif; ?>
</div>







