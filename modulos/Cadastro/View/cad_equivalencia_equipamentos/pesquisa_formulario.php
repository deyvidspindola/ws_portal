        <div class="bloco_titulo">Dados para Pesquisa</div>
        <div class="bloco_conteudo">
            <div class="formulario">
                <div class="campo medio">
                    <label for="eqqmodalidade">Modalidade do Contrato</label>
                    <select id="eqqmodalidade" name="eqqmodalidade" class="combo_pesquisa">
                        <option value="" <?php echo  isset($this->parametros->eqqmodalidade) && $this->parametros->eqqmodalidade != 'L' && $this->parametros->eqqmodalidade != 'V' ? 'selected="selected"' : '' ?> >Selecione</option>
                        <option class='opcao' value="L" <?php echo isset($this->parametros->eqqmodalidade) && !empty($this->parametros->eqqmodalidade) && $this->parametros->eqqmodalidade == 'L' ? 'selected="selected"' : '' ?> >Locação</option>
                        <option class='opcao' value="V" <?php echo isset($this->parametros->eqqmodalidade) && !empty($this->parametros->eqqmodalidade) && $this->parametros->eqqmodalidade == 'V' ? 'selected="selected"' : '' ?> >Revenda</option>                        
                    </select>
                </div>
                
                <div class="clear"></div>
                
                <div class="campo medio">
					<label for="eeqeqcoid">Classe do Contrato</label>
                    <select id="eeqeqcoid" name="eeqeqcoid" class="carregando combo_pesquisa">
                        <option value="">Selecione</option>  
                    </select>
                    <img class="carregando" id="loading-classe-contrato" style="display: none;" src="<?php echo _PROTOCOLO_ . _SITEURL_ . 'modulos/web/images/ajax-loader-circle.gif' ?>" />
                </div>   
                
                <div class="clear"></div>
                
				<div class="campo medio">
                    <label for="eeqtpcoid">Tipo do Contrato</label>
					<select id="eeqtpcoid" name="eeqtpcoid" class="carregando combo_pesquisa">
                       <option value="">Selecione</option>   
                    </select>
                    <img class="carregando" id="loading-tipo-contrato" style="display: none;" src="<?php echo _PROTOCOLO_ . _SITEURL_ . 'modulos/web/images/ajax-loader-circle.gif' ?>" />
                </div>   
                <div class="clear"></div>
                <input <?php echo isset($this->parametros->classes_sem_cadastro) && $this->parametros->classes_sem_cadastro == 1 ? 'checked="checked"' : ''; ?> style="float: left;" type="checkbox" id="classes_sem_cadastro" name="classes_sem_cadastro" value="1" />
							<label style="float: left; margin-top: 3px;" for="classes_sem_cadastro">Listar somente classes sem cadastro</label>
						<div class="clear"></div>
            </div>            
        </div>        
        <div class="bloco_acoes">

            <button type="button" id="bt_pesquisar" name="input_pesquisar" value="pesquisar">Pesquisar</button>  
			<button type="button" id="bt_gerar_xls" name="gerar_xls">Gerar XLS</button> 
            <button id="bt_novo" type="button">Novo</button>
        </div>
        
        <div class="separador"></div>

<script type="text/javascript">
    
var parametroModalidadeContrato = '';
var parametroPesquisaClasseContrato = '';
var parametroPesquisaTipoContrato = '';

<?php if ( isset($this->parametros->eqqmodalidade) &&  $this->parametros->eqqmodalidade != '' ) : ?>
    var parametroModalidadeContrato = '<?php echo $this->parametros->eqqmodalidade ?>';
<?php endif; ?>

<?php if ( isset($this->parametros->eeqeqcoid) &&  $this->parametros->eeqeqcoid != '' ) : ?>
    var parametroPesquisaClasseContrato = <?php echo $this->parametros->eeqeqcoid ?>;
<?php endif; ?>
    
<?php if ( isset($this->parametros->eeqtpcoid) &&  $this->parametros->eeqtpcoid != '' ) : ?>
    var parametroPesquisaTipoContrato = <?php echo $this->parametros->eeqtpcoid ?>;
<?php endif; ?>    

</script>