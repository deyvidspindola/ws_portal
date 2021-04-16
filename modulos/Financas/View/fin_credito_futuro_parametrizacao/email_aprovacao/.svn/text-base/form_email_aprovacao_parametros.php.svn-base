    <div class="separador"></div>    
    <div id="infoParametrizacao" class="mensagem info">
    		IMPORTANTE: O corpo do e-mail deve conter o texto [VALOR], [PERCENTUAL] e [QTD.PARCELAS] entre colchetes
			e em maiúsculo.<br/> No momento do envio do e-mail, o sistema substituirá, automaticamente, o texto
			pelo valor parametrizado.
	</div>
	
	<?php if (isset($this->mensagemSucesso)) : ?>
			<div class="mensagem sucesso"><?php echo $this->mensagemSucesso; ?></div>
	<?php endif; ?>
	
	<?php if (isset($this->mensagemErro)) : ?>
			<div class="mensagem erro"><?php echo $this->mensagemErro; ?></div>
	<?php endif; ?>
	
	<?php if (isset($this->mensagemAlerta)) : ?>
			<div class="mensagem alerta"><?php echo $this->mensagemAlerta; ?></div>
	<?php endif; ?>
	
	
	
	
    <div class="bloco_titulo">Parametrização</div>
    <div class="bloco_conteudo">
    	<div class="formulario">
    	
	    	<div class="campo">
	            <label>Será enviado e-mail para a lista de responsáveis quando:</label>
	        </div>
	        
	        <div class="clear"></div>
	        
    		<div class="campo maior maiorCustomizado">
				<label class="labelCutomizada" for="cfeavalor_credito_futuro">O valor do crédito futuro for maior que: *</label> 
				<div class="sifrao">R$</div><input tabindex="3" id="cfeavalor_credito_futuro" maxlength="9" class="campo requerido campoCustomizado" name="cfeavalor_credito_futuro" type="text" value="<?php echo isset($this->atualParametros->cfeavalor_credito_futuro) && !empty($this->atualParametros->cfeavalor_credito_futuro) ? trim($this->atualParametros->cfeavalor_credito_futuro) : '' ?>">
				<div class="label-ou">ou</div>
			</div>
			
			<div class="clear"></div>
			
			<div class="campo maior maiorCustomizado">
				<label class="labelCutomizada" for="cfeavalor_percentual_desconto">O percentual de desconto do crédito futuro for maior que: *</label>
				<input id="cfeavalor_percentual_desconto" tabindex="4" maxlength="6"  class="campo requerido campoCustomizado" name="cfeavalor_percentual_desconto" type="text" value="<?php echo isset($this->atualParametros->cfeavalor_percentual_desconto) && !empty($this->atualParametros->cfeavalor_percentual_desconto) ? trim($this->atualParametros->cfeavalor_percentual_desconto) : '' ?>"><div class="percent">%</div>
				<div class="label-ou">ou</div>
			</div>
			
			<div class="clear"></div>
			
			<div class="campo maior maiorCustomizado">
				<label class="labelCutomizada" for="cfeaparcelas">A quantidade de parcelas do crédito futuro for maior que: *</label>
				<input tabindex="5" id="cfeaparcelas" class="campo requerido campoCustomizado" name="cfeaparcelas" type="text" value="<?php echo isset($this->atualParametros->cfeaparcelas) && !empty($this->atualParametros->cfeaparcelas) ? trim($this->atualParametros->cfeaparcelas) : '' ?>">
			</div>
			
			<div class="clear"></div>
			
			<fieldset class="maior">
				<legend>Obrigação Financeira de Desconto para:</legend>
				<div class="campo tamanhoCustomizado">
					<label for="cfeaobroid_contestacao">Contestação *</label>
						<select tabindex="6" id="cfeaobroid_contestacao" name="cfeaobroid_contestacao">
							<option value="">Selecione</option>
							<?php foreach ($this->opcoesObrigacaoFinanceira as $item) : ?>
								<?php
									$selected = isset($this->atualParametros->cfeaobroid_contestacao) && !empty($this->atualParametros->cfeaobroid_contestacao) && $this->atualParametros->cfeaobroid_contestacao == $item->obroid ? 'selected="selected"' :'';
								?>
								<option <?php echo $selected; ?> value="<?php echo $item->obroid; ?>"><?php echo $item->obroid.' - '.$item->obrobrigacao; ?></option>
							<?php endforeach; ?>
						</select>
				</div>
				<div class="clear"></div>
				<div class="campo tamanhoCustomizado">
					<label for="cfeaobroid_contas">Contas a Receber *</label>
						<select tabindex="7" id="cfeaobroid_contas" name="cfeaobroid_contas">
							<option value="">Selecione</option>
							<?php foreach ($this->opcoesObrigacaoFinanceira as $item) : ?>
								<?php
									$selected = isset($this->atualParametros->cfeaobroid_contas) && !empty($this->atualParametros->cfeaobroid_contas) && $this->atualParametros->cfeaobroid_contas == $item->obroid ? 'selected="selected"' :'';
								?>
								<option <?php echo $selected; ?> value="<?php echo $item->obroid; ?>"><?php echo $item->obroid.' - '.$item->obrobrigacao; ?></option>
							<?php endforeach; ?>
						</select>
				</div>
				<div class="clear"></div>
				<div class="campo tamanhoCustomizado">
					<label for="cfeaobroid_campanha">Campanha Promocional *</label>
						<select tabindex="8" id="cfeaobroid_campanha" name="cfeaobroid_campanha">
							<option value="">Selecione</option>
							<?php foreach ($this->opcoesObrigacaoFinanceira as $item) : ?>
								<?php
									$selected = isset($this->atualParametros->cfeaobroid_campanha) && !empty($this->atualParametros->cfeaobroid_campanha) && $this->atualParametros->cfeaobroid_campanha == $item->obroid ? 'selected="selected"' :'';
								?>
								<option <?php echo $selected; ?> value="<?php echo $item->obroid; ?>"><?php echo $item->obroid.' - '.$item->obrobrigacao; ?></option>
							<?php endforeach; ?>
						</select>
				</div>
				<div class="clear"></div>
			</fieldset>
			
			<div class="clear"></div>
			
			<div class="campo maior">
				<label for="cfeacabecalho">Cabeçalho *</label>
				<input tabindex="9" id="cfeacabecalho" class="campo requerido" name="cfeacabecalho" type="text" value="<?php echo isset($this->atualParametros->cfeacabecalho) && !empty($this->atualParametros->cfeacabecalho) ? trim($this->atualParametros->cfeacabecalho) : 'Crédito futuro' ?>">
			</div>
			
			<div class="clear"></div>
			
			<div class="campo maior">
				<label for="cfeacorpo">Corpo do E-mail *</label>
				<textarea tabindex="10" style="resize:none" id="cfeacorpo" class="requerido" name="cfeacorpo" rows="5"><?php echo isset($this->atualParametros->cfeacorpo) && !empty($this->atualParametros->cfeacorpo) ? trim($this->atualParametros->cfeacorpo) : 'Informo que foi registrado um crédito futuro de R$ [VALOR] ou de [PERCENTUAL] % ou de [QTD.PARCELAS].' ?>
				</textarea>
			</div>
			
			<div class="clear"></div>
    	</div>
	</div>
	
	
	<div class="bloco_acoes">
		<button tabindex="11" type="submit" id="confirmar">Confirmar</button>
	</div>
	
	<style>
	.tamanhoCustomizado select{
		width: 348px !important;
	}
	
	label.labelCutomizada{
    float: left;
    margin-top: 6px;
    width: 226px;
	}
	
	.maiorCustomizado input.campoCustomizado{
		float: right;
		width: 80px !important;
		float: none\9;
		position: absolute\9;
		right: 0px\9;
		top: 0px\9;     
		
	}
	
	.maiorCustomizado{
		width: 344px;
		position: relative;
	}
	
	div.label-ou{
		font-size: 12px;
	    position: absolute;
	    right: -38px;
	    top: 5px;
	    background: none !important;
	    border: 0 !important;
	    color: #000 !important;
	    width: 20px;
	}
	
	.percent{
		font-size: 12px;
	    position: absolute;
	    right: -16px;
	    top: 5px;
	    background: none !important;
	    border: 0 !important;
	    color: #000 !important;
	}
	
	.sifrao{
		font-size: 12px;
	    position: absolute;
	    right: 86px;
	    top: 5px;
	    background: none !important;
	    border: 0 !important;
	    color: #000 !important;
	    width: 20px;
	}
        
        #campo_nome span{
            width: auto !important;
        }
	</style>

	