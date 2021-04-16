<div class="bloco_titulo">Cobrança / Faturamento</div>

<form action="" name="forma_pagamento" id="forma_pagamento" method="post">
	<input type="hidden" name="acao" id="acao" value="setCobranca" />
	<input type="hidden" name="clifoid" id="clifoid" value="" />
	<input type="hidden" name="clioid" id="forma_pagamento_clioid" value="<?php echo $this->clioid; ?>" />
	<input type="hidden" name="depoid" id="depoid" value="<?php echo $_SESSION['usuario']['depoid'] ?>" />
	<input type="hidden" name="entrada" id="entrada" value="I" />
	<input type="hidden" name="origem_chamada" id="origem_chamada" value="CT" />
	<input type="hidden" name="paginaOrigem" id="paginaOrigem" value="cadclientenovo" />
	
	<!-- DADOS COBRANCA FATURAMENTO -->
	<input type="hidden" name="cobranca_email" id="cobranca_email" value="<?php echo $this->dadosCobranca['clientes']['cliemail']?>" />
	<input type="hidden" name="cobranca_email_nfe" id="cobranca_email_nfe" value="<?php echo $this->dadosCobranca['clientes']['cliemail_nfe']?>" />
	<input type="hidden" name="cobranca_cep" id="cobranca_cep" value="<?php echo $this->getCEP($this->dadosCobranca['clientes']['endcep'])?>" />
	<input type="hidden" name="cobranca_pais" id="cobranca_pais" value="<?php echo $this->dadosCobranca['clientes']['endpaisoid']?>" />
	<input type="hidden" name="cobranca_estado" id="cobranca_estado" value="<?php echo $this->dadosCobranca['clientes']['endestoid']?>" />
	<input type="hidden" name="cobranca_cidade" id="cobranca_cidade" value="<?php echo $this->dadosCobranca['clientes']['endcidade']?>" />
	<input type="hidden" name="cobranca_bairro" id="cobranca_bairro" value="<?php echo $this->dadosCobranca['clientes']['endbairro']?>" />
	<input type="hidden" name="cobranca_logradouro" id="cobranca_logradouro" value="<?php echo $this->dadosCobranca['clientes']['endlogradouro']?>" />
	<input type="hidden" name="cobranca_num" id="cobranca_num" value="<?php echo $this->dadosCobranca['clientes']['endno_numero']?>" />
	<input type="hidden" id="forma_pagamento_atual_forcdebito_conta" value="<?php echo $this->dadosCobranca['clientes']['forcdebito_conta']?>" />
	<input type="hidden" id="forma_pagamento_atual_forccobranca_cartao_credito" value="<?php echo $this->dadosCobranca['clientes']['forccobranca_cartao_credito']?>" />
	<input type="hidden" id="forcoid_atual" name="forcoid_atual" value="<?php echo $this->dadosCobranca['clientes']['forcoid']?>"/>
	<input type="hidden" id="cdvoid" name="cdvoid" value="<?php echo $this->dadosCobranca['clientes']['cdvoid']?>"/>

	<div class="bloco_conteudo">
		<div class="conteudo">
	
		    <div class="campo maior" style="float:left;width:49%">
                <fieldset id="fieldsetFaturamento" style="height:160px" >
                    <legend>Faturamento</legend>
                    <div class="campo medio">
                        <label for="clifaturamento">Faturar Monitoramento em Série: </label>
                        <select id="clifaturamento" style="width: 250px" name="clifaturamento" class="campo" >
                            <option value="">Selecione</option>
                            <?php foreach($this->notaFiscalSerie as $serie):?>
                            <option value="<?php echo $serie['nfsserie']?>" <?if($this->dadosCobranca['clientes']['clifaturamento'] == $serie['nfsserie']){?> SELECTED <?}?> ><?php echo $serie['nfsserie']?></option>
                            <?php endforeach;?>
                        </select>
                    </div>
                    
                    <div class="clear" ></div>
                    
                    <div class="campo medio">
                        <label for="clifat_locacao">Faturar Locação em Série: </label>
                        <select id="clifat_locacao" style="width: 250px" name="clifat_locacao" class="campo">
                            <option value="">Selecione</option>
                            <?php foreach($this->notaFiscalSerie as $serie):?>
                            <option value="<?php echo $serie['nfsserie']?>" <?if($this->dadosCobranca['clientes']['clifat_locacao']== $serie['nfsserie']){?> SELECTED <?}?> ><?php echo $serie['nfsserie']?></option>
                            <?php endforeach;?>
                        </select>
                    </div>
                </fieldset>
            </div>
            <div class="campo maior" style="float:left;width:50%; margin:5px 0px;">
                <fieldset style="height:160px" class="camposObrigatoriosOU" id="fieldsetImpostoRetidoNaFonte">
                    <legend>Impostos Retidos na Fonte</legend>
                    
                    <div class="campo">
                        <table>
                            <tr>
                                <td>
                                    <label for="cliret_iss">ISS: </label>
                                    <input type="checkbox" id="cliret_iss" name="cliret_iss" value="1" <?php if($this->dadosFaturamentoCliente['cliret_iss'] === 't') echo 'checked';?> />
                                </td>
                                <td>
                                    <label for="cliret_piscofins">PIS/COFINS/CSLL/IRF: </label>
                                    <input type="checkbox" id="cliret_piscofins" name="cliret_piscofins" value="1" <?php if($this->dadosFaturamentoCliente['cliret_piscofins'] === 't') echo 'checked';?> />
                                   
                                </td>
                            </tr>
                            <tr>
                                <td style="vertical-align: top">
                                    <div>
                                    <label for="cliret_iss_perc">Percentual ISS: </label>
                                        <?php if($this->dadosFaturamentoCliente['cliret_iss'] === 'f') {$dis = 'disabled="disabled"';} else {$dis = '';}?>
                                        <input type="text" id="cliret_iss_perc" name="cliret_iss_perc" class="valor obrigatorio" <?php echo $dis;?> value="<?php echo $this->dadosFaturamentoCliente['cliret_iss_perc']?>" />
                                        
                                    </div><br><label class="pesoerro erro"></label>
                                </td>                    
                                <td>
                                    <table>
                                        <tr>
                                            <td colspan="4">Alíquotas:</td>
                                        <?php if($this->dadosFaturamentoCliente['cliret_piscofins'] === 'f') {$disPis = 'disabled="disabled"';} else {$disPis = '';}?>
                                        </tr>
                                        <tr>
                                            <td>
                                                <label for="cliret_pis_perc">PIS: </label>
                                            </td>
                                            <td>
                                                <input type="text" id="cliret_pis_perc"  name="cliret_pis_perc" <?php echo $disPis;?> size="5" value="<?php echo $this->dadosFaturamentoCliente['cliret_pis_perc'] ?>" class="valor obrigatorio_ou" />
                                            </td>
                                            <td>
                                                <label for="cliret_cofins_perc">COFINS: </label>
                                            </td>
                                            <td>
                                                <input type="text" id="cliret_cofins_perc" name="cliret_cofins_perc" <?php echo $disPis;?> size="5" value="<?php echo $this->dadosFaturamentoCliente['cliret_cofins_perc'] ?>" class="valor obrigatorio_ou" />
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <label for="cliret_csll_perc">CSLL: </label>
                                            </td>
                                            <td>
                                                <input type="text" id="cliret_csll_perc" name="cliret_csll_perc" <?php echo $disPis;?> size="5" value="<?php echo $this->dadosFaturamentoCliente['cliret_csll_perc'] ?>" class="valor obrigatorio_ou" />
                                            </td>
                                            <td>
                                                <label for="cliret_irf_perc">IRF: </label>
                                            </td>
                                            <td>
                                                <input type="text" id="cliret_irf_perc" name="cliret_irf_perc" <?php echo $disPis;?> size="5" value="<?php echo $this->dadosFaturamentoCliente['cliret_irf_perc'] ?>" class="valor obrigatorio_ou" />
                                            </td>
                                        </tr>
                                    </table>
                                    </div>
                                </td>
                            </tr>
                        </table>
                    </div>
                </fieldset>
            </div>
						
			<div class="clear" ></div>
						
			<fieldset id="caracteristicaPagamento">
				<legend>Características de Pagamento</legend>
				<div class="campo">
					
					<div>
						<label for="forcoid">Forma de Pagamento Atual: </label>
						<label id="forma_pagamento_atual_clinome"><b>
							<?php echo utf8_decode($this->dadosCobranca['formaPagamentoAtual']['forcnome'])?>
							<?php if($this->dadosCobranca['formaPagamentoAtual']['cccativo'] == 't') :?>
							- Sufixo: <?php echo $this->dadosCobranca['formaPagamentoAtual']['sufixo'];?>
							<?php endif;?>
							</b></label>
					</div>
					
					<div class="clear"></div>
					
					<div class="campo medio">
						<label for="forcoid">Forma de Pagamento: </label>
						<select id="forma_pagamento_forcoid" name="forcoid" class="campo obrigatorio">
							<option value="">Selecione</option>
							<?php foreach($this->formaCobranca as $forma):?>
							<option value="<?php echo $forma['forcoid']?>" <?php if($this->dadosCobranca['clientes']['forcoid'] == $forma['forcoid']){echo 'selected="selected"';}?> ><?php echo utf8_decode($forma['forcnome'])?></option>
							<?php endforeach;?>
						</select>
						
					</div>
					<!-- 
					<div class="campo menor">
						<label for="campanha">&nbsp;</label>
						<?php if(count($this->formasCobrancaCliente) > 0): ?>
						<button class="campo" type="button" id="campanhas" name="campanhas" style="width:220px" value="Campanhas Promocionais Vigentes" 
						onclick="window.open('prn_manutencao_forma_cobranca_cliente_campanhas.php','wdwCampanha','width=980,height=300,top=0,left=0,toolbar=no,location=no,directories=no,status=no,menubar=yes,scrollbars=yes,resizable=yes');">
						Campanhas Promocionais Vigentes
						</button>
						<?php else: ?>
						<button class="campo" type="button" id="sem_campanhas" style="width:220px" name="sem_campanhas" disabled="disabled" value="Campanhas Promocionais Vigentes" >Campanhas Promocionais Vigentes</button>
						<?php endif; ?>
					</div>
					 -->
					
					<div class="clear"></div>
					
					<?php
					// verifica se tem permissão para alterar dia de vencimento
					if($this->alteraVencimento):
					?>
					<div class="campo" id="forma_pagamento_dia">
						<label>Dia do Vencimento: </label> <br/>
						<select id="forma_pagamento_clidia_vcto" name="forma_pagamento_clidia_vcto" >
							<option value="">Selecione</option>							
							<?php 
							if($this->dadosCobranca['clientes']['forcoid'] == null){
								$this->dadosCobranca['clientes']['clidia_vcto'] = null;
							}
							foreach($this->buscarDiaCobranca as $diaCobranca):?>
							<option value="<?php echo $diaCobranca['codigo']?>" <?php if($this->dadosCobranca['clientes']['clidia_vcto'] == $diaCobranca['dia_pagamento']){echo 'selected="selected"';}?> ><?php echo utf8_decode($diaCobranca['dia_pagamento'])?></option>
							<?php endforeach;?>
						</select>
											
						<fieldset class="item-check">
						    <legend>Títulos em Aberto</legend>
							<input type="checkbox" id="cv" name="forma_pagamento_clidia_vcto_alterar" value="1" style="vertical-align: middle" />
							<label for="cv">Alterar a data de vencimento dos títulos em aberto?</label>
						</fieldset>
					</div>
					<?php endif; ?>
					
					<div class="clear"></div>
					
					<div id="tr_motivo" class="campo medio" style="display:none">
						<label for="motivo_alterar_debito">Motivo: </label>
						<select id="motivo_alterar_debito" name="motivo_alterar_debito">
							<option value="">Selecione</option>
						</select>
					</div>
					
					<div class="clear"></div>
					
					<!--  TODO -->
					<div style="display: none">
					<label for="clicformacobranca">Forma de Pagamento (preferencial): </label>
					<input type="radio" id="clicformacobranca" name="clicformacobranca" value="1" />
					<label for="debito">Débito Automático </label>
					
					<input type="radio" id="clicformacobranca" name="clicformacobranca" value="1" />
					<label for="boleto">Boleto </label>
					
					<input type="radio" id="clicformacobranca" name="clicformacobranca" value="1" />
					<label for="cartao_credito">Cartão de Crédito</label>
					</div>
					
					<div id="blocoDebito" class="forma_debito_automatico" <?php if($this->dadosCobranca['clientes']['forcdebito_conta'] == 'f' || $this->dadosCobranca['clientes']['forcdebito_conta'] == null):?>style="display:none;"<?php endif;?> >
						
						<div class="campo maior">
							<label for="bancodigo">&nbsp;</label>
							<select id="debito_banco" name="debito_banco">
								<option value="">Selecione</option>
								<?php foreach($this->bancosOrderByNome as $bancoNome):?>
								<option value="<?php echo $bancoNome['bancodigo']?>" <?php if($this->dadosCobranca['clientes']['bancodigo'] == $bancoNome['bancodigo']) echo "selected";?>><?php echo $bancoNome['bannome']?></option>
								<?php endforeach;?>
							</select>
						</div>
						
						<div class="clear" ></div>
						
						<div class="campo">
							<label for="debito_agencia">Agência: </label>
							<br/><input type="text" class="numerico" id="debito_agencia" name="debito_agencia" size="4" maxlength="4" value="<?php echo ($this->dadosCobranca['clientes']['clicagencia'] == '') ? $_POST['debito_agencia'] : $this->dadosCobranca['clientes']['clicagencia']; ?>" />
						</div>
						
						
						<div class="campo">
							<label for="debito_conta">Conta: </label>
							<br/><input type="text" class="numerico" id="debito_conta" name="debito_conta" size="9" maxlength="9" value="<?php echo ($this->dadosCobranca['clientes']['clicconta'] == '') ? $_POST['debito_conta']: $this->dadosCobranca['clientes']['clicconta'] ;?>" />
						</div>
						
						<div class="campo menor">
							<label for="clititular_conta">Titular: </label>
							<input type="text" id="clictitular_conta" name="clictitular_conta" value="<?php echo ($this->dadosCobranca['clientes']['clictitular_conta'] == '') ? $_POST['clictitular_conta'] : $this->dadosCobranca['clientes']['clictitular_conta'] ;?>" />
						</div>
						
						<div class="clear" ></div>
						
						<div class="campo medio">
							<label for="">Tipo Conta: </label>
						</div>
						
						<div class="clear" ></div>
						<div>
							<input type="radio" id="clitipo" name="clitipo" value="F" <?php if($this->dadosCobranca['clientes']['clitipo'] == 'F') echo checked;?> />
							<label for="">PF </label>
							<input type="radio" id="clitipo" name="clitipo" value="J" <?php if($this->dadosCobranca['clientes']['clitipo'] == 'J') echo checked;?> />
							<label for="">PJ </label>
						</div>
						
					</div>	
					
					<div id="blocoCartao" class="tableMoldura forma_cartao_credito" <?php if($this->dadosCobranca['formaPagamentoAtual']['forccobranca_cartao_credito'] == 'f' || $this->dadosCobranca['formaPagamentoAtual']['forccobranca_cartao_credito'] == null):?>style="display:none;"<?php endif;?> >
					
						<div class="campo">
							<label for="cccnome_cartao">Nome no Cartão: </label>
							<br/><input type="text" id="cccnome_cartao" name="cccnome_cartao" size="42" value="<?php echo utf8_decode($this->dadosCobranca['formaPagamentoAtual']['cccnome_cartao'])?>" />
						</div>
						
						<div class="clear" ></div>
						
						<div class="campo">
							<label for="numero_cartao">Número: </label>
							<br/><input type="text" id="numero_cartao" name="numero_cartao" maxlength="16" class="obrigatorio" value="" />	
						</div>
						
						<div class="campo">
							<label for="mes_ano">Validade: </label>
							<br/><input type="text" id="mes_ano" name="mes_ano" size="5" maxlength="5" class="obrigatorio" value="" />
							<label>Ex: 01/15</label>
						</div>
					</div>
										
				</div>
			</fieldset>
			
			<div class="clear" ></div>
			
			<fieldset id="caracteristicaVencimentoCt">
			    <legend>Características de Vencimento Cargotracck</legend>
			    			
			    <div class="campo medio">						
					<label for="clicdias_prazo">Dias para Pagamento: </label>
					<select id="clicdias_prazo" name="clicdias_prazo" class="menor">
						<option value="">Selecione</option>
						<?php foreach($this->prazoVencimento as $prazo):?>
						<option value="<?php echo $prazo['cpvoid']?>"><?php echo $prazo['cpvprazo_dias']?></option>
						<?php endforeach;?>
					</select>				
				</div>
				
				<div class="campo menor">
					<fieldset style="margin: 0">
					   <legend>Dias: </legend>
					<input type="checkbox" id="clicdias_uteis" name="clicdias_uteis" value="true" />
					<label for="clicdias_uteis">Úteis</label>
					</fieldset>
				</div>
							
				<div class="clear"></div>
				
				<div class="campo maior">
					<fieldset style="margin: 0">
					   <legend>Período de Emissão de Nota Fiscal: </legend>
						<input type="checkbox" id="clic_periodo_emissao" name="clic_periodo_emissao" value="true" <?php if($this->dadosFaturamentoCliente['clicdt_inicial'] != '') echo ' checked="checked"';?> />
						<label for="clic_periodo_emissao">Restringir Período de Emissão</label>
						<div class="clear"></div>
						<div class="campo menor">
						<label for="clicdt_inicial">De: </label>
						<select id="clicdt_inicial" name="clicdt_inicial" disabled="disabled" class="obrigatorio">
							<option value="">Selecione</option>
							<?php for($di = 1; $di<=31; $di++){?>
							<option value="<?php echo $di?>" <?php if($this->dadosFaturamentoCliente['clicdt_inicial'] == $di) echo ' selected="selected"';?>><?php echo $di?></option>
							<?php }?>
						</select>
						</div>
						<div class="campo menor">
						<label for="clicdt_final">Até: </label>
						<select id="clicdt_final" name="clicdt_final" disabled="disabled" class="obrigatorio">
							<option value="">Selecione</option>
							<?php for($df = 1; $df<=31; $df++){?>
							<option value="<?php echo $df?>" <?php if($this->dadosFaturamentoCliente['clicdt_final'] == $df) echo ' selected="selected"';?>><?php echo $df?></option>
							<?php }?>
						</select>
						</div>
					</fieldset>
				</div>
							
				<div class="clear"></div>				
						
				<div class="campo menor">
					<label for="clicdia_mes">Dia do Mês: </label>
					<select id="clicdia_mes" name="clicdia_mes" >
						<option value="">Selecione</option>
						<?php for($dia=1;$dia<=31;$dia++):?>
						<option value="<?php echo $dia?>" <?php if($this->dadosCobranca['clientes']['clicdia_mes'] == $dia) echo selected;?>><?php echo $dia?></option>
						<?php endfor;?>
					</select>
				</div>
				
				<div class="campo menor">
					<label for="clicdia_semana">Dia da Semana: </label>
					<select name="clicdia_semana" id="clicdia_semana" >
						<option value="">Selecione</option>
						<?php 
							$arrDiasSemana = array();
						    $arrDiasSemana[1] = 'Segunda';
						    $arrDiasSemana[2] = 'Terça';
						    $arrDiasSemana[3] = 'Quarta';
						    $arrDiasSemana[4] = 'Quinta';
						    $arrDiasSemana[5] = 'Sexta';
						    foreach($arrDiasSemana as $dia=>$diaSemana):
						?>
						<option value="<?php echo $diaSemana;?>" <?php if($this->dadosCobranca['clientes']['clicdia_semana'] == $diaSemana) echo selected;?>><?php echo $diaSemana?></option>
						<?php endforeach;?>
					</select>
				</div>
			
			</fieldset>
			
			<div class="clear" ></div>
			
			<fieldset id="fieldset_obrigacao_financeira_cliente">
				<legend>Obrigação Financeira Cliente</legend>
				<div class="campo" style="width: 100%">
						<fieldset class="medio">
							<legend>Chat </legend>
							<input type="checkbox" id="clivisualizacao_sasgc" name="clivisualizacao_sasgc" value="t" <?php if($this->chat['clivisualizacao_sasgc'] == 't') echo "checked";?> />
							<label style="display: inline" for="clivisualizacao_sasgc">SASGC</label>
						</fieldset>
						
					<div class="bloco_titulo">Obrigação Finaceira</div>
					<div class="bloco_conteudo">
					    <div class="listagem">
					        <table>
					            <thead>
					                <tr>
					                    <th>Período</th>
					                    <th>Obrigação</th>
					                    <th>Software Principal</th>
					                    <th>Software Secundário</th>
					                    <th>Valor</th>
					                    <th>Data de Início</th>
					                    <th>Faturamento</th>
					                    <th>Autorizado Por</th>
					                    <th>Valido Até</th>
					                    <th>Ação</th>
					                </tr>
					            </thead>
					            <tbody>
		            			<input type="hidden" id="cliooid_deletar" name="cliooid_deletar" value="" />
					            <?php 
					            $cor="par";
					            foreach($this->obrigacoes as $obrigacao){
					            	$cor = ($cor=="par") ? "" : "par";
					            ?>
					            	<tr class="<?php echo $cor; ?>">
					            		<td><?php echo $obrigacao['cliono_periodo_mes']?></td>
					            		<td><?php echo $obrigacao['obrobrigacao']?></td>
					            		<td><?php echo $obrigacao['software_principal']?></td>
					            		<td><?php echo $obrigacao['software_secundario']?></td>
					            		<td><?=number_format($obrigacao['cliovl_obrigacao'],2,",",".");?></td>
					            		<td><?php echo $obrigacao['cliodt_inicio']?></td>
					            		<td><?php echo $obrigacao['faturamento']?></td>
					            		<td><?php echo $obrigacao['nm_usuario']?></td>
					            		<td><?php echo $obrigacao['cliodemonst_validade']?></td>
					            		<td class="centro td_acao_excluir"><a href="javascript:void(0);" class="excluirObrigacaoCliente" cliooid="<?php echo $obrigacao['cliooid']?>" title="Remover registro"><img src="images/icon_error.png" /></a></td>
					            	</tr>
					            	<tr id="motivoExclusao<?php echo $obrigacao['cliooid']?>" style="display: none;">
					            		<td colspan="10">
					            			<div class="maior campo">
						            			<label for="cliomotivo_exclusao<?php echo $obrigacao['cliooid']?>">Motivo da Exclusão</label>
						            			<textarea id="cliomotivo_exclusao<?php echo $obrigacao['cliooid']?>" name="cliomotivo_exclusao<?php echo $obrigacao['cliooid']?>"></textarea>
					            			</div>
											<div class="clear" ></div>
					            			<div class="campo medio">
												<button value="Excluir" id="buttonExcluirObrigacao" name="buttonExcluirObrigacao" class="buttonExcluirObrigacao" cliooid="<?php echo $obrigacao['cliooid']?>">Excluir</button>
											</div>
					            		</td>
					            	</tr>
					            	<?php }?>
					            </tbody>
					        </table>
					    </div>
					</div>
					<div class="bloco_acoes"><p><?php echo $this->getMensagemTotalRegistros(count($this->obrigacoes));?></p></div>
				</div>
				
				
			</fieldset>
			
		</div>
	</div>
	<div class="bloco_acoes">
		<button type="button" value="Confirmar" id="buttonConfirmarCobranca" name="buttonConfirmarCobranca" class="">Confirmar</button>
		<button type="button" value="Voltar" id="buttonVoltar" name="buttonVoltar" onclick="window.location.href='<?php echo str_replace('/', '', strrchr($_SERVER['SCRIPT_NAME'], '/'));?>'">Voltar</button>
	    <? if($this->retCliente!=""){ ?>
    		<button type="button" value="Voltar" id="buttonVoltar" name="buttonVoltar" onclick="window.location.href='<?=trata_retorno($this->retCliente,$clioid)?>'">Retornar ao Contrato</button>
        <? } ?>
	</div>
</form>
<script src="includes/js/prn_manutencao_forma_cobranca_cliente.js"></script>