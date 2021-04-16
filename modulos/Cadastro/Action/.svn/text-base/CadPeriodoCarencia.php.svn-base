<?php
header('Content-Type: text/html; charset=ISO-8859-1');
/**
 * Classe de persistência de dados
*/
require (_MODULEDIR_ . "Cadastro/DAO/CadPeriodoCarenciaDAO.php");


class CadPeriodoCarencia {

	private $dao;
	
	function __construct() {
	
		global $conn;
		$this->dao = new CadPeriodoCarenciaDAO($conn);
		$this->id_usuario = $_SESSION['usuario']['oid'];
	}

	function reativacaoCobrancaMonitoramento($mensagem="") {
		$periodo = '';
		$vigencia = '';
		$mensagemInformativa = $mensagem;
		try {
			$res = $this->dao->buscarUltimoReativacaoCobranca();
			if ($res) {
				$row = pg_fetch_row($res);
				$vigencia = date("d/m/Y", strtotime($row[0]));
				$periodo =  $row[1];
			} else {
				$vigencia = '';
				$periodo = '';
			}
		} catch (Exception $e) {
			$mensagemInformativa = $e->getMessage();
		}
		$tabela = $this->buscarHistoricoReativacao();
		include(_MODULEDIR_ . 'Cadastro/View/cad_periodo_carencia/reativacao_cobranca_monitoramento.php');
	}
	
	function salvarParametrosReativacao() {
		if (($_POST['data_vigencia'] == $_POST['vigencia'])&&($_POST['periodo_vigencia'] == $_POST['periodo'])) {
			$this->reativacaoCobrancaMonitoramento("Esses já são os parâmetros atuais.");
			return;
		}
	
		$vigencia = $_POST['data_vigencia'];
		$periodo = $_POST['periodo_vigencia'];
		$usuarioLogado = Sistema::getUsuarioLogado();
		$mensagem = "";
		try {
			// verifica se há emails com período maior ao que está sendo salvo e exclui-os
			$emails = $this->dao->verificarEmailsPorLimitePeriodo($periodo);
			if ($emails != "") {
				$emailsArr = explode(",", $emails);
				while ($email=$emailsArr) {
					$this->dao->excluirModeloEmail($email[0]);
				}
			}
			$codigoUsuario = $usuarioLogado->cd_usuario;
			if ($this->dao->salvarReativacaoCobranca($periodo, $vigencia, $codigoUsuario)) {
				$mensagem = "Período de carência parametrizado com sucesso.";
			}
		} catch (Exception $e) {
			$mensagem = "Não foi possível salvar parâmetros.";
		}
		$this->reativacaoCobrancaMonitoramento($mensagem);
	}
	
	function buscarHistoricoReativacao() {
		$tabela = "";
		try {
			$cont = 1;
			$resultado = $this->dao->buscarHistoricoReativacaoCobranca();
			$ultDtCadastro = "";
			$ultUsuarioID = "";
			$ultVigencia = "";
			$ultPeriodo = "";
			$primeira = true;
			$class = "";
			if (pg_num_rows($resultado)) {
				while ($row = pg_fetch_array($resultado)) {
					if ($cont == 0) {
						$cont++;
						$class = "tdc";
					} else {
						$cont--;
						$class = "tde";
					}

					if ($primeira) {
						$ultDtCadastro = $row[1];
						$ultUsuarioID = $row[2];
						$ultVigencia = $row[3];
						$ultPeriodo = $row[4];
						$primeira = false;
					} else {
						$nomeUsuario = $this->dao->buscarNomeUsuario($ultUsuarioID);
						$tabela .= "<tr class='".$class."'><td><span style='margin-left:3px;'>".date("d/m/Y H:i", strtotime($ultDtCadastro))."</span></td>";
						$tabela .= "<td><span style='margin-left:3px;'>".$nomeUsuario."</span></td><td>";
						$verificador = false;
						if ($row[3] != $ultVigencia) :
							$tabela .= "<span style='margin-left:3px;'>Início de vigência alterado de ".date('d/m/Y', strtotime($row[3]))." para ".date('d/m/y', strtotime($ultVigencia)).".</span>";
							$verificador = true;
						 endif;
						if ($row[4] != $ultPeriodo) :
							if ($verificador)
								$tabela .= "<div style='clear:both'></div>";
							$tabela .= "<span style='margin-left:3px;'>Período de carência alterado de ".$row[4]." para ".$ultPeriodo." dias.</span>";
							$verificador = true; 
						endif;
						$tabela .= "</span></td></tr>";
						
						$ultDtCadastro = $row[1];
						$ultUsuarioID = $row[2];
						$ultVigencia = $row[3];
						$ultPeriodo = $row[4];
					}				
				}
				if ($cont == 0) {
					$cont++;
					$class = "tdc";
				} else {
					$cont--;
					$class = "tde";
				}
								
				$nomeUsuario = $this->dao->buscarNomeUsuario($ultUsuarioID);
				$tabela .= "<tr class='".$class."'><td><span style='margin-left:3px;'>".date("d/m/Y H:i", strtotime($ultDtCadastro))."</span></td>";
				$tabela .= "<td><span style='margin-left:3px;'>".$nomeUsuario."</span></td><td>";
				$tabela .= "<span style='margin-left:3px;'>Registro criado.</span>";
				$tabela .= "</span></td></tr>";
				return $tabela;
			}
		} catch (Exception $e) {
			$mensagemInformativa = $e->getMessage();	
		}
	}
	
	function manutencaoModelosEmail($mensagem = "") {
		$mensagemInformativa = $mensagem;
		try {
			//$excluir = false;
			$res = $this->dao->buscarModelosEmail();
			$cont = 0;
			$tabela = "";
			$class = "";
			while ($modelo = pg_fetch_array($res)) {
				if ($cont == 0) {
					$cont++;
					$class = "tdc";
				} else {
					$cont--;
					$class = "tde";
				}				
				$nomeUsuario = $this->dao->buscarNomeUsuario($modelo[2]);
				$tabela .= "<tr class='".$class."'><td><a style='margin-left:0px;' href='".$modelo[3]."' class='link_editar_email'>&nbsp;".$modelo[3]." dias</a></td>
								<td><span style='margin-left:0px;'>&nbsp;".$modelo[4]."</span></td>
								<td><span style='margin-left:0px;'>&nbsp;".$nomeUsuario."</span></td>
								<td align='right'>".date('d/m/Y H:i', strtotime($modelo[1]))."&nbsp;&nbsp;</td>
								<td><a href='".$modelo[0]."' class='link_excluir_email'>Excluir</a></td>";
			}
		} catch (Exception $e) {
			$mensagemInformativa = $e->getMessage();
		}
		
		include(_MODULEDIR_ . 'Cadastro/View/cad_periodo_carencia/manutencao_modelos_email.php');
	}
	
	function manutencaoModelosEmailSalvar() {
		try {
			if ((!isset($_POST['excluir']))||($_POST['excluir'] != 'true')) {
				$carencia = $_POST['fim_carencia_email'];
				$assunto = $_POST['assunto_email'];
				$modelo = $_POST['modelo_email'];
				$idModelo = $_POST['idModelo'];
				$usuario = Sistema::getUsuarioLogado();
				$maxCarencia = pg_fetch_row($this->dao->buscarUltimoReativacaoCobrancaRow());
				
				if ($carencia <= $maxCarencia[4]) {
					if ($this->dao->salvarModeloEmail($carencia, $assunto, $modelo, $usuario->cd_usuario)) {
						$mensagemInformativa = "O modelo foi salvo com sucesso.";
					} else {
						$mensagemInformativa = "Erro ao salvar modelo. <br />";
					}
				} else {
					if ($maxCarencia[4] == NULL)
						$mensagemInformativa = "O período de carência deve ser cadastrado antes de um modelo de e-mail ser criado.";
					else
						$mensagemInformativa = "A quantidade de dias para fim da carência deve ser menor ou igual a ".$maxCarencia[4];
				}
			}
			
		} catch (Exception $e) {
			$mensagemInformativa .= $e->getMessage();
		}
		
		$this->manutencaoModelosEmail($mensagemInformativa);
	}
	
	function manutencaoModelosBuscarCarencia() {
		$carencia = $_POST['carencia'];
		try { 
			$maxCarencia = pg_fetch_row($this->dao->buscarUltimoReativacaoCobrancaRow());
			if ($res = $this->dao->buscarModeloCarencia($carencia)) {
				if (pg_num_rows($res) > 0) {
					$row = pg_fetch_row($res);
					$modelo = array (
							'id' => $row[0],
							'assunto' => utf8_encode($row[4]),
							'mensagem' => utf8_encode($row[5]),
							'maximo' =>  $maxCarencia[4],
							'erro' => 0
							);
				} else {
					$modelo = array (
							'erro' => 1,
							'maximo' => $maxCarencia[4]
					);
				}
			}
		} catch (Exception $e) {
				$modelo = array (
						'mensagem' => "Erro ao buscar modelo de email",
						'erro' => 2
						);
		}
		
		echo json_encode($modelo);
		exit;
	}
	
	function manutencaoModelosEmailExcluir() {
		$email = @$_POST['id'];
		try {
			if ($res = $this->dao->excluirModeloEmail($email)) {
				$sucesso = 1;
			}				
		} catch (Exception $e) {
			$sucesso = 0;
			$mensagem = "Erro ao excluir modelo de email";
		}
		echo json_encode($sucesso);
	}
	
	function verificarEmailsPeriodo() {
		$periodo = $_POST['periodo'];
		if ($this->dao->verificarEmailsPorLimitePeriodo($periodo) == "") {
			echo 0;
		} else {
			echo 1;
		}
	}
}