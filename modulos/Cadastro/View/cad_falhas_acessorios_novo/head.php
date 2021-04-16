<script type="text/javascript" src="modulos/web/js/lib/jQuery.js"></script>
<link type="text/css" rel="stylesheet" href="lib/css/style.css" />

<script type="text/javascript" src="lib/js/bootstrap.js"></script>
<script type="text/javascript" src="lib/js/jquery-ui-1.10.0.custom.min.js"></script>
<script type="text/javascript" src="modulos/web/js/lib/jquery.maskedinput-1.3.min.js"></script>

<!-- jQuery UI -->
<link type="text/css" rel="stylesheet" href="lib/css/cupertino/jquery-ui-1.10.0.custom.min.css" />        

<link type="text/css" rel="stylesheet" href="modulos/web/css/cad_falhas_acessorios_novo.css" />        
<script type="text/javascript" src="modulos/web/js/cad_falhas_acessorios_novo.js"></script>
<script type="text/javascript">
jQuery(document).ready(function(){
<?php 
	if(count($this->camposAlerta)>0): 
		foreach($this->camposAlerta as $campo):
?>
		jQuery("#<?php echo $campo?>").addClass('erro');
<?php
		endforeach; 
	endif; 
?>
});
</script>