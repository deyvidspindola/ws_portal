<?php
require 'modulos/Principal/DAO/PrnPropostaSeguradoraDAO.php';
require 'modulos/Principal/View/PrnPropostaSeguradoraView.class.php';

class PrnPropostaSeguradora {	
	private $dao;
    private $view;
	
	function __construct(){		
		global $conn;
		$this->dao  = new PrnPropostaSeguradoraDAO($conn);
		$this->view = new PrnPropostaSeguradoraView();
	}
	
	public function propostaSegurado($id) {
		return $this->dao->propostaSegurado($id);
	}
	
	public function propostaSeguradoHistorico($id){
		return $this->dao->propostaSeguradoHistorico($id);
	}
	
	public function existeProposta($prpsproposta,$prpstpcoid) {
		
		$existeProposta = $this->dao->existeProposta($prpsproposta,$prpstpcoid);
		if ($existeProposta) {
			$retorno = array(
						"existeProposta"	=> 1
			);
		} else {
			$retorno = array(
						"existeProposta"	=> 0
			);
		}
		
		echo json_encode($retorno);
		exit;
	}
	
	public function getTiposContrato() {
		$txt = "";
		
		$tiposContrato = $this->dao->getTiposContrato();
		foreach ($tiposContrato AS $tipo) {
			$txt .= "<option value=\"".$tipo['tpcoid']."\">".$tipo['tpcdescricao']."</option>\n";
		}		
		echo $txt;
	}
	
	public function incluirProposta(
			$prpsproposta,
			$prpstpcoid,
			$prpsdt_solicitacao,
			$prpsprazo_inst,
			$prpsplaca,
			$prpschassi,
			$prpsapolice,
			$prpsno_item,
			$prpscorroid,
			$prpsemail_corretor,
			$prpscia,
			$prpscod_unid_emis,
			$prpsprpssoid,
			$prpsinicio_vigencia,
			$prpsfim_vigencia,
			$prpsobs_geral,
			$prpsscnpj_cpf,
			$prpstipo_pessoa,
			$prpssegurado,
			$prpsendereco,
			$prpsbairro,
			$prpsmunicipio,
			$prpsuf,
			$prpsddd,
			$prpsfone,
			$prpsnumero,
			$prpscep,
			$veisegoid,
			$prpsaditamento,
			$prpscombinacao,
			$prpssolicitante,
			$prpscorretor,
			$prpsssexo, 
			$prpssrg, 
			$prpssdt_nascimento,
			$prpsscomplemento,
			$veimlooid,
			$veicor,
			$veino_ano,
			$veino_renavan,
			$prpsddd_corretor,
			$prpsfone_corretor,
			$veinovo_prazo,
            $prpsddd2,
			$prpsfone2,
            $prpsddd3,
			$prpsfone3) {
		
		try {	
			$feedback = $this->dao->incluirProposta(
				$prpsproposta,
				$prpstpcoid,
				$prpsdt_solicitacao,
				$prpsprazo_inst,
				$prpsplaca,
				$prpschassi,
				$prpsapolice,
				$prpsno_item,
				$prpscorroid,
				$prpsemail_corretor,
				$prpscia,
				$prpscod_unid_emis,
				$prpsprpssoid,
				$prpsinicio_vigencia,
				$prpsfim_vigencia,
				$prpsobs_geral,
				preg_replace("/[^0-9]/","", $prpsscnpj_cpf),
				$prpstipo_pessoa,
				$prpssegurado,
				$prpsendereco,
				$prpsbairro,
				$prpsmunicipio,
				$prpsuf,
				$prpsddd,
				$prpsfone,
				$prpsnumero,
				$prpscep,
				$veisegoid,
				$prpsaditamento,
				$prpscombinacao,
				$prpssolicitante,
				$prpscorretor,
				$prpsssexo, 
				preg_replace("/[^0-9]/","", $prpssrg), 
				$prpssdt_nascimento,
				$prpsscomplemento,
				$veimlooid,
				$veicor,
				$veino_ano,
				$veino_renavan,
				$prpsddd_corretor,
				$prpsfone_corretor,
				$veinovo_prazo,
                $prpsddd2,
                $prpsfone2,
                $prpsddd3,
                $prpsfone3);			
		
		return array(
				"feedback" 	=> $feedback['feedback'],
				"prpsoid"	=> $feedback['prpsoid']
				);
		}
		catch(exception $e) {
			return array(
				"feedback" => $e->getMessage(),
				"prpsoid"	=> 0
				);
		}
	}

	public function delQuarentena($connumero,$proposta) {
		$deletou = $this->dao->delQuarentena($connumero,$proposta);
		if ($deletou) {
			$retorno = array(
					"deletou"	=> 1
			);
		} else {
			$retorno = array(
					"deletou"	=> 0
			);
		}
		
		echo json_encode($retorno);
		exit();
	}
	
	public function incQuarentena($connumero,$proposta,$contrato_tipo) {
		$retornoDAO = $this->dao->incQuarentena($connumero,$proposta,$contrato_tipo);
		$retorno = array(
					"incluiu"			=> $retornoDAO['incluiu'],
					"data_quarentena"	=> $retornoDAO['data_quarentena']
		);
	
		echo json_encode($retorno);
		exit;
	}
    
    public function telaPesquisarArquivo(){
        $_SESSION['aba'] = 5;
        $tipoContrato    = $this->dao->getTipoContrato();
        
        $this->view->getTelaPesquisarArquivo($tipoContrato);
    }
    
    public function pesquisarArquivo($dados){
        if($dados != null){
            $data_inicial  = $dados['data_inicial'];
            $data_final    = $dados['data_final'];
            $tipo_arquivo  = $dados['tipo_arquivo'];
            $tipo_contrato = $dados['tipo_contrato'];
            $status        = $dados['status'];
            
            $data_inicial = explode("/", $data_inicial);
            $data_inicial = $data_inicial[2]."-".$data_inicial[1]."-".$data_inicial[0];
            
            $data_final = explode("/", $data_final);
            $data_final = $data_final[2]."-".$data_final[1]."-".$data_final[0];
            
            $result = $this->dao->getResultadoPesquisarArquivo($data_inicial, $data_final, $tipo_arquivo, $tipo_contrato, $status);         
            $this->view->getTelaResultadoPesquisarArquivo($result);                         
        }
        
        exit;
    }
    
    /**
     * @param String $file - caminho do arquivo
     */
    public function validarArquivo($arquivo){
        if(file_exists($arquivo)){
            echo json_encode(array("status" => true));
        } else{
            echo json_encode(array("status" => false));
        }
        
        exit;
    }
    
    /**
     * @param String $file - caminho do arquivo
     */
    public function downloadFile($arquivo){        
        if(file_exists($arquivo)){
            $nome    = explode("/", $arquivo);
            $nome    = $nome[count($nome) - 1];
            
            header('Content-Type: text/plain'); 
            header('Content-Disposition: attachment; filename="'.$nome.'"');
            header("Content-Type: application/force-download");
            header("Content-Type: application/download");
            readfile($arquivo);
        }
        exit;
    }
}
?>