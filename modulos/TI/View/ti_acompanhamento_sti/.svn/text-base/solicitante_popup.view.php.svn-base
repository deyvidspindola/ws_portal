<?php
/**
 * Módulo Principal
 *
 * SASCAR (http://www.sascar.com.br/)
 *
 * @author Jorge A. D. Kautzmann <jorge.kautzmann@sascar.com.br>
 * @description	View da popup de consulta solicitante
 * @version 10/03/2008 [0.0.1]
 * @package SASCAR Intranet
 */
	// Conexão para montar listas COMBO BOX
	global $conn;
	$vResultado = array();
	$rs = null; $i= 0;
	$classZebra = 'tdc';
	$sqlQuery = '';
	// Dados do Formulário
	$vFormPesquisaData = array();
	$vFormPesquisaData['pesq_solicitante'] = (isset($_POST['pesq_solicitante'])) ? trim($_POST['pesq_solicitante']) : '';
 ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1">
<title>Intranet Sascar - STI Solicitações - Pesquisar Solicitante</title>
<head>
<link href="calendar/calendar.css" type="text/css" rel="stylesheet" />
<link type="text/css" rel="stylesheet" href="includes/css/base_form.css">
<script type="text/javascript" src="includes/js/calendar.js"></script>
<script type="text/javascript" src="includes/js/mascaras.js"></script>
<script type="text/javascript" src="includes/js/auxiliares.js"></script>
<script language="Javascript" type="text/javascript" src="includes/js/validacoes.js"></script>
<script type="text/javascript" src="modulos/web/js/ti_acompanhamento_sti.js"></script>
</head>
<table width="100%" cellpadding="0" cellspacing="0" border="0" width="100%" height="25">
  <tr style="background-color: #FFFFFF;">
   <td align="left">
	<div align="center">
	  <form name="solstiform" id="solstiform" method="post" action="ti_acompanhamento_sti.php" enctype="multipart/form-data">
		<input type="hidden" name="acao" id="form_acao" value="">
		<input type="hidden" name="origem_req" id="origem_req" value="">
		<table class="tableMoldura" width="98%">
			<tr class="tableSubTitulo">
			  <td colspan="2"><h2>Busca Solicitante</h2></td>
			</tr>
			<tr>
			  <td align="left" width="40%"><label for="pesq_solicitante">Solicitante:</label></td>
			  <td valign="top" align="left" width="60%">
				 <input type="text" id="pesq_solicitante" name="pesq_solicitante" value="<?php echo $vFormPesquisaData['pesq_solicitante']; ?>" size="25" maxlength="32" style="height: 18px;">
				 <input type="button" name="bt_pesquisar_solicitante" id="bt_pesquisar_solicitante" class="botao" value="Pesquisar" onclick="javascript:pesquisarSolicitante();"/>
			  </td>
			</tr>
 		</table>
		<?php if($vFormPesquisaData['pesq_solicitante'] != ''){ ?>
		<table class="tableMoldura" width="98%">
			<tr class="tableSubTitulo">
			  <td align="left" width="40%"><h3> Solicitante</h3></td>
			  <td align="left" width="60%"><h3> Departamento</h3></td>
			</tr>
			  <?php
				$sqlQuery = "";
				$sqlQuery .= " SELECT cd_usuario, nm_usuario, depdescricao  FROM usuarios, departamento ";
				$sqlQuery .= " WHERE usudepoid = depoid ";
				$sqlQuery .= " AND dt_exclusao IS NULL ";
				$sqlQuery .= " AND cd_usuario IN(SELECT perdusuoid FROM permissao_departamento WHERE cd_usuario = perdusuoid and perddt_exclusao IS NULL) ";
				$sqlQuery .= " AND nm_usuario ilike '%" . $vFormPesquisaData['pesq_solicitante'] . "%' ";
				$sqlQuery .= " ORDER BY nm_usuario ";
				$sqlQuery .= " LIMIT 1000 ";
				$rs = pg_query($conn, $sqlQuery);
				if(pg_num_rows($rs) > 0) {
 					for($i = 0; $i < pg_num_rows($rs); $i++) {
						$classZebra  = ($classZebra == 'tdc') ? 'tde' : 'tdc';
						$vResultado = pg_fetch_array($rs);
				?>
			<tr class="<? echo $classZebra; ?>">
				<td><a href="javascript:;" onclick="javascript:voltarPesquisaSolicitante(<?php echo $vResultado['cd_usuario']; ?>, '<?php echo $vResultado['nm_usuario']; ?>');"><?php echo $vResultado['nm_usuario']; ?></a></td>
				<td><?php echo $vResultado['depdescricao']; ?></td>
			</tr>
				<?php 
					} ?>
			<tr>
				<td colspan="2" align="left" width="100%"><h3> <br> Total de <?php echo pg_num_rows($rs); ?> registros encontrado(s)! <br><br></h3></td>
			</tr>
				<?php 
				}else{
				?>
			<tr>
				<td colspan="2" align="left" width="100%"><h3> <br> Nenhum registro encontrado! <br><br></h3></td>
			</tr>
		<?php } ?>
 		</table>
	<?php } ?>
	  </form>
	</div>

