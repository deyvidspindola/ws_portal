<?php

include _MODULEDIR_."/Relatorio/DAO/RelEnvioEmailsEmLoteDAO.php";
include "lib/Components/PHPExcel/PHPExcel.php";

/**
 * Action do relatório de envio de e-mails em lote 
 */
class RelEnvioEmailsEmLote {
    
    /**
	 * Acesso a dados do módulo
	 * @var RelEnvioEmailsAutomaticosDAO
	 */
	private $dao;
	
	/**
	 * Construtor
	 */
	public function __construct() {
		
		global $conn;
		$this->dao = new RelEnvioEmailsEmLoteDAO($conn);
	}
	
	/**
	 * Acesso inicial do módulo
	 */
	public function index() {
        
        include _MODULEDIR_ . 'Relatorio/View/rel_envio_emails_em_lote/filtros.php';
        
    }
    
    /**
	 * Gera o arquivo XLS
	 */
	public function pesquisar() {
		
		$filtros		= array();
		$retorno		= array();		
		$file = "log_envio_email_ocorrencia_".date('Y_m_d').".xlsx";
		$dir = '/var/www/docs_temporario/';
		
		try {
			
			$filtros['data_inicial']		= pg_escape_string(urldecode($_POST['dt_ini']));
			$filtros['data_final']			= pg_escape_string(urldecode($_POST['dt_fim']));			
			$filtros['placa']				= pg_escape_string(urldecode($_POST['placa']));
            $filtros['chassi']				= pg_escape_string(urldecode($_POST['chassi']));
            $filtros['sucesso_envio']		= pg_escape_string(urldecode($_POST['sucesso_envio']));            
			$filtros['nome_cliente']		= pg_escape_string(urldecode($_POST['nome_cliente']));

			$geraArquivo = $this->geraXLS($filtros, $dir, $file);
			
			if ($geraArquivo['erro'] === false) {
			
				if ($geraArquivo['resposta'] !== null) {
					$arquivo = "downloads.php?arquivo=docs_temporario/$file";
					$retorno		= array(
						'erro'		=> false,
						'codigo'	=> 0,// Código de sucesso com resultados
						'retorno'	=> 	$arquivo
					);
				}
				else {					
					$retorno		= array(
							'erro'		=> false,
							'codigo'	=> 1// Código de sucesso sem resultados
					);
				}
			}
			else {
				
				$retorno		= array(
					'erro'		=> true,					
					'retorno'	=> $geraArquivo['resposta']
				);
			}
			
			echo json_encode($retorno);
		}
		catch (Exception $e) {
			
			$retorno		= array(
					'erro'		=> true,					
					'retorno'	=> $e->getMessage()
			);
            
			echo json_encode($retorno);
		}
		exit;
	}
	
	/**
	 * Retorna a massa gerada para o relatório
	 * @param array $filtros
	 * @return Ambigous <resource, NULL>
	 */
	public function geraRelatorio($filtros) {
		return $this->dao->getRelatorio($filtros);
	}
	
	/**
	 * Gera o arquivo xls em disco
	 * @param array $filtros
	 */
	public function geraXLS($filtros, $path, $file) {
		
		// Arquivo modelo para gerar o XLS
		$arquivoModelo = _MODULEDIR_.'Relatorio/View/rel_envio_emails_em_lote/modelo_relatorio_001.xlsx';
	
		
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
					$PHPExcel->getActiveSheet()->setCellValue('A'.$linha, utf8_encode($row->contrato));
					$PHPExcel->getActiveSheet()->setCellValue('B'.$linha, utf8_encode($row->data_notificacao));
					$PHPExcel->getActiveSheet()->getStyle('C'.$linha)->getNumberFormat()->setFormatCode('0');
					$PHPExcel->getActiveSheet()->setCellValue('C'.$linha, utf8_encode($row->cliente));
					$PHPExcel->getActiveSheet()->setCellValue('D'.$linha, utf8_encode($row->cpf_cnpj));
					$PHPExcel->getActiveSheet()->getStyle('E'.$linha)->getNumberFormat()->setFormatCode('0');
					$PHPExcel->getActiveSheet()->setCellValue('E'.$linha, utf8_encode(($row->uf)));
					$PHPExcel->getActiveSheet()->setCellValue('F'.$linha, utf8_encode($row->cidade));
					$PHPExcel->getActiveSheet()->getStyle('G'.$linha)->getNumberFormat()->setFormatCode('0');
					$PHPExcel->getActiveSheet()->setCellValue('G'.$linha, utf8_encode($row->placa));
					$PHPExcel->getActiveSheet()->setCellValue('H'.$linha, utf8_encode($row->chassi));
					$PHPExcel->getActiveSheet()->setCellValue('I'.$linha, utf8_encode($row->modelo));
					$PHPExcel->getActiveSheet()->setCellValue('J'.$linha, utf8_encode($row->sucesso_envio));					
                    
                    $PHPExcel->getActiveSheet()->getStyle('B'.$linha)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                    $PHPExcel->getActiveSheet()->getStyle('F'.$linha)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                    $PHPExcel->getActiveSheet()->getStyle('G'.$linha)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                    $PHPExcel->getActiveSheet()->getStyle('I'.$linha)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                    $PHPExcel->getActiveSheet()->getStyle('J'.$linha)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			
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
			}
			else {
				$PHPExcel->getActiveSheet()->setCellValue('A8', utf8_encode("Nenhum resultado encontrado."));
			}
	
			$writer = PHPExcel_IOFactory::createWriter($PHPExcel, 'Excel2007');
			$writer->setPreCalculateFormulas(false);
            
            if(!file_exists($path) || !is_writable($path)) {
                throw new Exception('Houve um erro ao gerar o arquivo.');
            }
            
			$writer->save($path.$file);
			
			return array('erro' => false, 'resposta' => $relatorio);
		}
		catch (Exception $e) {               
			return array('erro' => true, 'resposta' => utf8_encode($e->getMessage()));						
		}
	}
    
}