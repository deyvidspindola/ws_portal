
	<?php if (count($this->view->camposDestaque) > 0) : ?>
		<!--  Caso contenha erros, exibe os campos destacados  -->
		<script type="text/javascript">jQuery(document).ready(function() {
			showFormErros(<?php echo json_encode($this->view->camposDestaque); ?>);			
		});
		</script>
    <?php endif; ?>

</div>
<div class="separador"></div>


<!-- Rodapé -->
<?php require_once 'lib/rodape.php'; ?>

</body>
</html>