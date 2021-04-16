<? require_once '_header.php' ?>
<script type="text/javascript">
jQuery(document).ready(function() {

	//removeAlerta();
	<?php if($this->hasFlashMessage()): ?>
		criaAlerta('<?=$this->flashMessage()?>');
		jQuery('#bt_enviar').attr('disabled', 'disabled');
	<?php endif; ?>
	
});
</script>
