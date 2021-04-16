<?php
/**
 * Módulo Principal
 *
 * SASCAR (http://www.sascar.com.br/)
 *
 * @author Jorge A. D. Kautzmann <jorge.kautzmann@sascar.com.br>
 * @description	View da página de detalhes
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
        <td align="center" valign="top"><table class="tableMoldura" id="filtro_pesquisa" width="98%" align="center">
            <tr class="tableSubTitulo">
              <td colspan="4"><h2>Dados Principais</h2></td>
            </tr>
            <tr height="22">
              <td colspan="4"><span class="msg"><?php echo $vData['action_msg']; ?></span></td>
            </tr>
            <tr>
              <td width="15%"><label for="sti">N° STI:</label></td>
              <td width="30%"><input type="text" id="sti" name="sti" size="9" maxlength="9" value="<?php echo $vData['sti']; ?>" readonly="readonly" style="height:18px;"></td>
              <td width="15%"><label for="numero_externo">N° Externo:</label></td>
              <td width="40%"><input type="text" id="numero_externo" name="numero_externo" size="9" maxlength="9" value="<?php echo $vData['numero_externo']; ?>"  onkeyup="formatar(this, '@@@@@@@@@@')" style="height:18px;">
				 <?php if(strlen(trim($vData['data_conclusao'])) == 0){ ?>
					&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
				   <input id="sti_concluida" name="sti_concluida" type="checkbox" value="1" <?php if(strlen($vData['data_conclusao']) > 0){echo 'CHECKED';}?> onClick="JavaScript:concluirSTICbx();" <?php if($_SESSION['funcao']['sti_edicao'] != 1){ echo 'disabled="disabled"'; } ?>>
				   <label for="sti_concluida"> STI Concluída </label>
				 <?php } else { ?>
				   &nbsp;&nbsp;&nbsp;&nbsp;
				   <label for="data_conclusao"> Data de conclusão:</label>
				   <?php if($_SESSION['funcao']['sti_alteracao'] == 1){ ?>
					<input type="text" name="data_conclusao" id="data_conclusao" size="10" maxlength="10" value="<?php echo $vData['data_conclusao']; ?>" onkeyup="formatar(this, '@@/@@/@@@@')" onBlur="revalidar(this,'@@/@@/@@@@','data');">
       		   <img src="images/calendar_cal.gif" border="0" onclick="displayCalendar(document.solstiform.data_conclusao,'dd/mm/yyyy',this)" align="top" alt="Calendário...">
 				   <?php }else{ ?>
					<input type="text" name="data_conclusao" id="data_conclusao" size="10" maxlength="10" value="<?php echo $vData['data_conclusao']; ?>" readonly="readonly" style="width: 110px; background-color:#D9D9F3;">
				   <?php }?>
				 <?php } ?>
			    </td>
            </tr>
            <tr>
              <td><label for="prev_inicio">Prev. Início:</label></td>
              <td><input type="text" name="prev_inicio" id="prev_inicio" size="10" maxlength="10" value="<?php echo $vData['prev_inicio']; ?>" onkeyup="formatar(this, '@@/@@/@@@@')" onBlur="revalidar(this,'@@/@@/@@@@','data');" style="height: 18px;">
                 <img src="images/calendar_cal.gif" border="0" onclick="displayCalendar(document.solstiform.prev_inicio,'dd/mm/yyyy',this)" align="top" alt="Calendário...">
			    </td>
              <td><label for="prev_termino">Prev. Término:</label></td>
              <td><input type="text" name="prev_termino" id="prev_termino" size="10" maxlength="10" value="<?php echo $vData['prev_termino']; ?>" onkeyup="formatar(this, '@@/@@/@@@@')" onBlur="revalidar(this,'@@/@@/@@@@','data');" style="height: 18px;">
                 <img src="images/calendar_cal.gif" border="0" onclick="displayCalendar(document.solstiform.prev_termino,'dd/mm/yyyy',this)" align="top" alt="Calendário...">
			    </td>
            </tr>
            <tr>
              <td><label for="tipo_sti">Tipo STI *:</label></td>
              <td><SELECT id="tipo_sti" name="tipo_sti">
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
					<option value="<?php echo $vOptions['reqtoid']; ?>" <?php if($vData['tipo_sti'] == $vOptions['reqtoid']){ echo ' selected="selected"'; } ?>> <?php echo $vOptions['reqttipo']; ?> </option>
					<?php 
					}
				}
			  ?>
                </SELECT></td>
               <td><label for="fluxo">Fluxo *:</label></td>
               <td><SELECT id="fluxo" name="fluxo">
                 <option value=""> Escolha </option>
			  <?php 
		  
				$vOptions = array();
				$sqlQuery = "";
				$sqlQuery .= " SELECT reqifoid, reqifdescricao, reqifusuoid_responsavel FROM req_informatica_fluxo";
				$sqlQuery .= " WHERE reqifdt_exclusao IS NULL ";
				$sqlQuery .= " ORDER BY reqifdescricao";
				$sqlQuery .= " LIMIT 1000 ";
				
				$rs = pg_query($conn, $sqlQuery);
				if(pg_num_rows($rs) > 0) {
					for($i = 0; $i < pg_num_rows($rs); $i++) {
						$vOptions = pg_fetch_array($rs);
					?>
					<option value="<?php echo $vOptions['reqifoid']; ?>"  <?php if($vData['fluxo'] == $vOptions['reqifoid']){ echo ' selected="selected"'; } ?>> <?php echo $vOptions['reqifdescricao']; ?></option>
					<?php 
					}
				}
			  ?> 
                 </SELECT>
				 </td>
            </tr>
            <tr>
              <td><label for="solicitante">Solicitante *:</label></td>
              <td><SELECT id="solicitante" name="solicitante">
                  <option value=""> Escolha </option>
			  <?php 
				$vOptions = array();
				$sqlQuery = "";
				$sqlQuery .= " SELECT DISTINCT cd_usuario, nm_usuario ";
				$sqlQuery .= " FROM usuarios ";
				$sqlQuery .= " WHERE dt_exclusao is null ";
				$sqlQuery .= " ORDER BY nm_usuario ";

				$rs = pg_query($conn, $sqlQuery);
				if(pg_num_rows($rs) > 0) {
					for($i = 0; $i < pg_num_rows($rs); $i++) {
						$vOptions = pg_fetch_array($rs);
						$vOptions['nm_usuario'] = substr($vOptions['nm_usuario'], 0 , 32);
					?>
					<option value="<?php echo $vOptions['cd_usuario']; ?>" <?php if($vData['solicitante'] == $vOptions['cd_usuario']){ echo ' selected="selected"'; } ?>> <?php echo $vOptions['nm_usuario']; ?> </option>
					<?php 
					}
				}
			  ?>  
                </SELECT></td>
              <td><label for="centro_custo"> Centro Custo *:</label></td>
              <td>
					<SELECT id="centro_custo" name="centro_custo">
                 <option value=""> Escolha </option>
			  <?php 
		  
				$vOptions = array();
				$sqlQuery = "";
				$sqlQuery .= " SELECT cntoid, cntconta FROM centro_custo ";
				$sqlQuery .= " WHERE cntdt_exclusao IS NULL ";
				$sqlQuery .= " ORDER BY cntconta";
				$sqlQuery .= " LIMIT 1000 ";
				$rs = pg_query($conn, $sqlQuery);
				if(pg_num_rows($rs) > 0) {
					for($i = 0; $i < pg_num_rows($rs); $i++) {
						$vOptions = pg_fetch_array($rs);
					?>
					<option value="<?php echo $vOptions['cntoid']; ?>"  <?php if($vData['centro_custo'] == $vOptions['cntoid']){ echo ' selected="selected"'; } ?>><?php echo $vOptions['cntconta']; ?></option>
					<?php 
					}
				}
			  ?> 
                </SELECT>
				  </td>
            </tr>
            <tr>
              <td><label for="natureza"> Natureza *:</label></td>
              <td><SELECT id="natureza" name="natureza">
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
					<option value="<?php echo $vOptions['reqinoid']; ?>"  <?php if($vData['natureza'] == $vOptions['reqinoid']){ echo ' selected="selected"'; } ?>> <?php echo $vOptions['reqindescricao']; ?></option>
					<?php 
					}
				}
			  ?> 
                </SELECT>
				</td>
               <td><label for="responsavel"> Responsável *:</label></td>
              <td>
					<SELECT id="responsavel" name="responsavel">
                 <option value=""> Escolha </option>
			  <?php 
		  
				$vOptions = array();
				$sqlQuery = "";
				$sqlQuery .= "SELECT DISTINCT
								cd_usuario,
								upper(nm_usuario)as nm_usuario
							FROM
								usuarios,
								req_informatica_funcao_usuario
							WHERE
								reqifuusuoid = cd_usuario
								AND dt_exclusao IS NULL
								AND reqifudt_exclusao IS NULL
								AND reqifureqieoid IN (1,2)
							ORDER BY
								nm_usuario";
				
				$rs = pg_query($conn, $sqlQuery);
				if(pg_num_rows($rs) > 0) {
					for($i = 0; $i < pg_num_rows($rs); $i++) {
						$vOptions = pg_fetch_array($rs);
					?>
					<option value="<?php echo $vOptions['cd_usuario']; ?>"  <?php if($vData['responsavel'] == $vOptions['cd_usuario']){ echo ' selected="selected"'; } ?>> <?php echo $vOptions['nm_usuario']; ?></option>
					<?php 
					}
				}
			  ?> 
                </SELECT>
				  </td>
            </tr>
            <tr>
              <td><label for="assunto"> Assunto *:</label></td>
              <td><input type="text" id="assunto" name="assunto" value="<?php echo $vData['assunto']; ?>" size="37" maxlength="50" readonly="readonly" style="height: 18px;"></td>
              <td><label for="projeto">Projeto:</label></td>
              <td><SELECT id="projeto" name="projeto">
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
              <td><label for="descricao"> Descrição *:</label></td>
              <td><textarea name="descricao" id="descricao" rows="4" cols="35" readonly="readonly" ><?php echo $vData['descricao']; ?></textarea></td>
              <td><label for="justificativa"> Justificativa:</label></td>
              <td><textarea name="justificativa" id="justificativa" rows="4" cols="36"><?php echo $vData['justificativa']; ?></textarea></td>
            </tr>
            <tr>
              <td><label for="sti_suspender"> Suspender: </label></td>
              <td>
			    <input id="sti_suspender" type="checkbox" value="1" onClick="JavaScript:suspenderSTICbx();" <?php if($vData['sti_suspender'] == '1'){echo 'CHECKED';}?> name="sti_suspender">
		      </td>
		      <td><label for="defeito_testes"> Defeito Fase Testes:</label></td>
  		      <td>
  		      	<input type="text" id="defeito_testes" name="defeito_testes" size="9" maxlength="3" value="<?php echo $vData['defeito_testes']; ?>"  onkeyup="formatar(this, '@');" onblur="revalidar(this,'@');" style="height:18px;">
  		      	<input type="hidden" id="defeito_testes_anterior" name="defeito_testes_anterior" value="<?php echo $vData['defeito_testes']; ?>">
  		      </td>
            </tr>
            <tr>
              <td><label for="sti_novo_fluxo"> Novo Fluxo:</label></td>
              <td>
			    <input id="sti_novo_fluxo" type="checkbox" value="1" onClick="JavaScript:registrarNovoFluxoSTICbx();" <?php if($vData['sti_novo_fluxo'] == '1'){echo 'CHECKED';}?> name="sti_novo_fluxo" <?php if(($vData['sti_suspender'] == '1') || (strlen(trim($vData['data_conclusao'])) > 0)){ echo 'disabled="disabled"'; } ?>>
			  </td>
  		      <td><label for="pontos_sascar"> N° Pontos Sascar:</label></td>
  		      <td>
  		      	<input type="text" id="pontos_sascar" name="pontos_sascar" size="9" maxlength="3" value="<?php echo $vData['pontos_sascar']; ?>"  onkeyup="formatar(this, '@');if(this.value==0){this.value=''}" onblur="revalidar(this,'@');" style="height:18px;">
  		      </td>
            </tr>
            <tr class="tableRodapeModelo1">
              <td colspan="4" align="center"><input type="button" style="display:none" name="bt_gerar_planilha_devolucao" id="bt_gerar_planilha_devolucao" class="botao" value="Gerar Planilha de Devolução" />
                <?php if($_SESSION['funcao']['sti_edicao'] == 1){ ?>
					<input type="button" name="bt_confirmar" id="bt_confirmar" class="botao" value="Confirmar" onclick="javascript:confirmarClassificacao();"/>
					<?php } ?>
            </tr>
          </table></td>
      </tr>
	   <?php
		$vData['fluxo'] = (int) $vData['fluxo'];
		if($vData['fluxo'] > 0){
	    ?>
      <tr>
        <td align="center" valign="top">
			<table class="tableMoldura" id="filtro_pesquisa" width="98%" align="center">
            <tr class="tableSubTitulo">
              <td width="100%"><h2>Planejamento de Fases</h2></td>
            </tr>
            <tr height="22">
              <td>
					<span class="msg"><?php echo $vData['action_msg_pfase']; ?></span>
				 </td>
            </tr>
            <tr height="22">
              <td width="100%">
					<div id="plan_fases_form" align="left">
						<table width="100%" border="0">
						  <tr>
							<td rowspan="3" width="40%">
							<label for="sti_pfase">Fase *:</label>
							<SELECT id="sti_pfase" name="sti_pfase" 
									onchange="xajax_getComboBoxListRecursoFase(document.solstiform.sti_pfase.value); xajax_getRelacaoRecursoFase(document.solstiform.sti_pfase.value);">
							 <option value=""> Escolha </option>
							  <?php 
								$vData['sti_pfase'] = (int) $_POST['sti_pfase'];
								$numFasesProgramadas = 0;
								$vOptions = array();
								$sqlQuery = "SELECT 
										E.reqiexoid, F.reqifsdescricao, E.reqiexordem
									FROM req_informatica_execucao as E, req_informatica_fase as F
									WHERE 
										E.reqiexreqioid = " . $vData['sti'] . "
									   and E.reqiexnovo_fluxo is null
									   and E.reqiexreqifsoid = F.reqifsoid
									ORDER BY E.reqiexordem LIMIT 1000 ";
								
								$rs = pg_query($conn, $sqlQuery);
								$numFasesProgramadas = pg_num_rows($rs);
								if($numFasesProgramadas > 0) {
									for($i = 0; $i < $numFasesProgramadas; $i++) {
										$vOptions = pg_fetch_array($rs);
									?>
									<option value="<?php echo $vOptions['reqiexoid']; ?>" <?php if($vData['sti_pfase'] == $vOptions['reqiexoid']){ echo ' selected="selected"'; } ?>> <?php echo $vOptions['reqiexordem'] . ' - ' . $vOptions['reqifsdescricao']; ?> </option>
									<?php 
									}
								}
							  ?>
							</SELECT>
							</td>
							<td width="10%" align="left"><label for="sti_recurso">Recurso *:</label></td>
							<td width="50%" align="left">
							<div id="combo_recurso">
							<SELECT id="sti_recurso" name="sti_recurso">
							 <option value=""> Escolha </option>
						  <?php 
							$vOptions = array();
							$sqlQuery = "SELECT cd_usuario, nm_usuario
							FROM
							  usuarios,
							  req_informatica_funcao_usuario,
							  req_informatica_fluxo_fase,
							  req_informatica_execucao
							WHERE
							  reqiffoid = reqiexreqiffoid
							  and reqiexoid = " . $vData['sti_pfase'] . "
							  and reqiffreqifcoid=reqifureqifcoid
							  and reqifuusuoid=cd_usuario
							  and reqifudt_exclusao is null
							  and dt_exclusao is null
							  ORDER BY nm_usuario LIMIT 2000";
							
							$rs = pg_query($conn, $sqlQuery);
							if(pg_num_rows($rs) > 0) {
								for($i = 0; $i < pg_num_rows($rs); $i++) {
									$vOptions = pg_fetch_array($rs);
							?>
								<option value="<?php echo $vOptions['cd_usuario']; ?>" <?php if($vData['sti_recurso'] == $vOptions['cd_usuario']){ echo ' selected="selected"'; } ?>> <?php echo $vOptions['nm_usuario']; ?></option>
							<?php 
								}
							}
						  ?>
							 </SELECT>
							 </div>
							 </td>
						  </tr>
						  <tr>
							<td align="left"><label for="sti_fase_inicio"> Período *:</label></td>
							<td align="left">
								<input type="text" name="sti_fase_inicio" id="sti_fase_inicio" size="10" maxlength="10" value="<?php echo $vData['sti_fase_inicio']; ?>" onkeyup="formatar(this, '@@/@@/@@@@')" onBlur="revalidar(this,'@@/@@/@@@@','data');" style="height: 18px;">
								<img src="images/calendar_cal.gif" border="0" onclick="displayCalendar(document.solstiform.sti_fase_inicio,'dd/mm/yyyy',this)" align="top" alt="Calendário...">
								<input type="text" name="sti_fase_final" id="sti_fase_final" size="10" maxlength="10" value="<?php echo $vData['sti_fase_final']; ?>" onkeyup="formatar(this, '@@/@@/@@@@')" onBlur="revalidar(this,'@@/@@/@@@@','data');" style="height: 18px;">
								<img src="images/calendar_cal.gif" border="0" onclick="displayCalendar(document.solstiform.sti_fase_final,'dd/mm/yyyy',this)" align="top" alt="Calendário...">
							</td>
						  </tr>
						  <tr>
							<td align="left"><label for="sti_fase_horas"> Nº horas *:</label></td>
							<td align="left"><input type="text" id="sti_fase_horas" name="sti_fase_horas" size="9" maxlength="9" value="<?php echo $vData['sti_fase_horas']; ?>"  onkeyup="formatar(this, '@@@@@@@@@@')" style="height:18px;"></td>
						  </tr>
						  <tr>
							<td colspan="3">&nbsp;</td>
						  </tr>
						  <tr>
							<td colspan="3" align="center"  class="tableRodapeModelo1">
							<?php if($_SESSION['funcao']['sti_edicao'] == 1){ ?>
								<input type="button" name="fase_bt_incluir" id="fase_bt_incluir" class="botao" value="Incluir" onclick="javascript:incluirPlanejamentoFase();"/>
								<input type="button" name="fase_bt_confirmar" id="fase_bt_confirmar" class="botao" value="Confirmar" onclick="javascript:alterarPlanejamentoFase();" style="display:none;"/>
							<?php } ?>
							</td>
						  </tr>
						</table>
					</div>
				 </td>
            </tr>
 			  <tr><td>&nbsp;</td></tr>
 			  <tr>
              <td>
  				   <input type="hidden" name="reqieroid" id="reqieroid" value="">
  					<div id="plan_fases_relacao" align="center">
					 <table class="tableMoldura">
						  <?php 
							if($vData['sti_pfase'] > 0){
						   ?>
						<tr class="tableTituloColunas">
							<td width="50%"><h3>Recurso</h3></td>
							<td width="15%"><h3>Data Inicial</h3></td>
							<td width="15%"><h3>Data Final</h3></td>
							<td width="15%"><h3>Nº Horas</h3></td>
							<?php if($_SESSION['funcao']['sti_edicao'] == 1){ ?>
							<td width="5%"><h3>Excluir</h3></td>
							<?php } ?>
						</tr>
						  <?php 
								$vOptions = array();
								$sqlQuery = " 
									SELECT R.reqieroid, 
										U.nm_usuario, 
										R.reqierreqiexoid, 
										to_char(R.reqierdt_inicio,'dd/mm/yyyy') as inicio_exec, 
										R.reqierusuoid_planejamento, 
										R.reqierusuoid_executor, 
										to_char(R.reqierdt_previsao_inicio,'dd/mm/yyyy') as inicio,
										to_char(R.reqierdt_previsao_fim,'dd/mm/yyyy') as final,
										R.reqierhoras_estimadas 
									FROM req_informatica_execucao_recurso AS R LEFT JOIN usuarios AS U
									ON (U.cd_usuario = R.reqierusuoid_executor)
									WHERE R.reqierreqiexoid = " . $vData['sti_pfase'] . "
									AND reqierdt_exclusao IS NULL
									ORDER BY R.reqierreqiexoid 
									LIMIT 2000";
  								$rs = pg_query($conn, $sqlQuery);
								
								$classZebra = 'tde';
								$fPodeAltExc = true;
								if(pg_num_rows($rs) > 0) {
									for($i = 0; $i < pg_num_rows($rs); $i++) {
										$vOptions = pg_fetch_array($rs);
										$classZebra  = ($classZebra == 'tdc') ? 'tde' : 'tdc';
										$fPodeAltExc = ((strlen($vOptions['inicio_exec']) == 0 || $_SESSION['funcao']['sti_acesso_full']==1) && ($_SESSION['funcao']['sti_edicao'] == 1)) ? true : false;
										$imgX = ($imgX == 'images/icones/t1/x.jpg') ? 'images/icones/tf1/x.jpg' : 'images/icones/t1/x.jpg';



								?>
								<tr class="<?php echo $classZebra; ?>">
								   <td><a href="JavaScript:;" <?php if($fPodeAltExc){ ?> onclick="Javascript:xajax_preparaFormAlteracaoRecursoFase(<?php echo $vOptions['reqieroid']; ?>);" <?php }?> > <?php echo $vOptions['nm_usuario']; ?></td>
								   <td><?php echo $vOptions['inicio']; ?></td>
								   <td><?php echo $vOptions['final']; ?></td>
								   <td><?php echo $vOptions['reqierhoras_estimadas']; ?></td>
								   <?php if($fPodeAltExc) : ?>
								   <td align="center"><a href="JavaScript:;" onclick="Javascript:excluirPlanejamentoFase(<?php echo $vOptions['reqieroid']; ?>);"><img src="<?php echo $imgX; ?>"></a></td>
								   <?php elseif ( $_SESSION['funcao']['sti_edicao'] == 1 ) : ?>
                                   <td></td>
                                   <?php endif; ?>
								</tr>
								<?php }
									}else{
								?>
								<tr>
								   <td colspan="5"><span class="msg">Não há recursos planejados </span></td>
								</tr>
							<?php 
									}
								}
							?>
					</table>
					</div>
			    </td>
           </tr>
            <tr class="tableRodapeModelo1"><td align="center">&nbsp; </td></tr>
          </table>		
		  </td>
      </tr>
      <tr height="22">
        <td>Campos assinalados com (*) são de preenchimento obrigatório</td>
      </tr>
    </table>
	 <?php }
		$fluxo_fase_lancamento = trim($_POST['fluxo_fase_lancamento']);
		if($numFasesProgramadas > 0){
		
	 ?>
    <table class="tableMoldura" width="98%">
      <tr class="tableTitulo">
        <td><h3>Fases</h3></td>
      </tr>
      <tr>
        <td><label for="fluxo_fase_lancamento"> Fluxo *:</label>
 			  <SELECT id="fluxo_fase_lancamento" name="fluxo_fase_lancamento" 
				onchange="xajax_getFasesAbas(document.solstiform.fluxo_fase_lancamento.value, 0);">
              <option value=""> Escolha </option>
			  <?php 
		  
				$vOptions = array();
				$sqlQuery = "";
				$sqlQuery .= " SELECT distinct reqifoid, reqifdescricao, reqiexlancamento FROM req_informatica_fluxo, req_informatica_execucao";
				$sqlQuery .= " WHERE reqifdt_exclusao IS NULL and reqiexreqifoid = reqifoid and reqiexreqioid = " . $vData['sti'];
				$sqlQuery .= " ORDER BY reqiexlancamento";
				$sqlQuery .= " LIMIT 1000 ";
				$rs = pg_query($conn, $sqlQuery);
				if(pg_num_rows($rs) > 0) {
					for($i = 0; $i < pg_num_rows($rs); $i++) {
						$vOptions = pg_fetch_array($rs);
					?>
					<option value="<?php echo $vOptions['reqifoid'] . '-' . $vOptions['reqiexlancamento'] . '-' . $vData['sti']; ?>" <?php if($numFasesProgramadas == $vOptions['reqifoid'] . '-' . $vOptions['reqiexlancamento']){ echo ' selected="selected"'; }?>> <?php echo $vOptions['reqiexlancamento'] . ' - ' . $vOptions['reqifdescricao']; ?></option>
					<?php 
					}
				}
			  ?> 
          </SELECT>
		  </td>
      </tr>
		<tr>
		  <td align="center" valign="top">
		  	<div id="fases_abas" align="center">
			
				
			</div>
		  </td>
		</tr>
      <tr>
        <td align="center" valign="top">
			<div id="fases_form" align="center">
			  <table class="tableMoldura" id="form_config_fase" width="98%" align="center">
				<tr height="80">
				  <td>
					<div id="fases_form_area" align="center">
						<span align="left"> 
							&nbsp;&nbsp;&nbsp;&nbsp; Selecione um fluxo.
						</span>
					</div>
				  </td>
				</tr>
			  </table>
		   </div>
		  </td>
      </tr>
	   <?php } ?>
	   
      <tr height="22">
        <td>Campos assinalados com (*) são de preenchimento obrigatório</td>
      </tr>
    </table>
  </form>
</div>
