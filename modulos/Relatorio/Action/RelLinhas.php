<?php
include "lib/Components/ProcessadorDeArquivo.php";
include "lib/Components/PHPExcel/PHPExcel.php";
include "lib/funcoes.php";
include "modulos/Relatorio/DAO/RelLinhasDAO.php";

/**
 * Relatório de linhas cujos contratos estejam em pendência financeira.
 * @author Gabriel Luiz Pereira
 * @since 12/12/2012
 * @package modulos/Relatorio/Action
 */
class RelLinhas {
	
	const TEMP_DIR = '/var/www/docs_temporario/';
	const ARQUIVO_REL = 'rel_linhas.xls';
	
	/**
	 * Acesso a dados do módulo
	 * @var RelLinhasDAO
	 */
	private $DAO;
	
	/**
	 * @var ProcessarArquivo
	 */
	private $processadorArquivo = null;
	
	/**
	 * Arquivo importado para gerar o relatório
	 * @var string
	 */
	private $arquivoImportado = null;

	/**
	 * Construtor
	 */
	public function __construct() {
		
		global $conn;
		$this->DAO = new RelLinhasDAO($conn);
	}
	
	/**
	 * Método index
	 */
	public function index() {
		
		include 'modulos/Relatorio/View/rel_linhas/filtro.php';
	}
	
	/**
	 * Método de importação do arquivo. Efetua a leitura inicial e pergunta se o 
	 * usuário quer confirmar a importação.
	 */
	public function importar() {
		
		$nomeArquivo 			= 'rel_linhas_'.date('YmdHis').'.txt';
		$arquivoTemporario 		= self::TEMP_DIR.$nomeArquivo;
		$mensagemInformativa 	= "";
		$javascript				= "";
		
		try {
			
			if (!isset($_FILES['arquivo']['tmp_name'])) {
				throw new Exception("Arquivo de importação inexistente");
			}
			
			if (!move_uploaded_file($_FILES['arquivo']['tmp_name'], $arquivoTemporario)) {
				throw new Exception("Erro ao mover arquivo temporário");
			}
			
			// Valida arquivo
			list($nome, $ext)		= explode('.', $_FILES['arquivo']['name']);
			
			if ($ext != 'txt' && $ext != 'csv') {
				throw new Exception("Formato inválido. Selecione um arquivo .txt ou .csv");
			} 
			
			$this->processadorArquivo = new ProcessaArquivoRelatorioLinhas($arquivoTemporario);
			$this->processadorArquivo->processar(true);
			
			$_POST['registros_arquivo_importado'] 	= $this->processadorArquivo->linesReaded;
			$_POST['arquivo_importado']				= $nomeArquivo;
			
			/*
			 * Monta a injeção de script
			*/
			ob_start();
			?>
			<script type="text/javascript">
				parent.document.filtro.registros_arquivo_importado.value = <?php echo $_POST['registros_arquivo_importado'] ?>;
				parent.document.filtro.arquivo_importado.value 			 = '<?php echo $_POST['arquivo_importado'] ?>';
				parent.document.filtro.listagem.value					 = '';
			</script>
			<?php 
			$javascript = ob_get_contents();
			ob_end_clean();
			
			echo $javascript;
		}
		catch (Exception $e) {

			unset($_POST['registros_arquivo_importado']);
			$mensagemInformativa = $e->getMessage();
			
			/*
			 * Monta a injeção de script
			*/
			ob_start();
			?>
			<script type="text/javascript">
				
				parent.$('#arquivo').removeAttr('readonly');
				parent.$('#arquivo').removeAttr('disabled');
				
				parent.removeAlerta();
				parent.criaAlerta('<?php echo $mensagemInformativa ?>');
			</script>
			<?php 
			$javascript = ob_get_contents();
			ob_end_clean();
			
			echo $javascript;
		}
	}
	
	/**
	 * Efetua a busca pela tabela temporária e monta a planilha com o relatório
	 */
	public function exportar() {
		
		$arquivoTemporario 		= self::TEMP_DIR.$_POST['arquivo_importado'];
		$PHPExcel				= null;
		$linha					= 4;
		
		try {
			
			header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
			header('Content-Disposition: attachment;filename=rel_linhas.xls');
			header('Cache-Control: max-age=0');
		    header('Content-Length: ' . filesize(self::TEMP_DIR.self::ARQUIVO_REL));
		    ob_clean();
		    flush();
		    readfile(self::TEMP_DIR.self::ARQUIVO_REL);
		    exit;
		}
		catch (Exception $e) {

			unset($_POST['registros_arquivo_importado']);
			$mensagemInformativa = $e->getMessage();
				
			/*
			 * Monta a injeção de script
			*/
			ob_start();
			?>
			<script type="text/javascript">
				
				parent.$('#arquivo').removeAttr('readonly');
				parent.$('#arquivo').removeAttr('disabled');
				
				parent.removeAlerta();
				parent.criaAlerta('<?php echo $mensagemInformativa ?>');
			</script>
			<?php 
			$javascript = ob_get_contents();
			ob_end_clean();
			
			echo $javascript;
		}
	}
	
	/**
	 * 	Imprime o relatório
	 */
	public function imprimirRelatorio() {
		
		$mensagemInformativa = "";
		$arquivoTemporario   = "";
		$linhas				 = array();
		$numLista 			 = 0;
		$filtro 			 = array();
		$lista 				 = array();
		$listaEncontrados	 = array();
		$listaNaoEncontrados = array();
		$relatorio 			 = null;
		$tipoLeitura		 = "";
		$arquivoLog			 = "";
		$retorno			 = array();
		$htmlRetorno		 = null;
		$numRows		     = 0;
		$PHPExcel			 = null;
		$planilhaRelatorio	 = '/var/www/docs_temporario/rel_linhas.csv';
		$arquivoPlanilha	 = 'docs_temporario/rel_linhas.csv';
		
		try {
			
			if (empty($_POST['arquivo_importado'])) {
				throw new Exception("Arquivo não importado.");
			}

			$arquivoTemporario 		= self::TEMP_DIR.$_POST['arquivo_importado'];
			unset($_POST['arquivo_importado']);
			
			if (file_exists($arquivoTemporario)) {
				
				$this->processadorArquivo = new ProcessaArquivoRelatorioLinhas($arquivoTemporario);
				$this->processadorArquivo->processar();
				$lista = $this->processadorArquivo->getRetorno();
				$tipoLeitura = $this->processadorArquivo->getTipoLeitura();
				
				$numLista = count($lista);
				
				if ($numLista > 0) {
					
					$filtro = array(
						'tipo'	=> $tipoLeitura,
						'lista' => $lista		
					);
					
					$relatorio = $this->gerarRelatorio($filtro);
					$resultadoRelatorio = true;
					
					
					if ($relatorio !== false) {
						
						$numRows = pg_num_rows($relatorio);
						// Identifica os números/CIDs não encontrados
						while ($row = pg_fetch_object($relatorio)) {
							
							if ($tipoLeitura == 'linha') {
								$listaEncontrados[] = $row->numero;
							}
							elseif ($tipoLeitura == 'cid') {
								$listaEncontrados[] = $row->cid;
							}
							elseif ($tipoLeitura == 'antena') {
								$listaEncontrados[] = $row->serial_antena;
							}
							else{
								// Nenhum default definido na ES
							}
						}
						
						// Reinicia o cursor do relatorio
						pg_result_seek($relatorio, 0);
						
						if ($numRows > 1000) {
							
							/* $PHPExcel = $this->geraXls($relatorio);
							$writer = PHPExcel_IOFactory::createWriter($PHPExcel, 'Excel2007');
							
							$writer->setPreCalculateFormulas(false);
							$writer->save($planilhaRelatorio); */
							
							$relatorioCSV = $this->geraCsv($relatorio, $planilhaRelatorio);
							
							
						} else {
							
							/**
								Gera o arquivo do relatório e já o deixa disponível para download.
							 */
							$PHPExcel = $this->geraXls($relatorio);
							
							$writer = PHPExcel_IOFactory::createWriter($PHPExcel, 'Excel2007');
							$writer->setPreCalculateFormulas(false);
							$writer->save(self::TEMP_DIR.self::ARQUIVO_REL);
						}
						
						pg_result_seek($relatorio, 0);
					}
					
					// Caso tenha encontrado algum, efetua a diferença para criação do log
					$listaNaoEncontrados = array_diff($lista, $listaEncontrados);
					

					// Caso exista algum não encontrado deverá criar o arquivo de 
					// log e disponibilizar em tela o download do mesmo
					if (count($listaNaoEncontrados) > 0) {
						
						$arquivoLog = $this->criarArquivoLog($listaNaoEncontrados, $tipoLeitura);
					} 
				}
			}
			else {
				throw new Exception("Arquivo $arquivoTemporario importado não encontrado.");
			}
			
			//include 'modulos/Relatorio/View/rel_linhas/filtro.php';
			
			ob_start();
			include "modulos/Relatorio/View/rel_linhas/resultado_relatorio.php";
			$htmlRetorno = ob_get_contents(); 
			ob_end_clean();
			
			$retorno = array(
				'erro'		=> 0,
				'retorno'	=> utf8_encode($htmlRetorno)		
			);
			
			echo json_encode($retorno);
		}
		catch (Exception $e) {

			$mensagemInformativa = $e->getMessage();
			
			//include 'modulos/Relatorio/View/rel_linhas/filtro.php';
			
			$retorno = array(
					'erro'		=> 1,
					'retorno'	=> utf8_encode($mensagemInformativa)
			);
				
			echo json_encode($retorno);
		}
	}
	
	/**
	 * Gera massa do relatório
	 * @param array $filtros
	 * @return Ambigous <boolean, NULL>
	 */
	public function gerarRelatorio($filtros) {
		return $this->DAO->getRelatorioLinhas($filtros);
	}
	
	
	/**
	 * Gera arquivo de log
	 * @param array $listaNaoEncontrados
	 * @param string $tipoLeitura
	 * @throws Exception
	 * @return string
	 */
	public function criarArquivoLog($listaNaoEncontrados, $tipoLeitura) {
		
		/*
		Log do tipo CID
		cid imp concatenado com a data e hora
		Ex.: cid imp 26122012 11h38min.txt

		Log do tipo Linha
		linha imp concatenado com a data e hora
		Ex.: linha imp 26122012 11h38min.txt

		*/
		if (strtolower($tipoLeitura) == 'cid') {
			$nomeArquivoLog = 'cid imp '.date('dmY H\hi').'min.txt';
		}
		elseif (strtolower($tipoLeitura) == 'antena') {
			$nomeArquivoLog = 'antena imp '.date('dmY H\hi').'min.txt';
		}
		else {
			$nomeArquivoLog = 'linha imp '.date('dmY H\hi').'min.txt';
		}
		
		$arquivoLog = self::TEMP_DIR.$nomeArquivoLog;
		$fp = fopen($arquivoLog, "a");
		$usuarioLogado = Sistema::getUsuarioLogado();
		
		if (!$fp) {
			throw new Exception("Erro ao criar arquivo de log.");
		}
		
		fwrite($fp, utf8_encode("Registros de entrada sem correspondente na base de Linhas\r\n\r\n"));
		fwrite($fp, utf8_encode("Usuário: ".$usuarioLogado->nm_usuario."\r\n"));
		fwrite($fp, utf8_encode("Data e Hora: ".date('d/m/Y H:i')."\r\n\r\n"));
		
		if ($tipoLeitura == 'cid'){
			fwrite($fp, strtoupper($tipoLeitura)."\r\n");
		}
		else {
			fwrite($fp, ucfirst($tipoLeitura)."\r\n");
		}
		
		foreach($listaNaoEncontrados as $codigo) {
			fwrite($fp, $codigo."\r\n");
		}	
		
		fclose($fp);

		return $arquivoLog;
	}
	
	/**
	 *
	 * @param resource $relatorio
	 * @return PHPExcel|boolean
	 */
	public function geraXls($relatorio) {
	
		$usuarioLogado 			= Sistema::getUsuarioLogado();
		$PHPExcel				= null;
		$linha					= 4;
	
		try {
				
			$PHPExcel = new PHPExcel();
	
			//$relatorio = $this->DAO->getRelatorioGerado();
	
			// Ajuste automamtico de largura das colunas
			$PHPExcel->getActiveSheet()->getColumnDimension('A')->setAutoSize(true);
			$PHPExcel->getActiveSheet()->getColumnDimension('B')->setAutoSize(true);
			$PHPExcel->getActiveSheet()->getColumnDimension('C')->setAutoSize(true);
			$PHPExcel->getActiveSheet()->getColumnDimension('D')->setAutoSize(true);
			$PHPExcel->getActiveSheet()->getColumnDimension('E')->setAutoSize(true);
			$PHPExcel->getActiveSheet()->getColumnDimension('F')->setAutoSize(true);
			$PHPExcel->getActiveSheet()->getColumnDimension('G')->setAutoSize(true);
			$PHPExcel->getActiveSheet()->getColumnDimension('H')->setAutoSize(true);
			$PHPExcel->getActiveSheet()->getColumnDimension('I')->setAutoSize(true);
			$PHPExcel->getActiveSheet()->getColumnDimension('J')->setAutoSize(true);
			$PHPExcel->getActiveSheet()->getColumnDimension('K')->setAutoSize(true);
			$PHPExcel->getActiveSheet()->getColumnDimension('L')->setAutoSize(true);
			$PHPExcel->getActiveSheet()->getColumnDimension('M')->setAutoSize(true);
			$PHPExcel->getActiveSheet()->getColumnDimension('N')->setAutoSize(true);
			$PHPExcel->getActiveSheet()->getColumnDimension('O')->setAutoSize(true);
			$PHPExcel->getActiveSheet()->getColumnDimension('P')->setAutoSize(true);
			$PHPExcel->getActiveSheet()->getColumnDimension('Q')->setAutoSize(true);
			$PHPExcel->getActiveSheet()->getColumnDimension('R')->setAutoSize(true);
			$PHPExcel->getActiveSheet()->getColumnDimension('S')->setAutoSize(true);
			$PHPExcel->getActiveSheet()->getColumnDimension('T')->setAutoSize(true);
			$PHPExcel->getActiveSheet()->getColumnDimension('U')->setAutoSize(true);
	
			// Montagem do cabeçalho
			$PHPExcel->getActiveSheet()->setCellValue('A1', utf8_encode('Usuário:'));
			$PHPExcel->getActiveSheet()->setCellValue('B1', utf8_encode($usuarioLogado->nm_usuario));
	
			$PHPExcel->getActiveSheet()->setCellValue('A2', utf8_encode('Data e hora:'));
			$PHPExcel->getActiveSheet()->setCellValue('B2', utf8_encode(date('d/m/Y H:i')));
	
			$PHPExcel->getActiveSheet()->setCellValue('A3', utf8_encode('CID/ICCID'));
			$PHPExcel->getActiveSheet()->setCellValue('B3', utf8_encode('Status CID/ICCID'));
			$PHPExcel->getActiveSheet()->setCellValue('C3', utf8_encode('Linha'));
			$PHPExcel->getActiveSheet()->setCellValue('D3', utf8_encode('Operadora'));
			$PHPExcel->getActiveSheet()->setCellValue('E3', utf8_encode('Status Linha'));
			$PHPExcel->getActiveSheet()->setCellValue('F3', utf8_encode('BAN'));
			$PHPExcel->getActiveSheet()->setCellValue('G3', utf8_encode('Data de Habilitação'));
			$PHPExcel->getActiveSheet()->setCellValue('H3', utf8_encode('Nº Série Equipamento'));
			$PHPExcel->getActiveSheet()->setCellValue('I3', utf8_encode('Status Equipamento'));
			$PHPExcel->getActiveSheet()->setCellValue('J3', utf8_encode('Termo'));
			$PHPExcel->getActiveSheet()->setCellValue('K3', utf8_encode('Status Termo'));
			$PHPExcel->getActiveSheet()->setCellValue('L3', utf8_encode('Antena Satelital'));
			$PHPExcel->getActiveSheet()->setCellValue('M3', utf8_encode('Plano Satelital'));
			$PHPExcel->getActiveSheet()->setCellValue('N3', utf8_encode('Status Fornecedor Antena Satelital'));
			$PHPExcel->getActiveSheet()->setCellValue('O3', utf8_encode('Data de Cancelamento'));
			$PHPExcel->getActiveSheet()->setCellValue('P3', utf8_encode('DT de Alteração Status NTC'));
			$PHPExcel->getActiveSheet()->setCellValue('Q3', utf8_encode('Classe do Contrato'));
			$PHPExcel->getActiveSheet()->setCellValue('R3', utf8_encode('Tipo do Contrato'));
			$PHPExcel->getActiveSheet()->setCellValue('S3', utf8_encode('Nome do Cliente'));
			$PHPExcel->getActiveSheet()->setCellValue('T3', utf8_encode('Placa'));
			$PHPExcel->getActiveSheet()->setCellValue('U3', utf8_encode('Versão Equipamento'));
	
			if ($relatorio != false && pg_num_rows($relatorio) > 0) {
					
				// Montagem do relatório
				while ($row = pg_fetch_object($relatorio)) {
	
					$ban  				= empty($row->ban) 					? '-' : $row->ban;
					$dataHabilitacao 	= empty($row->data_habilitacao) 	? '-' : $row->data_habilitacao;
					$statusLinha		= empty($row->status)				? '-' : $row->status;
					$serieEquipamento	= empty($row->serie_equipamento) 	? '-' : $row->serie_equipamento;
					$statusEquipamento	= empty($row->status_equipamento)	? '-' : $row->status_equipamento;
					$contrato			= empty($row->contrato)				? '-' : $row->contrato;
					$statusContrato		= empty($row->status_contrato)		? '-' : $row->status_contrato;
					$serialAntena		= empty($row->serial_antena)		? '-' : $row->serial_antena;
					$fornecedorAntena	= empty($row->fornecedor_antena)	? '-' : $row->fornecedor_antena;
					$planoAntena		= empty($row->plano)				? '-' : $row->plano;
					$operadora 			= empty($row->oploperadora)			? '-' : $row->oploperadora;
					$dataCancelamento 	= empty($row->linbloqueado)			? '-' : $row->linbloqueado;
					$dataAlteracao 		= empty($row->lindt_alteracaontc)	? '-' : $row->lindt_alteracaontc;
					$classe_contrato 	= empty($row->classe_contrato)		? '-' : $row->classe_contrato;
					$conno_tipo 		= empty($row->conno_tipo)			? '-' : $row->conno_tipo;
					$clinome 			= empty($row->clinome)				? '-' : $row->clinome;
					$veiplaca 			= empty($row->veiplaca)				? '-' : $row->veiplaca;
					$versao_eqpto 		= empty($row->versao_eqpto)			? '-' : $row->versao_eqpto;
						
					$PHPExcel->getActiveSheet()->getStyle('A'.$linha)->getNumberFormat()->setFormatCode( PHPExcel_Style_NumberFormat::FORMAT_TEXT );
	
					$PHPExcel->getActiveSheet()->setCellValue('A'.$linha, utf8_encode('CID'.$row->cid));
					$PHPExcel->getActiveSheet()->setCellValue('B'.$linha, utf8_encode($row->status_cid));
					$PHPExcel->getActiveSheet()->setCellValue('C'.$linha, utf8_encode($row->numero));
					$PHPExcel->getActiveSheet()->setCellValue('D'.$linha, utf8_encode($operadora));
					$PHPExcel->getActiveSheet()->setCellValue('E'.$linha, utf8_encode($statusLinha));
					$PHPExcel->getActiveSheet()->setCellValue('F'.$linha, utf8_encode($ban));
					$PHPExcel->getActiveSheet()->setCellValue('G'.$linha, utf8_encode($dataHabilitacao));
					$PHPExcel->getActiveSheet()->setCellValue('H'.$linha, utf8_encode($serieEquipamento));
					$PHPExcel->getActiveSheet()->setCellValue('I'.$linha, utf8_encode($statusEquipamento));
					$PHPExcel->getActiveSheet()->setCellValue('J'.$linha, utf8_encode($contrato));
					$PHPExcel->getActiveSheet()->setCellValue('K'.$linha, utf8_encode($statusContrato));
					$PHPExcel->getActiveSheet()->setCellValue('L'.$linha, utf8_encode($serialAntena));
					$PHPExcel->getActiveSheet()->setCellValue('M'.$linha, utf8_encode($planoAntena));
					$PHPExcel->getActiveSheet()->setCellValue('N'.$linha, utf8_encode($fornecedorAntena));
					$PHPExcel->getActiveSheet()->setCellValue('O'.$linha, utf8_encode($dataCancelamento));
					$PHPExcel->getActiveSheet()->setCellValue('P'.$linha, utf8_encode($dataAlteracao));
					$PHPExcel->getActiveSheet()->setCellValue('Q'.$linha, utf8_encode($classe_contrato));
					$PHPExcel->getActiveSheet()->setCellValue('R'.$linha, utf8_encode($conno_tipo));
					$PHPExcel->getActiveSheet()->setCellValue('S'.$linha, utf8_encode($clinome));
					$PHPExcel->getActiveSheet()->setCellValue('T'.$linha, utf8_encode($veiplaca));
					$PHPExcel->getActiveSheet()->setCellValue('U'.$linha, utf8_encode($versao_eqpto));
					
					$linha++;
				}
			}
				
			return $PHPExcel;
				
		} catch (Exception $e) {
			return false;
		}
	}
	
	/**
	 * 
	 * @param resource $relatorio
	 * @return PHPExcel|boolean
	 */
	public function geraCsv($relatorio, $planilhaRelatorio) {

		$usuarioLogado 			= Sistema::getUsuarioLogado();
		$handle 				= fopen($planilhaRelatorio, "w");
		$linha					= "";
		
		try {
			
			$linha .= '"Usuário:";';
			$linha .= '"'.$usuarioLogado->nm_usuario.'"';
			$linha .= "\r\n";
			
			fwrite($handle, $linha);
			$linha = "";
			
			$linha .= '"Data e hora:";';
			$linha .= date('d/m/Y H:i') ."\r\n";
			
			fwrite($handle, $linha);
			$linha = "";
			
			$linha .= '"CID/ICCID";';
			$linha .= '"Status CID/ICCID";';
			$linha .= '"Linha";';
			$linha .= '"Operadora";';
			$linha .= '"Status Linha";';
			$linha .= '"BAN";';
			$linha .= '"Data de Habilitação";';
			$linha .= '"Nº Série Equipamento";';
			$linha .= '"Status Equipamento";';
			$linha .= '"Termo";';
			$linha .= '"Status Termo";';
			$linha .= '"Antena Satelital";';
			$linha .= '"Plano Satelital";';
			$linha .= '"Status Fornecedor Antena Satelital";';
			$linha .= '"Data de Cancelamento";';
			$linha .= '"DT de Alteração Status NTC";';
			$linha .= '"Classe do Contrato";';
			$linha .= '"Tipo do Contrato";';
			$linha .= '"Nome do Cliente";';
			$linha .= '"Placa";';
			$linha .= '"Versão Equipamento";';
			$linha .= "\r\n";
			
			fwrite($handle, $linha);
			$linha = "";
			
			if ($relatorio != false && pg_num_rows($relatorio) > 0) {
			
				// Montagem do relatório
				while ($row = pg_fetch_object($relatorio)) {
					
					$linha = "";
					
					$ban  				= empty($row->ban) 					? '-' : $row->ban;
					$dataHabilitacao 	= empty($row->data_habilitacao) 	? '-' : $row->data_habilitacao;
					$statusLinha		= empty($row->status)				? '-' : $row->status;
					$serieEquipamento	= empty($row->serie_equipamento) 	? '-' : $row->serie_equipamento;
					$statusEquipamento	= empty($row->status_equipamento)	? '-' : $row->status_equipamento;
					$contrato			= empty($row->contrato)				? '-' : $row->contrato;
					$statusContrato		= empty($row->status_contrato)		? '-' : $row->status_contrato;
					$serialAntena		= empty($row->serial_antena)		? '-' : $row->serial_antena;
					$fornecedorAntena	= empty($row->fornecedor_antena)	? '-' : $row->fornecedor_antena;
					$planoAntena		= empty($row->plano)				? '-' : $row->plano;
					$operadora	        = empty($row->oploperadora)	        ? '-' : $row->oploperadora;
			        $dataCancelamento   = empty($row->linbloqueado)	        ? '-' : $row->linbloqueado;
			        $dataAlteracao      = empty($row->lindt_alteracaontc)	? '-' : $row->lindt_alteracaontc;
					$classe_contrato 	= empty($row->classe_contrato)		? '-' : $row->classe_contrato;
					$conno_tipo 		= empty($row->conno_tipo)			? '-' : $row->conno_tipo;
					$clinome 			= empty($row->clinome)				? '-' : $row->clinome;
					$veiplaca 			= empty($row->veiplaca)				? '-' : $row->veiplaca;
					$versao_eqpto 		= empty($row->versao_eqpto)			? '-' : $row->versao_eqpto;
			
					$linha .= '"CID'.$row->cid.'";';
					$linha .= '"'.$row->status_cid.'";';
					$linha .= $row->numero.';';
					$linha .= '"'.$operadora.'";';
					$linha .= '"'.$statusLinha.'";';
					$linha .= '"'.$ban.'";';
					$linha .= '"'.$dataHabilitacao.'";';
					$linha .= '"'.$serieEquipamento.'";';
					$linha .= '"'.$statusEquipamento.'";';
					$linha .= $contrato.';';
					$linha .= '"'.$statusContrato.'";';
					$linha .= '"'.$serialAntena.'";';
					$linha .= '"'.$planoAntena.'";';
					$linha .= '"'.$fornecedorAntena.'";';
					$linha .= '"'.$dataCancelamento.'";';
					$linha .= '"'.$dataAlteracao.'";';
					$linha .= '"'.$classe_contrato.'";';
					$linha .= '"'.$conno_tipo.'";';
					$linha .= '"'.$clinome.'";';
					$linha .= '"'.$veiplaca.'";';
					$linha .= '"'.$versao_eqpto.'";';
					$linha .= "\r\n";
					
					fwrite($handle, $linha);
				}
			}
			
			fclose($handle);
			return true;
			
		} catch (Exception $e) {
			return false;
		}
	}
}

/**
 * Processa o arquivo de entrada do relatório
 * @author Gabriel Luiz Pereira
 * @since 12/12/2012
 * @package modulos/Relatorio/Action
 */
class ProcessaArquivoRelatorioLinhas extends ProcessadorDeArquivo {
	
	/**
	 * Identifica se buscará as linhas pelo número da linha ou pelo CID
	 * @var string
	 */
	private $tipoLeitura;
	
	/**
	 * Não efetua o processamento do arquivo
	 * @var boolean
	 */
	private $somenteContagem;
	
	/**
	 * Retorno com a lista de CIDs ou Números das linhas
	 * @var array
	 */
	private $retorno;
	
	/**
	 * (non-PHPdoc)
	 * @see ProcessadorDeArquivo::processar()
	 * @param boolean $somenteContagem   Não efetua o processamento do arquivo
	 */
	public function processar($somenteContagem = false) {
		$this->somenteContagem = $somenteContagem;
		parent::processar();
		$this->linesReaded--; // Desconsidera o cabeçalho
		
		if ($this->linesReaded <= 0) {
			throw new Exception("Formato inválido. Selecione um arquivo .txt ou .csv");
		}
	}
	
	/**
	 * (non-PHPdoc)
	 * @see ProcessadorDeArquivo::processaLinha()
	 */
	public function processaLinha() {
		
		$conteudo = '';
		
		if (!$this->somenteContagem) {
			
			$conteudo = $this->buffer;
			
			if ($this->linesReaded == 0) {
				
				if (preg_match('/linha/', strtolower($conteudo))) {
					$this->tipoLeitura = 'linha';
				}
				else if (preg_match('/cid/', strtolower($conteudo))) {
					$this->tipoLeitura = 'cid';
				}
				else if (preg_match('/antena/', strtolower($conteudo))) {
					$this->tipoLeitura = 'antena';
				}
				else {
					throw new Exception("Formato inválido. Selecione um arquivo .txt ou .csv");
				}
			}
			else {
				if ($this->tipoLeitura == 'antena') {
					$preBuff  = preg_replace('/(\r\n|\r|\n|[0-9a-zA-Z])/', '', $conteudo);
					$this->retorno[]  = preg_replace('/[^0-9a-zA-Z]/', '', preg_replace('/(\r\n|\r|\n)/', '', $conteudo));
				}else{
					$preBuff  = preg_replace('/(\r\n|\r|\n|[0-9])/', '', $conteudo);
					$this->retorno[]  = preg_replace('/[^0-9]/', '', preg_replace('/(\r\n|\r|\n)/', '', $conteudo));
				}
				if (!empty($preBuff)) {
					$this->retorno[]  = null;
					throw new Exception("Conte?do inv?lido para a importa??o.");
				}
			}
		}
	}
	
	/**
	 * Retorna a lista de retorno do processamento
	 * @return multitype:array|boolean
	 */
	public function getRetorno() {

		$numRetorno = count($this->retorno);
		
		if ($numRetorno > 0) {
			return $this->retorno;
		}
		else {

			return false;
		}
	}
	
	/**
	 * Retorna o tipo da leitura, se é por número de linha ou por CID
	 * @return string
	 */
	public function getTipoLeitura() {
		return $this->tipoLeitura;
	}
}