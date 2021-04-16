<?php
/**
 * @file SmartAgendaDAO.php
 */

 require_once _MODULEDIR_ ."/SmartAgenda/DAO/DAO.php";

class SmartAgendaDAO extends DAO {

	public $ws_company;
	public $ws_login;
	public $ws_password;
	public $ws_api;

	public function getDadosAcesso(){

		if($this->ws_api == ''){
			throw new Exception("Informe o nome da API para recuperar os dados de acesso.");
		}

		$sql = "  SELECT pcsidescricao, pcsioid
			        FROM parametros_configuracoes_sistemas
			  INNER JOIN parametros_configuracoes_sistemas_itens ON pcsoid = pcsipcsoid
			       WHERE pcsdt_exclusao IS NULL
			         AND pcsidt_exclusao IS NULL
			         AND pcsoid = 'SMART_AGENDA'
			         AND (   pcsioid = '".trim($this->ws_company)."'
			         	  OR pcsioid = '".trim($this->ws_login)."'
			         	  OR pcsioid = '".trim($this->ws_password)."'
			         	  OR pcsioid = '".trim($this->ws_api)."'
			         	  ) ";

		$result = $this->executarQuery($sql);

		if (pg_num_rows($result) > 0) {
			return pg_fetch_all($result);
		}

	}

    public function gravaLogComunicacao($slcslctoid,$envRequest,$envResponse,$slchora_request,$slcusuoid_inclusao) {

        $timestampResponse = date('Y-m-d H:i:s') . substr((string)microtime(), 1, 8);

        $sql = "INSERT INTO
                    smartagenda_log_comunicacao
                    (
                        slcslctoid,
                        slcusuoid_inclusao,
                        slcrequest,
                        slchora_request,
                        slcresponse,
                        slchora_response
                    )
                    VALUES
                    (
                        ".intval($slcslctoid).",
                        ".intval($slcusuoid_inclusao).",
                        '".addslashes(utf8_decode($envRequest))."',
                        '".$slchora_request."',
                        '".addslashes($envResponse)."',
                        '".$timestampResponse."'
                    );";

        return pg_query($this->conn,$sql);
    }

    public function servicoExecutante($nomeServico) {

        $retorno = null;

        $sql = "SELECT
                    slctoid
                FROM
                    smartagenda_log_comunicacao_tipo
                WHERE
                    slctusuoid_exclusao IS NULL
                AND
                    slctdt_exclusao IS NULL
                AND
                    slctdescricao = '" .$nomeServico. "'";

        $result = $this->executarQuery($sql);

        if(pg_num_rows($result) > 0){
            $res = pg_fetch_object($result);
            $retorno = (int) $res->slctoid;
        }

        return $retorno;
    }

    public function getParametroSmartAgenda($parametro, $default = null) {

        $sql = "SELECT
                  pcsidescricao
                FROM
                  parametros_configuracoes_sistemas_itens
                WHERE
                  pcsipcsoid = 'SMART_AGENDA'
                AND
                  pcsioid = '{$parametro}'";

        $result = $this->executarQuery($sql);

        if (pg_num_rows($result)) {
            $data = pg_fetch_array($result, 0, PGSQL_ASSOC);
            return $data['pcsidescricao'];
        }
        return $default;
    }


    /**
     * Pega lista de parБmetros do SmartAgenda
     * @param  [string] $parametros [parametros separados por virgula]
     * @return [type]                 [description]
     */
    public function parametrosSmartAgenda($parametros = null) {
      $retorno = array();

      $sql = "
          SELECT
            pcsioid,pcsidescricao
          FROM
            parametros_configuracoes_sistemas_itens
          WHERE
            pcsipcsoid = 'SMART_AGENDA'
          AND
            pcsidt_exclusao IS NULL ";

      if(!is_null($parametros)) {
        $sql .= " AND pcsioid IN (".$parametros.") ";
      }

      if($rs = pg_query($this->conn, $sql)) {
          while ($linha = pg_fetch_assoc($rs)) {
              $retorno[$linha['pcsioid']] = $linha['pcsidescricao'];
          }
      } else {
        throw new Exception('Houve um erro ao buscar parБmetros do SmartAgenda no banco.');
      }

      return $retorno;
    }

    public function getWorkSillContrato($connumero) {

        $dados = array();

        $sql = "SELECT
                eqc.eqcecgoid,
                eqc.eqcohcoid
              FROM
                contrato con
              INNER JOIN
                equipamento_classe eqc ON (con.coneqcoid = eqc.eqcoid)
              WHERE
                connumero = " . $connumero;

        $rs =  $this->executarQuery($sql);

        if (pg_num_rows($rs) > 0) {
            $dados = pg_fetch_object($rs);
        }

        return $dados;

    }


    public function getWorkSkillOrdemServico($ordoid) {

        $dados = array();

        $sql = "SELECT DISTINCT
                    oti.otiohcoid
                FROM
                    ordem_servico_item osi
                INNER JOIN
                    os_tipo_item oti ON (osi.ositotioid = oti.otioid)
                WHERE
                    ositordoid = " . intval($ordoid) ."
                AND
                    osi.ositexclusao IS NULL
                AND
                    osi.ositstatus != 'X'
                AND
                    oti.otiohcoid IS NOT NULL";

        $rs =  $this->executarQuery($sql);

        if(pg_num_rows($rs) > 0) {
            while ($row = pg_fetch_assoc($rs)) {
              if ($row['otiohcoid'] != '') {
                $dados[] = $row['otiohcoid'];
              }
            }
        }

        return $dados;

    }

    public function getWorkSkillGrupoClasse($idClasseGrupo) {

        $dados = array();

        $sql = "SELECT
                     DISTINCT occdescricao AS descricao
                FROM
                    ofsc_capacidade_configuracao
                INNER JOIN
                    ofsc_capacidade_categoria on (ofsc_capacidade_categoria.occoid = occoccoid)
                WHERE
                    (occecgoid = $idClasseGrupo) ";

          $rs =  $this->executarQuery($sql);

          if(pg_num_rows($rs) == 0) {
            return $dados;
          } else {
            while ($row = pg_fetch_assoc($rs)) {
              $dados[] = $row['descricao'];
            }
          }

       return $dados;

    }
   

    public function getWorkSkillsItens($filtroHabilidades){

        $dados = array();

        $sql = "SELECT
            DISTINCT occdescricao AS descricao
          FROM
            ofsc_capacidade_configuracao
          INNER JOIN
            ofsc_capacidade_categoria on (ofsc_capacidade_categoria.occoid = occoccoid)
          WHERE
                $filtroHabilidades ";

      $rs =  $this->executarQuery($sql);

      if(pg_num_rows($rs) == 0) {
        return $dados;
      } else {
        while ($row = pg_fetch_assoc($rs)) {
          $dados[] = $row['descricao'];
        }
      }

      return $dados;

    }

    public function getOrdenacaoPrestadores($modalidade, $tipo){

        $sql = "SELECT
                  ott.ottchave,
                  ott.ottdescricao,
                  otp.otpprioridade
                FROM
                  ordenacao_timeslot AS ot
                  INNER JOIN ordenacao_timeslot_prioridade AS otp ON(otp.otpotsoid = ot.otsoid)
                  INNER JOIN ordenacao_timeslot_tipo AS ott ON(otp.otpottoid = ott.ottoid)
                WHERE
                  ot.otsmodalidade = '".$modalidade."' AND
                  ot.otstipo = '".$tipo."'
                ORDER BY
                  otp.otpprioridade";

        $rs =  $this->executarQuery($sql);

        return pg_num_rows($rs) ? pg_fetch_all($rs) : array();
    }

    public function getBairroMapeado($idBairro, $idCidade, $idEstado) {

      $retorno = '';

      $idBairro = (int)$idBairro;
      $idCidade = (int)$idCidade;
      $idEstado = (int)$idEstado;

      $sql = "SELECT
                  cmbcbaoid
                FROM
                  cidade_mapeada_bairro
                WHERE
                  cmbdt_exclusao IS NULL AND
                  cmbestoid = $idEstado AND
                  cmbclcoid = $idCidade AND
                  cmbcbaoid = $idBairro";

     $rs =  $this->executarQuery($sql);

      if($rs) {
        if(pg_num_rows($rs) > 0) {
          $retorno = str_pad($idBairro, 8, '0', STR_PAD_LEFT);
        } else {
          $retorno = str_pad('', 8, '0', STR_PAD_LEFT);
        }
      }

      return $retorno;
    }


    public function recuperarDadosEnderecoPrestador($repoid) {

        $sql = "SELECT
                    TRIM(endvuf) AS xa_state_code,
                    endvcomplemento AS xa_address_2,
                    TRANSLATE(TRIM(endvbairro), 'АЮЦБИЙМЛСТУЗЭГаюцбиймлстузэг','aaaaeeiiooouucAAAAEEIIOOUUC') AS xa_neighborhood_name,
                    TRANSLATE(TRIM(endvcidade), 'АЮЦБИЙМЛСТУЗЭГаюцбиймлстузэг','aaaaeeiiooouucAAAAEEIIOOUUC') AS city,
                    endvponto_referencia AS xa_address_reference,
                    (endvrua || ', ' || endvnumero) AS address,
                    TRIM(endvuf) AS state,
                    endvcep AS zip
                FROM
                    endereco_representante
                WHERE
                    endvrepoid = " . intval($repoid);

        $rs =  $this->executarQuery($sql);

        return (pg_num_rows($rs) > 0) ? pg_fetch_all($rs) : array();
    }

    public function getDadosWorkZonePrestador($uf, $cidade, $bairro){

        $sql = "SELECT
                    LPAD(clcoid::TEXT, 8, '0') AS xa_city_code,
                    COALESCE((LPAD(cbaoid::TEXT, 8, '0')),'00000000') AS xa_neighborhood_code,
                    clcestoid AS id_estado,
                    clcoid AS id_cidade,
                    cbaoid AS id_bairro
                FROM
                    correios_localidades
                LEFT JOIN
                    correios_bairros ON (cbaclcoid = clcoid AND cbauf_sg = '".$uf."' AND cbanome ILIKE '".$bairro."')
                WHERE
                    clcuf_sg = '".$uf."'
                AND
                    clcnome ='".$cidade."'";

        $rs =  $this->executarQuery($sql);

        return (pg_num_rows($rs) > 0) ? pg_fetch_all($rs) : array();

    }
}


?>