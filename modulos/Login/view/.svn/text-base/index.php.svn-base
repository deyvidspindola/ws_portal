<?php require_once _MODULEDIR_ . "Principal/View/prn_modificacao_contrato/cabecalho.php"; ?>

<head>
<title>SASCAR Tecnologia e Segurança Automotiva</title>
<link type="text/css" rel="stylesheet" href="includes/css/base_form.css">
<link type="text/css" rel="stylesheet" href="includes/css/calendar.css">
<script language="Javascript" type="text/javascript"
	src="includes/js/jquery-1.3.2.js"></script>
<script language="Javascript" type="text/javascript"
	src="includes/js/calendar.js"></script>
<script language="Javascript" type="text/javascript"
	src="includes/js/mascaras.js"></script>
<script language="Javascript" type="text/javascript"
	src="includes/js/auxiliares.js"></script>
<script language="Javascript" type="text/javascript"
	src="includes/js/validacoes.js"></script>
<script language="Javascript" type="text/javascript"
	src="includes/js/senha_portal_servicos.js"></script>

<script>
function pesquisar(){
	try{
		$('#acao').val('pesquisar');
		$(form).submit();
		
	}catch (e) {
		console.error(e.message)
	}
}
</script>

<form name="form" method="post" action="<?=$PHP_SELF?>">
	
	<input type="hidden" id="geroid" value=""> 
	<input type="text" id="acao" value=""> 
	<input type="hidden" id="gercuoid" value=""> <br>
	<center>
		<table class="tableMoldura">
			<tr class="tableTitulo">
				<td><h1>Cadastro Usuário Integração Sasintegra V3</h1></td>
			</tr>
			<tr height="20">
				<td><span id="div_msg" class="msg"></span></td>
			</tr>

			<tr>
				<td>
					<center>
						<table class="tableMoldura">
							<tr class="tableSubTitulo">
								<td colspan="4"><h2>Dados para Pesquisa</h2></td>
							</tr>

							<tr id="mensagemSucesso" style="display: none">
								<td colspan="3" class="msgSucesso"></td>
							</tr>
							<tr id="mensagemErro" style="display: none;">
								<td colspan="3" class="msgError"></td>
								<span style="color: red;"></span>
							</tr>


							<tr>
								<td width="5%"><label for='gernome_busca'>Nome:</label></td>
								<td width="35%"><input type="text" name="gernome_busca"
									id="gernome_busca" size="50" value=""></td>
								<td width="5%"><label for='busca_gersoftware'>Software:</label></td>
								<td width="35%"><SELECT name="busca_gersoftware"
									id="busca_gersoftware">
										<option value="">Todos</option>
										<option value="s">SASGC</option>
										<option value="i">INTEGRAÇÃO</option>
										<option value="ss">Sem Software</option>
										<option value="si">Software Indiferente</option>
								</select></td>
							</tr>
							<tr>
								<td width="5%"><label for='busca_tipo'>Tipo:</label></td>
								<td width="35%"><SELECT name='busca_tipo' id='busca_tipo'><option
											value=''>Todos</option>
										<option value='1'>CLIENTE</option>
										<option value='6'>FROTAS</option>
										<option value='3'>PA</option>
										<option value='4'>SUPORTE SASCAR</option>
										<option value='2'>TEGMA</option>
										<option value='5'>TELEMETRIA</option></select></td>
							</tr>

							<tr class="tableRodapeModelo1">
								<td colspan="4" align="center"><input type="button"
									class="botao" name="btn_pesquisar" id="btn_pesquisar"
									value="Pesquisar" OnClick="pesquisar();" style="width: 90px;">
									<input type="button" class="botao" name="btn_novo"
									id="btn_novo" value="Novo" OnClick="def_acao('novo');"
									style="width: 90px;"></td>
							</tr>
						</table>
					</center>
				</td>
			</tr>
		</table>
	</center>

	<center>
		<table class="tableMoldura">
			<tbody>
				<tr class="tableSubTitulo">
					<td colspan="2"><h2>Dados para Pesquisa</h2></td>
				</tr>

				<tr>
					<td>
						<table width="100%" border="0" cellspacing="1" cellpadding="1"
							bgcolor="#FFFFFF">
							<tbody>
								<tr class="tableTituloColunas">
									<td align="center"><h3>
											Acesso <br> CHAT
										</h3></td>
									<td align="center"><h3>Nome</h3></td>
									<td align="center"><h3>Tipo</h3></td>
									<td align="center"><h3>CNPJ</h3></td>
									<td align="center"><h3>Cidade</h3></td>
									<td align="center"><h3>UF</h3></td>
									<td align="center"><h3>Fone</h3></td>
									<td align="center"><h3>Software</h3></td>
									<td align="center"><h3>Integração</h3></td>
									<td align="center"><h3>Docto.Anexado</h3></td>
								</tr>

								<tr class="linha_interc0">
									<td class="item_tab" align="center"><input type="checkbox"
										id="id_acessochat[0]" name="bt_acessochat[]" value="57"
										checked="checked"> <input type="hidden"
										name="bt_acessochat_off[]" value="57"></td>

									<td class="item_tab"><a
										href="javascript:def_acao('editar',0,'document.form.geroid.value=57');">
											OPENTECH SISTEMAS DE GERENCIAMENTO DE RISCOS S/A (INTEGRACAO)
									</a></td>
									<td>TEGMA</td>
									<td align="center">04.368.185/0001-42</td>
									<td>JOINVILLE</td>
									<td align="center">SC</td>
									<td>(47) 2101-6122</td>
									<td align="center"><b><img src="images/icones/t1/v.png"></b></td>
									<td align="center"><b></b></td>
									<td align="center">Não</td>
								</tr>

								<tr class="tableRodapeModelo3">
									<td><input type="button" value="+"
										onclick="seleciona_acessochat(1, 2)"> <input type="button"
										value="-" onclick="seleciona_acessochat(0, 2)"></td>
									<td align="center"><input type="button" value="Atualizar"
										onclick="def_acao('atualizaStatus');"> <!-- --></td>
									<td colspan="15" align="center">
										<center>2 registro(s) encontrado(s) 240</center>
									</td>
								</tr>
							</tbody>
						</table>
					</td>
				</tr>
			</tbody>
		</table>
	</center>
      
	
</form>

<?php
require_once _MODULEDIR_ . "Principal/View/prn_modificacao_contrato/rodape.php";
?>