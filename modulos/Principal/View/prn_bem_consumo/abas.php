<?php $ativo = "class='ativo'";?>
<ul class="bloco_opcoes">
	<li <?=(isset($_GET['acao']) && $_GET['acao'] == "principal" || $_GET['acao'] == "visualizar" || $_GET['acao'] == "") ? $ativo : ''?>>
		<a href="?acao=principal">Principal</a>
	</li>
	<li <?=(isset($_GET['acao']) && $_GET['acao'] == "index") ? $ativo : ''?>>
		<a href="?acao=index">Importação de Serial</a>
	</li>
</ul>