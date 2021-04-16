
<?php require_once _MODULEDIR_ . "Principal/View/prn_modificacao_contrato/cabecalho.php"; ?>

    <div id="mensagem_erro" class="mensagem erro <?php if (empty($this->view->mensagemErro)): ?>invisivel<?php endif;?>">
        <?php echo $this->view->mensagemErro; ?>
    </div>

    <div id="mensagem_alerta" class="mensagem alerta <?php if (empty($this->view->mensagemAlerta)): ?>invisivel<?php endif;?>">
        <?php echo $this->view->mensagemAlerta; ?>
    </div>

    <div id="mensagem_sucesso" class="mensagem sucesso <?php if (empty($this->view->mensagemSucesso)): ?>invisivel<?php endif;?>">
        <?php echo $this->view->mensagemSucesso; ?>
    </div>

    <div class="mensagem alerta invisivel" id="msg_alerta_autocomplete"></div>

    <div id="tela_ativa" class="invisivel"><?php echo  $this->view->tela; ?></div>
    <div id="sub_tela_ativa" class="invisivel"><?php echo  $this->view->sub_tela; ?></div>
    <div id="msg_confirmar_voltar" class="invisivel">Deseja voltar para a tela de pesquisa? Você perderá os dados não salvos.</div>

<?php

    if(($this->view->tela != 'pesquisa' ) && ($this->view->tela != 'analise_credito') && ($this->view->tela != 'contratos_vencer')) {

        if($this->view->tela == 'cadastro') {

             if($this->view->sub_tela == 'aba_itens') {
                //Bloco Lote
                require_once _MODULEDIR_ . "Principal/View/prn_modificacao_contrato/aba_cadastro_modificacao/itens.php";
             } else if($this->view->sub_tela == 'aba_anexos') {
                //Bloco Anexo
                require_once _MODULEDIR_ . "Principal/View/prn_modificacao_contrato/aba_cadastro_modificacao/anexos.php";
             } else{
                //Bloco dados cadastro
                require_once _MODULEDIR_ . "Principal/View/prn_modificacao_contrato/aba_cadastro_modificacao/cadastro.php";
             }
        } else if ($this->view->tela == 'detalhes') {

             if($this->view->sub_tela == 'aba_itens'){
                //Bloco Itens
                require_once _MODULEDIR_ . "Principal/View/prn_modificacao_contrato/aba_detalhes_modificacao/itens.php";

             } else if($this->view->sub_tela == 'aba_acessorios'){
                //Bloco Particularidades
                require_once _MODULEDIR_ . "Principal/View/prn_modificacao_contrato/aba_detalhes_modificacao/acessorios.php";

             } else  if ($this->view->sub_tela == 'aba_dados_principais') {
                //Bloco dados principais
                require_once _MODULEDIR_ . "Principal/View/prn_modificacao_contrato/aba_detalhes_modificacao/dados_principais.php";
             } else  if ($this->view->sub_tela == 'aba_historico') {
                //Bloco Historico
                require_once _MODULEDIR_ . "Principal/View/prn_modificacao_contrato/aba_detalhes_modificacao/historico.php";
            }


        } else if ($this->view->tela == 'lista_contratos')  {
           //Bloco dados principais
           require_once _MODULEDIR_ . "Principal/View/prn_modificacao_contrato/lista_contratos.php";
        }

    } else {

        //Abas Prinicpais
        require_once _MODULEDIR_ . "Principal/View/prn_modificacao_contrato/aba_index.php";

        if ($this->view->tela == 'pesquisa') {
            require_once _MODULEDIR_ . "Principal/View/prn_modificacao_contrato/formulario_pesquisa.php";

            //Bloco Resultado da pesquisa
            if($this->view->statusPesquisa) {
                if($this->view->parametros->tipo_resultado == 'T'){
                    require_once _MODULEDIR_ . "Principal/View/prn_modificacao_contrato/resultado_pesquisa.php";
                } else {
                    require_once _MODULEDIR_ . "Principal/View/prn_modificacao_contrato/bloco_csv.php";
                }
            }

        } else if ($this->view->tela == 'analise_credito')  {
           require_once _MODULEDIR_ . "Principal/View/prn_modificacao_contrato/analise_credito.php";

        } else if ($this->view->tela == 'contratos_vencer') {
           require_once _MODULEDIR_ . "Principal/View/prn_modificacao_contrato/aba_contratos_vencer/formulario_pesquisa.php";

           //Bloco Resultado da pesquisa
            if($this->view->statusPesquisa) {
                require_once _MODULEDIR_ . "Principal/View/prn_modificacao_contrato/aba_contratos_vencer/resultado_pesquisa.php";

            }

        }


    }

require_once _MODULEDIR_ . "Principal/View/prn_modificacao_contrato/rodape.php";

?>
