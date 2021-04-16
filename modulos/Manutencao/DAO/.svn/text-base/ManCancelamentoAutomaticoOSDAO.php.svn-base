<?php

/**
 * Classe padrão para DAO
 *
 * @package  Cadastro
 * @author   Robson Aparecido Trizotte da Silva <robson.silva@meta.com.br>
 * 
 */
class ManCancelamentoAutomaticoOSDAO {

    /**
     * Conexão com o banco de dados
     * @var resource
     */
    private $conn;

    /**
     * Mensagem de erro para o processamentos dos dados
     * @const String
     */

    const MENSAGEM_ERRO_PROCESSAMENTO = "Houve um erro no processamento dos dados.";

    public function __construct($conn) {
//Seta a conexão na classe
        $this->conn = $conn;
    }

    /**
     * Método para realizar a pesquisa de varios registros
     * @param stdClass $parametros Filtros da pesquisa
     * @return array
     * @throws ErrorException
     */
    public function pesquisar() {

        $retorno = array();
        $sql = "SELECT 
					tpcoid, 
					tpcdescricao
				FROM 
					tipo_contrato                    
                WHERE 
                    tpcativo is TRUE
                ORDER BY
                       tpcdescricao
                       ";

        if (!$rs = pg_query($this->conn, $sql)) {
            throw new ErrorException(self::MENSAGEM_ERRO_PROCESSAMENTO);
        }

        while ($row = pg_fetch_object($rs)) {

            $retorno[] = $row;
        }

        return $retorno;
    }

    /**
     * Método para realizar a pesquisa dos
     * @param stdClass $parametros Filtros da pesquisa
     * @return array
     * @throws ErrorException
     */
    public function pesquisarParametrizacao() {

        $retorno = new stdClass();
        $sql = "SELECT 
                    array_to_string(pcaotipos_de, ',') AS pcaotipos_de,
                    array_to_string(pcaotipos_para, ',') AS pcaotipos_para,
                    array_to_string(pcaostatus_de, ',') AS pcaostatus_de,
                    array_to_string(pcaostatus_para, ',') AS pcaostatus_para,
                    pcaousuoid_cadastro,
                    pcaodt_inclusao
				FROM 
					parametrizacao_cancelamento_automatico_os                    
                LIMIT 1
";

        if (!$rs = pg_query($this->conn, $sql)) {
            throw new ErrorException(self::MENSAGEM_ERRO_PROCESSAMENTO);
        }
        if (pg_num_rows($rs) > 0){
            $retorno = pg_fetch_object($rs);
        }
        
        return $retorno;
        
    }

    /**
     * Método para realizar a pesquisa de varios registros
     * @param stdClass $parametros Filtros da pesquisa
     * @return array
     * @throws ErrorException
     */
    public function pesquisarContratoSituacao() {

        $retorno = array();
        $sql = "SELECT 
					csioid, 
					csidescricao
				FROM 
					contrato_situacao                    
                WHERE 
                    csiexclusao is NULL
                ORDER BY
                       csidescricao
                       ";

        if (!$rs = pg_query($this->conn, $sql)) {
            throw new ErrorException(self::MENSAGEM_ERRO_PROCESSAMENTO);
        }

        while ($row = pg_fetch_object($rs)) {

            $retorno[] = $row;
        }

        return $retorno;
    }

    /**
     * Responsável por atualizar os registros 
     * @param stdClass $dados Dados a serem gravados
     * @return boolean
     * @throws ErrorException
     */
    public function salvar(stdClass $dados) {

        $sql = "SELECT EXISTS(
                    SELECT 
                        1
                    FROM 
                        parametrizacao_cancelamento_automatico_os
                ) AS existe
                       ";

        if (!$rs = pg_query($this->conn, $sql)) {
            throw new ErrorException(self::MENSAGEM_ERRO_PROCESSAMENTO);
        }

        $linhaVerifica = pg_fetch_object($rs);

        //Trata os dados do array
        $gravar = new stdClass();
        $gravar->pcaotipos_de = '';
        $gravar->pcaotipos_para = '';
        $gravar->pcaostatus_de = '';
        $gravar->pcaostatus_para = '';


        if (is_array($dados->pcaotipos_de) && count($dados->pcaotipos_de) > 0) {
            $gravar->pcaotipos_de = "'{" . implode(",", $dados->pcaotipos_de) . "}'";
        } else {
            $gravar->pcaotipos_de = "NULL";
        }

        if (is_array($dados->pcaotipos_para) && count($dados->pcaotipos_para) > 0) {
            $gravar->pcaotipos_para = "'{" . implode(",", $dados->pcaotipos_para) . "}'";
        } else {
            $gravar->pcaotipos_para = "NULL";
        }

        if (is_array($dados->pcaostatus_de) && count($dados->pcaostatus_de) > 0) {
            $gravar->pcaostatus_de = "'{" . implode(",", $dados->pcaostatus_de) . "}'";
        } else {
            $gravar->pcaostatus_de = "NULL";
        }

        if (is_array($dados->pcaostatus_para) && count($dados->pcaostatus_para) > 0) {
            $gravar->pcaostatus_para = "'{" . implode(",", $dados->pcaostatus_para) . "}'";
        } else {
            $gravar->pcaostatus_para = "NULL";
        }
        //pcaotipos_de

        if ($linhaVerifica->existe == 't') {
            $sql = "UPDATE
                        parametrizacao_cancelamento_automatico_os
                    SET
                        pcaotipos_de = " . $gravar->pcaotipos_de . ",
                        pcaotipos_para = " . $gravar->pcaotipos_para . ",
                        pcaostatus_de = " . $gravar->pcaostatus_de . ",
                        pcaostatus_para = " . $gravar->pcaostatus_para . ",
                        pcaousuoid_cadastro = " . $dados->pcaousuoid_cadastro . ",
                        pcaodt_inclusao = 'NOW'
                    ";
        } else {
            $sql = "INSERT INTO
						parametrizacao_cancelamento_automatico_os
    						(
							pcaotipos_de,
                            pcaotipos_para,
                            pcaostatus_de,
                            pcaostatus_para,
                            pcaousuoid_cadastro,
							pcaodt_inclusao)
    			VALUES (
    						 " . $gravar->pcaotipos_de . ", 			
                             " . $gravar->pcaotipos_para . ",
                             " . $gravar->pcaostatus_de . ", 			                                     
                             " . $gravar->pcaostatus_para . ",
                             '" . $dados->pcaousuoid_cadastro . "',
                             NOW()
    						)";
        }
        if (!pg_query($this->conn, $sql)) {
            throw new ErrorException(self::MENSAGEM_ERRO_PROCESSAMENTO . $sql);
        }

        return true;
    }

    /**
     * Abre a transação
     */
    public function begin() {
        pg_query($this->conn, 'BEGIN');
    }

    /**
     * Finaliza um transação
     */
    public function commit() {
        pg_query($this->conn, 'COMMIT');
    }

    /**
     * Aborta uma transação
     */
    public function rollback() {
        pg_query($this->conn, 'ROLLBACK');
    }

}

?>
