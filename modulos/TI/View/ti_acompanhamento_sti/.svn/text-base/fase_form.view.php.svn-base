<?php
/**
 * Módulo Principal
 *
 * SASCAR (http://www.sascar.com.br/)
 *
 * @author Jorge A. D. Kautzmann <jorge.kautzmann@sascar.com.br>
 * @description    View da página de detalhes
 * @version 10/03/2008 [0.0.1]
 * @package SASCAR Intranet
 */
// Conexão para montar listas COMBO BOX
global $conn;
// Dados do Formulário
?>
<div align="center">
  <form name="solstiform" id="solstiform" method="post" action="ti_acompanhamento_sti.php" enctype="multipart/form-data">
    <input type="hidden" name="acao" id="form_acao" value="">
    <input type="hidden" name="origem_req" id="origem_req" value="">
    <table class="tableMoldura" width="98%">
      <tr class="tableTitulo">
        <td><h1>STI - Solicitação de TI</h1></td>
      </tr>
      <?php
        // inclui abas
        require 'modulos/TI/View/ti_acompanhamento_sti/abas.view.php';
        ?>
      <tr>
        <td align="center" valign="top"><table width="98%" class="tableMoldura" align="center">
          <tr class="tableSubTitulo">
            <td colspan="4"><h2>Configuração de Fases</h2></td>
          </tr>
          <tr height="25">
            <td colspan="4"><span class="msg"><?php echo $vData['action_msg']; ?></span></td>
          </tr>
          <tr>
            <td width="8%" align="left"><label for="reqifsoid">Fase *:</label></td>
          <td width="20%" align="left"><SELECT id="reqifsoid" name="reqifsoid" onChange="Javscript:faseBtChange();">
              <option value=""> Nova Fase </option>
             <?php 
                $vOptions = array();
                $sqlQuery = "SELECT 
							   reqifsoid,
							   reqifsdescricao
							FROM
							   req_informatica_fase
							WHERE 
							   reqifsdt_exclusao is NULL
							ORDER BY 
							   reqifsdescricao LIMIT 500;";
                $rs = pg_query($conn, $sqlQuery);
                if(pg_num_rows($rs) > 0) {
                    for($i = 0; $i < pg_num_rows($rs); $i++) {
                        $vOptions = pg_fetch_array($rs);
                    ?>
                  <option value="<?php echo $vOptions['reqifsoid']; ?>" <?php if($vData['reqifsoid'] == $vOptions['reqifsoid']){ echo ' selected="selected"'; } ?>> <?php echo $vOptions['reqifsdescricao']; ?> </option>
                <?php 
                    }
                }
                ?>
            </SELECT>
			  </td>
            <td colspan="2" align="left">
			    <table width="100%">
				  <tr>
				    <td align="left" width="15%"><label for="reqifsdescricao">Descrição:</label></td>
				    <td align="left" width="85%"><input type="text" id="reqifsdescricao" name="reqifsdescricao" value="<?php echo $vData['reqifsdescricao']; ?>" size="37" maxlength="50" style="height: 18px;"></td>
				  </tr>
			    </table>
			  </td>
          </tr>
          <tr>
            <td width="8%" align="left"><label for="reqifodoid">Origem Defeito (Mantis):</label></td>
            <td width="20%" align="left">
              <select id="reqifodoid" name="reqifodoid">
                <option value="">Sem Correspondência </option>
                <?php 
                  $sql = "SELECT 
                              reqifodoid,
                              reqifoddescricao
                          FROM
                            req_informatica_fase_origem_defeito
                          WHERE
                            reqifoddt_exclusao IS NULL
                          ORDER BY reqifoddescricao ASC"; 

                  $resOrDef= pg_query($conn, $sql);
                ?>
                <?php if($resOrDef && pg_num_rows($resOrDef)): ?>
                  <?php while($row = pg_fetch_assoc($resOrDef)): ?>
                      <option value="<?=$row['reqifodoid']; ?>" <?php if($vData['reqifsreqifodoid'] == $row['reqifodoid'] || ($_POST['reqifodoid'] == $row['reqifodoid'] && ($vData['acao'] === 'atualizar-fase' || $vData['acao'] === 'confirmar-nova-fase'))){ echo ' selected="selected"'; } ?>> 
                        <?=$row['reqifoddescricao']; ?> 
                      </option>
                  <?php endwhile; ?>
                <?php endif; ?>
              </select>
              <input type="button" name="fase_bt_atualizar" id="fase_bt_atualizar" class="botao" value="Atualizar" <?php if(!isset($vData['acao']) || $vData['reqifsoid'] == null) { echo "style='visibility:hidden'"; } ?> onclick="javascript:atualizarFase();"/>
            </td>
          </tr>
			<tr>
			  <td colspan="4" align="left" height="36"> &nbsp; </td>
			</tr>
			<tr>
			  <td colspan="4" align="center"  class="tableRodapeModelo1">
				<?php if($_SESSION['funcao']['sti_edicao'] == 1){
					$vData['reqifsoid'] = (int) $vData['reqifsoid'];
					if($vData['reqifsoid'] == 0){
						$podeConfirmar = '';
						$podeExcluir = ' style="display:none;"';
					}else{ 
						$podeConfirmar = ' style="display:none;"';
						$podeExcluir = '';
					}
				?>
				  <input type="button" name="fase_bt_excluir" id="fase_bt_excluir" class="botao" value="Excluir" onclick="javascript:excluirFase();" <?php echo $podeExcluir; ?>/>
				  <input type="button" name="fase_bt_confirmar" id="fase_bt_confirmar" class="botao" value="Confirmar" onclick="javascript:confirmarNovaFase();" <?php echo $podeConfirmar; ?>/>
				<?php } ?>
			  </td>
			</tr>
        </table>
        </td>
      </tr>
    </table>
  </form>
</div>
