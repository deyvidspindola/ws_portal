<?php
require_once _MODULEDIR_ . 'Cron/DAO/CronDAO.php';

/**
 * CancelamentoAutomaticoRmsDAO.php
 * 
 * Classe de persistência dos dados de Cancelamento RMS
 * 
 * @author André Luiz Zilz <andre.zilz@meta.com.br>
 * @version 31/05/2013
 * @package Cron
 */
class CancelamentoAutomaticoRmsDAO extends CronDAO {
	
	/**
	 * Construtor da Classe
	 * @param $conn
	 */
	public function __construct($conn) {
		parent::__construct($conn);
	}
	
	
	/**
	* Atualiza tabelas relacionadas a requisição, cancelando as requisições.
	*  
 	* @param int $reqmoid
	*/
	public function atualizarRmsSemAprovacao($reqmoid, $diasCancelamento){
		
		$reqmoid 			= (int)$reqmoid;
		$cd_usuario 		= $this->buscarCodigoUsuarioCron();
	
		//Cancela a requisição
		$sql = "
				UPDATE 	req_material
				SET		reqmstatus_compras = 'RE',
						reqmusuoid_excl = " . $cd_usuario . ",
	 					reqmmotivo_canc = 'RMS não aprovada pelo gestor dentro dos ".$diasCancelamento." dias úteis.',
	 					reqmexclusao = NOW()
				WHERE 	reqmoid = ". $reqmoid ."			
				";
		
		$this->query($sql);
		
		
		//Cancela os itens da requisição
		$sql = "
				UPDATE 	req_material_item
				SET		rmiexclusao  = NOW(),
						rmiusuoid_excl = ". $cd_usuario ."
				WHERE 	rmireqmoid  = ". $reqmoid ."
				";
		
		$this->query($sql);
	
		//Altera o status da tabela de aprovação da requisição
		$sql = "
				UPDATE 	req_material_aprovacao
				SET		rmapstatus  = 'CA'
				WHERE 	rmapreqmoid = ". $reqmoid ."
				";
		
		$this->query($sql);		
		
	}
	
	/**
	 * Busca todos as Requisições SEM APROVACAO
	 * @param int $diasCancelamento
	 * @return array
	 */
	public function buscarRequisicoesCancelamentoSemAprovacao($diasCancelamento){
		
		$diasCancelamento 	= (int)$diasCancelamento;
		$requisicoes = array();
		
		$sql = "
				SELECT 		reqmoid 
				FROM 		req_material
				INNER JOIN 	req_material_aprovacao ON rmapreqmoid = reqmoid
				WHERE 		reqmexclusao IS NULL
				AND 		rmapstatus = 'AA'
				AND		 	((NOW()::date - rmapdt_cadastro::date) > ". $diasCancelamento .")
				";
		// and rmapdt_cadastro::date = now()::date
		$rs = $this->query($sql);
		
		while($row = pg_fetch_object($rs)) {
			
			$requisicoes[] = $row->reqmoid;
		}
		
		return $requisicoes;		
		
	}		
	
	/**
	 * Busca todos as Requisições SEM APROVADOR
	 * @param int $diasCancelamento
	 * @return array
	 */
	public function buscarRequisicoesCancelamentoSemAprovador($diasCancelamento){
		
		$diasCancelamento 	= (int)$diasCancelamento;
		$requisicoes = array();
		
		$sql = "
				SELECT 		reqmoid 
				FROM 		req_material
				WHERE 		reqmoid NOT IN (SELECT rmapreqmoid FROM req_material_aprovacao WHERE rmapdt_exclusao IS NULL)
				AND 		reqmexclusao IS NULL
				AND 		reqmstatus_aprovacao = 'P'
				AND		 	((NOW()::date - reqmcadastro::date) > ". $diasCancelamento .")
				";
		// 	and reqmcadastro::date = now()::date
		$rs = $this->query($sql);
		
		while($row = pg_fetch_object($rs)) {
			
			$requisicoes[] = $row->reqmoid;
		}
		
		return $requisicoes;		
		
	}		
	
	/**
	 * Recupera os dias parametrizados para cancelamento de requisição SEM APROVAÇÃO
	 * 
	 * @return int
	 */
	public function buscarDiasCancelamentoSemAprovacao(){
		
		$sql = "SELECT COALESCE(sisno_dias_canc_rms_sem_aprovacao,0) AS dias
				FROM sistema";
			
		$rs = $this->query($sql);
		
		$row = pg_fetch_object($rs);
		
		$dias = isset($row->dias) ? $row->dias : 0;
	
		return $dias;		
	}
		
	/**
	 * Recupera os dias parametrizados para cancelamento de requisição SEM APROVADOR
	 *
	 * @return int
	 */
	public function buscarDiasCancelamentoSemAprovador(){
	
		$sql = "SELECT COALESCE(sisqtddiarmscanc,0) AS dias
				FROM sistema";
			
		$rs = $this->query($sql);
	
		$row = pg_fetch_object($rs);
	
		$dias = isset($row->dias) ? $row->dias : 0;
	
		return $dias;
	}
	
	/**
	 * @param  $reqmoid => Numero RMS
	 * @return $usuemail => email destinatário
	 * Pega email do destinatario vinculado a RMS constante na base sascar
	 */
	public function buscarDestinatarioEmail($reqmoid) {
	
	
		$sql="	SELECT
			    		usuemail
				FROM
						usuarios
				INNER JOIN 
						funcionario ON funcionario.funoid=usuarios.usufunoid
				INNER JOIN 
						req_material ON req_material.reqmfuncoid=funcionario.funoid
				WHERE
						usuarios.dt_exclusao IS NULL
				AND 
						reqmoid = ".$reqmoid." 
				ORDER BY
						cd_usuario ";
		 
		$rs = $this->query($sql);
	
		$row = pg_fetch_object($rs);
	
		$dias = isset($row->usuemail) ? $row->usuemail : "";
			
		return $usuemail;
	}
}

