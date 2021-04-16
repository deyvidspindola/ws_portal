<?php require_once 'header.php'; ?>

<div class="modulo_titulo">Equivalência de Equipamento por Classe de Contrato</div>
<div class="modulo_conteudo">
    
<div class="mensagem info">Os campos com * são obrigatórios.</div>	


<?php if (isset($this->mensagemSucesso) && !empty($this->mensagemSucesso) ) : ?>
    <div class="mensagem sucesso"><?php echo $this->mensagemSucesso;  ?></div>
<?php endif; ?>


<?php if (isset($this->mensagemAlerta) && !empty($this->mensagemAlerta) ) : ?>
    <div class="mensagem alerta"><?php echo $this->mensagemAlerta;  ?></div>
<?php endif; ?>

<!--se existir erro no processamento de dados -->
<?php if ( isset($this->mensagemErro) && !empty($this->mensagemErro)) : ?>
    <div id="div_mensagem_listagem" class="mensagem erro" style="margin-bottom: 20px;"><?php echo $this->mensagemErro; ?></div>
<?php endif; ?>
  
    <form id="formCadastro"  method="post" action="cad_equivalencia_equipamentos.php">
        <input type="hidden" name="acao" value="cadastrarEquivalencia">
        
        <input type="hidden" name="eeqoid" value="<?php echo isset($this->parametrosEquivalencia->eeqoid)  && $this->parametrosEquivalencia->eeqoid != '' ? $this->parametrosEquivalencia->eeqoid : '' ?>">
        
        <div class="bloco_titulo">Dados Principais</div>
        <div class="bloco_conteudo">
            <div class="formulario">
                <div class="campo medio">
                    <label for="eqqmodalidade">Modalidade do Contrato *</label>
                    <select id="eqqmodalidade" name="eqqmodalidade" <?php echo !empty($this->idEquivalencia) ? 'disabled="disabled"' : '' ?>>
                        <option value="">Selecione</option>
                        <option value="L" <?php echo isset($this->parametrosEquivalencia->eqqmodalidade) && !empty($this->parametrosEquivalencia->eqqmodalidade) && $this->parametrosEquivalencia->eqqmodalidade == 'L' ? 'selected="selected"' : '' ?> >Locação</option>
                        <option value="V" <?php echo isset($this->parametrosEquivalencia->eqqmodalidade) && !empty($this->parametrosEquivalencia->eqqmodalidade) && $this->parametrosEquivalencia->eqqmodalidade == 'V' ? 'selected="selected"' : '' ?> >Revenda</option>                        
                    </select>
                </div>
                
                <div class="clear"></div>
 				 <div class="campo medio">
                    <label for="eeqeqcoid">Classe do Contrato *</label>
                    <select id="eeqeqcoid" name="eeqeqcoid" class="carregando" <?php echo !empty($this->idEquivalencia) ? 'disabled="disabled"' : '' ?>>
                        <option value="">Selecione</option>  
                    </select>
                    <img class="carregando" id="loading-classe-contrato" style="display: none;" src="<?php echo _PROTOCOLO_ . _SITEURL_ . 'modulos/web/images/ajax-loader-circle.gif' ?>" />
                </div>
                
				<div class="clear"></div>
                
				 <div class="campo medio">
                    <label for="modulo_principal">Tipo do Contrato *</label>
                    <select id="eeqtpcoid" name="eeqtpcoid" class="carregando" <?php echo !empty($this->idEquivalencia) ? 'disabled="disabled"' : '' ?>>
                       <option value="">Selecione</option>   
                    </select>
                    <img class="carregando" id="loading-tipo-contrato" style="display: none;" src="<?php echo _PROTOCOLO_ . _SITEURL_ . 'modulos/web/images/ajax-loader-circle.gif' ?>" />
                </div>
                
				<div class="clear"></div>
                
            </div>            
        </div>   
        <div class="bloco_acoes">            
            <?php if (!isset($this->parametrosEquivalencia->eeqoid) || $this->parametrosEquivalencia->eeqoid == '') : ?>
            <button type="button" id="bt_confirmar">Confirmar</button>
                <button type="button" id="bt_voltar">Voltar</button>  
            <?php endif; ?>
            <button type="button" id="bt_copiar" name="pesquisar" <?php echo empty($this->idEquivalencia) || $this->produtos !== false ? 'disabled="disabled"' : ''; ?>>Copiar Perfil de Outra Classe</button>  
        </div>
        
        <div class="separador"></div>
    </form>
    
    <form id="formCopia"  method="post" action="cad_equivalencia_equipamentos.php">
        <input type="hidden" name="acao" value="copiarEquivalencia">        
        <input type="hidden" name="eeqoid_destino" value="<?php echo $this->idEquivalencia ?>">        
        
        <div class="bloco_copia <?php echo !$this->mostrarQuadroCopiaClasse ? 'invisivel' : '' ?>">
            <div class="bloco_titulo">Copiar Classe</div>
            <div class="bloco_conteudo">
                <div class="formulario">
                    <div class="campo medio">
                        <label for="eqqmodalidade_copia">Modalidade do Contrato *</label>
                        <select id="eqqmodalidade_copia" name="eqqmodalidade_copia">
                            <option value="">Selecione</option>
                            <option value="L" <?php echo isset($this->parametros->eqqmodalidade_copia) && !empty($this->parametros->eqqmodalidade_copia) && $this->parametros->eqqmodalidade_copia == 'L' ? 'selected="selected"' : '' ?> >Locação</option>
                            <option value="V" <?php echo isset($this->parametros->eqqmodalidade_copia) && !empty($this->parametros->eqqmodalidade_copia) && $this->parametros->eqqmodalidade_copia == 'V' ? 'selected="selected"' : '' ?> >Revenda</option>                                                
                        </select>
                    </div>

                    <div class="clear"></div>

                    <div class="campo medio">
                        <label for="eeqeqcoid_copia">Classe do Contrato *</label>
                        <select id="eeqeqcoid_copia" name="eeqeqcoid_copia" class="carregando">
                            <option value="">Selecione</option>  
                        </select>                        
                        <img class="carregando" id="loading-classe-contrato-copia" style="display: none;" src="<?php echo _PROTOCOLO_ . _SITEURL_ . 'modulos/web/images/ajax-loader-circle.gif' ?>" />
                    </div>   

                    <div class="clear"></div>

                    <div class="campo medio">
                        <label for="eeqtpcoid_copia">Tipo do Contrato *</label>
                        <select id="eeqtpcoid_copia" name="eeqtpcoid_copia" class="carregando">
                           <option value="">Selecione</option>   
                        </select>
                        <img class="carregando" id="loading-tipo-contrato-copia" style="display: none;" src="<?php echo _PROTOCOLO_ . _SITEURL_ . 'modulos/web/images/ajax-loader-circle.gif' ?>" />
                    </div>   
                    <div class="clear"></div>                
                </div>            
            </div>        
            <div class="bloco_acoes">
                <button type="submit" id="bt_copiar_submit">Copiar</button>  
                <button type="button" id="bt_cancelar_copia">Cancelar</button>
            </div>
            <div class="separador"></div>
        </div>
     </form>
        
       
        
    
    <!-- carrega cadastrod e produtos se já tiver equivalencia de equipamentos cadastrados -->
     <?php if (isset($this->parametrosEquivalencia->eeqoid) && $this->parametrosEquivalencia->eeqoid != '') : ?>
            <?php require_once 'cadastro_produtos.php'; ?>
     <?php endif;?>

    <!--Local de carregamento da listagem -->
    <center><div id="load-listagem" style="margin-bottom: 20px" class="carregando invisivel"></div></center>
    <div id="listagem_equivalencia_equipamentos">
    <?php if (isset($this->parametrosEquivalencia->eeqoid) && $this->parametrosEquivalencia->eeqoid != '') : ?>
        <?php $this->carregarListagemEquipamento($this->parametrosEquivalencia->eeqoid); ?>
    <?php endif;?>
    </div>
</div>
<div class="separador"></div>
<script type="text/javascript">
var parametroModalidadeContrato = '';
var parametroPesquisaClasseContrato = '';
var parametroPesquisaTipoContrato = '';

var parametroModalidadeContrato_copia = '';
var parametroPesquisaClasseContrato_copia = '';
var parametroPesquisaTipoContrato_copia = '';


var parametroCadastroTipoProduto = '';
var parametroCadastroProduto = '';
var parametroCadastroVersoes = '';


//parametrosProdutos

<?php if ( isset($this->parametrosProdutos->eeitipo) ) : ?>
    var parametroCadastroTipoProduto = '<?php echo $this->parametrosProdutos->eeitipo != '' ? $this->parametrosProdutos->eeitipo : -1  ?>';
<?php endif; ?>
    
<?php if ( isset($this->parametrosProdutos->eeiprdoid) &&  $this->parametrosProdutos->eeiprdoid != '' ) : ?>
    var parametroCadastroProduto = '<?php echo $this->parametrosProdutos->eeiprdoid ?>';
<?php endif; ?>
    
<?php if ( isset($this->parametrosProdutos->eeiversao) &&  $this->parametrosProdutos->eeiversao != '' ) : ?>
    var parametroCadastroVersoes = '<?php echo $this->parametrosProdutos->eeiversao ?>';
<?php endif; ?>
    
    
    

<?php if ( isset($this->parametrosEquivalencia->eqqmodalidade) &&  $this->parametrosEquivalencia->eqqmodalidade != '' ) : ?>
    var parametroModalidadeContrato = '<?php echo $this->parametrosEquivalencia->eqqmodalidade ?>';
<?php endif; ?>

<?php if ( isset($this->parametrosEquivalencia->eeqeqcoid) &&  $this->parametrosEquivalencia->eeqeqcoid != '' ) : ?>
    var parametroPesquisaClasseContrato = <?php echo $this->parametrosEquivalencia->eeqeqcoid ?>;
<?php endif; ?>

<?php if ($this->parametrosEquivalencia->eeqtpcoid == '' && !empty($this->idEquivalencia) ) : ?>
    var parametroPesquisaTipoContrato = '-1';
<?php elseif ($this->parametrosEquivalencia->eeqtpcoid != ''): ?>
    var parametroPesquisaTipoContrato = <?php echo $this->parametrosEquivalencia->eeqtpcoid ?>;
<?php endif; ?> 
    
    
//variaveis setadas para os paramentros de copia
<?php if ( isset($this->parametros->eqqmodalidade_copia) &&  $this->parametros->eqqmodalidade_copia != '' ) : ?>
    var parametroModalidadeContrato_copia = '<?php echo $this->parametros->eqqmodalidade_copia ?>';
<?php endif; ?>

<?php if ( isset($this->parametros->eeqeqcoid_copia) &&  $this->parametros->eeqeqcoid_copia != '' ) : ?>
    var parametroPesquisaClasseContrato_copia = <?php echo $this->parametros->eeqeqcoid_copia ?>;
<?php endif; ?>

<?php if (isset($this->parametros->eeqtpcoid_copia) && $this->parametros->eeqtpcoid_copia != ''): ?>
    var parametroPesquisaTipoContrato_copia = <?php echo $this->parametros->eeqtpcoid_copia ?>;
<?php endif; ?>   
    

    
    
var formErros = '';
<?php if (isset($this->erros) && !empty($this->erros)) : ?>
    var formErros = '<?php echo $this->erros ?>';    
<?php endif; ?>    
</script>        
        
<?php require_once 'footer.php'; ?>

        
        