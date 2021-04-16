<?php
require _MODULEDIR_."/Relatorio/DAO/RelPosicaoEstoqueDAO.php";
require _SITEDIR_ . "lib/excelwriter.inc.php";
require _SITEDIR_ . "lib/Components/PHPExcel/PHPExcel.php";

/**
 * Relatório de posição de estoque
 * 
 * @author Bruno Luiz Kumagai Aldana
 * @since 18/05/2015
 */
class RelPosicaoEstoque {
	
	/**
	 * Acesso a dados do módulo
	 * @var RelPosicaoEstoque
	 */
	private $DAO;
	
	/**
	 * Construtor
	 */
	public function __construct() {
		
		global $conn;
		$this->DAO = new RelPosicaoEstoqueDAO($conn);
	}
	
	/**
	 * Página index
	 */
	public function index() {
	  
		// Popula listas
		$statusRepresentanteList = array(
				'A'	=> 'Ativo',
				'D' => 'Ativo - Aguardando Distrato',
				'I' => 'Inativo'
		);
 
		$ufList  = $this->DAO->getUfList(); 
		$representanteList = $this->DAO->getRepresentanteList();
		$dataPosicaoList   = $this->DAO->getDataPosicaoEstoque();
		  
		$tipoItemList	         = array(
			'I' => 'Imobilizado',
			'M'	=> 'Material Instalação'		
		); 
		// Renderiza a tela
		ob_start();
		include _MODULEDIR_."/Relatorio/View/rel_posicao_estoque/filtro.php";
		$html = ob_get_contents();
		ob_end_clean();
		echo $html;
	}
	
	/**
	 * Retorna lista de todos os representantes ou por status 
	 */
	public function buscarRepresentante() {
	
		ob_start();
		try {
	
			$repstatus = $_POST['repstatus'];
			
			if(!empty($repstatus)){
				$repstatus = $_POST['repstatus'];
			}else{
				$repstatus = '';
			}
			
			$representanteList	= $this->DAO->getRepresentanteList($repstatus);
	 
			$retorno		= array(
					'erro'		=> false,
					'codigo'	=> 0,
					'retorno'	=> 	$representanteList
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
	 * Retorna as cidades de acordo com UF
	 */
	public function buscarCidades() {
	
		ob_start();
		try {
	
			$uf 		    = $_POST['uf'];
			$cidadesList	= $this->DAO->getCidadesList($uf);
		  
			$retorno		= array(
					'erro'		=> false,
					'codigo'	=> 0,
					'retorno'	=> 	$cidadesList
			);
	 
			echo  json_encode($retorno) ;
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
		$relatorio		= null;
		$relatorioHtml	= "";

		ob_start();
		try {
			
			if (isset($_POST['data_posicao'])) {
			    $filtros['data_posicao_estoque']= $_POST['data_posicao'];  
			}
			if (isset($_POST['tipo_item'])) {
					
				$filtros['tipo_item'] 		    = $_POST['tipo_item'];
			}
			if (isset($_POST['representante'])) {
					
				$filtros['repoid'] 		        = $_POST['representante'];
			}
			if (isset($_POST['status_representante'])) {
					
				$filtros['repstatus']           = $_POST['status_representante'];
			}
			if (isset($_POST['uf'])) {
					
				$filtros['uf']                  = $_POST['uf'];
			}
			if (isset($_POST['cidade'])) {
					
				$filtros['cidade']              = $_POST['cidade'];
			}
		
			$relatorio = $this->DAO->getRelatorioPosicaoEstoque($filtros);
	 	  
	 	    if($relatorio != null){
	 	    	$erro = false;
	 	    }else{
	 	    	$erro = true;
	 	    }
			include _MODULEDIR_."/Relatorio/View/rel_posicao_estoque/resultado.php";
				
		    $relatorioHtml = ob_get_contents();
		    ob_end_clean();
			$retorno		= array(
					'erro'		=> $erro,
					'codigo'	=> 0,
					'retorno'	=> utf8_encode($relatorioHtml)
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
					'retorno'	=> $e->getMessage()
			);
			echo json_encode($retorno);
			exit;
		}
	}
  
	/**
	 * Gera o relatório em csv
	 */
	public function gerarCsv() {
		$filtros 		= array();
		$relatorio		= null;
		
		if (isset($_POST['data_posicao'])) {
			$filtros['data_posicao_estoque']= utf8_encode($_POST['data_posicao']);
		}
		if (isset($_POST['tipo_item'])) {
				
			$filtros['tipo_item'] 		    = $_POST['tipo_item'];
		}
		if (isset($_POST['representante'])) {
				
			$filtros['repoid'] 		        = $_POST['representante'];
		}
		if (isset($_POST['status_representante'])) {
				
			$filtros['repstatus']           = $_POST['status_representante'];
		}
		if (isset($_POST['uf'])) {
				
			$filtros['uf']                  = $_POST['uf'];
		}
		if (isset($_POST['cidade'])) {
				
			$filtros['cidade']              = $_POST['cidade'];
		}
            $conteudo  = "";

			$relatorio = $this->DAO->getRelatorioPosicaoEstoque($filtros);
 	      if($relatorio == null){
				$retorno		= array(
					'erro'		=> true,
					'codigo'	=> $e->getCode(),
					'retorno'	=> $e->getMessage()
			);
			echo json_encode($retorno);exit;
			
			}

			$data = explode('/',utf8_encode($_POST['data_posicao']));
			unset($filtros);
			 
			header('Content-Type: text/csv');
			header('Content-Disposition: attachment;filename=RelatorioPosicaoEstoque_'.$data[2].$data[1].$data[0].'.csv');
			header('Cache-Control: max-age=0');
			
			$conteudo = "Dt Posição; ";
			$conteudo.= "Tipo Estoque; ";
			$conteudo.= "Cód Repr; "; 
			$conteudo.= "Repr; ";
			$conteudo.= "Cidade; ";
			$conteudo.= "UF; ";
			$conteudo.= "Cód Prod; "; 
			$conteudo.= "Prod; ";
			$conteudo.= "Qtd Dispon; ";
			$conteudo.= "Qtd Reserv; ";
			$conteudo.= "Qtd Transi; ";
			$conteudo.= "Qtd Reserv Transi; ";
			$conteudo.= "Qtd Confer; ";
			$conteudo.= "Qtd Retira; ";
			$conteudo.= "Qtd Instal; ";
			$conteudo.= "Qtd Retorn; ";
			$conteudo.= "Qtd Recall; ";
			$conteudo.= "Qtd Recall Dispon; ";
			$conteudo.= "Qtd Manute Fornec; ";
			$conteudo.= "Qtd Manute Intern; ";
			$conteudo.= "Qtd Aguard Manute; ";
			$conteudo.= "Total; ";
			$conteudo.= "Vlr Unit; ";
			$conteudo.= "Vlr Total; ";
			$conteudo.= "Status Repr; "; 
			
			echo $conteudo.="\n";
			
			if ($relatorio !== null && count($relatorio) != 0) {
			 
				foreach ($relatorio as $row){
		 
					$conteudo =  $row['data_posicao_estoque'].";";
					$conteudo .= $row['tipo_item'].";";
					$conteudo .= $row['repoid'].";"; 
					$conteudo .= $row['repnome'].";";
					$conteudo .= utf8_decode($row['cidade']).";";
					$conteudo .= $row['uf'].";";
					$conteudo .= $row['idprd'].";";
					$conteudo .= $row['prdproduto'].";"; 
					$conteudo .= $row['qtd_disponivel'].";";
					$conteudo .= $row['qtd_reserva'].";";
					$conteudo .= $row['qtd_transito'].";";
					$conteudo .= $row['qtd_reserv_transi'].";";
					$conteudo .= $row['qtd_conferencia_if'].";";
					$conteudo .= $row['qtd_retirada'].";";
					$conteudo .= $row['qtd_instalador'].";";
					$conteudo .= $row['qtd_retornado'].";";
					$conteudo .= $row['qtd_recall'].";";
					$conteudo .= $row['qtd_recall_disponivel'].";";
					$conteudo .= $row['qtd_manutencao_fornecedor'].";";
					$conteudo .= $row['qtd_manutencao_interna'].";";
					$conteudo .= $row['qtd_aguardando_manutencao'].";";
					$conteudo .= $row['total'].";";
					$conteudo .= number_format($row['custo_medio_produto'],2,",",".").";";
					$conteudo .= number_format($row['vlr_total'],2,",",".").";";
					$conteudo .= $row['repstatus'].";";
					
					echo $conteudo."\n";
				}
			}else{
				//$conteudo = "Nenhum Registro Encontrado";
				//echo $conteudo."\n";
				echo "NORES";
			}
	}
}