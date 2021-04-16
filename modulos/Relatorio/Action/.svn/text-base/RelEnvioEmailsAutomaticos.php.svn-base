<?php

include _MODULEDIR_."/Relatorio/DAO/RelEnvioEmailsAutomaticosDAO.php";
include "lib/Components/PHPExcel/PHPExcel.php";

/**
 * Action do relatório de envio de e-mails automaticos 
 */
class RelEnvioEmailsAutomaticos {
	
	/**
	 * Acesso a dados do módulo
	 * @var RelEnvioEmailsAutomaticosDAO
	 */
	private $DAO;
	
	/**
	 * Construtor
	 */
	public function __construct() {
		
		global $conn;
		$this->DAO = new RelEnvioEmailsAutomaticosDAO($conn);
	}
	
    public function __set($var, $value) {
        $this->$var = $value;
    }
    
    public function __get($var) {
        return $this->$var;
    }	
	/**
	 * Acesso inicial do módulo
	 */
	public function index() {
		
		$mensagemInformativa	= "";
		$comboTipoOS 			= array();
		$htmlFiltro 			= null;
		$comboQuantidadeEmails 	= array(
				'0'=>'Todos',
				'1'=>'1',
				'2'=>'2',
				'3'=>'3'
		);
		
		try {
			$comboTipoOS 				= $this->DAO->getTipoOSList();
			$this->comboPropostas 		= $this->DAO->getPropostas();
        	$this->comboContratos 		= $this->DAO->getContratos();
		}
		catch (Exception $e) {
			$mensagemInformativa = $e->getMessage();
		}
		
		ob_start();
		include _MODULEDIR_.'Relatorio/View/rel_envio_emails_automaticos/filtro.php';
		$htmlFiltro = ob_get_contents();
		ob_end_clean();
		echo $htmlFiltro;
	}
	
	/**
	 * Gera o arquivo XLS
	 */
	public function pesquisar() {
		
		$filtros		= array();
		$retorno		= array();
		$geraArquivo	= false;
		$file = "rel_envio_emails_automaticos.xlsx";
		$dir = '/var/www/docs_temporario/'.$file;
		
		try {
			
			$filtros['data_inicial']		= pg_escape_string(urldecode($_POST['dt_ini']));
			$filtros['data_final']			= pg_escape_string(urldecode($_POST['dt_fim']));
			$filtros['tipo_os']				= !empty($_POST['tipo_os']) ? $_POST['tipo_os'] : '';
			$filtros['ver_insucessos']		= $_POST['ver_insucessos'];
			$filtros['comboContratos']		= $_POST['comboContratos'];
			$filtros['comboPropostas']		= $_POST['comboPropostas'];
			$filtros['comboSubpropostas']	= $_POST['comboSubpropostas'];
			$filtros['quantidade_emails']	= !empty($_POST['quantidade_emails']) ? $_POST['quantidade_emails'] : '';
			$filtros['placa']				= pg_escape_string(urldecode($_POST['placa']));
			$filtros['numero_os']			= pg_escape_string(urldecode($_POST['numero_os']));
			$filtros['nome_cliente']		= pg_escape_string(urldecode($_POST['nome_cliente']));

			$geraArquivo 					= $this->geraXLS($filtros, $dir);
			
			if ($geraArquivo !== false) {
			
				if ($geraArquivo !== null) {
					$arquivo = "downloads.php?arquivo=docs_temporario/$file";
					$retorno		= array(
						'erro'		=> false,
						'codigo'	=> 0,
						'retorno'	=> 	$arquivo
					);
				}
				else {
					$arquivo = "downloads.php?arquivo=docs_temporario/$file";
					$retorno		= array(
							'erro'		=> false,
							'codigo'	=> 1,
							'retorno'	=> 	$arquivo
					);
				}
			}
			else {
				
				$retorno		= array(
					'erro'		=> true,
					'codigo'	=> 2,
					'retorno'	=> 	"Erro ao gerar arquivo"
				);
			}
			
			echo json_encode($retorno);
		}
		catch (Exception $e) {
			
			$retorno		= array(
					'erro'		=> true,
					'codigo'	=> 1,
					'retorno'	=> $e->getMessage()
			);
			echo json_encode($retorno);
		}
		exit;
	}
	
	/* retorna as subpropostas de uma proposta */
    public function buscarSubProposta() {
        $subPropostas = $this->DAO->getSubPropostas();
        echo json_encode($subPropostas);
        exit();
    }
    

	/**
	 * Retorna a massa gerada para o relatório
	 * @param array $filtros
	 * @return Ambigous <resource, NULL>
	 */
	public function geraRelatorio($filtros) {
		return $this->DAO->getRelatorio($filtros);
	}
	
	/**
	 * Gera o arquivo xls em disco
	 * @param array $filtros
	 */
	public function geraXLS($filtros, $filepath) {
		
		// Arquivo modelo para gerar o XLS
		$arquivoModelo = _MODULEDIR_.'Relatorio/View/rel_envio_emails_automaticos/modelo_relatorio_001.xlsx';
	
		
		try {
			
			// Instância PHPExcel
			$reader = PHPExcel_IOFactory::createReader("Excel2007");
				
			// Carrega o modelo
			$PHPExcel = $reader->load($arquivoModelo);
				
			// Processa o relatório
			$relatorio = $this->geraRelatorio($filtros);
				
			if ($relatorio !== null) {
			
				$linha = 8;
				while ($row = pg_fetch_object($relatorio)) {

					$PHPExcel->getActiveSheet()->getStyle('A'.$linha)->getNumberFormat()->setFormatCode('0');
					$PHPExcel->getActiveSheet()->setCellValue('A'.$linha, utf8_encode($row->numero_os));
					$PHPExcel->getActiveSheet()->setCellValue('B'.$linha, utf8_encode($row->tipo_os));
					$PHPExcel->getActiveSheet()->getStyle('C'.$linha)->getNumberFormat()->setFormatCode('0');
					$PHPExcel->getActiveSheet()->setCellValue('C'.$linha, utf8_encode($row->contrato));
					$PHPExcel->getActiveSheet()->setCellValue('D'.$linha, utf8_encode($row->data_notificacao));
					$PHPExcel->getActiveSheet()->getStyle('E'.$linha)->getNumberFormat()->setFormatCode('0');
					$PHPExcel->getActiveSheet()->setCellValue('E'.$linha, utf8_encode(($row->numero_notificacao)));
					$PHPExcel->getActiveSheet()->setCellValue('F'.$linha, utf8_encode($row->nome_cliente));
					$PHPExcel->getActiveSheet()->getStyle('G'.$linha)->getNumberFormat()->setFormatCode('0');
					$PHPExcel->getActiveSheet()->setCellValue('G'.$linha, utf8_encode($row->cnpj_cpf));
					$PHPExcel->getActiveSheet()->setCellValue('H'.$linha, utf8_encode($row->uf));
					$PHPExcel->getActiveSheet()->setCellValue('I'.$linha, utf8_encode($row->cidade));
					$PHPExcel->getActiveSheet()->setCellValue('J'.$linha, utf8_encode($row->placa));
					$PHPExcel->getActiveSheet()->getStyle('K'.$linha)->getNumberFormat()->setFormatCode('#');
					$PHPExcel->getActiveSheet()->setCellValue('K'.$linha, utf8_encode($row->chassi));
					$PHPExcel->getActiveSheet()->setCellValue('L'.$linha, utf8_encode($row->modelo));
					$PHPExcel->getActiveSheet()->setCellValue('M'.$linha, utf8_encode($row->data));
					$PHPExcel->getActiveSheet()->setCellValue('N'.$linha, utf8_encode($row->insucesso_envio));
						$PHPExcel->getActiveSheet()->setCellValue('O'.$linha, utf8_encode($row->proposta));
					} else {
			
						$PHPExcel->getActiveSheet()->getStyle('A'.$linha)->getNumberFormat()->setFormatCode('0');
						$PHPExcel->getActiveSheet()->setCellValue('A'.$linha, utf8_encode($row->contrato));
						$PHPExcel->getActiveSheet()->setCellValue('B'.$linha, utf8_encode($row->data_notificacao));
						$PHPExcel->getActiveSheet()->getStyle('C'.$linha)->getNumberFormat()->setFormatCode('0');
						$PHPExcel->getActiveSheet()->setCellValue('C'.$linha, utf8_encode(($row->numero_notificacao)));
						$PHPExcel->getActiveSheet()->setCellValue('D'.$linha, utf8_encode($row->nome_cliente));
						$PHPExcel->getActiveSheet()->getStyle('E'.$linha)->getNumberFormat()->setFormatCode('0');
						$PHPExcel->getActiveSheet()->setCellValue('E'.$linha, utf8_encode($row->cnpj_cpf));
						$PHPExcel->getActiveSheet()->setCellValue('F'.$linha, utf8_encode($row->uf));
						$PHPExcel->getActiveSheet()->setCellValue('G'.$linha, utf8_encode($row->cidade));
						$PHPExcel->getActiveSheet()->setCellValue('H'.$linha, utf8_encode($row->placa));
						$PHPExcel->getActiveSheet()->getStyle('I'.$linha)->getNumberFormat()->setFormatCode('#');
						$PHPExcel->getActiveSheet()->setCellValue('I'.$linha, utf8_encode($row->chassi));
						$PHPExcel->getActiveSheet()->setCellValue('J'.$linha, utf8_encode($row->modelo));
						$PHPExcel->getActiveSheet()->setCellValue('K'.$linha, utf8_encode($row->data));
						$PHPExcel->getActiveSheet()->setCellValue('L'.$linha, utf8_encode($row->insucesso_envio));
						$PHPExcel->getActiveSheet()->setCellValue('M'.$linha, utf8_encode($row->proposta));
						
					}

					$linha++;
				}
			
				$PHPExcel->getActiveSheet()->getColumnDimension('A')->setAutoSize(true);
				$PHPExcel->getActiveSheet()->getColumnDimension('B')->setAutoSize(true);
				$PHPExcel->getActiveSheet()->getColumnDimension('C')->setAutoSize(true);
				$PHPExcel->getActiveSheet()->getColumnDimension('D')->setAutoSize(true);
				$PHPExcel->getActiveSheet()->getColumnDimension('E')->setAutoSize(true);
				$PHPExcel->getActiveSheet()->getColumnDimension('H')->setAutoSize(true);
				$PHPExcel->getActiveSheet()->getColumnDimension('I')->setAutoSize(true);
				$PHPExcel->getActiveSheet()->getColumnDimension('J')->setAutoSize(true);
				$PHPExcel->getActiveSheet()->getColumnDimension('K')->setAutoSize(true);
				$PHPExcel->getActiveSheet()->getColumnDimension('L')->setAutoSize(true);
				$PHPExcel->getActiveSheet()->getColumnDimension('M')->setAutoSize(true);
				
				/*
				 * Formatar a coluna apenas se ela for preenchido
				 * No caso só preenchemos as colunas M e N do arquivo Excel se o filtro URA Ativo estiver vazio
				 */
				if(empty($filtros['ura_ativa'])){
					
				$PHPExcel->getActiveSheet()->getColumnDimension('N')->setAutoSize(true);
					$PHPExcel->getActiveSheet()->getColumnDimension('O')->setAutoSize(true);
			}
			else {
				$PHPExcel->getActiveSheet()->setCellValue('A8', utf8_encode("Nenhum resultado encontrado"));
			}
	
			$writer = PHPExcel_IOFactory::createWriter($PHPExcel, 'Excel2007');
			$writer->setPreCalculateFormulas(false);
			$writer->save($filepath);
			
			return $relatorio;
		}
		catch (Exception $e) {
			
			// Grava log no arquivo
			$fpLog = fopen($dir.$file, "w");
			fwrite($fpLog, $e->getMessage());
			fclose($fpLog);
			
			return false;
		}
	}
}