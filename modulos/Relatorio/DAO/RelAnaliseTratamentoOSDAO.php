<?php

/**
 * Classe RelAnaliseTratamentoOSDAO.
 * Camada de modelagem de dados.
 *
 * @package  Relatorio
 * @author   Robson Aparecido Trizotte da Silva <robson.silva@meta.com.br>
 *
 */
class RelAnaliseTratamentoOSDAO {

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

    /**
     * Mensagem de erro para tabela inexistente.
     * @const String
     */
    const MENSAGEM_ERRO_TABELA = "O Relatório não pode ser gerado no momento, pois os dados necessários para a pesquisa estão sendo atualizados em nosso servidor.";

    public function __construct($conn) {
        //Seta a conexão na classe
        $this->conn = $conn;
    }

    /**
     * Método que verifica a existência de uma tabela.
     *
     * @param String $tabela Nome da tabela.
     *
     * @return Boolean
     * @throws ErrorException
     */
    private function verificarTabela($tabela = '') {
        $sql = "
            SELECT
                EXISTS (
                    SELECT
                        1
                    FROM
                        pg_tables
                    WHERE
                        tablename = '$tabela'
                ) AS existe";

        if (!$rs = pg_query($this->conn, $sql)) {
            throw new ErrorException(self::MENSAGEM_ERRO_PROCESSAMENTO);
        }

        $registro = pg_fetch_object($rs);

        if ($registro->existe != 't') {
            return false;
        }

        return true;
    }

    /**
     * Método que monta parte do SQL de pesquisa.
     *
     * @param stdClass $parametros
     *
     * @return string
     */
    private function montarSqlPesquisar(stdClass $parametros) {
        $sql = "
            SELECT DISTINCT
                clinome,
                veiplaca,
                eprnome,
                conmodalidade,
                conveioid,
                tpcdescricao,
                osdfdescricao,
                ordoid,
                eproid,
                ossdescricao,
                TO_CHAR(veipdata, 'dd/mm/yyyy HH24:MI:SS') AS veipdata_atual,
                veipdata AS veipdata_atual_ordenacao,

                TO_CHAR(orddt_ordem, 'dd/mm/yyyy HH24:MI:SS') AS orddt_ordem,
                orddt_ordem AS orddt_ordem_ordenacao,

                TO_CHAR((SELECT MAX(osadata) FROM ordem_servico_agenda WHERE osaordoid = ordoid AND osaexclusao IS NULL ), 'dd/mm/yyyy') AS osdata,
                (SELECT MAX(osadata) FROM ordem_servico_agenda WHERE osaordoid = ordoid AND osaexclusao IS NULL ) AS osdata_ordenacao,

                acao.aoamdescricao   AS aoamdescricao_acao,
                motivo.aoamdescricao AS aoamdescricao_motivo,
                CASE 
                    WHEN TRIM(osdfdescricao) ILIKE 'Posi__o Congelada' THEN 1 
                    ELSE 0 END 
                AS posicao_congelada
            FROM
                contrato
                    INNER JOIN
                        clientes ON clioid = conclioid
                    INNER JOIN
                        ordem_servico ON
                            (
                                    ordconnumero = connumero
                                AND
                                    ordstatus IN (1,2,4,8)
                            )
                    INNER JOIN
                        ordem_servico_status ON ossoid = ordstatus
                    INNER JOIN
                        tipo_contrato ON tpcoid = conno_tipo
                    INNER JOIN
                        ordem_servico_item ON
                            (
                                    ositordoid = ordoid
                                AND
                                    ositstatus <> 'X'
                                AND
                                    ositexclusao IS NULL
                            )
                    INNER JOIN
                        os_tipo_item ON
                            (
                                    otioid = ositotioid
                                AND
                                    otiostoid = 4
                            )
                    INNER JOIN
                        equipamento ON equoid = conequoid
                    INNER JOIN
                        equipamento_versao ON eveoid = equeveoid
                    INNER JOIN
                        equipamento_projeto ON eproid = eveprojeto
                    INNER JOIN
                        veiculo ON veioid = conveioid
                    INNER JOIN
                        veiculo_posicao ON 
                            (
                                    veipveioid = veioid
                                AND veipgps = true
                            )
                    INNER JOIN
                        ordem_servico_defeito ON
                            (
                                    osdfoid = ositosdfoid_alegado
                                AND
                                    osdfexclusao IS NULL
                                AND
                                    osdfotioid = otioid
                                AND (
                                        ( TRIM(osdfdescricao) ILIKE 'N_o Atualiza Localiza__o' AND veipdata > orddt_ordem )
                                        OR
                                        ( TRIM(osdfdescricao) ILIKE 'Sem Contato GPRS e Satelital' AND veipdata > orddt_ordem )
                                        OR
                                        TRIM(osdfdescricao) ILIKE 'Posi__o Congelada'
                                    )    
                            )
                    LEFT JOIN
                        analise_os_tratamento ON aotordoid = ordoid
                    LEFT JOIN
                        analise_os_acao_motivo AS acao ON acao.aoamoid = aotaoamoid_acao
                    LEFT JOIN
                        analise_os_acao_motivo AS motivo ON motivo.aoamoid = aotaoamoid_motivo
                    LEFT JOIN
                        usuarios ON cd_usuario = aotusuoid_cadastro
            WHERE
                condt_exclusao IS NULL
        ";

        if (!empty($parametros->data_inicial) && !empty($parametros->data_final)) {
            $sql .= "
                AND
                    veipdata BETWEEN '".$parametros->data_inicial." 00:00:00' AND '".$parametros->data_final." 23:59:59'
            ";
        }

        if (isset($parametros->aoamoid_acao) && trim($parametros->aoamoid_acao) != '') {
            $sql .= "
                AND
                    acao.aoamoid = ".intval($parametros->aoamoid_acao)."
            ";
        }

        if (isset($parametros->aoamoid_motivo) && trim($parametros->aoamoid_motivo) != '') {
            $sql .= "
                AND
                    motivo.aoamoid = ".intval($parametros->aoamoid_motivo)."
            ";
        }

        return $sql;
    }

    /**
     * Método que pesquisa os registros.
     *
     * @param stdClass $parametros Filtros da pesquisa.
     *
     * @return Integer
     * @throws ErrorException
     */
    public function pesquisarQuantidade(stdClass $parametros) {
        $sql = "
            SELECT
                COUNT(1) AS quantidade
            FROM
                (
                    ".$this->montarSqlPesquisar($parametros)."
                ) AS registros
        ";

        if (!$rs = pg_query($this->conn, $sql)) {
            throw new ErrorException(self::MENSAGEM_ERRO_PROCESSAMENTO);
        }

        $registro = pg_fetch_object($rs);

        return $registro->quantidade;
    }

    /**
     * Método que pesquisa os registros.
     *
     * @param stdClass $parametros Filtros da pesquisa.
     * @param stdClass $paginacao Limite e offset.
     * @param stdClass $ordenacao Campos para ordenação.
     *
     * @return Integer
     * @throws ErrorException
     */
    public function pesquisar(stdClass $parametros, $paginacao = null, $ordenacao = 'osdata') {

        $retorno = array();

        if (!$this->verificarTabela('veiculo_posicao')) {
            throw new Exception(self::MENSAGEM_ERRO_TABELA);
        }

        if (empty($ordenacao)) {
            $ordenacao = 'osdata';
        }


        $sql = $this->montarSqlPesquisar($parametros);

        if (strpos($ordenacao, 'veipdata_os') === false){
            $sql .= "
                ORDER BY
                    ".$ordenacao.", ordoid
            ";
        }

        if (isset($paginacao->limite) && isset($paginacao->offset)) {
            $sql.= "
                LIMIT
                    ".intval($paginacao->limite)."
                OFFSET
                    ".intval($paginacao->offset)."
            ";
        }

        if (!$rs = pg_query($this->conn, $sql)) {

            throw new ErrorException(self::MENSAGEM_ERRO_PROCESSAMENTO);
        }

        $qtdRetirada = 0;
        
        while ($row = pg_fetch_object($rs)) {

            /*
                MANTIS 3878
                Para evitar time out na pesquisa foram removidos os WS's contidos nos seguintes metodos:
                
                buscarUltimaLocalizaoDataVeiculo
                verificaGPSValido
            */
            
            /*
            //Pega a ultima posição do veiculo
            $row->veipdata_os = $this->buscarUltimaLocalizaoDataVeiculo($row->conveioid, $row->orddt_ordem);
                
            $exibirLinha = true;
            
            if ($row->posicao_congelada == 1){
                //Só irá exibir o registro se o GPS for válido.
                if (!$this->verificaGPSValido($row->conveioid)){
                    $exibirLinha = false;
                    $qtdRetirada++;
                }
            }
            if ($exibirLinha){
                $retorno[] = $row;
            }
            
            */
            
            $row->veipdata_os = $row->veipdata_atual;

            //Modalidade do contrato
            $row->conmodalidade = ($row->conmodalidade == 'L') ? 'Locação' : 'Revenda';
            
            $retorno[] = $row;
        }

        //Ordena com base no retorno do WS
        if (strpos($ordenacao, 'veipdata_os') !== false){
            $this->orientacao = (strpos($ordenacao, 'DESC') !== false) ? 'DESC' : 'ASC';
            usort($retorno, array($this, 'comparaDataPosicaoOS') );
        }
        
        $objRetorno = new stdClass();
        $objRetorno->dados = $retorno;
        $objRetorno->quantidadeLinhasRetiradas = $qtdRetirada;
        
        return $objRetorno;
    }
    /**
     * Compara a data posição OS
     *
     *
     */
    private function comparaDataPosicaoOS($a, $b){

        //Verifica se os objetos estão preenchidos (Ordena as colunas em branco para o fim)
        if (empty($a->veipdata_os)){
            return 1;
        }

        if (empty($b->veipdata_os)){
            return -1;
        }

        //Separa a data da hora
        $datas1 = explode(" ", $a->veipdata_os);
        //Separa a data
        $data1 = explode("/", $datas1[0]);
        //Transforma a data em padrão americano.
        $data1 = $data1[2].'-'.$data1[1].'-'.$data1[0]. ' ' . $datas1[1];
        //Converte a data em tempo
        $time1 = strtotime($data1);

        //Separa a data da hora
        $datas2 = explode(" ", $b->veipdata_os);
        //Separa a data
        $data2 = explode("/", $datas2[0]);
        //Transforma a data em padrão americano.
        $data2 = $data2[2].'-'.$data2[1].'-'.$data2[0]. ' ' . $datas2[1];
        //Converte a data em tempo
        $time2 = strtotime($data2);

        if ($time1 == $time2){
            return 0;
        }

        //Verifica a orientação dos dados
        if (isset($this->orientacao) && $this->orientacao == 'DESC'){
            return ($time1 < $time2) ? 1 : -1;
        }
        return ($time1 < $time2) ? -1 : 1;
    }


 /**
     * 
     * @param type $veiculo
     * @return boolean
     */
    private function verificaGPSValido($veiculo){
        
        if ($_SESSION['servidor_teste']){
            $urlWebService = _PROTOCOLO_ . "sasweb-services-homolog.sascar.com.br/unificado_backend/posicao/obterUltimaPosicaoCentral/";
        } else {
            $urlWebService = _PROTOCOLO_ . "sasweb-services.sascar.com.br/unificado_backend/posicao/obterUltimaPosicaoCentral/";
        }
        
        if (!empty($veiculo)){
            $urlWebService .= $veiculo;           
        } else {
            return true;
        }
        

        $url = file_get_contents($urlWebService);
        if ($url !== false){
            $xmlobj = simplexml_load_string($url);
            return $xmlobj->gpsValido == 1;
        }

         return true;
    }

    /**
     * Método para realizar a pesquisa analitica de varios registros
     * @param stdClass $parametros Filtros da pesquisa
     * @return array
     * @throws ErrorException
     */
    public function pesquisarAnalitico(stdClass $parametros) {

    	$retorno = array();

       //Consulta do relatório
       $sql = "
            SELECT 
                clinome,
                veiplaca,
                acao.aoamdescricao   AS aoamdescricao_acao,
				acao.aoamoid AS aoamoid,
                motivo.aoamdescricao AS aoamdescricao_motivo,
        		ds_login,
        		TO_CHAR(aotdt_cadastro, 'dd/mm/yyyy') AS aotdt_cadastro,
       			eprnome
            FROM
                analise_os_tratamento
            INNER JOIN
                analise_os_acao_motivo AS acao ON acao.aoamoid = aotaoamoid_acao
            INNER JOIN
                analise_os_acao_motivo AS motivo ON motivo.aoamoid = aotaoamoid_motivo
       		INNER JOIN
                ordem_servico ON ordoid = aotordoid
       		INNER JOIN
       			contrato ON connumero = ordconnumero
       		INNER JOIN
                equipamento ON equoid = conequoid
            INNER JOIN
                equipamento_versao ON eveoid = equeveoid
            INNER JOIN
                equipamento_projeto ON eproid = eveprojeto
       		INNER JOIN
                clientes ON clioid = conclioid
            INNER JOIN
                veiculo ON veioid = conveioid
            LEFT JOIN
                usuarios ON cd_usuario = aotusuoid_cadastro
        	WHERE
                1=1
        ";


        if (( isset($parametros->data_inicial) && !empty($parametros->data_inicial) ) &&
                ( isset($parametros->data_final) && !empty($parametros->data_final) )) {

            $sql .= "AND
                            (aotdt_cadastro BETWEEN '" . $parametros->data_inicial . " 00:00:00' AND '" . $parametros->data_final . " 23:59:59')";
        }

        if (isset($parametros->aoamoid_acao) && trim($parametros->aoamoid_acao) != '') {

            $sql .= "AND
                            acao.aoamoid = " . intval($parametros->aoamoid_acao) . "";
        }
        if (isset($parametros->aoamoid_motivo) && trim($parametros->aoamoid_motivo) != '') {

            $sql .= "AND
                            motivo.aoamoid = " . intval($parametros->aoamoid_motivo) . "";
        }

        if (isset($parametros->cd_usuario) && trim($parametros->cd_usuario) != '') {

            $sql .= "AND
                            aotusuoid_cadastro = " . intval($parametros->cd_usuario) . "";
        }

        if (isset($parametros->clinome) && trim($parametros->clinome) != '') {

            $sql .= "AND
                            clinome ILIKE '".$parametros->clinome."%' ";
        }

        if (isset($parametros->tpcoid) && trim($parametros->tpcoid) != '') {

        	$sql .= "AND
                            conno_tipo = " . intval($parametros->tpcoid) . "";
        }

        $sql .= " ORDER BY aotdt_cadastro, clinome ";

        if (!$rs = pg_query($this->conn, $sql)) {
            throw new ErrorException(self::MENSAGEM_ERRO_PROCESSAMENTO);
        }

        while ($row = pg_fetch_object($rs)) {

            $retorno[] = $row;
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
    public function pesquisarPorID( $id) {

        $retorno = new stdClass();

        $sql = "
            SELECT DISTINCT
        		connumero,
                ordoid,
        		veiplaca,
                osdfdescricao,
                acao.aoamdescricao   AS aoamdescricao_acao,
                motivo.aoamdescricao AS aoamdescricao_motivo,
        		ordstatus,
                veioid
            FROM
                contrato
            INNER JOIN
                clientes ON clioid = conclioid
            INNER JOIN
                ordem_servico ON (ordconnumero = connumero AND ordstatus IN (1,2,4,8))
            INNER JOIN
                ordem_servico_status ON ossoid = ordstatus
            INNER JOIN
                tipo_contrato ON tpcoid = conno_tipo
            INNER JOIN
                ordem_servico_item ON (ositordoid = ordoid AND ositstatus <> 'X' AND ositexclusao IS NULL)
            INNER JOIN
                os_tipo_item ON (otioid = ositotioid AND otiostoid = 4)
            INNER JOIN
                ordem_servico_defeito ON osdfoid = ositosdfoid_alegado
            AND
                osdfexclusao IS NULL
            AND
                osdfotioid = otioid
            INNER JOIN
                equipamento ON equoid = conequoid
            INNER JOIN
                equipamento_versao ON eveoid = equeveoid
            INNER JOIN
                equipamento_projeto ON eproid = eveprojeto
            INNER JOIN
                veiculo ON veioid = conveioid
            LEFT JOIN
                analise_os_tratamento ON aotordoid = ordoid
            LEFT JOIN
                analise_os_acao_motivo AS acao ON acao.aoamoid = aotaoamoid_acao
            LEFT JOIN
                analise_os_acao_motivo AS motivo ON motivo.aoamoid = aotaoamoid_motivo
            WHERE
                condt_exclusao IS NULL
			AND ordoid =" . intval($id) . "";

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

        $sql = "INSERT INTO
                        analise_os_tratamento
                        (
        				aotdt_cadastro,
                        aotusuoid_cadastro,
                        aotordoid,
        				aoteproid,
                        aotaoamoid_acao,
                        aotaoamoid_motivo
                        )
                    VALUES
                        (
                        NOW(),
        				" . intval($_SESSION['usuario']['oid']) . ",
        				" . intval($dados->aotordoid) . ",
        				" . intval($dados->aoteproid) . ",
                        " . intval($dados->aoamoid_acao) . ",
                        " . intval($dados->aoamoid_motivo) . "
                    )";

        if (!pg_query($this->conn, $sql)) {
            throw new ErrorException(self::MENSAGEM_ERRO_PROCESSAMENTO);
        }

        return true;
    }

    /**
     * Responsável para inserir um registro de histórico de OS no banco de dados.
     * @param stdClass $dados Dados a serem gravados
     * @return boolean
     * @throws ErrorException
     */
    public function inserirHistoricoOs(stdClass $dados) {

        $sql = "INSERT INTO
                        ordem_situacao
                        (
        				orsordoid,
                        orssituacao,
                        orsusuoid
                        )
                    VALUES
                        (
        				" . intval($dados->ordoid) . ",
						'" . pg_escape_string( trim($dados->situacao) ) . "',
        				" . intval($_SESSION['usuario']['oid']) . "
                    )";

        if (!pg_query($this->conn, $sql)) {
            throw new ErrorException(self::MENSAGEM_ERRO_PROCESSAMENTO);
        }

        return true;
    }

    /**
     * Responsável para inserir um registro de histórico de Contrato no banco de dados.
     * @param stdClass $dados Dados a serem gravados
     * @return boolean
     * @throws ErrorException
     */
    public function inserirHistoricoContrato(stdClass $dados) {

       $sql = "SELECT
                        historico_termo_i
                        (
        				" . $dados->connumero . ",
                        " . intval($_SESSION['usuario']['oid']) . ",
                        '" . pg_escape_string( trim($dados->situacao) ) . "'
                    	)";

        if (!pg_query($this->conn, $sql)) {
            throw new ErrorException(self::MENSAGEM_ERRO_PROCESSAMENTO);
        }

        return true;
    }

    /**
     * Busca as ações
     * @return array
     * @throws ErrorException
     */
    public function buscarAcoes() {
        $retorno = array();

        $sql = "
            SELECT
                aoamoid,
                aoamdescricao
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

    /**
     * Buscar os motivos
     * @param stdClass $dados
     * @return array
     * @throws ErrorException
     */
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
     * Buscar e exibir no relatório as ações e os motivos
     * @param stdClass $dados
     * @return array
     * @throws ErrorException
     */
    public function buscarAcaoMotivos($ordoid) {

        $sql = "
            SELECT
                acao.aoamdescricao AS aoamdescricao_acao,
                motivo.aoamdescricao AS aoamdescricao_motivo
            FROM
			    analise_os_tratamento
        	INNER JOIN
			    analise_os_acao_motivo AS acao ON acao.aoamoid = aotaoamoid_acao
        	INNER JOIN
			    analise_os_acao_motivo AS motivo ON motivo.aoamoid = aotaoamoid_motivo
        	WHERE
        		aotordoid = " . intval($ordoid);

        if (!$rs = pg_query($this->conn, $sql)) {
            throw new ErrorException(self::MENSAGEM_ERRO_PROCESSAMENTO);
        }

        $retorno = pg_fetch_object($rs);

        return $retorno;
    }

    /**
     * Busca Tipos de Contrato
     * @return array
     * @throws ErrorException
     */
    public function buscarTipoContrato() {
        $retorno = array();

        $sql = "
            SELECT
                tpcoid,
                tpcdescricao
            FROM
                tipo_contrato
            WHERE
                tpcativo = 't'
            ORDER BY
                tpcdescricao";

        if (!$rs = pg_query($this->conn, $sql)) {
            throw new ErrorException(self::MENSAGEM_ERRO_PROCESSAMENTO);
        }

        while ($linha = pg_fetch_object($rs)) {
            $retorno[] = $linha;
        }

        return $retorno;
    }

    /**
     * Busca os Atendentes
     * @return array
     * @throws ErrorException
     */
    public function buscarAtendentes() {
        $retorno = array();

        $sql = "
            SELECT
                cd_usuario,
                ds_login
            FROM
                usuarios
            WHERE
                usudepoid IN(8)
                AND dt_exclusao IS NULL
            ORDER BY
                ds_login";

        if (!$rs = pg_query($this->conn, $sql)) {
            throw new ErrorException(self::MENSAGEM_ERRO_PROCESSAMENTO);
        }

        while ($linha = pg_fetch_object($rs)) {
            $retorno[] = $linha;
        }

        return $retorno;
    }

    /**
     * Busca a última localização e data do veículo
     * @see buscarUltimaLocalizaoVeiculo
     * @param int $veioid
     * @return string $ultimaPosicao
     */
    private function buscarUltimaLocalizaoDataVeiculo($veiculo, $dataOS) {
        $ultimaPosicao = '';
        
        if ($_SESSION['servidor_teste']){
            $urlWebService = _PROTOCOLO_ . "sasweb-services-homolog.sascar.com.br/unificado_backend/posicao/recuperarPosicaoPorMinutoComGMT";
            
        } else {
            $urlWebService = _PROTOCOLO_ . "sasweb-services.sascar.com.br/unificado_backend/posicao/recuperarPosicaoPorMinutoComGMT";
        }
    
        
        if (empty($veiculo) || empty($dataOS)){
            return $ultimaPosicao;
        }
        
        //Converte a data em Time stamp
        $separaData = explode(" ", $dataOS);
        $datas  = explode("/", $separaData[0]);
        $tempos = explode(":", $separaData[1]);
        $stamp  = mktime($tempos[0], $tempos[1], 0, $datas[1], $datas[0], $datas[2]);
        
        //Retira 29 dias da data
        $stamp = $stamp - (29 * 86400);
        
        $dataDe = date('d/m/Y H:i:s', $stamp);
        
        
        $xmlEnvio = "
            <PosicaoTO>
            <usuario>
                   <fusoHorarioTO>
                   <indicadorHorarioVerao>0</indicadorHorarioVerao>
                   <idTimeZone>Brazil/East</idTimeZone>
                   </fusoHorarioTO>
            </usuario>
            <quantidade>1</quantidade>
            <ignicao>3</ignicao>
            <dataAte>" . $dataOS . "</dataAte>
            <intervalo>5</intervalo>
            <dataDe>" . $dataDe . "</dataDe>
            <veioid>" . $veiculo . "</veioid>
            </PosicaoTO>
      ";
        
        //Define os cabeçalhos
        $headers = array(
          'Content-Type: text/xml; charset=utf-8',
          'Content-Length: '.strlen($xmlEnvio)
        );

        //chama o WS via cURL
        $ch = curl_init();
        //Define a url
        curl_setopt($ch, CURLOPT_URL, $urlWebService);
        //Define o metodo de envio (POST) pois o WS não aceita via GET
        curl_setopt($ch, CURLOPT_POST, TRUE);
        //Adiciona o cabeçalho na chamada
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        //adciona os campos
        curl_setopt($ch, CURLOPT_POSTFIELDS, $xmlEnvio);
        //Retornar os dados da tranferencia
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);

        
        if (($result = curl_exec($ch)) === FALSE) {
            return $ultimaPosicao;
        } 
        curl_close($ch);
        
        $xmlobj = simplexml_load_string($result);
        if (isset($xmlobj->posicoes->dataHoraPosicao)){
            $ultimaPosicao = $xmlobj->posicoes->dataHoraPosicao;
        }
        
        return $ultimaPosicao;
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
