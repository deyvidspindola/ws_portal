<?php 
require_once '_header.php';
$control = new RelBaseInformacoesFaturamento();
$relatorios = $control->retornaNomeRelatorios();

$retorno = $control->verificarProcesso(false);
if ($retorno['codigo'] == 2) {
	$disabled = 'disabled = "disabled"';
}
?>
<script type="text/javascript" src="modulos/web/js/rel_base_informacoes_faturamento.js" charset="UTF-8" ></script>
<table width="100%" border="0" cellspacing="1" cellpadding="0" class="tableMoldura">
        <tr class="tableTitulo">
            <td colspan="8">
                <h1>Relatório Base Detalhada</h1>
            </td>
        </tr>
          <tr id="msg">
                <td>
                <?php require_once '_msgPrincipal.php'; ?>
	                <span id="div_msg" class="msg"><?php if(!empty($retorno['msg']) || $retorno['msg'] != '') echo $retorno['msg']; ?></span>
                </td>
            </tr>
        <tr>
            <td colspan="8" align="center">
                <br>
                <form name="frm" id="frm" method="post" action="">
                <input type="hidden" name="acao" id="acao" value="" /> 
                <table class="tableMoldura">
                    <tr>
                        <td class="tableSubtitulo" colspan="2">
                            <h2>Tipo de relatório</h2>
                        </td>
                    </tr>
                    <tr>
                        <td>&nbsp;</td>
                    </tr>
                    <tr>
                        <td>
                            <label for='tipo_relatorio'>Tipo de relatório:*</label>
                        </td>
                        
                        <td>
                            <select name="tipo_relatorio" id="tipo_relatorio" style="width: 400px; ">
                                <option value="">- Escolha -</option>
                                <?php 
	                      	   foreach ($relatorios as $row) {
									echo "<option value='" . $row['id'] . "'>" . $row['valor'] . "</option>";
                        		}
                                ?>

                            </select>
                        </td>
                        
                    </tr>
                    <tr>
                        <td>&nbsp;</td>
                    </tr>
                      <tr>
                    <td><label for="pesq_campo1">Período:*</label></td>
                    <td>
                                <input type="text" name="rel_dt_ini" id="rel_dt_ini" value="<?=$rel_dt_ini?>" size="10" maxlength="10"  onkeyup="formatar(this,'@@/@@/@@@@');" onblur="revalidar(this,'@@/@@/@@@@','data');" > 
                                <img src="images/calendar_cal.gif" align="absmiddle" border="0" alt="Calendário..." onclick="displayCalendar(document.getElementById('rel_dt_ini'),'dd/mm/yyyy',this)"> 
                                até
                                <input type="text" name="rel_dt_fim" id="rel_dt_fim" value="<?=$rel_dt_fim ?>" size="10" maxlength="10"  onkeyup="formatar(this,'@@/@@/@@@@');" onblur="revalidar(this,'@@/@@/@@@@','data');" >
                                <img src="images/calendar_cal.gif" align="absmiddle" border="0" alt="Calendário..." onclick="displayCalendar(document.getElementById('rel_dt_fim'),'dd/mm/yyyy',this)">
                    </td>
                </tr>
                <tr>
                    <td colspan="4">&nbsp;</td>
                </tr>
                     <tr class="tableRodapeModelo1">
                    <td colspan="4" align="center">
                    <button <?php echo $disabled;?> name="bt_pesquisar" id="bt_pesquisar"  class="botao" style="width: 150px;">Gerar Arquivo</button>
                    <input <?php echo $disabled;?> type="button" name="bt_visualizar" id="bt_visualizar" value="Visualizar Arquivo" class="botao" " style="width: 150px;">
                    </td>
                </tr>
                
                </table>
                </form>
            </td>
        </tr>
                   <tr>
            	<td>
            	<div id="resultado" >
            		<?php require_once 'resultado_arquivos.php'; ?>
            	</div>
            	
            	</td>
            </tr>
    </table>    
<?
include("lib/rodape.php");
?>
