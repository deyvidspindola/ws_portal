<?php cabecalho(); ?>


<script>
    
    
//Edição de credito futuro

//verifico se o campos dor formulário serão bloqueado ou não, conforme a regra de possui movimentação ativa ou
// data de avaliação.
var bloqueia_campos_edicao = false;
<?php if ( isset($this->view->parametros->cadastro['bloqueia_campos_edicao'])  && $this->view->parametros->cadastro['bloqueia_campos_edicao']) : ?>
    var bloqueia_campos_edicao = true;
<?php endif; ?>    


//Fim edição

//cadastro de credito futuro

//verificacao se foi realizado post da step 3 no cadastro de credito futuro, para realizar validações
var post_realizado_step_3 = false;
<?php if (isset($this->view->parametros->postStep3) && $this->view->parametros->postStep3 == '1'  && $this->view->parametros->voltar == '0') : ?>
    post_realizado_step_3 = true;
<?php endif; ?>

//verifico se o step 3 é a tela atual no cadastro de credito futuro
var step3 = "<?php echo $this->view->parametros->step == 'step_3' && $this->view->parametros->acao == 'cadastrar' ? '1': '0'; ?>";

//seto o id do atual tipo de motivo de credito selecionado no step 2 no cadastro de credito futuro.
var tipoMotivoCredito = "<?php echo isset($_SESSION['credito_futuro']['step_2']['tipo_motivo']) && trim($_SESSION['credito_futuro']['step_2']['tipo_motivo']) != '' ? trim($_SESSION['credito_futuro']['step_2']['tipo_motivo']) : ''; ?>";

//fim cadastro
</script>

<!-- CSS -->
<link type="text/css" rel="stylesheet" href="lib/layout/1.1.0/style.css" />
<link type="text/css" rel="stylesheet" href="lib/layout/1.1.0/cupertino/jquery-ui-1.10.0.custom.min.css" />

<!-- jQuery -->
<script type="text/javascript" src="modulos/web/js/lib/jQuery.js"></script>

<!-- Arquivos básicos de javascript -->
<script type="text/javascript" src="lib/js/jquery-ui-1.10.0.custom.min.js"></script>
<script type="text/javascript" src="lib/layout/1.1.0/bootstrap.js"></script>
<script type="text/javascript" src="modulos/web/js/lib/jquery.maskMoney.js"></script>
<script type="text/javascript" src="modulos/web/js/lib/jquery.maskedinput-1.3.min.js"></script>

<!-- Arquivo javascript da demanda -->
<script type="text/javascript" src="modulos/web/js/fin_credito_futuro.js"></script>

<style type="text/css">

	.normal label{
		color: #000 !important;
	}

	.normal input{
		color: #000 !important;
		background: #fff !important;
		border-color: #000 !important;
	}

	label.normal {
		color: #000 !important;
	}

	input.normal {
		background: #fff !important;
		border-color: gray !important;
	}


    ul.ui-autocomplete {
        overflow-x: hidden !important;
        overflow-y: scroll !important;
        width: 244px !important; 
    }

    .msg_aviso{
        color: #666666;
        font-size: 12px;
        margin-top: 14px;
    }
    
    .important-style{
        height: 20px !important;
        width: 20px !important;
    }
    .bloco_opcoes li {
				display: block;
			}
			.step {
			
				width: 21px;
				height: 21px;
				margin: 3px;
				
				font-weight: bold;
			
				float: left;
			}
			.step-text {
				float: left;
				margin: 3px 55px 0 55px;
				font-size: 12px;
				font-weight: bold;
			}
			.nohover li.ativo {
				background: #d5d9dd;
			}
			.nohover li.voltar_aba:hover {
				background: #d5d9dd !important;
			}
			.listagem_grande {
				margin: auto;
				padding: 0 !important;
			}
			.listagem_itens {
				height: 420px;
				overflow: auto;
			}	
			.listagem_itens li {
				background: -webkit-repeating-linear-gradient(#fff, #fafcfc 100%);
				margin: 0;
				border-bottom: 1px solid #dfdfdf;
			}
			.listagem_itens li a { 
				display: block;
				padding: 5px;
				text-decoration: none;
			}
			.listagem_itens li a:hover { 
				display: block;
				padding: 5px;
				background: #fefadf;
			}
			.nopadding {
				padding: 0 !important;
			}
			.nomargin {
				margin: 0 !important;
			}
            
            .voltar_aba{
                cursor: pointer;
            }
            
            .inativo{
                background: #EEE9E9 !important;
                
            }
            
            .inativo:hover{
                background: #EEE9E9 !important;
            }
</style>

<div class="modulo_titulo">Crédito Futuro</div>
<div class="modulo_conteudo">
