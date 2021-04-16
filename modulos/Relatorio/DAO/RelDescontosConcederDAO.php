<?php

/**
 * Classe RelDescontosConcederDAO.
 * Camada de modelagem de dados.
 *
 * @package  Relatorio
 * @author   Ricardo Marangoni da Mota <ricardo.mota@meta.com.br>
 * 
 */
class RelDescontosConcederDAO {

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
 * 
 * @param stdClass $parametros Filtros da pesquisa
 * 
 * @throws ErrorException
 * @return array
 */
public function pesquisar(stdClass $parametros) {
    $retorno = array();

    $sql = "SELECT 
            credito_futuro.cfooid,
            credito_futuro.cfoclioid,
            credito_futuro.cfousuoid_inclusao,
            credito_futuro.cfousuoid_exclusao,
            credito_futuro.cfousuoid_encerramento,
            credito_futuro.cfousuoid_avaliador,
            TO_CHAR(credito_futuro.cfodt_inclusao,'DD/MM/YYYY') AS cfodt_inclusao,
            credito_futuro.cfodt_exclusao,
            credito_futuro.cfodt_encerramento,
            credito_futuro.cfodt_avaliacao,
            credito_futuro.cfoconnum_indicado,
            credito_futuro.cfocfcpoid,
            credito_futuro.cfocfmcoid,
            credito_futuro.cfoancoid,
            credito_futuro.cfoobroid_desconto,
            credito_futuro.cfostatus,
            credito_futuro.cfotipo_desconto,
            credito_futuro.cfovalor,
            credito_futuro.cfoforma_aplicacao,
            credito_futuro.cfoforma_inclusao,
            credito_futuro.cfosaldo,
            credito_futuro.cfoobservacao,
            credito_futuro.cfoaplicar_desconto,
            clientes.clinome,
            clientes.clitipo,
            CASE
                WHEN clientes.clitipo = 'F' THEN
                    clientes.clino_cpf
                ELSE
                    clientes.clino_cgc
            END AS clicpfcnpj,
            obrigacao_financeira.obrobrigacao,
            credito_futuro_motivo_credito.cfmcdescricao,
            usuarios.nm_usuario,
            cfmoid AS credito_futuro_movimentacao_ativa,
            (SELECT COUNT(cfpcfooid) FROM credito_futuro_parcela WHERE cfpcfooid = cfooid AND cfpativo = true) AS parcelas_ativas,
            cfpnumero,
            
            CASE 
                WHEN cfodt_avaliacao IS NOT NULL THEN (cfodt_avaliacao + cfpnumero * interval '1 month') 
                ELSE (cfodt_inclusao + cfpnumero * interval '1 month') 
            END AS conceder_em
            
        FROM
            credito_futuro
        INNER JOIN
            clientes ON credito_futuro.cfoclioid = clientes.clioid
        INNER JOIN
            obrigacao_financeira ON credito_futuro.cfoobroid_desconto = obrigacao_financeira.obroid
        INNER JOIN
            credito_futuro_motivo_credito ON credito_futuro.cfocfmcoid = credito_futuro_motivo_credito.cfmcoid
        INNER JOIN
            usuarios ON credito_futuro.cfousuoid_inclusao = usuarios.cd_usuario
        LEFT JOIN
            credito_futuro_movimento ON (cfmcfooid = cfooid AND cfmdt_exclusao IS NULL)
        INNER JOIN
            credito_futuro_parcela ON cfpcfooid = cfooid AND cfpativo = true                       
        WHERE
            1 = 1
        AND
        	credito_futuro.cfodt_exclusao IS NULL
    	AND
            credito_futuro.cfostatus = 1
    	AND
  	        (credito_futuro.cfosaldo > 0 OR (SELECT COUNT(cfpcfooid) FROM credito_futuro_parcela WHERE cfpcfooid = cfooid AND cfpativo = true) > 0 )
    ";

    if (!empty($parametros->cfooid)) {
        $sql.= "
            AND
                credito_futuro.cfooid = " . intval($parametros->cfooid) . "
        ";
    }

    if (!empty($parametros->cfoclioid)) {
        $sql.= "
            AND
                credito_futuro.cfoclioid = " . intval($parametros->cfoclioid) . "
        ";
    }

    if (!empty($parametros->periodo_inclusao_ini) && !empty($parametros->periodo_inclusao_fim)) {
        $sql.= "
            AND
                credito_futuro.cfodt_inclusao
                    BETWEEN '" . $parametros->periodo_inclusao_ini . " 00:00:01'
                        AND '" . $parametros->periodo_inclusao_fim . " 23:59:59'
        ";
    }

    if (!empty($parametros->cfoancoid)) {
        $sql.= "
            AND
                credito_futuro.cfoancoid = " . intval($parametros->cfoancoid) . "
        ";
    }

    if (!empty($parametros->cfocfcpoid)) {
        $sql.= "
            AND
                credito_futuro.cfocfcpoid = " . intval($parametros->cfocfcpoid) . "
        ";
    }

    if (!empty($parametros->cfoobroid_desconto)) {
        $sql.= "
            AND
                credito_futuro.cfoobroid_desconto = " . intval($parametros->cfoobroid_desconto) . "
        ";
    }

    if (!empty($parametros->cfoforma_inclusao) && intval($parametros->cfoforma_inclusao) <= 2) {
        $sql.= "
            AND
                credito_futuro.cfoforma_inclusao = " . intval($parametros->cfoforma_inclusao) . "
        ";
    }

    
    if (!empty($parametros->cfousuoid_inclusao)) {
        $sql.= "
            AND
                credito_futuro.cfousuoid_inclusao = " . intval($parametros->cfousuoid_inclusao) . "
        ";
    }

    if (!empty($parametros->cfotipo_desconto) && intval($parametros->cfotipo_desconto) <= 2) {
        $sql.= "
            AND
                credito_futuro.cfotipo_desconto = " . intval($parametros->cfotipo_desconto) . "
        ";
    }

    if (!empty($parametros->cfopercentual_de) && !empty($parametros->cfopercentual_ate)) {
        $parametros->cfopercentualde = str_replace(',', '.', $parametros->cfopercentual_de);
        $parametros->cfopercentualate = str_replace(',', '.', $parametros->cfopercentual_ate);

        $sql.= "
            AND
                credito_futuro.cfovalor
                    BETWEEN " . floatval($parametros->cfopercentualde) . "
                        AND " . floatval($parametros->cfopercentualate) . "
        ";
    }

    if (!empty($parametros->cfovalor_de) && !empty($parametros->cfovalor_ate)) {
        $parametros->cfovalorde = str_replace('R$ ', '', $parametros->cfovalor_de);
        $parametros->cfovalorde = str_replace('.', '', $parametros->cfovalorde);
        $parametros->cfovalorde = str_replace(',', '.', $parametros->cfovalorde);

        $parametros->cfovalorate = str_replace('R$ ', '', $parametros->cfovalor_ate);
        $parametros->cfovalorate = str_replace('.', '', $parametros->cfovalorate);
        $parametros->cfovalorate = str_replace(',', '.', $parametros->cfovalorate);

        $sql.= "
            AND
                credito_futuro.cfovalor
                    BETWEEN " . floatval($parametros->cfovalorde) . "
                        AND " . floatval($parametros->cfovalorate) . "
        ";
    }
    
    if (!empty($parametros->cfoforma_aplicacao) && intval($parametros->cfoforma_aplicacao) <= 2) {
        $sql.= "
            AND
                credito_futuro.cfoforma_aplicacao = " . intval($parametros->cfoforma_aplicacao) . "
        ";
    }

    $usuarioMotivoCreditoRestrito = isset($_SESSION['funcao']['credito_futuro_motivo_credito_restrito']) && $_SESSION['funcao']['credito_futuro_motivo_credito_restrito'] ? true : false;

    if (!empty($parametros->cfocfmcoid)) {            
      
        if (!in_array('-1', $parametros->cfocfmcoid)) {

            $filtro_motivo_credito = implode(', ', $parametros->cfocfmcoid);

            $sql.= " AND
                        credito_futuro.cfocfmcoid IN (" . $filtro_motivo_credito . ")";

        } elseif ( in_array('-1', $parametros->cfocfmcoid) && $usuarioMotivoCreditoRestrito) {

            
            $sql .= "AND
                         credito_futuro_motivo_credito.cfmctipo = 3 ";

        }

    } elseif ($usuarioMotivoCreditoRestrito) {

        $sql .= "AND
                         credito_futuro_motivo_credito.cfmctipo = 3 ";

    }


    $sql .= " ORDER BY
                        cfooid, cfodt_inclusao, conceder_em";

    if (!$rs = pg_query($this->conn, $sql)) {
        throw new ErrorException(self::MENSAGEM_ERRO_PROCESSAMENTO);
    }

    while ($row = pg_fetch_object($rs)) {

        if ($row->clitipo == "J") {
            $row->clicpfcnpj = $this->formatarDados('cnpj', $row->clicpfcnpj);
        } else if ($row->clitipo == "F") {
            $row->clicpfcnpj = $this->formatarDados('cpf', $row->clicpfcnpj);
        }

        $row->cfotipo_desconto_id =  $row->cfotipo_desconto;

        $row->cfosaldo = (float) $row->cfosaldo;

        if ($row->cfotipo_desconto == '1') {
            $row->cfotipo_desconto = "Percentual";

            if (trim($row->cfovalor) != '') {
                $row->valorDesconto = number_format($row->cfovalor, 2, ',', '.') . ' %';
            }
            
            if (trim($row->cfosaldo) != '') {
                $row->cfosaldo = number_format($row->cfosaldo, 2, ',', '.') . ' %';
            }
            
        } elseif ($row->cfotipo_desconto == '2') {
            $row->cfotipo_desconto = "Valor";

            if (trim($row->cfovalor) != '') {
                $row->valorDesconto = 'R$ ' . number_format($row->cfovalor, 2, ',', '.');
            }
            
            if (trim($row->cfosaldo) != '') {
                $row->cfosaldo = 'R$ ' . number_format($row->cfosaldo, 2, ',', '.');
            }
            
        } else {
            $row->cfotipo_desconto = "Todos";
            $row->valorDesconto = '';
        }

        $row->cfoforma_aplicacao_id =  $row->cfoforma_aplicacao;

        if ($row->cfoforma_aplicacao == '1') {
            $row->cfoforma_aplicacao = "Integral";
        } elseif ($row->cfoforma_aplicacao == '2') {
            $row->cfoforma_aplicacao = "Parcelas";
        } else {
            $row->cfoforma_aplicacao = "Todos";
        }

        $row->cfoforma_inclusao_id = $row->cfoforma_inclusao;
        
        if ($row->cfoforma_inclusao == "1") {
            $row->cfoforma_inclusao = "Manual";
        } elseif ($row->cfoforma_inclusao == "2") {
            $row->cfoforma_inclusao = "Automático";
        } else {
            $row->cfoforma_inclusao = "Todos";
        }

        $retorno[] = $row;
    }

    return $retorno;
}


/**
 * Método que realizar busca de Obrigação Financeira de Desconto
 * 
 * @return array $retorno Array de objetos para popular combo na view.
 */
public function buscarObrigacaoFinanceiraDesconto() {

    $sql = "SELECT 
				obroid,
				obrobrigacao
			FROM 
				obrigacao_financeira 
			JOIN 
				obrigacao_financeira_grupo 
					ON ofgoid = obrofgoid
			WHERE
				obrdt_exclusao IS NULL
			AND 
				ofgdescricao ILIKE '%Desconto%'
			ORDER BY 
				obrobrigacao";

    if (!$rs = pg_query($this->conn, $sql)) {
        throw new ErrorException(self::MENSAGEM_ERRO_PROCESSAMENTO);
    }

    $retorno = array();
    while ($row = pg_fetch_object($rs)) {
        $retorno[] = $row;
    }

    return $retorno;
}

/**
 * Método que realizar busca de Obrigação Financeira de Desconto
 * 
 * @return array $retorno Array de objetos para popular combo na view.
 */
public function buscarObrigacaoFinanceiraPorId($id) {

    $sql = "SELECT 
				obroid,
				obrobrigacao
			FROM 
				obrigacao_financeira 			
			WHERE
				obrdt_exclusao IS NULL
			AND
				obroid = ". $id ."			
			";

    if (!$rs = pg_query($this->conn, $sql)) {
        throw new ErrorException(self::MENSAGEM_ERRO_PROCESSAMENTO);
    }

    return pg_fetch_object($rs);
}


 /**
 * Buscar usuarios inclusao credito futuro
 * 
 * @return array $retorno
 */
public function buscarUsuarioInclusaoCreditoFuturo() {

    $sql = "SELECT 
                DISTINCT cd_usuario,
                nm_usuario
            FROM
                credito_futuro
            INNER JOIN
                usuarios ON cd_usuario = cfousuoid_inclusao
            ORDER BY 
                nm_usuario ASC";

    if (!$rs = pg_query($this->conn, $sql)) {
        throw new ErrorException(self::MENSAGEM_ERRO_PROCESSAMENTO);
    }

    $retorno = array();
    while ($row = pg_fetch_object($rs)) {
        $retorno[] = $row;
    }

    return $retorno;
}

/**
 * Método que busca motivos do crédito conforme o usuarios
 * 
 * @return array $retorno Motivos de créditos
 */
public function buscarMotivoDoCredito() {

    //verifica se o usuario logado tem a função motivo de credito restrito
    $usuarioMotivoCreditoRestrito = isset($_SESSION['funcao']['credito_futuro_motivo_credito_restrito']) && $_SESSION['funcao']['credito_futuro_motivo_credito_restrito'] ? true : false;

    $sql = "SELECT
                cfmcoid,
                cfmcdescricao 
            FROM
                credito_futuro_motivo_credito
            WHERE
                cfmcdt_exclusao IS NULL ";

    if ($usuarioMotivoCreditoRestrito) {

        $sql .= "AND
                cfmctipo = 3 ";
    }

    $sql .= " ORDER BY 
                cfmcdescricao ASC";

    if (!$rs = pg_query($this->conn, $sql)) {
        throw new ErrorException(self::MENSAGEM_ERRO_PROCESSAMENTO);
    }

    $retorno = array();
    while ($row = pg_fetch_object($rs)) {
        $retorno[] = $row;
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
     * Metodo para obter os usuarios de aprovacao
     *
     * @author Renato Teixeira Bueno <renato.bueno@meta.com.br>
     * @return Object
     */
    public function obterUsuariosAprovacao($idDepartamento){
         
        $sql = "
                SELECT
                    DISTINCT
                    cfmpcfeusuoid AS usuarioId,
                    usuemail as email
                FROM
                    credito_futuro_motivo_responsavel
                INNER JOIN
                    usuarios ON cfmpcfeusuoid = cd_usuario
                INNER JOIN
                    departamento ON usudepoid = depoid
                WHERE
                    depoid = " . $idDepartamento . "";
        if (!$rs = pg_query($this->conn, $sql)) {
            throw new ErrorException('Falha ao tentar buscar os usuários de aprovação.');
        }
    
        $usuarios = array();
    
        if (pg_num_rows($rs) > 0) {
            
            while ($row = pg_fetch_object($rs)) {
                $usuarios[] = $row->email;
            }
        }
        
        return $usuarios;
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
?>
