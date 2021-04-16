<?php

namespace modulos\Commun\DAO;
use Exception;

/**
 * 
 * @author alexandre.reczcki
 *
 */
abstract class AbstractDAO
{
    private $conn;
    private $connGerenciadora;
    private $connCargoTracck;
    
    private static $__MSG_ERRO_CONEXAO_INTRANET     = "Erro ao conectar ao banco de dados da Intranet";
    private static $__MSG_ERRO_CONEXAO_GERENCIADORA = "Erro ao conectar ao banco de dados da Gerenciadora";
    private static $__MSG_ERRO_CONEXAO_CARGOTRACCK = "Erro ao conectar ao banco de dados da Cargo Tracck";
    
    /**
     * 
     * @throws Exception
     * @return resource
     */
    public function obterConexaoIntranet(){
        if($this->conn == NULL){
            global $dbstring;
            if (! $this->conn = pg_connect ($dbstring)) {
                throw new Exception(self::$__MSG_ERRO_CONEXAO_INTRANET);
            }
        }
        return $this->conn;
    }
    
    /**
     * 
     * @throws Exception
     * @return resource
     */
    public function obterConexaoGerenciadoraBD(){
        if($this->connGerenciadora == NULL){
            global $dbstring_gerenciadoras;
            if (! $this->connGerenciadora = pg_connect ($dbstring_gerenciadoras)) {
                throw new Exception(self::$__MSG_ERRO_CONEXAO_GERENCIADORA);
            }
        }
        return $this->connGerenciadora;
    }
    
    /**
     * 
     * @throws Exception
     * @return resource
     */
    public function obterConexaoGerenciadoraCargoTracck(){
        if($this->connCargoTracck == NULL){
            global $dbstring_CargoTrack;
            if (! $this->connCargoTracck = pg_connect($dbstring_CargoTrack)) {
                throw new Exception(self::$__MSG_ERRO_CONEXAO_CARGOTRACCK);
            }
        }
        return $this->connCargoTracck;
    }
    
    /**
     * Abre a transação
     * @param string $conexao
     */
    public function begin($conexao){
        pg_query($conexao, 'BEGIN');
    }
    
    /**
     * Finaliza um transação
     * @param String $conexao
     */
    public function commit($conexao){
        pg_query($conexao, 'COMMIT');
    }
    
    /**
     * Aborta uma transação
     * @param String $conexao
     */
    public function rollback($conexao){
        pg_query($conexao, 'ROLLBACK');
    }
    
    public function executarQuery($conexao, $query) {
        if(!$rs = pg_query($conexao, $query)) {
            throw new Exception("ERRO AO EXECUTAR QUERY");
        }
        return $rs;
    }
    
}