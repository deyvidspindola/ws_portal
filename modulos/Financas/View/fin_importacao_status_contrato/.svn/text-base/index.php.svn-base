<form name="importa_informacoes" id="importa_informacoes" method="POST" enctype="multipart/form-data">
    
    <input type="hidden" name="acao" id="acao">
    
    <input type="hidden" name="importacao_status_contrato" id="importacao_status_contrato" value="<? echo $_SESSION['funcao']['Importacao_Status_Contrato']; ?>">
    
    <div align="center">
        
        <table class="tableMoldura">

            <tr class="tableTitulo">
                <td><h1>IMPORTAÇÃO - Status do Contrato</h1></td>
            </tr>

            <tr>
                <td><span id="msg" class="msg"><? echo $this->msg; ?></span>&nbsp;</td>
            </tr>

            <tr>
                <td align="center">

                        <table class="tableMoldura dados_pesquisa">                        

                            <tr class="tableSubTitulo">
                                <td colspan="2"><h2>Operação</h2></td>
                            </tr>

                            <tr>
                                <td width="50%">
                                    <input type="radio" name="par_operacao" value="valor_monitoramento" class="radio" <? if (!$this->par_operacao || $this->par_operacao == 'valor_monitoramento'){ echo "checked"; } ?>>
                                    <label class="label_radio">Altera o valor da obrigação financeira (Monitoramento)</label> 
                                </td>
                                <td rowspan="4">
                                    <fieldset>
                                        <legend>Base de Dados</legend>
                                        <table>
                                            
                                            <tr>
                                                <td>
                                                    <input type="radio" name="par_base" value="sascar" class="radio" <? if (!$this->par_base || $this->par_base == 'sascar'){ echo "checked"; } ?>> 
                                                    <label class="label_radio">SASCAR</label>
                                                </td>
                                            </tr>
                                            
                                            <tr>
                                                <td>
                                                    <input type="radio" name="par_base" value="sbtec" class="radio" <? if ($this->par_base == 'sbtec'){ echo "checked"; } ?>> 
                                                    <label class="label_radio">SBTEC</label>
                                                </td>
                                            </tr>
                                            
                                        </table>
                                    </fieldset>
                                </td>
                            </tr>

                            <tr>
                                <td>
                                    <input type="radio" name="par_operacao" value="valor_outras" class="radio" <? if ($this->par_operacao == 'valor_outras'){ echo "checked"; } ?>> 
                                    <label class="label_radio">Altera o valor das demais obrigações financeiras</label> 
                                </td>
                            </tr>

                            <tr>
                                <td>
                                    <input type="radio" name="par_operacao" value="status_stop" class="radio" <? if ($this->par_operacao == 'status_stop'){ echo "checked"; } ?>> 
                                    <label class="label_radio">Altera o status para STOP FATURAMENTO</label> 
                                </td>
                            </tr>

                            <tr>
                                <td>
                                    <input type="radio" name="par_operacao" value="status_ativo" class="radio" <? if ($this->par_operacao == 'status_ativo'){ echo "checked"; } ?>> 
                                    <label class="label_radio">Altera o status para ATIVO</label>
                                </td>
                            </tr>

                            <tr>
                                <td>
                                    <input type="radio" name="par_operacao" value="transf_titularidade" class="radio" <? if ($this->par_operacao == 'transf_titularidade'){ echo "checked"; } ?>> 
                                    <label class="label_radio">Alterar o status para Transferência de Titularidade</label>
                                </td>
                            </tr>

                            <tr>
                                <td colspan="2" height="50px">

                                    <label>Arquivo:*</label> 
                                    <input type="file" name="arquivo" id="arquivo">
                                    <?=desenhaHelpComment('Selecione o arquivo, extensão .csv e separador ponto e vírgula (;).');?>

                                </td>                                
                            </tr>

                        </table>

               </td>               
            </tr>

            <tr>
                <td>                    
                    <label>(*) Campos de preenchimento obrigatório.</label>
                </td>
            </tr>
            
            
            <tr>
                <td>
                    <div id="msg" class="loading">
                        <?php if ($this->exibeLogCSV){ ?>
                            <center>
                                <a href="download.php?arquivo=<? echo $this->nomeLogCsv; ?>">
                                <img src="images/icones/t3/caixa2.jpg"><br>Download do log de erros (CSV)
                                </a>
                            </center>
                            <br>
                        <?php } ?>
                    </div>
                </td>
            </tr>
            
            
            <tr class="tableRodapeModelo1" style="height:23px;">
                <td align="center">                    
                    <input type="button" name="btn_confirmar" id="btn_confirmar" value="Confirmar" class="botao">
                </td>
            </tr>

       </table>        
        
    </div> 
    
</form>
<?php include "lib/rodape.php"; ?>