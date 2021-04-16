<?php

/**
 * Classe de persistência do banco de dados
 *
 * @package Gestão
 * @author  André Luiz Zilz <andre.zilz@meta.com.br>
 */
class GesGraficoMetaDAO {

    /*
     * Mensagem de erro padrão.
     */
    const MENSAGEM_ERRO_PROCESSAMENTO = "Houve um erro no processamento dos dados.";

    /*
     * Objeto de conexão com o Banco
     */
    public $conn;

    /**
     * Contrutor da Classe
     * @param object $conn
     */
	public function __construct($conn){
        $this->conn = $conn;
	}

    /**
     * Busca dados complementares relativas a meta
     *
     * @param int $gmeoid
     * @return array
     * @throws Exception
     */
    public function buscarDadosComplementares($gmeoid, $ano) {

        $retorno = new stdClass;

        $sql = "
            SELECT
                gmenome
                ,gmeano
                ,gmeformula
                ,gmepeso
                ,gmelimite_superior
                ,gmelimite_inferior
                ,gmelimite
                ,gmetipo AS periodo
                ,gmeprecisao
                ,(
                    CASE WHEN
                        gmedirecao = 'D'
                    THEN
                        'Diretamente'
                    ELSE
                        'Indiretamente'
                    END
                ) AS direcao
                ,gmemetrica
                ,gmecodigo
                ,funnome
                ,(
                    SELECT
                        COUNT(1)
                    FROM
                        gestao_meta_plano_acao
                    WHERE
                        gplgmeoid = ".intval($gmeoid)."
                    ) AS qtde_plano_acao
                ,(SELECT
                        STRING_AGG(funnome, ', ')
                    FROM
                        gestao_meta_compartilhada
                    INNER JOIN
                        funcionario ON funoid = gmcfunoid
                    WHERE
                        gmcgmeoid = ".intval($gmeoid).") AS compartilhado
            FROM
                gestao_meta
            INNER JOIN
                funcionario ON funoid = gmefunoid_responsavel
            WHERE
                gmeoid = ".intval($gmeoid)."
            AND
                gmeano = ".intval($ano)."
        ";
//echo "<pre>" . $sql;exit;
        if (!$rs = pg_query($this->conn, $sql)) {
            throw new Exception (self::MENSAGEM_ERRO_PROCESSAMENTO);
        }

       if (pg_num_rows($rs) > 0) {

            $dados = pg_fetch_object($rs);

            $dados->gmepeso             = isset($dados->gmepeso )           ? number_format($dados->gmepeso, 2, ',','.')            : '0,00';
            $dados->gmelimite_superior  = isset($dados->gmelimite_superior) ? number_format($dados->gmelimite_superior, 2, ',','.') : '0,00';
            $dados->gmelimite_inferior  = isset($dados->gmelimite_inferior) ? number_format($dados->gmelimite_inferior, 2, ',','.') : '0,00';
            $dados->gmelimite           = isset($dados->gmelimite)          ? number_format($dados->gmelimite, 2, ',','.')          : '0,00';
            $dados->gmeprecisao         = isset($dados->gmeprecisao)        ? number_format($dados->gmeprecisao, 2, ',','.')        : '0,00';

            $retorno = $dados;
        }

        return $retorno;

    }

    /**
     * Busca os dados principais para geração dos gráficos
     *
     * @param stdClass $param
     * @return array
     * @throws Exception
     */
    public function buscarDadosGrafico (stdClass $param) {

        $retorno = array();

        $sql = "
            SELECT DISTINCT
                mes
                ,gmenome
                ,gmeano
                ,gmeformula
                ,gmepeso
                ,gmelimite_superior
                ,gmelimite_inferior
                ,gmelimite
                ,gmetipo
                ,gmeprecisao
                ,gmedirecao
                ,gmemetrica
                ,gmecodigo
                ,SUM(gimvalor_previsto)	OVER (PARTITION BY mes, gmecodigo) AS previsto
                ,funnome
                ,qtde_plano_acao
                ,gmeoid
            FROM
                (
                SELECT
                    TO_CHAR(gimdata, 'mm') AS mes
                    ,gmenome
                    ,gmeano
                    ,gmeformula
                    ,gmepeso
                    ,gmelimite_superior
                    ,gmelimite_inferior
                    ,gmelimite
                    ,gmetipo
                    ,gmeprecisao
                    ,gmedirecao
                    ,gmemetrica
                    ,gmecodigo
                    ,gimvalor_previsto
                    ,funnome
                    ,gmeoid
                    ";

        if ( isset($param->idFuncionario) && $param->idFuncionario > 0 ) {
            $sql .= " ,(SELECT COUNT(1) FROM gestao_meta_plano_acao WHERE gplfunoid_responsavel = " . $param->idFuncionario . ") AS qtde_plano_acao";

        } else {
            $sql .= " ,(SELECT COUNT(1) FROM gestao_meta_plano_acao WHERE gplgmeoid = ". intval($param->metaid).") AS qtde_plano_acao";

        }

        $sql .= "
                    ,gmicodigo
                    ,gimvalor_realizado
                    ,gimgmioid
                FROM
                    gestao_meta
                INNER JOIN
                    gestao_meta_indicadores_meta ON gimgmeoid = gmeoid
                INNER JOIN
                    gestao_meta_indicadores ON gimgmioid = gmioid
                INNER JOIN
                    funcionario ON funoid = gmefunoid_responsavel
                WHERE";

        if ( isset($param->idFuncionario) && $param->idFuncionario > 0 ) {
            $sql .= "
                gmefunoid_responsavel = " . $param->idFuncionario;
            $sql .= "
                AND gmeformula != ''";
        } else {
            $sql .= "
                gmeoid =  ". intval($param->metaid);
        }

        $sql .= "
                AND
                    gmeano =  ". intval($param->ano)."
                AND
                    gimdata BETWEEN '01/01/". intval($param->ano)."' AND '31/12/". intval($param->ano)."'

                ) AS FOO
            ORDER BY mes
            ";

//echo "<pre>" . $sql;exit;
         if (!$rs = pg_query($this->conn, $sql)) {
            throw new Exception (self::MENSAGEM_ERRO_PROCESSAMENTO);
        }

        while ($tupla = pg_fetch_object($rs)) {
            $retorno[] = $tupla;
        }

        return $retorno;

    }

     /**
     * Busca os dados relativos aos indicadores
     *
     * @param stdClass $param
     * @return array
     * @throws Exception
     */
    public function buscarDadosIndicadores (stdClass $param) {

        $retorno = array();
        $indicadores = array();
        $indicadoresVerificacaoFormula = array();

        $sql = "
            SELECT	DISTINCT
                mes
                ,SUM(valor_indicador) OVER (PARTITION BY mes, gimgmioid) AS valor
                ,gmicodigo
                ,gmitipo_indicador
            FROM
                (
                SELECT
                    TO_CHAR(gimdata, 'mm') AS mes
                    ,gmicodigo
                    ,gimgmioid
                    ,gmitipo_indicador
                     ,(
                        CASE WHEN
                            gmitipo_indicador = 'M'
                        THEN
                            gimvalor_previsto
                        ELSE
                            gimvalor_realizado
                        END
                        ) AS valor_indicador
                FROM
                    gestao_meta
                INNER JOIN
                    gestao_meta_indicadores_meta ON gimgmeoid = gmeoid
                INNER JOIN
                    gestao_meta_indicadores ON (gimgmioid = gmioid)
                INNER JOIN
                    funcionario ON funoid = gmefunoid_responsavel
                WHERE
                    gmeoid =  ". intval($param->metaid)."
                AND
                    gmeano =  ". intval($param->ano)."
                AND
                    gimdata BETWEEN '01/01/". $param->ano."' AND '31/12/". $param->ano."'

                ) AS FOO
            ORDER BY
                mes
            ";

//echo "<pre>" . $sql;exit;
         if (!$rs = pg_query($this->conn, $sql)) {
            throw new Exception (self::MENSAGEM_ERRO_PROCESSAMENTO);
        }

        while ($tupla = pg_fetch_object($rs)) {
            $retorno[$tupla->mes][$tupla->gmicodigo] = $tupla->valor;

            if(!in_array($tupla->gmicodigo,$indicadores)){
                $indicadores[] = $tupla->gmicodigo;
                $indicadoresVerificacaoFormula[$tupla->gmicodigo] = $tupla->gmitipo_indicador;
            }
        }

        $retorno['indicadores'] = $indicadores;
        return array('formatado' => $retorno, 'verificacaoFormula' => $indicadoresVerificacaoFormula);

    }

    public function buscarIdFuncionario ($idUsuario) {

        $sql = "
            SELECT
                usufunoid
            FROM
                usuarios
            WHERE
                cd_usuario = " . $idUsuario;

        $resultado = pg_query($this->conn, $sql);

        if ($resultado && pg_num_rows($resultado) > 0) {
            return pg_fetch_result($resultado, 0, 0);
        } else {
            return 0;
        }
    }

     /**
     * Inicia uma transação com o banco
     */
    public function iniciarTransacao(){
        pg_query($this->conn, 'BEGIN;');
    }

    /**
     * Reverte as ações de banco dentro da transação
     */
    public function reverterTransacao(){
        pg_query($this->conn, 'ROLLBACK;');
    }

    /**
     * Comita as ações de banco dentro da transação
     */
    public function comitarTransacao(){
        pg_query($this->conn, 'COMMIT;');
    }

}

