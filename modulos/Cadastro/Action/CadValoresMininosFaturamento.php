<?php 
 
require 'modulos/Cadastro/DAO/CadValoresMinimosFaturamentoDAO.php';

class CadValoresMininosFaturamento {

	private $dao;
	public $msg;

	
	public function CadValoresMininosFaturamento() {
		global $conn;
		
		$this->dao = new CadValoresMinimosFaturamentoDAO($conn);
		
		$this->dao->vmfusuoid_cadastro 				= $_SESSION[usuario][oid];
		$this->dao->vmfoid 							= (!empty($_POST['vmfoid'])) 						? $_POST['vmfoid'] 																	: 0;
	    $this->dao->vmfvl_acionamento 				= (!empty($_POST['vmfvl_acionamento'])) 			? str_replace(',','.',str_replace('.','',$_POST['vmfvl_acionamento']))				: 0;
		$this->dao->vmfqtd_min_acionamento 			= (!empty($_POST['vmfqtd_min_acionamento'])) 		? $_POST['vmfqtd_min_acionamento'] 													: 0;
		$this->dao->vmfqtd_max_acionamento 			= (!empty($_POST['vmfqtd_max_acionamento'])) 		? $_POST['vmfqtd_max_acionamento'] 													: 0;
		$this->dao->vmfvl_localizacao_web 			= (!empty($_POST['vmfvl_localizacao_web'])) 		? str_replace(',','.',str_replace('.','',$_POST['vmfvl_localizacao_web']))			: 0;			
		$this->dao->vmfvl_localizacao_solicitada 	= (!empty($_POST['vmfvl_localizacao_solicitada'])) 	? str_replace(',','.',str_replace('.','',$_POST['vmfvl_localizacao_solicitada']))	: 0;
		$this->dao->vmfvl_bloqueio_solicitado		= (!empty($_POST['vmfvl_bloqueio_solicitado'])) 	? str_replace(',','.',str_replace('.','',$_POST['vmfvl_bloqueio_solicitado']))		: 0;
		$this->dao->vmfvl_faturamento_minimo 		= (!empty($_POST['vmfvl_faturamento_minimo'])) 		? str_replace(',','.',str_replace('.','',$_POST['vmfvl_faturamento_minimo']))		: 0;
	}
	
	public function recuperar() {
		try{
			$retorno = $this->dao->recuperar();
			
			if (empty($retorno['vmfqtd_min_acionamento'])){
				$retorno['vmfqtd_min_acionamento'] = 0;
			}
			
			if (empty($retorno['vmfqtd_max_acionamento'])){
				$retorno['vmfqtd_max_acionamento'] = 0;
			}
			
			echo json_encode(	array(	"error" => 0,
						 				"msg" => "",
						  				"retorno" => $retorno));
			exit();
		}catch(Exception $e){
			echo json_encode(	array(	"error" => 1,
										"msg" =>$e->getMessage()));
			exit();
		}
	}
	
	public function salvar(){ 
		try{
			$this->dao->salvar();
			$retorno = $this->dao->recuperar();
                        
                        if ($this->dao->vmfoid > 0 || $this->dao->vmfoid != '') {
                                $msg = "Registro atualizado com sucesso!";
                        } else {
                                $msg = "Registro cadastrado com sucesso!";
                        }
			
			echo json_encode(	array(	"error" => 0,
					 					"msg" => $msg,
										"retorno" => $retorno));
			exit();
		}catch(Exception $e){
			echo json_encode(	array(	"error" => 1,
						 				"msg" =>$e->getMessage()));
			exit();
		}
	} 
}






?>