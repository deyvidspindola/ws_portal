<?php

/**
 * @author Felipe Ribeiro <felipe.ribeiro@ewave.com.br>
 * @since 01/07/2014
 */

class ManPoliticaDescontoDAO {
    
    private $conn = null;

    private $aplicacaoList = array();

    function __construct(){	
        
        global $conn;
        
        $this->conn = $conn;

        $this->aplicacaoList = array(
            "J"   => "Juros",
            "M"   => "Multa",
            "JM"  => "Juros e Multa",
            "TJM" => "Total, Juros e Multa"
        );
    }

    /**
    * Busca todas as Políticas de Desconto
    * @return array
    */
    public function getAll() {

        $sql = "
                SELECT 
                    podoid,
                    poddescricao_atraso,
                    poddias_atraso_ini,
                    poddias_atraso_fim,
                    podvlr_desconto,
                    CASE 
                        WHEN podaplicacao = 'J'   THEN 'Juros'
                        WHEN podaplicacao = 'M'   THEN 'Multa'
                        WHEN podaplicacao = 'JM'  THEN 'Juros e Multa'
                        WHEN podaplicacao = 'TJM' THEN 'Total, Juros e Multa'
                        ELSE 'Aplicação Inválida'
                    END as podaplicacao
                FROM 
                    politica_desconto
                ORDER BY 
                    podoid;";

        return $this->fetchArray($sql);
    }

    /**
    * Busca a política de desconto de acordo com o $podoid informado
    * @param int $podoid
    * @return array
    */
    public function getById($podoid){
        
       $sql = "
                SELECT 
                    podoid,
                    poddescricao_atraso,
                    poddias_atraso_ini,
                    poddias_atraso_fim,
                    podvlr_desconto,
                    poddias_atraso_ini, 
                    poddias_atraso_fim,
                    CASE 
                        WHEN podaplicacao = 'J'   THEN 'Juros'
                        WHEN podaplicacao = 'M'   THEN 'Multa'
                        WHEN podaplicacao = 'JM'  THEN 'Juros e Multa'
                        WHEN podaplicacao = 'TJM' THEN 'Total, Juros e Multa'
                        ELSE 'Aplicação Inválida'
                    END as podaplicacao
                FROM 
                    politica_desconto
                WHERE 
                    podoid = $podoid
                ORDER BY 
                    podoid;";
                    
        $politicaDesconto = $this->fetchArray($sql);
        return $politicaDesconto[0];
    }

    /**
    * Busca as aplicações de desconto
    * @return array
    */
    public function getAplicacaoList() {

        return $this->aplicacaoList;
    }

    /**
    * Busca as aplicações de desconto
    * @return array
    */
    public function getAplicacaoById($aplicacao) {

        return $this->aplicacaoList[$aplicacao];
    }

    /**
    * Busca o histórico de alteração das Políticas de Desconto
    * @return array
    */
    public function getHistorico() {

        $sql = "
                SELECT 
                    poddescricao_atraso,
                    nm_usuario, 
                    hipdalteracao,
                    to_char(hipddt_alteracao, 'DD/MM/YYYY  HH24:MI:SS') as hipddt_alteracao
                FROM 
                    historico_politica_desconto 
                INNER JOIN 
                    usuarios ON cd_usuario = hipdusuoid
                INNER JOIN
                    politica_desconto ON podoid = hipdpodoid
                ORDER BY 
                      hipddt_alteracao::TIMESTAMP DESC ";

        return $this->fetchArray($sql, true);
    }

    /**
    * Popula o array com o resultado do sql
    * @param $sql
    * @return array
    */
    public function fetchArray($sql, $historico = false) {

        $result = null;

        $sql = pg_query($this->conn, $sql);

        while($rs = pg_fetch_array($sql)) { 
            
            if($historico) {
                 $result[] = array(
                    "poddescricao_atraso" => $rs["poddescricao_atraso"],
                    "nm_usuario"          => $rs["nm_usuario"], 
                    "hipdalteracao"       => $rs["hipdalteracao"],
                    "hipddt_alteracao"    => $rs["hipddt_alteracao"]
                );
            }
            else {
                $result[] = array(
                    "podoid"              => $rs["podoid"], 
                    "poddescricao_atraso" => $rs["poddescricao_atraso"],
                    "poddias_atraso_ini"  => $rs["poddias_atraso_ini"],
                    "poddias_atraso_fim"  => $rs["poddias_atraso_fim"],
                    "podvlr_desconto"     => $this->numberFormatUsBr($rs["podvlr_desconto"]), 
                    "podaplicacao"        => $rs['podaplicacao']
                		
                );
            }
        }

        return $result;
    }
    
    /**
    * Atualiza uma política de desconto
    */
    public function update() {

        $politicaDesconto = array(
            "podoid"          => $_POST['podoid'],
            "podvlr_desconto" => $this->numberFormatBrUs($_POST["podvlr_desconto"]),
            "podaplicacao"    => $_POST['podaplicacao']
        );

        $this->insertHistorico();
        
        $sql = "
                UPDATE
                    politica_desconto
                SET
                    podvlr_desconto = "  . $politicaDesconto['podvlr_desconto'] . ",
                    podaplicacao    = '" . $politicaDesconto['podaplicacao'] . "'    
                WHERE
                    podoid = " . $politicaDesconto['podoid'];
        
        pg_query($this->conn, $sql);
    }

    
    /**
    * Insere a alteração na tabela de histórico
    */
    public function insertHistorico() {
        
        $alteracaoHistorico = array();

        $politicaDesconto = $this->getById($_POST['podoid']);

        if($politicaDesconto['podvlr_desconto'] != $_POST['podvlr_desconto']) {
            $alteracaoHistorico[] = "Desconto alterado de " . $politicaDesconto["podvlr_desconto"] . "% para " . $_POST["podvlr_desconto"] . "%";
        }

        if($politicaDesconto['podaplicacao'] != $this->getAplicacaoById($_POST["podaplicacao"])) {
            $alteracaoHistorico[] = "Aplicação alterada de " . $politicaDesconto["podaplicacao"] . " para " . $this->getAplicacaoById($_POST["podaplicacao"]);
        }

        if(!empty($alteracaoHistorico)) {
            
            $strAlteracao = "";

            foreach($alteracaoHistorico as $alteracao) {
                $strAlteracao .= $alteracao . "<br>";
            }

            $sql  = "
                    INSERT INTO 
                        historico_politica_desconto(
                            hipdpodoid, 
                            hipdusuoid, 
                            hipdalteracao
                        )
                    VALUES (
                        " . $politicaDesconto['podoid'] . ", 
                        " . $_SESSION['usuario']['oid'] . ", 
                        '" . $strAlteracao . "'
                    )";
                                  
            pg_query($this->conn, $sql);
        }
    }

    /**
    * Valida as informações do formulário
    * @return array
    */
    public function formValidation() {
        
        $response = array();

        if($_POST['podvlr_desconto'] == "") {
            $response = array(
                "message" => "O valor do desconto é obrigatório",
                "class"   => "mensagem erro"
            );
        }

        if($_POST['podvlr_desconto'] < 0 || $this->numberFormatBrUs($_POST['podvlr_desconto']) > 100.00) {
            $response = array(
                "message" => "O valor do desconto deve estar entre 0 e 100",
                "class"   => "mensagem erro"
            );
        }

        if( !is_numeric($this->numberFormatBrUs($_POST["podvlr_desconto"])) ) {

            $response = array(
                "message" => "O valor do desconto deve ser numérico",
                "class"   => "mensagem erro"
            );
        }
        
        return $response;
    }

    /** 
     * Converte número do formato brasileiro para americano
     * @param string $num
     */
    public function numberFormatBrUs($num) {
        $num = str_replace(".", "", "$num");
        $num = str_replace(",", ".", "$num");
        return $num;
    }

    /** 
     * Converte número do formato americano para brasileiro
     * @param string $num
     */
    public function numberFormatUsBr($num) {
        $num = str_replace(",", "", "$num");
        $num = str_replace(".", ",", "$num");
        return $num;
    }
}
?>