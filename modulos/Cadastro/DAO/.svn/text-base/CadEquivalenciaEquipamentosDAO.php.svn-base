<?php

/**
 * Equivalência de Equipamentos por Classe de Contrato
 */
class CadEquivalenciaEquipamentosDAO {
    /**
     *
     * @var connection Conexão 
     */
	/**
	 * Conexão do Banco de Dados
	 * 
	 * @var resource
	 */
	private $conn;
	
	/**
	 * Método construtor
	 * 
	 * @return boolean
	 */
	public function __construct() {
		
		global $conn;
		
		$this->conn = $conn;
		
		return true;
	}
	
    /**
     * Método responsável por realizar a pesquisa
     * @params object $filtros
     * @return Array Retorno um array de objetos.
     */
     public function pesquisar(stdClass $filtros) {
     	
     	try {
     		
     		$this->begin();
     		
     		$retorno = array();
     		
     		//Filtro da pesquisa
            $where = "";
            
            /**
             * Valida os filtros
             */
            //Modalidade do contrato
            if (isset($filtros->eqqmodalidade) && !empty($filtros->eqqmodalidade)){
                $where .= "
                    AND 
                        equivalencia_equipamento.eqqmodalidade = '".$filtros->eqqmodalidade."'";
            }
            
            if (isset($filtros->eeqeqcoid) && !empty($filtros->eeqeqcoid)){
                $where .= "
                    AND 
                        equivalencia_equipamento.eeqeqcoid = '".intval($filtros->eeqeqcoid)."'";
            }
            
            if (isset($filtros->eeqtpcoid) && trim($filtros->eeqtpcoid) != "") {
                if (intval($filtros->eeqtpcoid) > -1){
                    $where .= " 
                        AND equivalencia_equipamento.eeqtpcoid = '".intval($filtros->eeqtpcoid)."'";
                } else {
                    $where .= " 
                        AND equivalencia_equipamento.eeqtpcoid IS NULL ";
                }

            }
            
            /**
             * Verifica se foi marcado o checkbox para mostrar todas as classes. Altera 
             * a condição de ligação das tabelas.
             */
     	    if (isset($filtros->classes_sem_cadastro) && !empty($filtros->classes_sem_cadastro)) {
     			$condicaoTabelaEquipamentoClasse = "RIGHT";
     			$condicaoDemaisTabelas = "LEFT";
                //Filtra as classes sem cadastro
                $where .= "
                    AND     eeqoid IS NULL";
                
     		} else {
     			$condicaoTabelaEquipamentoClasse = "INNER";
     			$condicaoDemaisTabelas = "INNER";
     		}
     		$sql = "	SELECT 
                                eeqoid AS id,
                                equipamento_classe.eqcdescricao,
                                tipo_contrato.tpcdescricao,
                                TO_CHAR(equivalencia_equipamento.eeqdt_cadastro, 'DD/MM/YYYY') AS eeqdt_cadastro,
                                TO_CHAR(logs.leidt_alteracao, 'DD/MM/YYYY') AS leidt_alteracao,
                                logs.nm_usuario_2 AS nm_usuario_2,
                                logs.leiusuoid_alteracao,
                                usuarios.nm_usuario,
                                CASE 
                                    WHEN eqqmodalidade = 'L' THEN 'Locação' 
                                    ELSE 'Revenda' 
                                END AS modalidade
						FROM 
                                equivalencia_equipamento
						" . $condicaoTabelaEquipamentoClasse . " JOIN 
                                equipamento_classe ON equivalencia_equipamento.eeqeqcoid = equipamento_classe.eqcoid
                                
						LEFT JOIN 
                                tipo_contrato ON equivalencia_equipamento.eeqtpcoid = tipo_contrato.tpcoid
                                
						LEFT JOIN 
                                usuarios ON equivalencia_equipamento.eqqusuoid_cadastro = usuarios.cd_usuario
                        LEFT JOIN (        
                            SELECT 
                                DISTINCT leieeqoid,
                                FIRST_VALUE(usuarios_2.nm_usuario) OVER(wd_ultimo_registro) AS nm_usuario_2,
                                FIRST_VALUE(leidt_alteracao)       OVER(wd_ultimo_registro) AS leidt_alteracao,
                                FIRST_VALUE(leiusuoid_alteracao)   OVER(wd_ultimo_registro) AS leiusuoid_alteracao

                            FROM 
                                log_equivalencia_equipamento_item
                            INNER JOIN 
                                usuarios AS usuarios_2 ON usuarios_2.cd_usuario = leiusuoid_alteracao
                                
                            WINDOW wd_ultimo_registro AS (PARTITION BY leieeqoid ORDER BY leieeqoid, leidt_alteracao DESC)
                            
                            ) AS logs ON logs.leieeqoid = eeqoid
                                
						WHERE 1=1
			     		$where
			     		
                        ORDER BY 
                            eqcdescricao, 
                            tpcdescricao, 
                            eeqdt_cadastro, 
                            leidt_alteracao, 
                            nm_usuario, 
                            nm_usuario_2";

			if (!$rs = pg_query($this->conn,$sql)) {
				
				$this->rollback();
				
				return false;
			}
               
			if (pg_num_rows($rs) > 0) {
				while ($row = pg_fetch_object($rs)) {
					$retorno[] = $row;
				}
			}
               
			$this->commit();

			return $retorno;
     
     	}catch(Exception $e ) {
     		
     		$this->rollback();
     		
     		return false;
     	}
	}
    
    
    /**
     * Método responsável por realizar a pesquisa de equivalência de ID
     * @params int $eeqoid
     * @return Array Retorno um array de objetos.
     */
    public function buscarEquivalencia($eeqoid){
        
        $this->begin();
        
        $sql = "SELECT
                        eeqoid,
                        eqqmodalidade,
                        eeqeqcoid,
                        eeqtpcoid
               FROM
                        equivalencia_equipamento
               WHERE
                        eeqoid = ".$eeqoid."
               LIMIT 1";
        
        if (!$rs = pg_query($this->conn,$sql)) {
            $this->rollback();
            return false;
        }
        
        if (pg_num_rows($rs) > 0) {
            $this->commit();
            return pg_fetch_object($rs, '0');
        }
        
    }
    
    /**
    * Metodo para buscar produtos conforme equivalencia
    * $parametros->eeieeqoid : ID do Tipo de Contrato
    * @param int $eeqoid : ID da Equivalencia
    * @param int $eeioid : ID da Equivalencia_item (opicional)
    * @return Array
    * @throws Exception
    */
    public function buscarProdutosEquivalencia($eeqoid, $eeioid = null){
        
        $retorno = array();
        
        $where = "";
        
        if (!is_null($eeioid)){
            $where .= " OR eeioid = " . intval($eeioid);
        }
        
        $sql = "SELECT
                    eeioid AS id,
                    CASE WHEN eeitipo = 'E' THEN 'Equipamento'
                    ELSE 'Acessório'
                    END AS tipo,
                    prdproduto AS produto,
                    prdoid,
                    eeiversao AS versao,
                    TO_CHAR(eeidt_cadastro,'DD/MM/YYYY') AS data_inclusao,
                    eeieeqoid
                FROM
                    equivalencia_equipamento_item
                INNER JOIN
                    produto ON prdoid = eeiprdoid
                WHERE
                    eeidt_exclusao IS NULL
                AND
                    eeieeqoid = ". intval($eeqoid) . "
                    " . $where . "
                ORDER BY 
                    prdproduto, eeiversao ASC";
        
        $this->begin();
        if (!$rs = pg_query($this->conn,$sql)) {
           $this->rollback();
           return false;
        }
        
        $this->commit();
        if (pg_num_rows($rs) > 0){
            
            while ($row = pg_fetch_object($rs)) {
                $retorno[] = $row;
            }
            
           return $retorno;
        } else {
           $this->rollback();
           return false;
        }
    }
    
    
    
    /**
     * Grava o log de alteração do produto
     * parametros:
     * $parametros->eeioid : ID da tabela equivalencia_equipamento_item
     * $parametros->cd_usuario : ID do usuário
     * $parametros->tipo_alteracao : Tipo alteração (I = Inclusão, E = Exclusão)
     * $parametros->versao : Versão do equipamento
     * $parametros->prdoid : ID do produto
     * $parametros->eeqoid : ID da tabela equivalencia_equipamento
     * @param stdClass $parametros
     * @return boolean
     */
    private function gravarLog(stdClass $parametros){
        
        if (!isset($parametros->eeioid) || empty($parametros->eeioid)){
            return false;
        }
        
        if (!isset($parametros->cd_usuario) || empty($parametros->cd_usuario)){
            return false;
        }
        
        if (!isset($parametros->tipo_alteracao) || empty($parametros->tipo_alteracao)){
            return false;
        }
        
        if (!isset($parametros->versao)){
            $parametros->versao = 'NULL';
        } else {
            $parametros->versao = ($parametros->versao != 'NULL') ? "'" . pg_escape_string($parametros->versao) . "'" : 'NULL';
        }
        
        if (!isset($parametros->prdoid) || empty($parametros->prdoid)){
            return false;
        }
        
        if (!isset($parametros->eeqoid) || empty($parametros->eeqoid)){
            return false;
        }
        
        
        
        $sql = "
            INSERT INTO 
                log_equivalencia_equipamento_item(
                    leieeioid, 
                    leidt_alteracao, 
                    leiusuoid_alteracao, 
                    leitipo_alteracao, 
                    leiversao, 
                    leiprdoid, 
                    leieeqoid)
                VALUES ( 
                    " . intval($parametros->eeioid) . ", 
                    NOW(), 
                    ". intval($parametros->cd_usuario) ." , 
                    '" . $parametros->tipo_alteracao . "',
                    " . $parametros->versao . ", 
                    " . intval($parametros->prdoid) . ", 
                    " . intval($parametros->eeqoid) . "
            )";

        $this->begin();
        if (!$rs = pg_query($this->conn,$sql)) {
            $this->rollback();
            return false;
        }
        $this->commit();
        if (pg_affected_rows($rs) > 0){
            return true;
        } else {
            return false;
        }
    }


    /**
	 * Método responsável por buscar Classe Contrato
	 * @params object $filtros
	 * @return Array
	 */
	public function buscarClasseContrato(stdClass $filtros) {
		
		$idModalidadeContrato = (isset($filtros->eqqmodalidade) && !empty($filtros->eqqmodalidade)) ? $filtros->eqqmodalidade : null;
        
        //Join para retornar apenas classes cadastradas na tabela de equivalencia
        $join  = "";
        $where = "";
        //Filtro
        if (isset($filtros->copia) && (boolean) $filtros->copia){
            
            $join .= "
                INNER JOIN equivalencia_equipamento
                    ON equivalencia_equipamento.eeqeqcoid = equipamento_classe.eqcoid";
            
            $where = "eqqmodalidade = '".$idModalidadeContrato."'";
            
        } else {
            
            $where = "conmodalidade = '".$idModalidadeContrato."'";
            
            
        }
            
		$retorno = array();
	
		$sql = "SELECT
					DISTINCT
					eqcoid, 
					eqcdescricao 
				FROM 
					contrato
                INNER JOIN equipamento_classe 
					ON contrato.coneqcoid = equipamento_classe.eqcoid
                " . $join . "
				WHERE
					" . $where . "
				ORDER BY eqcdescricao ASC";

        if($resultado = pg_query($this->conn, $sql)) {
			if(pg_num_rows($resultado) > 0) {
				while($row = pg_fetch_object($resultado)) {
					array_push($retorno, $row);
				}
			}
		}
	
		return $retorno;
	}
	
	/**
	 * Método responsável por buscar Tipo de Contrato
	 * @params object $filtros
	 * @return Array
	 */
	public function buscarTipoContrato(stdClass $filtros) {
	
		$idModalidadeContrato = (isset($filtros->eqqmodalidade) && !empty($filtros->eqqmodalidade)) ? $filtros->eqqmodalidade : null;
		
		$idClasseContrato = (isset($filtros->eeqeqcoid) && !empty($filtros->eeqeqcoid)) ? $filtros->eeqeqcoid : null;
        
        
        $join  = "";
        $where = "";
        $union = "";
        if (isset($filtros->copia) && (boolean) $filtros->copia){
            $join .= "
                INNER JOIN
                    equivalencia_equipamento ON eeqtpcoid = tpcoid";
            
            $where = "
                    eqqmodalidade = '".$idModalidadeContrato."'
                AND
                    eeqeqcoid = ". $idClasseContrato ."";
            $union = "
                UNION
                SELECT 
                        -1 AS tpcoid,
                        'TODOS' AS tpcdescricao
                FROM 
                        equivalencia_equipamento
                WHERE 
                        eqqmodalidade = '".$idModalidadeContrato."'
                AND
                        eeqeqcoid = ". $idClasseContrato ."
                AND
                        eeqtpcoid IS NULL";
        } else {
            
            $where = "
                    conmodalidade = '".$idModalidadeContrato."'
                AND
                    eqcoid = ". $idClasseContrato ."";
            
        }
		
		
		$retorno = array();
	
		$sql = "SELECT
                    DISTINCT
                    tpcoid,
                    tpcdescricao
                FROM 
                    tipo_contrato
                INNER JOIN
                    contrato ON conno_tipo = tpcoid
                INNER JOIN
                    equipamento_classe ON coneqcoid = eqcoid
                " . $join . "
                WHERE
                    " . $where . "
                " . $union . "
                ORDER BY
                    tpcdescricao ASC";
	
		if($resultado = pg_query($this->conn, $sql)) {
			if(pg_num_rows($resultado) > 0) {
				while($row = pg_fetch_object($resultado)) {
					array_push($retorno, $row);
				}
			}
		}
	
		return $retorno;
	}
    
    
    /**
     * Metodo para buscar os produtos de acordo com o Tipo
     * $filtros->tipo: E-Equipamento ou A-Acessório
     * @param stdClass $filtros Filtros 
     * @return boolean
     * @throws Exception
     */
    public function buscarProdutos(stdClass $filtros){
        
        //Retorno da consulta
        $retorno = array();
        
        //Filtros da pesquisa
        $where = "";
        
        /**
         * Verifica o tipo do produto, E-Equipamento ou A-Acessório
         */
        if (isset($filtros->eeitipo) && !empty($filtros->eeitipo)){
            if ($filtros->eeitipo == 'A'){
                $where .= "
                    AND 
                        lower(imotdescricao) NOT IN ('outros', 'equipamento')";
            } else {
                $where .= "
                    AND 
                        lower(imotdescricao) = 'equipamento'";
            }
        } else {
            throw new Exception('Existem campos obrigatórios não preenchidos');
        }
        
        $sql = "
            SELECT 
                    prdoid,
                    prdproduto
            FROM 
                    produto
            INNER JOIN 
                    imobilizado_tipo on prdimotoid = imotoid
            WHERE 
                    prddt_exclusao IS NULL"
            . $where . "
            ORDER BY
                    prdproduto ASC";
        
        $this->begin();
        
        if (!$rs = pg_query($this->conn,$sql)) {

            $this->rollback();

            return false;
        }

        if (pg_num_rows($rs) > 0) {
            while ($row = pg_fetch_object($rs)) {
                $retorno[] = $row;
            }
        }

        $this->commit();

        return $retorno;
    }
    
    /**
     * Metodo para buscar as versões dos equipamentos
     * @param stdClass $filtros
     * @return boolean
     * @throws Exception
     */
    public function buscarVersoes(stdClass $filtros){
        
        //Retorno da consulta
        $retorno = array();
        
        //Filtros da pesquisa
        $where = "";
        
        if (isset($filtros->prdoid) && !empty($filtros->prdoid)){
            $where .= "
                AND
                    prdoid = " . intval($filtros->prdoid);
        } else {
            throw new Exception('Existem campos obrigatórios não preenchidos');
        }
        if ($filtros->tipo == 'E'){
            $sql = "
                SELECT 
                    DISTINCT eveoid AS id,
                    eveversao AS versao,
            		'E' as tipo
                FROM 
                    produto
                INNER JOIN
                    equipamento on equprdoid = prdoid
                INNER JOIN 
                    equipamento_versao on eveoid = equeveoid
                WHERE 
                    evedt_exclusao IS NULL
                " . $where . "
                ORDER BY
                    eveversao ASC
                ";
        } else {
            $sql ="
                SELECT
                        versoes.id,
                        versoes.versao, 
            			versoes.tipo                    
                FROM (
                        SELECT 
                            asvoid AS id,
                            asvdescricao AS versao,
                            prdoid AS produto,
            				'A' as tipo
                        FROM 
                            produto
                        INNER JOIN
                            antena_satelital ON asatprdoid = prdoid
                        INNER JOIN 	
                            antena_satelital_versao ON asatasvoid = asvoid AND asvexclusao IS NULL

                        UNION

                        SELECT 
                            cbvoid AS id,
                            cbvdescricao AS versao,
                            prdoid AS produto,
            				'C' as tipo
                        FROM 
                            produto
                        INNER JOIN
                            carb ON carbprdoid = prdoid
                        INNER JOIN 	
                            carb_versao ON carbcbvoid = cbvoid AND carbdt_exclusao IS NULL

                        UNION

                        SELECT 
                            cbvoid AS id,
                            cbvdescricao AS versao,
                            prdoid AS produto,
            				'B' as tipo
                        FROM 
                            produto
                        INNER JOIN
                            computador_bordo ON cborprdoid = prdoid
                        INNER JOIN 	
                            computador_bordo_versao ON cborcbvoid = cbvoid AND cbvexclusao IS NULL

                        UNION

                        SELECT 
                            muvoid AS id,
                            muvdescricao AS versao,
                            prdoid AS produto,
            				'M' as tipo
                        FROM 
                            produto
                        INNER JOIN
                            multisensor ON mtsprdoid = prdoid
                        INNER JOIN 	
                            multisensor_versao ON mtsmuvoid = muvoid AND muvexclusao IS NULL
                        UNION

                        SELECT 
                            afvoid AS id,
                            afvdescricao AS versao,
                            prdoid AS produto,
                            'AF' as tipo
                        FROM 
                            produto
                        INNER JOIN
                            afere ON afrprdoid = prdoid
                        INNER JOIN 	
                            afere_versao ON afrafvoid = afvoid AND afvexclusao IS NULL


                        UNION

                        SELECT 
                            sdvoid AS id,
                            sdvdescricao AS versao,
                            prdoid AS produto,
            				'S' as tipo
                        FROM 
                            produto
                        INNER JOIN
                            sensor_desengate ON sndprdoid = prdoid
                        INNER JOIN 	
                            sensor_desengate_versao ON sndsdvoid = sdvoid AND sdvexclusao IS NULL	

                        UNION

                        SELECT 
                            tveoid AS id,
                            tvedescricao AS versao,
                            prdoid AS produto,
            				'T' as tipo
                        FROM 
                            produto
                        INNER JOIN
                            teclado ON tecprdoid = prdoid
                        INNER JOIN 	
                            teclado_versao ON tectveoid = tveoid AND tveexclusao IS NULL	
                ) AS versoes

                WHERE 
                    versoes.produto = " . intval($filtros->prdoid) . "
                ORDER BY
                    versoes.versao ASC
                ";
        }
        $this->begin();
        
        if (!$rs = pg_query($this->conn,$sql)) {

            $this->rollback();

            return false;
        }

        if (pg_num_rows($rs) > 0) {
            while ($row = pg_fetch_object($rs)) {
                $retorno[] = $row;
            }
        }

        $this->commit();

        return $retorno;
        
        
    }
    
    
    /**
     * Metodo para gravar a equivalencia. Parametros:
     * $parametros->eeqtpcoid : ID do Tipo de Contrato
     * $parametros->eeqeqcoid : ID do Classe de contrato
     * $parametros->cd_usuario : ID do Usuário que cadastrou o registro
     * $parametros->eqqmodalidade : Modalidade do Contrato Caso L - Locação, Caso V - Revenda
     * @param stdClass $parametros 
     * @return boolean
     * @throws Exception
     */
    public function gravarEquivalencia(stdClass $parametros){
        
        
        if (!isset($parametros->eeqtpcoid) || trim($parametros->eeqtpcoid) == ''){
            throw new Exception('Existem campos obrigatórios não preenchidos');
        } else {
            $parametros->eeqtpcoid = intval($parametros->eeqtpcoid) > -1 ? intval($parametros->eeqtpcoid) : 'NULL';
        }
        
        if (!isset($parametros->eeqeqcoid) || trim($parametros->eeqeqcoid) == ''){
            throw new Exception('Existem campos obrigatórios não preenchidos');
        }
        
        if (!isset($parametros->cd_usuario) || trim($parametros->cd_usuario) == ''){
            throw new Exception('Existem campos obrigatórios não preenchidos');
        }
        
        if (!isset($parametros->eqqmodalidade) || trim($parametros->eqqmodalidade) == '' ){
            throw new Exception('Existem campos obrigatórios não preenchidos');
        }
        
        
        $sql = "
                INSERT INTO 
                    equivalencia_equipamento (
                        eeqdt_cadastro, 
                        eeqtpcoid, 
                        eeqeqcoid, 
                        eqqusuoid_cadastro, 
                        eqqmodalidade
                    )
                    VALUES (
                        NOW(), 
                        " . $parametros->eeqtpcoid . ", 
                        " . intval($parametros->eeqeqcoid) . ", 
                        " . intval($parametros->cd_usuario) . ", 
                        '" . $parametros->eqqmodalidade . "' 
                )
                RETURNING eeqoid;";
        
         $this->begin();
         
        if (!$rs = pg_query($this->conn, $sql)) {
            $this->rollback();
            return false;
        }
        
        if (pg_num_rows($rs) > 0){
            $this->commit();
            $row = pg_fetch_object($rs);
            return $row->eeqoid;
        } else {
            return false;
        }
        
        
         
    }
    
    /**
     * Copia os produtos de uma equivalencia de origem ($parametros->eeqoid_origem) 
     * para outra ($parametros->eeqoid_destino)
     * Parametros:
     * $parametros->eeqoid_origem : Equivalencia de origem
     * $parametros->eeqoid_destino : Equivalencia de destino
     * $parametros->cd_usuario : ID do usuário
     * @param stdClass $parametros
     * @return boolean
     * @throws Exception
     */
    public function copiarProdutos(stdClass $parametros){
                
        if (empty($parametros->eeqoid_origem) || empty($parametros->eeqoid_destino) || empty($parametros->cd_usuario)){
            throw new Exception('Houve um erro no processamento de dados.');
        }
        
        $this->begin();
        
        $sql = "
            INSERT INTO 
                    equivalencia_equipamento_item (
                        eeieeqoid, 
                        eeiprdoid, 
                        eeidt_exclusao, 
                        eeitipo, 
                        eeiversao, 
                        eeidt_cadastro, 
                        eeiusuoid_cadastro
                    )
                SELECT
                        " . intval($parametros->eeqoid_destino) . ",
                        eeiprdoid,
                        NULL,
                        eeitipo,
                        eeiversao,
                        NOW(),
                        " . intval($parametros->cd_usuario) . "
                FROM
                        equivalencia_equipamento_item
                WHERE
                        eeidt_exclusao IS NULL
                AND 
                        eeieeqoid = " . intval($parametros->eeqoid_origem);
        
        if (!$rs = pg_query($this->conn, $sql)) {
            $this->rollback();
            return false;
        }
        
        $this->commit();
        
        if (pg_affected_rows($rs) > 0){
            return true;
        } else {
            return false;
        }
    }
    /**
     * Metodo para gravar produtos da equivalencia. Parametros:
     * $parametros->eeitipo : Tipo de produto E -  equipamento / A -  Acessórios
     * $parametros->eeiprdoid : ID do produto
     * $parametros->eeiversao : ID da versão do produto
     * $parametros->eeiusuoid_cadastro : ID do Usuário que cadastrou o registro
     * @param stdClass $parametros 
     * @return boolean
     * @throws Exception
     */
    public function gravarEquivalenciaProduto(stdClass $parametros) {
        
        //Dados a serem inseridos
        $dados = array();
        
        
        if (!isset($parametros->eeitipo) || trim($parametros->eeitipo) == ''){
            throw new Exception('Existem campos obrigatórios não preenchidos');
        }
        
        if (!isset($parametros->eeiprdoid) || trim($parametros->eeiprdoid) == ''){
            throw new Exception('Existem campos obrigatórios não preenchidos');
        }
        
        //Todas as versões
        $todasVersoes = false;
        
        if (!isset($parametros->eeiversao) ||  trim($parametros->eeiversao) == '') {
            $parametros->eeiversao = 'NULL';
        }else{
            if ($parametros->eeiversao == 'Todas'){
                $todasVersoes = true;
            } else {
                $parametros->eeiversao = "'" . pg_escape_string(utf8_decode($parametros->eeiversao)) . "'";
            }
        }
        
        //Verifica se é a Versão (Todas)
        if ($todasVersoes){
            
            $parametrosVersoes = new stdClass();
            $parametrosVersoes->prdoid = $parametros->eeiprdoid;
            $parametrosVersoes->tipo   = $parametros->eeitipo;
            
            //Seleciona todas versões do equipamento/acessorio 
            $versoes = $this->buscarVersoes($parametrosVersoes);


            if (count($versoes) > 0){
            
                foreach($versoes as $versao){
                    $dadosInclusao = new stdClass();
                    $dadosInclusao->eeieeqoid = $parametros->eeieeqoid;
                    $dadosInclusao->eeiprdoid = $parametros->eeiprdoid;
                    $dadosInclusao->eeitipo = $parametros->eeitipo;
                    $dadosInclusao->eeiusuoid_cadastro = $parametros->eeiusuoid_cadastro;
                    $dadosInclusao->eeiversao = "'" . pg_escape_string($versao->versao) . "'";
                    $dados[] = $dadosInclusao;
                } 
            } else {
                $dados[] = $parametros;
            }
            
        } else {
            $dados[] = $parametros;
        }

        //Verifica o erro;
        
        foreach($dados as $equipamentoItem){
            $sql = "
                    INSERT INTO 
                        equivalencia_equipamento_item (
                            eeidt_cadastro,
                            eeieeqoid, 
                            eeiprdoid, 
                            eeitipo, 
                            eeiversao,
                            eeiusuoid_cadastro
                        )
                        VALUES (
                            NOW(), 
                            " . intval($equipamentoItem->eeieeqoid) . ", 
                            " . intval($equipamentoItem->eeiprdoid) . ", 
                            '" . $equipamentoItem->eeitipo . "', 
                            " . $equipamentoItem->eeiversao . ",
                            " . intval($equipamentoItem->eeiusuoid_cadastro) . "
                    )
                    RETURNING eeioid;";

             $this->begin();


            if (!$rs = pg_query($this->conn, $sql)) {
                $this->rollback();
                return false;
            }

            $this->commit();
            
            if (pg_affected_rows($rs) > 0){

                $row = pg_fetch_object($rs);

                //Grava o Log da inclusão
                $parametrosLog = new stdClass();
                $parametrosLog->eeioid         = $row->eeioid;
                $parametrosLog->cd_usuario     = $equipamentoItem->eeiusuoid_cadastro;
                $parametrosLog->versao         = str_replace("'", '', $equipamentoItem->eeiversao);
                $parametrosLog->tipo_alteracao = 'I';
                $parametrosLog->prdoid         = $equipamentoItem->eeiprdoid;
                $parametrosLog->eeqoid         = $equipamentoItem->eeieeqoid;
                $this->gravarLog($parametrosLog);
            } 
        }
        return true;
    }
    
    
    public function excluirEquivalenciaItem($eeioid, $cd_usuario){
        
        if (intval($eeioid) == 0){
            return false;
        }
        if (intval($cd_usuario) == 0){
            return false;
        }
        
        $sql = "
            UPDATE 
                equivalencia_equipamento_item
            SET 
                eeidt_exclusao = NOW()
            WHERE 
                eeioid = " . intval($eeioid) . ";";
        
        $this->begin();
        
        if (!$rs = pg_query($this->conn, $sql)) {
            $this->rollback();
            return false;
        }
        
        $this->commit();
        
        if (!pg_affected_rows($rs)){
            return false;
        }

        $dadosItem = $this->buscarProdutosEquivalencia(0, $eeioid);
        $dadosItem = $dadosItem[0];

        //Grava o Log da exclusão
        $parametrosLog = new stdClass();
        $parametrosLog->eeioid         = $eeioid;
        $parametrosLog->cd_usuario     = $cd_usuario;
        $parametrosLog->versao         = $dadosItem->versao;
        $parametrosLog->tipo_alteracao = 'E';
        $parametrosLog->prdoid         = $dadosItem->prdoid;
        $parametrosLog->eeqoid         = $dadosItem->eeieeqoid;
        $this->gravarLog($parametrosLog);
        
        return true;        
    }
	
	/*
	 * BEGIN
	*/
	private function begin(){
		pg_query($this->conn, "BEGIN");
	}
	
	
	/*
	 * COMMIT
	*/
	private function commit(){
		pg_query($this->conn, "COMMIT");
	}
	
	/*
	 * ROLLBACK
	*/
	private function rollback(){
		pg_query($this->conn, "ROLLBACK");
	}
	
}

