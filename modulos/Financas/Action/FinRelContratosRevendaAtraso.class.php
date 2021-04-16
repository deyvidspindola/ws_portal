<?php

/*
 * require para persistência de dados - classe DAO 
 */
require _MODULEDIR_ . 'Financas/DAO/FinRelContratosRevendaAtrasoDAO.php';


/**
 * FinRelContratosRevendaAtraso.php
 * 
 * Classe para buscar os contratos de revenda
 * com atraso na primeira parcela.
 * 
 * 
 * @author  Willian Ouchi
 * @email   willian.ouchi@meta.com.br
 * @since   09/11/2012
 * @package Finanças
 * 
 */
class FinRelContratosRevendaAtraso{
    
    /**
     * Atributo para acesso a persistência de dados
     */
    private $dao;
    private $conn;
    
    
    /*
     * Construtor
     *
     * @autor Willian Ouchi
     * @email willian.ouchi@meta.com.br
     */

    public function FinRelContratosRevendaAtraso() {

        global $conn;

        $this->conn = $conn;

        /**
         * Objeto
         * - DAO
         */
        $this->dao = new FinRelContratosRevendaAtrasoDAO($conn);
    }    
    
    
    /**
    * @author	Willian Ouchi
    * @email	willian.ouchi@meta.com.br
    * @return	Carrega as informações primárias da tela
    * */
    public function carregarInformacoes($conno_tipo = null) {
        
        $resultado = array();
        $cont = 0;
        
        $rs = $this->dao->buscarTiposContrato($conno_tipo);
        while ($rTipoContrato = pg_fetch_assoc($rs)){
        
            $resultado['tiposContrato'][$cont]['tpcoid'] = (!isset($rTipoContrato['tpcoid'])) ? '' : $rTipoContrato['tpcoid'];
            $resultado['tiposContrato'][$cont]['tpcdescricao'] = empty($rTipoContrato['tpcdescricao']) ? '' : $rTipoContrato['tpcdescricao'];
            $cont++;            
        }
        
        echo json_encode($resultado);
        exit;        
    }
    
    
    /**
    * @author	Willian Ouchi
    * @email	willian.ouchi@meta.com.br
    * @return	String json com os contratos
    * */
    public function pesquisar_contratos() {
        
        $this->dao->clinome = (isset($_POST['clinome'])) ? $_POST['clinome'] : "";
        $this->dao->connumero = (isset($_POST['connumero'])) ? $_POST['connumero'] : "";
        $this->dao->diasatraso = (isset($_POST['diasatraso'])) ? $_POST['diasatraso'] : "";
        $this->dao->conno_tipo = (isset($_POST['conno_tipo'])) ? $_POST['conno_tipo'] : "";
        $this->dao->nfldt_emissao_ini = (isset($_POST['nfldt_emissao_ini'])) ? $_POST['nfldt_emissao_ini'] : "";
        $this->dao->nfldt_emissao_fin = (isset($_POST['nfldt_emissao_fin'])) ? $_POST['nfldt_emissao_fin'] : "";
        $this->dao->rczcd_zona = (isset($_POST['rczcd_zona'])) ? $_POST['rczcd_zona'] : "";
        
        $rs = $this->dao->buscarContratosRevandaAtraso();

        $resultado = array();
        $resultado['contratos'] = array();
        
        $cont = 0;
        
        while ($rContrato = pg_fetch_assoc($rs)){
            
            $resultado['contratos'][$cont]['connumero'] = empty($rContrato['connumero']) ? '' : $rContrato['connumero'];
            $resultado['contratos'][$cont]['condt_cadastro'] = empty($rContrato['condt_cadastro']) ? '' : date('d/m/Y',strtotime($rContrato['condt_cadastro']));
            $resultado['contratos'][$cont]['condt_ini_vigencia'] = empty($rContrato['condt_ini_vigencia']) ? '' : date('d/m/Y',strtotime($rContrato['condt_ini_vigencia']));
            $resultado['contratos'][$cont]['tpcdescricao'] = utf8_encode($rContrato['tpcdescricao']);
            $resultado['contratos'][$cont]['dmv'] = empty($rContrato['dmv']) ? '' : $rContrato['dmv'];
            $resultado['contratos'][$cont]['clinome'] = utf8_encode($rContrato['clinome']);
            $resultado['contratos'][$cont]['clifone'] = empty($rContrato['clifone']) ? '' : $rContrato['clifone'];            
            $resultado['contratos'][$cont]['cliemail'] = empty($rContrato['cliemail']) ? '' : $rContrato['cliemail'];          
            $resultado['contratos'][$cont]['nfloid'] = empty($rContrato['nfloid']) ? '0' : $rContrato['nfloid'];
            $resultado['contratos'][$cont]['nflno_numero'] = empty($rContrato['nflno_numero']) ? '' : $rContrato['nflno_numero'];
            $resultado['contratos'][$cont]['nflserie'] = empty($rContrato['nflserie']) ? '' : trim($rContrato['nflserie']);
            $resultado['contratos'][$cont]['nfldt_emissao'] = empty($rContrato['nfldt_emissao']) ? '' : date('d/m/Y',strtotime($rContrato['nfldt_emissao']));
            $resultado['contratos'][$cont]['nflvl_total'] = empty($rContrato['nflvl_total']) ? '' : number_format($rContrato['nflvl_total'], 2, ',', '.');
            $resultado['contratos'][$cont]['titdt_vencimento'] = empty($rContrato['titdt_vencimento']) ? '' : date('d/m/Y',strtotime($rContrato['titdt_vencimento']));
            $resultado['contratos'][$cont]['titvl_titulo'] = empty($rContrato['titvl_titulo']) ? '' : number_format($rContrato['titvl_titulo'], 2, ',', '.');
            
            $cont++;
        }  
        
        $resultado['total_registros'] = pg_num_rows($rs);        
        
        echo json_encode($resultado);
        
        exit;
    }
    
    /**
    * @author	Willian Ouchi
    * @email	willian.ouchi@meta.com.br
    * @return	Gera o arquivos xls e retorna o path para download
    * */
    public function gerarXLS() {

        $tabela = (isset($_POST['tabela'])) ? $_POST['tabela'] : null;
        $tabela = '<table border="1">' . utf8_decode($tabela) . '</table>';        

        $resultado['file_path'] = "/var/www/arq_financeiro/";
        $resultado['file_name'] = "rel_contratos_parcela_atraso_" . date('dmYHi') . ".xls";
        
        file_put_contents($resultado['file_path'] . $resultado['file_name'], $tabela);

        echo json_encode($resultado);

        exit;
    }
}

?>
