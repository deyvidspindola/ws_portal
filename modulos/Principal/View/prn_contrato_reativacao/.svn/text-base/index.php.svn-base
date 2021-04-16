<link type="text/css" rel="stylesheet" href="lib/layout/1.1.0/style.css" />

<div class="bloco_titulo" style="margin:0px 1px -1px 1px;">
    <table>
        <tr>
            <td> <input id="hab_form_rt" name="hab_form_rt" value="0" type="checkbox"/> </td>
            <td><label style="font-size: 12px; font-weight: bold;" for="hab_form_rt"> &nbsp;Reativar parcelas do(s) contrato(s)</label></td>
        </tr>
    </table>
</div>
    
    <div class="bloco_conteudo"  id="bloco_conteudo_reativacao" style="margin:0px 1px -1px 1px;">
        <div class="formulario" style="padding:10px;">
            <table style="margin-left:8px">
                <tr>
                    <td>
                        <input id="opcao_lote" name="opcao_lote" value="0" type="checkbox" disabled="disabled"> 
                    </td>
                    <td><label for="opcao_lote">Arquivo CSV lote</label></td>
                </tr>
            </table>

            <br />

            <div id="conteudo_formulario">
                <?php require_once _MODULEDIR_ . "Principal/View/prn_contrato_reativacao/formulario_padrao.php"; ?>
            </div>
            
        </div>
    </div>

<div class="bloco_acoes" style="margin:0px 1px -1px 1px;padding:1px 0" id="bl_enviar_reativacao" >
    <button type="button" id="btn_enviar_reativacao" disabled="disabled" style="padding:0px;">Gravar</button>
</div>

<script type="text/javascript">

    jQuery('.numeric').bind('paste',function(e){
        e.preventDefault();
    });

    jQuery("#hab_form_rt").on('click', function(event) {
        if(jQuery("#hab_form_rt").is(':checked')) {
            jQuery('#opcao_lote').attr('disabled', false);
            jQuery('#valor_negociado').attr('disabled', false);
            jQuery('#justificativa').attr('disabled', false);
            jQuery('#btn_enviar_reativacao').attr('disabled', false);
            jQuery('#manter_valor').attr('disabled', false);
            jQuery('#parcelas_acessorios').attr('disabled', false);
            jQuery('#arquivo_reativacao').attr('disabled', false);
        } else {
            jQuery('#opcao_lote').attr('disabled', true);
            jQuery('#valor_negociado').attr('disabled', true); 
            jQuery('#justificativa').attr('disabled', true); 
            jQuery('#btn_enviar_reativacao').attr('disabled', true); 
            jQuery('#manter_valor').attr('disabled', true);
            jQuery('#parcelas_acessorios').attr('disabled', true);
            jQuery('#arquivo_reativacao').attr('disabled', true); 
        }
    });

    jQuery("#opcao_lote").on('click', function(event) {

        var formularioLote = false;

        if(jQuery("#opcao_lote").is(':checked')) {
            formularioLote = true;
            jQuery("#btn_enviar_reativacao").text("Importar");
        } else {
            jQuery("#btn_enviar_reativacao").text("Gravar");
        }

        jQuery.ajax({
            type: 'POST',
            dataType: 'html',
            data: {tipo_formulario: formularioLote},
            contentType: "application/x-www-form-urlencoded;charset=ISO-8859-1",
        })
        .done(function(data) {

            jQuery("#conteudo_formulario").html(data);
            if(formularioLote == false) {
                jQuery('#opcao_lote').attr('disabled', false); //makes it enabled
                jQuery('#valor_negociado').attr('disabled', false);
                jQuery('#justificativa').attr('disabled', false);
                jQuery('#btn_enviar_reativacao').attr('disabled', false);
                jQuery('#manter_valor').attr('disabled', false);
                jQuery('#parcelas_acessorios').attr('disabled', false);
            } else {
                jQuery('#btn_enviar_reativacao').attr('disabled', false);
                jQuery('#arquivo_reativacao').attr('disabled', false);
            }

            console.log("success");
        })
        .fail(function() {
            console.log("error");
        })
        .always(function() {
            console.log("complete");
        });
        
    });

    jQuery("#btn_enviar_reativacao").on('click', function(event) {
        event.preventDefault();
        /* Act on the event */
        if(jQuery("#opcao_lote").is(':checked')) {
            reativacaoContratoArquivo();
        } else {
            reativacaoFormularioPadrao();
        }
    });

    /**
     * [reativacaoFormularioPadrao description]
     * @return {[type]} [description]
     */
    function reativacaoFormularioPadrao() {
        if(jQuery('input:checkbox[name^="ct_"]:checked').length == 0) {
            alert("Selecione ao menos um contrato!")
        } else {
            var contratos = [];

            jQuery('input:checkbox[name^="ct_"]:checked').each(function(){
                contratos.push(jQuery(this).attr("name").replace(/_[0-9]+$/,'').replace(/\D/g,''));
            });

            if(!jQuery("#manter_valor").is(':checked') && (jQuery("#valor_negociado").val().length == 0 || jQuery("#valor_negociado").val() == '0,00') ) {
                alert("Campo valor precisa ser preenchido.");
            } else {
                //console.log(contratos);
                jQuery('#btn_enviar_reativacao').attr('disabled', true); 
                jQuery.ajax({
                    type: 'POST',
                    dataType: 'json',
                    data: {
                        tipo_reativacao : 'formulario_principal',
                        lista_contratos: contratos,
                        valor_servico: jQuery("#valor_negociado").val(),
                        manter_valor: jQuery("#manter_valor").is(':checked'),
                        parcelas_acessorios: jQuery("#parcelas_acessorios").is(':checked'),
                        justificativa: jQuery("#justificativa").val()
                    },
                })
                .success(function(data){
                    if(typeof data.erro !== 'undefined') {
                        alert(data.erro)
                    } else {
                        alert("Operação realizada com sucesso.")
                        window.location.assign('contrato_servicos.php');
                    }
                })
                .done(function() {
                    console.log("success");
                })
                .fail(function() {
                    console.log("error");
                })
                .always(function() {
                    console.log("complete");
                    jQuery('#btn_enviar_reativacao').attr('disabled', false); 
                });
            }
            
        }
    }

    /**
     * [reativacaocContratoArquivo description]
     * @return {[type]} [description]
     */
    function reativacaoContratoArquivo() {
        jQuery('#btn_enviar_reativacao').attr('disabled', true); 
        // Get current url
        var url = window.location.toString()
        // Get form
        var form = document.getElementById('form_reativacao')
        // Get file input
        var fileInput = document.getElementById('arquivo_reativacao')
        // Get file object, contains file properties
        var file = fileInput.files[0]
        var formData = new FormData(form)
        // Append the file object to formData
        //formData.append("file", file)
        var xhr = new XMLHttpRequest()
        xhr.onreadystatechange = function() {
            
            if (xhr.readyState == 4) {

                try{
                    var jsonResponse = JSON.parse(xhr.responseText);
                    //console.log(jsonResponse);

                    if (typeof  jsonResponse.erro !== 'undefined') {
                        document.getElementById("bloco_conteudo_reativacao").innerHTML = jsonResponse.erro;
                        jQuery("#bl_enviar_reativacao").hide();
                        alert("Erro ao processar arquivo de Reativação em Lote. Arquivo TXT foi gerado com detalhamento dos problemas encontrados.");
                    } else if (typeof  jsonResponse.sucesso !== 'undefined') {
                        alert(jsonResponse.sucesso);
                    }

                } catch (e) {
                    console.log(e)
                    alert("Erro ao processar arquivo de Reativação em Lote. Arquivo TXT foi gerado com detalhamento dos problemas encontrados.");
                }
               
               jQuery('#btn_enviar_reativacao').attr('disabled', false); 
            }
        }
        xhr.open("POST",url, false)
        // Send request to server
        xhr.send(formData)
    }
        
</script>