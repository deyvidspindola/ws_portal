<div class="bloco_titulo">Dados para Pesquisa</div>
<div class="bloco_conteudo">
    <div class="formulario">
    
        <div class="campo data periodo" id="ajuste_periodo">
            <div class="inicial">
                <label for="dt_evento_de">Período *</label>
                <input id="dt_evento_de" name="dt_evento_de" maxlength="10" value="<?php echo $this->view->parametros->dt_evento_de; ?>" class="campo" />
            </div>
            <div class="campo label-periodo">a</div>
            <div class="final">
                <label for="dt_evento_ate">&nbsp;</label>
                <input id="dt_evento_ate" name="dt_evento_ate" maxlength="10" value="<?php echo $this->view->parametros->dt_evento_ate; ?>" class="campo" />
            </div>
        </div>      
        
        <div class="campo medio">
            <label id="lbl_status" for="status">Status</label>
            <select id="status" name="status">
                <option <?php echo ($this->view->parametros->status == '') ? 'selected="selected"' : '' ?>  value="">Escolha</option>
                <option <?php echo ($this->view->parametros->status == 'C') ? 'selected="selected"' : '' ?> value="C">Concluido</option>
                <option <?php echo ($this->view->parametros->status == 'A') ? 'selected="selected"' : '' ?> value="A">Em Andamento</option>
                <option <?php echo ($this->view->parametros->status == 'P') ? 'selected="selected"' : '' ?> value="P">Pendente</option>
            </select>
        </div>
        
     
       <fieldset class="medio opcoes-inline" id="ajuste_selecao_por">
            <legend id="ldg_selecao_por">Seleção por *</legend>
            <input type="radio" id="selecao_por_analitico" name="selecao_por" value="A" <?php echo trim($this->view->parametros->selecao_por) != '' && $this->view->parametros->selecao_por == 'A' ? 'checked="checked"' : ($this->view->parametros->selecao_por != 'A') ? 'checked="checked"' : ''  ?> />
            <label id="lbl_selecao_por_analitico" for="selecao_por_analitico">Analítico</label>
            <input type="radio" id="selecao_por_sintetico" name="selecao_por" value="S" <?php echo trim($this->view->parametros->selecao_por) != '' && $this->view->parametros->selecao_por == 'S' ? 'checked="checked"' : '' ?>  />
            <label id="lbl_selecao_por_sintetico" for="selecao_por_sintetico">Sintético</label>                
        </fieldset>
        
        
        <div class="clear"></div>
        
        
       <div class="campo maior">
            <label id="lbl_cliente" for="cliente" >Cliente</label>
            <input type="text" id="clinome" maxlength="50" name="cliente" value="<?php echo trim($this->view->parametros->cliente); ?>" class="campo cliente limpar_campos" />
            <input type="hidden" id="clioid" name="clioid"  value="<?php echo (isset($this->view->parametros->clioid) ? $this->view->parametros->clioid : '') ?>"  />   
       </div>   
       
       <div class="campo menor">
            <div id="div_placa">
                <label id="lbl_placa" for="placa">Placa</label>           
                <input type="text" id="idplaca" maxlength="9" name="placa" value="<?php echo trim($this->view->parametros->placa); ?>" class="campo placa limpar_campos" />
            </div>
       </div>        
                          
      <div class="clear"></div>
        
        
      <div class="campo maior">
            <label id="lbl_tipo_contrato" for="tipo_contrato">Tipo de Contrato</label>
            <select id="tipo_contrato" name="tipo_contrato">
                <option value="">Escolha</option>
                <?php 
                    if (isset($this->view->parametros->buscarTipoContrato) && count($this->view->parametros->buscarTipoContrato) > 0) { 
                        foreach ($this->view->parametros->buscarTipoContrato as $item) { 
                            if (strtoupper(trim($this->view->parametros->tipo_contrato)) == strtoupper(trim((string)$item->tpcoid))) { 
                                    ?>
                                    <option selected="selected" value="<?php echo $item->tpcoid ?>"><?php echo $item->tpcdescricao ?></option>
                                    <?php
                            } else { 
                                    ?>
                                    <option value="<?php echo $item->tpcoid ?>"><?php echo $item->tpcdescricao ?></option>
                                    <?php 
                            } 
                        }
                    } ?>
 
            </select>
        </div>
        
       <div class="campo maior">
            <label id="lbl_atendente" for="tipo_atendente">Atendente</label>
            <select id="tipo_atendente" name="tipo_atendente">
                <option value="">Escolha</option>
                <?php 
                    if (isset($this->view->parametros->buscarAtendente) && count($this->view->parametros->buscarAtendente) > 0) { 
                        foreach ($this->view->parametros->buscarAtendente as $item) { 
                            if (strtoupper(trim($this->view->parametros->tipo_atendente)) == strtoupper(trim((string)$item->cd_usuario))) { 
                                    ?>
                                    <option selected="selected" value="<?php echo $item->cd_usuario ?>"><?php echo $item->nm_usuario ?></option>
                                    <?php
                            } else { 
                                    ?>
                                    <option value="<?php echo $item->cd_usuario ?>"><?php echo $item->nm_usuario ?></option>
                                    <?php 
                            } 
                        }
                    } ?>
 
            </select>
        </div>

        
        <div class="clear"></div>
        
        
       <div class="campo maior">
            <label id="lbl_motivo" for="tipo_motivo">Motivo</label>
            <select id="tipo_motivo" name="tipo_motivo">
                <option value="">Escolha</option>
                <?php 
                    if (isset($this->view->parametros->buscarMotivo) && count($this->view->parametros->buscarMotivo) > 0) { 
                        foreach ($this->view->parametros->buscarMotivo as $item) { 
                            if (strtoupper(trim($this->view->parametros->tipo_motivo)) == strtoupper(trim((string)$item->bmsoid))) { 
                                    ?>
                                    <option selected="selected" value="<?php echo $item->bmsoid ?>"><?php echo $item->bmsdescricao ?></option>
                                    <?php
                            } else { 
                                    ?>
                                    <option value="<?php echo $item->bmsoid ?>"><?php echo $item->bmsdescricao ?></option>
                                    <?php 
                            } 
                        }
                    } ?>
 
            </select>
        </div>
        
        <div class="campo menor">
            <label id="lbl_uf" for="uf">UF</label>
            <select id="uf" name="uf">
                <option value="">Escolha</option>
                <?php 
                    if (isset($this->view->parametros->buscarUF) && count($this->view->parametros->buscarUF) > 0) { 
                        foreach ($this->view->parametros->buscarUF as $item) { 
                            if (strtoupper(trim($this->view->parametros->uf)) == strtoupper(trim((string)$item->estoid))) { 
                                    ?>
                                    <option selected="selected" value="<?php echo $item->estoid ?>"><?php echo $item->estuf ?></option>
                                    <?php
                            } else { 
                                    ?>
                                    <option value="<?php echo $item->estoid ?>"><?php echo $item->estuf ?></option>
                                    <?php 
                            } 
                        }
                    } ?>
                    
            </select>
        </div> 
        
        <div class="campo medio">
            <label id="lbl_cidade" for="cidade">Cidade</label>
            <select id="cidade" name="cidade">
                <option value="">Escolha UF</option>
		        <?php if (isset($this->view->parametros->uf) && $this->view->parametros->uf != ''): ?>
		            <?php $cidades = $this->dao->buscarCidade($this->view->parametros->uf); ?>
		            <?php foreach ($cidades as $cidade): ?>
		                <option value="<?php echo $cidade['id']; ?>" <?php echo ($this->view->parametros->cidade == $cidade['id']) ? 'selected="selected"' : '' ; ?>>
		                    <?php echo $cidade['cidade'];?>
						</option>
		            <?php endforeach;?>
				<?php endif;?>
            </select> 
        </div>
        
    	
		<div class="clear"></div>
		
    </div>
</div>

<div class="bloco_acoes">
    <button type="submit" id="bt_pesquisar">Pesquisar</button>
    <button type="button" id="bt_novo">Novo</button>
    <button type="button" id="bt_gerarArquivo">Gerar Arquivo</button>
</div>