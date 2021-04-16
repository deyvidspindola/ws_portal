<?php

/**
 * Classe GesMetaDAO.
 * Camada de modelagem de dados.
 *
 * @package  Gestao
 * @author   Jose Fernando Carlos <jose.carlos@meta.com.br>
 * 
 */
class GesMetaDAO {

    /**
     * Conexão com o banco de dados
     * @var resource
     */
    private $conn;
    private $isAjax;

    /**
     * Mensagem de erro para o processamentos dos dados
     * @const String
     */

    const MENSAGEM_ERRO_PROCESSAMENTO = "Houve um erro no processamento dos dados.";

    public function __construct($conn) {
//Seta a conexão na classe
        $this->conn = $conn;

        $this->isAjax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest' ? true : false;
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
			gmeoid,
			gmenome 	AS meta,
			gmeano  	AS ano,
			gmecodigo 	AS codigo,
			nm_usuario  AS responsavel,

			CASE 	WHEN gmetipo = 'D' THEN 'Diário'
					WHEN gmetipo = 'M' THEN 'Mensal'
					WHEN gmetipo = 'B' THEN 'Bimestral'
					WHEN gmetipo = 'T' THEN 'Trimestral'
					WHEN gmetipo = 'Q' THEN 'Quadrimestral'
					WHEN gmetipo = 'S' THEN 'Semestral'
					WHEN gmetipo = 'A' THEN 'Anual'
			END 		AS tipo,

			CASE 	WHEN gmemetrica = 'V' THEN 'Vlr'
					WHEN gmemetrica = 'P' THEN '%'
					WHEN gmemetrica = 'M' THEN '$'
			END 		AS metrica,

			gmeprecisao	AS precisao,

			CASE 	WHEN gmedirecao = 'I' THEN 'Inversamente'
					WHEN gmedirecao = 'D' THEN 'Diretamente'
			END 		AS direcao,


			gmelimite_superior AS limite_superior,
			gmelimite 		   AS limite,
			gmelimite_inferior AS limite_inferior,

			gmepeso 	AS peso

		FROM
			gestao_meta
		LEFT JOIN
			usuarios ON (usufunoid = gmefunoid_responsavel)
		LEFT JOIN
			perfil_rh ON (prhoid = usucargooid)
		WHERE
			 gmedt_exclusao IS NULL 
        AND
            dt_exclusao IS NULL ";

        if (isset($parametros->filtro_gmeano) && trim($parametros->filtro_gmeano) != '') {

            $sql .= " AND
				gmeano = '" . pg_escape_string($parametros->filtro_gmeano) . "' ";
        }

        if (isset($parametros->filtro_gmeoid) && trim($parametros->filtro_gmeoid) != '') {

            $sql .= " AND
				gmeoid = " . intval($parametros->filtro_gmeoid) . " ";
        }

        if (isset($parametros->filtro_cargo) && trim($parametros->filtro_cargo) != '') {

            $sql .= " AND
				prhoid = " . intval($parametros->filtro_cargo) . " ";
        }

        if (isset($parametros->gmefunoid_responsavel) && trim($parametros->gmefunoid_responsavel) != '') {

            $sql .= " AND
				gmefunoid_responsavel = " . intval($parametros->gmefunoid_responsavel) . " ";
        }

        if (!$rs = pg_query($this->conn, $sql)) {
            throw new ErrorException(self::MENSAGEM_ERRO_PROCESSAMENTO);
        }

        while ($row = pg_fetch_object($rs)) {

            $row->limite_superior = number_format($row->limite_superior, $row->precisao, ',', '.');
            $row->limite = number_format($row->limite, $row->precisao, ',', '.');
            $row->limite_inferior = number_format($row->limite_inferior, $row->precisao, ',', '.');

            $retorno[] = $row;
        }

        return $retorno;
    }

    public function buscarIndicadores($tipo = "") {

        $retorno = array();

        if (trim($tipo) == '') {
            return $retorno;
        }

        $sql = "SELECT 
					gmicodigo, gminome
			FROM 
					gestao_meta_indicadores
			WHERE 
					gmistatus = 'A'
			AND 
					gmitipo = '" . pg_escape_string($tipo) . "' ";

        if (!$rs = pg_query($this->conn, $sql)) {
            throw new ErrorException(self::MENSAGEM_ERRO_PROCESSAMENTO);
        }

        $i = 0;
        while ($row = pg_fetch_object($rs)) {

            $retorno[$i]['id'] = '[' . trim($row->gmicodigo) . ']';
            if ($this->isAjax) {
                $retorno[$i]['label'] = utf8_encode($row->gminome) . ' - ' . $row->gmicodigo;
            } else {
                $retorno[$i]['label'] = $row->gminome . ' - ' . $row->gmicodigo;
            }

            $i++;
        }

        return $retorno;
    }

    public function buscarNomeMetas($ano) {

        $ano = trim($ano) != '' ? $ano : date('Y');

        $sql = "SELECT 
					gmeoid,
					gmenome 
			FROM 
					gestao_meta
			WHERE 
					gmeano = '" . $ano . "' 
			AND 
					gmedt_exclusao IS NULL 
			ORDER BY 
					gmenome ASC";

        if (!$rs = pg_query($this->conn, $sql)) {
            throw new ErrorException(self::MENSAGEM_ERRO_PROCESSAMENTO);
        }

        $i = 0;
        while ($row = pg_fetch_object($rs)) {
            $retorno[$i]['id'] = $row->gmeoid;

            if ($this->isAjax) {
                $retorno[$i]['label'] = utf8_encode($row->gmenome);
            } else {
                $retorno[$i]['label'] = $row->gmenome;
            }

            $i++;
        }

        return $retorno;
    }

    public function buscarCargos() {

        $sql = "SELECT
                	prhoid,
                	prhperfil
            FROM
                	perfil_rh
            WHERE
               		prhexclusao IS NULL
            ORDER BY
	                prhperfil ASC";

        if (!$rs = pg_query($this->conn, $sql)) {
            throw new ErrorException(self::MENSAGEM_ERRO_PROCESSAMENTO);
        }

        $i = 0;
        while ($row = pg_fetch_object($rs)) {
            $retorno[$i]['id'] = $row->prhoid;

            if ($this->isAjax) {
                $retorno[$i]['label'] = utf8_encode($row->prhperfil);
            } else {
                $retorno[$i]['label'] = $row->prhperfil;
            }

            $i++;
        }

        return $retorno;
    }

    public function buscarFuncionarios($meta = '', $cargo = '', $funcionario = '', $ano = '') {

        $sql = " SELECT 
					DISTINCT(usufunoid),
					nm_usuario 
			 FROM
					usuarios 
			 INNER JOIN 
			 		funcionario ON (funoid = usufunoid) 
			 INNER JOIN 
			 		gestao_meta_arvore ON (gmafunoid = usufunoid)";

        if (trim($meta) != '') {

            $sql .= "INNER JOIN 
					gestao_meta ON (gmefunoid_responsavel = usufunoid) ";
        }

        if (trim($cargo) != '') {

            $sql .= "INNER JOIN
					perfil_rh   ON (prhoid = usucargooid) ";
        }


        $sql .= "WHERE 
			 		1 = 1 
                AND
                    dt_exclusao IS NULL ";

        if (trim($meta) != '') {

            $sql .= "AND 
						gmeoid = " . intval($meta) . " ";
        }

        if (trim($cargo) != '') {

            $sql .= "AND 
						prhoid = " . intval($cargo) . " ";
        }


        if (trim($funcionario) != '') {

            $sql .= "AND 
						usufunoid != " . intval($funcionario) . " ";
        }


        if (trim($ano) != '') {

            $sql .= "AND 
						gmaano = " . $ano . " ";
        }

        $sql .=" ORDER BY 
					nm_usuario ASC ";


        if (!$rs = pg_query($this->conn, $sql)) {
            throw new ErrorException(self::MENSAGEM_ERRO_PROCESSAMENTO);
        }

        $i = 0;
        while ($row = pg_fetch_object($rs)) {
            $retorno[$i]['id'] = $row->usufunoid;

            if ($this->isAjax) {
                $retorno[$i]['label'] = utf8_encode($row->nm_usuario);
            } else {
                $retorno[$i]['label'] = $row->nm_usuario;
            }

            $i++;
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
					gmeoid, 
					gmeano, 
					gmecodigo, 
					gmenome, 
					gmefunoid_responsavel, 
					gmepeso, 
					gmeprecisao, 
					gmedirecao, 
					gmelimite_superior, 
					gmelimite, 
					gmelimite_inferior, 
					gmemetrica, 
					gmetipo, 
					gmeformula, 
					gmedt_exclusao
				FROM 
					gestao_meta
				WHERE 
					gmeoid =" . intval($id) . "";

        if (!$rs = pg_query($this->conn, $sql)) {
            throw new ErrorException(self::MENSAGEM_ERRO_PROCESSAMENTO);
        }

        if (pg_num_rows($rs) > 0) {
            $retorno = pg_fetch_object($rs);
        }

        return $retorno;
    }

    public function pesquisarMetaCopiada($meta) {

        $retorno = new stdClass();

        $sql = "SELECT 
						gmecodigo
					FROM 
						gestao_meta
					WHERE 
						gmeoid = " . intval($meta) . "";

        if (!$rs = pg_query($this->conn, $sql)) {
            throw new ErrorException(self::MENSAGEM_ERRO_PROCESSAMENTO);
        }

        if (pg_num_rows($rs) > 0) {
            $retorno = pg_fetch_object($rs);
        }

        return $retorno;
    }

    public function pesquisarMetaOriginal($codigo) {

        $retorno = new stdClass();

        $sql = "SELECT 
						*
					FROM 
						gestao_meta
					WHERE 
						gmecodigo = '" . $codigo . "'
			LIMIT 1";

        if (!$rs = pg_query($this->conn, $sql)) {
            throw new ErrorException(self::MENSAGEM_ERRO_PROCESSAMENTO);
        }

        if (pg_num_rows($rs) > 0) {
            $retorno = pg_fetch_object($rs);
        }

        return $retorno;
    }

    public function pesquisarMetaCopias($codigo) {

        $sql = "SELECT 
				count(*) AS qtd 
			FROM 
				gestao_meta 
			WHERE 
				gmecodigo ILIKE '" . trim($codigo) . "%'";

        if (!$rs = pg_query($this->conn, $sql)) {
            throw new ErrorException(self::MENSAGEM_ERRO_PROCESSAMENTO);
        }

        return pg_fetch_result($rs, 0, 'qtd');
    }

    /**
     * Responsável para inserir um registro no banco de dados.
     * @param stdClass $dados Dados a serem gravados
     * @return boolean
     * @throws ErrorException
     */
    public function inserir(stdClass $dados) {


        $dados->gmelimite = str_replace('.', '', $dados->gmelimite);
        $dados->gmelimite = str_replace(',', '.', $dados->gmelimite);

        $dados->gmelimite_superior = str_replace('.', '', $dados->gmelimite_superior);
        $dados->gmelimite_superior = str_replace(',', '.', $dados->gmelimite_superior);

        $dados->gmelimite_inferior = str_replace('.', '', $dados->gmelimite_inferior);
        $dados->gmelimite_inferior = str_replace(',', '.', $dados->gmelimite_inferior);

        $dados->meta_compartilhamento = trim($dados->meta_compartilhamento) ? trim(rtrim($dados->meta_compartilhamento, ',')) : '';


        $sql = "INSERT INTO
					gestao_meta
					(
						gmeano,
						gmecodigo,
						gmenome,
						gmefunoid_responsavel,
						gmepeso,
						gmeprecisao,
						gmedirecao,
						gmelimite_superior,
						gmelimite,
						gmelimite_inferior,
						gmemetrica,
						gmetipo,
						gmeformula
					)
				VALUES
					(
						'" . pg_escape_string($dados->gmeano) . "',
						'" . pg_escape_string($dados->gmecodigo) . "',
						'" . pg_escape_string($dados->gmenome) . "',
						" . intval($dados->gmefunoid_responsavel) . ",
						" . intval($dados->gmepeso) . ",
						" . intval($dados->gmeprecisao) . ",
						'" . pg_escape_string($dados->gmedirecao) . "',
						" . floatval($dados->gmelimite_superior) . ",
						" . floatval($dados->gmelimite) . ",
						" . floatval($dados->gmelimite_inferior) . ",
						'" . pg_escape_string($dados->gmemetrica) . "',
						'" . pg_escape_string($dados->gmetipo) . "',
						'" . pg_escape_string($dados->gmeformula) . "'
					)
				RETURNING gmeoid";

        if (!$rs = pg_query($this->conn, $sql)) {
            throw new ErrorException(self::MENSAGEM_ERRO_PROCESSAMENTO);
        }


        if (pg_affected_rows($rs) > 0) {

            //realiza a persistência do compartilhamento de metas.
            $metaId = pg_fetch_result($rs, 0, 'gmeoid');
            $funcionariosCompartilhamento = array_unique(explode(',', trim($dados->meta_compartilhamento)));


            if (count($funcionariosCompartilhamento) > 0) {
                foreach ($funcionariosCompartilhamento as $key => $value) {
                    if (trim($value) != '') {
                        $this->compatilharMeta($metaId, $value);
                    }
                }
            }
        }


        $_SESSION['cache_arvore']['atualizado'] = strtotime(date('Y-m-d H:i:s'));
        return $metaId;
    }

    private function compatilharMeta($metaId, $funcionarioId) {

        $sql = "INSERT INTO 
					gestao_meta_compartilhada
					(
						gmcgmeoid, 
						gmcfunoid
					)
				VALUES 
					(
						" . intval($metaId) . ",
						" . intval($funcionarioId) . "
					)";

        if (!$rs = pg_query($this->conn, $sql)) {            
            throw new ErrorException(self::MENSAGEM_ERRO_PROCESSAMENTO);
        }
    }

    public function buscarCompartilhamento($metaId) {

        $sql = "SELECT 
					DISTINCT(gmcfunoid),
					nm_usuario				
			FROM 
					gestao_meta_compartilhada
			INNER JOIN 
					usuarios ON (usufunoid = gmcfunoid)
			WHERE 
					gmcgmeoid = " . intval($metaId) . "
            AND
                    dt_exclusao IS NULL
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
     * Responsável por atualizar os registros 
     * @param stdClass $dados Dados a serem gravados
     * @return boolean
     * @throws ErrorException
     */
    public function atualizar(stdClass $dados) {

        $dados->gmelimite = str_replace('.', '', $dados->gmelimite);
        $dados->gmelimite = str_replace(',', '.', $dados->gmelimite);

        $dados->gmelimite_superior = str_replace('.', '', $dados->gmelimite_superior);
        $dados->gmelimite_superior = str_replace(',', '.', $dados->gmelimite_superior);

        $dados->gmelimite_inferior = str_replace('.', '', $dados->gmelimite_inferior);
        $dados->gmelimite_inferior = str_replace(',', '.', $dados->gmelimite_inferior);

        $dados->meta_compartilhamento = trim($dados->meta_compartilhamento) ? trim(rtrim($dados->meta_compartilhamento, ',')) : '';


        $sql = "UPDATE
					gestao_meta
				SET					
					gmenome = '" . pg_escape_string($dados->gmenome) . "',
					gmefunoid_responsavel = " . intval($dados->gmefunoid_responsavel) . ",
					gmepeso = " . intval($dados->gmepeso) . ",
					gmeprecisao = " . intval($dados->gmeprecisao) . ",
					gmedirecao = '" . pg_escape_string($dados->gmedirecao) . "',
					gmelimite_superior = " . floatval($dados->gmelimite_superior) . ",
					gmelimite = " . floatval($dados->gmelimite) . ",
					gmelimite_inferior = " . floatval($dados->gmelimite_inferior) . ",
					gmemetrica = '" . pg_escape_string($dados->gmemetrica) . "',
					gmeformula = '" . pg_escape_string($dados->gmeformula) . "'
				WHERE 
					gmeoid = " . $dados->gmeoid . "";

        if (!pg_query($this->conn, $sql)) {
            throw new ErrorException(self::MENSAGEM_ERRO_PROCESSAMENTO);
        }

        //deleta tudo do compatilhamento daquela meta para adicionar novamente
        $sql = "DELETE FROM gestao_meta_compartilhada WHERE gmcgmeoid = " . intval($dados->gmeoid) . "";
        
        if (!pg_query($this->conn, $sql)) {
            
            throw new ErrorException(self::MENSAGEM_ERRO_PROCESSAMENTO);
        }

        $funcionariosCompartilhamento = array_unique(explode(',', $dados->meta_compartilhamento));
        
        if (count($funcionariosCompartilhamento) > 0) {
            foreach ($funcionariosCompartilhamento as $key => $value) {
                if (trim($value) != '') {
                    $this->compatilharMeta($dados->gmeoid, $value);
                }
            }
        }

        $_SESSION['cache_arvore']['atualizado'] = strtotime(date('Y-m-d H:i:s'));
        return true;
    }

    public function verificarIndicador($meta, $indicador) {

        $sql = "SELECT 
				COUNT(1) AS qtd
			FROM 
				gestao_meta_indicadores_meta
			INNER JOIN 
				gestao_meta_indicadores ON (gmioid = gimgmioid)
			WHERE 
				gimgmeoid = " . intval($meta) . "
			AND 
				TRIM(gmicodigo) = '" . pg_escape_string(trim($indicador)) . "'
			AND 
				gmitipo_indicador = 'I'
			AND 
				gimvalor_realizado IS NOT NULL";

        if (!$rs = pg_query($this->conn, $sql)) {
            throw new ErrorException(self::MENSAGEM_ERRO_PROCESSAMENTO);
        }

        $qtd = pg_fetch_result($rs, 0, 'qtd');

        if (intval($qtd) != 0) {
            return true;
        }

        return false;
    }

    public function salvarPlanoEacao($plano, $acao) {

        $sqlPlano = "INSERT INTO 
						gestao_meta_plano_acao
						(
							gplgmeoid,
							gplfunoid_responsavel,
							gplnome,
							gpldt_inicio,
							gpldt_fim,
							gplstatus,
							gplcompartilhar
						)
					VALUES 
						(
							" . intval($plano->gplgmeoid) . ",
							" . intval($plano->gplfunoid_responsavel) . ",
							'" . pg_escape_string($plano->gplnome) . "',
							'" . pg_escape_string($plano->gpldt_inicio) . "',
							'" . pg_escape_string($plano->gpldt_fim) . "',
							'" . pg_escape_string($plano->gplstatus) . "',
							" . $plano->gplcompartilhar . "
						)
					RETURNING gploid";

        if (!$rs = pg_query($this->conn, $sqlPlano)) {
            throw new ErrorException(self::MENSAGEM_ERRO_PROCESSAMENTO);
        }

        if (pg_affected_rows($rs) > 0) {

            $planoId = pg_fetch_result($rs, 0, 'gploid');
            $acao->gmagploid = $planoId;


            $sqlAcao = "INSERT INTO 
							gestao_meta_acao
							(
								gmafunoid_responsavel,
								gmagploid,
								gmanome,
								gmatipo,
								gmadt_inicio_previsto,
								gmadt_fim_previsto,
								gmastatus,
								gmapercentual,
								gmacompartilhar
							)
						VALUES
							(
								" . intval($acao->gmafunoid_responsavel) . ",
								" . intval($acao->gmagploid) . ",
								'" . pg_escape_string($acao->gmanome) . "',
								'" . pg_escape_string($acao->gmatipo) . "',
								'" . pg_escape_string($acao->gmadt_inicio_previsto) . "',
								'" . pg_escape_string($acao->gmadt_fim_previsto) . "',
								'" . pg_escape_string($acao->gmastatus) . "',
								" . intval($acao->gmapercentua) . ",
								" . $acao->gmacompartilhar . "

							)";

            if (!$rs = pg_query($this->conn, $sqlAcao)) {
                throw new ErrorException(self::MENSAGEM_ERRO_PROCESSAMENTO);
            }
        }

        $_SESSION['cache_arvore']['atualizado'] = strtotime(date('Y-m-d H:i:s'));
        return true;
    }

    public function buscarIndicadoresMetas($meta) {

        $retorno = array();

        $sql = "SELECT
				TO_CHAR(gimdata,'DD/MM/YYYY') AS data,
				gmicodigo AS codigo,
				gminome AS nome,
				gimvalor_previsto AS valor_previsto,
				gimvalor_realizado AS valor_realizado,
				gmitipo_indicador AS tipo,
				gmiprecisao AS precisao
			FROM
				gestao_meta_indicadores_meta
			INNER JOIN
				gestao_meta_indicadores ON (gmioid = gimgmioid)
			WHERE
				gimgmeoid = " . intval($meta) . "";

        if (!$rs = pg_query($this->conn, $sql)) {
            throw new ErrorException(self::MENSAGEM_ERRO_PROCESSAMENTO);
        }

        while ($row = pg_fetch_object($rs)) {

            $row->valor_realizado = number_format($row->valor_realizado, $row->precisao, ',', '.');
            $row->valor_previsto = number_format($row->valor_previsto, $row->precisao, ',', '.');

            $retorno[] = $row;
        }

        return $retorno;
    }

    public function buscarRelacionamentosMeta($meta) {

        $sql = "SELECT
				COUNT(gploid) AS plano_acao,
				COUNT(gmcgmeoid) AS meta_compartilhada,
				COUNT(gimgmioid) AS indicadores
			FROM
				gestao_meta
			lEFT JOIN
				gestao_meta_plano_acao ON (gplgmeoid = gmeoid)
			lEFT JOIN
				gestao_meta_compartilhada ON (gmcgmeoid = gmeoid)
			lEFT JOIN
				gestao_meta_indicadores_meta ON (gimgmeoid = gmeoid)
			WHERE
				gmeoid = " . intval($meta) . "";

        if (!$rs = pg_query($this->conn, $sql)) {
            throw new ErrorException(self::MENSAGEM_ERRO_PROCESSAMENTO);
        }

        $qtd_plano_acao = pg_fetch_result($rs, 0, 'plano_acao');
        $qtd_meta_compartilhada = pg_fetch_result($rs, 0, 'meta_compartilhada');
        $qtd_indicadores = pg_fetch_result($rs, 0, 'indicadores');

        if ($qtd_plano_acao != 0 || $qtd_meta_compartilhada != 0 || $qtd_indicadores != 0) {
            return 1;
        }

        return 0;
    }

    public function verificarCodigo($codigo, $ano) {

        $sql = "SELECT
					gmeoid
			FROM 
					gestao_meta
			WHERE 
					gmecodigo = '" . pg_escape_string($codigo) . "'
			AND 
					gmeano = '" . pg_escape_string($ano) . "' ";

        if (!$rs = pg_query($this->conn, $sql)) {
            throw new ErrorException(self::MENSAGEM_ERRO_PROCESSAMENTO);
        }

        if (pg_num_rows($rs) > 0) {
            return false;
        }

        return true;
    }

    public function registroJaExcluido($meta) {

        $sql = "SELECT
					1
			FROM 
					gestao_meta
			WHERE 
					gmedt_exclusao IS NOT NULL 
			AND 
					gmeoid = " . intval($meta) . "
			LIMIT 1";

        if (!$rs = pg_query($this->conn, $sql)) {
            throw new ErrorException(self::MENSAGEM_ERRO_PROCESSAMENTO);
        }

        if (pg_num_rows($rs) > 0) {
            return true;
        }

        return false;
    }

    /**
     * Exclui (UPDATE) um registro da base de dados.
     * @param int $id Identificador do registro
     * @return boolean
     * @throws ErrorException
     */
    public function excluir($id, $logica) {


        if ($logica) {

            $sql = "UPDATE
					gestao_meta
				SET
					gmedt_exclusao = NOW() 
				WHERE
					gmeoid = " . intval($id) . "";
        } else {


            $sql = "DELETE FROM
					gestao_meta
				WHERE
					gmeoid = " . intval($id) . "";
        }

        if (!pg_query($this->conn, $sql)) {
            throw new ErrorException(self::MENSAGEM_ERRO_PROCESSAMENTO);
        }

        $_SESSION['cache_arvore']['atualizado'] = strtotime(date('Y-m-d H:i:s'));
        return true;
    }

    public function buscarMetasExportacao($ano) {

        $retorno = array();

        $sql = "
            SELECT
                gestao_meta.gmecodigo AS codigo_meta,
                gestao_meta.gmenome AS nome_meta,
                gestao_meta.gmeformula AS formula,
                '[' || gestao_meta_indicadores.gmicodigo || ']' AS codigo_indicador,
                funcionario.funnome AS nome_funcionario,
                CASE
                    WHEN gestao_meta_indicadores.gmitipo_indicador = 'M' THEN
                        gimvalor_previsto
                    ELSE
                        gimvalor_realizado
                END AS valor,
                gestao_meta_indicadores.gmitipo_indicador AS tipo_indicador,
                gestao_meta.gmelimite AS limite,
                gestao_meta.gmelimite_superior AS limite_superior,
                gestao_meta.gmelimite_inferior AS limite_inferior,
                TO_CHAR(gestao_meta_indicadores_meta.gimdata, 'MM') AS mes,
                gestao_meta.gmeprecisao AS precisao
            FROM
                gestao_meta_indicadores_meta
                INNER JOIN gestao_meta ON (gmeoid = gimgmeoid)
                INNER JOIN funcionario ON (gmefunoid_responsavel = funoid)
                INNER JOIN gestao_meta_indicadores ON (gmioid = gimgmioid)
            WHERE
                TO_CHAR(gestao_meta_indicadores_meta.gimdata, 'YYYY') = '" . $ano . "'
                AND ( 
                    (gestao_meta_indicadores.gmitipo_indicador = 'M' AND gestao_meta_indicadores_meta.gimvalor_previsto > 0) 
                    OR (gestao_meta_indicadores.gmitipo_indicador = 'I' AND gestao_meta_indicadores_meta.gimvalor_realizado > 0) 
                )

            UNION ALL

			SELECT                
                gestao_meta.gmecodigo AS codigo_meta,
                gestao_meta.gmenome AS nome_meta,                
                gestao_meta.gmeformula AS formula,
                '[' || gestao_meta_indicadores.gmicodigo || ']' AS codigo_indicador,
                funcionario.funnome AS nome_funcionario,
                CASE
                    WHEN gestao_meta_indicadores.gmitipo_indicador = 'M' THEN
                        gimvalor_previsto
                    ELSE
                        gimvalor_realizado
                END AS valor,
                gestao_meta_indicadores.gmitipo_indicador AS tipo_indicador,
                gestao_meta.gmelimite AS limite,
                gestao_meta.gmelimite_superior AS limite_superior,
                gestao_meta.gmelimite_inferior AS limite_inferior,
                TO_CHAR(gestao_meta_indicadores_meta.gimdata, 'MM') AS mes,
                gestao_meta.gmeprecisao AS precisao
            FROM
                gestao_meta_compartilhada
                INNER JOIN gestao_meta_indicadores_meta ON (gimgmeoid = gmcgmeoid)
                INNER JOIN gestao_meta ON (gmeoid = gimgmeoid)
                INNER JOIN funcionario ON (gmcfunoid = funoid)
                INNER JOIN gestao_meta_indicadores ON (gmioid = gimgmioid)
            WHERE
		        gmcfunoid != gmefunoid_responsavel
                AND TO_CHAR(gestao_meta_indicadores_meta.gimdata, 'YYYY') = '" . $ano . "'
                AND ( 
                    (gestao_meta_indicadores.gmitipo_indicador = 'M' AND gestao_meta_indicadores_meta.gimvalor_previsto > 0) 
                    OR (gestao_meta_indicadores.gmitipo_indicador = 'I' AND gestao_meta_indicadores_meta.gimvalor_realizado > 0) 
                )";

        $resultado = pg_query($this->conn, $sql);
        
        if ($resultado && pg_num_rows($resultado) > 0) {
            while ($linha = pg_fetch_object($resultado)) {
                array_push($retorno, $linha);
            }
        }

        return $retorno;
    }

    public function buscarMetasUsuario($idUsuario) {

        $retorno = array();

        $sql = "
            SELECT
                funoid,
                funnome,
                gmeoid,
                gmenome
            FROM
                gestao_meta
                INNER JOIN funcionario ON funoid = gmefunoid_responsavel
                INNER JOIN usuarios ON funoid = usufunoid
            WHERE
                cd_usuario = " . $idUsuario;

        $resultado = pg_query($this->conn, $sql);

        if ($resultado && pg_num_rows($resultado) > 0) {
            while ($linha = pg_fetch_object($resultado)) {
                array_push($retorno, $linha);
            }
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
