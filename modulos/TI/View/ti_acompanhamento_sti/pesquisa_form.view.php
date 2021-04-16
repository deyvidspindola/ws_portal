<?php
/**
 * Módulo Principal
 *
 * SASCAR (http://www.sascar.com.br/)
 *
 * @author Jorge A. D. Kautzmann <jorge.kautzmann@sascar.com.br>
 * @description	View da página default
 * @version 10/03/2008 [0.0.1]
 * @package SASCAR Intranet
 */
	// Conexão para montar listas COMBO BOX
	global $conn;
	// Dados do Formulário
	$vFormPesquisaData = array();
	$vFormPesquisaData['pesq_periodo_inicial'] = (isset($_POST['pesq_periodo_inicial'])) ? $_POST['pesq_periodo_inicial'] : date("d/m/Y");
	$vFormPesquisaData['pesq_periodo_final'] = (isset($_POST['pesq_periodo_final'])) ? $_POST['pesq_periodo_final'] : date("d/m/Y");
	$vFormPesquisaData['reqioid'] = (isset($_POST['reqioid'])) ? $_POST['reqioid'] : '';
    $vFormPesquisaData['reqioid_externo'] = (isset($_POST['reqioid_externo'])) ? $_POST['reqioid_externo'] : '';
	$vFormPesquisaData['sti_tipo'] = (isset($_POST['sti_tipo'])) ? $_POST['sti_tipo'] : '';
	$vFormPesquisaData['sti_subtipo'] = (isset($_POST['sti_subtipo'])) ? $_POST['sti_subtipo'] : '';
    $vFormPesquisaData['sti_solicitante'] = (isset($_POST['sti_solicitante'])) ? $_POST['sti_solicitante'] : '';
    $vFormPesquisaData['cd_usuario_solicitante'] = (isset($_POST['cd_usuario_solicitante'])) ? $_POST['cd_usuario_solicitante'] : '';
    $vFormPesquisaData['sti_funcao'] = (isset($_POST['sti_funcao'])) ? $_POST['sti_funcao'] : '';
    $vFormPesquisaData['sti_usuario'] = (isset($_POST['sti_usuario'])) ? $_POST['sti_usuario'] : '';
    $vFormPesquisaData['sti_natureza'] = (isset($_POST['sti_natureza'])) ? $_POST['sti_natureza'] : '';
    $vFormPesquisaData['sti_fluxo'] = (isset($_POST['sti_fluxo'])) ? $_POST['sti_fluxo'] : '';
	$vFormPesquisaData['sti_projeto'] = (isset($_POST['sti_projeto'])) ? $_POST['sti_projeto'] : '';
    $vFormPesquisaData['sti_assunto'] = (isset($_POST['sti_assunto'])) ? $_POST['sti_assunto'] : '';
    $vFormPesquisaData['sti_demanda_propria'] = (isset($_POST['sti_demanda_propria'])) ? $_POST['sti_demanda_propria'] : '';
    $vFormPesquisaData['sti_demanda_atraso'] = (isset($_POST['sti_demanda_atraso'])) ? $_POST['sti_demanda_atraso'] : '';
 
?>
<div align="center">
  <form name="solstiform" id="solstiform" method="post" action="ti_acompanhamento_sti.php" enctype="multipart/form-data">
    <input type="hidden" name="acao" id="form_acao" value="">
    <input type="hidden" name="origem_req" id="origem_req" value="">
    <table class="tableMoldura" width="98%">
      <tr class="tableTitulo">
        <td><h1>TI - Acompanhamento de STI</h1></td>
      </tr>
      <?php
        // inclui abas
        require 'modulos/TI/View/ti_acompanhamento_sti/abas.view.php';
        ?>
      <tr>
        <td align="center" valign="top"><table class="tableMoldura" id="filtro_pesquisa" width="98%">
            <tr class="tableSubTitulo">
              <td colspan="4"><h2>Dados para Pesquisa</h2></td>
            </tr>
            <tr height="22">
              <td colspan="4"><span class="msg" id="div_msg"> </span></td>
            </tr>
            <tr>
              <td width="18%"><label for="pesq_periodo_inicial">Período:</label></td>
              <td colspan="3" width="82%"><input type="text" name="pesq_periodo_inicial" id="pesq_periodo_inicial" size="10" maxlength="10" value="<? echo $vFormPesquisaData['pesq_periodo_inicial']; ?>" onkeyup="formatar(this, '@@/@@/@@@@')" onBlur="revalidar(this,'@@/@@/@@@@','data');" style="height: 18px;">
                <img src="images/calendar_cal.gif" border="0" onclick="displayCalendar(document.solstiform.pesq_periodo_inicial,'dd/mm/yyyy',this)" align="top" alt="Calendário..."> à
                <input type="text" name="pesq_periodo_final" id="pesq_periodo_final" size="10" maxlength="10" value="<? echo $vFormPesquisaData['pesq_periodo_final']; ?>" onkeyup="formatar(this, '@@/@@/@@@@')" onBlur="revalidar(this,'@@/@@/@@@@','data');" style="height: 18px;">
                <img src="images/calendar_cal.gif" border="0" onclick="displayCalendar(document.solstiform.pesq_periodo_final,'dd/mm/yyyy',this)" align="top" alt="Calendário..."></td>
            </tr>
            <tr>
              <td width="18%"><label for="reqioid">N° STI:</label></td>
              <td width="37%"><input type="text" id="reqioid" name="reqioid" size="9" maxlength="9" value="<? echo $vFormPesquisaData['reqioid']; ?>"  onkeyup="formatar(this, '@@@@@@@@@@')" style="height: 18px;"></td>
              <td width="10%"><label for="reqioid_externo">N° Externo:</label></td>
              <td width="35%"><input type="text" id="reqioid_externo" name="reqioid_externo" size="9" maxlength="9" value="<? echo $vFormPesquisaData['reqioid_externo']; ?>"  onkeyup="formatar(this, '@@@@@@@@@@')" style="height: 18px;"></td>
            </tr>
            <tr>
              <td><label for="sti_tipo">Tipo STI:</label></td>
              <td><SELECT id="sti_tipo" name="sti_tipo" onChange="Javascript:xajax_getComboBoxListSubtipo(document.solstiform.sti_tipo.value);">
                  <option value=""> Escolha </option>
			  <?php 
				$vOptions = array();
				$sqlQuery = "";
				$sqlQuery .= " SELECT reqtoid, reqttipo FROM req_informatica_tipo ";
				$sqlQuery .= " WHERE reqtexclusao IS NULL ";
				$sqlQuery .= " AND reqtcontrole_sti IS TRUE ";
				$sqlQuery .= " ORDER BY reqttipo ";
				$sqlQuery .= " LIMIT 1000 ";
				$rs = pg_query($conn, $sqlQuery);
				if(pg_num_rows($rs) > 0) {
					for($i = 0; $i < pg_num_rows($rs); $i++) {
						$vOptions = pg_fetch_array($rs);
					?>
					<option value="<?php echo $vOptions['reqtoid']; ?>" <?php if($vFormPesquisaData['sti_tipo'] == $vOptions['reqtoid']){ echo ' selected="selected"'; } ?>> <?php echo $vOptions['reqttipo']; ?> </option>
					<?php 
					}
				}
			  ?>
                </SELECT></td>
              <td><label for="sti_subtipo">Sub-tipo STI:</label></td>
              <td>
					<div id="combo_subtipo" >
					<SELECT id="sti_subtipo" name="sti_subtipo">
                  <option value=""> Escolha </option>
			  <?php 
				$vOptions = array();
				$sqlQuery = "";
				$sqlQuery .= " SELECT rqstoid, rqsttipo FROM req_informatica_subtipo";
				$sqlQuery .= " WHERE rqstdt_exclusao IS NULL ";
				if($vFormPesquisaData['sti_tipo'] != ''){
					$sqlQuery .= " AND rqstreqtoid = " . $vFormPesquisaData['sti_tipo'];
				}else{
					$sqlQuery .= " AND rqstreqtoid = 0 "; // quando não há tipo selecionado não carregar nada no combo
				}
				$sqlQuery .= " ORDER BY rqsttipo";
				$sqlQuery .= " LIMIT 1000 ";
				
				$rs = pg_query($conn, $sqlQuery);
				if(pg_num_rows($rs) > 0) {
					for($i = 0; $i < pg_num_rows($rs); $i++) {
						$vOptions = pg_fetch_array($rs);
					?>
					<option value="<?php echo $vOptions['rqstoid']; ?>"  <?php if($vFormPesquisaData['sti_subtipo'] == $vOptions['rqstoid']){ echo ' selected="selected"'; } ?>> <?php echo $vOptions['rqsttipo']; ?></option>
					<?php 
					}
				}
			  ?>  
                </SELECT>
				  </div>
				 </td>
            </tr>
            <tr>
              <td><label for="sti_solicitante">Solicitante:</label></td>
              <td colspan="3" valign="top">
			     <input type="text" id="sti_solicitante" name="sti_solicitante" value="<? echo $vFormPesquisaData['sti_solicitante']; ?>"  readonly="readonly" size="25" maxlength="32" style="height: 18px;">
				  <input type="button" name="bt_pesquisar_solicitante" id="bt_pesquisar_solicitante" class="botao" value="Pesquisar" onclick="javascript:pesquisarSolicitantePop();"/>
				  <input type="button" name="bt_limpar_solicitante" id="bt_limpar_solicitante" class="botao" value="Limpar" onclick="javascript:limparPesquisaSolicitante();"/>
				  <input type="hidden" name="cd_usuario_solicitante" id="cd_usuario_solicitante" value="<? echo $vFormPesquisaData['cd_usuario_solicitante']; ?>">
				</td>
            </tr>
            <tr>
              <td><label for="sti_funcao">Função:</label></td>
              <td><SELECT id="sti_funcao" name="sti_funcao" onChange="Javascript:xajax_getComboBoxListUsuario(document.solstiform.sti_funcao.value);">
                  <option value=""> Escolha </option>
			  <?php 
				$vOptions = array();
				$sqlQuery = "";
				$sqlQuery .= " SELECT reqifcoid, reqifcdescricao FROM req_informatica_funcao ";
				$sqlQuery .= " WHERE reqifcdt_exclusao IS NULL ";
				$sqlQuery .= " ORDER BY reqifcdescricao ";
				$sqlQuery .= " LIMIT 1000 ";

				$rs = pg_query($conn, $sqlQuery);
				if(pg_num_rows($rs) > 0) {
					for($i = 0; $i < pg_num_rows($rs); $i++) {
						$vOptions = pg_fetch_array($rs);
					?>
					<option value="<?php echo $vOptions['reqifcoid']; ?>" <?php if($vFormPesquisaData['sti_funcao'] == $vOptions['reqifcoid']){ echo ' selected="selected"'; } ?>> <?php echo $vOptions['reqifcdescricao']; ?> </option>
					<?php 
					}
				}
			  ?>  
                </SELECT></td>
              <td><label for="sti_usuario"> Usuário:</label></td>
              <td>
					<div id="combo_usuario" >
					<SELECT id="sti_usuario" name="sti_usuario">
                 <option value=""> Escolha </option>
			  <?php 
		  
				$vOptions = array();
				$sqlQuery = "";
				$sqlQuery .= " SELECT DISTINCT cd_usuario, nm_usuario FROM usuarios, req_informatica_funcao_usuario";
				$sqlQuery .= " WHERE reqifudt_exclusao IS NULL ";
				$sqlQuery .= " AND dt_exclusao IS NULL ";
				$sqlQuery .= " AND reqifuusuoid = cd_usuario ";
				if($vFormPesquisaData['sti_tipo'] != ''){
					$sqlQuery .= " AND reqifureqifcoid = " . $vFormPesquisaData['sti_funcao'];
				}
				$sqlQuery .= " ORDER BY nm_usuario";
				$sqlQuery .= " LIMIT 1000 ";
				
				$rs = pg_query($conn, $sqlQuery);
				if(pg_num_rows($rs) > 0) {
					for($i = 0; $i < pg_num_rows($rs); $i++) {
						$vOptions = pg_fetch_array($rs);
					?>
					<option value="<?php echo $vOptions['cd_usuario']; ?>"  <?php if($vFormPesquisaData['sti_usuario'] == $vOptions['cd_usuario']){ echo ' selected="selected"'; } ?>> <?php echo $vOptions['nm_usuario']; ?></option>
					<?php 
					}
				}
			  ?> 
                </SELECT>
				  </div>
				  </td>
            </tr>
            <tr>
              <td><label for="sti_natureza">Natureza:</label></td>
              <td><SELECT id="sti_natureza" name="sti_natureza">
                 <option value=""> Escolha </option>
			  <?php 
		  
				$vOptions = array();
				$sqlQuery = "";
				$sqlQuery .= " SELECT reqinoid, reqindescricao FROM req_informatica_natureza";
				$sqlQuery .= " WHERE reqindt_exclusao IS NULL ";
				$sqlQuery .= " ORDER BY reqindescricao";
				$sqlQuery .= " LIMIT 1000 ";
				
				$rs = pg_query($conn, $sqlQuery);
				if(pg_num_rows($rs) > 0) {
					for($i = 0; $i < pg_num_rows($rs); $i++) {
						$vOptions = pg_fetch_array($rs);
					?>
					<option value="<?php echo $vOptions['reqinoid']; ?>"  <?php if($vFormPesquisaData['sti_natureza'] == $vOptions['reqinoid']){ echo ' selected="selected"'; } ?>> <?php echo $vOptions['reqindescricao']; ?></option>
					<?php 
					}
				}
			  ?> 
                </SELECT>
				</td>
              <td><label for="sti_fluxo">Fluxo:</label></td>
              <td><SELECT id="sti_fluxo" name="sti_fluxo">
                 <option value=""> Escolha </option>
			  <?php 
		  
				$vOptions = array();
				$sqlQuery = "";
				$sqlQuery .= " SELECT reqifoid, reqifdescricao FROM req_informatica_fluxo";
				$sqlQuery .= " WHERE reqifdt_exclusao IS NULL ";
				$sqlQuery .= " ORDER BY reqifdescricao";
				$sqlQuery .= " LIMIT 1000 ";
				
				$rs = pg_query($conn, $sqlQuery);
				if(pg_num_rows($rs) > 0) {
					for($i = 0; $i < pg_num_rows($rs); $i++) {
						$vOptions = pg_fetch_array($rs);
					?>
					<option value="<?php echo $vOptions['reqifoid']; ?>"  <?php if($vFormPesquisaData['sti_fluxo'] == $vOptions['reqifoid']){ echo ' selected="selected"'; } ?>> <?php echo $vOptions['reqifdescricao']; ?></option>
					<?php 
					}
				}
			  ?> 
                 </SELECT></td>
            </tr>
            <tr>
              <td><label for="sti_projeto">Projeto:</label></td>
              <td colspan="3"><SELECT id="sti_projeto" name="sti_projeto">
                <option value=""> Escolha </option>
                <?php 
		  
    				$vOptions = array();
    				$sql = "SELECT
                                rproid,
                                rprnome,
                                rpridentificacao
                            FROM
                                req_projeto
                            WHERE
                                rprdt_exclusao IS NULL
                            ORDER BY
                                rpridentificacao";
    				
    				$rs = pg_query($conn, $sql);

                ?>

				<?php while( $vOptions = pg_fetch_object($rs) ) { ?>
					<option value="<?php echo $vOptions->rproid; ?>" <?php echo ($vFormPesquisaData['sti_projeto'] == $vOptions->rproid) ? 'selected="selected"' : '' ?>>
                        <?php echo $vOptions->rpridentificacao; ?>
                    </option>
                <?php } ?>

                </SELECT></td>
            </tr>
            <tr>
              <td><label for="sti_assunto"> Assunto:</label></td>
              <td colspan="3"><input type="text" id="sti_assunto" name="sti_assunto" value="<? echo $vFormPesquisaData['sti_assunto']; ?>" size="40" maxlength="50" style="height: 18px;"></td>
            </tr>
            <tr>
              <td><label for="sti_demanda_propria"> Apenas Demanda Própria:</label></td>
              <td colspan="3">
			    <input id="sti_demanda_propria" type="checkbox" value="1" <?php if($vFormPesquisaData['sti_demanda_propria'] == '1'){echo 'CHECKED';}?> name="sti_demanda_propria">
			  </td>
            </tr>
            <tr>
              <td><label for="sti_demanda_atraso"> Demanda em atraso:</label></td>
              <td colspan="3">
			    <input id="sti_demanda_atraso" type="checkbox" value="1" <?php if($vFormPesquisaData['sti_demanda_propria'] == '1'){echo 'CHECKED';}?> name="sti_demanda_atraso">
			  </td>
            </tr>
            <tr class="tableRodapeModelo1">
              <td colspan="4" align="center"><input type="button" style="display:none" name="bt_gerar_planilha_devolucao" id="bt_gerar_planilha_devolucao" class="botao" value="Gerar Planilha de Devolução" />
                <input type="button" name="bt_pesquisar" id="bt_pesquisar" class="botao" value="Pesquisar" onclick="javascript:pesquisar();"/>
            </tr>
          </table></td>
      </tr>
    </table>
  </form>
</div>
