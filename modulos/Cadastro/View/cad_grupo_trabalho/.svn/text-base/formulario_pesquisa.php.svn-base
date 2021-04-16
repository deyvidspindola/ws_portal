<div class="bloco_titulo">Dados principais</div>
<div class="bloco_conteudo">
    <div class="formulario">
        
        <div class="campo medio">
            <label for="gtroid">Grupo de Trabalho *</label>
            <select class="campo" id="gtroid" name="gtroid">
                <option value="">Escolha</option>
                <?php if (isset($this->view->parametros->grupos) && count($this->view->parametros->grupos)> 0) : ?>
                <?php foreach($this->view->parametros->grupos as $grupo): ?>
                
                <option value="<?php echo $grupo->gtroid;?>"><?php echo $grupo->gtrnome;?></option>
                
                <?php endforeach;?>
                <?php endif;?>
                
            </select>
        </div>
        
        <div class="clear"></div>
        
        
        <fieldset class="maior">
            <legend>Perfil</legend>
            <input type="radio" name="gtropcoes" value="gtrvisualizacao_individual" id="gtrvisualizacao_individual" checked="checked" />
            <label for="gtrvisualizacao_individual">Visualização Individual</label>
            
            <input type="radio" name="gtropcoes" value="gtrlancamento_edicao" id="gtrlancamento_edicao" />
            <label for="gtrlancamento_edicao">Lançamento/Edição</label>
            
        </fieldset>
        
        <div class="clear"></div>
        
        
    </div>
</div>

<div class="bloco_acoes">
    <button type="submit" id="bt_confirmar">Confirmar</button>
</div>







