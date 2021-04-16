<?php

require 'modulos/Cadastro/DAO/TipoContratoDAO.php';

class TipoContrato {
	private $dao;
	private $clinome;
	private $clidoc;
	private $tipo;
	private $clioid;
	private $modalidade;
	
	public function TipoContrato() {
		global $conn;
		$this->dao = new TipoContratoDAO($conn);
		$this->clinome = (!empty($_POST['clinome'])) ? $_POST['clinome'] : '';
		$this->clidoc = (!empty($_POST['clidoc'])) ? $_POST['clidoc'] : '';
		$this->tipo = (!empty($_POST['tipo'])) ? $_POST['tipo'] : '';
		$this->clioid = (isset($_POST['clioid']) && $_POST['clioid']>0) ? $_POST['clioid'] : 0;		
		$this->modalidade = (!empty($_POST['modalidade'])) ? $_POST['modalidade'] : '';
	}
	
	public function index() {
		
	}
	
	public function pesquisarClientes() {
		try {
						
			$clientes = $this->dao->pesquisarClientes($this->clinome, $this->clidoc, $this->tipo);
			
			$msg = "";
			
			if (count($clientes)==0) {
				throw new exception("Nenhum resultado encontrado");
			}
			
			echo json_encode(array(
					"clientes"	=>	$clientes,
					"tipo"		=>	utf8_decode($this->tipo),
					"msg"		=>	""
					));
			exit;
		} 
		catch (Exception $e) {
			echo json_encode(array(
					"clientes"	=>	array(),
					"tipo"		=>	'',
					"msg"		=> utf8_decode($e->getMessage)
			));	
			exit;
		}
	}
	
	public function atributosCliente() {
		try {
						
			$atributosCliente = $this->dao->atributosCliente($this->clioid);
				
			$msg = "";
				
			if (count($atributosCliente)==0) {
				throw new exception("Nenhum resultado encontrado");
			}
				
			echo json_encode(array(
					"nome"		=>	$atributosCliente['nome'],
					"tipo"		=>	$atributosCliente['tipo'],
					"doc"		=>	$atributosCliente['doc'],
					"id"		=>	$atributosCliente['id'],
					"msg"		=>	""
			));
			exit;
		}
		catch (Exception $e) {
			echo json_encode(array(
					"nome"		=>	"",
					"tipo"		=>	"",
					"doc"		=>	"",
					"id"		=>	"",
					"msg"		=> utf8_decode($e->getMessage)
			));
			exit;
		}
	}

	public function getEquipamentoProjeto() {
		return $this->dao->getEquipamentoProjeto();
	}

	public function getEquipamentoRestricao($tpcoid) {
		return $this->dao->getEquipamentoRestricao($tpcoid);
	}

	public function getTabelaEquipamentoRestricao($tpcoid) {
		$equipamento_restricao = $this->getEquipamentoRestricao($tpcoid);
		$tabela_equipamento_restricao = '';

		if (count($equipamento_restricao) > 0) {
			ob_start();
			include "modulos/Cadastro/View/tipo_contrato/tabelaRestricaoProjetoEquipamento.php";
			$tabela_equipamento_restricao = ob_get_contents();
			ob_end_clean();
		} 

		return $tabela_equipamento_restricao;
	}

	public function addRestricaoEquipamentoProjeto($tpcitpcoid, $tpcieproid, $tpciusuoid_cadastro) {
		$this->dao->addRestricaoEquipamentoProjeto($tpcitpcoid, $tpcieproid, $tpciusuoid_cadastro);
		return $this->getEquipamentoRestricao($tpcitpcoid);
	}

	public function delRestricaoEquipamentoProjeto($tpcitpcoid, $tpcieproids, $tpciusuoid_exclusao) {
		$this->dao->delRestricaoEquipamentoProjeto($tpcitpcoid, $tpcieproids, $tpciusuoid_exclusao);
		return $this->getEquipamentoRestricao($tpcitpcoid);
	}
}