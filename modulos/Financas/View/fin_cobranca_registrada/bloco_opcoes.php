<ul class="bloco_opcoes">

    <li class="<?php echo ($this->view->parametros->acao == 'remessa' || trim($this->view->parametros->acao) == '') ?  'ativo' : '' ?>" id="aba_remessa" 
        <?php echo ($this->view->parametros->acao == 'remessa' || trim($this->view->parametros->acao) == '') ?  '' : 'style="background: url(images/fundo.gif);"' ?>>
        <a href="javascript:void(0)">Remessa</a>
    </li>

    <li class="<?php echo ($this->view->parametros->acao == 'rejeitado') ?  'ativo' : '' ?>" id="aba_rejeitado" 
        <?php echo ($this->view->parametros->acao == 'rejeitado') ?  '' : 'style="background: url(images/fundo.gif);"' ?>>
        <a href="javascript:void(0)">Títulos Rejeitados</a>
    </li>
    <?php 
    //[ORGMKTOTVS-837] - bloqueio CRIS
    if(!INTEGRACAO_TOTVS_ATIVA){
    ?>
    <li class="<?php echo ($this->view->parametros->acao == 'arquivo') ?  'ativo' : '' ?>" id="aba_arquivo" 
        <?php echo ($this->view->parametros->acao == 'arquivo') ?  '' : 'style="background: url(images/fundo.gif);"' ?>>
        <a href="javascript:void(0)">Gerar Arquivo Remessa</a>
    </li>
        <?PHP
    }
     //Fim - [ORGMKTOTVS-837] - bloqueio CRIS
    ?>
    
    <li class="<?php echo ($this->view->parametros->acao == 'conciliacao') ?  'ativo' : '' ?>" 
        <?php echo ($this->view->parametros->acao == 'conciliacao') ?  '' : 'style="background: url(images/fundo.gif);"' ?>>
        <a href="fin_cobranca_registrada.php?acao=conciliacao">Conciliação dos Registros de Títulos</a>
    </li>

</ul>