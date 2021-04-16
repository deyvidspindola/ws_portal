<?php

/**
 * Classe CadGrupoControleDocumentoDAO.
 * Camada de modelagem de dados.
 *
 * @package  Cadastro
 * @author   Robson Aparecido Trizotte da Silva <robson.silva@meta.com.br>
 * 
 */
class CadGrupoControleDocumentoDAO {

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
					itseoid, 
					itsedescricao
				FROM 
					instr_trabalho_segmento
				WHERE 
					1 = 1
				AND 
					itsedt_exclusao IS NULL ";

        if ( isset($parametros->itsedescricao) && !empty($parametros->itsedescricao) ) {
        
            $sql .= "AND
                        itsedescricao ILIKE '%" . pg_escape_string( $parametros->itsedescricao ) . "%'";
                
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
 * Método para realizar a pesquisa de apenas um registro.
 * 
 * @param int $id Identificador único do registro
 * @return stdClass
 * @throws ErrorException
 */
public function pesquisarPorID($id){

$retorno = new stdClass();

$sql = "SELECT 
					itseoid, 
					itsedescricao, 
					itsedt_cadastro, 
					itseusuario, 
					itsedt_exclusao
				FROM 
					instr_trabalho_segmento
				WHERE 
					itseoid =" . intval( $id ) . "";

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


$usuarioInclusao = isset($_SESSION['usuario']['oid']) && trim($_SESSION['usuario']['oid']) != '' ? trim($_SESSION['usuario']['oid']) : '';

$sql = "INSERT INTO
					instr_trabalho_segmento
					(
					itsedescricao,
					itsedt_cadastro,
					itseusuario
					)
				VALUES
					(
					'" . pg_escape_string( $dados->itsedescricao ) . "',
					NOW(),
					" . $usuarioInclusao . "
				)";

if (!pg_query($this->conn, $sql)){
throw new ErrorException(self::MENSAGEM_ERRO_PROCESSAMENTO);
}

return true;
}


public function verificarExistenciaGrupo(stdClass $parametros) {


	$sql = "SELECT 
					itsedescricao
			FROM
					instr_trabalho_segmento
			WHERE
					LOWER(itsedescricao) = '" . pg_escape_string( strtolower($parametros->itsedescricao) ) . "' 
			AND
					itsedt_exclusao IS NULL ";

			if ( isset($parametros->itseoid) && $parametros->itseoid != 0) {

				$sql .= " AND 
								itseoid <> " . $parametros->itseoid;

			}
	$sql .= " LIMIT 1";


	if (!$rs = pg_query($this->conn, $sql)){
		throw new ErrorException(self::MENSAGEM_ERRO_PROCESSAMENTO);
	}

	if (pg_num_rows($rs) > 0) {
		return true;
	}

	return false;
 
}

/**
 * Responsável por atualizar os registros 
 * @param stdClass $dados Dados a serem gravados
 * @return boolean
 * @throws ErrorException
 */
public function atualizar(stdClass $dados){

$sql = "UPDATE
					instr_trabalho_segmento
				SET
					itsedescricao = '" . pg_escape_string( $dados->itsedescricao ) . "'
				WHERE 
					itseoid = " . $dados->itseoid . "";

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
					instr_trabalho_segmento
				SET
					itsedt_exclusao = NOW() 
				WHERE
					itseoid = " . intval( $id ) . "";

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
?>
