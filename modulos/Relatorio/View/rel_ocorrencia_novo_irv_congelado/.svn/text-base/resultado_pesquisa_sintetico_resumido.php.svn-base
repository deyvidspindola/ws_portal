<div class="separador"></div>
<div id="resultados_relatorio" class="<?php echo ($this->view->resultados) ? '' : 'invisivel'; ?>">
<?php echo (trim($_POST['sub_acao'])!='gerarPdf') ? '<div class="bloco_titulo">Sintético Resumido</div>' : "";  ?>
<div class="bloco_titulo">
    Índice de Ocorrências Comunicadas e/ou Recuperadas no Período de
    <?php
        echo $this->view->parametros->ococdperiodo_inicial . " à ";
        echo $this->view->parametros->ococdperiodo_final;
    ?>
</div>
<div class="bloco_conteudo">
	<div class="listagem">
		<table <?php echo (trim($_POST['sub_acao'])=='gerarPdf') ? 'border="1"' : "";  ?>>
			<tbody>
                <?php require_once 'resumo.php';?>
			</tbody>

		</table>
	</div>
</div>
<?php if (trim($_POST['sub_acao'])!='gerarPdf'): ?>
<div class="bloco_acoes">
	<button type="button" id="gerar_pdf">Gerar PDF</button>
	<button type="button" id="btn_gerar_xls">Gerar XLS</button>
	<button type="button" onclick="javascript:window.print();">Imprimir</button>
</div>
<?php endif; ?>
</div>

<?php if (trim($_POST['sub_acao'])!='gerarPdf'): ?>
   <?php require_once _MODULEDIR_ . "Relatorio/View/rel_ocorrencia_novo_irv_congelado/bloco_csv.php"; ?>
<?php endif; ?>
