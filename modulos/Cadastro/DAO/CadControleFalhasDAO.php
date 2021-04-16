<?php

//error_reporting(0);

class CadControleFalhasDAO {
    
    private $conn;

    function __construct($conn) {    
    	$this->conn = $conn;
        $this->cd_usuario = $_SESSION['usuario']['oid'];;
    }
    
    
    public function buscaEquipamento( $numero_serie = null ){
        
        try{
            
            if ( empty($numero_serie) ){            
                throw new Exception('Parâmetro de pesquisa não informado.');                
            }
            
            $sql = "
                SELECT
                    equoid,
                    equesn,
                    equeveoid
                FROM
                    equipamento
                WHERE
                    equno_serie = $numero_serie
            ";            
            $rsEquipamento = pg_query( $this->conn, $sql );
            
            if (!is_resource($rsEquipamento)){
                throw new Exception('Erro ao pesquisar registro.'); 
            }
            
            return array(
                "error" => false,
                "result" => $rsEquipamento
            );

        }
        catch(Exception $e){
            
            return array(
                "error" => true,
                "message" => $e->getMessage()
            );
        }
    }
    
    
    public function buscaInformacoesEquipamento( $numero_serie = null, $controle_falha_id = null ){
        
        $filtro = "";
        
        try{
            
            if ( empty($numero_serie) ){            
                throw new Exception('Inserir número de serial desejado.');                
            }
            
            if ( empty($controle_falha_id) ){
                $filtro .= " AND hieeqsoid = 19 ";
            }
            
            $sql = "
                SELECT 
                    equoid AS id_equipamento,
                    equno_serie AS numero_serie,
                    ositoid AS item_ordem_servico,
                    osdfdescricao AS defeito_constatado,
                    otcdescricao AS causa,
                    otodescricao AS ocorrencia,
                    otsdescricao AS solucao,
                    otadescricao AS componente,
                    otidescricao AS motivo,
                    otioid,
                    eproid AS modelo_equipamento_id,
                    eprnome AS modelo_equipamento_descricao,
                    to_char(hiedt_historico, 'DD/MM/YYYY') AS data_entrada_laboratorio,
                    (SELECT hieconnumero FROM historico_equipamento WHERE hieconnumero IS NOT NULL AND hieequoid = equoid ORDER BY hiedt_historico DESC LIMIT 1) AS numero_contrato 
                FROM 
                    (
                        SELECT
                            he.hieequoid, 
                            he.hiedt_historico, 
                            he.hieeqsoid, 
                            e.equoid,
                            e.equno_serie,
                            e.equeveoid
                        FROM
                            historico_equipamento AS he 
                            INNER JOIN (SELECT e.equoid, e.equno_serie, e.equeveoid FROM equipamento AS e WHERE e.equno_serie = $numero_serie) AS e ON (e.equoid = he.hieequoid) 
                        WHERE
                            1 = 1
                            $filtro
                        ORDER BY 
                            hiedt_historico DESC 
                        LIMIT 1 
                    ) AS historico 
                    INNER JOIN equipamento_versao ON equeveoid = eveoid
                    INNER JOIN equipamento_projeto ON eveprojeto = eproid                                                         
                    INNER JOIN
                    (
                        SELECT 
                            ordequoid,
                            ordoid,
                            ordconnumero,
                            ordclioid,
                            ordveioid,
                            ordrelroid,
                            orditloid,
                            orddt_ordem,
                            connumero,
                            conmodalidade,
                            condt_ini_vigencia,
                            coneqcoid

                        FROM 
                            ordem_servico 
                            INNER JOIN contrato ON ordconnumero = connumero 
                        WHERE 
                        ordoid IN (
                            SELECT 
                                ordoid

                            FROM
                                contrato 
                            LEFT JOIN (
                                SELECT 
                                    ordequoid,
                                    ordconnumero,
                                    MAX(ordoid) as ordoid
                                FROM 
                                    ordem_servico
                                GROUP BY 
                                    ordequoid, ordconnumero
                            ) as os ON connumero = ordconnumero 
                        )
                    ) AS os ON ordequoid = equoid
                    INNER JOIN ordem_servico_item ON ordoid = ositordoid                    
                    LEFT JOIN ordem_servico_defeito ON ositosdfoid_analisado = osdfoid                        
                    LEFT JOIN os_tipo_causa ON ositotcoid = otcoid
                    LEFT JOIN os_tipo_ocorrencia ON ositotooid = otooid
                    LEFT JOIN os_tipo_solucao ON ositotsoid =  otsoid
                    LEFT JOIN os_tipo_componente_afetado ON ositotaoid = otaoid
                    LEFT JOIN os_tipo_item ON ositotioid = otioid                     
                WHERE 
                    otitipo = 'E'
                    AND otiostoid IN (3, 4)  
                ORDER BY 
                    orddt_ordem DESC
		LIMIT 1 
            ";

            $rsInformacoesEquipamento = pg_query( $this->conn, $sql );
                  
            if (!is_resource($rsInformacoesEquipamento)){
                throw new Exception('Erro ao pesquisar registro.'); 
            }
            
            return array(
                "error" => false,
                "result" => $rsInformacoesEquipamento
            );

        }
        catch(Exception $e){    
            return array(
                "error" => true,
                "message" => $e->getMessage()
            );
        }
        
    }
    
    
    public function buscaDefeitoReincidente( $numero_serie = null ){
        
        try{
            
            if ( empty($numero_serie) ){            
                throw new Exception('Parâmetro de pesquisa não informado.');                
            }
            
            $sql = "
                SELECT 
                    ctfifdoid 
                FROM
                    (
                        SELECT 
                            COUNT(*) AS contador,
                            ctfifdoid
                        FROM 
                            controle_falha 
                        WHERE 
                            ctfno_serie = '$numero_serie' 
                            AND ctfdt_exclusao IS NULL  
                        GROUP BY ctfifdoid
                    ) AS defeitos 
                WHERE contador > 3
            ";
            $rsDefeitosReincidentes = pg_query( $this->conn, $sql );
            
            if (!is_resource($rsDefeitosReincidentes)){
                throw new Exception('Erro ao pesquisar registro.'); 
            }
            
            return array(
                "error" => false,
                "result" => $rsDefeitosReincidentes
            );

        }
        catch(Exception $e){
            
            return array(
                "error" => true,
                "message" => $e->getMessage()
            );
        }
        
        
    }
    
    
    public function buscaAcaoLaboratorio( $filtros = array() ){
        
        $filtro = "";
        
        if (isset($filtros['equno_serie']) && !empty($filtros['equno_serie'])) {
            $filtro .= " AND equno_serie = ".$filtros['equno_serie']." ";
        }
        
        try{
            
            $sql = "
                SELECT 
                    ifaoid,
                    ifadescricao  
                FROM 
                    item_falha_acao 
                    INNER JOIN equipamento_projeto ON eproid = ifaeproid
                    INNER JOIN equipamento_versao ON eveprojeto = eproid
                    INNER JOIN equipamento ON equeveoid = eveoid
                WHERE 
                    ifadt_exclusao IS NULL  
                    $filtro
            ";
            $rsAcaoLaboratorio = pg_query($this->conn, $sql);
            
            return $rsAcaoLaboratorio;
            
        }
        catch(Exception $e){    
            
            return false;
            
        }
    
    }
    
    
    public function buscaComponenteAfetado($filtros = array()){
        
        $filtro = "";
        
        if (isset($filtros['equno_serie']) && !empty($filtros['equno_serie'])) {
            $filtro .= " AND equno_serie = ".$filtros['equno_serie']." ";
        }
        
        try{
            
            $sql = "
                SELECT 
                    ifcoid, 
                    ifcdescricao  
                FROM 
                    item_falha_componente 
                    INNER JOIN equipamento_projeto ON eproid = ifceproid
                    INNER JOIN equipamento_versao ON eveprojeto = eproid
                    INNER JOIN equipamento ON equeveoid = eveoid
                WHERE 
                    ifcdt_exclusao IS NULL  
                    $filtro
            ";
            $rsComponentesAfetados = pg_query($this->conn, $sql);
            
            return $rsComponentesAfetados;
            
        }
        catch(Exception $e){    
            
            return false;
            
        }
    
    }
    
    
    public function buscaDefeitoLaboratorio($filtros = array()){
        
        $filtro = "";
        
        if (isset($filtros['equno_serie']) && !empty($filtros['equno_serie'])) {
            $filtro .= " AND equno_serie = ".$filtros['equno_serie']." ";
        }
        
        try{
            
            $sql = "
                SELECT 
                    ifdoid, 
                    ifddescricao
                FROM 
                    item_falha_defeito 
                    INNER JOIN equipamento_projeto ON eproid = ifdeproid 
                    INNER JOIN equipamento_versao ON eveprojeto = eproid
                    INNER JOIN equipamento ON equeveoid = eveoid
                WHERE 
                    ifddt_exclusao IS NULL 
                    $filtro
            ";
            $rsDefeitosLaboratorio = pg_query($this->conn, $sql);
            
            return $rsDefeitosLaboratorio;
            
        }
        catch(Exception $e){    
            
            return false;
            
        }
    
    }
    
    public function insereFalha(
        $numero_serie,
        $modelo_equipamento,
        $data_entrada_laboratorio,
        $defeito_constatado,
        $acao_laboratorio,
        $componente_afetado,
        $numero_contrato,
        $item_ordem_servico
    ){
        
        try{
            
            $sql = "
                INSERT INTO 
                    controle_falha
                (
                    ctfeproid,
                    ctfno_serie,
                    ctfdt_entrada,
                    ctfifaoid,
                    ctfifcoid,
                    ctfifdoid,
                    ctfdt_cadastro,
                    ctfconnumero,
                    ctfositoid
                )
                VALUES
                (
                    $modelo_equipamento,
                    '$numero_serie',
                    '$data_entrada_laboratorio',
                    $acao_laboratorio,
                    $componente_afetado,         
                    $defeito_constatado,                       
                    NOW(),
                    $numero_contrato,
                    $item_ordem_servico
                )
            ";
            $rsControleFalha = pg_query( $this->conn, $sql );
            
            if (!is_resource($rsControleFalha)){
                throw new Exception('Erro ao inserir registro.'); 
            }
            
            return array(
                "error" => false,
                "message" => utf8_encode("Registro incluído.")
            );

        }
        catch(Exception $e){    
            
            return array(
                "error" => true,
                "message" => $e->getMessage()
            );
        }         
    }
    
    
       
    public function editaFalha(
        $controle_falhas_id,       
        $defeito_constatado,
        $acao_laboratorio,
        $componente_afetado  
    ){
       
        try{
            
            $sql = "
               UPDATE
                    controle_falha
                SET
                    ctfifaoid = $acao_laboratorio,
                    ctfifcoid = $componente_afetado,
                    ctfifdoid = $defeito_constatado
                WHERE
                    ctfoid = $controle_falhas_id
            ";
            $rsControleFalha = pg_query( $this->conn, $sql );
            
            if (!is_resource($rsControleFalha)){
                throw new Exception('Erro ao alterar registro.'); 
            }
            
            return array(
                "error" => false,
                "message" => "Registro alterado."
            );

        }
        catch(Exception $e){    
            
            return array(
                "error" => true,
                "message" => $e->getMessage()
            );
        }       
    }
    
    
    
    public function buscaHistoricoFalha( $filtros = array() ){
        
        $filtro = "";
        
        if (isset($filtros['numero_serie']) && !empty($filtros['numero_serie'])) {
            $filtro .= " AND ctfno_serie = '".$filtros['numero_serie']."' ";
        }
        
        try{
            
            $sql = "
                SELECT 
                    ctfoid AS controle_falhas_id,
                    ctfno_serie AS numero_serie,
                    eprnome AS modelo_equipamento,
                    to_char(ctfdt_entrada, 'DD/MM/YYYY') AS data_entrada_laboratorio,
                    ifadescricao AS acao_laboratorio,
                    ifcdescricao AS componente_afetado_laboratorio,
                    ifddescricao AS defeito_laboratorio	
                FROM 
                    controle_falha
                    INNER JOIN equipamento ON equno_serie = ctfno_serie::bigint
                    INNER JOIN equipamento_versao ON equeveoid = eveoid
                    INNER JOIN equipamento_projeto ON eveprojeto = eproid 
                    INNER JOIN item_falha_acao ON ctfifaoid = ifaoid
                    INNER JOIN item_falha_componente ON ctfifcoid = ifcoid
                    INNER JOIN item_falha_defeito ON ctfifdoid = ifdoid
                WHERE 
                    ctfdt_exclusao IS NULL
                    $filtro
                ORDER BY 
                    ctfdt_entrada,
                    ctfoid
            ";
            $rsHistoricoFalha = pg_query( $this->conn, $sql );
            
            if (!is_resource($rsHistoricoFalha)){
                throw new Exception('Erro ao pesquisar registro.'); 
            }
            
            return array(
                "error" => false,
                "result" => $rsHistoricoFalha
            );

        }
        catch(Exception $e){    
            return array(
                "error" => true,
                "message" => $e->getMessage()
            );
        }       
    }    
    
    
    public function buscaUltimaDataFalha( $numero_serie ){
        
        
        try{
            
            $sql = "
                SELECT 
                     to_char(MAX(ctfdt_entrada), 'DD/MM/YYYY') AS ultima_data_entrada_laboratorio
                FROM 
                    controle_falha                    
                WHERE 
                    ctfdt_exclusao IS NULL
                    AND ctfno_serie = '$numero_serie'
                GROUP BY
                    ctfno_serie
            ";

            $rsUltimaDataFalha = pg_query( $this->conn, $sql );
            
            if (!is_resource($rsUltimaDataFalha)){
                throw new Exception('Erro ao pesquisar registro.'); 
            }
            
            return array(
                "error" => false,
                "result" => $rsUltimaDataFalha
            );

        }
        catch(Exception $e){    
            return array(
                "error" => true,
                "message" => $e->getMessage()
            );
        }       
    }
    
    
    public function excluiHistoricoFalha( $ctfoid ){
        
        try{
            
            $sql = "
                UPDATE
                    controle_falha
                SET
                    ctfdt_exclusao = NOW()
                WHERE    
                    ctfoid = $ctfoid
            ";
            $rsHistoricoFalha = pg_query( $this->conn, $sql );
            
            if (!is_resource($rsHistoricoFalha)){
                throw new Exception('Erro ao inserir registro.'); 
            }
            
            return array(
                "error" => false,
                "result" => $rsHistoricoFalha
            );
            
        }
        catch(Exception $e){    
            return array(
                "error" => true,
                "message" => $e->getMessage()
            );
        }
        
    }
    
    
    public function buscaStatusEquipamento( $equno_serie = null ){
        
        $filtro = "";
        
        if ( !isset($equno_serie) || empty($equno_serie) ) {
            throw new Exception('Parâmetro "Número de série" não informado.');
        }
        
        try{
        
            $sql = "
                SELECT
                    equoid AS equipamento_id, 
                    equno_serie AS equipamento_numero_serie,                    
                    hieeqsoid AS equipamento_status_id,                    
                    hiemotivo AS historico_motivo,
                    hiedt_historico AS historico_ultima_data
                FROM
                    equipamento
                    INNER JOIN historico_equipamento ON equoid = hieequoid                    
                WHERE
                    equno_serie = $equno_serie
                    AND hieeqsoid > 0
                ORDER BY 
                    hiedt_historico DESC 
                LIMIT 1 
            ";
            
            $rsStatusEquipamento = pg_query( $this->conn, $sql );
            
            if (!is_resource($rsStatusEquipamento)){
                throw new Exception('Erro ao pesquisar registro.'); 
            }
            
            return array(
                "error" => false,
                "result" => $rsStatusEquipamento
            );
            
        }
        catch(Exception $e){ 
            
            return array(
                "error" => true,
                "message" => $e->getMessage()
            );
        } 
    }
    
    
    /*
     * Busca informações do controle de falha a partir do ID.
     */
    public function buscaControleFalhas( $controle_falha_id = null ){
    
        try{
            
            if ( empty($controle_falha_id) ){
                throw new Exception('Parâmetro de pesquisa não informado.'); 
            }
            
            $sql = "
                SELECT
                    ctfifaoid AS acao_laboratorio,
                    ctfifcoid AS componente_afetado_laboratorio,
                    ctfifdoid AS defeito_laboratorio
                FROM
                    controle_falha
                WHERE
                    ctfoid = $controle_falha_id
            ";
            
            $rsControleFalha = pg_query( $this->conn, $sql );
            
            if (!is_resource($rsControleFalha)){
                throw new Exception('Erro ao pesquisar registro.'); 
            }
            
            return array(
                "error" => false,
                "result" => $rsControleFalha
            );

        }
        catch(Exception $e){  
            
            return array(
                "error" => true,
                "message" => $e->getMessage()
            );
        }      
    }

    
    public function alteraStatusEquipamento( $equipamento_id = null, $equipamento_status = null, $equipamento_esn = null, $equipamento_versao = null ){
        
        try{
            
            $sql = "
               UPDATE
                    equipamento
                SET
                    equeqsoid = $equipamento_status
                WHERE
                    equoid = $equipamento_id
            ";
            $rsEquipamento = pg_query( $this->conn, $sql );
            
            if (!is_resource($rsEquipamento)){
                throw new Exception('Erro ao alterar o status do equipamento.'); 
            }
            
            $sql = "
               INSERT INTO
                    historico_equipamento
                (
                    hieequoid,
                    hieeqsoid,
                    hieusuoid,
                    hiedt_historico,
                    hiemotivo,
                    hieesn,
                    hieeveoid
                )
                VALUES
                (
                    $equipamento_id,
                    $equipamento_status,
                    $this->cd_usuario,
                    NOW(),
                    'Controle de Defeitos',
                    $equipamento_esn,
                    $equipamento_versao
                )

            ";
            $rsHistoricoEquipamento = pg_query( $this->conn, $sql );
            
            if (!is_resource($rsHistoricoEquipamento)){
                throw new Exception('Erro ao alterar o status do equipamento.'); 
            }
            
            return array(
                "error" => false
            );

        }
        catch(Exception $e){    
            
            return array(
                "error" => true,
                "message" => $e->getMessage()
            );
        }   
        
    }
    
    
}
?>