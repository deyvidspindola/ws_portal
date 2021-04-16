<?php


class DAO {

    protected $conn;
    protected $usuarioLogado;

    const MENSAGEM_ERRO_PROCESSAMENTO = "Houve um erro no processamento dos dados.";

    public function __construct($conn = NULL){

        if(is_null($conn)){
            Global $conn;
        }
        $this->conn = $conn;

        $this->usuarioLogado = isset($_SESSION['usuario']['oid']) ? $_SESSION['usuario']['oid'] : '';
    }

    /** Abre a transação */
    public function begin(){
        pg_query($this->conn, 'BEGIN');
    }

    /** Finaliza um transação */
    public function commit(){
        pg_query($this->conn, 'COMMIT');
    }

    /** Aborta uma transação */
    public function rollback(){
        pg_query($this->conn, 'ROLLBACK');
    }

    public function executarQuery($query) {

        if(!$rs = pg_query($this->conn, $query)) {

            $msgErro = self::MENSAGEM_ERRO_PROCESSAMENTO;

            if( _AMBIENTE_ == 'LOCALHOST' || _AMBIENTE_ == 'DESENVOLVIMENTO' ) {
                $msgErro = "Erro ao processar a query: " . $query;
            }
            throw new ErrorException($msgErro);
        }
        return $rs;
    }

}