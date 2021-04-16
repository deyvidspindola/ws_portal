<?php
/**
 * Sascar - Sistema Corporativo
 *
 * LICENSE
 *
 * Sascar Tecnologia Automotiva S/A - Todos os Direitos Reservados
 *
 * @author Bruno Bonfim Affonso <bruno.bonfim@sascar.com.br>
 * @version 12/09/2016
 * @since 12/09/2016
 * @package Core
 * @subpackage Classe Modelo do Boleto
 * @copyright Copyright (c) Sascar Tecnologia Automotiva S/A (http://www.sascar.com.br)
 */
namespace module\Boleto;

use module\Boleto\BoletoDAO;

class BoletoModel{
    private $dao;
    
    public function __construct(){
        $this->dao = new BoletoDAO();
    }
    
    /**
     * Retorna os dados bancarios.
     * Caso for informado forcoid - busca na tabela forma_cobranca;
     * Caso for informado cfbbanco - busca na tabela config_banco;
     *
     * @param int $forcoid ID da forma de cobrança
     * @param int $cfbbanco ID do Banco
     * @return mixed array/false
     */
    public function getDadosBancarios($forcoid=0, $cfbbanco=0){
        if($forcoid > 0){
            return $this->dao->getDadosBancarios($forcoid);         
        } elseif($cfbbanco > 0){
            return $this->dao->getDadosBancarios(0, $cfbbanco);         
        } else{
            return false;
        }
    }
    
    /**
     * Retorna true se o boleto está registrado no banco.
     * @param int $titulo Número do título
     * @param string $tipo Define em qual tabela realiza a consulta - Tipo do titulo: titulo; consolidado; retencao;
     * @throw
     * @return boolean
     */
    public function consultarRegistroBoleto($titulo=0, $tipo='titulo'){
        if($titulo > 0 && !empty($tipo)){
            return $this->dao->consultarRegistroBoleto($titulo, $tipo);         
        } else{
            throw new Exception('TAX007');
        }
    }
    
    /**
     * Retorna as instruções para apresentar no boleto.
     *
     * @param string $tipoBoleto
     * @return array
     */
    public function getInstrucoes($tipoBoleto){
        return $this->dao->getInstrucoes($tipoBoleto);
    }

    /**
     * Retorna a data de vencimento de um boleto
     * @return string
     */
    public function getDataVencimento($titoid){
        return $this->dao->getDataVencimento($titoid);
    }
    
    /**
     * Retorna os prazso estipulados pela Febraban com o limite de valor e datas para registro de boletos 
     * @return array|\infra\Array
     */
    public function getPrazosFebraban(){
    	return $this->dao->getVerificaPrazosFebraban();
    }
 
    /**
     * Verifica se o id de ttulo fornecido  de reteno (boleto seco).
     * @return bool
     */
     public function isTituloRetencao($titoId) {
        return $this->dao->isTituloRetencao($titoId);
    }

    /**
     * Retorna o parâmetro de sistema DIAS_BAIXA_DEVOLUCAO_BOLETO_SECO.
     * @return int
     */
    public function getDiasBaixaDevolucaoBoletoSeco() {
        return $this->dao->getDiasBaixaDevolucaoBoletoSeco();
    }

    /**
     * Retorna a forma de registro do Boleto no banco (XML ou CNAB)
     * @return string
     */
    public function getformaRegistro($titoid) {
        return $this->dao->getformaRegistro($titoid);
    }
    
    /**
     * Retorna o o nome da tabela me que o título se encontra
     * @param unknown $titoid
     * @return string
     */
    public function getTabelaTitulo($titoid){
    	return $this->dao->getTabelaTitulo($titoid);
    }
    
}