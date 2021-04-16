<script language="JavaScript">
<!--

    function Mascara(objeto){
       if(objeto.value.length == 0)
         objeto.value = '(' + objeto.value;

       if(objeto.value.length == 3)
          objeto.value = objeto.value + ')';

     if(objeto.value.length == 8)
         objeto.value = objeto.value + '-';
}
//-->
</script>

<div 
class="bloco_titulo">Dados para Cadastro</div>
<div class="bloco_conteudo">
    <div class="formulario">
    
    
        <div class="campo menor">
		<label for="data_confirmar">Data Solicitação *</label>
            <? if ( isset($this->view->parametros->acao) && $this->view->parametros->acao == 'cadastrar' ) {?>
              
			 <input id="data_confirmar" name="data_confirmar" maxlength="10"   value="<?php echo date('d/m/Y H:i:s'); ?>" class="campo desabilitado" >
		     <?} else {?>
              <input id="data_confirmar" name="data_confirmar" maxlength="10"   value="<?php echo (isset($this->view->parametros->bacdt_solicitacao) ? $this->view->parametros->bacdt_solicitacao : '') ?>"  class="campo desabilitado" >
            <?}?>
        </div>


        <div class="campo medio">
            <label id="lbl_status" for="status">Status *</label>
            <select id="status" name="bacstatus">
                <option <?php echo (isset($this->view->parametros->bacstatus) && $this->view->parametros->bacstatus == 'C') ? 'selected=selected' : '' ?> value='C'>Concluido</option>
                <option <?php echo (isset($this->view->parametros->bacstatus) && $this->view->parametros->bacstatus == "A") ? 'selected=selected' : '' ?> value="A">Em Andamento</option>
                <option <?php echo (isset($this->view->parametros->bacstatus) && $this->view->parametros->bacstatus == "P") || ($this->view->parametros->bacstatus == '' && $_GET['acao'] == 'cadastrar') ? 'selected=selected' : '' ?> value="P">Pendente</option>
            </select>
        </div>
    
		<div class="clear"></div>
		
		
		<div class="campo maior">
            <label for="nome" >Cliente *</label>
            <input type="text" id="clinome"  maxlength="50" value="<?php echo (isset($this->view->parametros->clinome) ? $this->view->parametros->clinome : '') ?>" name="clinome" class="campo limpar_campos" />
            <input type="hidden" id="clioid" name="clioid"  value="<?php echo (isset($this->view->parametros->clioid) ? $this->view->parametros->clioid : '') ?>"  />   
        </div>   
       
       	<div class="clear"></div>
       	 

        <div class="campo menor">
            <label for="idplaca">Placa *</label>
            <select id="idplaca" name="bacplaca">
                <option value="">Escolha</option>
                <?php 
                    if (isset($this->view->parametros->placas) && count($this->view->parametros->placas) > 0) { 
                        foreach ($this->view->parametros->placas as $valorPlaca) { 
                        ?>
                            <option tipoContratoId="<?= $valorPlaca['tpcoid']?>" tipoContratoDesc="<?=  utf8_decode($valorPlaca['tpcdescricao'])?>" <?= (isset($this->view->parametros->bacplaca) && $this->view->parametros->bacplaca == $valorPlaca['veiplaca']) ? 'selected=selected' : '' ?> value="<?= $valorPlaca['veiplaca'] ?>"><?= $valorPlaca['veiplaca'] ?></option>
                        <?php 
                        }
                    } ?>
 
            </select>
         </div>
		
		 <div class="campo medio">
            <label for="idPlacaInput">Buscar por Placa</label>
            <input type="search" id="nomePlacaInput"  maxlength="50" value="<?= $valorPlaca['veiplaca'] ?>" name="nomePlacaInput" class="campo" placeholder="Digite 5 caracteres p/ autocompletar"/>
            <input type="hidden" id="idPlacaInput" name="idPlacaInput"  value=""  />
         </div>

		<div class="clear"></div>
		
		
		<div class="campo maior">
			<label  for="atendente_logado">Atendente *</label>
			<?php 
                    if (isset($this->view->parametros->buscarAtendenteLogado) && count($this->view->parametros->buscarAtendenteLogado) > 0) { 
                        foreach ($this->view->parametros->buscarAtendenteLogado as $item) {
                           $item->nm_usuario;
                           $item->cd_usuario ;
                         }                            
                      }
                       
            ?>  
            <input id="atendente_logado" name="atendente_logado" value="<?php  echo $item->nm_usuario ?>" disabled="disabled" class="campo desabilitado" type="text">
		</div>		
		
		
		<div class="clear"></div>
		
		<div class="campo maior">
            
			<label for="tpcdescricao">Tipo de Contrato *</label>
			<input id="tpcdescricao" name="tpcdescricao" value="<?php echo (isset($this->view->parametros->tpcdescricao) ? $this->view->parametros->tpcdescricao : '') ?>" readonly="readonly" class="campo desabilitado" type="text">
		    <input type="hidden" id="tpcoid" name="tpcoid"  value="<?php echo (isset($this->view->parametros->tpcoid) ? $this->view->parametros->tpcoid : '') ?>" />    
        </div>

		
		<div class="clear"></div>        
		
		<div class="campo menor">
			<label for="clifone">Telefone *</label>
            <input type="text" onkeypress="Mascara(this);" id="clifone" name="bacfone"  value="<?php echo (isset($this->view->parametros->bacfone) ? $this->view->parametros->bacfone : '') ?>"class="campo"  maxlength="15" />
		</div>	


		<div class="campo medio">
			<label for="cpf_cgc">CPF/CNPJ *</label>
			<input id="cpf_cgc" name="baccpf_cnpj" value="<?php echo (isset($this->view->parametros->baccpf_cnpj) ? $this->view->parametros->baccpf_cnpj : '') ?>" readonly="readonly" class="campo desabilitado" type="text" />
		</div>
		
		
		<div class="clear"></div>
        
		<?php if ( isset($this->view->parametros->bacoid) && $this->view->parametros->bacoid != '' ) :?>
	        <div class="campo menor">
	            <label id="lbl_bacestoid" for="bacestoid">UF</label>
	            <select id="bacestoid" name="bacestoid" disabled="disabled" class="campo desabilitado">
	                <option value="">Escolha</option>
	                <?php 
	                    if (isset($this->view->parametros->buscarUF) && count($this->view->parametros->buscarUF) > 0) { 
	                        foreach ($this->view->parametros->buscarUF as $item) { 
	                            if (strtoupper(trim($this->view->parametros->bacestoid)) == strtoupper(trim((string)$item->estoid))) { 
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
	            <label id="lbl_bacclcoid" for="bacclcoid">Cidade</label>
	            <select id="bacclcoid" name="bacclcoid" disabled="disabled" class="campo desabilitado">
	                <option value="">Escolha UF</option>
			        <?php if (isset($this->view->parametros->bacestoid) && $this->view->parametros->bacestoid != ''): ?>
			            <?php $cidades = $this->dao->buscarCidade($this->view->parametros->bacestoid); ?>
			            <?php foreach ($cidades as $cidade): ?>
			                <option value="<?php echo $cidade['id']; ?>" <?php echo ($this->view->parametros->bacclcoid == $cidade['id']) ? 'selected="selected"' : '' ; ?>>
			                    <?php echo $cidade['cidade'];?>
							</option>
			            <?php endforeach;?>
					<?php endif;?>
	            </select> 
	        </div>
		<?php endif;?>
		
		
		<div class="clear"></div>
		
	    <div class="campo maior">
            <label id="lbl_motivo" for="tipo_motivo">Motivo *</label>
            <select id="tipo_motivo" name="bacbmsoid">
                <option value="">Escolha</option>
                <?php 
                    if (isset($this->view->parametros->buscarMotivo) && count($this->view->parametros->buscarMotivo) > 0) { 
                        foreach ($this->view->parametros->buscarMotivo as $item) { 
                            if (strtoupper(trim($this->view->parametros->bacbmsoid)) == strtoupper(trim((string)$item->bmsoid))) { 
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
        		

		<div class="clear"></div>
		


		<div class="clear"></div><div class="campo maior">
			<label for="bacdetalhamento_solicitacao">Detalhamento da Solicitação *</label>
			<textarea id="bacdetalhamento_solicitacao" name="bacdetalhamento_solicitacao" ><?php echo (isset($this->view->parametros->bacdetalhamento_solicitacao) ? $this->view->parametros->bacdetalhamento_solicitacao : '') ?></textarea>
		</div>
		
		<div class="clear"></div>

    </div>
</div>
<div class="bloco_acoes">        
    <button type="submit" id="bt_gravar" name="bt_gravar" value="gravar">Confirmar</button>
    <button type="button" id="bt_voltar">Voltar</button>
</div>

<div class="separador"></div>


 <?php if (($this->view->parametros->bacoid) > 0) {?>
            

<div class="bloco_titulo">Detalhamento da Tratativa</div>
    <div class="bloco_conteudo">
        <div class="formulario">

            <div class="campo maior">
                <label for="bachtratativa">Detalhamento da Tratativa *</label>
                <textarea id="bachtratativa" name="bachtratativa"  maxlength="500" rows="5"><?php echo (isset($_POST['bachtratativa']) ? htmlentities( $_POST['bachtratativa'] ): ''); ?></textarea>
            </div>

            <div class="clear"></div>

        </div>
    </div>
<div class="bloco_acoes">
    <button type="button" id="bt_incluir_historico">Incluir</button>
</div>  

<div class="separador"></div>
            
    

<? if (count($this->view->parametros->dadosHistorico) > 0){?>
    <div class="bloco_conteudo">
        <div class="listagem">
            <table>
                <thead>
                    <tr>
                        <th class="menor">Data</th>
                        <th class="maior">Detalhamento da Tratativa</th>
                        <th class="medio">Atendente</th>                                
                    </tr>
                </thead>
                <tbody>
                    <?php
                        if (count($this->view->parametros->dadosHistorico) > 0):
                        $classeLinha = "par";
                    ?>
                    

                        <?php foreach ($this->view->parametros->dadosHistorico as $resultado) : ?>
                            <?php $classeLinha = ($classeLinha == "") ? "par" : ""; ?>
                                <tr id="<?php echo $resultado->bacoid ?>" class="<?php echo $classeLinha; ?>">

                                    <td class="centro"><?php echo $resultado->bachdt_cadastro; ?></td>
                                    <td class="esquerda"><?php  echo wordwrap($resultado->bachtratativa, 100, '<br />', true); ?></td>                                
                                    <td class="esquerda"><?php echo $resultado->nm_usuario; ?></td>
                                    
                               </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
                <tfoot>
                <tr>
                    <td colspan="10" class="centro">
                        <?php
                        $totalRegistros = count($this->view->parametros->dadosHistorico);
                        echo ($totalRegistros > 1) ? $totalRegistros . ' registros encontrados.' : '1 registro encontrado.';
                       ?>
                    </td>
                </tr>
            </tfoot>
            </table>
        </div>
    </div>

    <?}?>

    <?}?>
    
</div>      

