<div class="bloco_titulo">Dados Principais</div>
<div class="bloco_conteudo">
    <div class="formulario">
    	
        <div class="campo maior">
            <label id="lbl_token" for="token">Token * </label>
            <!--
            <input id="token" class="campo" type="text" value="<?php echo $this->view->parametros->token; ?>" name="token">
			-->
			<textarea id="token" name="token" class="campo" cols="80" rows="6"><?php echo $this->view->parametros->token; ?></textarea>
        </div>

		<div class="clear"></div>

		<div class="campo data periodo">
			<label id="lbl_dt_expiracao" for="dt_expiracao" style="margin-left: 0px;">Data Expiração * </label>
			<input type="text" name="dt_expiracao" id="dt_expiracao" value="<?php echo $this->view->parametros->dt_expiracao; ?>" class="campo" />
		</div>

		<div class="clear"></div>
                            
		<?php if(empty($this->view->parametros->tokenid)) : ?>
				
			<div class="cliente">
        		<?php $this->comp_cliente->render() ?>
			</div>

		<?php else : ?>
							
			<div class="dados_cliente">	
				<div class="campo maior">
					<label for="nome_cliente">Nome do Cliente *</label>
					<input class="campo disabled" disabled="disabled" type="text" id="nome_cliente" name="nome_cliente" size="50" maxlength="50" value="<?php echo $this->view->parametros->clinome; ?>" />
					<input type="hidden" id="cpx_valor_cliente_nome" name="cpx_valor_cliente_nome" value="<?php echo $this->view->parametros->clioid; ?>" />
				</div>

				<div>
					<fieldset style="width:180px; background: none; border: 1px solid silver;float:left; margin-top: 5px; margin-bottom: 5px;">
						<legend>Tipo Pessoa</legend>
						<label>
							<input type="radio" name="tipo_pessoa" value="F" <?php echo (isset($this->view->parametros->clitipo) && $this->view->parametros->clitipo == 'F' ? 'checked="checked"' : '') ;?> disabled="disabled" />                                  
							Física
						</label>
						<label>
							<input type="radio" name="tipo_pessoa" value="J" <?php echo (!isset($this->view->parametros->clitipo) || $this->view->parametros->clitipo == 'J') ? 'checked="checked"' : '' ;?> disabled="disabled" />                                  
							Jurídica
						</label>
					</fieldset>
					<input type="hidden" name="tipo_pessoa_literal" value="<?php echo isset($this->view->parametros->clitipo) ? $this->view->parametros->clitipo : 'J'; ?>" />
				</div>			
				<div class="campo medio">
					<label for="cpf_cnpj">
					<?php if ($this->view->parametros->clitipo == 'F'):?>
						CPF
					<?php else:?>
						CNPJ
					<?php endif;?>
					</label>
					<input class="campo <?php echo empty($this->view->parametros->clino_cpf) ? 'mask_cnpj' : 'mask_cpf'; ?> disabled" type="text" id="cpf_cnpj" name="cpf_cnpj" value="<?php echo empty($this->view->parametros->clino_cpf) ? formata_cgc_cpf($this->view->parametros->clino_cgc) : formata_cgc_cpf($this->view->parametros->clino_cpf); ?>" disabled="disabled" />
					<input type="hidden" id="cpf_cnpj_cliente" name="cpf_cnpj_cliente" value="<?php echo empty($this->view->parametros->clino_cpf) ? $this->view->parametros->clino_cgc : $this->view->parametros->clino_cpf;?>"/>
				</div>

			</div>
							 
		<?php endif; ?>

        
		<div class="clear"></div>

		<div class="campo maior">
			<label id="lbl_site_name" for="site_name" style="margin-left: 0px;">Site Name * </label>
			<input type="text" name="site_name" id="site_name" value="<?php echo $this->view->parametros->site_name; ?>" class="campo" />
		</div>
        
		<div class="clear"></div>

    </div>
</div>

<div class="bloco_acoes">
    <button type="button" id="bt_gravar" name="bt_gravar" value="gravar" <?php echo ($this->view->parametros->acao == 'cadastrar') ? 'class="disabled" disabled="disabled"' : ''; ?> >Confirmar</button>
    <button type="button" id="bt_voltar">Cancelar</button>
</div>
