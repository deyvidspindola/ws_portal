<?php

/**
 * Classe GesEstruturaArvoreDAO.
 * Camada de modelagem de dados.
 *
 * @package  Gestao
 * @author   João Paulo Tavares da Silva <joao.silva@meta.com.br>
 * 
 */
class GesEstruturaArvoreDAO {

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
                    gmaano,
    				gmaoid,
    				gmanome, 
    				gmanivel, 
    				gmasubnivel, 
    				fun.funnome AS funcionario, 
    				depdescricao AS departamento, 
    				prhperfil AS cargo, 
    				superior.funnome AS superior_imediato
    			FROM 
    				gestao_meta_arvore
    			INNER JOIN funcionario AS fun ON fun.funoid = gestao_meta_arvore.gmafunoid
    			INNER JOIN departamento ON departamento.depoid = gestao_meta_arvore.gmadepoid
    			INNER JOIN perfil_rh ON gestao_meta_arvore.gmaprhoid = perfil_rh.prhoid 
    			LEFT JOIN funcionario AS superior ON superior.funoid = gestao_meta_arvore.gmafunoid_superior
    			WHERE 
    				1 = 1 ";

        if ( isset($parametros->gmaano) && trim($parametros->gmaano) != '' ) {

            $sql .= "AND
                        gmaano = " . intval( $parametros->gmaano ) . "";
            
        }

        if ( isset($parametros->gmanome) && !empty($parametros->gmanome) ) {
        
            $sql .= "AND
                        gmanome ilike '%" . pg_escape_string( $parametros->gmanome ) . "%'";
                
        }

        if ( isset($parametros->gmafunoid) && trim($parametros->gmafunoid) != '' ) {

            $sql .= "AND
                        gmafunoid = " . intval( $parametros->gmafunoid ) . "";
            
        }

        if ( isset($parametros->gmadepoid) && trim($parametros->gmadepoid) != '' ) {

            $sql .= "AND
                        gmadepoid = " . intval( $parametros->gmadepoid ) . "";
            
        }

        if ( isset($parametros->gmaprhoid) && trim($parametros->gmaprhoid) != '' ) {

            $sql .= "AND
                        gmaprhoid = " . intval( $parametros->gmaprhoid ) . "";
            
        }

        $sql.=" ORDER BY gmanivel, gmasubnivel";
        
        if (!$rs = pg_query($this->conn, $sql)){
            throw new ErrorException(self::MENSAGEM_ERRO_PROCESSAMENTO);
        }

        while($row = pg_fetch_object($rs)){
            $retorno[] = $row;
        }
       
        //echo '<pre>', $sql, '</pre>';

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
					gmaoid, 
					gmaano, 
					gmanome, 
					gmanivel, 
					gmasubnivel, 
					gmafunoid, 
					gmadepoid, 
					gmaprhoid, 
					gmafunoid_superior
				FROM 
					gestao_meta_arvore
				WHERE 
					gmaoid =" . intval( $id ) . "";

		if (!$rs = pg_query($this->conn, $sql)){
			throw new ErrorException(self::MENSAGEM_ERRO_PROCESSAMENTO);
		}

		if (pg_num_rows($rs) > 0){
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
	public function inserir(stdClass $dados){
		
        $sql = "INSERT INTO
					gestao_meta_arvore
					(
					gmaano,
					gmanome,
					gmanivel,
					gmasubnivel,
					gmafunoid,
					gmadepoid,
					gmaprhoid,
					gmafunoid_superior
					)
				VALUES
					(
					" . intval( $dados->gmaano ) . ",
					'" . pg_escape_string( $dados->gmanome ) . "',
					" . intval( $dados->gmanivel ) . ",
					" . intval( $dados->gmasubnivel ) . ",
					" . intval( $dados->gmafunoid ) . ",
					" . intval( $dados->gmadepoid ) . ",
					" . intval( $dados->gmaprhoid ) . ",";
				

        if(!empty($dados->gmafunoid_superior)){
            $sql.= intval( $dados->gmafunoid_superior );
        }else{
            $sql.="NULL";
        }
        
        $sql .=");";

		if (!pg_query($this->conn, $sql)){
			throw new ErrorException(self::MENSAGEM_ERRO_PROCESSAMENTO);
		}

		return true;
	}
    
    
	public function atualizar(stdClass $dados){
		
        $sql = "
            UPDATE 
                gestao_meta_arvore
            SET
                gmafunoid   = " . intval( $dados->gmafunoid ) . ",
                gmadepoid   = " . intval( $dados->gmadepoid ) . ",
                gmaprhoid   = " . intval( $dados->gmaprhoid ) . "
            WHERE
                gmaoid = " . intval( $dados->gmaoid );

		if (!pg_query($this->conn, $sql)){
			throw new ErrorException(self::MENSAGEM_ERRO_PROCESSAMENTO);
		}

		return true;
	}
    
    
    public function atualizarFuncionarioArvore($funcionarioAtual, $funcionarioNovo){
        
        $sql = "
            UPDATE 
                gestao_meta_arvore
            SET
                gmafunoid_superior = " . intval($funcionarioNovo) . "
            WHERE
                gmafunoid_superior = " . intval($funcionarioAtual);    
        
		if (!pg_query($this->conn, $sql)){
			throw new ErrorException(self::MENSAGEM_ERRO_PROCESSAMENTO);
		}
            
		return true;
        
    }
    public function atualizarFuncionarioPlanos($funcionarioAtual, $funcionarioNovo){
        
        $sql = "
            UPDATE 
                gestao_meta_plano_acao
            SET
                gplfunoid_responsavel = " . intval($funcionarioNovo) . "
            WHERE
                gplfunoid_responsavel = " . intval($funcionarioAtual);    
        
		if (!pg_query($this->conn, $sql)){
			throw new ErrorException(self::MENSAGEM_ERRO_PROCESSAMENTO);
		}
            
		return true;
        
    }
    
    public function atualizarFuncionarioMetas($funcionarioAtual, $funcionarioNovo){
        $sql = "
            UPDATE 
                gestao_meta
            SET
                gmefunoid_responsavel = " . intval($funcionarioNovo) . "
            WHERE
                gmefunoid_responsavel = " . intval($funcionarioAtual);    
        
		if (!pg_query($this->conn, $sql)){
			throw new ErrorException(self::MENSAGEM_ERRO_PROCESSAMENTO);
		}
        
        $sql = "
            UPDATE 
                gestao_meta_compartilhada
            SET
                gmcfunoid = " . intval($funcionarioNovo) . "
            WHERE
                gmcfunoid = " . intval($funcionarioAtual);    
        
		if (!pg_query($this->conn, $sql)){
			throw new ErrorException(self::MENSAGEM_ERRO_PROCESSAMENTO);
		}

		return true;
        
    }
    public function atualizarFuncionarioAcoes($funcionarioAtual, $funcionarioNovo){
        
        $sql = "
            UPDATE 
                gestao_meta_acao
            SET
                gmafunoid_responsavel = " . intval($funcionarioNovo) . "
            WHERE
                gmafunoid_responsavel = " . intval($funcionarioAtual);  
        
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

		$sql = "DELETE FROM
					gestao_meta_arvore
				WHERE
					gmaoid = " . intval( $id ) . "";

		if (!pg_query($this->conn, $sql)){
			throw new ErrorException(self::MENSAGEM_ERRO_PROCESSAMENTO);
		}

        $_SESSION['cache_arvore']['atualizado'] = strtotime(date('Y-m-d H:i:s'));
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

 	public function buscarDepartamentos(){

        $retorno = array();

        $sql = "SELECT 
                    depoid,
                    depdescricao,
                    depexclusao
                FROM 
                    departamento
                WHERE 
                    depexclusao = null
                ORDER BY depdescricao";

        if (!$rs = pg_query($this->conn, $sql)) {
            throw new ErrorException(self::MENSAGEM_ERRO_PROCESSAMENTO);
        }

        while ($registro = pg_fetch_object($rs)) {
            $retorno[] = $registro;
        }

        return $retorno;
    }

    public function buscarCargos(stdClass $param) {
        
        $retorno = array();
        $sql     = "
            SELECT
                prhoid,
                prhperfil,
                prhtipusuario
            FROM
                perfil_rh
            WHERE
               prhexclusao = null
        ";

        if (isset($param->gmadepoid) AND $param->gmadepoid != 'todos') {
            $sql.= "
                AND
                    prhdepoid = '".$param->gmadepoid."'
            ";
        }
    
        $sql.= "
            ORDER BY
                prhperfil
        ";
           
        if (!$rs = pg_query($this->conn, $sql)) {
            throw new ErrorException(self::MENSAGEM_ERRO_PROCESSAMENTO);
        }

        while ($registro = pg_fetch_object($rs)) {
            $retorno[] = $registro;
        }

        return $retorno;
    }

    public function buscarTodosFuncionarios(){
    	  $retorno = array();
        $sql = "SELECT 
                    funoid,
                    funnome AS funcionario
                FROM 
                    funcionario
                WHERE 
                        funexclusao = null 
                    AND
                        fundemissao = null
                ORDER BY funnome";

            
        if (!$rs = pg_query($this->conn, $sql)) {
            throw new ErrorException(self::MENSAGEM_ERRO_PROCESSAMENTO);
        }

        while ($registro = pg_fetch_object($rs)) {
            $retorno[] = $registro;
        }

        return $retorno;
    }

    public function buscarFuncionarios(stdClass $param){

        $retorno = array();
        $sql = "SELECT DISTINCT ON (funnome)
                    funoid,
                    funnome AS funcionario
                FROM 
                    funcionario
                INNER JOIN usuarios ON usuarios.usufunoid = funcionario.funoid
                INNER JOIN perfil_rh ON perfil_rh.prhoid = usuarios.usucargooid
                WHERE 
                    funexclusao IS NULL
                AND
                    fundemissao IS NULL
                AND
                    dt_exclusao IS NULL";

        if(!empty($param->gmadepoid) and $param->gmadepoid != 'todos'){
            
            $sql.="
                AND 
                   prhdepoid = $param->gmadepoid
            ";
        }

        if(!empty($param->gmaprhoid) and $param->gmaprhoid != 'todos'){

            $sql.="
                AND 
                   usucargooid = $param->gmaprhoid
            ";   
        }

        $sql.=" ORDER BY funnome";
            
        if (!$rs = pg_query($this->conn, $sql)) {
            throw new ErrorException(self::MENSAGEM_ERRO_PROCESSAMENTO);
        }

        while ($registro = pg_fetch_object($rs)) {
            $retorno[] = $registro;
        }

        return $retorno;
    }

    public function validarExclusao(stdClass $parametros){

        $sql = "SELECT 
                    gmaano
                FROM 
                    gestao_meta_arvore
                WHERE 
                    gmanivel = $parametros->gmanivel AND
                    gmaano = $parametros->gmaano";
                      
        if (!$rs = pg_query($this->conn, $sql)) {
            throw new ErrorException(self::MENSAGEM_ERRO_PROCESSAMENTO);
        }
            
        if(pg_num_rows($rs) == 1){
             $sql = "SELECT 
                        gmaano
                    FROM 
                        gestao_meta_arvore
                    WHERE 
                        gmanivel > $parametros->gmanivel AND
                        gmaano = $parametros->gmaano
                    LIMIT 1";

            if (!$rs = pg_query($this->conn, $sql)) {
               throw new ErrorException(self::MENSAGEM_ERRO_PROCESSAMENTO);
            }        
            
            if(pg_num_rows($rs) == 0){
                return true;
            }        
            return false;
        }else{

            $sql = "SELECT 
                        gmaano
                    FROM 
                        gestao_meta_arvore
                    WHERE 
                        gmanivel = $parametros->gmanivel AND
                        gmasubnivel = $parametros->gmasubnivel AND
                        gmaano = $parametros->gmaano;";

            if (!$rs = pg_query($this->conn, $sql)) {
                throw new ErrorException(self::MENSAGEM_ERRO_PROCESSAMENTO);
            }      
               
            if(pg_num_rows($rs) > 1){

                return true;
            }else{
                $sql = "SELECT 
                            gmaano
                        FROM 
                            gestao_meta_arvore
                        WHERE 
                            gmanivel = $parametros->gmanivel AND
                            gmasubnivel > $parametros->gmasubnivel AND
                            gmaano = $parametros->gmaano
                        LIMIT 1";

                if (!$rs = pg_query($this->conn, $sql)) {
                   throw new ErrorException(self::MENSAGEM_ERRO_PROCESSAMENTO);
                }

                if(pg_num_rows($rs) == 1){
                    return false;
                }
                return true;
            }
        }
    }
}
?>
