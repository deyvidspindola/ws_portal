<?php cabecalho(); ?>

<!-- CSS -->
<link type="text/css" rel="stylesheet" href="lib/layout/1.1.0/style.css" />
<link type="text/css" rel="stylesheet" href="lib/layout/1.1.0/jquery-ui/jquery-ui.custom.min.css" />

<!-- JAVASCRIPT -->
<script type="text/javascript" src="lib/layout/1.1.0/jquery/jquery.min.js"></script>
<script type="text/javascript" src="lib/layout/1.1.0/jquery-ui/jquery-ui.custom.min.js"></script>
<script type="text/javascript" src="modulos/web/js/lib/jquery.maskMoney.js"></script>
<script type="text/javascript" src="lib/layout/1.1.0/jquery/jquery.maskedinput.min.js"></script>
<script type="text/javascript" src="lib/layout/1.1.0/bootstrap.js"></script>

<!-- Arquivo javascript da demanda -->
<script type="text/javascript" src="modulos/web/js/rel_descontos_conceder.js"></script>

<style type="text/css">
	#form_email input, #form_email textarea {
		font-size: 11px;
	}
</style>

<div class="modulo_titulo">Crédito Futuro - Relatório Gerencial</div>
<div class="modulo_conteudo">
	<ul class="bloco_opcoes">
	    <li class="<?php echo $this->view->parametros->aba_ativa == 'credito_conceder' ? 'ativo' : ($this->view->parametros->aba_ativa != 'credito_concedidos' && $this->view->parametros->aba_ativa != 'campanhas_vigentes' ? 'ativo' : '') ?>">
            <a href="rel_descontos_conceder.php" title="Relatório de Descontos a Conceder">
                Relatório de Descontos a Conceder
            </a>
        </li>
	    <li class="<?php echo $this->view->parametros->aba_ativa == 'credito_concedidos' ? 'ativo' : '' ?>">
            <a href="fin_credito_futuro_relatorio_gerencial.php?acao=relatorioCreditosConcedidos" title="Relatório de Descontos a Concedidos">
                Relatório de Descontos Concedidos
            </a>
        </li>
	    <li class="<?php echo $this->view->parametros->aba_ativa == 'campanhas_vigentes' ? 'ativo' : '' ?>">
            <a href="fin_credito_futuro_relatorio_gerencial.php?acao=listarCampanhasVigentes" title="Campanhas Promocionais Vigentes">
                Campanhas Promocionais Vigentes
            </a>
        </li>
	</ul>