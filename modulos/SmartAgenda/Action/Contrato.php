<?php

require_once _MODULEDIR_ . 'SmartAgenda/DAO/ContratoDAO.php';

/**
 * Classe ContratoDAO.
 * Camada de negocio para entidades de Contrato
 */
class Contrato{


	private $dao;


	public function __construct($conn = null){

        if( is_null($conn) ){
            global $conn;
        }
		$this->dao = new ContratoDAO($conn);
	}

    public function isContratoSiggo($connumero) {

        $dado = $this->dao->isContratoSiggo($connumero);

        return $dado;

    }

    public function getContratoOS($ordoid){

        $dados = $this->dao->getContratoOS($ordoid);

        return $dados;

    }

    public function getEquipamentoContrato($ordoid){
        $dados = $this->dao->getEquipamentoContrato($ordoid);

        return $dados;
    }

}