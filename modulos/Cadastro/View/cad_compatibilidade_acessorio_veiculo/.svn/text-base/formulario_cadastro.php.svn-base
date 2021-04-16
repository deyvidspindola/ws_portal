<div class="bloco_titulo">Cadastro</div>
<div class="bloco_conteudo">
    <div class="formulario">

        <div class="campo medio">
            <label id="lbl_cavmcaoid" for="cavmcaoid">Marca Veículo *</label>
            <select id="cavmcaoid" name="cavmcaoid" tabindex="1">
				<option value="">Escolha</option>
				<?php foreach ($this->view->marcaList as $id=>$marca): ?>
					<option value="<?php echo $marca['mcaoid'] ?>" <?php echo ($this->view->parametros->cavmcaoid == $marca['mcaoid']) ? 'selected="selected"' : '' ?> >
						<?php echo $marca['mcamarca'] ?>
					</option>
				<?php endforeach;?>
            </select>
        </div>

        <div class="campo medio">
            <label id="lbl_cavmlooid" for="cavmlooid">Modelo Veículo *</label>
            <select id="cavmlooid" name="cavmlooid" tabindex="2">
                <option value="">Escolha uma marca</option>						
		        <?php if (isset($this->view->parametros->cavmcaoid) && $this->view->parametros->cavmcaoid != ''): ?>
		            <?php $modeloList = $this->dao->getModeloList($this->view->parametros->cavmcaoid); ?>
		            <?php foreach ($modeloList as $modelo): ?>
		                <option value="<?php echo $modelo['mlooid']; ?>" <?php echo ($this->view->parametros->cavmlooid == $modelo['mlooid']) ? 'selected="selected"' : '' ; ?>>
		                    <?php echo $modelo['mlomodelo'];?>
						</option>
		            <?php endforeach;?>
				<?php endif;?>
            </select>
        </div>

        <div class="campo menor">
            <label id="lbl_cavano" for="cavano">Ano *</label>
            <input id="cavano" class="campo" type="text" value="<?php echo $this->view->parametros->cavano; ?>" name="cavano" maxlength="4" tabindex="3">
        </div>

		<div class="clear"></div>

        <div class="campo medio">
            <label id="lbl_cavcbmooid" for="cavcbmooid">Modelo Acessório *</label>
            <select id="cavcbmooid" name="cavcbmooid" tabindex="4">
                <option value="">Escolha</option>
				<?php foreach ($this->view->modeloCBList as $id=>$modeloCB): ?>
					<option value="<?php echo $modeloCB['cbmooid'] ?>" <?php echo ($this->view->parametros->cavcbmooid == $modeloCB['cbmooid']) ? 'selected="selected"' : '' ?> >
						<?php echo $modeloCB['cbmodescricao'] ?>
					</option>
				<?php endforeach;?>
            </select>
        </div>

		<div class="clear"></div>
        
        <div class="campo medio">
            <label id="lbl_cavstatus" for="cavstatus">Status</label>
            <select id="cavstatus" name="cavstatus" tabindex="5">
                <option value="NULL"  <?php echo ($this->view->parametros->cavstatus == "NULL" ) ? 'selected="selected"' : '' ?>>Aguardando homologação</option>
                <option value="TRUE"  <?php echo ($this->view->parametros->cavstatus == "TRUE" ) ? 'selected="selected"' : '' ?>>Compatível</option>
                <option value="FALSE" <?php echo ($this->view->parametros->cavstatus == "FALSE") ? 'selected="selected"' : '' ?>>Incompatível</option>
            </select>
        </div>
        
		<div class="clear"></div>

    </div>
</div>

<div class="bloco_acoes">
    <button type="submit" id="bt_gravar" name="bt_gravar" value="gravar" tabindex="6">Confirmar</button>
    <button type="button" id="bt_voltar" tabindex="7">Voltar</button>
</div>