

<?php require_once _MODULEDIR_ . "Financas/View/fin_consulta_nota_fiscal_vivo/cabecalho.php"; ?>
    
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


<form id="form"  method="post" action="fin_consulta_nota_fiscal_vivo.php">
    <input type="hidden" id="acao" name="acao" value="pesquisar"/>
    <!--Inclui o formulário de pesquisa-->
    <?php require_once _MODULEDIR_ . "Financas/View/fin_consulta_nota_fiscal_vivo/formulario_pesquisa.php"; ?>
</form>
    
<div id="resultado_pesquisa" >
    <form id="form_listagem_pesquisa" action="" method="POST">
        <input type="hidden" id="acao" name="acao" value="">
        <input type="hidden" id="nfloid" name="nfloid" value="">             
        <?php 
        if ($this->view->status && count($this->view->dados) > 0) {
            if ($this->view->relatorio == 'N') {
                require_once _MODULEDIR_ . 'Financas/View/fin_consulta_nota_fiscal_vivo/resultado_pesquisa.php';
            } else {
                require_once _MODULEDIR_ . 'Financas/View/fin_consulta_nota_fiscal_vivo/resultado_pesquisa_placa.php';
            }
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

<?php require_once _MODULEDIR_ . "Financas/View/fin_consulta_nota_fiscal_vivo/rodape.php"; ?>
