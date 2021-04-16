

<?php require_once _MODULEDIR_ . "Financas/View/fin_credito_futuro_relatorio_cliente_indicador/cabecalho.php"; ?>


<!-- Mensagens-->
<div id="mensagem_info" class="mensagem info">Os campos com * são obrigatórios.</div>

<?php if (isset($this->view->parametros->verificarCadastroEmailAprovacao) && !$this->view->parametros->verificarCadastroEmailAprovacao) : ?>
    <div id="mensagem_info" class="mensagem info">É necessário o cadastramento do e-mail para aprovação do crédito futuro.</div>
<?php endif; ?>


<div id="mensagem_erro" class="mensagem erro <?php if (empty($this->view->mensagemErro)): ?>invisivel<?php endif; ?>">
    <?php echo $this->view->mensagemErro; ?>
</div>

<div id="mensagem_alerta" class="mensagem alerta <?php if (empty($this->view->mensagemAlerta)): ?>invisivel<?php endif; ?>">
    <?php echo $this->view->mensagemAlerta; ?>
</div>

<div id="mensagem_sucesso" class="mensagem sucesso <?php if (empty($this->view->mensagemSucesso)): ?>invisivel<?php endif; ?>">
    <?php echo $this->view->mensagemSucesso; ?>
</div>


<form id="form"  method="post" action="fin_credito_futuro_relatorio_cliente_indicador.php">
    <input type="hidden" id="acao" name="acao" value="pesquisar"/>
    <input type="hidden" id="cliente_id" name="cliente_id" value="<?php echo trim($this->view->parametros->cliente_id); ?>">
    <input type="hidden" id="cliente_id_indicado" name="cliente_id_indicado" value="<?php echo trim($this->view->parametros->cliente_id_indicado); ?>">
    <!--Inclui o formulário de pesquisa-->
    <?php require_once _MODULEDIR_ . "Financas/View/fin_credito_futuro_relatorio_cliente_indicador/formulario_pesquisa.php"; ?>
</form>
    
<div id="resultado_pesquisa" >
    <form id="form_listagem_pesquisa" action="" method="POST">
        <input type="hidden" id="acao" name="acao" value="">            
        <?php
        if ($this->view->status && count($this->view->dados) > 0) {
            require_once _MODULEDIR_ . 'Financas/View/fin_credito_futuro_relatorio_cliente_indicador/resultado_pesquisa.php';
        }
        ?>
    </form>
</div> 

<?php if (count($this->view->dados) > 0) : ?>
    <!--  Caso contenha erros, exibe os campos destacados  -->
    <script type="text/javascript" >jQuery(document).ready(function() {
            showFormErros(<?php echo json_encode($this->view->dados); ?>);
        });
    </script>

<?php endif; ?>

<?php require_once _MODULEDIR_ . "Financas/View/fin_credito_futuro_relatorio_cliente_indicador/rodape.php"; ?>
