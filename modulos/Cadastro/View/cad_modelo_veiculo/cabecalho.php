<?php cabecalho(); ?>

<!-- CSS -->
<link type="text/css" rel="stylesheet" href="lib/layout/1.1.0/style.css" />
<link type="text/css" rel="stylesheet" href="lib/layout/1.1.0/jquery-ui/jquery-ui.custom.min.css" />

<!-- JAVASCRIPT -->
<script type="text/javascript" src="lib/layout/1.1.0/jquery/jquery.min.js"></script>
<script type="text/javascript" src="lib/layout/1.1.0/jquery-ui/jquery-ui.custom.min.js"></script>
<script type="text/javascript" src="lib/layout/1.1.0/jquery/jquery.maskedinput.min.js"></script>
<script type="text/javascript" src="lib/layout/1.1.0/bootstrap.js"></script>
<script type="text/javascript" src="lib/funcoes.js"></script>
<script type="text/javascript" src="modulos/web/js/cad_modelo_veiculo.js"></script>

<style type="text/css">
 .hand{
    cursor:pointer;
}
#bt_nova_marca{
    margin-top: 15px;
}
#bt_copiar{
    margin-top: 20px;
}
</style>

<div id="msg_dialogo_exclusao" class="invisivel">Deseja excluir o registro?</div>
<div id="msg_dialogo_exclusao_item" class="invisivel">Deseja realmente remover o acessório?</div>
<div class="modulo_titulo">Cadastro de Modelo de Veículos</div>
<div class="modulo_conteudo">