
<link href="modulos/web/css/ges_layout.css" rel="stylesheet" type="text/css">

<div id="layout-menu">
	<input type="hidden" name="superUsuario" id="superUsuario" value="<?php echo ($this->superUsuario) ? 't' : 'f' ?>" />
	<input type="hidden" name="meta_selecionada" id="meta_selecionada" value="" />
	
	<ul>
		<?php if ($this->habilitaInserir) : ?>
		<li class="inserir" id="inserir_acao"><a href="#">Inserir</a></li>

		<li class="alterar" id="alterar_plano_acao"><a href="#">Alterar</a></li>

		<?php endif; ?>

		<?php 		
		if ($this->habilitaUsuarios) : ?>
			<li class="usuario"><a href="ges_usuarios.php" target="ges_conteudo" class="link-menu">Usuário</a></li>
		<?php endif; ?>

		<?php if ($this->habilitaArvore) : ?>
			<li class="arvore-menu"><a href="ges_estrutura_arvore.php" target="ges_conteudo" class="link-menu">Árvore</a></li>
		<?php endif; ?>

		<?php if ($this->habilitaImportacao) : ?>
			<li class="importacao"><a href="ges_importacao.php" target="ges_conteudo" class="link-menu">Importação</a></li>
		<?php endif; ?>

		<?php if ($this->habilitaMetas) : ?>
			<li class="metas"><a href="ges_meta.php" target="ges_conteudo" class="link-menu">Metas</a></li>
		<?php endif; ?>

		<?php if ($this->habilitaIndicadores) : ?>
			<li class="indicadores"><a href="ges_indicador.php" target="ges_conteudo" class="link-menu">Indicadores</a></li>
		<?php endif; ?>

	</ul>

</div>
<div id="conteudo-inicial">
<?php 
    if ($this->habilitaInserir && $this->habilitaUsuarios && $this->habilitaArvore && $this->habilitaMetas && $this->habilitaIndicadores && $this->habilitaImportacao) {
        echo "Para iniciar selecione uma opção do menu.";
    } else {
        echo "Usuário sem permissão de acesso.";
    }
?>    
</div>
<div id="layout-principal-content">
    <div id="loader_iframe" class="carregando invisivel"></div>
    <iframe src="" name="ges_conteudo" id="ges_conteudo" width="100%" frameborder="0" scrolling="no" onload="setInterval(function(){resizeIframe(jQuery('#ges_conteudo').get(0)) }, 1500)"></iframe>






