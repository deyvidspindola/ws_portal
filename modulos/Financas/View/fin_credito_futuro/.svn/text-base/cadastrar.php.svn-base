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

<div class="bloco_titulo">Novo Crédito Futuro</div>

<div class="bloco_conteudo">

    <div class="formulario">
        <!--include de abas aqui-->
        <ul class="bloco_opcoes nohover">
            <li id="aba_1" class="<?php echo $this->view->parametros->step == 'step_1' ? 'ativo' : ''; ?> <?php echo $this->view->parametros->step == 'step_2' || $this->view->parametros->step == 'step_3' ? 'voltar_aba' : ''; ?>"  >
                <div class="step">1</div>
                <div class="step-text">Cliente</div>
            </li>

            <li id="aba_2" class="<?php echo $this->view->parametros->step == 'step_2' ? 'ativo' : ''; ?> <?php echo $this->view->parametros->step == 'step_3' ? 'voltar_aba' : ''; ?>">
                <div class="step">2</div>
                <div class="step-text">Motivo</div>
            </li>

            <!-- Se estiver excluido -->
            <li id="aba_3" class="<?php echo $this->view->parametros->step == 'step_3' ? 'ativo' : ''; ?>">
                <div class="step">3</div>
                <div class="step-text">Valores do Crédito</div>
            </li>
        </ul>

        <!-- Renderiza os steps conforme passado-->
        <?php require_once _MODULEDIR_ . "Financas/View/fin_credito_futuro/cadastrar/formulario_cadastro_" . $this->view->parametros->step . ".php"; ?>


        <div class="bloco_acoes">
            <?php if ($this->view->parametros->step != '' && $this->view->parametros->step != 'step_1') : ?>
                <button type="button" id="bt_voltar">Voltar</button>
            <?php endif; ?>

            <?php if ($this->view->parametros->step == 'step_3') : ?>
                <button id="bt_avancar" class="salvar" disabled="disabled" type="button">Salvar</button>
            <?php else: ?>
                <button id="bt_avancar" disabled="disabled" type="button">Avançar</button>
            <?php endif; ?>     

        </div>
    </div>
</div>
<div class="bloco_acoes">
    <button id="bt_retornar" type="button">Cancelar</button>
</div>


<div class="separador"></div>

<?php if (count($this->view->dados) > 0) : ?>
    <!--  Caso contenha erros, exibe os campos destacados  -->
    <script type="text/javascript" >jQuery(document).ready(function() {
        showFormErros(<?php echo json_encode($this->view->dados); ?>); 
    });
    </script>

<?php endif; ?>

<?php require_once _MODULEDIR_ . "Financas/View/fin_credito_futuro/rodape.php"; ?>
