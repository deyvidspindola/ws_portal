<?php

/**
 * Classe padrão para DAO
 *
 * @author robson.silva
 */
class CadHorarioPausaItemDAO {

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
     * Construtor da Classe
     */
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

        $sql = "
        		SELECT
                    DISTINCT
					hrpioid,
					gtrnome AS grupo_trabalho,
					motamotivo AS tipo_pausa,
					nm_usuario AS atendente,
					hrpitempo AS tempo,
					(TO_CHAR(hrpihorario_ini, 'HH24:MI') || ' - ' || TO_CHAR(hrpihorario_fim, 'HH24:MI')) AS horario,
					hrpitolerancia AS tolerancia,
                    hrpiatendente
				FROM
					horario_pausa_item
				INNER JOIN
					motivo_pausa ON (motaoid = hrpimotaoid AND motacentral IS TRUE)
				INNER JOIN
        			horario_pausa ON (hrpmotaoid = motaoid AND hrpdt_exclusao IS NULL)
				INNER JOIN
        			grupo_trabalho ON (gtroid = hrpgtroid AND gtrdt_exclusao IS NULL)
        		INNER JOIN
        			grupo_trabalho_usuario ON (gtugtroid = gtroid AND gtuusuoid = hrpiatendente)
        		INNER JOIN
					usuarios ON cd_usuario = hrpiatendente
				WHERE
					hrpidt_exclusao IS NULL
                    ";

        //Grupo de Trabalho
        if (isset($parametros->gtroid) && trim($parametros->gtroid) != '') {

            $sql .= "AND
                        gtroid  = " . intval($parametros->gtroid) . "";
        }

        //Tipo Pausa
        if (isset($parametros->motaoid) && trim($parametros->motaoid) != '') {

            $sql .= "AND
                        motaoid = " . intval($parametros->motaoid) . "";
        }

        //Atendente
        if (isset($parametros->hrpiatendente) && trim($parametros->hrpiatendente) != '') {

            $sql .= "AND
                        hrpiatendente = " . intval($parametros->hrpiatendente) . "";
        }

        //Tempo
        if (isset($parametros->hrpitempo) && trim($parametros->hrpitempo) != '') {

            $sql .= "AND
                        hrpitempo = " . intval($parametros->hrpitempo) . "";
        }

        //Tolerancia
        if (isset($parametros->tolerancia) && trim($parametros->tolerancia) != '') {

            $sql .= "AND
                        hrpitolerancia = " . intval($parametros->tolerancia) . "";
        }

        //Horario
        if (isset($parametros->horario_inicial) && trim($parametros->horario_inicial) != ''
                && isset($parametros->horario_final) && trim($parametros->horario_final) != '') {


            if ($parametros->filtro_horario == 'I') {

                $sql .= "
                	AND
					(
							'". $parametros->horario_inicial ."'::time  BETWEEN hrpihorario_ini AND hrpihorario_fim

						OR
							'". $parametros->horario_final ."'::time BETWEEN hrpihorario_ini AND hrpihorario_fim
					)
                     ";
            } else {

                $sql .= "
                	AND
                       hrpihorario_ini = '" . $parametros->horario_inicial . "'::time
                     AND
                       hrpihorario_fim = '" . $parametros->horario_final . "'::time
                     ";
            }
        }

        $sql.= "
            ORDER BY
                atendente,
                horario;
        ";

        if (!$rs = pg_query($this->conn, $sql)) {
            throw new ErrorException(self::MENSAGEM_ERRO_PROCESSAMENTO);
        }

        while ($row = pg_fetch_object($rs)) {

            //$row->grupo_trabalho = utf8_encode($row->grupo_trabalho);
            //$row->tipo_pausa = utf8_encode($row->tipo_pausa);
            //$row->atendente = utf8_encode($row->atendente);

            $retorno[] = $row;
        }

        return $retorno;
    }
    
    /**
     * Método para realizar a pesquisa de 1 registro
     * @param stdClass $hrpioid Filtro da pesquisa
     * @return array
     * @throws ErrorException
     */
    public function pesquisarPorID($hrpioid) {

        $sql = "
        		SELECT
                    DISTINCT
                    
                    -- Valores brutos
					hrpioid,
                    gtroid,
                    motaoid,
                    cd_usuario,
                    TO_CHAR(hrpihorario_ini, 'HH24:MI') AS hrpihorario_ini,
                    TO_CHAR(hrpihorario_fim, 'HH24:MI') AS hrpihorario_fim,
                    hrpitempo,
                    hrpitolerancia,
                    
                    -- Valores tratados
                    gtrnome AS grupo_trabalho,
					motamotivo AS tipo_pausa,
					nm_usuario AS atendente,
					hrpitempo AS tempo,
					(TO_CHAR(hrpihorario_ini, 'HH24:MI') || ' - ' || TO_CHAR(hrpihorario_fim, 'HH24:MI')) AS horario,
					hrpitolerancia AS tolerancia
				FROM
					horario_pausa_item
				INNER JOIN
					motivo_pausa ON (motaoid = hrpimotaoid AND motacentral IS TRUE)
				INNER JOIN
        			horario_pausa ON (hrpmotaoid = motaoid AND hrpdt_exclusao IS NULL)
				INNER JOIN
        			grupo_trabalho ON (gtroid = hrpgtroid AND gtrdt_exclusao IS NULL)
        		INNER JOIN
        			grupo_trabalho_usuario ON (gtugtroid = gtroid AND gtuusuoid = hrpiatendente)
        		INNER JOIN
					usuarios ON cd_usuario = hrpiatendente
				WHERE
					hrpidt_exclusao IS NULL
                AND
                    hrpioid = ". intval($hrpioid) .";
                    ";


        if (!$rs = pg_query($this->conn, $sql)) {
            throw new ErrorException(self::MENSAGEM_ERRO_PROCESSAMENTO);
        }
        
        return pg_fetch_object($rs);
    }

    /**
     * busca combo "Grupo de Trabalho"
     * @return boolean
     * @throws ErrorException
     */
    public function buscarGrupoTrabalho() {

        $retorno = array();

        $sql = "
            SELECT
                gtroid,
                gtrnome
            FROM
                grupo_trabalho
            WHERE
                gtrdt_exclusao IS NULL
            ORDER BY
                gtrnome ASC;
            ";

        if (!$rs = pg_query($this->conn, $sql)) {

            throw new ErrorException(self::MENSAGEM_ERRO_PROCESSAMENTO);
        }

        while ($row = pg_fetch_object($rs)) {
            $retorno[] = $row;
        }

        return $retorno;
    }

    /**
     * busca combo "Tipo Pausa"
     * @return boolean
     * @throws ErrorException
     */
    public function buscarTipoPausa($gtroid) {

        $retorno = array();

        $sql = "
            SELECT
				motaoid,
				motamotivo
		   	FROM
				motivo_pausa
			INNER JOIN
				horario_pausa ON (hrpmotaoid = motaoid AND hrpdt_exclusao IS NULL)
			INNER JOIN
				grupo_trabalho ON (gtroid = hrpgtroid AND gtrdt_exclusao IS NULL)
			WHERE
				motacentral IS TRUE
			AND
				gtroid = " . intval($gtroid) . "
			ORDER BY
				motamotivo ASC;
            ";

        if (!$rs = pg_query($this->conn, $sql)) {
            throw new ErrorException(self::MENSAGEM_ERRO_PROCESSAMENTO);
        }

        while ($row = pg_fetch_object($rs)) {

            $row->motamotivo = utf8_encode($row->motamotivo);

            $retorno[] = $row;
        }

        return $retorno;
    }
    
    public function buscarParametrosPausa(stdClass $parametros) {
        
        $retorno = array('status' => true);
        
        $sql = "SELECT
                    hrptempo,
                    hrptolerancia
				FROM
					horario_pausa
				WHERE
					hrpgtroid =" . intval( $parametros->gtroid ) . "
                AND
                    hrpmotaoid =" . intval( $parametros->motaoid ) . "
                AND
                    hrpdt_exclusao IS NULL;";
        
        if (!$rs = pg_query($this->conn, $sql)){
            throw new ErrorException(self::MENSAGEM_ERRO_PROCESSAMENTO);
        }
        
        if (pg_num_rows($rs) > 0){
            $row = pg_fetch_object($rs);
            $retorno['hrptempo'] = $row->hrptempo;
            $retorno['hrptolerancia'] = str_pad($row->hrptolerancia, 2, '0', STR_PAD_LEFT);
        }
        
        return $retorno;
        
    }

    /**
     * busca combo "Atendente"
     * @return boolean
     * @throws ErrorException
     */
    public function buscarAtendente($gtroid) {

        $retorno = array();

        $sql = "
            SELECT
				gtuusuoid AS usuoid,
				nm_usuario AS atendente
			FROM
				grupo_trabalho_usuario
			INNER JOIN
				grupo_trabalho ON gtugtroid = gtroid
			INNER JOIN
				usuarios ON cd_usuario = gtuusuoid
			WHERE
				gtugtroid = " . intval($gtroid) . "
			AND
				dt_exclusao IS NULL
			ORDER BY
				nm_usuario;
            ";

        if (!$rs = pg_query($this->conn, $sql)) {
            throw new ErrorException(self::MENSAGEM_ERRO_PROCESSAMENTO);
        }

        while ($row = pg_fetch_object($rs)) {

            $row->atendente = utf8_encode($row->atendente);

            $retorno[] = $row;
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
					horario_pausa_item
					(
					hrpiatendente,
					hrpidt_cadastro,
					hrpiusuoid,
					hrpihorario_ini,
					hrpihorario_fim,
        			hrpitempo,
        			hrpimotaoid,
					hrpitolerancia
					)
				VALUES
					(
					" . intval($dados->hrpiatendente) . ",
					NOW(),
					" . intval($dados->hrpiusuoid) . ",
					'" . $dados->horario_inicial . "'::time,
					'" . $dados->horario_final . "'::time,
					" . intval($dados->hrpitempo) . ",
					" . intval($dados->motaoid) . ",
					" . intval($dados->tolerancia) . "
				);";

        if (!pg_query($this->conn, $sql)) {
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
    public function atualizar(stdClass $dados) {
        if (!empty($dados->inibir) && !empty($dados->hrpimotivo_inibicao)) {
            $sql = "
                UPDATE
                    horario_pausa_item
                SET
                    hrpiinibe_alerta = TRUE
                WHERE
                    hrpioid IN(" . implode(',', $dados->inibir) . ");
            ";
            
            if (!pg_query($this->conn, $sql)) {
                throw new ErrorException(self::MENSAGEM_ERRO_PROCESSAMENTO);
            }
            
            foreach($dados->inibir as $hrpioid) {
                $this->inserirMotivoInibicao($dados, $hrpioid);
            }
        } elseif (!empty($dados->hrpioid)) {
            $sql = "
                UPDATE
                    horario_pausa_item
                SET
                    hrpimotaoid = ".intval($dados->motaoid).",
                    hrpiatendente = ".intval($dados->hrpiatendente).",
                    hrpihorario_ini = '".$dados->horario_inicial."'::TIME,
                    hrpihorario_fim = '".$dados->horario_final."'::TIME,
                    hrpitempo = ".intval($dados->hrpitempo).",
                    hrpitolerancia = ".intval($dados->tolerancia)."
                WHERE
                    hrpioid = ".$dados->hrpioid.";
            ";
            
            if (!pg_query($this->conn, $sql)) {
                throw new ErrorException(self::MENSAGEM_ERRO_PROCESSAMENTO);
            }
        }
        
        return true;
    }
    
    /**
     * Responsável inserir o motivo da inibição
     * @param stdClass $dados Dados a serem gravados
     * @return boolean
     * @throws ErrorException
     */
    private function inserirMotivoInibicao(stdClass $dados, $hrpioid) {
        
        $item = $this->pesquisarPorID($hrpioid);
        
        if(!$item) {
            return;
        }
        
        $sql = "INSERT INTO
					horario_pausa_historico
                    (
					hphhrpioid,
                    hphobservacao,
                    hphhpmaoid,
                    hphdt_cadastro,
                    hphatendente,
                    hphusuoid
                    )
                    VALUES
                    (
					 ". $item->hrpioid .",
                    '". $dados->hrpimotivo_inibicao ."',
                    2,
                    NOW(),
                    ". $item->hrpiatendente .",
                    " . intval($dados->hrpiusuoid) . "
                    );
				";
        
        if (!pg_query($this->conn, $sql)) {
            throw new ErrorException(self::MENSAGEM_ERRO_PROCESSAMENTO);
        }
        
    }

    /**
     * Exclui (UPDATE) um registro da base de dados.
     * @param int $id Identificador do registro
     * @return boolean
     * @throws ErrorException
     */
    public function excluir($id) {

        $sql = "UPDATE
					horario_pausa_item
				SET
					 hrpidt_exclusao = NOW()
				WHERE
					hrpioid = " . intval($id) . ";";

        if (!pg_query($this->conn, $sql)) {
            throw new ErrorException(self::MENSAGEM_ERRO_PROCESSAMENTO);
        }

        return true;
    }

    /**
     * Verifica o nivel de ação que o usuário logado tem permissão
     *
     * @param int $idUsuario
     * @throws ErrorException
     * @return boolean
     */
    public function validarAcessoUsuario($idUsuario) {

        $retorno = false;

        $sql = "
    			SELECT EXISTS(
    							SELECT
    									1
							    FROM
							    	grupo_trabalho_usuario
							    INNER JOIN
							    	grupo_trabalho ON gtugtroid = gtroid
							    WHERE
							    	gtuusuoid = " . $idUsuario . "
							    AND
							    	gtrlancamento_edicao = TRUE
							    AND
							    	gtrdt_exclusao IS NULL
							   ) AS acesso;
    			";

        if (!$rs = pg_query($this->conn, $sql)) {
            throw new ErrorException(self::MENSAGEM_ERRO_PROCESSAMENTO);
        }

        if (pg_num_rows($rs) > 0) {
            $linha = pg_fetch_object($rs);
        }

        $retorno = ($linha->acesso == 't') ? true : false;

        return $retorno;
    }

    /**
     * Busca pausas obrigatorias não cadastradas para um atendente
     *
     * @param stdClass $dados
     * @return array
     * @throws ErrorException
     */
    public function verificarPausaObrigatoria(stdClass $dados) {

        $retorno = array();

        $sql = "
                SELECT
                    DISTINCT hrpmotaoid, motamotivo
                FROM
                    horario_pausa
                INNER JOIN
                    motivo_pausa ON motaoid = hrpmotaoid
                WHERE
                    hrpgtroid = " . intval($dados->gtroid) . "
                AND
                    hrpcadastro_obrigatorio = TRUE
                AND
                    hrpdt_exclusao IS NULL
                AND
                    hrpmotaoid NOT IN (
                                SELECT
                                    DISTINCT hrpimotaoid
                                FROM
                                    horario_pausa_item
                                WHERE
                                    hrpiatendente = " . intval($dados->hrpiatendente) . "
                                AND
                                    hrpidt_exclusao IS NULL
                    );
                ";

        if (!$rs = pg_query($this->conn, $sql)) {
            throw new ErrorException(self::MENSAGEM_ERRO_PROCESSAMENTO);
        }

        if (pg_num_rows($rs) > 0) {

            while ($linha = pg_fetch_object($rs)) {

                $retorno[] = $linha;
            }
        }
        return $retorno;
    }
    
    /**
     * Verifica se há disponibilidade de horário para o intervalo informado no cadastro
     * Se retornar TRUE o intervalo esta disponível.
     *
     * @param stdClass $dados
     * @throws ErrorException
     * @return boolean
     */
    public function verificarIntervaloDisponivel(stdClass $dados){
    	
    	$retorno = false;
    	
    	$sql = "
    			SELECT
    				COUNT(1) AS total
				FROM
    				horario_pausa_item
				WHERE
					hrpiatendente = ". $dados->hrpiatendente ."
				AND
					hrpidt_exclusao IS NULL
				AND
					(
							'". $dados->horario_inicial ."'::time  BETWEEN hrpihorario_ini AND hrpihorario_fim

						OR
							'". $dados->horario_final ."'::time BETWEEN hrpihorario_ini AND hrpihorario_fim
					)
    			";
        
        if (!empty($dados->hrpioid)) {
            $sql.= "
                AND
                    hrpioid != ".intval($dados->hrpioid)."
            ";
        }
        
        $sql.= ";";
        
    	if (!$rs = pg_query($this->conn, $sql)) {
    		throw new ErrorException(self::MENSAGEM_ERRO_PROCESSAMENTO);
    	}
    	
    	if (pg_num_rows($rs) > 0) {
    	
    		$linha = pg_fetch_object($rs);
    		$retorno = isset($linha->total) ? $linha->total : 1;
    		$retorno = (boolean)$retorno;
    	
    	}
    	
    	return $retorno;
    	
    }

    public function verificarTiposCadastrados(stdClass $dados) {
        $retorno = false;
        
        $sql = "
            SELECT
                1
            FROM
                horario_pausa_item
            WHERE
                hrpimotaoid = ".intval($dados->motaoid)."
            AND
                hrpiatendente = ".intval($dados->hrpiatendente)."
            AND 
                hrpidt_exclusao IS NULL
        ";
        
        if (!empty($dados->hrpioid)) {
            $sql.= "
                AND
                    hrpioid != ".intval($dados->hrpioid)."
            ";
        }
        
        $sql.= ";";
        
        if (!$rs = pg_query($this->conn, $sql)) {
            throw new ErrorException(self::MENSAGEM_ERRO_PROCESSAMENTO);
        }
        
        if (pg_num_rows($rs) > 0) {
            $retorno = true;
        }
        
        return $retorno;
    }

    /**
     * Abre a transação
     */
    public function begin() {
        pg_query($this->conn, 'BEGIN;');
    }

    /**
     * Finaliza um transação
     */
    public function commit() {
        pg_query($this->conn, 'COMMIT;');
    }

    /**
     * Aborta uma transação
     */
    public function rollback() {
        pg_query($this->conn, 'ROLLBACK;');
    }

}

?>
