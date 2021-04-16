<?php 

class GesLayoutDAO {

	private $conn;

	public function __construct($conn) {

		$this->conn = $conn;

	}

	public function verificarPermissao($acao) {

		$usuarioLogado = $_SESSION['usuario']['oid'];

		$sql = "SELECT 
						COUNT(1) AS permitido
				FROM 
						gestao_meta_permissao 
				INNER JOIN 
						usuarios ON (gmpfunoid = usufunoid)
				WHERE 
						cd_usuario = " . intval($usuarioLogado) . " ";

		switch ($acao) {

			case 'inserir':

				$sql .= " AND 
								gmpcriar_plano_acao = 1
						  AND 
						  		gmpcriar_acao  = 1 ";

				break;

			case 'usuarios':
			case 'arvores':
			case 'metas':
			case 'indicadores':

				$sql .= " AND 
								gmpsuper_usuario = 1 ";

				break;

			case 'importacao':

				$sql .= " AND 
								(
									gmpsuper_usuario = 1 
						  		OR 
						  			gmpimportacao = 1 
						  		) ";

				break;
		}

		if (!$rs = pg_query($this->conn, $sql)) {
			throw new ErrorException("Houve um erro no processamento de dados.");
		}

		$permitido = pg_fetch_result($rs, 0, 'permitido');

		return intval($permitido) == 1 ? true : false;


	}

	public function buscarNivelUsuario($funcionarioId) {

		$sql = "SELECT
					gmanivel AS nivel
				FROM
					gestao_meta_arvore
				WHERE
					gmafunoid = " . intval($funcionarioId) . " 
				LIMIT 1";

		if (!$rs = pg_query($this->conn, $sql)) {
			throw new ErrorException("Houve um erro no processamento de dados.");
		}


		if (pg_num_rows($rs) > 0) {
			return intval(pg_fetch_result($rs, 0, 'nivel'));
		} else {
			return '';
		}	

	}


	public function buscarInfoArvore($arvore) {

		$sql = "SELECT
					gmanivel AS nivel,
					gmafunoid AS funcionario
				FROM
					gestao_meta_arvore
				WHERE
					gmaoid = " . intval($arvore) . " 
				LIMIT 1";

		if (!$rs = pg_query($this->conn, $sql)) {
			throw new ErrorException("Houve um erro no processamento de dados.");
		}


		if (pg_num_rows($rs) > 0) {
			$retorno['nivel'] = intval(pg_fetch_result($rs, 0, 'nivel'));
			$retorno['funcionario'] = intval(pg_fetch_result($rs, 0, 'funcionario'));
			return $retorno;
		} else {
			return array();
		}

	}


	public function buscarFuncionarioId($usuarioLogado){

		$sql = "SELECT 
						usufunoid
				FROM 
						usuarios 
				WHERE 
						cd_usuario = " . intval($usuarioLogado) . "
                AND
                        dt_exclusao IS NULL";

		if (!$rs = pg_query($this->conn, $sql)) {
			throw new ErrorException("Houve um erro no processamento de dados.");
		}


		if (pg_num_rows($rs) > 0) {
			return intval(pg_fetch_result($rs, 0, 'usufunoid'));
		} else {
			return '';
		}


	}



	public function buscarArvoreNome($funcionarioId , $ano = '2014') {


		$sql = "
				SELECT 
						gmanome AS arvore 
				FROM 
						gestao_meta_arvore
				WHERE 
						gmafunoid = " . intval($funcionarioId) . "
				AND 
						gmaano = " . $ano . "
		";	

		if (!$rs = pg_query($this->conn, $sql)) {
			throw new ErrorException("Houve um erro no processamento de dados.");
		}


		if (pg_num_rows($rs) > 0) {
			return pg_fetch_result($rs, 0, 'arvore');
		} else {
			return '';
		}


	}


	public function buscarArvoreNomePorId($arvoreId) {


		$sql = "
				SELECT 
						gmanome AS arvore 
				FROM 
						gestao_meta_arvore
				WHERE 
						gmaoid = " . intval($arvoreId) . ";
		";	

		if (!$rs = pg_query($this->conn, $sql)) {
			throw new ErrorException("Houve um erro no processamento de dados.");
		}


		if (pg_num_rows($rs) > 0) {
			return pg_fetch_result($rs, 0, 'arvore');
		} else {
			return '';
		}


	}
	

	public function buscarFuncionarioNome($funcionarioId) {


		$sql = "SELECT 
						nm_usuario
				FROM 
						usuarios 
				WHERE 
						usufunoid = " . intval($funcionarioId) . "
                AND 
                        dt_exclusao IS NULL";

		if (!$rs = pg_query($this->conn, $sql)) {
			throw new ErrorException("Houve um erro no processamento de dados.");
		}


		if (pg_num_rows($rs) > 0) {
			return pg_fetch_result($rs, 0, 'nm_usuario');
		} else {
			return '';
		}

	}


	public function montarDadosArvore($funcionarioId, $ano = '2014') {

		$nivel = $this->buscarNivelUsuario($funcionarioId);

		$_SESSION['gestao']['funcionario_arvore'][] = $funcionarioId;

		$arvore = array();

		if (!empty($nivel)) {
            
			$sql = "
				SELECT 
					gmanivel   AS nivel,
					gmasubnivel AS subnivel,
					usufunoid  AS funcionario_id,
					nm_usuario AS nome,	
					gmafunoid_superior AS superior,
					gmaoid,
                    gmanome,gmafunoid_superior,
                    dt_exclusao
                FROM
                    gestao_meta_arvore
                INNER JOIN
                    usuarios ON dt_exclusao IS NULL AND (usufunoid = gmafunoid) 
                WHERE
                   gmaano = " . $ano . "   --SEMPRE VAI OCORRER
                AND  -- E AS DUAS CONDIÇOES
                   (
                    ( -- 1A CONDIÇAO
                     gmanivel >= " . intval($nivel) . "
                     AND gmafunoid =  " . intval($funcionarioId) . "
                    )

                OR 
                     ( --2A CONDIÇÃO
                      gmafunoid_superior = " . intval($funcionarioId) . "
                      OR 
                      gmafunoid_superior IN (
                         SELECT 
                          gmafunoid 
                         FROM 
                          gestao_meta_arvore 
                         WHERE 
                          gmafunoid_superior = " . intval($funcionarioId) . "
                          AND gmaano = " . $ano . " 
                         )
                     )
                   )
                   ORDER BY gmanivel, gmasubnivel
            ";
            
			/*$sql = "
				SELECT 
					gmanivel   AS nivel,
					gmasubnivel AS subnivel,
					usufunoid  AS funcionario_id,
					nm_usuario AS nome,	
					gmafunoid_superior AS superior,
					gmaoid,
					gmanome
				FROM
					gestao_meta_arvore
				INNER JOIN
					usuarios ON dt_exclusao IS NULL AND (usufunoid = gmafunoid) 
				WHERE
					gmanivel >= " . intval($nivel) . "                
				AND 
					gmaano = " . $ano . "
				AND
					(gmafunoid =  " . intval($funcionarioId) . ")
				OR 
					(gmafunoid_superior = " . intval($funcionarioId) . "
					OR 
					gmafunoid_superior IN (
						SELECT 
							gmafunoid 
						FROM 
							gestao_meta_arvore 
						WHERE 
							gmafunoid_superior = " . intval($funcionarioId) . "
						)
					)
				ORDER BY gmanivel, gmasubnivel";*/

			if (!$rs = pg_query($this->conn, $sql)) {
				throw new ErrorException("Houve um erro no processamento de dados.");
			}
            
			//ID do superior
			$superior = 0;
            
			$nivel = 1;
			
            $nivelUsuario = 0;

			while($row = pg_fetch_object($rs)) {
                
				if ($nivel > 3){
					break;
				}

				//Topo da arvore
				if ($nivel == 1){
					$arvore['primeiro'] = $row;
					$superior = $row->funcionario_id;		
					$nivel++;
				} else {

					if ($superior != $row->superior && $nivelUsuario != $row->nivel){						
						$superior = $row->superior;
						$nivel++;
					}
                    
					switch ($nivel) {
						case 2:
							$arvore['segundo'][$superior][] = $row;
							break;
						case 3:
							$arvore['terceiro'][$row->superior][] = $row;
							break;
					}

				}

                $nivelUsuario = intval($row->nivel);
                
			}

			
		}
        
        
        /*echo '<pre>';
        echo $sql;       
        echo '</pre>';*/
        
		return $arvore;

	}


	public function montarDadosArvorePorID($gmaoid, $ano = 2014){


		$sql = "
			SELECT 
				gmanivel   AS nivel,
				gmasubnivel AS subnivel,
				usufunoid  AS funcionario_id,
				nm_usuario AS nome,	
				gmafunoid_superior AS superior,
				gmaoid,
				gmanome
			FROM
				gestao_meta_arvore
			INNER JOIN
				usuarios ON dt_exclusao IS NULL AND (usufunoid = gmafunoid) 
			WHERE
                gmaano = " . $ano . " 
                
            AND (  
				(gmaoid = " . intval($gmaoid) . ")
			OR 
				(gmafunoid_superior = (
					SELECT 
						gmafunoid 
					FROM 
						gestao_meta_arvore 
					WHERE 
						gmaoid = " . intval($gmaoid) . "
                    AND 
                        gmaano = " . $ano . " 
					)
				OR 
				gmafunoid_superior IN (
					SELECT 
						gmafunoid 
					FROM 
						gestao_meta_arvore 
					WHERE 
						gmafunoid_superior = (
						SELECT 
							gmafunoid 
						FROM 
							gestao_meta_arvore 
						WHERE 
							gmaoid = " . intval($gmaoid) . "   
                        AND 
                            gmaano = " . $ano . " 
						)
                        AND 
                            gmaano = " . $ano . "                         
					)
				))                
			ORDER BY gmanivel, gmasubnivel";



		if (!$rs = pg_query($this->conn, $sql)) {
			throw new ErrorException("Houve um erro no processamento de dados.");
		}

		//ID do superior
		$superior = 0;
        
		$nivel = 1;
        $nivelUsuario = 0;

		//Estrutura da arvore
		$arvore = array();
		
		while($row = pg_fetch_object($rs)) {
            
			if ($nivel > 3){
				break;
			}

			//Topo da arvore
			if ($nivel == 1){
				$arvore['primeiro'] = $row;
				$superior = $row->funcionario_id;
				$nivel++;
			} else {

				if ($superior != $row->superior && $nivelUsuario != $row->nivel){                    					
					$superior = $row->superior;  
					$nivel++;
				}

				switch ($nivel) {
					case 2:
						$arvore['segundo'][$superior][] = $row;
						break;
					case 3:
						$arvore['terceiro'][$row->superior][] = $row;
						break;
				}

			}
            
            $nivelUsuario = intval($row->nivel);

		}
	
        /*echo '<pre>';
        echo $sql;
        var_dump($arvore);
        echo '</pre>';*/
        
		return $arvore;
	}



	public function buscarSuperiorArvore($funcionario) {

		$sql = "SELECT 
						gmafunoid_superior AS superior 
				FROM 
						gestao_meta_arvore 
				WHERE 
						gmafunoid  = " . intval($funcionario) . "
				LIMIT 1";

		if (!$rs = pg_query($this->conn, $sql)) {
				throw new ErrorException("Houve um erro no processamento de dados.");
		}

		if (pg_num_rows($rs) > 0) {
			return pg_fetch_result($rs, 0, 'superior');
		} else {
			return '';
		}

	}

	public function buscarArvoresCadastradas($ano = 2014){

		$retorno = array();

		$sql = "
			SELECT 
				gmaoid,
				gmanome
			FROM
				gestao_meta_arvore
			WHERE 
				gmaano = " . intval($ano) . "
			ORDER BY 
				gmaoid
		";


		if (!$rs = pg_query($this->conn, $sql)) {
				throw new ErrorException("Houve um erro no processamento de dados.");
		}

		$retorno = array();

		while ($row = pg_fetch_object($rs)) {
			$retorno[] = $row;
		}
        
		return $retorno;

	}

	public function buscarArvoresCadastradasFuncionario($ano = 2014, $funcionario = 0) {


		//Retorna apenas as arvores que o funcionÃ¡rio tem direito de ver
		/*$sql = "
			SELECT 
				DISTINCT
				gmaoid,
				gmanome
			FROM
				gestao_meta_arvore
			INNER JOIN
				usuarios ON (usufunoid = gmafunoid) 
			WHERE
				gmaano = " . intval($ano) . "
			AND
				(gmafunoid = " . intval($funcionario) . ")
			OR 
				(gmafunoid_superior = " . intval($funcionario) . "
				OR 
				gmafunoid_superior IN (
					SELECT 
						gmafunoid 
					FROM 
						gestao_meta_arvore 
					WHERE 
						gmafunoid_superior = " . intval($funcionario) . "
					)
				)
			ORDER BY gmaoid
		";*/
        
		$sql = "
            SELECT 
                gmaoid,
				gmanome
            FROM 
                gestao_meta_arvore
            WHERE 
                gmafunoid = " . intval($funcionario) . "
            AND 
                gmaano = " . intval($ano) . "
		";	


		if (!$rs = pg_query($this->conn, $sql)) {
				throw new ErrorException("Houve um erro no processamento de dados.");
		}

		$retorno = array();

		while ($row = pg_fetch_object($rs)) {
			$retorno[] = $row;
		}


		return $retorno;
	}

	public function buscarMetasPlanos($ano = 2014, $funoid = null){

		$sql = "
            SELECT DISTINCT ON (usufunoid, gmeoid, gploid)
                nm_usuario,'PROPRIA',
                usufunoid funcionario_id,
                gmenome AS meta,
                gmeoid AS meta_id,
                gplnome AS acao,
                gploid     AS acao_meta_id  
			FROM 
                usuarios 
            JOIN gestao_meta on gmefunoid_responsavel = usufunoid
            JOIN gestao_meta_arvore on gmaano = gmeano and gmefunoid_responsavel = gmafunoid
            LEFT join gestao_meta_plano_acao on gplgmeoid = gmeoid
            WHERE 
                gmedt_exclusao IS NULL
            AND
                gmeano = " . intval($ano) . "

            UNION

            SELECT DISTINCT ON (usufunoid, gmeoid, gploid)
                nm_usuario,'COMPARTILHADA',
                usufunoid funcionario_id,
                gmenome AS meta,
                gmeoid AS meta_id,
                gplnome AS acao,
                gploid AS acao_meta_id
            FROM 
                usuarios 
            JOIN gestao_meta_compartilhada     on gmcfunoid = usufunoid
            JOIN gestao_meta                   on gmeoid = gmcgmeoid
            JOIN gestao_meta_arvore            on gmaano = gmeano and gmefunoid_responsavel = gmafunoid
            LEFT JOIN gestao_meta_plano_acao   on gplgmeoid = gmeoid 
					AND (
                    (gplfunoid_responsavel = gmcfunoid) -- o responsavel do plano de ação da meta compartilhada é o proprio usuario
                    or (gplfunoid_responsavel = gmafunoid and gplcompartilhar = 1)  -- o responsável do plano de ação compartilhada é responsavel da meta compartilhada e o plano de ação está marcado para compartilhar
            )
            WHERE
                gmedt_exclusao IS NULL
            AND
				gmeano = " . intval($ano);
                

		if (!is_null($funoid)){
            
            //Traz as metas/planos de um usuário em especifico e seus decendentes.
			/*$sql .= " 
                AND (gmafunoid IN (
                    SELECT 
                        DISTINCT
                        sub.gmafunoid
                    FROM
                        gestao_meta_arvore AS sub
                    INNER JOIN
                        usuarios AS usu_sub ON (usu_sub.usufunoid = sub.gmafunoid) 
                    WHERE
                        sub.gmaano = " . intval($ano) . "
                    AND
                        dt_exclusao IS NULL
                    AND
                        (sub.gmafunoid = " . intval($funoid) . ")
                    OR 
                        (
                            sub.gmafunoid_superior = " . intval($funoid) . "
                            OR 
                            sub.gmafunoid_superior IN (
                                SELECT 
                                    gmafunoid 
                                FROM 
                                    gestao_meta_arvore 
                                WHERE 
                                    gmafunoid_superior = " . intval($funoid) . "
                                )
                            )
                        ) 
                    )";*/
		}

		$sql .= "
			order by 1,2,4,6
		";	

		if (!$rs = pg_query($this->conn, $sql)) {
			throw new ErrorException("Houve um erro no processamento de dados.");
		}
		
		$metas = array();
		$usufunoid_atual = 0;
		$meta_id = 0;

		while($row = pg_fetch_object($rs)) {



			if (!isset($metas[$row->funcionario_id])){
				$metas[$row->funcionario_id] = array();
			}


			if ($usufunoid_atual != $row->funcionario_id ||  $meta_id != $row->meta_id){

				$metas[$row->funcionario_id]['metas'][$row->meta_id]['meta'] = $row->meta;
				$metas[$row->funcionario_id]['metas'][$row->meta_id]['meta_id'] = $row->meta_id;
				if(!isset($metas[$row->funcionario_id]['metas'][$row->meta_id]['planos'])){
                    $metas[$row->funcionario_id]['metas'][$row->meta_id]['planos'] = array();
                }
				if (intval($row->acao_meta_id) > 0){
					$metas[$row->funcionario_id]['metas'][$row->meta_id]['planos'][] = array(
						'plano' 	=> $row->acao,
						'plano_id' 	=> $row->acao_meta_id
					);	
				}
		

				$usufunoid_atual = $row->funcionario_id;
				$meta_id = $row->meta_id;
			} else {
				if (intval($row->acao_meta_id) > 0){
					$metas[$row->funcionario_id]['metas'][$row->meta_id]['planos'][] = array(
						'plano' 	=> $row->acao,
						'plano_id' 	=> $row->acao_meta_id
					);	
				}
			}

		}
        
        /*echo '<pre>';
        var_dump($metas);
        echo '</pre>';*/
        
        return $metas;
	}

}