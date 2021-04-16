<?php

/**
 * Classe GesUsuariosDAO.
 * Camada de modelagem de dados.
 *
 * @package Relatorio
 * @author  João Paulo Tavares da Silva <joao.silva@meta.com.br>
 *
 */
class GesUsuariosDAO{

	/**
     * Mensagem de erro padrão.
     *
     * @const String
     */
    const MENSAGEM_ERRO_PROCESSAMENTO = "Houve um erro no processamento dos dados.";

    private $conn;

	public function __construct(){
		global $conn;
        $this->conn = $conn;
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

        if (isset($param->depoid) AND $param->depoid != 'todos') {
            $sql.= "
                AND
                    prhdepoid = '".$param->depoid."'
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

    public function buscarFuncionarios(stdClass $param){

        $retorno = array();
        $sql = "SELECT 
                    funoid,
                    funnome AS funcionario,
                    gmpimportacao AS importacao,
                    gmpcriar_plano_acao AS criar_pa,
                    gmpcriar_acao AS criar_acao,
                    gmpsuper_usuario AS super_usuario,
                    depdescricao AS departamento
                FROM 
                    funcionario
                INNER JOIN
                    usuarios ON usuarios.usufunoid = funcionario.funoid  
                LEFT JOIN
                    gestao_meta_permissao ON gestao_meta_permissao.gmpfunoid = funcionario.funoid
                INNER JOIN 
                    perfil_rh ON perfil_rh.prhoid = usuarios.usucargooid 
                INNER JOIN 
                    departamento ON departamento.depoid = perfil_rh.prhdepoid
                WHERE 
                        funexclusao IS NULL
                    AND
                        fundemissao IS NULL
                    AND 
                        dt_exclusao IS NULL";

        if(!empty($param->depoid) and $param->depoid != 'todos'){
            
            $sql.="
                AND 
                   depoid = $param->depoid
            ";
        }

        if(!empty($param->prhoid) and $param->prhoid != 'todos'){

            $sql.="
                AND 
                   prhoid = $param->prhoid
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

    public function atualizarPermissoes($funoid, $permissoes){
        $sql = "SELECT 
                    gmpfunoid
                FROM 
                    gestao_meta_permissao
                WHERE 
                    gmpfunoid = $funoid
                LIMIT 1";

        if (!$rs = pg_query($this->conn, $sql)) {
            throw new ErrorException(self::MENSAGEM_ERRO_PROCESSAMENTO);
        }

        if(pg_num_rows($rs) == 0){
            $sqlAtualiza = "INSERT INTO 
                                gestao_meta_permissao
                            (
                                gmpfunoid,
                                gmpimportacao,
                                gmpcriar_plano_acao,
                                gmpcriar_acao,
                                gmpsuper_usuario    
                            )
                            VALUES
                            (
                                ". $funoid .",
                                ". $permissoes['importacao'] .",
                                ". $permissoes['criar_pa'] .",
                                ". $permissoes['criar_acao'] .",
                                ". $permissoes['super_usuario'] ."    
                            );";
        }else{
            $sqlAtualiza = "UPDATE 
                                gestao_meta_permissao
                            SET 
                                gmpimportacao = " . $permissoes['importacao'] .",
                                gmpcriar_plano_acao = ". $permissoes['criar_pa'] .",
                                gmpcriar_acao = ". $permissoes['criar_acao'] .",
                                gmpsuper_usuario = ". $permissoes['super_usuario'] ."
                            WHERE 
                                gmpfunoid = ". $funoid;
        }

        if (!pg_query($this->conn, $sqlAtualiza)) {
            throw new ErrorException(self::MENSAGEM_ERRO_PROCESSAMENTO);
        }
    }

    public function begin(){
        $sql = 'BEGIN;';
        if(!pg_query($this->conn, $sql)){
            throw new ErrorException(self::MENSAGEM_ERRO_PROCESSAMENTO);
        }
    }

    public function rollback(){
        $sql = 'ROLLBACK;';
        if(!pg_query($this->conn, $sql)){
            throw new ErrorException(self::MENSAGEM_ERRO_PROCESSAMENTO);
        }
    }
    public function commit(){
        $sql = 'COMMIT;';
        if(!pg_query($this->conn, $sql)){
            throw new ErrorException(self::MENSAGEM_ERRO_PROCESSAMENTO);
        }
    }
}