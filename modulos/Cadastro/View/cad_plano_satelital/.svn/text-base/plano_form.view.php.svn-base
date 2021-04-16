<?php
/**
 * Módulo Principal
 *
 * SASCAR (http://www.sascar.com.br/)
 *
 * @author Jorge A. D. Kautzmann <jorge.kautzmann@sascar.com.br>
 * @description View da página de detalhes
 * @version 28/03/2013 [1.0]
 * @package SASCAR Intranet
 */
// Conexão para montar listas COMBO BOX
global $conn;
// Dados do Formulário
?>
<div align="center">
  <form name="cadplanosat" id="cadplanosat" method="post" action="cad_plano_satelital.php" enctype="multipart/form-data">
    <input type="hidden" name="acao" id="form_acao" value="">
    <input type="hidden" name="origem_req" id="origem_req" value="">
    <table class="tableMoldura" width="98%">
      <tr class="tableTitulo">
        <td><h1>Cadastro de Plano Satelital</h1></td>
      </tr>
      <tr>
        <td>&nbsp;</td>
      </tr>
      <tr>
        <td align="center" valign="top"><table width="98%" class="tableMoldura" align="center">
          <tr class="tableSubTitulo">
            <td colspan="4"><h2>Configuração de Planos</h2></td>
          </tr>
          <tr height="25">
            <td colspan="4"><span class="msg"><?php echo $vData['action_msg']; ?></span></td>
          </tr>
          <tr>
            <td width="8%" align="left"><label for="asapoid">Plano *:</label></td>
            <td width="20%" align="left"><SELECT id="asapoid" name="asapoid" onChange="Javscript:planoChange();">
              <option value=""> Novo Plano </option>
             <?php 
                $vOptions = array();
                $sqlQuery = "SELECT 
							   asapoid,
							   asapdescricao
							FROM
							   antena_satelital_plano
							WHERE 
							   asapdt_exclusao is NULL
							ORDER BY 
							   asapdescricao LIMIT 500;";
                $rs = pg_query($conn, $sqlQuery);
                if(pg_num_rows($rs) > 0) {
                    for($i = 0; $i < pg_num_rows($rs); $i++) {
                        $vOptions = pg_fetch_array($rs);
                    ?>
                  <option value="<?php echo $vOptions['asapoid']; ?>" <?php if($vData['asapoid'] == $vOptions['asapoid']){ echo ' selected="selected"'; } ?>> <?php echo $vOptions['asapdescricao']; ?> </option>
                <?php 
                    }
                }
                ?>
            </SELECT>
			  </td>
            <td colspan="2" align="left">
			    <table width="100%">
				  <tr>
				    <td align="left" width="8%"><label for="asapdescricao">Descrição:</label></td>
				    <td align="left" width="92%"><input type="text" id="asapdescricao" name="asapdescricao" value="<?php echo $vData['asapdescricao']; ?>" size="37" maxlength="50" style="height: 18px;"></td>
				  </tr>
			    </table>
			  </td>
          </tr>
			<tr>
			  <td colspan="4" align="left" height="36"> &nbsp; </td>
			</tr>
			<tr>
			  <td colspan="4" align="center"  class="tableRodapeModelo1">
				<?php
					$vData['asapoid'] = (int) $vData['asapoid'];
					if($vData['asapoid'] == 0){
						$podeConfirmar = '';
						$podeExcluir = ' style="display:none;"';
					}else{ 
						$podeConfirmar = ' style="display:none;"';
						$podeExcluir = '';
					}
				?>
				<input type="button" name="plano_bt_excluir" id="plano_bt_excluir" class="botao" value="Excluir" onclick="javascript:excluirPlano();" <?php echo $podeExcluir; ?>/>
				<input type="button" name="plano_bt_confirmar" id="plano_bt_confirmar" class="botao" value="Confirmar" onclick="javascript:confirmarNovoPlano();" <?php echo $podeConfirmar; ?>/>
			  </td>
			</tr>
        </table>
        </td>
      </tr>
    </table>
  </form>
</div>
