<?php

/*
 * Cabeçalho e Estilos
 * */
cabecalho();
include("calendar/calendar.js");
require("lib/funcoes.js");
?>

<!-- Cabeçalho -->
<?php require _MODULEDIR_ . 'Cadastro/View/cad_atendimento_pronta_resposta/head.php' ?>

<body>        
    <div align="center">
        <br />        
        <table width="100%" border="0" cellspacing="0" cellpadding="3" align="center" class="tableMoldura">
            <tr class="tableTitulo">
                <td><h1>Cadastro de Atendimentos Pronta Resposta</h1></td>
            </tr>
            <tr>
                <td>&nbsp;</span></td>
            </tr>                
            <tr>
                <td align="center">
                    <form id="cadastro" method="post" action="cad_atendimento_pronta_resposta.php">
                        <input type="hidden" name="acao" id="acao" value="" />
                        <input type="hidden" name="preroid" id="preroid" value="<?php echo $this->atendimento['id_atendimento'] ?>" />
                        <table class="tableMoldura dados_principais" style="">
                            <tbody>
                                <tr class="tableSubTitulo">
                                    <td><h2>RELATÓRIO DE ATENDIMENTO SASCAR</h2></td>
                                </tr>
                                <tr>
                                    <td>&nbsp;</td>
                                </tr>
                                <tr>
                                    <td align="center">
                                        <table class="tableMoldura">
                                            <tbody>
                                                <tr class="tableSubTitulo">
                                                    <td colspan="2">
                                                        <h2>Dados principais</h2>
                                                    </td>
                                                </tr>
                                                <?php if($_SESSION['funcao']['permissao_total_ocorrencia']): ?>
                                                <tr>
                                                    <td class="label">
                                                        <label>Aprovação:</label>
                                                    </td>
                                                    <td>
                                                        <select class="small" id="aprovacao" name="aprovacao">                                                            
                                                            <option value="">Escolha</option>
                                                            <option <?php echo $this->atendimento['aprovado'] == 't' ? 'selected="selected"' : ''?> value="1">Aprovado</option>
                                                            <option <?php echo $this->atendimento['aprovado'] == 'f' ? 'selected="selected"' : ''?> value="0">Rejeitado</option>
                                                        </select>
                                                    </td>
                                                </tr>
                                                <?php endif; ?>
                                                <tr>
                                                    <td class="label">
                                                        <label>Data: *</label>
                                                    </td>
                                                    <td>
                                                        <input class="small" type="text" id="data" name="data" maxlength="10" onkeyup="formata_dt(this);" value="<?php echo $this->atendimento['data_atendimento'] ?>" />
                                                        <img src="images/calendar_cal.gif" align="absmiddle" border="0" alt="Calendário..." onclick="displayCalendar( document.getElementById('data'),'dd/mm/yyyy',this)" />
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td class="label">
                                                        <label>Hora do Acionamento: *</label>
                                                    </td>
                                                    <td>                                                        
                                                        <input class="small hour" type="text" id="hora_acionamento" name="hora_acionamento" value="<?php echo $this->atendimento['hora_acionamento'] ?>" />                                                        
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td class="label">
                                                        <label>Hora Chegada Local: *</label>
                                                    </td>
                                                    <td>
                                                        <input class="small hour" type="text" id="hora_chegada_local" name="hora_chegada_local" value="<?php echo $this->atendimento['hora_chegada'] ?>" />                                                        
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td class="label">
                                                        <label>Hora Encerramento: *</label>
                                                    </td>
                                                    <td>
                                                        <input class="small hour" type="text" id="hora_encerramento" name="hora_encerramento" value="<?php echo $this->atendimento['hora_encerramento'] ?>" />                                                        
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </td>
                                </tr>
                                <tr>
                                    <td>&nbsp;</td>
                                </tr>
                                <tr>
                                    <td align="center">
                                        <table class="tableMoldura">
                                            <tbody>
                                                <tr class="tableSubTitulo">
                                                    <td colspan="2">
                                                        <h2>Local do Acionamento</h2>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td class="label">
                                                        <label>CEP: *</label>
                                                    </td>
                                                    <td>
                                                        <input type="text" maxlength="8" name="cep" id="cep" onkeypress="javascript:return numero(event,false,false);" value="<?php echo $this->atendimento['cep'] ?>"/>
                                                        <label>Ex: 11920000</label>
                                                        <img id="cep_loader" class="loader" alt="" src="modulos/web/images/ajax-loader-circle.gif">
                                                    </td>
                                                </tr>
                                                <tr class="hide">
                                                    <td class="label">
                                                        <label>UF: *</label>
                                                    </td>
                                                    <td>                                                        
                                                        <input type="text" id="uf" name="uf" value="<?php echo $this->atendimento['uf'] ?>" readonly="readonly" />
                                                    </td>
                                                </tr>
                                                <tr class="hide">
                                                    <td class="label">
                                                        <label>
                                                            Cidade: *                                                          
                                                        </label>
                                                    </td>
                                                    <td>
                                                        <div class="float-left">
                                                            <input type="text" name="cidade" id="cidade" value="<?php echo utf8_decode($this->atendimento['cidade']) ?>" readonly="readonly" />                                                        
                                                        </div>
                                                        <img id="cidade_loader" class="loader float-left" alt="" src="modulos/web/images/ajax-loader-circle.gif">
                                                    </td>                                                    
                                                </tr> 
                                                <tr class="hide">
                                                    <td class="label">
                                                        <label>Bairro: *</label>
                                                    </td>
                                                    <td>
                                                        <div class="float-left">
                                                            <input type="text" name="bairro" id="bairro" value="<?php echo utf8_decode($this->atendimento['bairro']) ?>" />
                                                        </div>
                                                        <img id="bairro_loader" class="loader float-left" alt="" src="modulos/web/images/ajax-loader-circle.gif">
                                                    </td>                                                    
                                                </tr>
                                                <tr class="hide">
                                                    <td class="label">
                                                        <label>Logradouro: *</label>
                                                    </td>
                                                    <td>
                                                        <input type="text" name="logradouro" id="logradouro" value="<?php echo utf8_decode($this->atendimento['logradouro']) ?>" />
                                                    </td>
                                                </tr> 
                                                <tr class="hide">
                                                    <td class="label">
                                                        <label>Número: *</label>
                                                    </td>
                                                    <td>
                                                        <input type="text" name="end_numero" id="end_numero" onkeypress="javascript:return numero(event,false,false);" value="<?php echo $this->atendimento['end_numero'] ?>" />
                                                    </td>
                                                </tr>
                                                <tr class="hide">
                                                    <td class="label">
                                                        <label>Zona:</label>
                                                    </td>
                                                    <td>
                                                        <input type="text" name="zona" id="zona" value="<?php echo $this->atendimento['zona'] ?>" />
                                                    </td>
                                                </tr>                                                                                               
                                            </tbody>
                                        </table>
                                    </td>
                                </tr>
                                <tr>
                                    <td>&nbsp;</td>
                                </tr>
                                <tr>
                                    <td align="center">
                                        <table class="tableMoldura">
                                            <tbody>
                                                <tr class="tableSubTitulo">
                                                    <td colspan="4">
                                                        <h2>Dados da Ocorrência</h2>
                                                    </td>
                                                </tr>
                                                <?php if($_SESSION['funcao']['permissao_total_ocorrencia']): ?>
                                                <tr>
                                                    <td class="label">
                                                        <label>Cliente: *</label>
                                                    </td>
                                                    <td>
                                                        <input type="text" name="cliente" id="cliente" value="<?php echo utf8_decode($this->atendimento['cliente']) ?>" />
                                                    </td>                                                    
                                                </tr>
                                                <?php endif; ?>
                                                <tr>
                                                    <td class="label" nowrap>
                                                        <label>Latitude: *</label>
                                                    </td>
                                                    <td class="width-middle" nowrap>
                                                        <input type="text" name="latitude" id="latitude" maxlength="16" value="<?php echo $this->latitude ?>" />                                                                                                                
                                                    </td>
                                                    <td class="small-label" nowrap>
                                                        <label>Longitude: *</label>
                                                    </td>
                                                    <td nowrap>   
                                                        <input type="text" name="longitude" id="longitude" maxlength="16" value="<?php echo $this->longitude ?>" />
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td class="label">
                                                        <label>Operador Sascar: *</label>
                                                    </td>
                                                    <td>
                                                        <select id="operador_sascar" name="operador_sascar">
                                                            <option value="">Escolha</option>
                                                            <?php foreach($this->operadores_sascar as $operador): ?>
                                                            <option <?php echo $this->atendimento['id_operador'] == $operador['id_usuario'] ? 'selected="selected"' : '' ?> value="<?php echo $operador['id_usuario'] ?>"><?php echo $operador['nome_usuario'] ?></option>
                                                            <?php endforeach; ?>
                                                        </select>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td class="label">
                                                        <label>Tipo de Ocorrência: *</label>
                                                    </td>
                                                    <td>
                                                        <select class="small" name="tipo_ocorrencia" id="tipo_ocorrencia">                                                                                                  
                                                            <option <?php echo $this->atendimento['tipo'] == 0 ? 'selected="selected"' : '' ?> value="0">Cerca</option>
                                                            <option <?php echo $this->atendimento['tipo'] == 1 ? 'selected="selected"' : '' ?> value="1">Roubo</option>
                                                            <option <?php echo $this->atendimento['tipo'] == 2 ? 'selected="selected"' : '' ?> value="2">Furto</option>
                                                            <option <?php echo $this->atendimento['tipo'] == 3 ? 'selected="selected"' : '' ?> value="3">Suspeita</option>
                                                            <option <?php echo $this->atendimento['tipo'] == 4 ? 'selected="selected"' : '' ?> value="4">Sequestro</option>
                                                        </select>   
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td class="label">
                                                        <label>Recuperado: </label>
                                                    </td>
                                                    <td>
                                                        <select class="small" name="recuperado" id="recuperado">
                                                            <option value="">Escolha</option>
                                                            <option <?php echo $this->atendimento['recuperado'] == 'f' ? 'selected="selected"' : '' ?> value="0">Não</option>
                                                            <option <?php echo $this->atendimento['recuperado'] == 't' ? 'selected="selected"' : '' ?> value="1">Sim</option>                                                            
                                                        </select>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </td>
                                </tr>
                                <tr>
                                    <td>&nbsp;</td>
                                </tr>
                                <tr>
                                    <td align="center">
                                        <table class="tableMoldura">
                                            <tbody>
                                                <tr class="tableSubTitulo">
                                                    <td colspan="4">
                                                        <h2>Veículo</h2>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td class="label">
                                                        <label>Placa: *</label>
                                                    </td>
                                                    <td>
                                                        <input type="text" name="veiculo_placa" id="veiculo_placa" value="<?php echo $this->atendimento['veiculo_placa'] ?>" />
                                                    </td>                                                    
                                                </tr>
                                                <tr>
                                                    <td class="label">
                                                        <label>Cor: *</label>
                                                    </td>
                                                    <td>
                                                        <input type="text" name="veiculo_cor" id="veiculo_cor" value="<?php echo $this->atendimento['veiculo_cor'] ?>" />
                                                        <img class="veiculo_loader" alt="" src="modulos/web/images/ajax-loader-circle.gif">
                                                    </td>                                                    
                                                </tr>
                                                <tr>
                                                    <td class="label">
                                                        <label>Ano: *</label>
                                                    </td>
                                                    <td>
                                                        <input type="text" name="veiculo_ano" id="veiculo_ano" value="<?php echo $this->atendimento['veiculo_ano'] ?>" />
                                                        <img class="veiculo_loader" alt="" src="modulos/web/images/ajax-loader-circle.gif">
                                                    </td>                                                    
                                                </tr>
                                                <tr>
                                                    <td class="label">
                                                        <label>Marca: *</label>
                                                    </td>
                                                    <td>
                                                        
                                                            <select id="veiculo_marca" name="veiculo_marca">
                                                                <option value="">Escolha</option>
                                                                 <?php foreach($this->marcas as $marca): ?>
                                                                <option value="<?php echo $marca['descricao_marca'] ?>" <?php if ($this->atendimento['veiculo_marca'] == $marca['descricao_marca']){echo "SELECTED";} ?>><?php echo $marca['descricao_marca'] ?></option>
                                                                <?php endforeach; ?>
                                                            </select>
                                                        <img class="veiculo_loader" class="float-left" alt="" src="modulos/web/images/ajax-loader-circle.gif">
                                                    </td>                                                    
                                                </tr>
                                                <tr>
                                                    <td class="label">
                                                        <label>Modelo: *</label>
                                                    </td>
                                                    <td>
                                                        <div id="div_veiculo_modelo" class="float-left">
                                                            <select id="veiculo_modelo" name="veiculo_modelo">
                                                                <option value="">Escolha</option>
                                                                     <?php foreach($this->modelos_veiculo as $modelo): ?>
                                                                    <option value="<?php echo $modelo['modelo'] ?>" <?php if ($this->atendimento['veiculo_modelo'] == $modelo['modelo']){echo "SELECTED";} ?>><?php echo $modelo['modelo'] ?></option>
                                                                    <?php endforeach; ?>                                                          
                                                            </select>    
                                                        </div>    
                                                        <img class="loader_modelo" alt="" src="modulos/web/images/ajax-loader-circle.gif">
                                                    </td>                                                    
                                                </tr>                                                
                                            </tbody>
                                        </table>
                                    </td>
                                </tr>
                                <tr>
                                    <td>&nbsp;</td>
                                </tr>
                                <tr>
                                    <td align="center">
                                        <table class="tableMoldura">
                                            <tbody>
                                                <tr class="tableSubTitulo">
                                                    <td colspan="4">
                                                        <h2>Carreta</h2>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td class="label">
                                                        <label>Placa:</label>
                                                    </td>
                                                    <td>
                                                        <input type="text" name="carreta_placa" id="carreta_placa" value="<?php echo $this->atendimento['carreta_placa'] ?>" />
                                                    </td>                                                    
                                                </tr>
                                                <tr>
                                                    <td class="label">
                                                        <label>Cor:</label>
                                                    </td>
                                                    <td>
                                                        <input type="text" name="carreta_cor" id="carreta_cor" value="<?php echo $this->atendimento['carreta_cor'] ?>" />
                                                        <img class="veiculo_loader_carreta" alt="" src="modulos/web/images/ajax-loader-circle.gif">
                                                    </td>                                                    
                                                </tr>
                                                <tr>
                                                    <td class="label">
                                                        <label>Ano:</label>
                                                    </td>
                                                    <td>
                                                        <input type="text" name="carreta_ano" id="carreta_ano" value="<?php echo $this->atendimento['carreta_ano'] ?>" />
                                                        <img class="veiculo_loader_carreta" alt="" src="modulos/web/images/ajax-loader-circle.gif">
                                                    </td>                                                    
                                                </tr> 
                                                <tr>
                                                    <td class="label">
                                                        <label>Marca:</label>
                                                    </td>
                                                    <td>
                                                        <select id="carreta_marca" name="carreta_marca">
                                                            <option value="">Escolha</option>
                                                             <?php foreach($this->marcas as $marca): ?>
                                                            <option value="<?php echo $marca['descricao_marca'] ?>" <?php if ($this->atendimento['carreta_marca'] == $marca['descricao_marca']){echo "SELECTED";} ?>><?php echo $marca['descricao_marca'] ?></option>
                                                            <?php endforeach; ?>
                                                        </select>                                                        
                                                        <img class="veiculo_loader_carreta" alt="" src="modulos/web/images/ajax-loader-circle.gif">
                                                    </td>                                                    
                                                </tr>
                                                <tr>
                                                    <td class="label">
                                                        <label>Modelo:</label>
                                                    </td>
                                                    <td>
                                                        <div id="div_carreta_modelo" class="float-left">
                                                            <select id="carreta_modelo" name="carreta_modelo">
                                                                <option value="">Escolha</option>
                                                                 <?php foreach($this->modelos_carreta as $modelo): ?>
                                                                <option value="<?php echo $modelo['modelo'] ?>" <?php if ($this->atendimento['carreta_modelo'] == $modelo['modelo']){echo "SELECTED";} ?>><?php echo $modelo['modelo'] ?></option>
                                                                <?php endforeach; ?>                                                             
                                                            </select> 
                                                        </div>
                                                        <img class="loader_modelo_carreta" class="float-left" alt="" src="modulos/web/images/ajax-loader-circle.gif">
                                                    </td>                                                    
                                                </tr>
                                                <tr>
                                                    <td class="label">
                                                        <label>Carga:</label>
                                                    </td>
                                                    <td>
                                                        <input type="text" name="carreta_carga" id="carreta_carga" value="<?php echo utf8_decode($this->atendimento['carreta_carga']) ?>" />
                                                    </td>                                                    
                                                </tr>
                                            </tbody>
                                        </table>
                                    </td>
                                </tr>
                                <tr>
                                    <td>&nbsp;</td>
                                </tr>
                                <tr>
                                    <td align="center">
                                        <table class="tableMoldura">
                                            <tbody>
                                                <tr class="tableSubTitulo">
                                                    <td colspan="4">
                                                        <h2>Agente de Apoio</h2>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td class="label">
                                                        <label>Placa do veículo utilizado nas buscas: *</label>
                                                    </td>
                                                    <td>
                                                        <input type="text" name="placa_veiculo_busca" id="placa_veiculo_busca" value="<?php echo $this->atendimento['placa_busca'] ?>" />
                                                    </td>                                                    
                                                </tr>                                                
                                            </tbody>
                                        </table>
                                    </td>
                                </tr>
                                <tr>
                                    <td>&nbsp;</td>
                                </tr>
                                <tr>
                                    <td align="center">
                                        <table class="tableMoldura">
                                            <tbody>
                                                <tr class="tableSubTitulo">
                                                    <td colspan="4">
                                                        <h2>Descrição da Ocorrência</h2>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td class="label">
                                                        <label>Descrição: *</label>
                                                    </td>
                                                    <td>
                                                        <textarea id="descricao_ocorrencia" name="descricao_ocorrencia"><?php echo $this->atendimento['descricao'] ?></textarea>
                                                    </td>                                                    
                                                </tr>                                                
                                            </tbody>
                                        </table>
                                    </td>
                                </tr>
                                <tr>
                                    <td>&nbsp;</td>
                                </tr>
                                <tr>
                                    <td align="center">
                                        <table class="tableMoldura">
                                            <tbody>
                                                <tr class="tableSubTitulo">
                                                    <td colspan="2">
                                                        <h2>Endereço da Recuperação</h2>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td class="label">
                                                        <label>CEP:</label>
                                                    </td>
                                                    <td>
                                                        <input type="text" maxlength="8" name="cep_recup" id="cep_recup" onkeypress="javascript:return numero(event,false,false);" value="<?php echo $this->atendimento['cep_recup'] ?>" />
                                                        <label>Ex: 11920000</label>
                                                        <img id="cep_loader_recup" class="loader" alt="" src="modulos/web/images/ajax-loader-circle.gif">
                                                    </td>
                                                </tr>
                                                <tr class="hide_recup">
                                                    <td class="label">
                                                        <label>UF:</label>
                                                    </td>
                                                    <td>
                                                        <input type="text" name="uf_recup" id="uf_recup" value="<?php echo $this->atendimento['uf_recup'] ?>" readonly="readonly" />                                                        
                                                    </td>
                                                </tr>
                                                <tr class="hide_recup">
                                                    <td class="label">
                                                        <label>
                                                            Cidade:                                                            
                                                        </label>
                                                    </td>
                                                    <td>
                                                        <div class="float-left">
                                                            <input type="text" name="cidade_recup" id="cidade_recup" value="<?php echo $this->atendimento['cidade_recup'] ?>" readonly="readonly" />                                                        
                                                        </div>
                                                        <img id="cidade_loader_recup" class="loader float-left" alt="" src="modulos/web/images/ajax-loader-circle.gif">
                                                    </td>                                                    
                                                </tr> 
                                                <tr class="hide_recup">
                                                    <td class="label">
                                                        <label>Bairro:</label>
                                                    </td>
                                                    <td>
                                                        <div class="float-left">
                                                            <input type="text" name="bairro_recup" id="bairro_recup" value="<?php echo $this->atendimento['bairro_recup'] ?>" />
                                                        </div>
                                                        <img id="bairro_loader_recup" class="loader float-left" alt="" src="modulos/web/images/ajax-loader-circle.gif">
                                                    </td>                                                    
                                                </tr>
                                                <tr class="hide_recup">
                                                    <td class="label">
                                                        <label>Logradouro:</label>
                                                    </td>
                                                    <td>
                                                        <input type="text" name="logradouro_recup" id="logradouro_recup" value="<?php echo $this->atendimento['logradouro_recup'] ?>" />
                                                    </td>
                                                </tr>
                                                <tr class="hide_recup">
                                                    <td class="label">
                                                        <label>Número: </label>
                                                    </td>
                                                    <td>
                                                        <input type="text" name="numero_recup" id="numero_recup" onkeypress="javascript:return numero(event,false,false);" value="<?php echo $this->atendimento['numero_recup'] ?>" />
                                                    </td>
                                                </tr>
                                                <tr class="hide_recup">
                                                    <td class="label">
                                                        <label>Zona:</label>
                                                    </td>
                                                    <td>
                                                        <input type="text" name="zona_recup" id="zona_recup" value="<?php echo $this->atendimento['zona_recup'] ?>" />
                                                    </td>
                                                </tr>                                                                                               
                                            </tbody>
                                        </table>
                                    </td>
                                </tr>
                                <tr>
                                    <td>&nbsp;</td>
                                </tr>
                                <tr>
                                    <td align="center">
                                        <table class="tableMoldura">
                                            <tbody>
                                                <tr class="tableSubTitulo">
                                                    <td colspan="4">
                                                        <h2>Destinação do Veículo Pós Recuperação</h2>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td class="label">
                                                        <label>Destino:</label>
                                                    </td>
                                                    <td>
                                                        <input type="text" name="destino_veiculo" id="destino_veiculo" value="<?php echo utf8_decode($this->atendimento['destino_veiculo']) ?>" />
                                                    </td>                                                    
                                                </tr>                                                
                                            </tbody>
                                        </table>
                                    </td>
                                </tr>
                                <tr>
                                    <td>&nbsp;</td>
                                </tr>
                                <tr>
                                    <td style="text-align: center;"><span id="div_msg" class="msg"></td>
                                </tr>
                                <tr>
                                    <td>&nbsp;</td>
                                </tr>
                                <tr>
                                    <td align="center">
                                        <table class="tableMoldura">
                                            <tbody>
                                                <tr class="tableSubTitulo">
                                                    <td colspan="4">
                                                        <h2>Anexos</h2>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td class="label">
                                                        <label>Arquivo:</label>
                                                    </td>
                                                    <td id="input_file_arquivo">
                                                        <input type="file" name="arquivo" id="arquivo" />                                                        
                                                    </td>                                                    
                                                </tr>
                                                <tr>
                                                    <td class="label">
                                                        <label>Tipo Arquivo:</label>
                                                    </td>
                                                    <td>
                                                        <select id="tipo_arquivo" name="tipo_arquivo">
                                                            <option value="foto">Foto</option>
                                                            <option value="documento">Documento</option>
                                                        </select>
                                                    </td>                                                    
                                                </tr> 
                                            </tbody>
                                        </table>
                                    </td>
                                </tr>
                                <tr>
                                    <td>&nbsp;</td>
                                </tr>
                                <tr>
                                    <td align="center">
                                        <table class="tableMoldura" id="anexados">
                                            <tbody>
                                                <tr class="tableSubTitulo">
                                                    <td colspan="6">
                                                        <h2>Arquivos já anexados</h2>
                                                    </td>
                                                </tr>
                                                <tr class="tableTituloColunas">
                                                    <td>
                                                        <h3>Excluir</h3>
                                                    </td>
                                                    <td>
                                                        <h3>Pré-View</h3>
                                                    </td>
                                                    <td>
                                                        <h3>Data de Inclusão</h3>
                                                    </td>
                                                    <td>
                                                        <h3>Tipo</h3>
                                                    </td>
                                                    <td>
                                                        <h3>Arquivo</h3>
                                                    </td>
                                                    <td>
                                                        <h3>Usuário</h3>
                                                    </td>
                                                </tr>
                                                <?php foreach($this->anexos_atendimento as $anexo): ?>
                                                <tr class="result">
                                                    <td align="center">                                                        
                                                        <b>[</b><img id="<?php echo $anexo['lauaoid'] ?>" rel="<?php echo $anexo['nome_arquivo'] ?>" class="remover_anexo" align="absmiddle" height="12" width="13" title="Remover" alt="Remover" src="images/del.gif"><b>]</b>
                                                    </td>
                                                    <td align="center">
                                                        <a href="download.php?arquivo=/var/www/anexos_ocorrencia/<?php echo $anexo['lauapreroid'] ?>/<?php echo $anexo['nome_arquivo'] ?>">
                                                            <img class="preview_anexo" align="absmiddle" height="12" width="13" title="Preview" alt="Preview" src="images/icones/file.gif">
                                                        </a>                                                        
                                                    </td>
                                                    <td>
                                                        <?php echo $anexo['data_inclusao'] ?>                                                        
                                                    </td>
                                                    <td>
                                                        <?php echo $anexo['tipo_arquivo'] ?>
                                                    </td>
                                                    <td>
                                                        <?php echo $anexo['nome_arquivo'] ?>
                                                    </td>
                                                    <td>
                                                        <?php echo $anexo['usuario'] ?>
                                                    </td>
                                                </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </td>
                                </tr>
                                <?php if($_SESSION['funcao']['permissao_total_ocorrencia']): ?>
                                <tr>
                                    <td>&nbsp;</td>
                                </tr>
                                <tr>
                                    <td align="center">
                                        <input type="button" id="gerarPdf" class="botao" value="Gerar PDF" name="gerarPdf" />
                                        <input type="button" id="gerarDoc" class="botao" value="Gerar DOC" name="gerarDoc" />
                                    </td>
                                </tr>
                                <tr>
                                    <td>&nbsp;</td>
                                </tr>
                                <?php endif; ?>
                            </tbody>
                        </table> 
                    </form>                    
                </td>
            </tr>
            <!-- Botão para realizar a pesquisa -->
            <tr class="tableRodapeModelo1">
                <td align="center" colspan="2">
                    <input type="button" id="confirmar" class="botao" value="Confirmar" name="confirmar" />                                    
                    <input type="button" id="voltar" class="botao" value="Voltar" name="voltar" onclick="window.location = 'cad_atendimento_pronta_resposta.php'" />                                    
                </td>
            </tr>
        </table>    
    </div>
</body>
<?php include "lib/rodape.php"; ?>