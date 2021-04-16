

<?php require_once _MODULEDIR_ . "Manutencao/View/man_parametrizacao_smart_agenda/cabecalho.php"; ?>

    <!-- Mensagens-->
    <div id="mensagem_info" class="mensagem info">Todos os campos são obrigatórios.</div>

    <div id="mensagem_erro" class="mensagem erro <?php if (empty($this->view->mensagemErro)): ?>invisivel<?php endif;?>">
        <?php echo $this->view->mensagemErro; ?>
    </div>

    <div id="mensagem_alerta" class="mensagem alerta <?php if (empty($this->view->mensagemAlerta)): ?>invisivel<?php endif;?>">
        <?php echo $this->view->mensagemAlerta; ?>
    </div>

    <div id="mensagem_sucesso" class="mensagem sucesso <?php if (empty($this->view->mensagemSucesso)): ?>invisivel<?php endif;?>">
        <?php echo $this->view->mensagemSucesso; ?>
    </div>

    <ul class="bloco_opcoes">
        <li class="<?php echo ($this->view->parametros->acao != 'log') ? 'ativo' : '' ?>">
            <a href="?acao=pesquisar">Parametrização</a>
        </li>
        <li class="<?php echo ($this->view->parametros->acao == 'log') ? 'ativo' : '' ?>" >
            <a href="?acao=log">Histórico de Alterações</a>
        </li>

    </ul>

    <form id="form_cadastrar"  method="post" action="">
    <input type="hidden" id="acao" name="acao" value="cadastrar"/>

    <?php

        if($this->view->parametros->acao == 'log'){
            require_once _MODULEDIR_ . "Manutencao/View/man_parametrizacao_smart_agenda/resultado_pesquisa.php";

        } else {
            require_once _MODULEDIR_ . "Manutencao/View/man_parametrizacao_smart_agenda/formulario_cadastro.php";
        }
    ?>

    </form>

<?php require_once _MODULEDIR_ . "Manutencao/View/man_parametrizacao_smart_agenda/rodape.php"; ?>
