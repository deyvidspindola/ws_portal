<!-- Arquivos do calendário para o campo Validade--> 
<script language="javascript" type="text/javascript" src="includes/js/calendar.js"></script>
<script language="Javascript" type="text/javascript" src="js/jquery.maskedinput.js"></script>
<link rel="stylesheet" href="calendar/calendar.css" media="screen"></link>
<link rel="stylesheet" href="/resources/demos/style.css">

<script language="javascript">
    jQuery('#divServico').hide();

    $(document).ready(function() {
        $('#validade').mask('99/99/9999',{placeholder:'_'});
    });

    function mostraValidade() {
        var cbSituacao = document.getElementById('situacao');

        if(cbSituacao.options[cbSituacao.selectedIndex].text == 'Demonstração') {
            document.getElementById('divValidade').style.visibility = 'visible';
            if(document.getElementById('validade').value == '') {
                var validade = new Date();
                validade.setDate(validade.getDate() + 60);

                var dia = validade.getDate();
                var mes = (validade.getMonth() + 1);
                var ano = validade.getFullYear();

                if(dia.toString().length < 2) {
                    dia = '0' + dia;
                }
                if(mes.toString().length < 2) {
                    mes = '0' + mes;
                }
                document.getElementById('validade').value = dia + '/' + mes + '/' + ano;
            }
        } else {
            document.getElementById('divValidade').style.visibility = 'hidden';
            closeCalendar();
        }
    }

    function alteraTipoServicoPacote() {
        var divPacote = jQuery('#divPacote');
        var divServico = jQuery('#divServico');

        jQuery("#servico")[0].selectedIndex = 0;
        jQuery("#pacote")[0].selectedIndex = 0;
        jQuery("#vlrMinimo").val('');
        jQuery("#vlrMaximo").val('');
        jQuery("#valor_tabela").val('');
        jQuery("#valor_negociado").val('');
        jQuery("#desconto").val('');
        jQuery("#contrato").val(''); // Seleciona o placeholder

        switch($('#tipo_serv_pac option:selected').val()) {
            case 'P':
                divPacote.show();
                divServico.hide();
                break;
            case 'F':
                divPacote.show();
                divServico.show();
                break;
            case 'A':
                divPacote.hide();
                divServico.show();
                break;
            default:
                break;
        }
    }

    function recarregaAutocompleteContrato() {

        var contratos = jQuery("#numerosContrato").val();
        var arrContratos = contratos.split(",");

        var source = $("#contrato").autocomplete("option", "source");
        $("#contrato").autocomplete("option", "source", arrContratos);
        
    }

    alteraTipoServicoPacote();
</script>

<div class="bloco_titulo">Dados do Cliente</div>
<div class="bloco_conteudo">
    <div class="formulario">
        <input type="hidden" name="id_termo" id="id_termo" value="<?=$dados_termo['taseoid']?>"/>
        <table width="100%">
            <tr>
                <td>            
                    <div class="campo maior">
                        <label for="cliente">Cliente:</label>
                        <input type="text" class="campo" style="z-index: 1" name="cliente" id="cliente" value="<?=$dados_termo['clinome']?>">                        
                    </div>
                    <div>
                        <button type="button" id="pesquisar_cliente">Pesquisar</button>
                    </div>
                    <!-- GIF: COMPONENTE PESQUISA -->
                    <div id="div_mini_loader">
                        <img width="16px" height="16px" src="images/loading_index.gif">
                    </div>
                    <!-- RESULTADO COMPONENTE PESQUISA -->
                    <div id="div_content_result_pesquisa"></div>
                    
                    <div class="clear"></div>
                    
                    <div class="campo">					
                        <label for="cpf_cnpj">CPF/CNPJ:</label>
                        <input type="text" class="campo" name="cpf_cnpj" id="cpf_cnpj" value="<?=$cpf_cnpj?>"/>
                    </div>
                    
                    <div class="clear"></div>
                    
                    <div class="campo">					
                        <label for="ta_servico">Nº do TA Serviço:</label>
                        <input type="text" class="campo" name="ta_servico" id="ta_servico" readonly="readonly" value="<?=$dados_termo['taseoid']?>"/>
                    </div>
                    
                    <div class="clear"></div>
                    
                    <div class="campo medio">					
                        <label for="situacao">Situação:</label>
                        <select name="situacao" id="situacao" onchange="mostraValidade();">
                            <option value="" <?php if(!empty($dados_termo['tasesituacao'])) { echo "disabled"; } ?>>Escolha</option>                            
                            <option value="C" <?php if(!empty($dados_termo['tasesituacao'])) {if($dados_termo['tasesituacao'] == 'C'){echo "selected='selected'";} else { echo "disabled"; }}?>>Cortesia</option>
                            <option value="D" <?php if(!empty($dados_termo['tasesituacao'])) {if($dados_termo['tasesituacao'] == 'D'){echo "selected='selected'";} else { echo "disabled"; }}?>>Demonstração</option>
                            <option value="M" <?php if(!empty($dados_termo['tasesituacao'])) {if($dados_termo['tasesituacao'] == 'M'){echo "selected='selected'";} else { echo "disabled"; }}?>>Faturamento Mensal</option>
                        </select>
                    </div>
                    <?php
                        $visibilidadeValidade = 'hidden';
                        $validade = '';
                        if($dados_termo['tasesituacao'] == 'D') {

                            $visibilidadeValidade = 'visible';

                            if(!empty($dados_termo['validade'])) {
                                $validade = date("d/m/Y", strtotime($dados_termo['validade']));;
                            } else {
                                $validade = date('d/m/Y', strtotime('+60 days'));
                            }
                        }
                    ?>
                    <div class="campo pequeno" id="divValidade" style="visibility:<?php echo $visibilidadeValidade; ?>;">                   
                            <label for="validade">Validade:</label>
                            <input type="text" class="campo" name="validade" id="validade" value="<?=$validade?>"/>&nbsp;
                            <img src="images/calendar_cal.gif" border="0" onclick="displayCalendar(document.getElementById('validade'),'dd/mm/yyyy',this)" align="top" alt="Calendário...">
                    </div>
                </td>
            </tr>
            <tr>


                <td id="td_fieldset">
                    <fieldset style="min-width:98%;">
                        <legend><strong>Itens</strong></legend>

                    <div class="campo medio">
                        <label for="tipo_serv_pac">Tipo:</label>
                        <select name="tipo_serv_pac" id="tipo_serv_pac" onchange="alteraTipoServicoPacote()">
                            <option value="A" selected>Serviço</option>
                            <option value="P">Pacote Rastreamento</option>
                            <option value="F">Funcionalidades Rastreamento</option>
                        </select>
                    </div>

                    <div class="clear"></div>
                    
                    <div class="campo medio" id="divPacote">                   
                        <label for="pacote">Pacote:</label>
                        <select name="pacote" id="pacote">
                            <option value="">Escolha</option>
                            <?php
                                if($pacote != null) {
                                    foreach ($pacote as $row) {
                                        echo "<option value='" . $row['obroid'] . "'>" . $row['obrobrigacao'] . "</option>";
                                    }
                                }
                            ?>
                        </select>
                    </div>

                    <div class="clear"></div>

                        <div class="campo medio" id="divServico" >					
                            <label for="servico">Serviço:</label>
                            <select name="servico" id="servico">
                                <option value="">Escolha</option>
                                <?php
                                    if($servico != null){
                                        foreach($servico as $row){                                            
                                            echo "<option value='".$row['obroid']."'>".$row['obrobrigacao']."</option>";
                                        }
                                    }
                                ?>
                            </select>
                        </div>
                        
                        <div class="clear"></div>
                        
                        <div class="campo medio">					
                            <input type="hidden" value="" id="numerosContrato" name="numerosContrato"/>
                            <label for="contrato">Contrato:</label>
                            </br>
                            <input id="contrato" name="contrato" onclick="recarregaAutocompleteContrato()">
                                <script>
                                var tags = ["0"];
                                $("#contrato").autocomplete( {
                                    source: function(request, response) {
                                        var matcher = new RegExp("^" + $.ui.autocomplete.escapeRegex(request.term), "i");
                                        response(
                                            $.grep(tags, function(item) {
                                                return matcher.test(item);
                                            })
                                        );
                                    }
                                });
                                $("#contrato").autocomplete("option", "autoFocus", true);
                                $("#contrato").autocomplete("option", "minLength", 2);
                                </script>
                            </input>
                        </div>
                        
                        <div class="clear"></div>
                        
                        <div class="campo menor">
                            <input type="hidden" id="vlrMinimo" name="vlrMinimo" value=""/>
                            <input type="hidden" id="vlrMaximo" name="vlrMaximo" value=""/>
                            <label for="valor_tabela">Valor de Tabela:</label>
                            <input type="text" id="valor_tabela" name="valor_tabela" value="0,00" class="campo" maxlength="10" onkeyup="moeda(this,2);"/>
                        </div>
                    
                        <div class="clear"></div>
                        
                        <div class="campo menor">					
                            <label for="valor_negociado">Valor Negociado:</label>
                            <input type="text" id="valor_negociado" name="valor_negociado" value="0,00" class="campo" maxlength="10" onkeyup="moeda(this,2);" <?php if($dados_termo['tasesituacao'] == 'D'){echo "readonly='readonly'";}?>/>
                        </div>
                    
                        <div class="clear"></div>
                        
                        <div class="campo menor">					
                            <label for="desconto">Desconto:</label>
                            <input type="text" id="desconto" name="desconto" value="0,00" class="campo" maxlength="10" onkeyup="moeda(this,2);"/>
                        </div>
                        
                        <div class="clear"></div>
                        
                         <div class="campo medio">	
	                         <fieldset class="menor">
								<legend>Tipo de Reajuste:</legend>
	                            
	                            <label for="reajuste_igpm">IGPM</label>
								<input id="reajuste_igpm" type="radio" checked="checked" value="1" name="tipo_reajuste">
								
								<label for="reajuste_inpc">INPC</label>
								<input id="reajuste_inpc" type="radio" value="2" name="tipo_reajuste">
	                         </fieldset>  
                        </div>
                        
                        <div class="clear"></div>
                        
                        <div class="campo menor">
                            <button id="adicionar_servico" type="button">Adicionar</button>
                        </div>
                        
                    </fieldset>
                </td>
            </tr>
            <?php
            // id do Termo Aditivo
            if($dados_termo['taseoid'] != ""){ ?>
            <tr>
                <td>
                    <div class="bloco_titulo">Itens cadastrados</div>
                    <div class="bloco_conteudo">
                        <div class="listagem">
                            <table id="tbl_itens_aditivos">
                                <thead>
                                    <tr>
                                        <th style="text-align:center;">Serviço</th>
                                        <th style="text-align:center;">Modalidade</th>
                                        <th style="text-align:center;">Contrato</th>
                                        <th style="text-align:center;">Placa</th>
                                        <th style="text-align:center;">Chassi</th>
                                        <th style="text-align:center;">Valor</th>
                                        <th style="text-align:center;">&nbsp;</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                        if($itens_termo != null){
                                            // AO ALTERAR O ID DO LINK QUE REMOVE O ITEM,
                                            //ALTERAR TAMBÉM NO ARQUIVO: PrnTermoAditivoServicoView.class.php
                                            //na função: getLinhaItemAditivo
                                            $i = 0;
                                            
                                            foreach($itens_termo as $row){
                                                $class      = !($i % 2) ? "par" : "";
                                                $id_item    = $row['taseioid'];                                                
                                                $result     = $dao->getServico($row['taseiobroid'], null, null, true);
                                                $servico    = $result[0]['obrobrigacao'];
                                                $modalidade = $result[0]['modalidade'];
                                                $placa      = "&nbsp;";
                                                $chassi     = "&nbsp;";
                                                
                                                if($row['taseiconnumero'] != ""){                                       
                                                    $result = $dao->getDadosVeiculo($row['taseiconnumero']);
                                
                                                    if($result != null){
                                                        $placa  = $result['veiplaca'];
                                                        $chassi = $result['veichassi'];
                                                    }
                                                }
                                                
                                                echo "<tr class='$class'>
                                                        <td>".$servico."</td>
                                                        <td>".$modalidade."</td>
                                                        <td>".$row['taseiconnumero']."</td>
                                                        <td>".$placa."</td>
                                                        <td>".$chassi."</td>
                                                        <td align='right'>".number_format($row['taseivalor_negociado'], 2, ',', '.')."</td>
                                                        <td style='text-align:center;'>
                                                            <a href='javascript:void(0);' id='lnk_remove_item_$id_item'>
                                                                <img width='13' height='12' align='absmiddle' title='Remover' alt='Remover' src='images/del.gif'>
                                                            </a>
                                                        </td>
                                                      </tr>";
                                                $i++;
                                            }
                                        }
                                    ?>
                                </tbody>
                                <tfoot>							
                                    <tr>
                                        <!-- Total de registros -->
                                        <td style="text-align:center;" colspan="7"><?=$total_itens?></td>                
                                    </tr>					
                                </tfoot>
                            </table>
                        </div>
                    </div>
                    <div class="bloco_acoes"></div>
                </td>
            </tr>            
    <?php   } ?>
            <tr>
                <td>
                    <div class="campo medio">					
                        <label for="status">Status:</label>
                        <select name="status" id="status">
                            <option value="">Escolha</option>    
                            <?php
                                if($status != null){
                                    $selected = "";
                                    foreach($status as $row){
                                        if($dados_termo['tasetasesoid'] == $row['tasesoid']){
                                            $selected = "selected='selected'";
                                        } else{
                                            $selected = "";
                                        }
                                        
                                        echo "<option value='".$row['tasesoid']."' ".$selected.">".$row['tasesdescricao']."</option>";
                                    }
                                }
                            ?>
                        </select>
                    </div>
                </td>
            </tr>
        </table>
    </div>
</div>
<div class="bloco_acoes">
    <button type="button" id="confirmar">Confirmar</button>
    <button type="button" id="excluir_termo">Excluir Termo Aditivo</button>
    <button type="button" id="retornar" onclick="location.reload();">Retornar</button>
</div>