<?php

/**
 * Classe de persist�ncia de dados
 *
 * @author Felipe Pereira Augusto <felipe.augusto@meta.com.br>
 * @package Finan�as
 * @since 08/05/2020
 */

class FinMacrosMicrosMotivosDAO {

	private $conn;
	
	/*
	 * Construtor
	 */
	function __construct($conn) {

		$this->conn = $conn;
	}
	
	
	/** Pesquisa **/
	
	/**
	 * M�todo que executa a consulta para pesquisa
	 * @author Renato Teixeira Bueno <renato.bueno@meta.com.br>
	 */
	public function pesquisar($pWhere){
		
		$sql = "SELECT
		            pfmtipo as nivel,
					pfmmotivo as descricao,
					TO_CHAR(pfmdata_criacao, 'DD/MM/YYYY') as data_criacao,
					TO_CHAR(pfmdata_exclusao, 'DD/MM/YYYY') as data_exclusao,
					pfmoid		AS id
				FROM
					parametros_faturamento_motivos				
				WHERE true
				$pWhere  
		        ORDER BY pfmoid ASC";

		if(!$rs = pg_query($this->conn, $sql)){
			throw new Exception('Erro ao pesquisar registro.');
		}
		
		$result = array();
		
		if(pg_num_rows($rs) > 0){
			
			$arrResult = pg_fetch_all($rs);
			
			foreach($arrResult as $resultado){
				$result[] = array(
				    'nivel' => $resultado['nivel'],
				    'descricao' => $resultado['descricao'],
					'data_criacao' => $resultado['data_criacao'],
					'data_exclusao' => $resultado['data_exclusao'],
					'id'=> $resultado['id']
				);
				
		    }

		    return $result;
		}
		
		return false;
		
	}


    /**
     * M�todo que executa a consulta para buscar dos micros e macros motivos
     */
    public function buscarMacroMicroMotivo($varios_micromacro = NULL){

        try {

            $sql = " SELECT pfmoid as id, pfmmotivo as motivo, pfmtipo as tipo
				       FROM parametros_faturamento_motivos
				      WHERE pfmdata_exclusao IS NULL ";

            if(!empty($varios_micromacro)){
                $sql .= " AND pfmtipo = '$varios_micromacro' ";
            }

            $sql .= "ORDER BY pfmoid ";
            if(!$rs = pg_query($this->conn, $sql)){
                throw new Exception('Erro ao buscar os Micros e Macros Motivos.');
            }

            if (pg_num_rows($rs) > 0) {
                return pg_fetch_all($rs);
            }

            return false;

        } catch (Exception $e) {
            return $e->getMessage();
        }

    }

	public function insereParametro($valores = array()) {
		
		if (count($valores) == 0) {
			throw new Exception("Erro ao inserir par�metro. Op��es n�o informadas.");
		}
		
		$usuarioLogado 	= Sistema::getUsuarioLogado();
		$usuario 		= $usuarioLogado->cd_usuario; 
		
		// Campos
		$nivel					= "NULL";
		$descricao				= "NULL";

		// parfnivel integer NOT NULL, -- N�vel de parametriza��o: 1 - Contrato, 2 - Cliente ou 3 - Tipo de Contrato
		if(!empty($nivel)){
			$nivel = $valores['nivel'];
		}

        if(!empty($descricao)){
            $descricao = $valores['descricao'];
        }

		pg_query($this->conn, "BEGIN;");
		 
		$sql = "
			INSERT INTO 
				parametros_faturamento_motivos
			(
				pfmdata_criacao,
				pfmtipo,
				pfmmotivo
			)
			VALUES
			(
				NOW(),
				$nivel,
				'$descricao'
			);
		";

		//echo '<pre>';
		//print_r($sql); die;

		$result = pg_query($this->conn, $sql);
		
		if (!$result || pg_affected_rows($result) == 0) {
			
			pg_query($this->conn, "ROLLBACK;");
			throw new Exception("Erro ao inserir registro.");
			
		} else {
            pg_query($this->conn, "COMMIT;");

            return true;
		}
	}
	
	
	/**
	 * Atualiza o motivo de acordo com os dados fornecidos.
	 * @param Array $valores Dados para a atualização do motivo
	 * @throws Exception
	 */
	public function atualizaParametro($valores) {
		$usuarioLogado 	= Sistema::getUsuarioLogado();
		$usuario 		= $usuarioLogado->cd_usuario; // -- Usuário que realizou o cadastro.
		
		// Campos
		$pfmoid				        = null;
		$nivel					     = "NULL";
        $descricao				     = "NULL";

		if (count($valores) == 0) {
			throw new Exception("Erro ao atualizar motivo. Opções não informadas.");
		}

        // parfoid serial, -- Oid da tabela, PK.
        if (!empty($valores['pfmoid'])) {
            $pfmoid = $valores['pfmoid'];
        }

		// parfnivel integer NOT NULL, -- N�vel de parametriza��o: 1 - Contrato, 2 - Cliente ou 3 - Tipo de Contrato
		if (!empty($valores['nivel'])) {
			$nivel = $valores['nivel'];
		}
		
		// parfconoid integer, -- Id do contrato. Requerido para o n�vel 1.
		if (!empty($valores['descricao'])) {
            $descricao = $valores['descricao'];
		}

		pg_query($this->conn, "BEGIN;");
		
		$sql = "
		UPDATE
			parametros_faturamento_motivos
		SET
			pfmtipo 	= $nivel,
			pfmmotivo	= '$descricao'
		WHERE
			pfmoid = $pfmoid
		";

		$result = pg_query($this->conn, $sql);
		
		if (!$result || pg_affected_rows($result) == 0) {
            pg_query($this->conn, "ROLLBACK;");
			throw new Exception("Erro ao alterar registro.");
		} else {
            pg_query($this->conn, "COMMIT;");
            return true;
		}

	} 
	

	/**
	 * Seta a data de exclusão de um parâmetro
	 * @param integer $pfmoid
	 * @throws Exception
	 * @return boolean
	 */
	public function excluirParametro($pfmoid) {

		if (!isset($pfmoid) || empty($pfmoid) || !is_numeric($pfmoid)) {
			throw new Exception("Motivo nâo informado ou inválido.");
		}
		
		$usuarioLogado 	= Sistema::getUsuarioLogado();
		$usuario 		= $usuarioLogado->cd_usuario;

		$parametroFaturamento = $this->getMotivo($pfmoid);

		if (!$parametroFaturamento) {
			throw new Exception("Parâmetro não encontrado");
		}

		pg_query($this->conn, "BEGIN;");
		
		$numAffected = 0;
		$sql = "
			UPDATE
				parametros_faturamento_motivos
			SET
				pfmdata_exclusao = NOW()
			WHERE
				pfmoid = $pfmoid
		";
		$result = pg_query($this->conn, $sql);
		
		if (!$result) {
			pg_query($this->conn, "ROLLBACK;");
			throw new Exception("Erro ao excluir registro.");
		} else {
			$numAffected = pg_affected_rows($result);

			if ($numAffected == 0) {
				pg_query($this->conn, "ROLLBACK;");
				return false;
			} else {
				pg_query($this->conn, "COMMIT;");
				return true;
			}
		}
	}

	/**
	 * Valida se os parâmetros estão duplicados
	 * @param array $parametros
	 * @throws Exception
	 * @return boolean
	 */
	public function validarParametros($parametros = array()) {
		
		try {
		
			if(count($parametros) == 0) {
				throw new Exception("Erro: Nenhum parâmetro foi informado.");
			}
		
			$sql = " 
					SELECT	
						pfm.pfmoid
					FROM	
						parametros_faturamento_motivos pfm
					WHERE	
						pfm.pfmdata_exclusao IS NULL	
						AND (pfm.pfmtipo IS NULL)
					";
			
			switch($parametros['nivel']) {
				case 1 :
					$sql.= " AND pfm.pfmtipo = 'MACRO'
							 AND pfm.pfmmotivo = ".$parametros['descricao']." ";
					
					$tipo = 'macro'; //usado na exibição da mensagem
					break;
				case 2 :
					$sql.= " AND pfm.pfmtipo = 2
							 AND pfm.pfmmotivo = ".$parametros['descricao']." ";
					
					$tipo = 'micro'; //usado na exibição da mensagem
					break;
			}

			if(!$resultado = pg_query($this->conn, $sql)) {
				throw new Exception("Erro ao validar a duplicidade dos parâmetros de motivos.");
			}
			
			if(pg_num_rows($resultado)) {
				$msg = "Parâmetro de motivo, para o ".$tipo.", já cadastrado.";
				return $resultado;
			} else {
				return $resultado;
			}
			    
		} catch (Exception $e) {
			return $e->getMessage();
		}
	}

    /**
     * Retorna o parâmetro para fatuamento de acordo com o parfoid informado.
     * @param interger $parfoid
     * @throws Exception
     * @return object|null
     */
    public function getParametroMacroMicro($pfmoid) {

        $numRows = 0;
        $sql = "
		SELECT
			*
		FROM
			parametros_faturamento_motivos
		WHERE
			pfmoid = $pfmoid
		";
        $result = pg_query($this->conn, $sql);
        if (!$result) {
            throw new Exception("Erro ao buscar parâmetro para faturamento.");
        } else {

            $numRows = pg_num_rows($result);
            if ($numRows > 0) {
                return pg_fetch_object($result);
            } else {
                return null;
            }
        }
    }

    /**
     * Retorna o parâmetro para fatuamento de acordo com o parfoid informado.
     * @param interger $pfmoid
     * @throws Exception
     * @return object|null
     */
    public function getMotivo($pfmoid) {

        $numRows = 0;
        $sql = "
		SELECT
			*
		FROM
			parametros_faturamento_motivos
		WHERE
			pfmoid = $pfmoid
		";
        $result = pg_query($this->conn, $sql);
        if (!$result) {
            throw new Exception("Erro ao buscar motivo.");
        } else {

            $numRows = pg_num_rows($result);
            if ($numRows > 0) {
                return pg_fetch_object($result);
            } else {
                return null;
            }
        }
    }

}