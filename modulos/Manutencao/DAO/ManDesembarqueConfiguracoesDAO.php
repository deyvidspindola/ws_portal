<?php

/**
 * Classe ManDesembarqueConfiguracoesDAO.
 * Camada de modelagem de dados.
 *
 * @package  Cadastro
 * @author   CÁSSIO VINÍCIUS LEGUIZAMON BUENO <cassio.bueno.ext@sascar.com.br>
 *
 */
class ManDesembarqueConfiguracoesDAO {

    /** Conexão com o banco de dados */
    private $conn;

    /** Usuario logado */
    private $usarioLogado;

    const MENSAGEM_ERRO_PROCESSAMENTO = "Houve um erro no processamento dos dados.";

    public function __construct($conn) {

        //Seta a conexao na classe
        $this->conn = $conn;
        $this->usarioLogado = isset($_SESSION['usuario']['oid']) ? $_SESSION['usuario']['oid'] : '';

        //Se nao tiver nada na sessao assume usuario AUTOMATICO
        if(empty($this->usarioLogado)) {
            $this->usarioLogado = 2750;
        }
    }

    /** retorna o id do veículo */
    public function getVeiculoIdDAO($idEquipamento){
        $sql = "SELECT
                    conveioid
                FROM
                    contrato
                WHERE
                    conequoid = $idEquipamento
                LIMIT 1";

        $rs = $this->executarQuery($sql);
        $registro = pg_fetch_object($rs);
        return $registro ? $registro->conveioid : false;
    }

    public function getLogComandosEquipamentoDAO($serialEquipamento, $limit = 1){

        global $dbstringComandos;

        $retorno = array();

        $sql = "SELECT
                    logcoid,
                    logcdt_validade,
                    logccomando_enviado
                FROM
                    log_comando
                WHERE
                    logcesn = $serialEquipamento
                ORDER BY
                    logcdt_envio
                DESC LIMIT
                    $limit";

        $conn_comandos = pg_connect($dbstringComandos);
        $rs = pg_query($conn_comandos, $sql);
        while($registro = pg_fetch_object($rs)){
            $retorno[] = array(
                'id' => $registro->logcoid,
                'validade' => $registro->logcdt_validade,
                'comando' => $registro->logccomando_enviado
            );
        }

        return $retorno;
    }

    /** retorna a classe do Equipamento */
    public function getClasseEquipamentoDAO($idEquipamento){
        
        $retorno = array();
        $sql = "SELECT 
                    epcveqcoid
                FROM 
                    equipamento_projeto_classe_versao 
                INNER JOIN equipamento ON equeveoid = epcveveoid 
                WHERE 
                    equoid = $idEquipamento
                LIMIT 1 ";
                
        $rs = $this->executarQuery($sql);
        while($registro = pg_fetch_object($rs)){
            $retorno = $registro;
        }
        return $retorno->epcveqcoid;
    }

    /** retorna o tipo de equipamento */
    public function getTipoEquipamentoDAO($classeEquipamento, $idEquipamento){
        
        $retorno = array();
        $sql = "SELECT 
                    /*epcvoid, eprnome, eqcdescricao, eveversao, epcveqcoid,*/
                    eprnome as tipoequipamento                    
                FROM 
                    equipamento_projeto_classe_versao, 
                    equipamento_classe, 
                    equipamento_versao, 
                    equipamento_projeto, 
                    equipamento
                WHERE   
                    epcveqcoid = eqcoid AND 
                    epcveproid = eproid AND 
                    epcveveoid = eveoid AND 
                    epcvdt_exclusao IS NULL AND
                    equeveoid = epcveveoid AND
                    epcveqcoid = $classeEquipamento AND 
                    equoid = $idEquipamento
                ORDER BY 
                    eprnome, eqcdescricao, eveversao
                LIMIT 1";

        $rs = $this->executarQuery($sql);
        while($registro = pg_fetch_object($rs)){
            $retorno = $registro;
        }        

        return $retorno->tipoequipamento;
    }

    /** Retorna numero de serie do equipamento */    
    public function getNumeroSerieEquipamentoDAO( $idEquipamento ){

        $retorno = array();
        $sql = "SELECT 
                    equesn
                FROM 
                    equipamento
                WHERE                       
                    equoid = $idEquipamento                
                LIMIT 1";

        $rs = $this->executarQuery($sql);
        while($registro = pg_fetch_object($rs)){
            $retorno = $registro;
        }

        return $retorno->equesn;
    }

    public function getProjetoEquipamentoDAO ($idEquipamento, $classeEquipamento ){
        $retorno = array();
        $sql = "SELECT 
                    /*epcvoid, eprnome, eqcdescricao, eveversao, epcveqcoid,*/
                    eproid
                FROM 
                    equipamento_projeto_classe_versao, 
                    equipamento_classe, 
                    equipamento_versao, 
                    equipamento_projeto, 
                    equipamento
                WHERE   
                    epcveqcoid = eqcoid AND 
                    epcveproid = eproid AND 
                    epcveveoid = eveoid AND 
                    epcvdt_exclusao IS NULL AND
                    equeveoid = epcveveoid AND
                    epcveqcoid = $classeEquipamento AND 
                    equoid = $idEquipamento
                ORDER BY 
                    eprnome, eqcdescricao, eveversao
                ";

        $rs = $this->executarQuery($sql);
        while($registro = pg_fetch_object($rs)){
            $retorno = $registro;
        }

        return $retorno->eproid;        
    }

    /**
     * Retorna o proprietário do rotograma embarcado
     */
    public function getProprietarioLayoutRotogramaDAO($idVeiculo, $idGerenciadora = null){

        global $dbstring_gerenciadora;

        $retorno = array();

        $sql = "SELECT
                    embarque.*
                FROM rotograma_embarque as embarque
                INNER JOIN rotograma_layout AS layout ON layout.rloid = embarque.rerloid
                WHERE 1=1
                    AND embarque.redt_desembarque IS NULL
                    AND embarque.reveioid = $idVeiculo";

        if(!empty($idGerenciadora)){
            $sql .= " AND layout.rlgeroid = $idGerenciadora";
        }

        $conn_gerenciadora = pg_connect($dbstring_gerenciadora);
        $rs = pg_query($conn_gerenciadora, $sql);
        while($registro = pg_fetch_object($rs)){
            $retorno = $registro;
        }

        return $retorno;
    }

    /**
     * Retorna o proprietário do layout sequenciamento de macro embarcado
     */
    public function getProprietarioLayoutSequenciamentoDAO($idVeiculo, $idGerenciadora = null){

        global $dbstring_gerenciadora;

        $retorno = array();
        $sql = "SELECT
                    CadastroSEQ.sqmoid,
                    CadastroSEQ.sqmnome,
                    CadastroSEQ.sqmdescricao,
                    CadastroSEQ.sqmclioid,
                    CadastroSEQ.sqmgeroid,
                    SEQEmbarcada.sqmclioid,
                    SEQEmbarcada.sqmgeroid,
                    SEQEmbarcada.sqmcadastro,
                    SEQEmbarcada.sqmalteracao,
                    SEQEmbarcada.sqmcriacao_embarque,
                    SEQEmbarcada.sqmexclusao
                FROM sequenciamento_macros AS CadastroSEQ
                INNER JOIN sequenciamento_macros_virtual AS SEQEmbarcada ON SEQEmbarcada.sqmoid = CadastroSEQ.sqmoid
                WHERE 1=1
                    AND SEQEmbarcada.sqmveioid = $idVeiculo";

        if(!empty($idGerenciadora)){
            $sql .= " AND SEQEmbarcada.sqmgeroid = $idGerenciadora";
        }

        $conn_gerenciadora = pg_connect($dbstring_gerenciadora);
        $rs = pg_query($conn_gerenciadora, $sql);
        while($registro = pg_fetch_object($rs)){
            $retorno = $registro;
        }

        return $retorno;
    }

    /**
     * Retorna o proprietário do layout ação embarcado
     */
    public function getProprietarioLayoutAcaoDAO($idVeiculo, $idGerenciadora = null){

        global $dbstring_gerenciadora;

        $retorno = array();
        $sql = "SELECT
                    AcaoEmbarcada.*
                FROM layout_acao_embarcada_mtc600 AS CadastroAcao
                INNER JOIN layout_acao_embarcada_mtc600_virtual AS AcaoEmbarcada ON AcaoEmbarcada.laemoid = CadastroAcao.laemoid
                WHERE 1=1
                    AND AcaoEmbarcada.veioid = $idVeiculo";

        if(!empty($idGerenciadora)){
            $sql .= " AND CadastroAcao.laemgeroid = $idGerenciadora";
        }

        $conn_gerenciadora = pg_connect($dbstring_gerenciadora);

        $rs = pg_query($conn_gerenciadora, $sql);
        while($registro = pg_fetch_object($rs)){
            $retorno = $registro;
        }

        return $retorno;
    }

    /**
     * Retorna linhas com os ids dos projetos válidos
     */
    public function getProjetosValidosDAO(){

        $retorno = array();

        $sql = "SELECT
                    v.valvalor
                FROM dominio AS d
                INNER JOIN registro AS r ON r.regdomoid = d.domoid
                INNER JOIN valor AS v ON r.regoid = v.valregoid
                WHERE r.regoid in (SELECT valregoid FROM valor WHERE valtpvoid = 6 AND valvalor in ('ID_PROJETO_MTC_EMBARQUE_ACAO','ID_PROJETO_MTC_EMBARQUE_MACRO','ID_PROJETO_MTC_EMBARQUE_SEQUENCIAMENTO','ID_PROJETO_LMU_SEQUENCIAMENTO','ID_PROJETO_LMU_EMBARQUE_ROTOGRAMA'))
                AND valtpvoid = 8
                ORDER BY v.valregoid, v.valoid";

        $rs = $this->executarQuery($sql);

        while($registro = pg_fetch_object($rs)){
            $retorno[] = $registro;
        }

        return $retorno;

    }

    public function getComandoProjetoDAO($idProjeto, $acao){

        $retorno = array();

        $sql = "SELECT v.* 
                FROM dominio AS d
                    INNER JOIN registro AS r ON r.regdomoid = d.domoid
                    INNER JOIN valor AS v ON r.regoid = v.valregoid
                WHERE
                    d.domoid = 35
                    AND valtpvoid = 3
                    AND valregoid IN (
                        SELECT
                            v.valregoid
                        FROM dominio AS d
                            INNER JOIN registro AS r ON r.regdomoid = d.domoid
                            INNER JOIN valor AS v ON r.regoid = v.valregoid
                        WHERE
                            d.domoid = 35
                            AND v.valvalor LIKE '$idProjeto' OR v.valvalor LIKE '%;$idProjeto;%' OR v.valvalor LIKE '%;$idProjeto' OR v.valvalor LIKE '$idProjeto;%'
                    )
                    AND valregoid IN (
                        SELECT
                         v.valregoid
                        FROM dominio AS d
                            INNER JOIN registro AS r ON r.regdomoid = d.domoid
                            INNER JOIN valor AS v ON r.regoid = v.valregoid
                        WHERE
                         d.domoid = 35
                         AND v.valvalor ILIKE '%_$acao'
                    )";

        $rs = $this->executarQuery($sql);

        while($registro = pg_fetch_object($rs)){
            // $retorno[] = $registro;
            $retorno[] = array(
                'comando' => $registro->valvalor,
                'layout' => $acao
            );
        }

        return $retorno;

    }

    /**
     * Retorna o proprietário do layout macro embarcado
     */
    public function getProprietarioLayoutMacroDAO($idVeiculo, $idGerenciadora = null){

        global $dbstring_gerenciadora;

        $retorno = array();
        
        $sql = "SELECT
                    *
                FROM layout_teclado_td50
                WHERE 1=1
                    AND lttdoid IN (
                        SELECT DISTINCT
                            metdlamtlttdoid
                        FROM layout_embarcado_td50_veiculo
                        WHERE
                            levveioid = $idVeiculo";

        if(!empty($idGerenciadora)){
            $sql .= " AND lttdgeroid = $idGerenciadora";
        }

        $sql .= ")";

        $conn_gerenciadora = pg_connect($dbstring_gerenciadora);
        $rs = pg_query($conn_gerenciadora, $sql);
        while($registro = pg_fetch_object($rs)){
            $retorno = $registro;
        }

        return $retorno;
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

    /** 
     * Submete uma query a execucao do SGBD
     * @param  [string] $query
     * @return [bool]
     */
    private function executarQuery($query) {

        if(!$rs = pg_query($this->conn, $query)) {
            throw new ErrorException(self::MENSAGEM_ERRO_PROCESSAMENTO);
        }

        return $rs;
    }

    /**
     * cria ponto de salvamento
     * @param  $nome [alias para o savepoint]
     */
    public function savePoint($nome){
        pg_query($this->conn, 'SAVEPOINT ' . $nome);
    }

     /**
     * Aborta ações dentro de um bloco de ponto de salvamento
     * @param  $nome [alias para do savepoint]
     */
    public function rollbackPoint($nome){
        pg_query($this->conn, 'ROLLBACK TO SAVEPOINT ' . $nome);
    }
}
?>
