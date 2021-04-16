<?php 
	$ativo = "ativo";
	$idCliente = isset($_GET['clioid'])?'&clioid='.$_GET['clioid']:'';

	if(!$idCliente)
	    $blocked = "blocked";
	$retCliente = '';
	if($this->retCliente!=""){

	    $str = str_replace("=","%3D",str_replace("&","%26",$this->retCliente));
	    $retCliente = '&retCliente='.urlencode($str);
	}
?>
<ul class="bloco_opcoes">
	<li class="<?=((isset($_GET['acao']) && $_GET['acao'] == "principal") || $_GET['acao'] == "") ? $ativo : ''?>">
		<a href="?acao=principal<?php echo $idCliente?><?php echo $retCliente?>">Principal</a>
	</li>
	<li class="<?=(isset($_GET['acao']) && $_GET['acao'] == "endereco") ? $ativo : ''?> <?php echo $blocked;?>" >
		<a href="<?php echo ($idCliente?'?acao=endereco'.$idCliente:'#') ?><?php echo $retCliente?>">Endereço</a>
	</li>
	<li class="<?=(isset($_GET['acao']) && $_GET['acao'] == "particularidades") ? $ativo : ''?> <?php echo $blocked;?>" >
		<a href="<?php echo ($idCliente?'?acao=particularidades'.$idCliente:'#') ?><?php echo $retCliente?>">Particularidades</a>
	</li>
	<li class="<?=(isset($_GET['acao']) && $_GET['acao'] == "cobranca") ? $ativo : ''?> <?php echo $blocked;?>" >
		<a href="<?php echo ($idCliente?'?acao=cobranca'.$idCliente:'#') ?><?php echo $retCliente?>">Cobrança / Faturamento</a>
	</li>
	<li class="<?=(isset($_GET['acao']) && $_GET['acao'] == "gerenciadora") ? $ativo : ''?> <?php echo $blocked;?>" >
		<a href="<?php echo ($idCliente?'?acao=gerenciadora'.$idCliente:'#') ?><?php echo $retCliente?>">Gerenciadora</a>
	</li>
	<li class="<?=(isset($_GET['acao']) && $_GET['acao'] == "beneficios") ? $ativo : ''?> <?php echo $blocked;?>" >
		<a href="<?php echo ($idCliente?'?acao=beneficios'.$idCliente:'#') ?><?php echo $retCliente?>">Benefícios</a>
	</li>
	<li class="<?=(isset($_GET['acao']) && $_GET['acao'] == "contatos") ? $ativo : ''?> <?php echo $blocked;?>" >
		<a href="<?php echo ($idCliente?'?acao=contatos'.$idCliente:'#') ?><?php echo $retCliente?>">Contatos</a>
	</li>
	<li class="<?=(isset($_GET['acao']) && $_GET['acao'] == "segmentacao") ? $ativo : ''?> <?php echo $blocked;?>" >
		<a href="<?php echo ($idCliente?'?acao=segmentacao'.$idCliente:'#') ?><?php echo $retCliente?>">Segmentação</a>
	</li>
	<li class="<?=(isset($_GET['acao']) && $_GET['acao'] == "siggo") ? $ativo : ''?> <?php echo $blocked;?>" >
		<a href="<?php echo ($idCliente?'?acao=siggo'.$idCliente:'#') ?><?php echo $retCliente?>">Siggo</a>
	</li>
	<li class="<?=(isset($_GET['acao']) && $_GET['acao'] == "operacoes") ? $ativo : ''?> <?php echo $blocked;?>" >
		<a href="<?php echo ($idCliente?'?acao=operacoes'.$idCliente:'#') ?><?php echo $retCliente?>">Operações Cargo Tracck</a>
	</li>
	<li class="<?=(isset($_GET['acao']) && $_GET['acao'] == "historico") ? $ativo : ''?> <?php echo $blocked;?>" >
		<a href="<?php echo ($idCliente?'?acao=historico'.$idCliente:'#') ?><?php echo $retCliente?>">Histórico</a>
	</li>	
</ul>