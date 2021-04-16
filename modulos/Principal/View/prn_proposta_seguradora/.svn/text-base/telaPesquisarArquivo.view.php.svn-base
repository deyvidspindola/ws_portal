<?php include 'header.php';?>
<form name="form" id="form" method="post" action="prn_proposta_seguradora_arquivos.php" enctype="multipart/form-data" >
    <input type="hidden" name="caminho_arquivo" id="caminho_arquivo" value=""/>
    <input type="hidden" name="acao" id="acao" value=""/>
    <br/>
    <div align="center">
        <table class="tableMoldura">
            <tr class="tableTitulo">
                <td>
                    <h1>Arquivos</h1>
                </td>
            </tr>
            <?php $this->getAbas();?>
            <tr>
                <td align="center" valign="top">
                    <table class="tableMoldura" id="filtro_pesquisa">
                        <tr class="tableSubTitulo">
                            <td colspan="4">
                                <h2>Dados para Pesquisa</h2>
                            </td>
                        </tr>
                        <tr>
                            <td width="10%"><label>Período:</label></td>
                            <td width="40%">
                                <input type="text" id="prpsdt_ultima_acao_inicio_busca" name="prpsdt_ultima_acao_inicio_busca" value="<?php echo $prpsdt_ultima_acao_inicio_busca;?>" size="10" maxlength="10" onKeyUp="formata_dt(this);" onBlur="ValidaDataPC(prpsdt_ultima_acao_inicio_busca);">
                                <img src="images/calendar_cal.gif" align="absmiddle" border="0" alt="Calendário..." onclick="displayCalendar(document.forms[0].prpsdt_ultima_acao_inicio_busca,'dd/mm/yyyy',this)">
                                &nbsp; a &nbsp;
                                <input type="text" id="prpsdt_ultima_acao_final_busca" name="prpsdt_ultima_acao_final_busca" value="<?php echo $prpsdt_ultima_acao_final_busca;?>" size="10" maxlength="10" onKeyUp="formata_dt(this);" onBlur="ValidaDataPC(prpsdt_ultima_acao_final_busca);">
                                <img src="images/calendar_cal.gif" align="absmiddle" border="0" alt="Calendário..." onclick="displayCalendar(document.forms[0].prpsdt_ultima_acao_final_busca,'dd/mm/yyyy',this)">
                            </td>
                            <td width="10%"><label>Tipo Arquivo:</label></td>
                            <td>
                                <select id="slc_tipo_arquivo">
                                    <option value="">Escolha</option>
                                    <option value="1">Processado (Recebido)</option>
                                    <option value="2">Retorno (Enviado)</option>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <label>Tipo Contrato:</label>
                            </td>
                            <td>
                                <select id="slc_tipo_contrato">
                                    <option value="">Escolha</option>
                                    <?php
                                        if($tipoContrato != null){
                                            foreach($tipoContrato as $row){
                                                echo '<option value="'.$row['valor'].'">'.$row['desc'].'</option>';
                                            }
                                        }
                                    ?>
                                </select>
                            </td>
                            <td width="10%"><label>Status:</label></td>
                            <td>
                                <select id="slc_status">
                                    <option value="">Escolha</option>
                                    <option value="P">Processado</option>
                                    <option value="N">Não Processado</option>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="4">&nbsp;</td>
                        </tr>
                        <tr class="tableRodapeModelo1">
                            <td colspan="4" align="center">                                
                                <input type="button" name="bt_pesquisar" id="bt_pesquisar" class="botao" value="Pesquisar"/>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
        <div id="resultadoConteudo">

        </div>
    </div>
</form>
