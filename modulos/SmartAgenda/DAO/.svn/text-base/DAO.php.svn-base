<?php 

class DAO {


    protected $conn;

    const MENSAGEM_ERRO_PROCESSAMENTO = "Houve um erro no processamento dos dados.";

    public function __construct($conn = NULL){

        if(is_null($conn)){
            Global $conn;
        }
        $this->conn = $conn;
    }

    public function executarQuery($query){

        if(!$rs = pg_query($this->conn, $query)) {

            $msgErro = self::MENSAGEM_ERRO_PROCESSAMENTO;

            if( _AMBIENTE_ == 'LOCALHOST' || _AMBIENTE_ == 'DESENVOLVIMENTO' ) {
                $msgErro = "Erro ao processar a query: " . $query;
        }
            throw new ErrorException($msgErro);
        }
        return $rs;
    }


    public function begin() {
        $resultado = pg_query($this->conn, 'BEGIN');
        if (false === $resultado) {
            throw new Exception('Houve um erro ao iniciar a transação');
        }
    }

    public function commit(){
        $resultado = pg_query($this->conn, 'COMMIT');
        if (false === $resultado) {
            throw new Exception('Houve um erro ao commitar a transação');
        }
        pg_query($this->conn, 'COMMIT');
    }

    public function rollback() {
        $resultado = pg_query($this->conn, 'ROLLBACK');
        if (false === $resultado) {
            throw new Exception('Houve um erro ao executar o rollback a transação');
        }
    }
    
    public function savePoint($nome){
        pg_query($this->conn, 'SAVEPOINT ' . $nome);
    }

    public function rollbackSavePoint($nome){
        pg_query($this->conn, 'ROLLBACK TO SAVEPOINT ' . $nome);
    }

    public function inserirRegistro($tabela, $dados, $chavePrimaria = null) {
        $resultado = @pg_insert($this->conn, $tabela, $dados);
        if (false === $resultado) {
            throw new Exception(
                'Houve um erro ao inserir um registro na tabela "' 
                . $tabela . '" - ' . pg_last_error($this->conn)
            );
        } else if (!is_null($chavePrimaria)) {
            $sql = "SELECT CURRVAL(pg_get_serial_sequence('{$tabela}','{$chavePrimaria}'))";
            
            if (!$query = pg_query($this->conn, $sql)) {
                throw new Exception('Houve um erro ao recuperar o ID inserido.');
            }
            return pg_num_rows($query) ? pg_fetch_result($query, 0, 0) : array();
        } else {
            return true;
        }
    }
    
    public function atualizarRegistro($tabela, $dados, $condicao) {
        $resultado = pg_update($this->conn, $tabela, $dados, $condicao);
        if (false === $resultado) {
            throw new Exception(
                'Houve um erro ao atualizar um registro na tabela "' 
                . $tabela . '" - ' . pg_last_error($this->conn)
            );
        }
        return $resultado;
    }
    
    public function removerRegistro($tabela, $condicao) {
        $resultado = pg_delete($this->conn, $tabela, $condicao);
        if (false === $resultado) {
            throw new Exception('Houve um erro ao remover registros na tabela ' . $tabela);
        }
        return $resultado;
    }

    public function getUsuarioLogado(){
        return isset($_SESSION['usuario']['oid']) ? $_SESSION['usuario']['oid'] : '2750';
    }
}