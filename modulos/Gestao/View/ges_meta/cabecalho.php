


<?php 
    $script = array(
        'ges_meta.js'
    );
    $this->layout->renderizarCabecalho('Cadastro de Metas ', $script);

?>


<script type="text/javascript">

var funcionarios = new Array();

<?php if (isset($this->view->parametros->compartilhamento)  && !empty($this->view->parametros->compartilhamento)) : ?>
	<?php foreach ($this->view->parametros->compartilhamento AS $compartilhado) : ?>
		var funcionario = new Object();
   		funcionario.id = <?php echo intval($compartilhado->gmcfunoid) ?>;
   		funcionario.nome = '<?php echo $compartilhado->nm_usuario ?>';  	   			
   		funcionarios.push(funcionario);
	<?php endforeach; ?>
<?php endif; ?>
	

</script>

<script type="text/javascript">

<?php if (isset($_SESSION['gestao']['meta']['id']) && trim($_SESSION['gestao']['meta']['id']) != '') : ?>

	var metaId = <?php echo intval($_SESSION['gestao']['meta']['id']); ?>

	jQuery(document).ready(function() {
		if (confirm('Deseja incluir um plano de ação e uma ação para essa meta?')) {

			window.location.href = "ges_meta.php?acao=cadastrarPlanoAcao&metaId="+metaId;

		} else {
			jQuery.ajax({
				url: 'ges_meta.php',
				type: 'POST',
				data: {
					acao: 'limparSessao'
				}
			})

		}
	});


<?php endif; ?>


</script>
