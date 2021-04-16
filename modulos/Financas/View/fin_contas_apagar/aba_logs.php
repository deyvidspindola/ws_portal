<input type="hidden" id="acao" name="acao" value="pesquisarLogs"/>

<div class="bloco_titulo">Dados para pesquisa</div>
<div class="bloco_conteudo">

    <div class="formulario">

        <div class="campo medio">
            <label for="banco">Banco</label> 
            <select id="banco" name="banco">
                <?php foreach ($this->view->bancos as $bancoId => $banco) :?>
                    <option value="<?php echo $banco->bancodigo; ?>" <?php echo ($this->view->filtros->banco == $banco->bancodigo ? 'selected="selected"' : ""); ?> ><?php echo $banco->bannome; ?></option>
                <?php endforeach;?>
            </select>
        </div>

        <div class="clear"></div>
    
    </div>
</div>

<div class="bloco_acoes">
    <button type="submit" id="bt_pesquisar">Pesquisar</button></div>


<div id="resultado_pesquisa" >
    
    <?php 
        if (count($this->view->dados['logs']) > 0) { 
            require_once 'aba_logs_resultado.php'; 
        } 
    ?>
        
</div>







