

<?php require_once _MODULEDIR_ . "Financas/View/fin_credito_futuro_pendentes/cabecalho.php"; ?>


<!-- Mensagens-->
<div id="mensagem_info" class="mensagem info">Os campos com * s&atilde;o obrigat&oacute;rios.</div>

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


<form id="form"  method="post" action="fin_credito_futuro_pendentes.php">
    <input type="hidden" id="acao" name="acao" value="pesquisar"/>
    <input type="hidden" id="cliente_id" name="cliente_id" value="<?php echo $this->view->parametros->cliente_id ?>">
    <!--Inclui o formulário de pesquisa-->
    <?php require_once _MODULEDIR_ . "Financas/View/fin_credito_futuro_pendentes/formulario_pesquisa.php"; ?>
</form>
    
<div id="resultado_pesquisa" >
    <form id="form_listagem_pesquisa" action="" method="POST">
        <input type="hidden" id="acao" name="acao" value="">            
        <?php
        if ($this->view->status && count($this->view->dados) > 0) {
            require_once _MODULEDIR_ . 'Financas/View/fin_credito_futuro_pendentes/resultado_pesquisa.php';
        }
        ?>
    </form>
</div> 

<?php if ($this->view->xls !== false) : ?>
    <div class="separador"></div>
    <div class="bloco_titulo">Download</div>
    <div class="bloco_conteudo">
        <div class="conteudo centro">
            <a target="_blank" href="download.php?arquivo=<?php echo $this->view->xls ?>">
                <img src="images/icones/t3/caixa2.jpg">
                <br>
                Relatório de Créditos Futuros - Pendentes
            </a>
        </div>
    </div>

<?php endif; ?>   
    
    
<div id="dialog-reprovar-credito-futuro" title="REPROVAR CRÉDITO FUTURO?" class="invisivel">
        
        <div class="separador"></div>
            
        <div id="reprovar_mensagem"></div>
            
        <div class="formulario">
            <form id="form_reprovar" action="fin_credito_futuro_pendentes.php" method="POST">
                <input type="hidden" name="acao" value="reprovar">
                <input type="hidden" id="cfooid" name="cfooid" value="<?php echo $this->view->parametros->cadastro['cfooid'] ?>"/>
                <input type="hidden" id="excluir_listagem" name="excluir_listagem" value="1"/>
                <label style="font-size: 10px; color: gray">
                    Campos com <b>*</b> são obrigatórios.
                </label>
                <div class="separador"></div>
                <div class="campo maior">
                    <label>Justificativa *</label>
                    <textarea id="justificativa_reprova" name="justificativa" rows="4" cols="25"></textarea>
                </div>
                <div style="clear: both"></div>
            </form>
                
        </div>
        <div class="separador"></div>
            
    </div>    

<?php if (count($this->view->dados) > 0) : ?>
    <!--  Caso contenha erros, exibe os campos destacados  -->
    <script type="text/javascript" >jQuery(document).ready(function() {
            showFormErros(<?php echo json_encode($this->view->dados); ?>);
        });
    </script>

<?php endif; ?>

<?php require_once _MODULEDIR_ . "Financas/View/fin_credito_futuro_pendentes/rodape.php"; ?>
