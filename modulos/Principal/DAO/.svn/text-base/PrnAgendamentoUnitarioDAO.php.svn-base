<?php
// Inclui a classe de funções
include_once _SITEDIR_."lib/funcoes.php";

/**
 * Classe PrnAgendamentoUnitarioDAO.
 * Camada de modelagem de dados.
 *
 * @package  Principal
 * @author   Adenilson Santos <adenilson.santos.ext@sascar.com.br>
 *
 */
class PrnAgendamentoUnitarioDAO extends DAO {

	public $usarioLogado;

	const MENSAGEM_ERRO_PROCESSAMENTO = "Houve um erro no processamento dos dados.";

	public function __construct($conn) {

		//Seta a conexao na classe
        $this->conn = $conn;
        $this->usarioLogado = $this->getUsuarioLogado;
        }

    /**
     * Retorna a conexão com o banco de dados
     *
     * @return resource
     */
    public function getConn()
    {
        return $this->conn;
    }

    public function abreConexaoExterna() {
      $ConexaoExterna = pg_connect(conexao());

      $this->conn = $ConexaoExterna;
    }
    /**
	 * Método para realizar a pesquisa de varios registros
	 * @param stdClass $parametros Filtros da pesquisa
	 * @return array
	 * @throws ErrorException
	 */
	public function pesquisar(stdClass $parametros, $paginacao = null, $ordenacao = null) {

        if (is_null($paginacao)) {
            $sql = "SELECT
                      COUNT(DISTINCT os.ordoid) as total ";
        } else {
            $sql = "SELECT
                      DISTINCT ON (os.ordoid)
                      ot.ostdescricao,
                      os.ordoid,
            		  osa.osaoid,
                      COALESCE(osa.osaasaoid, 2) AS osaasaoid,
                      os.ordconnumero,
                      rep.repnome,
                      v.veiplaca,
                      v.veichassi,
                      TO_CHAR(osa.osadata, 'DD/MM/YYYY') AS osadata,
                      osadata AS data_agenda,
                      TO_CHAR(osa.osahora, 'HH24:MI') AS osahora,
                      TO_CHAR(osa.osahora_final, 'HH24:MI') AS osahora_final,
                      TO_CHAR(os.orddt_ordem, 'DD/MM/YYYY') AS orddt_ordem,
                      os.orddt_ordem AS data_os,
                      tc.clinome,
                      upper(osa.osaendereco) as osaendereco,
                      upper(est.estuf) as estado,
                      upper(cl.clcnome) as cidade,
                      upper(cb.cbanome) as bairro ";
        }

		    $sql .= "FROM
                   ordem_servico AS os
                   LEFT JOIN ordem_servico_agenda AS osa ON (os.ordoid = osa.osaordoid AND osa.osaexclusao IS NULL)
                   LEFT JOIN estado est on (est.estoid = osa.osaestoid)
                   LEFT JOIN correios_localidades cl on (cl.clcoid = osa.osaclcoid)
                   LEFT JOIN correios_bairros cb on (cb.cbaoid = osa.osacbaoid)
                   INNER JOIN contrato AS c ON (os.ordconnumero = c.connumero)
                   INNER JOIN veiculo AS v ON (c.conveioid = v.veioid)
                   INNER JOIN clientes AS tc ON (os.ordclioid = tc.clioid)
                   INNER JOIN os_tipo AS ot ON (os.ordostoid = ot.ostoid)
                   LEFT JOIN representante AS rep ON (osa.osarepoid = rep.repoid)
                 WHERE
                   EXISTS (SELECT 1 FROM ordem_servico_item AS osi WHERE osi.ositordoid = os.ordoid AND osi.ositstatus  =
                    ANY(string_to_array((SELECT
                                            pcsidescricao
                                        FROM
                                            parametros_configuracoes_sistemas_itens
                                        WHERE
                                            pcsipcsoid = 'SMART_AGENDA'
                                        AND
                                            pcsioid = 'STATUS_ITEM_OS'),','))


                    ) AND
                   c.condt_exclusao IS NULL AND
                   (os.ordfrota IS NULL OR os.ordfrota = FALSE) AND
                   os.ordstatus = ANY(string_to_array((SELECT
                                                            pcsidescricao
                                                        FROM
                                                            parametros_configuracoes_sistemas_itens
                                                        WHERE
                                                            pcsipcsoid = 'SMART_AGENDA'
                                                        AND
                                                            pcsioid = 'STATUS_OS_PESQUISA'),',')::integer[])";

        if (isset($parametros->cmp_cliente) && trim($parametros->cmp_cliente) != '') {
            $sql .= " AND os.ordclioid = " . intval($parametros->cmp_cliente);
        }

        if (isset($parametros->portal_cliente) && $parametros->portal_cliente) {
          $sql .= " AND os.ordstatus NOT IN (1, 10) ";
        }

        if (isset($parametros->cmp_cpf_cnpj) && trim($parametros->cmp_cpf_cnpj) != '') {
            $sql .= " AND (clino_cpf = " . intval($parametros->cmp_cpf_cnpj);
            $sql .= " OR clino_cgc = " . intval($parametros->cmp_cpf_cnpj) . ")";
        }

        if (isset($parametros->cmp_numero_os) && trim($parametros->cmp_numero_os) != '') {
            $sql .= " AND os.ordoid = " . intval($parametros->cmp_numero_os);
        }

        if (isset($parametros->cmp_data_os) && !empty($parametros->cmp_data_os)) {
            $dataOS = DateTime::createFromFormat('d/m/Y', $parametros->cmp_data_os);
            $sql .= " AND os.orddt_ordem::date = '" . $dataOS->format('Y-m-d') . "'::date";
        }

        if (isset($parametros->cmp_tipo_servico) && !empty($parametros->cmp_tipo_servico)) {
            $sql .= " AND os.ordostoid = " . intval($parametros->cmp_tipo_servico);
        }

        if (isset($parametros->cmp_contrato) && !empty($parametros->cmp_contrato)) {
            $sql .= " AND os.ordconnumero = " . intval($parametros->cmp_contrato);
        }

        if (isset($parametros->cmp_placa) && trim($parametros->cmp_placa) != '') {
            $placa = strtoupper(preg_replace('/[^A-Za-z0-9\-]/', '', $parametros->cmp_placa));

            $sql .= " AND v.veiplaca = '{$placa}'";
        }

        if (isset($parametros->cmp_chassi) && !empty($parametros->cmp_chassi)) {
            $sql .= " AND v.veichassi ILIKE '%{$parametros->cmp_chassi}%'";
        }

        if (isset($parametros->cmp_agendamento_aberto) && $parametros->cmp_agendamento_aberto) {
            $sql .= " AND osa.osaoid IS NULL";
        } else {
            if (isset($parametros->cpm_cep) && trim($parametros->cpm_cep) != '') {
                $cep = preg_replace('/[\D]/', '', $parametros->cpm_cep);
                $sql .= " AND osa.osacep = {$cep}";
            }

            if (isset($parametros->cmp_uf) && !empty($parametros->cmp_uf)) {
                $sql .= " AND osa.osaestoid = " . intval($parametros->cmp_uf);
            }

            if (isset($parametros->cmp_cidade) && !empty($parametros->cmp_cidade)) {
                $sql .= " AND osa.osaclcoid = " . intval($parametros->cmp_cidade);
            }
        }

        if (isset($parametros->cmp_data_inicio) && !empty($parametros->cmp_data_inicio) &&
            isset($parametros->cmp_data_fim) && !empty($parametros->cmp_data_fim)) {

            $dataInicial = DateTime::createFromFormat('d/m/Y', $parametros->cmp_data_inicio);
            $dataFinal = DateTime::createFromFormat('d/m/Y', $parametros->cmp_data_fim);

            $sql .= " AND osa.osadata BETWEEN '" . $dataInicial->format('Y-m-d') . "' AND '" . $dataFinal->format('Y-m-d') . "'";
        }

        if (!is_null($paginacao)) {
            $sql = "SELECT * FROM ({$sql}) AS dd";

            if (!empty($ordenacao)) {
                $sql .= " ORDER BY {$ordenacao}";
            }

            $sql .= " LIMIT " . $paginacao->limite . " OFFSET " . $paginacao->offset;
        }

		    $rs = pg_query($this->conn, $sql);

        if (is_null($paginacao)) {
            return pg_fetch_object($rs);
        } else {
            while($registro = pg_fetch_object($rs)){
                $retorno[] = $registro;
            }
            return $retorno;
        }
	}


    public function getEstados()
    {
        $retorno = array();
		$sql = "SELECT
                  estoid,
                  estnome
                FROM
                  estado
                WHERE
                  estexclusao IS NULL AND
                  estpaisoid = 1
                ORDER BY
                  estnome ASC";

		$rs = pg_query($this->conn,$sql);
        while($linha = pg_fetch_object($rs)){
			$retorno[$linha->estoid] = $linha->estnome;
		}
		return $retorno;
    }

    public function getCidades($idEstado)
    {
        $retorno = array();
		$sql = "SELECT
                  clcoid,
                  clcnome
                FROM
                  correios_localidades
                WHERE
                  clcestoid = {$idEstado}
                ORDER BY
                  clcnome ASC";

		$rs = pg_query($this->conn,$sql);
        while($linha = pg_fetch_object($rs)){
			$retorno[$linha->clcoid] = removeAcentos($linha->clcnome);
		}
		return $retorno;
    }

    public function getNomeClientes($termo)
    {
        $retorno = array();
		$sql = "SELECT
                  clioid,
                  clinome
                FROM
                  clientes
                WHERE
                  clidt_exclusao IS NULL AND
                  clinome ILIKE '{$termo}%'
                ORDER BY
                  clinome ASC
                LIMIT 10";

		$query = pg_query($this->conn,$sql);
        while($linha = pg_fetch_object($query)){
            $nomeCliente = removeAcentos($linha->clinome);
			$retorno[] = array(
                'id' => $linha->clioid,
                'label' => $nomeCliente,
                'value' => $nomeCliente,
            );
		}
		return $retorno;
    }

    public function getOrdemServico($idOrdemServico)
    {
        $sql = "SELECT
                  os.ordoid,
                  os.ordclioid,
                  ot.ostoid,
                  ot.ostgrupo,
                  ot.ostdescricao,
                  ot.ostoid,
                  upper(oss.ossdescricao) AS ossdescricao,
                  CASE
                    WHEN os.ordurgente THEN 'URGENTE' ELSE 'NORMAL'
                  END AS ordurgente,
                  TO_CHAR(os.orddt_ordem, 'DD/MM/YYYY') AS orddt_ordem,
                  os.ordconnumero,
                  tc.clioid,
                  tc.clinome,
                  tc.cliemail,
                  tc.clitipo,
                  tc.clifone_res,
                  tc.clifone_com,
                  tc.clino_cpf,
                  tc.clino_cgc,
                  CASE
                    WHEN tc.clitipo = 'F' THEN tc.cliuf_res ELSE tc.cliuf_com
                  END AS uf,
                  CASE
                    WHEN tc.clitipo = 'F' THEN tc.clicidade_res ELSE tc.clicidade_com
                  END AS cidade,
                  ma.mcamarca,
                  m.mlomodelo,
                  v.veimlooid,
                  v.veino_ano,
                  v.veicor,
                  v.veiplaca,
                  v.veino_renavan,
                  c.conmodalidade,
                  c.condt_cadastro::date,
                  c.condt_ini_vigencia::date,
                  c.conno_tipo,
                  ec.eqcoid,
                  ec.eqcdescricao,
                  ec.eqcecgoid,
                  v.veichassi,
                  os.ordrepoid_direcionado AS id_relacao_representante,
                  r.repnome AS representante,
                  r.repoid AS id_representante,
                  ag.agccodigo
                FROM
                  ordem_servico AS os
                  INNER JOIN ordem_servico_status AS oss ON (os.ordstatus = oss.ossoid)
                  INNER JOIN contrato AS c ON (os.ordconnumero = c.connumero)
                  INNER JOIN veiculo AS v ON (c.conveioid = v.veioid)
                  INNER JOIN modelo AS m ON (v.veimlooid = m.mlooid)
                  INNER JOIN marca AS ma ON (m.mlomcaoid = ma.mcaoid)
                  INNER JOIN equipamento_classe AS ec ON (c.coneqcoid = ec.eqcoid)
                  INNER JOIN clientes AS tc ON (os.ordclioid = tc.clioid)
                  INNER JOIN os_tipo AS ot ON (os.ordostoid = ot.ostoid)
                  LEFT JOIN agrupamento_classe AS ag ON (ec.eqcagcoid = ag.agcoid)
                  LEFT JOIN representante AS r ON (r.repoid = os.ordrepoid_direcionado)
                 WHERE
                   os.ordoid = ".floatval($idOrdemServico);

		if (!$query = pg_query($this->conn, $sql)) {
			throw new Exception('Houve um erro ao tentar executar a verificacao de capacidade no banco de dados.');
		}
        return pg_num_rows($query) ? pg_fetch_array($query, 0, PGSQL_ASSOC) : array();
    }

    public function getServicosOS($idOrdemServico)
    {
        $sql = "SELECT
                  oti.otitipo AS codigo_tipo_os,
                  CASE
                    WHEN oti.otitipo = 'E' THEN 'EQUIPAMENTO'
                    WHEN oti.otitipo = 'A' THEN 'ACESSORIOS'
                    ELSE 'KIT'
                  END AS item,
                  ot.ostoid AS id_tipo_os,
                  ot.ostdescricao AS tipo,
                  oti.otioid AS id_tipo_servico,
                  oti.otidescricao AS motivo,
                  osd.osdfotdoid AS id_tipo_defeito_alegado,
                  osd.osdfdescricao AS defeito_alegado,
                  CASE
                    WHEN osi.ositstatus = 'A' THEN 'AUTORIZADO'
                    WHEN osi.ositstatus = 'C' THEN 'CONCLUIDO'
                    WHEN osi.ositstatus = 'E' THEN 'NAO EXECUTADO'
                    WHEN osi.ositstatus = 'N' THEN 'NAO AUTORIZADO'
                    WHEN osi.ositstatus = 'X' THEN 'CANCELADO'
                    ELSE 'PENDENTE'
                  END AS status,
                  oti.otipeso
                FROM
                  ordem_servico_item AS osi
                  INNER JOIN os_tipo_item AS oti ON (osi.ositotioid = oti.otioid)
                  INNER JOIN os_tipo AS ot ON (oti.otiostoid = ot.ostoid)
                  LEFT JOIN ordem_servico_defeito AS osd ON (osi.ositosdfoid_alegado = osd.osdfoid)
                 WHERE
                   osi.ositexclusao IS NULL AND
                   osi.ositstatus = ANY(string_to_array((SELECT
                                            pcsidescricao
                                        FROM
                                            parametros_configuracoes_sistemas_itens
                                        WHERE
                                            pcsipcsoid = 'SMART_AGENDA'
                                        AND
                                            pcsioid = 'STATUS_ITEM_OS'),','))
                    AND osi.ositordoid = {$idOrdemServico}";
		if (!$query = pg_query($this->conn, $sql)) {
			throw new Exception('Houve um erro ao tentar executar a buscar os serviços da OS no banco de dados.');
		}
        return pg_num_rows($query) ? pg_fetch_all($query) : array();
    }

    public function buscarOSAdicional($idContrato, $idOrdemServico, $reagendar = false)
    {
        // Determina se
        $condicao = $reagendar ? '' : 'NOT';
        $sql = "SELECT
                  os.ordoid
                FROM
                  ordem_servico AS os
                WHERE
                  os.ordstatus = ANY(
                    string_to_array((SELECT
                      pcsidescricao AS example
                    FROM
                      parametros_configuracoes_sistemas_itens
                    WHERE
                      pcsipcsoid = 'SMART_AGENDA'
                      AND pcsioid = 'STATUS_OS_PESQUISA'),',')::integer[]
                  ) AND
                  os.ordostoid IN (2, 3, 9) AND
                  os.ordoid != ".floatval($idOrdemServico)." AND
                  os.ordconnumero = ".floatval($idContrato)." AND
                  EXISTS (SELECT 1 FROM ordem_servico_item AS osi WHERE osi.ositordoid = os.ordoid) AND /*Verifica se possui itens de serviços*/
                  {$condicao} EXISTS (
                    SELECT
                      1
                    FROM
                      ordem_servico_agenda AS osa
                    WHERE
                      osa.osaexclusao IS NULL AND
                      osa.osaordoid = ".floatval($idOrdemServico)."
                  )";


        if (!$query = pg_query($this->conn, $sql)) {
			throw new Exception('Houve um erro ao buscar OS adicional no banco de dados. ' .  pg_last_error($this->conn));
		}
        return pg_num_rows($query) ? pg_fetch_array($query, 0, PGSQL_ASSOC) : array();
    }

    /**
     * Verifica se existe uma OS relacionada ja agendada
     * @param  [int]  $idContrato
     * @param  [int]  $idOrdemServico
     * @return [int]
     */
    public function buscarOSAdicionalAjax($idContrato, $idOrdemServico) {

        $retorno = array();

        $sql = "SELECT
                    osaordoid,
                    lower(ostdescricao) AS descricao,
                    '2' AS tipo,
                    ordostoid
                FROM
                    ordem_servico_agenda
                INNER JOIN
                    ordem_servico ON (ordoid = osaordoid)
                INNER JOIN
                    os_tipo ON (ostoid = ordostoid)
                WHERE
                    osaexclusao IS NULL
                AND
                    osaordoid = (

                        SELECT
                          ordoid
                        FROM
                          ordem_servico
                        WHERE
                            ordstatus = ANY(
                                            string_to_array((SELECT
                                              pcsidescricao
                                            FROM
                                              parametros_configuracoes_sistemas_itens
                                            WHERE
                                              pcsipcsoid = 'SMART_AGENDA'
                                              AND pcsioid = 'STATUS_OS_PESQUISA'),',')::integer[]
                                            )
                        AND
                          ordostoid IN (2, 3, 9)
                        AND
                          ordoid != ".intval($idOrdemServico)."
                        AND
                          ordconnumero = ".floatval($idContrato)."
                        AND
                          EXISTS (SELECT 1 FROM ordem_servico_item WHERE ositordoid = ordoid)
                        LIMIT 1)
                UNION
                SELECT
                    osaordoid,
                    lower(ostdescricao)AS descricao,
                    '1' as tipo,
                    ordostoid
                FROM
                    ordem_servico_agenda
                INNER JOIN
                    ordem_servico ON (ordoid = osaordoid)
                INNER JOIN
                    os_tipo ON (ostoid = ordostoid)
                WHERE
                    ordoid = ".intval($idOrdemServico)."";

        if (!$rs = pg_query($this->conn, $sql)) {
            throw new Exception('Houve um erro ao buscar OS adicional no banco de dados. ' .  pg_last_error($this->conn));
        }

        while ($tupla = pg_fetch_object($rs)) {
            $retorno[] = $tupla;
        }


        return $retorno;
    }

    public function cancelarRedirecionamento($idOrdemServico)
    {
        $sql = "UPDATE ordem_servico SET ordrepoid_direcionado = NULL WHERE ordoid = ". floatval($idOrdemServico);

		if (!$query = pg_query($this->conn, $sql)) {
			throw new Exception('#0014');
		}
        return true;
    }

    public function getNomeRepresentante($idOrdemServico)
    {
        $sql = "SELECT
                  repnome
                FROM
                  representante
                INNER JOIN
                    ordem_servico ON (ordrepoid_direcionado = repoid)
                WHERE
                  ordoid = ". floatval($idOrdemServico);

        if (!$query = pg_query($this->conn, $sql)) {
			throw new Exception('Houve um erro buscar o nome do representante no banco de dados.');
		}
        return pg_num_rows($query) ? pg_fetch_array($query, 0, PGSQL_ASSOC) : array();
    }


    public function buscarIdAgendamentos($idOrdemServicos)
    {
        $sql = "SELECT
                  ot.ostgrupo,
                  osaid_atividade
                FROM
                  ordem_servico AS os
                  INNER JOIN ordem_servico_agenda AS osa ON (os.ordoid = osa.osaordoid)
                  INNER JOIN os_tipo AS ot ON (os.ordostoid = ot.ostoid)
                WHERE
                  osa.osaexclusao IS NULL AND
                  os.ordoid IN (".  implode(",", $idOrdemServicos).")";

        if (!$query = pg_query($this->conn, $sql)) {
    		throw new Exception('Erro ao buscar os ID dos agendamentos.');
    	}
        $resultado = array();
        if (pg_num_rows($query)) {
            while ($dados = pg_fetch_assoc($query)) {
                $resultado[$dados['ostgrupo']] = $dados['osaid_atividade'];
            }
        }
    	return $resultado;
    }

    /**
     * Recuperar os dados para enviar email e SMS
     *
     * @param int $ordoid
     * @throws Exception
     * @return multitype:
     */
    public function getDadosEmailSms($ordoid){

    	$sql =" SELECT osecnome, osecemail, oscccelular
     			  FROM ordem_servico_email_contato
             LEFT JOIN ordem_servico_celular_contato ON osccordoid = osecordoid
                 WHERE osecordoid = $ordoid " ;

    	if (!$query = pg_query($this->conn, $sql)) {
    		throw new Exception('Erro ao recuperar dados de Email e SMS.');
    	}
    	return pg_num_rows($query) ? pg_fetch_array($query, 0, PGSQL_ASSOC) : array();
    }



    /**
     * Recupera tipo da OS
     *
     * @param unknown $ordoid
     * @throws Exception
     * @return multitype:
     */
    public function getTipoOS($ordoid){

    	$sql =" SELECT ordconnumero
			      FROM ordem_servico
			INNER JOIN os_tipo ON ordostoid = ostoid
		         WHERE ordoid = $ordoid
    			   AND ostoid = 3 --RETIRADA  " ;

    	if (!$query = pg_query($this->conn, $sql)) {
    		throw new Exception('Erro ao recuperar dados do tipo da OS.');
    	}

    	return pg_num_rows($query) ? pg_fetch_all($query) : array();

    }


    public function getTipoRepresentante($idRepresentante)
    {
        $sql = "SELECT
                  r.repoid,
                  r.repnome,
                  r.reptiproid,
                  INITCAP(r.repcontato) AS repcontato,
                  r.repcontato_ddd,
                  REGEXP_REPLACE(r.repcontato_fone, '[^0-9]', '', 'gi') AS repcontato_fone,
                  cl.clcoid,
                  cl.clcnome,
                  cl.clcestoid,
                  cl.clcuf_sg,
                  er.endvrua,
                  er.endvnumero,
                  INITCAP(er.endvcomplemento) AS endvcomplemento,
                  INITCAP(er.endvponto_referencia) AS endvponto_referencia,
                  er.endvbairro,
                  er.endvcep,
                  er.endvddd,
                  REGEXP_REPLACE(er.endvfone, '[^0-9]', '', 'gi') AS endvfone
                FROM
                  representante AS r
                  INNER JOIN endereco_representante AS er ON (r.repoid = er.endvrepoid)
                  INNER JOIN correios_localidades AS cl ON (er.endvcidade = cl.clcnome AND er.endvuf = cl.clcuf_sg)
                WHERE
                  r.repoid = ".intval($idRepresentante)."";

        if (!$query = pg_query($this->conn, $sql)) {
			throw new Exception('Houve um erro ao tentar buscar o tipo de representante no banco de dados.');
		}
        return pg_num_rows($query) ? pg_fetch_array($query, 0, PGSQL_ASSOC) : array();
    }

   /**
    * Recupera o número da OS de instalação vinculada com o mesmo contrato de uma OS de retirada
    *
    * @param int $contrato
    * @throws Exception
    * @return multitype:
    */
    public function getOSInstalacao($contrato){

    	$sql = "SELECT ordoid
			      FROM ordem_servico AS os
			INNER JOIN ordem_servico_agenda ON osaordoid = os.ordoid
			INNER JOIN os_tipo ON os.ordostoid = ostoid
		         WHERE os.ordconnumero = $contrato
    			   AND ostoid in (2,9)  -- Reinstalação / Reinstalação a cobrar
    			   AND osaexclusao IS NULL -- Se tiver data é porque foi finalizada ou excluída
                   AND os.ordfrota IS NULL OR os.ordfrota = FALSE /*Não pode ser uma OS de frota*/
                   AND os.ordstatus = ANY(string_to_array((SELECT pcsidescricao AS example
				           		                             FROM parametros_configuracoes_sistemas_itens
									                        WHERE pcsipcsoid = 'SMART_AGENDA'
									                          AND pcsioid = 'STATUS_OS_PESQUISA'),',')::integer[]) " ;

    	if (!$query = pg_query($this->conn, $sql)) {
    		throw new Exception('Erro ao  recuperar dados da OS de instalaçãos.');
    	}

    	return pg_num_rows($query) ? pg_fetch_all($query) : array();
    }

    /**
     * Retorna a malhor data para agendamento
     * @param  string $cidade nome da cidade
     * @return array
     */
    public function getMelhorData($cidade)
    {
      $melhorData = array();

      $sql = "SELECT
                osarepoid,
                osadata
              FROM ordem_servico_agenda
              INNER JOIN correios_localidades
              ON osaclcoid = clcoid
              AND clcnome ilike '$cidade'
              INNER JOIN endereco_representante
              ON osarepoid = endvrepoid
              AND endvcidade NOT ilike '$cidade'
              WHERE osadata >= NOW()
              AND osaexclusao IS NULL
              GROUP BY osarepoid,osadata";

      $rs = pg_query($this->conn,$sql);

      if(pg_num_rows($rs) > 0) {
        while ($row = pg_fetch_assoc($rs)) {
          $melhorData[$row['osarepoid']][] = strtotime($row['osadata']);
        }
      }

      return $melhorData;
    }


    /**
     * Bus Motivos da Ordem de Servico
     * @return array
     */
    public function getMotivosNoShow(){

    	$retorno = array();

    	$sql = "SELECT
					omnoid,
					omndescricao,
					omnid_ofsc
				FROM
					os_motivo_noshow
				WHERE
					omnexclusao IS NULL
				ORDER BY
					omndescricao ASC";

		$rs = pg_query($this->conn,$sql);

		while($registro = pg_fetch_object($rs)){
            $retorno[$registro->omnoid] = $registro;
        }

        return $retorno;
    }

    public function verificaSituacaoAgendamento($idOrdemServicos) {

        $retorno = array();

        $sql = "
            SELECT
                osaordoid,
                osadata
            FROM
                ordem_servico_agenda
            INNER JOIN
                ordem_servico ON (ordoid = osaordoid)
            WHERE
                osaordoid IN (" . implode(',', $idOrdemServicos) . ")
            AND
                osaexclusao IS NULL";

        if($rs = pg_query($this->conn, $sql)) {
            while ($tupla = pg_fetch_object($rs)) {
                $retorno[] = $tupla;
            }
        }

        return $retorno;

    }

}
