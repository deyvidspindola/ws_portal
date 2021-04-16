

<?php require_once _MODULEDIR_ . "Cadastro/View/cad_retencao_impostos/cabecalho.php"; ?>





    <div class="">
        <ul class="bloco_opcoes">
            <li>
                <a href="cad_parametrizacao_rs_calculo_repasse.php" title="Cálculo do Repasse">Cálculo do Repasse</a>
            </li>
            <li class="">
                <a href="cad_parametrizacao_rs_calculo_repasse.php?acao=historico" title="Histórico Cálculo do Repasse">Histórico Cálculo do Repasse</a>
            </li>
            <li class="">
                <a href="cad_retencao_impostos.php" title="Retenção de Impostos">Retenção de Impostos</a>
            </li>
            <li class="ativo">
                <a href="cad_retencao_impostos.php?acao=historico" title="Histórico Retenção de Impostos">Histórico Retenção de Impostos</a>
            </li>
        </ul>
    </div>

    <div class="resultado bloco_titulo">Resultado da Pesquisa</div>
    <div class="resultado bloco_conteudo">

        <div class="separador"></div>

        <!-- Mensagens-->

        <div id="mensagem_erro" class="mensagem erro <?php if (empty($this->view->mensagemErro)): ?>invisivel<?php endif;?>">
            <?php echo $this->view->mensagemErro; ?>
        </div>

        <div id="mensagem_alerta" class="mensagem alerta <?php if (empty($this->view->mensagemAlerta)): ?>invisivel<?php endif;?>">
            <?php echo $this->view->mensagemAlerta; ?>
        </div>

        <div id="resultado_pesquisa" >

    	    <?php
            if ( $this->view->status && count($this->view->dados) > 0) {
                require_once 'resultado_pesquisa_historico.php';
            }
            ?>

        </div>
    </div>


<?php require_once _MODULEDIR_ . "Cadastro/View/cad_retencao_impostos/rodape.php"; ?>
