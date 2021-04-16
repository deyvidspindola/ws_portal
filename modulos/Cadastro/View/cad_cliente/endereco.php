<div class="bloco_titulo">Endereço</div>
<form action="" method="post" id="cad_endereco">
	<input type="hidden" name="acao" id="acao" value="setEndereco">
	<input type="hidden" name="clioid" id="clioid" value="<?php echo $this->clioid; ?>">
    <input type="hidden" name="endoid" id="endoid" value="">
<div class="bloco_conteudo">
	<div class="conteudo" style="position:relative;min-height:50px">
		<ul class="links bloco_opcoes">
			<li class="ativo"><a class="link_box" href="#enderecoprincipal">Principal</a></li>
			<li><a class="link_box" href="#enderecocobranca">Cobrança</a></li>
			<li><a class="link_box" href="#enderecoentrega">Entrega</a></li>
		</ul>
		<div class="carregando" id="endereco_load" style="left: 50%;margin-left: -60px;position: absolute;top: 0;width: 60px;display:none"></div>
		<div id="enderecoprincipal" class="conteudo_validacao box conteudo">
			<div class="chamada_correios">
				<div class="campo medio">
					<label for="clicep_res">CEP *: </label>
					<input type="text" id="clicep_res" name="clicep_res" maxlength="8" value="<?php echo $this->getEndereco['clicep_res'];?>" class="correios_cep campo obrigatorio valida_cep"/>
					<img src="images/progress4.gif" class="loading_cep loader">
					<button class="duvidas_cep" type="button" style="float:right" onclick="javascript:return false;">?</button>
				</div>
				<div class="clear"></div>
				<div class="campo semresultado">
						
				</div>
				<div class="clear"></div>
				<div class="campo maior descricao_duvidas">
						<strong>Ajuda para o preenchimento do Endereço</strong>
						<ul>
							<li>
							Se inserido um CEP válido, serão preenchidos automaticamente os campos para o devido endereço.
							<br>
							</li>
							<li>
							Se inserido um CEP inválido estará disponível a pesquisa pelo logradouro.
							<br>
							</li>
							<li>Caso não encontrado nenhum resultado, estará disponível para inserir manualmente o endereço. <br/></li>
							<li>
							<a onclick="javascript:window.open('http://www.correios.com.br/servicos/cep/cep_loc_log.cfm')" href="#">Clique aqui</a>
							para consultar o site dos correios.
							</li>
						</ul>
						<div style="text-align: right;">
							<a class="descricao_fechar" href=" javascript: void(null);">X Fechar</a>
						</div>
				</div>
				<div class="clear"></div>
				<div class="campo maior">
					<label for="cliuf_res">Estado *:</label>
						
							<select name="cliuf_res" class="correios_estado obrigatorio">
								<option value="">Selecione</option>
								<?php 
								$estado = $this->getEstado();
								foreach ($estado as $chave => $valor) { ?>
									<option value="<?php echo $valor?>" <?php echo ($this->getEndereco['cliuf_res'] == $valor) ? 'selected' : '' ;?>><?php echo $valor ?></option>
								<?php }	?>
							</select>
				</div>
				<div class="clear"></div>
				<div class="campo maior">
					<label for="clicidade_res">Cidade *:</label>
					<input type="text" value="<?php echo $this->getEndereco['clicidade_res'];?>" name="clicidade_res" class="correios_cidade campo obrigatorio" readonly/>
				</div>
				<div class="clear"></div>
				<div class="campo maior">
					<label for="clibairro_res">Bairro *:</label>
					<input type="text" value="<?php echo $this->getEndereco['clibairro_res'];?>" name="clibairro_res" class="correios_bairro campo obrigatorio" readonly/>
				</div>
				<div class="clear"></div>
				<div class="campo maior">
					<label for="clirua_res">Endereço *: </label>
					<input type="text" id="clirua_res" name="clirua_res" value="<?php echo $this->getEndereco['clirua_res'];?>" class="correios_endereco campo obrigatorio" readonly maxlength="60"/>
				</div>
			<div class="clear"></div>
			<div class="campo menor">
				<label for="clino_res">Número *: </label>
				<input type="text" id="clino_res" name="clino_res" value="<?php echo $this->getEndereco['clino_res'];?>" class="campo correios_numero camponum obrigatorio"  maxlength="7"/>
			</div>
			<div class="clear"></div>		
			<div class="campo maior">
				<label for="clicompl_res">Complemento: </label>
				<input type="text" id="clicompl_res" name="clicompl_res" value="<?php echo $this->getEndereco['clicompl_res'];?>" class="correios_complemento campo" />
			</div>

			</div>
			<div class="clear"></div>
            <?php 
                // TELEFONES
                if($this->tipoPessoa['clitipo'] == 'J') {
            ?>
			<div class="campo">
				<label for="clifone_res">Fone *: </label>
				<input type="text" id="clifone_com" name="clifone_com" value="<?php echo $this->getEndereco['clifone_com'];?>" size="15" class="campo obrigatorio telefone" maxlength='15'/>
			</div>
			<div class="clear"></div>	
			<div class="campo">
				<label for="clifone_com">Fone 2: </label>
				<input type="text" id="clifone2_com" name="clifone2_com" value="<?php echo $this->getEndereco['clifone2_com'];?>" size="15" class="campo telefone" maxlength='15'/>
			</div>
			<div class="clear"></div>	
			
			<div class="campo">
				<label for="clifone_com">Fone 3: </label>
				<input type="text" id="clifone3_com" name="clifone3_com" value="<?php echo $this->getEndereco['clifone3_com'];?>" size="15" class="campo telefone" maxlength='15'/>
			</div>
			<div class="clear"></div>	

			<div class="campo">
				<label for="clifone_cel">Fone 4: </label>
				<input type="text" id="clifone4_com" name="clifone4_com" value="<?php echo $this->getEndereco['clifone4_com'];?>" size="15" class="campo telefone" maxlength='15'/>
			</div>
			<div class="clear"></div>	
			<?php 
			    }else{
			?>
			    
			<div class="campo">
				<label for="clifone_res">Fone Residencial *: </label>
				<input type="text" id="clifone_res" name="clifone_res" value="<?php echo $this->getEndereco['clifone_res'];?>" size="15" class="campo obrigatorio telefone" maxlength='15'/>
			</div>
			<div class="clear"></div>	
			<div class="campo">
				<label for="clifone_cel">Fone Celular: </label>
				<input type="text" id="clifone_cel" name="clifone_cel" value="<?php echo $this->getEndereco['clifone_cel'];?>" size="15" class="campo telefone" maxlength='15'/>
			</div>
			<div class="clear"></div>	
			
			<div class="campo">
				<label for="clifone_com">Fone Comercial: </label>
				<input type="text" id="clifone_com" name="clifone_com" value="<?php echo $this->getEndereco['clifone_com'];?>" size="15" class="campo telefone" maxlength='15'/>
			</div>
			<div class="clear"></div>	
			<?php 
			    }
			    // FIM TELEFONES
			?>
			<div class="campo maior">
				<label for="cliemail">Email *: </label>
				<input type="text" id="cliemail" name="cliemail" value="<?php echo $this->getEndereco['cliemail'];?>" class="campo obrigatorio valida_email" />
			</div>
			<div class="clear"></div>			
			<div class="campo maior">
				<label for="cliemail_nfe">Email NFE *: </label>
				<input type="text" id="cliemail_nfe" name="cliemail_nfe" value="<?php echo $this->getEndereco['cliemail_nfe'];?>" class="campo obrigatorio valida_email" />
			</div>
			<div class="clear"></div>			
			<div class="campo maior">
				<label for="clino_correspondente">Correspondências A/C: </label>
				<input type="text" id="clino_correspondente" name="clino_correspondente" value="<?php echo $this->getEndereco['clicorrespondencia'];?>" class="campo" />
			</div>
			<div class="clear"></div>	
			<div class="campo maior">
				<label for="clino_observacoes">Observações: </label>
				<textarea rows="7" name="clino_observacoes"><?php echo $this->getEndereco['cliobservacao'];?></textarea>
			</div>
			<div class="clear"></div>
			<div class="campo maior">
				<label for="anexo_comprovante_residencia">Anexo Comprovante Endereço: </label>
				<input type="file" name="anexo_comprovante_residencia" id="anexo_comprovante_residencia" class="anexo_comprovante_residencia">
				<br/><img class="loadingtipo" style="display: none;" src="images/progress4.gif">
				<p class="arquivoresposta"><?php echo ($this->getEndereco['clicomprovante_endereco'] != '') ? '<span class="td_acao_excluir"><a href="javascript:return false;" class="excluirAnexo"  clioid="'.$this->clioid.'"" type="button"><img src="images/icon_error.png" /></a></span><a href="?acao=downloadAnexoComprovanteEndereco&clioid='.$this->clioid.'" target="_blank" >'.$this->getEndereco['clicomprovante_endereco'].'</a>' : '';?></p>
			</div>
			<div class="clear"></div>
	</div>		

		<div id="enderecocobranca" style="display:none" class="conteudo_validacao box conteudo">
			<div class="chamada_correios">
				<fieldset class="maior">
					<legend>Copiar do Endereço Principal</legend>
					<input type="checkbox" id="copiarPrincipal" name="copiarPrincipal" value="" class="" />
					<label for="copiarPrincipal" class="clear" style="display:inline">Sim</label>
				</fieldset>
				<div class="clear"></div>
				<div class="campo medio">
					<label for="endcep">CEP: </label>
					<input type="text" id="endcep" name="endcep" maxlength="8" value="<?php echo $this->getEndereco['endcep'];?>" class="correios_cep campo obrigatorio valida_cep" style="width:108px"/>
					<img src="images/progress4.gif" class="loading_cep loader">
					<button class="duvidas_cep" type="button" style="float:right" onclick="javascript:return false;">?</button>
				</div>
				<div class="clear"></div>
				<div class="campo semresultado">
						
				</div>
				<div class="clear"></div>
				<div class="campo maior descricao_duvidas">
						<strong>Ajuda para o preenchimento do Endereço</strong>
						<ul>
							<li>
							Se inserido um CEP válido, serão preenchidos automaticamente os campos para o devido endereço.
							<br>
							</li>
							<li>
							Se inserido um CEP inválido estará disponível a pesquisa pelo logradouro.
							<br>
							</li>
							<li>Caso não encontrado nenhum resultado, estará disponível para inserir manualmente o endereço. <br/></li>
							<li>
							<a onclick="javascript:window.open('http://www.correios.com.br/servicos/cep/cep_loc_log.cfm')" href="#">Clique aqui</a>
							para consultar o site dos correios.
							</li>
						</ul>
						<div style="text-align: right;">
							<a class="descricao_fechar" href=" javascript: void(null);">X Fechar</a>
						</div>
				</div>
				<div class="clear"></div>
				<div class="campo maior">

					<label for="enduf">Estado:</label>
					
						<select name="enduf" class="obrigatorio correios_estado">
							<option value="">Selecione</option>
							<?php 
							$estado = $this->getEstado();
							foreach ($estado as $chave => $valor) { ?>
								<option value="<?php echo $valor?>" <?php echo ($this->getEndereco['enduf'] == $valor) ? 'selected' : '' ;?>><?php echo $valor ?></option>
							<?php }	?>
						</select>
				</div>
				<div class="clear"></div>
				<div class="campo maior">
					<label for="endcidade">Cidade:</label>
					<input type="text" value="<?php echo $this->getEndereco['endcidade'];?>" name="endcidade" class="obrigatorio correios_cidade campo readonly"  readonly/>
				</div>
				<div class="clear"></div>
				<div class="campo maior">
					<label for="endbairro">Bairro:</label>
					<input type="text" value="<?php echo $this->getEndereco['endbairro'];?>" name="endbairro" class="obrigatorio correios_bairro campo readonly"  readonly/>
				</div>
				<div class="clear"></div>
				<div class="campo maior">
					<label for="endlogradouro">Endereço: </label>
					<input type="text" id="endlogradouro" name="endlogradouro" value="<?php echo $this->getEndereco['endlogradouro'];?>" class="obrigatorio correios_endereco campo"  readonly/>
					</div>
				<div class="clear"></div>
				<div class="campo menor">
					<label for="endno_numero">Número: </label>
					<input type="text" id="endno_numero" name="endno_numero" value="<?php echo $this->getEndereco['endno_numero'];?>" class="obrigatorio correios_numero campo camponum "  maxlength="7"/>
				</div>
				<div class="clear"></div>		
				<div class="campo maior">
					<label for="endcomplemento">Complemento: </label>
					<input type="text" id="endcomplemento" name="endcomplemento" value="<?php echo $this->getEndereco['endcomplemento'];?>" class="correios_complemento campo" />
				</div>
			</div>

			<div class="clear"></div>
		</div>		
		
		<div id="enderecoentrega" style="display:none" class="conteudo_validacao box conteudo">
				<div class="campo medio">
					<table>
						<tr>
							<td>
								<label for="copiarDe">Copiar de:</label>
								<select name="copiarDe" id="copiarDe">
										<option value="">Selecione</option>
										<option value="enderecoprincipal">Endereço Principal</option>
										<option value="enderecocobranca">Endereço Cobrança</option>
								</select>
							</td>
<!--							
							<td>
								<label for="copiarPara">Para:</label>
								<select name="copiarPara" id="copiarPara" class="clear">
										<option value="favorito1">Favorito 1</option>
										<option value="favorito2">Favorito 2</option>
										<option value="favorito3">Favorito 3</option>
								</select>
							</td>
							<td class="buttonCopiar">
								<button type="button" value="Copiar" id="copiarDePara" name="copiarDePara" onclick="javascript:return false;">Copiar</button>
							</td>
-->							
						</tr>
					</table>
				</div>			

				<div class="clear"></div>
			<fieldset id="enderecoEntregaFavorito">
			<legend>Endereço de Entrega</legend>
			<div class="chamada_correios">
				<div class="campo medio">
					<label for="entrega_no_cep">CEP: </label>
					<input type="text" name="entrega_no_cep" maxlength="8" value="" class=" valida_cep correios_cep campo <?php echo (count($this->enderecosEntrega) == 0) ? 'obrigatorio' : 'obrigatorio_todos' ?>" style="width:108px"/>
					<img src="images/progress4.gif" class="loading_cep loader">
					<button class="duvidas_cep" type="button" style="float:right" onclick="javascript:return false;">?</button>
				</div>
				<div class="clear"></div>
				<div class="campo semresultado">
						
				</div>
				<div class="clear"></div>
				<div class="campo maior descricao_duvidas">
						<strong>Ajuda para o preenchimento do Endereço</strong>
						<ul>
							<li>
							Se inserido um CEP válido, serão preenchidos automaticamente os campos para o devido endereço.
							<br>
							</li>
							<li>
							Se inserido um CEP inválido estará disponível a pesquisa pelo logradouro.
							<br>
							</li>
							<li>Caso não encontrado nenhum resultado, estará disponível para inserir manualmente o endereço. <br/></li>
							<li>
							<a onclick="javascript:window.open('http://www.correios.com.br/servicos/cep/cep_loc_log.cfm')" href="#">Clique aqui</a>
							para consultar o site dos correios.
							</li>
						</ul>
						<div style="text-align: right;">
							<a class="descricao_fechar" href=" javascript: void(null);">X Fechar</a>
						</div>
				</div>
				<div class="clear"></div>
				<div class="campo maior">
					<label for="entrega_uf">Estado:</label>
					<select name="entrega_uf" class="correios_estado <?php echo (count($this->enderecosEntrega) == 0) ? 'obrigatorio' : 'obrigatorio_todos' ?>">
						<option value="">Selecione</option>
						<?php 
						$estado = $this->getEstado();
						foreach ($estado as $chave => $valor) { ?>
							<option value="<?php echo $valor?>"><?php echo $valor ?></option>
						<?php }	?>
					</select>
				</div>
				<div class="clear"></div>
				<div class="campo maior">
					<label for="entrega_cidade">Cidade:</label>
					<input type="text" name="entrega_cidade" class="desabilitado correios_cidade campo  <?php echo (count($this->enderecosEntrega) == 0) ? 'obrigatorio' : 'obrigatorio_todos' ?>" readonly/>
				</div>
				<div class="clear"></div>
				<div class="campo maior">
					<label for="entrega_bairro">Bairro:</label>
					<input type="text" name="entrega_bairro" class="desabilitado correios_bairro campo <?php echo (count($this->enderecosEntrega) == 0) ? 'obrigatorio' : 'obrigatorio_todos' ?>" readonly/>
				</div>
				<div class="clear"></div>
				<div class="campo maior">
					<label for="entrega_logradouro">Endereço: </label>
					<input type="text" name="entrega_logradouro" value="" class="desabilitado correios_endereco campo <?php echo (count($this->enderecosEntrega) == 0) ? 'obrigatorio' : 'obrigatorio_todos' ?>" />
				</div>
				<div class="clear"></div>
				<div class="campo menor">
					<label for="entrega_numero">Número: </label>
					<input type="text" name="entrega_numero" value="" class="camponum correios_numero campo <?php echo (count($this->enderecosEntrega) == 0) ? 'obrigatorio' : 'obrigatorio_todos' ?>"  maxlength="7"/>
				</div>
				<div class="clear"></div>		
				<div class="campo maior">
					<label for="entrega_complemento">Complemento: </label>
					<input type="text" name="entrega_complemento" value="" class="correios_complemento campo" />
				</div>
			</div>

				<div class="clear"></div>	
		</fieldset>

<!-- grid endereço de entrega -->
<div class="separador"></div>
<div class="bloco_titulo" style="margin:0">Endereços de Entrega</div>
<div class="bloco_conteudo" style="margin:0">
    <div class="listagem">
        <table>
            <thead>
                <tr>
                    <th>CEP</th>
                    <th>Estado</th>
                    <th>Cidade</th>
                    <th>Bairro</th>
                    <th>Endereço</th>
                    <th>Número</th>
                    <th>Complemento</th>
                    <th>Excluir</th>
                </tr>
            </thead>
            <tbody>
            <?php if(count($this->enderecosEntrega) > 0) :?>
	            <?php foreach($this->enderecosEntrega as $endereco) :
		            	 ?>
					<tr <?php if ($cor != '') { ?> class="<?=$cor?>" <?php } ?>>
	                    <td class="entrega_cep td_acao_link"><a href="#enderecoEntregaFavorito" endoid="<?php echo $endereco['endoid']?>"><?php echo $endereco['endcep']?></a></td>
	                    <td class="entrega_estado"><?php echo $endereco['enduf']?></td>
	                    <td class="entrega_cidade"><?php echo $endereco['endcidade']?></td>
	                    <td class="entrega_bairro"><?php echo $endereco['endbairro']?></td>
	                    <td class="entrega_endereco"><?php echo $endereco['endlogradouro']?></td>
	                    <td class="entrega_numero"><?php echo $endereco['endno_numero']?></td>
	                    <td class="entrega_complemento"><?php echo $endereco['endcomplemento']?></td>
	                    <td align="center" class="td_acao_excluir">
	                    	<a href="javascript:void(0);" endoid="<?php echo $endereco['endoid']?>" class="excluirEndereco"><img src="images/icon_error.png" /></a>
	                    </td>
	                </tr>
	            <?php  
	               $cor = ($cor=="par") ? "" : "par"; 
	               
	               endforeach;?>
            <?php endif;?>
            </tbody>
        </table>
    </div>
</div>
	<div class="bloco_acoes" style="margin:0"><p><?php echo $this->getMensagemTotalRegistros(count($this->enderecosEntrega));?></p></div>
<!-- fim grid endereco de entrega -->





		</div>
	</div>
</div>
	<div class="bloco_acoes">
		<button type="submit" value="Confirmar" class="validacao valida_invisivel" id="buttonConfirmarEndereco" onclick="javascript:return false;" name="buttonConfirmarEndereco" >Confirmar</button>
		<button type="button" value="Voltar" id="buttonVoltar" name="buttonVoltar" onclick="window.location.href='<?php echo str_replace('/', '', strrchr($_SERVER['SCRIPT_NAME'], '/'));?>'">Voltar</button>
	    <? if($this->retCliente!=""){ ?>
    		<button type="button" value="Voltar" id="buttonVoltar" name="buttonVoltar" onclick="window.location.href='<?=trata_retorno($this->retCliente,$clioid)?>'">Retornar ao Contrato</button>
        <? } ?>
	</div>
</form>