<style>
    
    #cliente_autocomplete {
        list-style: none;
        margin: 0;
        position: absolute;
        background-color: #fff;
        border: 1px solid #94adc2;
        box-sizing: border-box;
        width: 380px;
        margin-top: -1px;
        padding: 0;
        max-height: 128px;
        overflow-y: auto;
        display: none;
    }

    #cliente_autocomplete.show {
        display: block;
    }

    #cliente_autocomplete li {
        display: block;
        width: 100%;
    }

    #cliente_autocomplete a {
        color: #000000;
        font-size: 11px;
        padding: 4px 10px;
        box-sizing: border-box;
        display: block;
        width: 100%;
    }


    #cliente_autocomplete a:hover {
        cursor: pointer;
        background-color: #e6eaee;
        text-decoration: none;
    }

    .alerta-exception {
        color: #a94442;
        background-color: #f2dede;
        padding: 15px;
        margin-left: 20px;
        margin-bottom: 20px;
        border: 1px solid #ebccd1;
        border-radius: 4px;
        font-size: 11px;
    }

</style>
<?php if(isset($this->view->exception)): ?>
<div class="alerta-exception">
    <b>Erro!</b> <?php echo $this->view->exception; ?>
</div>
<?php endif; ?>
<form id="form_pesquisa" method="post" autocomplete="off">
    <input id="acao" type="hidden" name="acao" value="" />
    <div class="bloco_titulo">Dados para Pesquisa</div>
    <div class="bloco_conteudo">
        <div class="formulario">
            <!-- Período -->
            <?php
                // Período Padrão
                $data_inicial = date('d/m/Y', strtotime(date('Y-m-d H:i:s') . ' -90 days'));
                $data_final = date('d/m/Y');
            ?>
            <div class="campo data periodo">
                <div class="inicial">
                    <label for="periodo_data_inicial" style="cursor: default; ">Período:</label>
                    <input id="periodo_data_inicial" type="text" name="periodo_data_inicial" maxlength="10" value="<?php echo (isset($this->param->periodo_data_inicial)) ? $this->param->periodo_data_inicial : $data_inicial; ?>" class="campo" readonly>
                </div>
                <div class="campo label-periodo">a</div>
                <div class="final">
                    <label for="periodo_data_final" style="cursor: default; ">&nbsp;</label>
                    <input id="periodo_data_final" type="text" name="periodo_data_final" maxlength="10" value="<?php echo isset($this->param->periodo_data_final) ? $this->param->periodo_data_final : $data_final; ?>" class="campo" readonly>
                </div>
            </div>
            <div class="clear"></div>
            <!-- Veículo -->
            <div class="campo maior">
                <label for="veiculo">Veículo *</label>
                <input id="veiculo" type="text" name="veiculo" value="<?php echo (isset($this->param->veiculo)) ? $this->param->veiculo : ''; ?>">
            </div>
            <div class="clear"></div>
            <!-- Veículo ID -->
            <div class="campo maior">
                <label for="veiculo_id">ID</label>
                <input id="veiculo_id" type="text" name="veiculo_id" value="<?php echo (isset($this->param->veiculo_id)) ? $this->param->veiculo_id : ''; ?>">
            </div>
            <div class="clear"></div>
            <!-- Gerenciadora -->
            <div class="campo maior">
                <label for="gerenciadora">Gerenciadora</label>
                <select id="gerenciadora" name="gerenciadora">
                    <option value=""></option>
                    <?php foreach($this->view->gerenciadoras as $gerenciadora): ?>
                    <option value="<?php echo $gerenciadora->geroid; ?>" <?php echo isset($this->param->gerenciadora) && $this->param->gerenciadora == $gerenciadora->geroid ? 'selected' : ''; ?>><?php echo $gerenciadora->descricao; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="clear"></div>
            <!-- Cliente -->
            <div class="campo maior" style="position:relative;">
                <label for="cliente">Cliente *</label>
                <input id="cliente" type="hidden" name="cliente" style="width:380px;" value="<?php echo (isset($this->param->cliente)) ? $this->param->cliente : ''; ?>">
                <input id="cliente_termo" type="text" name="cliente_termo" style="width:380px;" value="<?php echo (isset($this->param->cliente_termo)) ? $this->param->cliente_termo : ''; ?>">
                <ul id="cliente_autocomplete"></ul>
            </div>
            <div class="clear"></div>
        </div>
    </div>
    <div class="bloco_acoes">
        <button id="bt_pesquisar" type="submit" style="cursor: default; ">Pesquisar</button>
    </div>
</form>
<script>

    jQuery(document).ready(function(){

        var ajaxClienteRunning = false;

        // jQuery('#veiculo_id').focusout(function(){

        jQuery('#veiculo_id').focusout(function(){
            var veiculo = jQuery(this).val();
            if(veiculo !== ''){

                jQuery.ajax({
                    type: 'POST',
                    url: 'rel_direcionamento_sinal.php',
                    dataType: 'json',
                    data: {
                        acao: 'ajaxLocalizarClienteByVeiculoId',
                        veiculo_id: veiculo
                    },
                    success: function(resp) {
                        if(resp.length > 0){
                            jQuery('#cliente').val(resp[0].clioid);
                            jQuery('#cliente_termo').val(resp[0].clinome);
                        }else{
                            // Mostrar texto de placa do veículo inválida
                        }
                        
                    },
                    error: function() {
                        ajaxClienteRunning = false;
                    }
                });

            }
        });

        jQuery('#veiculo').focusout(function(){
            var veiculo = jQuery(this).val();
            if(veiculo !== ''){

                jQuery.ajax({
                    type: 'POST',
                    url: 'rel_direcionamento_sinal.php',
                    dataType: 'json',
                    data: {
                        acao: 'ajaxLocalizarClienteByPlacaVeiculo',
                        placa_veiculo: veiculo
                    },
                    success: function(resp) {
                        if(resp.length > 0){
                            jQuery('#cliente').val(resp[0].clioid);
                            jQuery('#cliente_termo').val(resp[0].clinome);
                        }else{
                            // Mostrar texto de placa do veículo inválida
                        }
                        
                    },
                    error: function() {
                        ajaxClienteRunning = false;
                    }
                });

            }
        });
        
        jQuery('#cliente_termo').keyup(function(){

            var clienteTermo = jQuery('#cliente_termo').val();
            var clienteAutocomplete = jQuery('#cliente_autocomplete');

            jQuery('#cliente').val('');

            if(clienteTermo.length > 5 && ajaxClienteRunning === false){

                ajaxClienteRunning = true;

                jQuery.ajax({
                    type: 'POST',
                    url: 'rel_direcionamento_sinal.php',
                    dataType: 'json',
                    data: {
                        acao: 'ajaxCliente',
                        cliente_termo: clienteTermo
                    },
                    success: function(resp) {

                        if(resp !== null && resp.length > 0){
                            clienteAutocomplete.addClass('show');
                            clienteAutocomplete.empty();
                            for(var i = 0; i < resp.length; i++){
                                var newElem = '<li><a href="#" data-id="' + resp[i].clioid + '">' +  resp[i].clinome + '</a></li>';
                                clienteAutocomplete.append(newElem);
                            }

                            jQuery('#cliente_autocomplete a').click(function(){
                                var id = jQuery(this).data('id');
                                var nomeCliente = jQuery(this).text();
                                jQuery('#cliente').val(id);
                                jQuery('#cliente_termo').val(nomeCliente);
                                clienteAutocomplete.removeClass('show');
                                return false;
                            });

                        }else{
                            clienteAutocomplete.removeClass('show');
                        }

                        ajaxClienteRunning = false;

                    },
                    error: function() {
                        ajaxClienteRunning = false;
                    }
                });

            }

        });

    });

</script>