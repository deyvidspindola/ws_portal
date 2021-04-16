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
                                                        <?php echo $this->atendimento['aprovado'] ?>
                                                    </td>
                                                </tr>
                                                <?php endif; ?>
                                                <tr>
                                                    <td class="label">
                                                        <label>Data: </label>
                                                    </td>
                                                    <td>                                                       
                                                        <?php echo $this->atendimento['data_atendimento'] ?>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td class="label">
                                                        <label>Hora do Acionamento: </label>
                                                    </td>
                                                    <td>                                                        
                                                        <?php echo $this->atendimento['hora_acionamento'] ?>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td class="label">
                                                        <label>Hora Chegada Local: </label>
                                                    </td>
                                                    <td>                                                        
                                                        <?php echo $this->atendimento['hora_chegada'] ?>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td class="label">
                                                        <label>Hora Encerramento: </label>
                                                    </td>
                                                    <td>                                                        
                                                        <?php echo $this->atendimento['hora_encerramento'] ?>
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
                                                        <label>CEP: </label>
                                                    </td>
                                                    <td>                                                        
                                                        <?php echo $this->atendimento['cep'] ?>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td class="label">
                                                        <label>UF: </label>
                                                    </td>
                                                    <td>                                                        
                                                        <?php echo $this->atendimento['uf'] ?>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td class="label">
                                                        <label>
                                                            Cidade:                                                           
                                                        </label>
                                                    </td>
                                                    <td>                                                        
                                                        <?php echo utf8_decode($this->atendimento['cidade']) ?>
                                                    </td>                                                    
                                                </tr> 
                                                <tr>
                                                    <td class="label">
                                                        <label>Bairro: </label>
                                                    </td>
                                                    <td>                                                        
                                                        <?php echo utf8_decode($this->atendimento['bairro']) ?>
                                                    </td>                                                    
                                                </tr>
                                                <tr>
                                                    <td class="label">
                                                        <label>Logradouro: </label>
                                                    </td>
                                                    <td>                                                        
                                                        <?php echo utf8_decode($this->atendimento['logradouro']) ?>
                                                    </td>
                                                </tr> 
                                                <tr>
                                                    <td class="label">
                                                        <label>Número: </label>
                                                    </td>
                                                    <td>                                                        
                                                        <?php echo $this->atendimento['end_numero'] ?>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td class="label">
                                                        <label>Zona:</label>
                                                    </td>
                                                    <td>                                                        
                                                        <?php echo $this->atendimento['zona'] ?>
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
                                                        <label>Cliente:</label>
                                                    </td>
                                                    <td>
                                                        <?php echo utf8_decode($this->atendimento['cliente']) ?>
                                                    </td>                                                    
                                                </tr>
                                                <?php endif; ?>
                                                <tr>
                                                    <td class="label">
                                                        <label>Latitude: </label>
                                                    </td>
                                                    <td class="width-middle">                                                        
                                                        <?php echo $this->atendimento['latitude'] ?>
                                                    </td>
                                                    <td class="small-label">
                                                        <label>Longitude: </label>
                                                    </td>
                                                    <td>                                                        
                                                        <?php echo $this->atendimento['longitude'] ?>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td class="label">
                                                        <label>Operador Sascar: </label>
                                                    </td>
                                                    <td>                                                        
                                                        <?php echo $this->atendimento['nome_operador'] ?>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td class="label">
                                                        <label>Tipo de Ocorrência: </label>
                                                    </td>
                                                    <td>                                                        
                                                        <?php echo $this->atendimento['tipo'] ?>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td class="label">
                                                        <label>Recuperado: </label>
                                                    </td>
                                                    <td>                                                        
                                                        <?php echo $this->atendimento['recuperado'] ?>
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
                                                        <label>Placa: </label>
                                                    </td>
                                                    <td>                                                        
                                                        <?php echo $this->atendimento['veiculo_placa'] ?>
                                                    </td>                                                    
                                                </tr>
                                                <tr>
                                                    <td class="label">
                                                        <label>Cor: </label>
                                                    </td>
                                                    <td>                                                        
                                                        <?php echo $this->atendimento['veiculo_cor'] ?>
                                                    </td>                                                    
                                                </tr>
                                                <tr>
                                                    <td class="label">
                                                        <label>Ano: </label>
                                                    </td>
                                                    <td>                                                        
                                                        <?php echo $this->atendimento['veiculo_ano'] ?>
                                                    </td>                                                    
                                                </tr>
                                                <tr>
                                                    <td class="label">
                                                        <label>Marca: </label>
                                                    </td>
                                                    <td>                                                        
                                                        <?php echo $this->atendimento['veiculo_marca'] ?>
                                                    </td>                                                    
                                                </tr>
                                                <tr>
                                                    <td class="label">
                                                        <label>Modelo: </label>
                                                    </td>
                                                    <td>                                                        
                                                        <?php echo $this->atendimento['veiculo_modelo'] ?>
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
                                                        <?php echo $this->atendimento['carreta_placa'] ?>
                                                    </td>                                                    
                                                </tr>
                                                <tr>
                                                    <td class="label">
                                                        <label>Cor:</label>
                                                    </td>
                                                    <td>                                                        
                                                        <?php echo $this->atendimento['carreta_cor'] ?>
                                                    </td>                                                    
                                                </tr>
                                                <tr>
                                                    <td class="label">
                                                        <label>Ano:</label>
                                                    </td>
                                                    <td>                                                        
                                                        <?php echo $this->atendimento['carreta_ano'] ?>
                                                    </td>                                                    
                                                </tr> 
                                                <tr>
                                                    <td class="label">
                                                        <label>Marca:</label>
                                                    </td>
                                                    <td>                                                        
                                                        <?php echo $this->atendimento['carreta_marca'] ?>
                                                    </td>                                                    
                                                </tr>
                                                <tr>
                                                    <td class="label">
                                                        <label>Modelo:</label>
                                                    </td>
                                                    <td>                                                        
                                                        <?php echo $this->atendimento['carreta_modelo'] ?>
                                                    </td>                                                    
                                                </tr>
                                                <tr>
                                                    <td class="label">
                                                        <label>Carga:</label>
                                                    </td>
                                                    <td>                                                        
                                                        <?php echo utf8_decode($this->atendimento['carreta_carga']) ?>
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
                                                        <label>Placa do veículo utilizado nas buscas: </label>
                                                    </td>
                                                    <td>                                                        
                                                        <?php echo $this->atendimento['placa_busca'] ?>
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
                                                        <label>Descrição: </label>
                                                    </td>
                                                    <td>
                                                        <?php echo $this->atendimento['descricao'] ?>
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
                                                        <?php echo $this->atendimento['cep_recup'] ?>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td class="label">
                                                        <label>UF:</label>
                                                    </td>
                                                    <td>                                                        
                                                        <?php echo $this->atendimento['uf_recup'] ?>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td class="label">
                                                        <label>
                                                            Cidade:                                                            
                                                        </label>
                                                    </td>
                                                    <td>                                                     
                                                        <?php echo utf8_decode($this->atendimento['cidade_recup']) ?>
                                                    </td>                                                    
                                                </tr> 
                                                <tr>
                                                    <td class="label">
                                                        <label>Bairro:</label>
                                                    </td>
                                                    <td>                                                                                                                   
                                                        <?php echo utf8_decode($this->atendimento['bairro_recup']) ?>
                                                    </td>                                                    
                                                </tr>
                                                <tr>
                                                    <td class="label">
                                                        <label>Logradouro:</label>
                                                    </td>
                                                    <td>
                                                        <?php echo utf8_decode($this->atendimento['logradouro_recup']) ?>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td class="label">
                                                        <label>Número: </label>
                                                    </td>
                                                    <td>                                                        
                                                        <?php echo $this->atendimento['numero_recup'] ?>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td class="label">
                                                        <label>Zona:</label>
                                                    </td>
                                                    <td>                                                        
                                                        <?php echo $this->atendimento['zona_recup'] ?>
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
                                                        <?php echo utf8_decode($this->atendimento['destino_veiculo']) ?>
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
                                                        <h2>Arquivos Anexados</h2>
                                                    </td>
                                                </tr>
                                                <tr class="tableTituloColunas">                                                   
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
            <tr class="tableRodapeModelo1">
                <td align="center" colspan="2">                    
                    <input type="button" id="voltar" class="botao" value="Voltar" name="voltar" onclick="window.location = 'cad_atendimento_pronta_resposta.php'" />                                    
                </td>
            </tr>
        </table>    
    </div>
</body>
<?php include "lib/rodape.php"; ?>