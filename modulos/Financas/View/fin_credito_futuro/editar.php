<?php require_once _MODULEDIR_ . "Financas/View/fin_credito_futuro/cabecalho.php"; ?>

<!-- Mensagens-->
<div id="mensagem_info" class="mensagem info">Os campos com * são obrigatórios.</div>

<div id="mensagem_erro" class="mensagem erro <?php if (empty($this->view->mensagemErro)): ?>invisivel<?php endif; ?>">
    <?php echo $this->view->mensagemErro; ?>
</div>

<div id="mensagem_alerta" class="mensagem alerta <?php if (empty($this->view->mensagemAlerta)): ?>invisivel<?php endif; ?>">
    <?php echo $this->view->mensagemAlerta; ?>
</div>

<div id="mensagem_sucesso" class="mensagem sucesso <?php if (empty($this->view->mensagemSucesso)): ?>invisivel<?php endif; ?>">
    <?php echo $this->view->mensagemSucesso; ?>
</div>

<div class="modulo_titulo">Editar Crédito Futuro</div>
<?php require_once _MODULEDIR_ . "Financas/View/fin_credito_futuro/formulario_editar.php"; ?> 

    
 <?php if (count($this->view->dados) > 0) : ?>
    <!--  Caso contenha erros, exibe os campos destacados  -->
    <script type="text/javascript" >jQuery(document).ready(function() {
        showFormErros(<?php echo json_encode($this->view->dados); ?>); 
    });
    </script>

<?php endif; ?>

<?php require_once _MODULEDIR_ . "Financas/View/fin_credito_futuro/rodape.php"; ?>