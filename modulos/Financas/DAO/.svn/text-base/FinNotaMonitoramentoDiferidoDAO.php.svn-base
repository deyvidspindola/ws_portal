<?php
/**
 * STI 84974 - Cadastro de NOTAS DE SAÍDA do tipo: Monitoramento Diferido.
 * Item 116
 *
 * @class FinNotaMonitoramentoDiferidoDAO
 * @author Bruno Bonfim Affonso - <bruno.bonfim@sascar.com.br>
 * @version 1.0
 * @since 15/12/2014
 */
class FinNotaMonitoramentoDiferidoDAO{
    private $conn;
    
    public function __construct(){
        if(strstr($_SERVER['HTTP_HOST'], 'teste')){
            $conn = pg_connect("dbname=sascar_d1 host=10.0.100.81 user=desenv");
        } else{
            global $conn;
        }
        
        //$conn = pg_connect("dbname=sascar_d1 host=10.0.100.81 user=desenv");
        $this->conn = $conn;
        
        if(!$conn){
            echo json_encode(array('msg' => 'N&atilde;o foi poss&iacute;vel estabelecer uma conex&atilde;o com o banco de dados.', 'tipo' => '#msgerro'));
            exit;
        }
    }
    
    /**
     * @return array retorna os tipos de séries de nota fiscal.
     */
    public function getSerie(){
        $sql = "SELECT
                    nfsserie
                FROM
                    nota_fiscal_serie
                WHERE
                    nfsdt_exclusao IS NULL
                ORDER BY
                    nfsserie;";
                
        $sql = pg_query($this->conn, $sql);
        
        if($sql){
            return pg_fetch_all($sql);
        }
    }
    
    /**
     * @param int $nota número da nota
     * @param string $serie
     * @return boolean
     */
    public function confirmar($nota, $serie){
        if($serie != 'SB'){
            $table = "nota_fiscal";
        } else{
            $table = "sbtec.nota_fiscal";
        }
        
        $nota = (int) $nota;
        
        if($nota > 0){
            $sql = "UPDATE
                        $table
                    SET
                        nflmonitoramento_diferido = 't'
                    WHERE
                        nflno_numero = $nota
                    AND
                        nflserie = '$serie';";
                        
            return pg_query($this->conn, $sql);
        } else{
            return false;
        }
    }
    
    /**
     * @param string $periodo (012012)
     * @param int $nota número da nota
     * @param string $serie
     * @return array
     */
    public function pesquisar($periodo, $nota, $serie){
        $clausula = "";
        
        if(!empty($nota) && !empty($serie)){
            $nota = (int) $nota;
            $clausula = "AND nflno_numero = $nota AND nflserie = '$serie'";
        }
        
        $sql = "SELECT
                    nflno_numero, nflserie, TO_CHAR(nfldt_emissao, 'MM/YYYY') as nfldt_emissao, nflmonitoramento_diferido
                FROM
                    nota_fiscal
                WHERE
                    TO_CHAR(nfldt_emissao, 'MMYYYY') = '$periodo'
                $clausula
                
                UNION ALL
                
                SELECT
                    nflno_numero, nflserie, TO_CHAR(nfldt_emissao, 'MM/YYYY') as nfldt_emissao, nflmonitoramento_diferido
                FROM
                    sbtec.nota_fiscal
                WHERE
                    TO_CHAR(nfldt_emissao, 'MMYYYY') = '$periodo'
                $clausula
                ORDER BY
                    nflmonitoramento_diferido DESC, nflserie ASC, nflno_numero ASC";
                    
        $sql = pg_query($this->conn, $sql);
        
        if(pg_num_rows($sql) > 0){
            return pg_fetch_all($sql);
        } else{
            return array();
        }
    }
    
    /**
     * Desvincula a nota como Monitoramento Diferido.
     * @param int $nota número da nota
     * @param string $serie
     * @return array
     */
    public function excluir($nota, $serie){
        if($serie != 'SB'){
            $table = "nota_fiscal";
        } else{
            $table = "sbtec.nota_fiscal";
        }
        
        $sql = "UPDATE
                    $table
                SET
                    nflmonitoramento_diferido = 'f'
                WHERE
                    nflno_numero = $nota
                AND
                    nflserie = '$serie';";
                    
        return pg_query($this->conn, $sql);
    }
}