

<?php require_once _MODULEDIR_ . "Relatorio/View/rel_analise_tratamento_os/cabecalho.php"; ?>

    <style>
         .bloco_tabelas{
             width: 578px;
          }

         .bloco_tabelas .bloco_titulo, .bloco_tabelas .bloco_conteudo{
            margin: 0 !important;
         }

         .bloco_direito{
             float: right;
             margin-right: 20px;
         }

         .bloco_esquerdo{
             float: left;
             margin-left: 20px;
         }

         .help{
            height: 16px !important;
            margin-top: 4px !important;
            width: 16px !important;
            cursor: pointer;
         }
        </style>
    <div id="mensagem_info" class="mensagem info">
        Os campos com * são obrigatórios.
    </div>

    <!-- Mensagens-->
    <div id="mensagem_erro" class="mensagem erro <?php if (empty($this->view->mensagemErro)): ?>invisivel<?php endif;?>">
        <?php echo $this->view->mensagemErro; ?>
    </div>

    <div id="mensagem_alerta" class="mensagem alerta <?php if (empty($this->view->mensagemAlerta)): ?>invisivel<?php endif;?>">
        <?php echo $this->view->mensagemAlerta; ?>
    </div>

    <div id="mensagem_sucesso" class="mensagem sucesso <?php if (empty($this->view->mensagemSucesso)): ?>invisivel<?php endif;?>">
        <?php echo $this->view->mensagemSucesso; ?>
    </div>


    <form id="form"  method="post" action="rel_analise_tratamento_os.php">
    <input type="hidden" id="acao" name="acao" value="<?php echo $acao?>"/>
    <input type="hidden" id="aotoid" name="aotoid" value=""/>


    <?php require_once _MODULEDIR_ . "Relatorio/View/rel_analise_tratamento_os/formulario_pesquisa.php"; ?>

    </form>

    <div id="resultado_pesquisa" >

	    <?php
        if ( $this->view->status && count($this->view->dados) > 0) {

			if (isset($this->view->csv) && $this->view->csv === true){

				require_once 'csv.php';

			} else {

				switch ( $this->view->parametros->tipo ){
					case 1:
						require_once 'resultado_pesquisa.php';
						break;
					case 2:
						require_once 'resultado_pesquisa_analitico.php';
						break;
				}

			}

        }
        ?>

    </div>

    <?php if (isset($this->view->json) && count($this->view->json) > 0) : ?>
    <script type="text/javascript" >jQuery(document).ready(function() {
        showFormErros(<?php echo json_encode($this->view->json); ?>);
    });
    </script>
    <?php endif;?>

<?php require_once _MODULEDIR_ . "Relatorio/View/rel_analise_tratamento_os/rodape.php"; ?>
