<?php require_once _MODULEDIR_ . "Financas/View/fin_cobranca_registrada/cabecalho.php"; ?>
<?php if(!empty($this->view->parametros->respostaErro)): ?>
	<div class="mensagem erro"><?php echo $this->view->parametros->respostaErro; ?></div>
<?php endif; ?>
<?php if(!empty($this->view->parametros->respostaSucesso)): ?>
	<div class="mensagem sucesso"><?php echo $this->view->parametros->respostaSucesso; ?></div>
<?php endif; ?>
<?php if(!empty($this->view->parametros->respostaAlerta)): ?>
	<div class="mensagem alerta"><?php echo $this->view->parametros->respostaAlerta; ?></div>
<?php endif; ?>
<?php if(!empty($this->view->parametros->respostaInfo)): ?>
	<div class="mensagem info"><?php echo $this->view->parametros->respostaInfo; ?></div>
<?php endif; ?>
<?php require_once _MODULEDIR_ . "Financas/View/fin_cobranca_registrada/bloco_opcoes.php"; ?>
<div id="conciliacao" <?php echo ($this->view->parametros->acao == 'conciliacao') ?  '' : 'style="display:none;"' ?>>
  <div class="bloco_titulo">Conciliação de Títulos</div>
  <form method="POST" action="fin_cobranca_registrada.php?acao=conciliacao" enctype='multipart/form-data'>
	  
	  <div class="bloco_conteudo">

	    <div class="formulario">
	          
					<div class="campo medio">
				    <label>Importar arquivo de retorno</label>
				    <br>
				    <input type="file" name="arquivo_retorno">
					</div>

					<div class="clear"></div>

	    </div>

	  </div>
	  <div class="bloco_acoes">
			<button type="submit">Gerar arquivos</button>
	  </div>

	</form>
</div>
<?php if ($_SERVER['REQUEST_METHOD'] === 'POST' && (!empty($this->view->nomeRelatorioBancoErp) || !empty($this->view->nomeRelatorioErpBanco))): ?>
<div class="separador"></div>
<div class="resultado_pesquisa">
    <div class="bloco_titulo">Download</div>
    <style>
    	
    	.bloco_conteudo .linha {
    		display: table;
    		width: 100%;
				max-width: 850px;
				margin: 0 auto;
    	}

    	.bloco_conteudo .coluna { display: table-cell; width: 50%; }

    	.bloco_conteudo .arquivo-item {
    		display: table;
    		margin: 40px auto;
    	}

    	.bloco_conteudo .arquivo-item img {
    		display: block;
    		margin: 0 auto 20px;
    	}

    	.bloco_conteudo .arquivo-item span {
    		display: block;
    		font-size: 10px;
    		text-align: center;
    	}

    	.bloco_conteudo p {
    		font-size: 14px;
    		text-align: center;
    	}


    </style>
    <div class="bloco_conteudo">
			<div class="linha">
				<?php if(!empty($this->view->nomeRelatorioBancoErp)): ?>
				<div class="coluna">			
					<a href="fin_cobranca_registrada.php?acao=downloadCsv&arquivo=<?php echo base64_encode($this->view->nomeRelatorioBancoErp); ?>" class="arquivo-item">
						<img src="images/icones/t3/caixa2.jpg" alt="Arquivo CSV">
						<span><?php echo $this->view->nomeRelatorioBancoErp; ?></span>
					</a>
				</div>
				<?php endif; ?>
				<?php if(!empty($this->view->nomeRelatorioErpBanco)): ?>
				<div class="coluna">
					<a href="fin_cobranca_registrada.php?acao=downloadCsv&arquivo=<?php echo base64_encode($this->view->nomeRelatorioErpBanco); ?>" class="arquivo-item">
						<img src="images/icones/t3/caixa2.jpg" alt="Arquivo CSV">
						<span><?php echo $this->view->nomeRelatorioErpBanco; ?></span>
					</a>
				</div>
				<?php endif; ?>
			</div>
    </div>
</div>
<?php endif; ?>
<?php require_once _MODULEDIR_ . "Financas/View/fin_cobranca_registrada/rodape.php"; ?>