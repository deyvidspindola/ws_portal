<?php
/**
 * Módulo Principal
 *
 * SASCAR (http://www.sascar.com.br/)
 *
 * @author Jorge A. D. Kautzmann <jorge.kautzmann@sascar.com.br>
 * @description	View da página default
 * @version 10/10/2012 [0.0.1]
 * @package SASCAR Intranet
 */
	// Conexão para montar listas COMBO BOX
	global $conn;
	// Dados do Formulário
	$vFormPesquisaData = array();
	$vFormPesquisaData['periodo_cad_inicial'] = (isset($_POST['periodo_cad_inicial'])) ? $_POST['periodo_cad_inicial'] : date('d/m/Y');
	$vFormPesquisaData['periodo_cad_final'] = (isset($_POST['periodo_cad_final'])) ? $_POST['periodo_cad_final'] : date('d/m/Y');
	$vFormPesquisaData['periodo_con_inicial'] = (isset($_POST['periodo_con_inicial'])) ? $_POST['periodo_con_inicial'] : date('d/m/Y');
	$vFormPesquisaData['periodo_con_final'] = (isset($_POST['periodo_con_final'])) ? $_POST['periodo_con_final'] : date('d/m/Y');

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
              <td colspan="4"><h2>Pesquisa</h2></td>
            </tr>
           <tr>
              <td width="15%"><label for="periodo_cad_inicial">Período Cadastro:</label></td>
              <td width="30%"><input type="text" name="periodo_cad_inicial" id="periodo_cad_inicial" size="10" maxlength="10" value="<? echo $vFormPesquisaData['periodo_cad_inicial']; ?>" onkeyup="formatar(this, '@@/@@/@@@@')" onBlur="revalidar(this,'@@/@@/@@@@','data');" style="height: 18px;">
                <img src="images/calendar_cal.gif" border="0" onclick="displayCalendar(document.solstiform.periodo_cad_inicial,'dd/mm/yyyy',this)" align="top" alt="Calendário..."> à
                <input type="text" name="periodo_cad_final" id="periodo_cad_final" size="10" maxlength="10" value="<? echo $vFormPesquisaData['periodo_cad_final']; ?>" onkeyup="formatar(this, '@@/@@/@@@@')" onBlur="revalidar(this,'@@/@@/@@@@','data');" style="height: 18px;">
                <img src="images/calendar_cal.gif" border="0" onclick="displayCalendar(document.solstiform.periodo_cad_final,'dd/mm/yyyy',this)" align="top" alt="Calendário..."></td>
              <td width="15%"><label for="periodo_con_inicial">Período Conclusão:</label></td>
              <td width="40%"><input type="text" name="periodo_con_inicial" id="periodo_con_inicial" size="10" maxlength="10" value="<? echo $vFormPesquisaData['periodo_con_inicial']; ?>" onkeyup="formatar(this, '@@/@@/@@@@')" onBlur="revalidar(this,'@@/@@/@@@@','data');" style="height: 18px;">
                <img src="images/calendar_cal.gif" border="0" onclick="displayCalendar(document.solstiform.periodo_con_inicial,'dd/mm/yyyy',this)" align="top" alt="Calendário..."> à
                <input type="text" name="periodo_con_final" id="periodo_con_final" size="10" maxlength="10" value="<? echo $vFormPesquisaData['periodo_con_final']; ?>" onkeyup="formatar(this, '@@/@@/@@@@')" onBlur="revalidar(this,'@@/@@/@@@@','data');" style="height: 18px;">
                <img src="images/calendar_cal.gif" border="0" onclick="displayCalendar(document.solstiform.periodo_con_final,'dd/mm/yyyy',this)" align="top" alt="Calendário..."></td>
            </tr>
            <tr>
              <td><label for="sti_tipo">Tipo STI:</label></td>
              <td><SELECT id="sti_tipo" name="sti_tipo">
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
              <td><label for="sti_projeto">Projeto:</label></td>
              <td colspan="3"><SELECT id="sti_projeto" name="sti_projeto">
                 <option value=""> Escolha </option>
			  <?php 
		  
				$vOptions = array();
				$sqlQuery = "";
				$sqlQuery .= " SELECT rproid, rprnome FROM req_projeto";
				$sqlQuery .= " WHERE rprdt_exclusao IS NULL ";
				$sqlQuery .= " AND rprrpsccoid in(1,2,4,6) ";
				$sqlQuery .= " ORDER BY rprnome";
				$sqlQuery .= " LIMIT 1000 ";
				
				$rs = pg_query($conn, $sqlQuery);
				if(pg_num_rows($rs) > 0) {
					for($i = 0; $i < pg_num_rows($rs); $i++) {
						$vOptions = pg_fetch_array($rs);
					?>
					<option value="<?php echo $vOptions['rproid']; ?>"  <?php if($vFormPesquisaData['sti_projeto'] == $vOptions['rproid']){ echo ' selected="selected"'; } ?>> <?php echo $vOptions['rprnome']; ?></option>
					<?php 
					}
				}
			  ?> 
                </SELECT></td>
            </tr>
          <tr>
            <td><label for="sti_modalidade">Modalidade:</label></td>
              <td colspan="3"><SELECT id="sti_modalidade" name="sti_modalidade">
               <option value=""> Escolha </option>
				  <option value="1" <?php if($vFormPesquisaData['sti_modalidade'] == 1){ echo ' selected="selected"'; } ?>> Demanda por Natureza </option>
               <option value="2" <?php if($vFormPesquisaData['sti_modalidade'] == 2){ echo ' selected="selected"'; } ?>> Demanda em Atraso </option>
               <option value="3" <?php if($vFormPesquisaData['sti_modalidade'] == 3){ echo ' selected="selected"'; } ?>> Recursos Alocados </option>
               <option value="4" <?php if($vFormPesquisaData['sti_modalidade'] == 4){ echo ' selected="selected"'; } ?>> Indicadores </option>
               </SELECT>
			   </td>
           </tr>
         </table></td>
      </tr>
      <tr>
        <td align="center" valign="top">&nbsp;</td>
      </tr>
      <tr>
        <td align="center" valign="top"><table class="tableMoldura" id="filtro_pesquisa2" width="98%">
            <tr class="tableTituloColunas ">
              <td colspan="10" style="border-bottom: 1px solid white;"><h2>Dados para Pesquisa</h2></td>
            </tr>
            <tr class="tableTituloColunas">
              <td align="center" width="10%"><h3>Data</h3></td>
              <td align="center" width="8%" colspan="2"><h3>STI</h3></td>
              <td align="left" width="15%"><h3>Solicitante</h3></td>
              <td align="left" width="15%"><h3>Projeto</h3></td>
              <td align="left" width="12%"><h3>Responsavel</h3></td>
              <td align="left" width="10%"><h3>Fluxo</h3></td>
              <td align="left" width="10%"><h3>Fase atual</h3></td>
              <td align="center" width="10%"><h3>Previs&atilde;o de entrega</h3></td>
              <td align="center" width="10%"><h3>Natureza</h3></td>
            </tr>
            <?php
                $classZebra = 'tdc';
				if(is_array($vPesquisa['lista'])){
					foreach ($vPesquisa['lista'] as $key => $vValue) { 
						$classZebra  = ($classZebra == 'tdc') ? 'tde' : 'tdc';
				?>
				<tr align="center" class="<? echo $classZebra; ?>">
				  <td align="center" ><?php echo $vValue['data']; ?></td>
				  <td align="center" >
				    <a href="javascript:;" onclick="javascript:abreAbaDetalheSTIRelacao(<?php echo $vValue['sti']; ?>);" title="Abrir aba detalhes"> <div id="iDetRelA_<?php echo $vValue['sti']; ?>" style="display:block">[+]</div> </a>
				    <a href="javascript:;" onclick="javascript:abreAbaDetalheSTIRelacao(<?php echo $vValue['sti']; ?>);" title="Fechar aba detalhes"> <div id="iDetRelF_<?php echo $vValue['sti']; ?>" style="display:none">[-]</div> </a>
				  </td>
				  <td align="left">
				    <a href="javascript:;" onclick="javascript:exibirDetalhes(<?php echo $vValue['sti']; ?>);" title="Exibir Tela de Edição da STI"> <?php echo $vValue['sti']; ?> </a>
				  </td> 
				  <td align="left"><?php echo $vValue['solicitante']; ?></td>
				  <td align="left"><?php echo $vValue['projeto']; ?></td>
				  <td align="left"><?php echo $vValue['responsavel']; ?></td>
				  <td align="left"><?php echo $vValue['fluxo']; ?></td>
				  <td align="left"><?php echo $vValue['fase']; ?></td>
				  <td align="left"><?php echo $vValue['previsao_entrega']; ?></td>
				  <td align="left"><?php echo $vValue['natureza']; ?></td>
				</tr>
				<tr align="center" class="<? echo $classZebra; ?>">
				  <td colspan="10">
                 <div id="iDetRel_<?php echo $vValue['sti']; ?>" style="display:none">
				 
				    </div>
               </td>
			   </tr>
				<?php
						}
					}
				?>
            <tr>
              <td colspan="9" align="center"><span class="msg" id="div_msg"><?php echo $vPesquisa['action_msg']; ?></span></td>
            </tr>
          </table></td>
      </tr>
    </table>
  </form>
</div>
