<?php
/**
 * @file RelEstatisticasGsm.class.php
 * Classe para retornar dados do veículo utilizando o banco de dados Oracle
 * @author Paulo Henrique da Silva Junior
 * @version 20/08/2013
 * @since 20/08/2013
 * @package SASCAR RelEstatisticasGsm.class.php
*/
require_once (_MODULEDIR_.'Relatorio/DAO/RelEstatisticasGsmDAO.class.php');

class RelEstatisticasGsm {
    
    private $dao;
    
    public function __construct() {
        $this->dao = new RelEstatisticasGsmDAO();
    }

    public function getDataHora($veioid) {
    	$result = $this->dao->getDadosVeiculo($veioid);
    	return $result['datahora'];
    }

    public function fecharConexao() {
    	return $this->dao->fecharConexao();
    }
}