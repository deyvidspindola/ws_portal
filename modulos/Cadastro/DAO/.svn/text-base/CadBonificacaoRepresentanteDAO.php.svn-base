<?php

include "CadCategoriaBonificacaoRepresentanteDAO.php";

/**
 * Classe CadBonificacaoRepresentanteDAO.
 * Camada de modelagem de dados.
 *
 * @package  Cadastro
 * @author   RICARDO ROJO BONFIM <ricardo.bonfim@meta.com.br>
 *
 */
class CadBonificacaoRepresentanteDAO {

    /** Conexão com o banco de dados */
    private $conn;

    /** Usuario logado */
    private $usuarioLogado;

    const MENSAGEM_ERRO_PROCESSAMENTO = "Houve um erro no processamento dos dados.";

    public function __construct($conn) {

        //Seta a conexao na classe
        $this->conn = $conn;
        $this->usuarioLogado = isset($_SESSION['usuario']['oid']) ? $_SESSION['usuario']['oid'] : '';

        //Se nao tiver nada na sessao assume usuario AUTOMATICO
        if(empty($this->usuarioLogado)) {
            $this->usuarioLogado = 2750;
        }
    }

    /**
     * Método para realizar a pesquisa de varios registros
     * @param stdClass $parametros Filtros da pesquisa
     * @return array
     * @throws ErrorException
     */
    public function pesquisar(stdClass $parametros){

        $retorno = array();

        $sql = "SELECT 
                    bonreoid,
                    bonrecatnome,
                    repnome,
                    TO_CHAR(bonredt_bonificacao,'MM/YYYY') AS bonredt_bonificacao,
                    bonrevalor_bonificacao,
                    bonreqtd_min_os,
                    bonrestatus,
                    CASE
                        WHEN bonrestatus = 'A' THEN
                            'Aberto'
                        WHEN bonrestatus = 'C' THEN
                            'Cancelado'
                        ELSE
                            'Rateado'
                    END AS status_formatado
                FROM 
                    bonificacao_representante
                    INNER JOIN bonificacao_representante_categoria ON bonrebonrecatoid = bonrecatoid
                    INNER JOIN representante ON bonrerepoid = repoid
                WHERE 
                    bonificacao_representante.bonredt_exclusao IS NULL ";

        if ( isset($parametros->bonrebonrecatoid) && trim($parametros->bonrebonrecatoid) != '' ) {

            $sql .= "AND
                        bonrebonrecatoid = " . intval( $parametros->bonrebonrecatoid ) . " ";
            
        }

        if ( isset($parametros->bonrerepoid) && trim($parametros->bonrerepoid) != '' ) {

            $sql .= "AND
                        bonrerepoid = " . intval( $parametros->bonrerepoid ) . " ";
            
        }

        if ( isset($parametros->bonredt_bonificacao) && !empty($parametros->bonredt_bonificacao) ) {

            $sql .= "AND
                        TO_CHAR(bonredt_bonificacao,'MM/YYYY') = '" . $parametros->bonredt_bonificacao . "' ";
                
        }

        if ( isset($parametros->bonrestatus) && !empty($parametros->bonrestatus) ) {
        
            $sql .= "AND
                        bonrestatus = '" . pg_escape_string( $parametros->bonrestatus ) . "' ";
                
        }

        $sql .= "
                ORDER BY
                    bonrestatus,
                    bonredt_bonificacao,
                    repnome";

        $rs = $this->executarQuery($sql);

        while($registro = pg_fetch_object($rs)){
            $retorno[] = $registro;
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
    public function pesquisarPorID($id){

        $retorno = new stdClass();

        $sql = "SELECT 
                    bonreoid,
                    bonrebonrecatoid,
                    bonrerepoid,
                    TO_CHAR(bonredt_bonificacao,'MM/YYYY') AS bonredt_bonificacao,
                    bonrevalor_bonificacao,
                    bonreqtd_min_os,
                    bonrestatus,
                    CASE
                        WHEN bonrestatus = 'A' THEN
                            'Aberto'
                        WHEN bonrestatus = 'C' THEN
                            'Cancelado'
                        ELSE
                            'Rateado'
                    END AS status_formatado
                FROM 
                    bonificacao_representante
                WHERE 
                    bonreoid =" . intval( $id ) . "";

        $rs = $this->executarQuery($sql);

        if (pg_num_rows($rs) > 0){
            $retorno = pg_fetch_object($rs);
        }

        return $retorno;
    }

    public function buscarHistorico($id){

        $retorno = array();

        $sql = "SELECT 
                    TO_CHAR(bonrehdt_cadastro,'HH24:MI') AS horario,
                    TO_CHAR(bonrehdt_cadastro,'DD/MM/YYYY') AS data,
                    bonrehmensagem AS mensagem
                FROM 
                    bonificacao_representante_historico
                WHERE 
                    bonrehbonreoid =" . intval( $id ) . "
                ORDER BY
                    bonrehdt_cadastro DESC";

        $rs = $this->executarQuery($sql);

        while($registro = pg_fetch_object($rs)){
            $retorno[] = $registro;
        }

        return $retorno;
    }

    public function inserirHistorico($id, $mensagem) {

        $sql = "INSERT INTO bonificacao_representante_historico (
                    bonrehbonreoid,
                    bonrehmensagem,
                    bonrehdt_cadastro
                ) VALUES (
                    " . intval( $id ) . ",
                    '" . $mensagem . "',
                    NOW()
                )";

        $this->executarQuery($sql);

        return true;
    }

    /**
     * Responsável para inserir um registro no banco de dados.
     * @param stdClass $dados Dados a serem gravados
     * @return boolean
     * @throws ErrorException
     */
    public function inserir(stdClass $dados){

        $sql = "INSERT INTO bonificacao_representante (
                    bonrebonrecatoid,
                    bonrerepoid,
                    bonredt_bonificacao,
                    bonrevalor_bonificacao,
                    bonreqtd_min_os,
                    bonrestatus
                ) VALUES (
                    " . intval( $dados->bonrebonrecatoid ) . ",
                    " . intval( $dados->bonrerepoid ) . ",
                    '01/" . $dados->bonredt_bonificacao . "',
                    " . floatval( $dados->bonrevalor_bonificacao ) . ",
                    " . intval( $dados->bonreqtd_min_os ) . ",
                    'A'
                ) RETURNING bonreoid";

        $rs = $this->executarQuery($sql);

        $dados = pg_fetch_object($rs);

        return $dados->bonreoid;
    }

    /**
     * Responsável por atualizar os registros
     * @param stdClass $dados Dados a serem gravados
     * @return boolean
     * @throws ErrorException
     */
    public function atualizar(stdClass $dados){

        $sql = "UPDATE
                    bonificacao_representante
                SET
                    bonrebonrecatoid = " . intval( $dados->bonrebonrecatoid ) . ",
                    bonrerepoid = " . intval( $dados->bonrerepoid ) . ",
                    bonredt_bonificacao = '01/" . $dados->bonredt_bonificacao . "',
                    bonrevalor_bonificacao = '" . floatval( $dados->bonrevalor_bonificacao ) . "',
                    bonreqtd_min_os = " . intval( $dados->bonreqtd_min_os ) . "
                WHERE 
                    bonreoid = " . $dados->bonreoid . "";

        $this->executarQuery($sql);

        return true;
    }

    /**
     * Exclui (UPDATE) um registro da base de dados.
     * @param int $id Identificador do registro
     * @return boolean
     * @throws ErrorException
     */
    public function excluir($id){

        $sql = "UPDATE
                    bonificacao_representante
                SET
                    bonredt_exclusao = NOW() 
                WHERE
                    bonreoid = " . intval( $id ) . "
                    AND bonredt_exclusao IS NULL";

        $rs = $this->executarQuery($sql);

        if ( pg_affected_rows($rs) < 1 ) {
            return false;
        }

        return true;
    }

    public function cancelarBonificacao($id){

        $sql = "UPDATE
                    bonificacao_representante
                SET
                    bonrestatus = 'C'
                WHERE
                    bonreoid = " . intval( $id ) . "
                    AND bonrestatus != 'C'";

        $rs = $this->executarQuery($sql);

        if ( pg_affected_rows($rs) < 1 ) {
            return false;
        }

        return true;
    }

    public function limparComissoesInstalacao($id) {

        $sql = "UPDATE
                    comissao_instalacao
                SET
                    cmivalor_bonificacao = NULL,
                    cmibonreoid = NULL
                WHERE
                    cmibonreoid = " . intval( $id );

        $rs = $this->executarQuery($sql);

        if ( pg_affected_rows($rs) < 1 ) {
            return false;
        }

        return true;
    }

    public function buscarCategoriasBonificacao() {
        $CategoriaBonificacaoDAO = new CadCategoriaBonificacaoRepresentanteDAO($this->conn);
        return $CategoriaBonificacaoDAO->pesquisar(new stdClass());
    }

    public function buscarRepresentantes() {

        $representantes = array();

        $sql = "SELECT
                    repoid,
                    repnome
                FROM
                    representante
                WHERE
                    repexclusao IS NULL
                ORDER BY
                    repnome";

        $rs = $this->executarQuery($sql);

        if ( pg_num_rows($rs) > 0 ) {
            while( $representante = pg_fetch_object($rs) ) {
                $representantes[] = $representante;
            }
        }

        return $representantes;
    }

    /** Abre a transação */
    public function begin(){
        pg_query($this->conn, 'BEGIN');
    }

    /** Finaliza um transação */
    public function commit(){
        pg_query($this->conn, 'COMMIT');
    }

    /** Aborta uma transação */
    public function rollback(){
        pg_query($this->conn, 'ROLLBACK');
    }

    /** Submete uma query a execucao do SGBD */
    private function executarQuery($query) {

        if(!$rs = pg_query($query)) {
            throw new ErrorException(self::MENSAGEM_ERRO_PROCESSAMENTO);
        }

        return $rs;
    }
}
?>
