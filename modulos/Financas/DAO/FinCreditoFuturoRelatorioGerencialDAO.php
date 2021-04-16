 <?php

/**
 * Classe FinCreditoFuturoRelatorioGerencialDAO.
 * Camada de modelagem de dados.
 *
 * @package  Financas
 * @author   José Fernando <jose.carlos@meta.com.br>
 * 
 */
class FinCreditoFuturoRelatorioGerencialDAO {

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
public function pesquisar(stdClass $parametros){

$retorno = array();

$sql = "SELECT 
					cfcpoid, 
					cfcpdt_inicio_vigencia, 
					cfcpdt_fim_vigencia, 
					cfcpcftpoid, 
					cfcpcfmccoid, 
					cfcptipo_desconto, 
					cfcpdesconto, 
					cfcpaplicacao, 
					cfcpqtde_parcelas, 
					cfcpobroid, 
					cfcpobservacao, 
					cfcpusuoid_exclusao, 
					cfcpdt_exclusao, 
					cfcpaplicar_sobre, 
					cfcpusuoid_inclusao, 
					cfcpdt_inclusao
				FROM 
					credito_futuro_campanha_promocional
				WHERE 
					1 = 1
				AND 
					cfcpdt_exclusao IS NULL";

        if ( isset($parametros->cfcpoid) && trim($parametros->cfcpoid) != '' ) {

            $sql .= "AND
                        cfcpoid = " . intval( $parametros->cfcpoid ) . "";
            
        }



        if ( isset($parametros->cfcpcftpoid) && trim($parametros->cfcpcftpoid) != '' ) {

            $sql .= "AND
                        cfcpcftpoid = " . intval( $parametros->cfcpcftpoid ) . "";
            
        }

        if ( isset($parametros->cfcpcfmccoid) && trim($parametros->cfcpcfmccoid) != '' ) {

            $sql .= "AND
                        cfcpcfmccoid = " . intval( $parametros->cfcpcfmccoid ) . "";
            
        }

        if ( isset($parametros->cfcptipo_desconto) && !empty($parametros->cfcptipo_desconto) ) {
        
            $sql .= "AND
                        cfcptipo_desconto = '" . pg_escape_string( $parametros->cfcptipo_desconto ) . "'";
                
        }

        if ( isset($parametros->cfcpdesconto) && trim($parametros->cfcpdesconto) != '' ) {

            $sql .= "AND
                        cfcpdesconto = " . intval( $parametros->cfcpdesconto ) . "";
            
        }

        if ( isset($parametros->cfcpaplicacao) && !empty($parametros->cfcpaplicacao) ) {
        
            $sql .= "AND
                        cfcpaplicacao = '" . pg_escape_string( $parametros->cfcpaplicacao ) . "'";
                
        }

        if ( isset($parametros->cfcpqtde_parcelas) && trim($parametros->cfcpqtde_parcelas) != '' ) {

            $sql .= "AND
                        cfcpqtde_parcelas = " . intval( $parametros->cfcpqtde_parcelas ) . "";
            
        }

        if ( isset($parametros->cfcpobroid) && trim($parametros->cfcpobroid) != '' ) {

            $sql .= "AND
                        cfcpobroid = " . intval( $parametros->cfcpobroid ) . "";
            
        }

        if ( isset($parametros->cfcpobservacao) && !empty($parametros->cfcpobservacao) ) {
        
            $sql .= "AND
                        cfcpobservacao = '" . pg_escape_string( $parametros->cfcpobservacao ) . "'";
                
        }

        if ( isset($parametros->cfcpusuoid_exclusao) && trim($parametros->cfcpusuoid_exclusao) != '' ) {

            $sql .= "AND
                        cfcpusuoid_exclusao = " . intval( $parametros->cfcpusuoid_exclusao ) . "";
            
        }

        if ( isset($parametros->cfcpaplicar_sobre) && !empty($parametros->cfcpaplicar_sobre) ) {
        
            $sql .= "AND
                        cfcpaplicar_sobre = '" . pg_escape_string( $parametros->cfcpaplicar_sobre ) . "'";
                
        }

        if ( isset($parametros->cfcpusuoid_inclusao) && trim($parametros->cfcpusuoid_inclusao) != '' ) {

            $sql .= "AND
                        cfcpusuoid_inclusao = " . intval( $parametros->cfcpusuoid_inclusao ) . "";
            
        }




if (!$rs = pg_query($this->conn, $sql)){
throw new ErrorException(self::MENSAGEM_ERRO_PROCESSAMENTO);
}

while($row = pg_fetch_object($rs)){
$retorno[] = $row;
}

return $retorno;
}

/**
 * Método que busca os motivos de créditos
 * @return array
 */
public function listarMotivosCreditos() {

	$sql = "SELECT
				cfmcoid,
				cfmcdescricao
			FROM
				credito_futuro_motivo_credito
			WHERE
				cfmcdt_exclusao IS NULL 
		    ORDER BY 
				cfmcdescricao ASC";

	if (!$rs = pg_query($this->conn, $sql)) {
		throw new ErrorException(self::MENSAGEM_ERRO_PROCESSAMENTO);
	}

	$retorno = array();
	$retorno[] = (object) array('cfmcoid'=>'-1','cfmcdescricao'=>'Todos');
	while ($row = pg_fetch_object($rs)) {
		$retorno[] = $row;
	}

	return $retorno;
}

/**
 * Lista de tipos de campanha
 * @return array:StdClass
 */
public function listarTiposCampanhas() {

    $sql = "SELECT
			    cftpoid,
			    cftpdescricao
			FROM
			    credito_futuro_tipo_campanha
			WHERE
			    cftpdt_exclusao IS NULL
			ORDER BY
			    cftpdescricao ASC";

	if (!$rs = pg_query($this->conn, $sql)) {
		throw new ErrorException(self::MENSAGEM_ERRO_PROCESSAMENTO);
	}

    $retorno = array();
	$retorno[] = (object) array('cftpoid'=>'-1','cftpdescricao'=>'Todos');

    while ($row = pg_fetch_object($rs)) {
		$retorno[] = $row;
	}

    return $retorno;
}


/**
 * Método que busca os séries de notas
 * @return array
 */
public function listarSeriesNotas() {

	$sql = "SELECT 
 				nfsoid,
 				nfsserie
            FROM
 				 nota_fiscal_serie
 			WHERE
 				nfsdt_exclusao   is  null
            ORDER BY 
            	nfsserie ASC";

    if (!$rs = pg_query($this->conn, $sql)) {
		throw new ErrorException(self::MENSAGEM_ERRO_PROCESSAMENTO);
	}

    $retorno = array();
	$retorno[] = (object) array('nfsoid'=>'-1','nfsserie'=>'Todos');

    while ($row = pg_fetch_object($rs)) {
		$retorno[] = $row;
	}

    return $retorno;
}


/**
 * Método que busca e-mail de aprovadores do mesmo departamento do uuário logado
 * @return array
 */
public function buscarUsuariosAprovadoresCc() {


	$sql =  "SELECT 
				usuemail
			FROM 
				credito_futuro_email_responsavel 
			INNER JOIN
				usuarios on (cd_usuario = cferusuoid)
			WHERE
				cd_usuario <> " . intval($_SESSION['usuario']['oid']) . "
			AND
				usudepoid = " . intval($_SESSION['usuario']['depoid']) . "
			";

	if (!$rs = pg_query($this->conn, $sql)) {
		throw new ErrorException(self::MENSAGEM_ERRO_PROCESSAMENTO);
	}

	$retorno = array();

	while ($row = pg_fetch_object($rs)) {
		$retorno[] = $row->usuemail;
	}

	return $retorno;
}

/**
 * Método que busca e-mail do usuario logado
 * @return string
 */
public function buscarEmailUsuarioLogado() {


	$sql = "SELECT
				usuemail
			FROM
				usuarios
			WHERE 
				cd_usuario = " . intval($_SESSION['usuario']['oid']) . "";

	if (!$rs = pg_query($this->conn, $sql)) {
		throw new ErrorException(self::MENSAGEM_ERRO_PROCESSAMENTO);
	}

	$emailUsuarioLocado = pg_fetch_result($rs, 0, 'usuemail');

	return trim($emailUsuarioLocado) != '' ? $emailUsuarioLocado : '';


}

/**
 * Método que busca creditos concedidos na forma análitica conforme parametros
 * @return array
 */
public function pesquisarConcedidosAnalitico(stdClass $parametros) {


	$sql = "SELECT 
				-- data de emissao da nota
				TO_CHAR(cfhdt_emissao_nf,'DD/MM/YYYY') AS data_emissao_nota,

				-- numero da nota fiscal
				cfhnf_numero AS numero_nota,

				-- serie da nota fiscal
				cfhnf_serie AS serie_nota,

				-- nome do cliente
				clinome AS cliente,

				-- motivo de credito
				cfmcdescricao AS motivo_credito,

				-- tipo do cliente F OU J
				clitipo AS cliente_tipo,

				-- documento do cliente CNPJ OU CPF
				CASE WHEN clitipo = 'J' THEN
				    clino_cgc
				ELSE
				    clino_cpf
				END AS cliente_doc,

				-- forma de inclusão manual ou automático
				CASE WHEN cfoforma_inclusao = '1' THEN 'Manual'
				     ELSE 'Automatico'
				END AS forma_inclusao,

				-- protocolo
				cfoancoid AS protocolo,

				-- código do crédito futuro
				cfooid AS credito_futuro_id,
				
				-- campanha promocional
				cftpdescricao AS campanha_promocional,

				-- valor da nota
				cfhvalor_total_nf AS valor_nota,

				-- valor dos itens da  nota
				cfhvl_total_itens_nf AS valor_itens,

				-- valor do desconto concedido
				cfhvalor_aplicado_desconto AS valor_desconto

			FROM
				credito_futuro_historico
			INNER JOIN
				credito_futuro ON (cfooid = cfhcfooid)
			INNER JOIN
				clientes ON (clioid = cfoclioid)
			INNER JOIN
				credito_futuro_motivo_credito ON (cfmcoid = cfocfmcoid)
			LEFT JOIN
				credito_futuro_campanha_promocional ON (cfcpoid = cfocfcpoid)
			LEFT JOIN
				credito_futuro_tipo_campanha ON (cftpoid = cfcpcftpoid)
			WHERE
				cfhoperacao = 5
			AND
				cfhdt_emissao_nf IS NOT NULL 
			AND 
				cfhdt_emissao_nf BETWEEN '" . $parametros->periodo_inclusao_ini . " 00:00:00' AND '" . $parametros->periodo_inclusao_fim . " 23:59:59' ";

	if (isset($parametros->cliente_id) && trim($parametros->cliente_id) != '') {
		$sql .= " AND
						clioid = " . $parametros->cliente_id . " ";
	}

	if (isset($parametros->motivo_credito) && trim($parametros->motivo_credito) != '-1') {

		$sql .= " AND
						cfmcoid = " . $parametros->motivo_credito . " ";
	}

	if (isset($parametros->tipo_campanha_promocional) && trim($parametros->tipo_campanha_promocional) != '-1') {
		$sql .= " AND
						cftpoid = " . $parametros->tipo_campanha_promocional . " ";
	}

	if (isset($parametros->forma_inclusao) && trim($parametros->forma_inclusao) != '-1') {
		$sql .= " AND
						cfoforma_inclusao = " . $parametros->forma_inclusao . " ";
	}

	if (isset($parametros->numero_nf) && trim($parametros->numero_nf) != '') {
		$sql .= " AND
						cfhnf_numero = " . $parametros->numero_nf . " ";
	}

	if (isset($parametros->serie_nf) && trim($parametros->serie_nf) != 'Todos') {
		$sql .= " AND
						cfhnf_serie = '" . $parametros->serie_nf . "' ";
	}

	$sql .=" ORDER BY
				cfhdt_emissao_nf, cfhnf_numero, cfhnf_serie, clinome, cfmcdescricao ASC";


	if (!$rs = pg_query($this->conn, $sql)) {
		throw new ErrorException(self::MENSAGEM_ERRO_PROCESSAMENTO);
	}

    $retorno = array();
    $retorno['total_valor_notas']    = 0;
    $retorno['total_valor_itens']    = 0;
    $retorno['total_valor_desconto'] = 0;

    while ($row = pg_fetch_object($rs)) {		
		$retorno['total_valor_notas'] += floatval($row->valor_nota);
		$retorno['total_valor_itens'] += floatval($row->valor_itens);
		$retorno['total_valor_desconto'] += floatval($row->valor_desconto);

		if ($row->cliente_tipo == 'J') {
			$row->cliente_doc = $this->formatarDados('cnpj', $row->cliente_doc);
		} else {
			$row->cliente_doc = $this->formatarDados('cpf', $row->cliente_doc);
		}

		$row->valor_nota     = number_format($row->valor_nota, 2, ',', '.');
		$row->valor_itens    = number_format($row->valor_itens, 2, ',', '.');
		$row->valor_desconto = number_format($row->valor_desconto, 2, ',', '.');

		$retorno['descontos'][] = $row;
	}

    return $retorno;
}


/**
 * Método que busca créditos concedidos na forma sintética, conforme parametros
 * @param stdClass $parametros 
 * @return array
 */
public function pesquisarConcedidosSintetico(stdClass $parametros) {

	$sql = "SELECT 
				-- valor da nota
				SUM(cfhvalor_total_nf) AS valor_nota,

				-- valor dos itens da  nota
				SUM(cfhvl_total_itens_nf) AS valor_itens,

				-- valor do desconto concedido
				SUM(cfmvalor) AS valor_desconto, ";


	if (isset($parametros->tipo_resultado) && $parametros->tipo_resultado == "d") {

		$sql .=     "-- data de emissao da nota
					TO_CHAR(cfhdt_emissao_nf,'DD/MM/YYYY') AS data_emissao_nota,

					-- motivo de credito
					cfmcdescricao AS motivo_credito, 

					TO_CHAR(cfhdt_emissao_nf,'MM/YYYY') AS mes_emissao_nota, 

					-- motico de credito ID
					cfmcoid AS motivo_credito_id ";

	}

	if (isset($parametros->tipo_resultado) && $parametros->tipo_resultado == "m") {

		$sql .=     "-- data de emissao da nota
				TO_CHAR(cfhdt_emissao_nf,'MM/YYYY') AS data_emissao_nota ";

	}
	

	$sql .=	"FROM
				credito_futuro_movimento
			INNER JOIN
				nota_fiscal ON (nfloid = cfmnfloid)
			INNER JOIN
				credito_futuro ON (cfooid = cfmcfooid)
			INNER JOIN			
				credito_futuro_historico ON (nflno_numero = cfhnf_numero AND nflserie = cfhnf_serie AND cfhcfooid = cfooid)
			INNER JOIN
				clientes ON (clioid = cfoclioid)
			INNER JOIN
				credito_futuro_motivo_credito ON (cfmcoid = cfocfmcoid)
			LEFT JOIN
				credito_futuro_campanha_promocional ON (cfcpoid = cfocfcpoid)
			LEFT JOIN
				credito_futuro_tipo_campanha ON (cftpoid = cfcpcftpoid)			
			WHERE
				cfhoperacao = 5
			AND
				cfhdt_emissao_nf IS NOT NULL 
			AND 
				cfhdt_emissao_nf BETWEEN '" . $parametros->periodo_inclusao_ini . " 00:00:00' AND '" . $parametros->periodo_inclusao_fim . " 23:59:59' ";

	
	if (isset($parametros->motivo_credito) && trim($parametros->motivo_credito) != '-1') {

		$sql .= " AND
						cfmcoid = " . $parametros->motivo_credito . " ";
	}

	if (isset($parametros->tipo_campanha_promocional) && trim($parametros->tipo_campanha_promocional) != '-1') {
		$sql .= " AND
						cftpoid = " . $parametros->tipo_campanha_promocional . " ";
	}

	if (isset($parametros->forma_inclusao) && trim($parametros->forma_inclusao) != '-1') {
		$sql .= " AND
						cfoforma_inclusao = " . $parametros->forma_inclusao . " ";
	}

	if (isset($parametros->tipo_resultado) && $parametros->tipo_resultado == "d") {

		$sql .=" GROUP BY 
							data_emissao_nota, mes_emissao_nota, motivo_credito_id
				 ORDER BY
							data_emissao_nota, cfmcdescricao ASC";

	}
	
	if (isset($parametros->tipo_resultado) && $parametros->tipo_resultado == "m") { 

		$sql .=" GROUP BY 
							data_emissao_nota
			     ORDER BY
							data_emissao_nota ASC";

	}

		
	if (!$rs = pg_query($this->conn, $sql)) {
		throw new ErrorException(self::MENSAGEM_ERRO_PROCESSAMENTO);
	}


    $retorno = array();

	if (isset($parametros->tipo_resultado) && $parametros->tipo_resultado == "d") {

		$retorno['valor_nota_total'] = 0;
	    $retorno['valor_itens_total'] = 0;
	    $retorno['valor_descontos_total'] = 0;
	    $retorno['percentual_descontos_total'] = 0;

		 while ($row = pg_fetch_object($rs)) {	
			
			$row->percentual = round_up(($row->valor_desconto * 100) / $row->valor_itens,2);
			$row->percentual = number_format($row->percentual,2,',','.');
			$retorno['descontos'][$row->mes_emissao_nota]['vl_itens'] += floatval($row->valor_itens);
			$retorno['descontos'][$row->mes_emissao_nota]['vl_desc']  += floatval($row->valor_desconto);
			$retorno['descontos'][$row->mes_emissao_nota]['vl_nf']    += floatval($row->valor_nota);
			$retorno['descontos'][$row->mes_emissao_nota]['itens'][] = $row;
		 }

		 if (isset($retorno['descontos']) && count($retorno['descontos']) > 0) {
		 	
			 foreach ($retorno['descontos'] as $key => $linha) {

			 	$retorno['valor_nota_total']      += floatval($linha['vl_nf']);
			 	$retorno['valor_itens_total'] 	  += floatval($linha['vl_itens']);
			 	$retorno['valor_descontos_total'] += floatval($linha['vl_desc']);

			 	$retorno['descontos'][$key]['vl_percentual'] = round_up(($linha['vl_desc'] * 100) / $linha['vl_itens'],2);
			 	$retorno['descontos'][$key]['vl_percentual'] = number_format($retorno['descontos'][$key]['vl_percentual'] , 2 ,',','.');
			 }

		 
			 $retorno['percentual_descontos_total'] = round_up(($retorno['valor_descontos_total'] * 100) / $retorno['valor_itens_total'],2);
			 $retorno['percentual_descontos_total'] = number_format($retorno['percentual_descontos_total'], 2 ,',','.');
		 }

	} else {

		$retorno['valor_nota_total'] = 0;
	    $retorno['valor_itens_total'] = 0;
	    $retorno['valor_descontos_total'] = 0;
	    $retorno['percentual_descontos_total'] = 0;

		 while ($row = pg_fetch_object($rs)) {	
			

		 	$retorno['valor_nota_total'] 		+= floatval($row->valor_nota);
		 	$retorno['valor_itens_total'] 		+= floatval($row->valor_itens);
		 	$retorno['valor_descontos_total']	+= floatval($row->valor_desconto);

			$row->percentual = round_up(($row->valor_desconto * 100) / $row->valor_itens,2);
			$row->percentual = number_format($row->percentual,2,',','.');
			$retorno['descontos'][$row->data_emissao_nota] = $row;

		 }

		 if (isset($retorno['descontos']) && count($retorno['descontos']) > 0) {
		 	$retorno['percentual_descontos_total'] = round_up(($retorno['valor_descontos_total'] * 100) / $retorno['valor_itens_total'],2);
		 	$retorno['percentual_descontos_total'] = number_format($retorno['percentual_descontos_total'], 2 ,',','.');
		 }		 

	}

    return $retorno;
}

/**
 * Método buscarCampanhasVigentes();
 * 
 * Retorna array de objetos de de campanhas promocionais vigentes e ativas.
 * 
 * @return array $retorno
 */
public function buscarCampanhasVigentes() {

	$sql = "SELECT 
				cfcpoid AS id,
				TO_CHAR(cfcpdt_inicio_vigencia, 'DD/MM/YYYY') AS ini_vigencia,
				TO_CHAR(cfcpdt_fim_vigencia, 'DD/MM/YYYY') AS fim_vigencia,
				cftpdescricao AS tipo_campanha,
				cfmcdescricao AS motivo_credito,
				cfcptipo_desconto,
				CASE	WHEN cfcptipo_desconto = 'V' THEN 'Valor'
				ELSE 'Percentual'
				END AS tipo_desconto,

				CASE	WHEN cfcpaplicacao = 'I' THEN 'Integral'
				ELSE 'Parcelas'
				END AS forma_aplicacao,

				cfcpdesconto AS valor,

				cfcpqtde_parcelas AS qtd_parcelas,
				nm_usuario AS usuario
			FROM 
				credito_futuro_campanha_promocional
			INNER JOIN
				credito_futuro_tipo_campanha ON (cftpoid = cfcpcftpoid)
			INNER JOIN
				credito_futuro_motivo_credito ON (cfmcoid = cfcpcfmccoid)
			INNER JOIN
				usuarios ON (cd_usuario = cfcpusuoid_inclusao)
			WHERE
				--NOW()::timestamp BETWEEN (cfcpdt_inicio_vigencia || ' 00:00:00 ')::timestamp AND (cfcpdt_fim_vigencia || ' 23:59:59 ')::timestamp
				NOW()::date BETWEEN cfcpdt_inicio_vigencia AND cfcpdt_fim_vigencia
			AND
				cfcpdt_exclusao IS NULL
			ORDER BY
				cfcpoid ASC";

	if (!$rs = pg_query($this->conn, $sql)){
		throw new ErrorException(self::MENSAGEM_ERRO_PROCESSAMENTO);
	}

	$retorno = array();

	while ($row = pg_fetch_object($rs)) {

		$row->periodo = $row->ini_vigencia . ' a ' . $row->fim_vigencia;

		if ($row->cfcptipo_desconto == 'V') {
			$row->valor = 'R$ ' . number_format($row->valor, 2, ',', '.');
		} else {
			$row->valor = number_format($row->valor, 2, ',', '.') . ' %';
		}

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
public function pesquisarPorID($id){

$retorno = new stdClass();

$sql = "SELECT 
					cfcpoid, 
					cfcpdt_inicio_vigencia, 
					cfcpdt_fim_vigencia, 
					cfcpcftpoid, 
					cfcpcfmccoid, 
					cfcptipo_desconto, 
					cfcpdesconto, 
					cfcpaplicacao, 
					cfcpqtde_parcelas, 
					cfcpobroid, 
					cfcpobservacao, 
					cfcpusuoid_exclusao, 
					cfcpdt_exclusao, 
					cfcpaplicar_sobre, 
					cfcpusuoid_inclusao, 
					cfcpdt_inclusao
				FROM 
					credito_futuro_campanha_promocional
				WHERE 
					cfcpoid =" . intval( $id ) . "";

if (!$rs = pg_query($this->conn, $sql)){
throw new ErrorException(self::MENSAGEM_ERRO_PROCESSAMENTO);
}

if (pg_num_rows($rs) > 0){
$retorno = pg_fetch_object($rs);
}

return $retorno;
}


/**
     * Formatar dados (CPF||CNPJ)
     * 
     * @param string $tipo  tipo doc
     * @param string $valor valor do doc
     * 
     * @return string $valor
     */
    public function formatarDados($tipo, $valor) {

        if ($tipo == "cpf" && $valor != "") {
            $valor = str_pad($valor, 11, "0", STR_PAD_LEFT);
            return $valor = substr($valor, 0, 3) . "." . substr($valor, 3, 3) . "." . substr($valor, 6, 3) . "-" . substr($valor, 9, 2);
        }

        if ($tipo == "cnpj" && $valor != "") {
            $valor = str_pad($valor, 14, "0", STR_PAD_LEFT);
            return $valor = substr($valor, 0, 2) . "." . substr($valor, 2, 3) . "." . substr($valor, 5, 3) . "/" . substr($valor, 8, 4) . "-" . substr($valor, 12, 2);
        }
    }

/**
 * Responsável para inserir um registro no banco de dados.
 * @param stdClass $dados Dados a serem gravados
 * @return boolean
 * @throws ErrorException
 */
public function inserir(stdClass $dados){

$sql = "INSERT INTO
					credito_futuro_campanha_promocional
					(
					cfcpdt_inicio_vigencia,
					cfcpdt_fim_vigencia,
					cfcpcftpoid,
					cfcpcfmccoid,
					cfcptipo_desconto,
					cfcpdesconto,
					cfcpaplicacao,
					cfcpqtde_parcelas,
					cfcpobroid,
					cfcpobservacao,
					cfcpusuoid_exclusao,
					cfcpaplicar_sobre,
					cfcpusuoid_inclusao,
					cfcpdt_inclusao
					)
				VALUES
					(
					'" . $dados->cfcpdt_inicio_vigencia . "',
					'" . $dados->cfcpdt_fim_vigencia . "',
					" . intval( $dados->cfcpcftpoid ) . ",
					" . intval( $dados->cfcpcfmccoid ) . ",
					'" . pg_escape_string( $dados->cfcptipo_desconto ) . "',
					" . intval( $dados->cfcpdesconto ) . ",
					'" . pg_escape_string( $dados->cfcpaplicacao ) . "',
					" . intval( $dados->cfcpqtde_parcelas ) . ",
					" . intval( $dados->cfcpobroid ) . ",
					'" . pg_escape_string( $dados->cfcpobservacao ) . "',
					" . intval( $dados->cfcpusuoid_exclusao ) . ",
					'" . pg_escape_string( $dados->cfcpaplicar_sobre ) . "',
					" . intval( $dados->cfcpusuoid_inclusao ) . ",
					'" . $dados->cfcpdt_inclusao . "'
				)";

if (!pg_query($this->conn, $sql)){
throw new ErrorException(self::MENSAGEM_ERRO_PROCESSAMENTO);
}

return true;
}

/**
 * Responsável por atualizar os registros 
 * @param stdClass $dados Dados a serem gravados
 * @return boolean
 * @throws ErrorException
 */
public function atualizar(stdClass $dados){

$sql = "UPDATE
					credito_futuro_campanha_promocional
				SET
					cfcpdt_inicio_vigencia = '" . $dados->cfcpdt_inicio_vigencia . "',
					cfcpdt_fim_vigencia = '" . $dados->cfcpdt_fim_vigencia . "',
					cfcpcftpoid = " . intval( $dados->cfcpcftpoid ) . ",
					cfcpcfmccoid = " . intval( $dados->cfcpcfmccoid ) . ",
					cfcptipo_desconto = '" . pg_escape_string( $dados->cfcptipo_desconto ) . "',
					cfcpdesconto = " . intval( $dados->cfcpdesconto ) . ",
					cfcpaplicacao = '" . pg_escape_string( $dados->cfcpaplicacao ) . "',
					cfcpqtde_parcelas = " . intval( $dados->cfcpqtde_parcelas ) . ",
					cfcpobroid = " . intval( $dados->cfcpobroid ) . ",
					cfcpobservacao = '" . pg_escape_string( $dados->cfcpobservacao ) . "',
					cfcpusuoid_exclusao = " . intval( $dados->cfcpusuoid_exclusao ) . ",
					cfcpaplicar_sobre = '" . pg_escape_string( $dados->cfcpaplicar_sobre ) . "',
					cfcpusuoid_inclusao = " . intval( $dados->cfcpusuoid_inclusao ) . ",
					cfcpdt_inclusao = '" . $dados->cfcpdt_inclusao . "'
				WHERE 
					cfcpoid = " . $dados->cfcpoid . "";

if (!pg_query($this->conn, $sql)){
throw new ErrorException(self::MENSAGEM_ERRO_PROCESSAMENTO);
}

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
					credito_futuro_campanha_promocional
				SET
					cfcpdt_exclusao = NOW() 
				WHERE
					cfcpoid = " . intval( $id ) . "";

if (!pg_query($this->conn, $sql)){
throw new ErrorException(self::MENSAGEM_ERRO_PROCESSAMENTO);
}

return true;
}

/**
 * Abre a transação
 */
public function begin(){
pg_query($this->conn, 'BEGIN');
}

/**
 * Finaliza um transação
 */
public function commit(){
pg_query($this->conn, 'COMMIT');
}

/**
 * Aborta uma transação
 */
public function rollback(){
pg_query($this->conn, 'ROLLBACK');
}


}

 function round_up ($value, $places=0) {
  if ($places < 0) { $places = 0; }
  $mult = pow(10, $places);
  return ceil($value * $mult) / $mult;
 }
?>

