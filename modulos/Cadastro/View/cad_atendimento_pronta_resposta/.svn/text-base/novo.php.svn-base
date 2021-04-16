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
                        <input type="hidden" name="tipo_arquivo_hidden" id="tipo_arquivo_hidden" value="1" /> 
                        
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
                                                <tr>
                                                    <td class="label">
                                                        <label>Data: *</label>
                                                    </td>
                                                    <td>
                                                        <input class="small" type="text" id="data" name="data" maxlength="10" onkeyup="formata_dt(this);" value="<?php echo date('d/m/Y'); ?>" />
                                                        <img src="images/calendar_cal.gif" align="absmiddle" border="0" alt="Calendário..." onclick="displayCalendar( document.getElementById('data'),'dd/mm/yyyy',this)" />
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td class="label">
                                                        <label>Hora do Acionamento: *</label>
                                                    </td>
                                                    <td>
                                                        <input class="small hour" type="text" id="hora_acionamento" name="hora_acionamento" />                                                        
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td class="label">
                                                        <label>Hora Chegada Local: *</label>
                                                    </td>
                                                    <td>
                                                        <input class="small hour" type="text" id="hora_chegada_local" name="hora_chegada_local" />                                                        
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td class="label">
                                                        <label>Hora Encerramento: *</label>
                                                    </td>
                                                    <td>
                                                        <input class="small hour" type="text" id="hora_encerramento" name="hora_encerramento" />                                                        
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
                                                        <input type="text" maxlength="8" name="cep" id="cep" onkeypress="javascript:return numero(event,false,false);" />
                                                        <label>Ex: 11920000</label>
                                                        <img id="cep_loader" class="loader" alt="" src="modulos/web/images/ajax-loader-circle.gif">
                                                    </td>
                                                </tr>
                                                <tr class="hide">
                                                    <td class="label">
                                                        <label>UF: *</label>
                                                    </td>
                                                    <td>
                                                        <select id="uf" name="uf"></select>
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
                                                            <input type="text" name="cidade" id="cidade" />                                                        
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
                                                            <input type="text" name="bairro" id="bairro" />
                                                        </div>
                                                        <img id="bairro_loader" class="loader float-left" alt="" src="modulos/web/images/ajax-loader-circle.gif">
                                                    </td>                                                    
                                                </tr>
                                                <tr class="hide">
                                                    <td class="label">
                                                        <label>Logradouro: *</label>
                                                    </td>
                                                    <td>
                                                        <input type="text" name="logradouro" id="logradouro" />
                                                    </td>
                                                </tr> 
                                                <tr class="hide">
                                                    <td class="label">
                                                        <label>Número: *</label>
                                                    </td>
                                                    <td>
                                                        <input type="text" name="end_numero" id="end_numero" />
                                                    </td>
                                                </tr>
                                                <tr class="hide">
                                                    <td class="label">
                                                        <label>Zona:</label>
                                                    </td>
                                                    <td>
                                                        <input type="text" name="zona" id="zona" />
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
                                                <tr>
                                                    <td class="label" nowrap>
                                                        <label>Latitude: *</label>
                                                    </td>
                                                    <td class="width-middle" nowrap>
                                                        <input type="text" name="latitude" id="latitude" maxlength="16" />                                                        
                                                    </td>
                                                    <td class="small-label" nowrap>
                                                        <label>Longitude: *</label>
                                                    </td>
                                                    <td nowrap nowrap>   
                                                        <input type="text" name="longitude" id="longitude" maxlength="16" />
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
                                                            <option value="<?php echo $operador['id_usuario'] ?>"><?php echo $operador['nome_usuario'] ?></option>
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
                                                            <option value="0">Cerca</option>
                                                            <option value="1">Roubo</option>
                                                            <option value="2">Furto</option>
                                                            <option value="3">Suspeita</option>
                                                            <option value="4">Sequestro</option>
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
                                                            <option value="0">Não</option>
                                                            <option value="1">Sim</option>                                                            
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
                                                        <input type="text" name="veiculo_placa" id="veiculo_placa" />
                                                    </td>                                                    
                                                </tr>
                                                <tr>
                                                    <td class="label">
                                                        <label>Cor: *</label>
                                                    </td>
                                                    <td>
                                                        <input type="text" name="veiculo_cor" id="veiculo_cor" />
                                                        <img class="veiculo_loader" alt="" src="modulos/web/images/ajax-loader-circle.gif">
                                                    </td>                                                    
                                                </tr>
                                                <tr>
                                                    <td class="label">
                                                        <label>Ano: *</label>
                                                    </td>
                                                    <td>
                                                        <input type="text" name="veiculo_ano" id="veiculo_ano" />
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
                                                            <option value="<?php echo $marca['descricao_marca'] ?>"><?php echo $marca['descricao_marca'] ?></option>
                                                            <?php endforeach; ?>
                                                        </select>                                                        
                                                        <img class="veiculo_loader" alt="" src="modulos/web/images/ajax-loader-circle.gif">
                                                    </td>                                                    
                                                </tr>
                                                <tr>
                                                    <td class="label">
                                                        <label>Modelo: *</label>
                                                    </td>
                                                    <td>
                                                    	<div id="div_veiculo_modelo" class="float-left">
                                                    		<select id='veiculo_modelo' name='veiculo_modelo'>
								    	                		<option value="">Escolha</option>
								    	                     </select>
                                                            <img class="loader_modelo" class="float-left" alt="" src="modulos/web/images/ajax-loader-circle.gif">
                                                    	</div>
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
                                                        <input type="text" name="carreta_placa" id="carreta_placa" />
                                                    </td>                                                    
                                                </tr>
                                                <tr>
                                                    <td class="label">
                                                        <label>Cor:</label>
                                                    </td>
                                                    <td>
                                                        <input type="text" name="carreta_cor" id="carreta_cor" />
                                                        <img class="veiculo_loader_carreta" alt="" src="modulos/web/images/ajax-loader-circle.gif">
                                                    </td>                                                    
                                                </tr>
                                                <tr>
                                                    <td class="label">
                                                        <label>Ano:</label>
                                                    </td>
                                                    <td>
                                                        <input type="text" name="carreta_ano" id="carreta_ano" />
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
                                                            <option value="<?php echo $marca['descricao_marca'] ?>"><?php echo $marca['descricao_marca'] ?></option>
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
                                                            </select>                                                          
                                                            <img class="loader_modelo_carreta" class="float-left" alt="" src="modulos/web/images/ajax-loader-circle.gif">
                                                        </div>
                                                    </td>                                                    
                                                </tr>
                                                <tr>
                                                    <td class="label">
                                                        <label>Carga:</label>
                                                    </td>
                                                    <td>
                                                        <input type="text" name="carreta_carga" id="carreta_carga" />
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
                                                        <input type="text" name="placa_veiculo_busca" id="placa_veiculo_busca" />
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
                                                        <textarea id="descricao_ocorrencia" name="descricao_ocorrencia"></textarea>
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
                                                        <input type="text" maxlength="8" name="cep_recup" id="cep_recup" onkeypress="javascript:return numero(event,false,false);" />
                                                        <label>Ex: 11920000</label>
                                                        <img id="cep_loader_recup" class="loader" alt="" src="modulos/web/images/ajax-loader-circle.gif">
                                                    </td>
                                                </tr>
                                                <tr class="hide_recup">
                                                    <td class="label">
                                                        <label>UF:</label>
                                                    </td>
                                                    <td>
                                                        <select id="uf_recup" name="uf_recup"></select>
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
                                                            <input type="text" name="cidade_recup" id="cidade_recup" />                                                        
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
                                                            <input type="text" name="bairro_recup" id="bairro_recup" />
                                                        </div>
                                                        <img id="bairro_loader_recup" class="loader float-left" alt="" src="modulos/web/images/ajax-loader-circle.gif">
                                                    </td>                                                    
                                                </tr>
                                                <tr class="hide_recup">
                                                    <td class="label">
                                                        <label>Logradouro:</label>
                                                    </td>
                                                    <td>
                                                        <input type="text" name="logradouro_recup" id="logradouro_recup" />
                                                    </td>
                                                </tr>
                                                <tr class="hide_recup">
                                                    <td class="label">
                                                        <label>Número: </label>
                                                    </td>
                                                    <td>
                                                        <input type="text" name="numero_recup" id="numero_recup" />
                                                    </td>
                                                </tr>
                                                <tr class="hide_recup">
                                                    <td class="label">
                                                        <label>Zona:</label>
                                                    </td>
                                                    <td>
                                                        <input type="text" name="zona_recup" id="zona_recup" />
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
                                                        <input type="text" name="destino_veiculo" id="destino_veiculo" />
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
                                <tr class="anexos">
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
                                                    <td>
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
                                <tr class="anexos">
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
                                            </tbody>
                                        </table>
                                    </td>
                                </tr>                                
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