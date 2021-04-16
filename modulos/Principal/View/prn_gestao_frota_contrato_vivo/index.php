<?php 

    if (!$_SESSION['usuario']['vivo']['token']) {
        cabecalho();
    } 
?>

<script type="text/javascript" src="modulos/web/js/lib/jQuery.js"></script>
<link type="text/css" rel="stylesheet" href="lib/css/style.css" />

<script type="text/javascript" src="lib/js/bootstrap.js"></script>
<script type="text/javascript" src="lib/js/jquery-ui-1.10.0.custom.min.js"></script>
<script type="text/javascript" src="modulos/web/js/lib/jquery.maskedinput-1.3.min.js"></script>
<script type="text/javascript" src="lib/funcoes_masc_novo.js"></script>
<script type="text/javascript" src="modulos/web/js/prn_gestao_frota_contrato_vivo.js"></script>

<!-- jQuery UI -->
<link type="text/css" rel="stylesheet" href="lib/css/cupertino/jquery-ui-1.10.0.custom.min.css" />        


<style type="text/css">
    .ui-autocomplete-loading {
		background: white url('modulos/web/images/ajax-loader-circle.gif') right center no-repeat;
	}
    
    .ui-widget {
        font: 12px Arial !important;
    }
    
    .ui-menu .ui-menu-item a {
        cursor: pointer;
    }
    
    .ui-helper-hidden-accessible {       
        display: none !important;
    }
    
    .campo span {
        width: 241px !important;
    }
    
    div.campo {
        position: relative;
    }
    
    ul.ui-autocomplete {
        width: 253px !important;
    }
    .teste{
        float:left;
        *width: 300px !important;
    }
    
    .teste2 {
        *width: 313px !important;
    }
    
    .teste2a {
        *width: 314px !important;
    }
    
    .teste3{
        *width: 300px !important;
    }
    
    fieldset {
        *margin-right: 10px !important;
    }
        
    div.campo, .campo {
        *margin-right: 8px !important;
    }
    
</style>

<div class="modulo_titulo">Gestão de Contratos Vivo</div>
<div class="modulo_conteudo">
    <form>
        
        <input type="hidden" name="clioid_hidden" id="clioid_hidden" />
        <input type="hidden" name="resultado_pesquisa_hidden" id="resultado_pesquisa_hidden" />
        <input type="hidden" name="resultado_pesquisa_os_hidden" id="resultado_pesquisa_os_hidden" />
        
        <div id="msg_alerta" class="mensagem alerta invisivel"></div>
        
        <div class="teste">
        
        <div class="bloco_titulo teste2a">Resumo Cliente</div>
        <div class="bloco_conteudo teste3">

            <?php
            if ($_SESSION['usuario']['vivo']['token']) { ?>

                <div class="formulario">
                    <fieldset class="medio">
                        <legend>Tipo de Pessoa</legend>
                        <input disabled="disabled" type="radio" id="tipo_pessoa_juridica" name="tipo_pessoa" value="J" checked="checked" />
                        <label for="tipo_pessoa_juridica">Jurídica</label>
                        <input disabled="disabled" type="radio" id="tipo_pessoa_fisica" name="tipo_pessoa" value="F" />
                        <label for="tipo_pessoa_fisica">Física</label>                        
                    </fieldset>

                    <div class="clear"></div>

                    <div class="campo medio">
                        <label for="razao_social" class="razao_social">Razão Social</label>
                        <input readOnly="readonly" type="text" id="razao_social" value="<?php echo $_SESSION['usuario']['vivo']['cliente']['nome'] ?>" class="campo desabilitado razao_social limpar_campos" />
                        <label for="nome" class="invisivel nome">Nome</label>
                        <input type="text" id="nome" value="" class="campo nome invisivel limpar_campos" />
                    </div>

                    <div class="clear"></div>

                    <div class="campo medio">
                        <label for="cnpj" class="cnpj">CNPJ</label>
                        <input readOnly="readonly" type="text" id="cnpj" value="" class="campo desabilitado cnpj limpar_campos" />  
                        <label for="cpf" class="invisivel cpf">CPF</label>
                        <input readOnly="readonly" type="text" id="cpf" value="<?php echo $_SESSION['usuario']['vivo']['cliente']['cnpj'] ?>" class="campo cpf invisivel limpar_campos" />  
                    </div>

                    <div class="clear"></div>

                    <div class="campo medio">
                        <label for="idvivo">ID VIVO</label>
                        <input readOnly="readonly" type="text" id="idvivo" name="idvivo" value="" class="campo desabilitado limpar_campos" />                    
                    </div>

                    <div class="clear"></div>

                    <div class="campo medio">
                        <label for="endereco">Endereço</label>
                        <textarea id="endereco" readonly="readonly" class="campo desabilitado limpar_campos" rows="5" style="resize: none;"></textarea>                   
                    </div>

                    <div class="clear"></div>
                </div>



            <?php } else { ?>

                <div class="formulario">
                    <fieldset class="medio">
                        <legend>Tipo de Pessoa</legend>
                        <input type="radio" id="tipo_pessoa_juridica" name="tipo_pessoa" value="J" checked="checked" />
                        <label for="tipo_pessoa_juridica">Jurídica</label>
                        <input type="radio" id="tipo_pessoa_fisica" name="tipo_pessoa" value="F" />
                        <label for="tipo_pessoa_fisica">Física</label>                        
                    </fieldset>

                    <div class="clear"></div>

                    <div class="campo medio">
                        <label for="razao_social" class="razao_social">Razão Social</label>
                        <input type="text" id="razao_social" value="" class="campo razao_social limpar_campos" />
                        <label for="nome" class="invisivel nome">Nome</label>
                        <input type="text" id="nome" value="" class="campo nome invisivel limpar_campos" />
                    </div>

                    <div class="clear"></div>

                    <div class="campo medio">
                        <label for="cnpj" class="cnpj">CNPJ</label>
                        <input type="text" id="cnpj" value="" class="campo cnpj limpar_campos" />  
                        <label for="cpf" class="invisivel cpf">CPF</label>
                        <input type="text" id="cpf" value="" class="campo cpf invisivel limpar_campos" />  
                    </div>

                    <div class="clear"></div>

                    <div class="campo medio">
                        <label for="idvivo">ID VIVO</label>
                        <input type="text" id="idvivo" name="idvivo" value="" class="campo limpar_campos" />                    
                    </div>

                    <div class="clear"></div>

                    <div class="campo medio">
                        <label for="endereco">Endereço</label>
                        <textarea id="endereco" readonly="readonly" class="campo desabilitado limpar_campos" rows="5" style="resize: none;"></textarea>                   
                    </div>

                    <div class="clear"></div>
                </div>

            <?php
            } ?>
        </div>
        
        </div>
        
        <div class="teste">
        
        <div class="bloco_titulo teste2">Pessoas Autorizadas</div>
        <div class="bloco_conteudo teste3">
            <div class="formulario">
                <div class="campo medio">
                    <label for="pessoa_autorizada_nome">Nome</label>
                    <input type="text" id="pessoa_autorizada_nome" readonly="readonly" value="" class="campo desabilitado limpar_campos" />
                </div> 

                <div class="clear"></div>

                <div class="campo medio">
                    <label for="pessoa_autorizada_telefone">Telefones de Contato</label>
                    <input type="text" id="pessoa_autorizada_telefone" readonly="readonly" value="" class="campo desabilitado limpar_campos" />
                </div> 

                <div class="clear"></div>

            </div>
        </div>
        
      </div>
        
        <div class="teste">
        
        <div class="bloco_titulo teste2">Pessoas Emergência</div>
        <div class="bloco_conteudo teste3">
            <div class="formulario">
                <div class="campo medio">
                    <label for="pessoa_emergencia_nome_1">1 - Nome</label>
                    <input type="text" id="pessoa_emergencia_nome_1" readonly="readonly" value="" class="campo desabilitado limpar_campos" />
                </div> 

                <div class="clear"></div>

                <div class="campo medio">
                    <label for="pessoa_emergencia_telefone_1">Telefones de Contato</label>
                    <input type="text" id="pessoa_emergencia_telefone_1" readonly="readonly" value="" class="campo desabilitado limpar_campos" />
                </div> 

                <div class="clear"></div>
                
                <div class="campo medio">
                    <label for="pessoa_emergencia_nome_2">2 - Nome</label>
                    <input type="text" id="pessoa_emergencia_nome_2" readonly="readonly" value="" class="campo desabilitado limpar_campos" />
                </div> 

                <div class="clear"></div>

                <div class="campo medio">
                    <label for="pessoa_emergencia_telefone_2">Telefones de Contato</label>
                    <input type="text" id="pessoa_emergencia_telefone_2" readonly="readonly" value="" class="campo desabilitado limpar_campos" />
                </div> 

                <div class="clear"></div>

            </div>
        </div>
        
        </div>
        <div class="clear"></div>
        
        <?php include_once _MODULEDIR_ . 'Principal/View/prn_gestao_frota_contrato_vivo/tabela_veiculos.php' ?>
        
        <?php include_once _MODULEDIR_ . 'Principal/View/prn_gestao_frota_contrato_vivo/tabela_historico_os.php' ?>
        
    </form>
</div>