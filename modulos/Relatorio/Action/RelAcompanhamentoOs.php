<?php
require _MODULEDIR_."/Relatorio/DAO/RelAcompanhamentoOsDAO.php";
require _SITEDIR_ . "lib/excelwriter.inc.php";
require _SITEDIR_ . "lib/Components/PHPExcel/PHPExcel.php";

/**
 * Relatório de acompanhamento de ordens de serviço
 * 
 * @author Gabriel Luiz Pereira
 * @since 08/11/2012
 */
class RelAcompanhamentoOs {
	
	/**
	 * Acesso a dados do módulo
	 * @var RelAcompanhamentoOsDAO
	 */
	private $DAO;
	
	/**
	 * Construtor
	 */
	public function __construct() {
		
		global $conn;
		$this->DAO = new RelAcompanhamentoOsDAO($conn);
	}
	
	/**
	 * Página index
	 */
	public function index() {
		
		// Popula listas
		$statusList 			= $this->DAO->getStatusList();
		$tipoSolicitacaoList 	= $this->DAO->getTipoSolicitacaoList();
		$tipoOsList 			= $this->DAO->getOSTipoList(); 
		$equipamentoClasseList	= $this->DAO->getEquipamentoClasseList();
		$tipoContratoList		= $this->DAO->getTipoContratoList();
		$modeloEquipamentoList	= $this->DAO->getModeloVersaoList();
		$defeitosList			= $this->DAO->getDefeitosList();
		$usuariosList			= $this->DAO->getUsuariosList();
		
		$tiposRelatorio = array(
				'A'	=> 'Analítico',
				'S' => 'Sintético'
		);
		
		$itemList	 = array(
				'A' => 'ACESSÓRIO',
				'E'	=> 'EQUIPAMENTO'		
		);
		
		
		// Renderiza a tela
		ob_start();
		include _MODULEDIR_."/Relatorio/View/rel_acompanhamento_os/filtro.php";
		$html = ob_get_contents();
		ob_end_clean();
		echo $html;
	}
	
	/**
	 * Retorna as versões de acordo com o modelo do equipamento
	 */
	public function buscaVersoesModelo() {
		
		ob_start();
		try {
			
			$modelo 		= $_POST['modelo'];
			$versoesList	= $this->DAO->getVersaoEquipamentoList($modelo);
			
			$retorno		= array(
				'erro'		=> false,
				'codigo'	=> 0,
				'retorno'	=> 	$versoesList
			);
			
			echo json_encode($retorno);
			ob_flush();
			exit;
		}
		catch (Exception $e) {
			
			ob_end_clean();
			$retorno		= array(
				'erro'		=> true,
				'codigo'	=> $e->getCode(),
				'retorno'	=> 	$e->getMessage()
			);
			echo json_encode($retorno);
			exit;
		}
	}
	
	/**
	 * Retorna os motivos de acordo com o item
	 */
	public function buscaMotivos() {
	
		ob_start();
		try {
			
			$item 			= !empty($_POST['item']) ? $_POST['item'] : 'NULL' ;
			$tipo 			= !empty($_POST['tipo']) ? $_POST['tipo'] : 'NULL' ;
			
			$motivosList	= $this->DAO->getMotivosList($item, $tipo);
			
			$retorno		= array(
					'erro'		=> false,
					'codigo'	=> 0,
					'retorno'	=> 	$motivosList
			);
			
			echo json_encode($retorno);
			ob_flush();
			exit;
		}
		catch (Exception $e) {
			
			ob_end_clean();
			$retorno		= array(
					'erro'		=> true,
					'codigo'	=> $e->getCode(),
					'retorno'	=> 	$e->getMessage()
			);
			echo json_encode($retorno);
			exit;
		}
	}
	
	/**
	 * Gera o relatório
	 */
	public function pesquisar() {
		
		$filtros 		= array();
		$tipoRelatorio 	= empty($_POST['tipo_relatorio']) ? 'A' : $_POST['tipo_relatorio'];
		$relatorio		= null;
		$relatorioHtml	= "";
		
		ob_start();
		try {
		
			if (isset($_POST['dt_ini']) && isset($_POST['dt_fim'])) {
					
				$filtros['data_inicio'] 		= $_POST['dt_ini'];
				$filtros['data_fim'] 			= $_POST['dt_fim'];
				$filtros['status']				= $_POST['status'];
				$filtros['tipo_solicitacao']	= $_POST['tipo_solicitacao'];
				$filtros['tipo_contrato']		= $_POST['tipo_contrato'];
				$filtros['classe_contrato']		= $_POST['classe_contrato'];
				$filtros['cliente']				= $_POST['cliente'];
				$filtros['placa']				= strtoupper($_POST['placa']);
				$filtros['item']				= $_POST['item'];
				$filtros['tipo']				= $_POST['tipo'];
				$filtros['motivo']				= $_POST['motivo'];
				$filtros['modelo']				= $_POST['modelo_equipamento'];
				$filtros['versao']				= $_POST['versao_equipamento'];
				$filtros['defeito_alegado']		= $_POST['defeito_alegado'];
				$filtros['defeito_constatado']	= $_POST['defeito_constatado'];
				$filtros['responsavel_abertura']= $_POST['responsavel_abertura'];
				$filtros['responsavel_autorizacao']= $_POST['responsavel_autorizacao'];
				$filtros['responsavel_cancelamento']= $_POST['responsavel_cancelamento'];
				$filtros['responsavel_conclusao']= $_POST['responsavel_conclusao'];
			}
			
			if ($tipoRelatorio == 'A') {
				$relatorio = $this->geraAnalitico($filtros);
				include _MODULEDIR_."/Relatorio/View/rel_acompanhamento_os/resultado_analitico.php";
				
				$relatorioHtml = ob_get_contents();
			}
			else {
				
				$relatorio = $this->geraSintetico($filtros);
				
				// Gera agrupamentos para o relatório
				$listaStatus				= array();
				$grupoPorStatus				= array();
				$totalPorStatus 			= 0;
				
				$listaClasseEquipamento		= array();
				$grupoPorClasseEquipamento	= array();
				$totalPorClasseEquipamento 	= 0;
				
				$listaVersaoEquipamento		= array();
				$grupoPorVersaoEquipamento	= array();
				$totalPorVersaoEquipamento	= 0;
				
				$listaDefeitoAlegado		= array();
				$grupoPorDefeitoAlegado		= array();
				$totalPorDefeitoAlegado		= 0;
				
				$listaDefeitoConstatado		= array();
				$grupoPorDefeitoConstatado	= array();
				$totalPorDefeitoConstatado	= 0;
				
				$listaTipo					= array();
				$grupoPorTipo				= array();
				$totalPorTipo				= 0;
				
				$listaMotivo				= array();
				$grupoPorMotivo				= array();
				$totalPorMotivo				= 0;
				
				$listaTipoSolicitacao		= array();
				$grupoPorTipoSolicitacao	= array();
				$totalPorTipoSolicitacao	= 0;
				
				if ($relatorio !== null) {
					while ($row = pg_fetch_object($relatorio)) {
						
						if (!in_array($row->status, $listaStatus)) {
							
							$listaStatus[] = $row->status;
							$grupoPorStatus[] = array(
									'nome'		=>$row->status, 
									'quantidade'=>$row->total_status
							);
							
							$totalPorStatus += $row->total_status;
						}
						
						if (!in_array($row->classe_contrato, $listaClasseEquipamento)) {
						
							$listaClasseEquipamento[] = $row->classe_contrato;
							
							$grupoPorClasseEquipamento[] = array(
									'nome'		=>$row->classe_contrato,
									'quantidade'=>$row->total_classe_contrato
							);
						
							$totalPorClasseEquipamento += $row->total_classe_contrato;
						}
						
						if (!in_array($row->versao, $listaVersaoEquipamento) && !empty($row->versao)) {
								
							$listaVersaoEquipamento[] = $row->versao;
						
							$grupoPorVersaoEquipamento[] = array(
									'nome'		=>$row->versao,
									'quantidade'=>$row->total_versao
							);
								
							$totalPorVersaoEquipamento += $row->total_versao;
						}
						
						if (!in_array($row->defeito_alegado, $listaDefeitoAlegado) && !empty($row->defeito_alegado)) {
								
							$listaDefeitoAlegado[] = $row->defeito_alegado;
								
							$grupoPorDefeitoAlegado[] = array(
									'nome'		=>$row->defeito_alegado,
									'quantidade'=>$row->total_defeito_alegado
							);
								
							$totalPorDefeitoAlegado += $row->total_defeito_alegado;
						}
						
						if (!in_array($row->defeito_constatado, $listaDefeitoConstatado) && !empty($row->defeito_constatado)) {
								
							$listaDefeitoConstatado[] = $row->defeito_constatado;
								
							$grupoPorDefeitoConstatado[] = array(
									'nome'		=>$row->defeito_constatado,
									'quantidade'=>$row->total_defeito_constatado
							);
								
							$totalPorDefeitoConstatado += $row->total_defeito_constatado;
						}
						
						if (!in_array($row->tipo, $listaTipo) && !empty($row->tipo)) {
								
							$listaTipo[] = $row->tipo;
								
							$grupoPorTipo[] = array(
									'nome'		=>$row->tipo,
									'quantidade'=>$row->total_tipo
							);
								
							$totalPorTipo += $row->total_tipo;
						}
						
						if (!in_array($row->motivo, $listaMotivo) && !empty($row->motivo)) {
								
							$listaMotivo[] = $row->motivo;
								
							$grupoPorMotivo[] = array(
									'nome'		=>$row->motivo,
									'quantidade'=>$row->total_motivo
							);
								
							$totalPorMotivo += $row->total_motivo;
						}
						
						if (!in_array($row->tipo_solicitacao, $listaTipoSolicitacao) && !empty($row->tipo_solicitacao)) {
								
							$listaTipoSolicitacao[] = $row->tipo_solicitacao;
								
							$grupoPorTipoSolicitacao[] = array(
									'nome'		=>$row->tipo_solicitacao,
									'quantidade'=>$row->total_tipo_solicitacao
							);
								
							$totalPorTipoSolicitacao += $row->total_tipo_solicitacao;
						}
					}
					
					$grupoPorStatus            = self::ordenaMultiArray($grupoPorStatus, 'quantidade');
					$grupoPorClasseEquipamento = self::ordenaMultiArray($grupoPorClasseEquipamento, 'quantidade');
					$grupoPorVersaoEquipamento = self::ordenaMultiArray($grupoPorVersaoEquipamento, 'quantidade');
					$grupoPorDefeitoAlegado    = self::ordenaMultiArray($grupoPorDefeitoAlegado, 'quantidade');
					$grupoPorDefeitoConstatado = self::ordenaMultiArray($grupoPorDefeitoConstatado, 'quantidade');
					$grupoPorTipo              = self::ordenaMultiArray($grupoPorTipo, 'quantidade');
					$grupoPorMotivo            = self::ordenaMultiArray($grupoPorMotivo, 'quantidade');
					$grupoPorTipoSolicitacao   = self::ordenaMultiArray($grupoPorTipoSolicitacao, 'quantidade');
				}
				
				include _MODULEDIR_."/Relatorio/View/rel_acompanhamento_os/resultado_sintetico.php";
				
				$relatorioHtml = ob_get_contents();
			}
			ob_end_clean();
			
			$retorno		= array(
					'erro'		=> false,
					'codigo'	=> 0,
					'retorno'	=> utf8_encode($relatorioHtml)
			);
			
			echo json_encode($retorno);
			exit;
		}
		catch (Exception $e) {
			
			ob_end_clean();
			$retorno		= array(
					'erro'		=> true,
					'codigo'	=> $e->getCode(),
					'retorno'	=> $e->getMessage()
			);
			echo json_encode($retorno);
			exit;
		}
	}
	
	
	/**
	 * Gera o relatório em XLS
	 */
	public function gerarXls() {
		set_time_limit(60);
		
		$filtros 		= array();
		$tipoRelatorio 	= empty($_POST['tipo_relatorio']) ? 'A' : $_POST['tipo_relatorio'];
		$relatorio		= null;
		$detalhe		= '';
		
		try {
		
			if (isset($_POST['dt_ini']) && isset($_POST['dt_fim'])) {
					
				$filtros['data_inicio'] 		= urldecode($_POST['dt_ini']);
				$filtros['data_fim'] 			= urldecode($_POST['dt_fim']);
				$filtros['status']				= urldecode($_POST['status']);
				$filtros['tipo_solicitacao']	= urldecode($_POST['tipo_solicitacao']);
				$filtros['tipo_contrato'] 		= explode(',', urldecode($_POST['tipo_contrato']));
				$filtros['classe_contrato']		= urldecode($_POST['classe_contrato']);
				$filtros['cliente']				= urldecode($_POST['cliente']);
				$filtros['placa']				= strtoupper(urldecode($_POST['placa']));
				$filtros['item']				= urldecode($_POST['item']);
				$filtros['tipo']				= urldecode($_POST['tipo']);
				$filtros['motivo']				= urldecode($_POST['motivo']);
				$filtros['modelo']				= urldecode($_POST['modelo_equipamento']);
				$filtros['versao']				= urldecode($_POST['versao_equipamento']);
				$filtros['defeito_alegado']		= urldecode($_POST['defeito_alegado']);
				$filtros['defeito_constatado']	= urldecode($_POST['defeito_constatado']);
				$filtros['responsavel_abertura']= urldecode($_POST['responsavel_abertura']);
				$filtros['responsavel_autorizacao']= urldecode($_POST['responsavel_autorizacao']);
				$filtros['responsavel_cancelamento']= urldecode($_POST['responsavel_cancelamento']);
				$filtros['responsavel_conclusao']= urldecode($_POST['responsavel_conclusao']);
			}
			
			/*
			 * Gera arquivo XLS
			 */
			if ($tipoRelatorio == 'A') {
				$conteudo  = "";
				$relatorio = $this->geraAnalitico($filtros);
				
				/**
				 * IMPORTANTE: Limpa as variáveis que não serão mais utilizadas, devido
				 * a um problema de estouro de memória (dependendo da quantidade de re-
				 * gistros encontrados pelo consulta).
				 */
				unset($filtros);
				
				header('Content-Type: text/csv');
				header('Content-Disposition: attachment;filename=rel_acompanhamento_os_analitico.csv');
				header('Cache-Control: max-age=0');
				
				$conteudo = "DATA;";
				$conteudo.= "NºORDEM;";
				$conteudo.= "CLIENTE;";
				$conteudo.= "PLACA;";
				$conteudo.= "RECORRÊNCIA;";
				$conteudo.= "STATUS;";
				$conteudo.= "ITEM;";
				$conteudo.= "TIPO;";
				$conteudo.= "MOTIVO;";
				$conteudo.= "TIPO CONTRATO;";
				$conteudo.= "CLASSE CONTRATO;";
				$conteudo.= "MODELO EQUIPAMENTO;";
				$conteudo.= "VERSÃO EQUIPAMENTO;";
				$conteudo.= "DEFEITO ALEGADO;";
				$conteudo.= "DEFEITO CONSTATADO;";
				$conteudo.= "RESPONSÁVEL ABERTURA;";
				$conteudo.= "RESPONSÁVEL AUTORIZAÇÃO;";
				$conteudo.= "RESPONSÁVEL CANCELAMENTO;";
				$conteudo.= "RESPONSÁVEL CONCLUSÃO;";
				
				echo $conteudo."\n";
				
				if ($relatorio !== null && $relatorio !== false && is_resource($relatorio)) {
					$ordem_servico = null;
					
					while($row = pg_fetch_object($relatorio)) {
						if($ordem_servico == $row->ordem_servico) {
							$conteudo = ";";
							$conteudo.= ";";
							$conteudo.= ";";
							$conteudo.= ";";
						} else {
							$ordem_servico = $row->ordem_servico;
							
							$conteudo = $row->data.";";
							$conteudo.= $row->ordem_servico.";";
							$conteudo.= $row->cliente.";";
							$conteudo.= $row->placa.";";
						}
						
						$conteudo.= $row->status == 'Cancelado' ? ";" : $row->recorrencia.";";
						$conteudo.= $row->status.";";
						$conteudo.= $row->item.";";
						$conteudo.= $row->tipo.";";
						$conteudo.= $row->motivo.";";
						$conteudo.= $row->tipo_contrato.";";
						$conteudo.= $row->classe_contrato.";";
						$conteudo.= $row->modelo.";";
						$conteudo.= $row->versao.";";
						$conteudo.= $row->defeito_alegado.";";
						$conteudo.= $row->defeito_constatado.";";
						$conteudo.= $row->responsavel_abertura.";";
						$conteudo.= $row->responsavel_autorizacao.";";
						$conteudo.= $row->responsavel_cancelamento.";";
						$conteudo.= $row->responsavel_conclusao.";";
						
						echo $conteudo."\n";
					}
				} else {
					echo "Nenhum resultado encontrado;\n";
				}
				
				exit;
			}
			else {
				
				$PHPExcel = new PHPExcel();
				
				$detalhe = 'sintetico';
				
				$relatorio = $this->geraSintetico($filtros);
				
				// Gera agrupamentos para o relatório
				$listaStatus				= array();
				$grupoPorStatus				= array();
				$totalPorStatus 			= 0;
				
				$listaClasseEquipamento		= array();
				$grupoPorClasseEquipamento	= array();
				$totalPorClasseEquipamento 	= 0;
				
				$listaVersaoEquipamento		= array();
				$grupoPorVersaoEquipamento	= array();
				$totalPorVersaoEquipamento	= 0;
				
				$listaDefeitoAlegado		= array();
				$grupoPorDefeitoAlegado		= array();
				$totalPorDefeitoAlegado		= 0;
				
				$listaDefeitoConstatado		= array();
				$grupoPorDefeitoConstatado	= array();
				$totalPorDefeitoConstatado	= 0;
				
				$listaTipo					= array();
				$grupoPorTipo				= array();
				$totalPorTipo				= 0;
				
				$listaMotivo				= array();
				$grupoPorMotivo				= array();
				$totalPorMotivo				= 0;
				
				$listaTipoSolicitacao		= array();
				$grupoPorTipoSolicitacao	= array();
				$totalPorTipoSolicitacao	= 0;
				
				if ($relatorio !== null) {
					while ($row = pg_fetch_object($relatorio)) {
							
						if (!in_array($row->status, $listaStatus)) {
					
							$listaStatus[] = $row->status;
							$grupoPorStatus[] = array(
									'nome'		=>$row->status,
									'quantidade'=>(int)$row->total_status
							);
					
							$totalPorStatus += $row->total_status;
						}
							
						if (!in_array($row->classe_contrato, $listaClasseEquipamento)) {
								
							$listaClasseEquipamento[] = $row->classe_contrato;
					
							$grupoPorClasseEquipamento[] = array(
									'nome'		=>$row->classe_contrato,
									'quantidade'=> $row->total_classe_contrato
							);
								
							$totalPorClasseEquipamento += $row->total_classe_contrato;
						}
							
						if (!in_array($row->versao, $listaVersaoEquipamento) && !empty($row->versao)) {
								
							$listaVersaoEquipamento[] = $row->versao;
								
							$grupoPorVersaoEquipamento[] = array(
									'nome'		=>$row->versao,
									'quantidade'=>$row->total_versao
							);
								
							$totalPorVersaoEquipamento += $row->total_versao;
						}
							
						if (!in_array($row->defeito_alegado, $listaDefeitoAlegado) && !empty($row->defeito_alegado)) {
								
							$listaDefeitoAlegado[] = $row->defeito_alegado;
								
							$grupoPorDefeitoAlegado[] = array(
									'nome'		=>$row->defeito_alegado,
									'quantidade'=>$row->total_defeito_alegado
							);
								
							$totalPorDefeitoAlegado += $row->total_defeito_alegado;
						}
							
						if (!in_array($row->defeito_constatado, $listaDefeitoConstatado) && !empty($row->defeito_constatado)) {
								
							$listaDefeitoConstatado[] = $row->defeito_constatado;
								
							$grupoPorDefeitoConstatado[] = array(
									'nome'		=>$row->defeito_constatado,
									'quantidade'=>$row->total_defeito_constatado
							);
								
							$totalPorDefeitoConstatado += $row->total_defeito_constatado;
						}
							
						if (!in_array($row->tipo, $listaTipo) && !empty($row->tipo)) {
								
							$listaTipo[] = $row->tipo;
								
							$grupoPorTipo[] = array(
									'nome'		=>$row->tipo,
									'quantidade'=>$row->total_tipo
							);
								
							$totalPorTipo += $row->total_tipo;
						}
							
						if (!in_array($row->motivo, $listaMotivo) && !empty($row->motivo)) {
								
							$listaMotivo[] = $row->motivo;
								
							$grupoPorMotivo[] = array(
									'nome'		=>$row->motivo,
									'quantidade'=>$row->total_motivo
							);
								
							$totalPorMotivo += $row->total_motivo;
						}
							
						if (!in_array($row->tipo_solicitacao, $listaTipoSolicitacao) && !empty($row->tipo_solicitacao)) {
								
							$listaTipoSolicitacao[] = $row->tipo_solicitacao;
								
							$grupoPorTipoSolicitacao[] = array(
									'nome'		=>$row->tipo_solicitacao,
									'quantidade'=>$row->total_tipo_solicitacao
							);
								
							$totalPorTipoSolicitacao += $row->total_tipo_solicitacao;
						}
					}
					
					$grupoPorStatus            = self::ordenaMultiArray($grupoPorStatus, 'quantidade');
					$grupoPorClasseEquipamento = self::ordenaMultiArray($grupoPorClasseEquipamento, 'quantidade');
					$grupoPorVersaoEquipamento = self::ordenaMultiArray($grupoPorVersaoEquipamento, 'quantidade');
					$grupoPorDefeitoAlegado    = self::ordenaMultiArray($grupoPorDefeitoAlegado, 'quantidade');
					$grupoPorDefeitoConstatado = self::ordenaMultiArray($grupoPorDefeitoConstatado, 'quantidade');
					$grupoPorTipo              = self::ordenaMultiArray($grupoPorTipo, 'quantidade');
					$grupoPorMotivo            = self::ordenaMultiArray($grupoPorMotivo, 'quantidade');
					$grupoPorTipoSolicitacao   = self::ordenaMultiArray($grupoPorTipoSolicitacao, 'quantidade');
				}
				
				/*
				 * Gera arquivo
				 */
				$PHPExcel->getActiveSheet()->getColumnDimension('A')->setAutoSize(true);
				$PHPExcel->getActiveSheet()->getColumnDimension('B')->setAutoSize(true);
				
				$PHPExcel->getActiveSheet()->setCellValue('A1', utf8_encode('O.S. GERADA - TOTAL POR STATUS'));
				$PHPExcel->getActiveSheet()->setCellValue('B1', utf8_encode('QUANTIDADE'));
				
				$linha = '2';
				foreach($grupoPorStatus as $status) {
					
					$PHPExcel->getActiveSheet()->setCellValue('A'.$linha, utf8_encode($status['nome']));
					$PHPExcel->getActiveSheet()->setCellValue('B'.$linha, utf8_encode($status['quantidade']));
					$linha++;
				}
				
				$PHPExcel->getActiveSheet()->setCellValue('A'.$linha, utf8_encode('TOTAL'));
				$PHPExcel->getActiveSheet()->setCellValue('B'.$linha, utf8_encode($totalPorStatus));
				$linha++;
				$linha++;
				
				$PHPExcel->getActiveSheet()->setCellValue('A'.$linha, utf8_encode('O.S. GERADA - TOTAL POR CLASSE DE EQUIPAMENTO'));
				$PHPExcel->getActiveSheet()->setCellValue('B'.$linha, utf8_encode('QUANTIDADE'));
				$linha++;
				
				foreach($grupoPorClasseEquipamento as $classe) {
						
					$PHPExcel->getActiveSheet()->setCellValue('A'.$linha, utf8_encode($classe['nome']));
					$PHPExcel->getActiveSheet()->setCellValue('B'.$linha, utf8_encode($classe['quantidade']));
					$linha++;
				}
				
				$PHPExcel->getActiveSheet()->setCellValue('A'.$linha, utf8_encode('TOTAL'));
				$PHPExcel->getActiveSheet()->setCellValue('B'.$linha, utf8_encode($totalPorClasseEquipamento));
				$linha++;
				$linha++;
				
				
				$PHPExcel->getActiveSheet()->setCellValue('A'.$linha, utf8_encode('O.S. GERADA - TOTAL POR VERSÃO DE EQUIPAMENTO'));
				$PHPExcel->getActiveSheet()->setCellValue('B'.$linha, utf8_encode('QUANTIDADE'));
				$linha++;
				
				foreach($grupoPorVersaoEquipamento as $versao) {
				
					$PHPExcel->getActiveSheet()->setCellValue('A'.$linha, utf8_encode($versao['nome']));
					$PHPExcel->getActiveSheet()->setCellValue('B'.$linha, utf8_encode($versao['quantidade']));
					$linha++;
				}
				
				$PHPExcel->getActiveSheet()->setCellValue('A'.$linha, utf8_encode('TOTAL'));
				$PHPExcel->getActiveSheet()->setCellValue('B'.$linha, utf8_encode($totalPorVersaoEquipamento));
				$linha++;
				$linha++;
				
				
				
				$PHPExcel->getActiveSheet()->setCellValue('A'.$linha, utf8_encode('O.S. GERADA - TOTAL POR DEFEITO ALEGADO'));
				$PHPExcel->getActiveSheet()->setCellValue('B'.$linha, utf8_encode('QUANTIDADE'));
				$linha++;
				
				foreach($grupoPorDefeitoAlegado as $defeitoAlegado) {
				
					$PHPExcel->getActiveSheet()->setCellValue('A'.$linha, utf8_encode($defeitoAlegado['nome']));
					$PHPExcel->getActiveSheet()->setCellValue('B'.$linha, utf8_encode($defeitoAlegado['quantidade']));
					$linha++;
				}
				
				$PHPExcel->getActiveSheet()->setCellValue('A'.$linha, utf8_encode('TOTAL'));
				$PHPExcel->getActiveSheet()->setCellValue('B'.$linha, utf8_encode($totalPorDefeitoAlegado));
				$linha++;
				$linha++;
				
				
				$PHPExcel->getActiveSheet()->setCellValue('A'.$linha, utf8_encode('O.S. GERADA - TOTAL POR DEFEITO CONSTATADO'));
				$PHPExcel->getActiveSheet()->setCellValue('B'.$linha, utf8_encode('QUANTIDADE'));
				$linha++;
				
				foreach($grupoPorDefeitoConstatado as $defeitoConstatado) {
				
					$PHPExcel->getActiveSheet()->setCellValue('A'.$linha, utf8_encode($defeitoConstatado['nome']));
					$PHPExcel->getActiveSheet()->setCellValue('B'.$linha, utf8_encode($defeitoConstatado['quantidade']));
					$linha++;
				}
				
				$PHPExcel->getActiveSheet()->setCellValue('A'.$linha, utf8_encode('TOTAL'));
				$PHPExcel->getActiveSheet()->setCellValue('B'.$linha, utf8_encode($totalPorDefeitoConstatado));
				$linha++;
				$linha++;
				
				
				$PHPExcel->getActiveSheet()->setCellValue('A'.$linha, utf8_encode('O.S. GERADA - TOTAL POR TIPO'));
				$PHPExcel->getActiveSheet()->setCellValue('B'.$linha, utf8_encode('QUANTIDADE'));
				$linha++;
				
				foreach($grupoPorTipo as $tipo) {
				
					$PHPExcel->getActiveSheet()->setCellValue('A'.$linha, utf8_encode($tipo['nome']));
					$PHPExcel->getActiveSheet()->setCellValue('B'.$linha, utf8_encode($tipo['quantidade']));
					$linha++;
				}
				
				$PHPExcel->getActiveSheet()->setCellValue('A'.$linha, utf8_encode('TOTAL'));
				$PHPExcel->getActiveSheet()->setCellValue('B'.$linha, utf8_encode($totalPorTipo));
				$linha++;
				$linha++;
				
				
				$PHPExcel->getActiveSheet()->setCellValue('A'.$linha, utf8_encode('O.S. GERADA - TOTAL POR MOTIVO'));
				$PHPExcel->getActiveSheet()->setCellValue('B'.$linha, utf8_encode('QUANTIDADE'));
				$linha++;
				
				foreach($grupoPorMotivo as $motivo) {
				
					$PHPExcel->getActiveSheet()->setCellValue('A'.$linha, utf8_encode($motivo['nome']));
					$PHPExcel->getActiveSheet()->setCellValue('B'.$linha, utf8_encode($motivo['quantidade']));
					$linha++;
				}
				
				$PHPExcel->getActiveSheet()->setCellValue('A'.$linha, utf8_encode('TOTAL'));
				$PHPExcel->getActiveSheet()->setCellValue('B'.$linha, utf8_encode($totalPorMotivo));
				$linha++;
				$linha++;
				
				
				$PHPExcel->getActiveSheet()->setCellValue('A'.$linha, utf8_encode('O.S. GERADA - TOTAL POR TIPO DE SOLICITAÇÃO'));
				$PHPExcel->getActiveSheet()->setCellValue('B'.$linha, utf8_encode('QUANTIDADE'));
				$linha++;
				
				foreach($grupoPorTipoSolicitacao as $tipoSolicitacao) {
				
					$PHPExcel->getActiveSheet()->setCellValue('A'.$linha, utf8_encode($tipoSolicitacao['nome']));
					$PHPExcel->getActiveSheet()->setCellValue('B'.$linha, utf8_encode($tipoSolicitacao['quantidade']));
					$linha++;
				}
				
				$PHPExcel->getActiveSheet()->setCellValue('A'.$linha, utf8_encode('TOTAL'));
				$PHPExcel->getActiveSheet()->setCellValue('B'.$linha, utf8_encode($totalPorTipoSolicitacao));
				$linha++;
				$linha++;

				header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
				header('Content-Disposition: attachment;filename=rel_acompanhamento_os_'.$detalhe.'.xls');
				header('Cache-Control: max-age=0');
					
				$writer = PHPExcel_IOFactory::createWriter($PHPExcel, 'Excel2007');
				$writer->setPreCalculateFormulas(false);
				$writer->save('php://output');
				exit;
			}
			
		}
		catch (Exception $e) {
				
			ob_end_clean();
			$retorno		= array(
					'erro'		=> true,
					'codigo'	=> $e->getCode(),
					'retorno'	=> $e->getMessage()
			);
			echo json_encode($retorno);
			exit;
		}
	}

	/**
	 * Retorna a massa de dados analítica
	 * @param array $filtros
	 * @return Ambigous <resource, NULL>
	 */
	public function geraAnalitico($filtros) {
		return $this->DAO->getAnalitico($filtros);
	}
	
	/**
	 * Retorna a massa de dados sintetizada
	 * @param array  $filtros
	 * @return Ambigous <resource, NULL>
	 */
	public function geraSintetico($filtros) {
		return $this->DAO->getSintetico($filtros);
	}
	
	private function ordenaMultiArray($array, $field) {
		if(is_array($array) && is_string($field)) {
			$temp = array();
			
			foreach($array as $key => $row) {
				$temp[$key] = $row[$field];
			}
			
			array_multisort($temp, SORT_DESC, $array);
		}
		
		return $array;
	}
	
}