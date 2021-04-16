 <div id="msg_inclusao_manual" class="mensagem  invisivel"></div>  

<?php require_once '_header.php' ?>

<script type="text/javascript" src="modulos/web/js/man_parametrizacao_ura.js"></script>
    
<form method="post" action="" id="estatistica_form" >
	<input type="hidden" name="acao" id="acao" value="" />

    <div class="bloco_titulo">Desconsiderar Registros de Estatísticas GSM</div>
    <div class="bloco_conteudo">    
    	<div class="conteudo">
    	<div class="left ">
			<div class="campo medio" style="clear:both;">
	         <label>Status:</label>

            <div class="combo-check">
                <ul>
                    <li>
                        <label for="puestatus_a">Andamento</label>
                        <input type="checkbox" id="puestatus_a" name="puestatus[]" value="A"
                        <? $this->checkArray('A', $form['puestatus']) ?> />
                    </li>                            
                    <li>
                         <label for="puestatus_c">Concluído</label>
                         <input type="checkbox" id="puestatus_c" name="puestatus[]" value="C"
                          <? $this->checkArray('C', $form['puestatus']) ?> />
                   </li>
                    <li>
                         <label for="puestatus_p">Pendente</label>
                         <input type="checkbox" id="puestatus_p" name="puestatus[]" value="P"
                          <? $this->checkArray('P', $form['puestatus']) ?> />
                   </li>                   
               </ul>
            </div>
            </div>   
        	<div class="campo medio">
					<label>Ação:</label>
	            	<div class="combo-check">
	            		<ul>
                                <? foreach ($tiposAcao as $item): ?>
                                <li>
                                    <label for="pueegaoid_<?= $item['egaoid'] ?>"><?= $item['egadescricao'] ?></label>
                                    <input type="checkbox" id=pueostoid_<?= $item['egaoid'] ?>"
                                        name="pueegaoid[]" value="<?= $item['egaoid'] ?>"
                                        <? $this->checkArray($item['egaoid'], $form['pueegaoid']) ?> />
                                </li>
                                <? endforeach ?>
                            </ul>
      
	                </div>
           </div>
       	   </div> 
           <div class="left params_veiculo"> 
                <div class="campo maior3 clear-left"  >     
               <p>Veículo sem atualização inferior a: <input class="small-input" type="text" name="pueperiodo_atualizacao"  id="pueperiodo_atualizacao"   value="<?= $form['pueperiodo_atualizacao'] ?>" /> horas</p>
               <p>Pendência Financeira maior que: <input class="small-input" type="text"  name="puependencia_financeira"   id="puependencia_financeira" value="<?= $form['puependencia_financeira']?>" />dias</p>
               <p>LED de Bloqueio Ativo: <input type="checkbox" name="pueled_bloqueio" value="true" <? $this->isChecked($form['pueled_bloqueio']) ?> /></p>
               <p>Veículo dentro do período de Manutenção/Lava-Car (Front-End): <input type="checkbox" name="pueperiodo_lavacar" value="true" <? $this->isChecked($form['pueperiodo_lavacar']) ?> /></p>
               <p>Veículo dentro do período de Manutenção (Cadastro URA): <input type="checkbox" name="pueperiodo_manutencao" value="true" <? $this->isChecked($form['pueperiodo_manutencao']) ?> /></p>
           	   <p>Clientes Frota que não recebem contatos de estatística: </p>    
         	    <div class="campo maior3 clear-left" >
           	   		 <?php  $this->component_busca_cliente->render(); ?>             	    	 
       				<button id="btn_adicionar" name="btn_adicionar"  type="button" >Adicionar </button>
       			</div>       			  
       			<div id="puecliente_frota" class="borda-fix campo medio2 clear-left" >       			
       			 <?php if ($buscaClientes): ?>
       			   <? foreach ($buscaClientes as $item): ?>         			
	       			<div class="listagem">
	       			<table style="width:100%;margin:0px;" id ="puecliente_frotaX"><tr><td> <?= $item['clinome'] ?>
	    			<input type="hidden"  name = "puecliente_frota[]"  id = "puecliente_frota_<?= $item['clioid'] ?>"  value="<?= $item['clioid'] ?>" /></td> 
	    			<td style="width:18px;"><button id="clear_cliente_nome" class="componente_btn_limpar" name="clear_cliente_nome"  type="button"> X </button></td></tr></table></div>
	       			
	       			<? endforeach ?>
       			<?php endif; ?>
       			</div>    
					<button style="margin-top:5px" id="clear_client" class="componente_btn_limpar" name="limpar" type="button"> Limpar </button>
           	  	</div>
         </div>
    
     </div>
 	 <div class="conteudo">
            <div class="left">                
                <div class="borda-fix">
                    <p><b>Com Ordem de Serviço</b></p>
                    <div class="campo medio clear-left">
                        <label>Tipo:</label>
                        <div class="combo-check">
                            <ul>
                                <? foreach ($tiposOrdemServico as $item): ?>
                                <li>
                                    <label for="pueostoid_<?= $item['ostoid'] ?>"><?= $item['ostdescricao'] ?></label>
                                    <input type="checkbox" id=pueostoid_<?= $item['ostoid'] ?>"
                                        name="pueostoid[]" value="<?= $item['ostoid'] ?>"
                                        <? $this->checkArray($item['ostoid'], $form['pueostoid']) ?> />
                                </li>
                                <? endforeach ?>
                            </ul>
                        </div>
                    </div>                    
                    <div class="campo medio">
                        <label>Item:</label>
                        <div class="combo-check">
                            <ul>
                                <li>
                                    <label for="pueitem_a">Acessórios</label>
                                    <input type="checkbox" id="pueitem_a" name="pueitem[]" value="A"
                                        <? $this->checkArray('A', $form['pueitem']) ?> />
                                </li>                            
                                <li>
                                    <label for="pueitem_e">Equipamento</label>
                                    <input type="checkbox" id="pueitem_e" name="pueitem[]" value="E"
                                        <? $this->checkArray('E', $form['pueitem']) ?> />
                                </li>
                            </ul>
                        </div>
                    </div>
                    
                    <div class="campo medio clear-left">
                        <label>Status:</label>
                        <div class="combo-check">
                            <ul>
                                <? foreach ($statusOrdemServico as $item): ?>
                                <li>
                                    <label for="pueossoid_<?= $item['ossoid'] ?>"><?= $item['ossdescricao'] ?></label>
                                    <input type="checkbox" id="pueossoid_<?= $item['ossoid'] ?>"
                                        name="pueossoid[]" value="<?= $item['ossoid'] ?>"
                                        <? $this->checkArray($item['ossoid'], $form['pueossoid']) ?> />
                                </li>
                                <? endforeach ?>
                            </ul>
                        </div>
                    </div>     
                    <div class="clear"></div>
                </div>
            </div>
           <div class="left">                
                <div class="borda-fix">
                    <p><b>Com Contratos</b></p>
                    <div class="campo medio clear-left">
                        <label>Tipo de Contrato:</label>
                    <div class="combo-check">
                        <ul>
                            <? foreach ($tiposContrato as $item): ?>
                            <li>
                                <label for="puetpcoid_<?= $item['tpcoid'] ?>"><?= $item['tpcdescricao'] ?></label>
                                <input type="checkbox" id="puetpcoid_<?= $item['tpcoid'] ?>"
                                    name="puetpcoid[]" value="<?= $item['tpcoid'] ?>" 
                                    <? $this->checkArray($item['tpcoid'], $form['puetpcoid']) ?> />
                            </li>
                            <? endforeach ?>
                        </ul>
                    </div>
 
                    </div>
                    
                    <div class="campo medio">
                        <label>Status Contrato:</label>
                        <div class="combo-check">                    
                      	  <ul>
                            <? foreach ($statusContrato as $item): ?>
                            <li>
                                <label for="puecsioid_<?= $item['csioid'] ?>"><?= $item['csidescricao'] ?></label>
                                <input type="checkbox" id="puecsioid_<?= $item['csioid'] ?>" 
                                    name="puecsioid[]" value="<?= $item['csioid'] ?>" 
                                    <? $this->checkArray($item['csioid'], $form['puecsioid']) ?> />
                            </li>
                            <? endforeach ?>
                       	 </ul>
                       </div>
                    </div>                    
                    <div class="campo medio clear-left">
                        <label>Ocorrência com Status:</label>
                        <div class="combo-check">                
                       	 <ul>
                            <? foreach ($ocorrenciasStatus as $value => $label): ?>
                            <li>
                                <label for="pueocostatus_<?= $value ?>"><?= $label ?></label>
                                <input type="checkbox" id="pueocostatus_<?= $value ?>" 
                                    name=pueocostatus[]" value="<?= $value ?>" 
                                    <? $this->checkArray($value, $form['pueocostatus']) ?> />
                            </li>
                            <? endforeach ?>
                        </ul>
                    </div>
                    </div>     
                    <div class="clear"></div>
                </div>
            </div>
            <div class="right">
    			 <div class="campo medio clear-right">
                  
           </div>
       </div>        
    </div>
        
        <div class="clear"></div>
  </div>   
    <div class="bloco_acoes">
        <button type="button" name="botao_salvar_estatistica"    id="botao_salvar_estatistica">Salvar</button>
        <button type="button" name="botao_salvar_preestatistica" id="botao_salvar_preestatistica"> Predefinidos</button>
       
 
    </div>
</form>

<? require_once '_footer.php' ?>