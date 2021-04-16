<?php

/**
 * Classe padrão para DAO
 *
 * @author robson.silva
 */
class CadAcaoMotivoDAO {

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
    public function pesquisar(stdClass $parametros) {

        $retorno = array();

        $sql = "SELECT 
                    Acao.aoamdescricao AS acao,
                    Acao.aoamoid AS acaoId,
                    TO_CHAR(Acao.aoamdt_cadastro, 'DD/MM/YYYY') AS acaoCad,
                    Motivo.aoamdescricao AS motivo,
                    Motivo.aoamoid AS motivoId,
                    TO_CHAR(Motivo.aoamdt_cadastro, 'DD/MM/YYYY') AS motivoCad
                FROM 
                    analise_os_acao_motivo AS Acao
                LEFT JOIN
                    analise_os_acao_motivo AS Motivo ON Acao.aoamoid = Motivo.aoampai AND Motivo.aoamdt_exclusao IS NULL
                WHERE
                    1=1 
                AND
                    Acao.aoampai IS NULL
                AND 
                    Acao.aoamdt_exclusao IS NULL ";
        // Incluir Pesquisa Filtro
        if (isset($parametros->aoamoid_motivo) && !empty($parametros->aoamoid_motivo)) {

            $sql .= "AND
                        Motivo.aoamoid = " . intval($parametros->aoamoid_motivo) . "";
        }

        if (isset($parametros->aoamoid) && trim($parametros->aoamoid) != '') {

            $sql .= "AND
                        Acao.aoamoid = " . intval($parametros->aoamoid) . " ";
        }

        $sql .= " ORDER BY
                       Acao.aoamdescricao,
                       Motivo.aoamdescricao ";
        
        // Fim
        if (!$rs = pg_query($this->conn, $sql)) {
            throw new ErrorException(self::MENSAGEM_ERRO_PROCESSAMENTO);
        }
        
        while ($row = pg_fetch_object($rs)) {
            
            if (!isset($retorno[$row->acaoid])){
                 $retorno[$row->acaoid] = array(
                     'acao' => $row->acao,
                     'cadastro' => $row->acaocad,
                     'tipo' => "Ação",
                     'motivos'  => array()
                 );
             } 

            if (!is_null($row->motivoid)) {
                $retorno[$row->acaoid]['motivos'][] = array(
                   'motivo' => $row->motivo,
                   'motivoID' => $row->motivoid,
                   'cadastro' => $row->motivocad,
                   'tipo' => "Motivo"
               );
            } 
        }

        
        return $retorno;
    }

    /**
     * Método para realizar a pesquisa de apenas um registro.
     * 
     * @param int $id Identificador único do registro
     * @return stdClass
     * @throws ErrorException
     */
    public function pesquisarPorID($id) {

        $retorno = new stdClass();

        $sql = "SELECT 
                    aoamoid,
                    aoamdescricao,
                    aoampai,
                    aoamdt_cadastro
				FROM 
					analise_os_acao_motivo
				WHERE 
					aoamoid = " . intval($id) . "";

        if (!$rs = pg_query($this->conn, $sql)) {
            throw new ErrorException(self::MENSAGEM_ERRO_PROCESSAMENTO);
        }

        if (pg_num_rows($rs) > 0) {
            $retorno = pg_fetch_object($rs);
        }

        return $retorno;
    }

    /**
     * Responsável para inserir um registro no banco de dados.
     * @param stdClass $dados Dados a serem gravados
     * @return boolean
     * @throws ErrorException
     */
    public function inserir(stdClass $dados) {

        $retorno = new stdClass();
        $retorno->aoamoid = false;

        if (!isset($dados->aoampai)) {
            $dados->aoampai = 'NULL';
        } else {
            $dados->aoampai = intval($dados->aoampai);
        }

        $sql = "INSERT INTO
					analise_os_acao_motivo 
					(
                         aoamdescricao,
                         aoamdt_cadastro,
                         aoamusuoid_cadastro,
                         aoampai 
					)
				VALUES
					(
                    '" . pg_escape_string($dados->aoamdescricao) . "',
                    NOW(),
                    " . $dados->cd_usuario . ",
                    " . $dados->aoampai . "
				) 
                RETURNING aoamoid;";

        if (!$rs = pg_query($this->conn, $sql)) {
            throw new ErrorException(self::MENSAGEM_ERRO_PROCESSAMENTO);
        }

        if (pg_num_rows($rs) > 0) {
            $retorno = pg_fetch_object($rs);
        }

        return $retorno->aoamoid;
    }

    /**
     * Responsável por atualizar os registros 
     * @param stdClass $dados Dados a serem gravados
     * @return boolean
     * @throws ErrorException
     */
    public function atualizar(stdClass $dados) {

        $sql = "UPDATE
					analise_os_acao_motivo
				SET
					aoamdescricao = '" . pg_escape_string($dados->aoamdescricao) . "',
					aoamoid = " . intval($dados->aoamoid) . "
				WHERE 
					aoamoid = " . $dados->aoamoid . "";

        if (!pg_query($this->conn, $sql)) {
            throw new ErrorException(self::MENSAGEM_ERRO_PROCESSAMENTO);
        }

        return $dados->aoamoid;
    }


    /**
     * Exclui (UPDATE) um registro (Motivo) da base de dados.
     * @param stdCalss
     * @return boolean
     * @throws ErrorException
     */
    public function excluir(stdClass $filtros) {

        //Verifica se os dados existem
        if (!isset($filtros->cd_usuario)){
            throw new ErrorException(self::MENSAGEM_ERRO_PROCESSAMENTO);
        }
        if (!isset($filtros->acao_id)){
            throw new ErrorException(self::MENSAGEM_ERRO_PROCESSAMENTO);
        }
        
        
        $sql = "UPDATE
                    analise_os_acao_motivo
                SET
                    aoamdt_exclusao = NOW(),
                    aoamusuoid_exclusao = " . intval($filtros->cd_usuario) . "
                WHERE
                    aoamoid = " . intval($filtros->acao_id) . "";
                
        if (!pg_query($this->conn, $sql)) {
            throw new ErrorException(self::MENSAGEM_ERRO_PROCESSAMENTO);
        }

        return true;
    }

    public function buscarAcoes() {

        $retorno = array();

        $sql = "
            SELECT 
                aoamoid, 
                aoamdescricao,
                TO_CHAR(aoamdt_cadastro, 'DD/MM/YYYY') AS aoamdt_cadastro
            FROM 
                analise_os_acao_motivo
            WHERE
                aoampai IS NULL
            AND 
                aoamdt_exclusao IS NULL
            ORDER BY
                aoamdescricao";

        if (!$rs = pg_query($this->conn, $sql)) {
            throw new ErrorException(self::MENSAGEM_ERRO_PROCESSAMENTO);
        }

        while ($linha = pg_fetch_object($rs)) {
            $retorno[] = $linha;
        }

        return $retorno;
    }

    public function buscarMotivos(stdClass $dados) {

        $retorno = array();

        //Filtro da pesquisa
        $where = "";

        if (isset($dados->aoampai) && trim($dados->aoampai) != '') {
            $where .= "
                AND 
                    aoampai = " . intval($dados->aoampai);
        }

        $sql = "
            SELECT 
                aoamoid, 
                aoamdescricao,
                TO_CHAR(aoamdt_cadastro, 'DD/MM/YYYY') AS aoamdt_cadastro
            FROM 
                analise_os_acao_motivo
            WHERE
                aoampai IS NOT NULL
            AND 
                aoamdt_exclusao IS NULL
            " . $where . "
            ORDER BY
                aoamdescricao";
        
        if (!$rs = pg_query($this->conn, $sql)) {
            throw new ErrorException(self::MENSAGEM_ERRO_PROCESSAMENTO);
        }

        while ($linha = pg_fetch_object($rs)) {
            $retorno[] = $linha;
        }

        return $retorno;
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
