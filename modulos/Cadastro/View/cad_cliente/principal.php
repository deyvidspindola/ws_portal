<div class="bloco_titulo">Principal</div>

<form action="" name="cad_cliente_principal" id="cad_cliente_principal" method="post">
    <input type="hidden" name="acao" id="acao" value="setPrincipal" />
    <input type="hidden" name="clioid" id="clioid" value="<?php echo $this->clioid; ?>" />
	<div class="bloco_conteudo">
		<div class="conteudo">
		
			<div class="campo medio">
				<label for="clitipo">Tipo Pessoa *: </label>
				
				<?php if (empty($this->clioid)){?>
			
					<select id="clitipo" name="clitipo" class="obrigatorio">
						<option value="">Selecione</option>
						<option value="F" <?=(isset($_POST['clitipo']) && $_POST['clitipo'] == "F") ? "selected = 'selected'" : ''?>>Física</option>
						<option value="J" <?=(isset($_POST['clitipo']) && $_POST['clitipo'] == "J") ? "selected = 'selected'" : ''?>>Jurídica</option>
				    </select>
			
				<?php }else{?>
					
					<input type="hidden" name="clitipo" id="clitipo" value=<?php echo $_POST['clitipo']?> />	
					<p><?=(isset($_POST['clitipo']) && $_POST['clitipo'] == "F") ? "FÍSICA" : "JURÍDICA"?><p>
				
				<?php }?>
				
				
			</div>
			
			<div class="campo medio">
				<label for="clicloid">Classe: </label>
				<select id="clicloid" name="clicloid" >
					<option value="">Selecione</option>
					
					<?php foreach($resultadoPesquisa['comboClassesCliente'] as $classe): 
					
					$sel = (isset($_POST['clicloid']) && $_POST['clicloid'] == $classe['clicloid']) ? "selected = 'selected'" : "";?>
					<option value="<?php echo $classe['clicloid']; ?>" <?php echo $sel; ?>><?php echo utf8_decode($classe['clicldescricao']); ?></option>
	                
	                <?php endforeach; ?> 
	                
				</select>
			</div>
				 				 
			<div class="clear" ></div>
			 
		 	<div id="camposPessoaFisica">
			 	<div class="campo medio">					
					<label for="clino_cpf">CPF *: </label>
					<input type="text" id="clino_cpf" name="clino_cpf" value="<?=(isset($_POST['clino_cpf']) && $_POST['clino_cpf'] != "")?$this->getCPF($_POST['clino_cpf']):''?>" class="campo obrigatorio" />
				</div>
			
				<div class="clear" ></div>
					
				<div class="campo maior">
					<label for="clinome">Cliente *: </label>
					<input type="text" id="clinome" name="clinome" value="<?=(isset($_POST['clinome']) && $_POST['clinome'] != "")?$_POST['clinome']:''?>" class="campo obrigatorio alfanumCar" />
				</div>
					 
				 <div class="clear" ></div>
				
				<div class="campo medio">
					<label for="clirg">RG *: </label>
					<input type="text" id="clirg" name="clirg" value="<?=(isset($_POST['clirg']) && $_POST['clirg'] != "")?$_POST['clirg']:''?>" class="campo obrigatorio" />
				</div>
				
				<div class="campo menor">
					<label for="cliemissor_rg">Órgão Emissor *: </label>
					<input type="text" id="cliemissor_rg" name="cliemissor_rg" value="<?=(isset($_POST['cliemissor_rg']) && $_POST['cliemissor_rg'] != "")?$_POST['cliemissor_rg']:''?>" class="campo obrigatorio alfanum" maxlength="6" />
				</div>
				 
				<div class="campo">
					<label for="cliuf_emiss">UF *: </label>
					<select id="cliuf_emiss" name="cliuf_emiss" class="obrigatorio" style="margin-top: 2px;">
						<option value="">Selecione</option>
						
						<?php foreach($resultadoPesquisa['arrayUF'] as $uf): 
						
						$sel = (isset($_POST['cliuf_emiss']) && $_POST['cliuf_emiss'] == $uf) ? "selected = 'selected'" : "";?>
						<option value="<?php echo $uf; ?>" <?php echo $sel; ?>><?php echo utf8_decode($uf); ?></option>
		                
		                <?php endforeach; ?> 
		                
					</select> 
				</div>
				
				<div class="campo data">
					<label for="clidt_emissao_rg">Data Emissão *: </label>				
					<input type="text" id="clidt_emissao_rg" name="clidt_emissao_rg" value="<?=(isset($_POST['clidt_emissao_rg']) && $_POST['clidt_emissao_rg'] != "")?$_POST['clidt_emissao_rg']:''?>"  class="campo obrigatorio" >
				</div>
						 
				<div class="clear" ></div>			
				
				<div class="campo data">
					<label for="clidt_nascimento">Data Nasc. *: </label>				
					<input type="text" id="clidt_nascimento" name="clidt_nascimento" value="<?=(isset($_POST['clidt_nascimento']) && $_POST['clidt_nascimento'] != "")?$_POST['clidt_nascimento']:''?>" class="campo obrigatorio" >
				</div>
						 
				<div class="clear" ></div>
					
				<div class="campo maior">
					<label for="clinaturalidade">Naturalidade *: </label>
					<input type="text" id="clinaturalidade" name="clinaturalidade" value="<?=(isset($_POST['clinaturalidade']) && $_POST['clinaturalidade'] != "")?$_POST['clinaturalidade']:''?>" class="campo obrigatorio alfanum" maxlength="65"/>
				</div>
			
				<div class="clear" ></div>
					
				<div class="campo maior">
					<label for="clipai">Pai: </label>
					<input type="text" id="clipai" name="clipai" value="<?=(isset($_POST['clipai']) && $_POST['clipai'] != "")?$_POST['clipai']:''?>" class="campo alfanumCar" maxlength="60"/>
				</div>
			
				<div class="clear" ></div>
					
				<div class="campo maior">
					<label for="climae">Mãe *: </label>
					<input type="text" id="climae" name="climae" value="<?=(isset($_POST['climae']) && $_POST['climae'] != "")?$_POST['climae']:''?>" class="campo obrigatorio alfanumCar"  maxlength="60"/>
				</div>
			
				<div class="clear" ></div>
				
				<div class="campo medio">
					<label for="clisexo">Sexo *: </label>
					<select id="clisexo" name="clisexo" class="obrigatorio">
						<option value="">Selecione</option>
						<option value="M" <?=(isset($_POST['clisexo']) && $_POST['clisexo'] == "M") ? "selected = 'selected'" : ''?>>Masculino</option>
						<option value="F" <?=(isset($_POST['clisexo']) && $_POST['clisexo'] == "F") ? "selected = 'selected'" : ''?>>Feminino</option>
					</select>
				 </div>
			
				<div class="clear" ></div>
				
				<div class="campo medio">
					<label for="cliestado_civil">Estado Civil *: </label>
					<select id="cliestado_civil" name="cliestado_civil" class="obrigatorio">
						<option value="">Selecione</option>
						<option value="S" <?=(isset($_POST['cliestado_civil']) && $_POST['cliestado_civil'] == "S") ? "selected = 'selected'" : ''?>>Solteiro(a)</option>
	                	<option value="C" <?=(isset($_POST['cliestado_civil']) && $_POST['cliestado_civil'] == "C") ? "selected = 'selected'" : ''?>>Casado(a)</option>
	                	<option value="V" <?=(isset($_POST['cliestado_civil']) && $_POST['cliestado_civil'] == "V") ? "selected = 'selected'" : ''?>>Vi&uacute;vo(a)</option>
	                	<option value="D" <?=(isset($_POST['cliestado_civil']) && $_POST['cliestado_civil'] == "D") ? "selected = 'selected'" : ''?>>Divorciado(a)</option>
	                	<option value="A" <?=(isset($_POST['cliestado_civil']) && $_POST['cliestado_civil'] == "A") ? "selected = 'selected'" : ''?>>Amasiado(a)</option>
	                	<option value="O" <?=(isset($_POST['cliestado_civil']) && $_POST['cliestado_civil'] == "O") ? "selected = 'selected'" : ''?>>Outro</option>
					</select>
				 </div>
			 </div>
		 
		 	<div id="camposPessoaJuridica">				
				<div class="campo medio">				
					<label for="clino_cgc">CNPJ *: </label>
					<input type="text" id="clino_cgc" name="clino_cgc" value="<?=(isset($_POST['clino_cgc']) && $_POST['clino_cgc'] != "")?$this->getCNPJ($_POST['clino_cgc']):''?>" class="campo obrigatorio" />
				</div>
			
				<div class="clear" ></div>
					
				<div class="campo maior">
					<label for="clinomePJ">Cliente *: </label>
					<input type="text" id="clinomePJ" name="clinomePJ" value="<?=(isset($_POST['clinome']) && $_POST['clinome'] != "")?$_POST['clinome']:''?>" class="campo obrigatorio" />
				</div>
							
				<div class="clear" ></div>
					
				<div class="campo menor">
					<label for="clireg_simples">Optante Simples *: </label>
					<select id="clireg_simples" name="clireg_simples" class="obrigatorio">
						<option value="">Selecione</option>
						<option value="S" <?=(isset($_POST['clireg_simples']) && $_POST['clireg_simples'] == "S") ? "selected = 'selected'" : ''?>>Sim</option>
						<option value="N" <?=(isset($_POST['clireg_simples']) && $_POST['clireg_simples'] == "N") ? "selected = 'selected'" : ''?>>Não</option>
					</select>
				</div>				 
							
				<div class="clear" ></div>
				
				<div class="campo medio">
					<label for="cliinscr">Inscrição Estadual: </label>
					<input type="text" id="cliinscr" name="cliinscr" value="<?=(isset($_POST['cliinscr']) && $_POST['cliinscr'] != "")?$_POST['cliinscr']:''?>" class="campo" maxlength="20"/>
				</div>
				 
				<div class="campo menor">
					<label for="cliuf_inscr">UF: </label>
					<select id="cliuf_inscr" name="cliuf_inscr" >
						<option value="">Selecione</option>
						
						<?php foreach($resultadoPesquisa['arrayUF'] as $uf): 
						
						$sel = (isset($_POST['cliuf_inscr']) && $_POST['cliuf_inscr'] == $uf) ? "selected = 'selected'" : "";?>
						<option value="<?php echo $uf; ?>" <?php echo $sel; ?>><?php echo utf8_decode($uf); ?></option>
		                
		                <?php endforeach; ?> 
		                
					</select> 
				</div>	 
							
				<div class="clear" ></div>
				
				<div class="campo medio">
					<label for="cliinscr_municipal">Inscrição Municipal: </label>
					<input type="text" id="cliinscr_municipal" name="cliinscr_municipal" value="<?=(isset($_POST['cliinscr_municipal']) && $_POST['cliinscr_municipal'] != "")?$_POST['cliinscr_municipal']:''?>" class="campo" />
				</div>
				 
				<div class="clear" ></div>
				
				<div class="campo medio">
					<label for="cliinscr_municipal">Código CNAE: </label>
					<input type="text" id="clicnae" name="clicnae" value="<?=(isset($_POST['clicnae']) && $_POST['clicnae'] != "")?$_POST['clicnae']:''?>" class="campo"  />
				</div>
                                
				<div class="clear" ></div>
				
				<div class="campo data">
					<label for="clidt_fundacao">Data de Fundação: </label>				
					<input type="text" id="clidt_fundacao" name="clidt_fundacao" value="<?=(isset($_POST['clidt_fundacao']) && $_POST['clidt_fundacao'] != "")?$_POST['clidt_fundacao']:''?>" class="campo" >
				</div>
			 </div>			
		
			<div class="clear" ></div>
			
						
		</div>
	</div>
	<div class="bloco_acoes">
		<button type="submit" value="Confirmar" id="buttonConfirmarPrincipal" name="buttonConfirmarPrincipal" onclick="javascript:return false;" class="validacao">Confirmar</button>
		<button type="button" value="Voltar" id="buttonVoltar" name="buttonVoltar" onclick="window.location.href='<?php echo str_replace('/', '', strrchr($_SERVER['SCRIPT_NAME'], '/'));?>'">Voltar</button>
	    <? if($this->retCliente!=""){ ?>
	        <button type="button" value="Voltar" id="buttonVoltar" name="buttonVoltar" onclick="window.location.href='<?=trata_retorno($this->retCliente,$clioid)?>'">Retornar ao Contrato</button>
	    <? } ?>
	</div>
</form>