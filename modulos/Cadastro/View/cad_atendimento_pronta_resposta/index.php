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
        <form id="form" method="post" action="cad_atendimento_pronta_resposta.php">
            <input type="hidden" name="acao" id="acao" value="" />
            <table width="100%" border="0" cellspacing="0" cellpadding="3" align="center" class="tableMoldura">
                <tr class="tableTitulo">
                    <td><h1>Cadastro de Atendimentos Pronta Resposta</h1></td>
                </tr>
                <tr>
                    <td>&nbsp;</td>
                </tr>                
                <tr>
                    <td align="center">
                        <table width="98%"  class="tableMoldura">
                            <tr class="tableSubTitulo">
                                <td colspan="8"><h2>Dados para pesquisa</h2></td>
                            </tr>
                            <tr>
                                <td colspan="8" align="center"><br></td>
                            </tr>  
                            <tr>                                
                                <td nowrap="nowrap" class="label"><label>Placa:</label></td>
                                <td nowrap="nowrap" style="text-align:left;">
                                    <input maxlength="15" style="width:268px;" type="text" id="placa" name="placa" value="">                            
                                </td>                                
                            </tr>
                            <tr>                                
                                <td nowrap="nowrap" class="label"><label>Data Acionamento:</label></td>
                                <td nowrap="nowrap" style="text-align:left;">
                                    <input style="width:104px;" type="text" id="dt_ini" name="dt_ini" maxlength="10" onKeyUp="formata_dt(this);" value="<?php echo isset($options['dt_ini']) ? $options['dt_ini'] : date('d/m/Y'); ?>">
                                    <img src="images/calendar_cal.gif" align="absmiddle" border="0" alt="Calendário..." onclick="displayCalendar( document.getElementById('dt_ini'),'dd/mm/yyyy',this)">
                                    <span style="margin: 0 10px 0 5px; padding-top: 3px"> à </span>
                                    <input style="width:104px;" type="text" id="dt_fim" name="dt_fim" maxlength="10" onKeyUp="formata_dt(this);" value="<?php echo isset($options['dt_fim']) ? $options['dt_fim'] : date('d/m/Y'); ?>">
                                    <img src="images/calendar_cal.gif" align="absmiddle" border="0" alt="Calendário..." onclick="displayCalendar( document.getElementById('dt_fim'),'dd/mm/yyyy',this)">                                                                    
                                </td>
                            </tr>
                            <tr>                                
                                <td nowrap="nowrap" class="label"><label>Equipe:</label></td>
                                <td nowrap="nowrap" style="text-align:left;">
                                    <select name="equipe" id="equipe">
                                        <?php if($_SESSION['funcao']['permissao_total_ocorrencia']): ?>                                        
                                        <option value="">Todos</option>
                                        <?php endif; ?>
                                        <?php foreach($this->comboEquipes as $equipe): ?>
                                        <option value="<?php echo $equipe['tetoid'] ?>"><?php echo $equipe['tetdescricao'] ?></option>  
                                        <?php endforeach; ?>                                        
                                    </select>                                    
                                </td>
                            </tr> 
                            <tr>                                
                                <td nowrap="nowrap" class="label"><label>Tipo:</label></td>
                                <td nowrap="nowrap" style="text-align:left;">
                                    <select name="tipo" id="tipo">
                                        <option value="">Todos</option>                                        
                                        <option value="0">Cerca</option>
                                        <option value="1">Roubo</option>
                                        <option value="2">Furto</option>
                                        <option value="3">Suspeita</option>
                                        <option value="4">Sequestro</option>
                                    </select>                                    
                                </td>
                            </tr> 
                            <tr>                                
                                <td nowrap="nowrap" class="label"><label>&nbsp;</label></td>
                                <td nowrap="nowrap" style="text-align:left;">
                                    <input class="float-left" type="checkbox" name="recuperado" id="recuperado" /> <span style="margin: 4px 0 0 5px;" class="float-left">Recuperado</span>
                                </td>
                            </tr>                             
                            <!-- Botão para realizar a pesquisa -->
                            <tr class="tableRodapeModelo1">
                                <td align="center" colspan="2">
                                    <input type="button" id="pesquisar" class="botao" value="Pesquisar" name="pesquisar" />                                    
                                    <?php if(!$_SESSION['funcao']['permissao_total_ocorrencia']): ?>
                                    <input type="button" id="novo" class="botao" value="Novo" name="novo" />                                    
                                    <?php endif; ?>
                                </td>
                            </tr>
                        </table>
						<div id="img_loading"></div>
                    </td>
                </tr>
                <tr>
                    <td align="center">
                        <div id="resultado">
						
						
						</div>
                    </td>
                </tr>                
            </table>
        </form>         
    </div>
</body>
<?php include "lib/rodape.php"; ?>