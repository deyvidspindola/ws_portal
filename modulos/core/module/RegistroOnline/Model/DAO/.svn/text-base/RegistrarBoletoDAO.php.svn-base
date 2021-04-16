<?php

namespace module\RegistroOnline;

use infra\Helper\Response;
use infra\ComumDAO;

class RegistrarBoletoDAO extends ComumDAO {

    public function __construct() {
        parent::__construct();
        $this->response = new Response();
    }

    public function getParametros($pcsipcsoid, $pcsioid) {

        $sqlString = "SELECT pcsidescricao FROM parametros_configuracoes_sistemas
                    INNER JOIN parametros_configuracoes_sistemas_itens ON pcsoid = pcsipcsoid
                    WHERE pcsipcsoid = '".$pcsipcsoid."' AND pcsioid = '".$pcsioid."'";

        $this->exec = $this->queryExec($sqlString);

        if ($this->getNumRows() == 0) {
        
            return false;
        }

        return $this->getAssoc();
    }
    

    public function getDadosRegistro($clienteID, $tituloID) {

        $query = "SELECT 'titulo'                 AS tipo,
          clioid                                  AS id_cliente,
          TO_CHAR(titdt_vencimento, 'DD-MM-YYYY')   AS data_vencimento_bd,
          titvl_titulo                            AS valor_nominal,
          TO_CHAR(titdt_inclusao, 'DDMMYYYY')     AS data_emissao,
          TO_CHAR(titdt_vencimento+1, 'DDMMYYYY') AS data_juros_mora,
          (
          CASE
            WHEN titvl_desconto IS NULL
            THEN 0
            ELSE 1
          END ) AS cod_desconto1,
          (
          CASE
            WHEN titvl_desconto IS NULL
            THEN ''
            ELSE TO_CHAR(titdt_vencimento, 'DDMMYYYY')
          END )          AS data_desconto1,
          titvl_desconto AS titvl_desconto,
          titoid         AS identificacao_titulo,
          (
          CASE
            WHEN clitipo = 'F'
            THEN 1
            WHEN clitipo = 'J'
            THEN 2
            ELSE NULL
          END ) AS tipo_inscricao,
          (
          CASE
            WHEN clitipo = 'F'
            THEN clino_cpf
            WHEN clitipo = 'J'
            THEN clino_cgc
            ELSE NULL
          END )         AS inscricao,
          clinome       AS nome,
          enduf         AS uf,
          endcep        AS cep,
          endbairro     AS bairro,
          endcidade     AS cidade,
          endlogradouro AS endereco,

          ( SELECT teccnpj FROM tectran WHERE tecurl_sistema = 'PUBLIC'
          ) AS inscricao_avalista,
          ( SELECT tecrazao FROM tectran WHERE tecurl_sistema = 'PUBLIC'
          ) AS nome_avalista
        FROM titulo
        INNER JOIN clientes
        ON titclioid=clioid
        INNER JOIN endereco
        ON cliend_cobr=endoid
        WHERE titoid = $tituloID AND clioid = $clienteID 
       
 UNION ALL
     
        SELECT 'titulo_retencao'                  AS tipo,
          clioid                                  AS id_cliente,
          TO_CHAR(titdt_vencimento, 'DD-MM-YYYY')   AS data_vencimento_bd,
          titvl_titulo_retencao                   AS valor_nominal,
          TO_CHAR(titdt_inclusao, 'DDMMYYYY')     AS data_emissao,
          TO_CHAR(titdt_vencimento+1, 'DDMMYYYY') AS data_juros_mora,
          (
          CASE
            WHEN titvl_desconto IS NULL
            THEN 0
            ELSE 1
          END ) AS cod_desconto1,
          (
          CASE
            WHEN titvl_desconto IS NULL
            THEN ''
            ELSE TO_CHAR(titdt_vencimento, 'DDMMYYYY')
          END )          AS data_desconto1,
          titvl_desconto AS titvl_desconto,
          titoid         AS identificacao_titulo,
          (
          CASE
            WHEN clitipo = 'F'
            THEN 1
            WHEN clitipo = 'J'
            THEN 2
            ELSE NULL
          END ) AS tipo_inscricao,
          (
          CASE
            WHEN clitipo = 'F'
            THEN clino_cpf
            WHEN clitipo = 'J'
            THEN clino_cgc
            ELSE NULL
          END )         AS inscricao,
          clinome       AS nome,
          enduf         AS uf,
          endcep        AS cep,
          endbairro     AS bairro,
          endcidade     AS cidade,
          endlogradouro AS endereco,

          ( SELECT teccnpj FROM tectran WHERE tecurl_sistema = 'PUBLIC'
          ) AS inscricao_avalista,
          ( SELECT tecrazao FROM tectran WHERE tecurl_sistema = 'PUBLIC'
          ) AS nome_avalista
        FROM titulo_retencao
        INNER JOIN clientes
        ON titclioid=clioid
        INNER JOIN endereco
        ON cliend_cobr=endoid
        WHERE titoid = $tituloID and clioid = $clienteID 
        
UNION ALL
    
        SELECT 'titulo_consolidado'                AS tipo,
          clioid                                   AS id_cliente,
          TO_CHAR(titcdt_vencimento, 'DD-MM-YYYY')   AS data_vencimento_bd,
          titcvl_titulo                            AS valor_nominal,
          TO_CHAR(titcdt_inclusao, 'DDMMYYYY')     AS data_emissao,
          TO_CHAR(titcdt_vencimento+1, 'DDMMYYYY') AS data_juros_mora,
          (
          CASE
            WHEN titcvl_desconto IS NULL
            THEN 0
            ELSE 1
          END ) AS cod_desconto1,
          (
          CASE
            WHEN titcvl_desconto IS NULL
            THEN ''
            ELSE TO_CHAR(titcdt_vencimento, 'DDMMYYYY')
          END )           AS data_desconto1,
          titcvl_desconto AS titcvl_desconto,
          titcoid         AS identificacao_titulo,
          (
          CASE
            WHEN clitipo = 'F'
            THEN 1
            WHEN clitipo = 'J'
            THEN 2
            ELSE NULL
          END ) AS tipo_inscricao,
          (
          CASE
            WHEN clitipo = 'F'
            THEN clino_cpf
            WHEN clitipo = 'J'
            THEN clino_cgc
            ELSE NULL
          END )         AS inscricao,
          clinome       AS nome,
          enduf         AS uf,
          endcep        AS cep,
          endbairro     AS bairro,
          endcidade     AS cidade,
          endlogradouro AS endereco,

          ( SELECT teccnpj FROM tectran WHERE tecurl_sistema = 'PUBLIC'
          ) AS inscricao_avalista,
          ( SELECT tecrazao FROM tectran WHERE tecurl_sistema = 'PUBLIC'
          ) AS nome_avalista
        FROM titulo_consolidado
        INNER JOIN clientes
        ON titcclioid=clioid
        INNER JOIN endereco
        ON cliend_cobr=endoid
        WHERE titcoid = $tituloID AND titcclioid = $clienteID ";

        try {
            $exec = $this->queryExec($query);

            if ($this->getNumRows() == 0) {
                return 0;
            }

            return  $this->getAssoc();
        } catch (Exception $e) {
            return 0;
        }
    }

    public function registraTitulo($cResponse, $params, $xml) {

      $retCode = $cResponse->TicketResponse->retCode;
      $ticket = $cResponse->TicketResponse->ticket;

      $desc = "SELECT tpetoid
              FROM tipo_evento_titulo
              WHERE tpetcodigo = $retCode
              AND tpettipo_evento = 'Registro_OnLine_Ticket'
              AND tpetcfbbanco = 33
              AND tpetcob_registrada IS TRUE";

      $this->queryExec($desc);      

      if ($this->getNumRows() == 0) {
          return 0; 
      }

      $tpetoid = $this->getAssoc();
      $tpetoid = $tpetoid['tpetoid'];

      if (isset($_SESSION['usuario']['oid'])) {
      	$cd_usuario = $_SESSION['usuario']['oid'];
      } else {
      	$cd_usuario = 2750;
      }
      
      $insertHist = "INSERT INTO titulo_historico_online 
      (thotitoid, thousuoid, thodt_cadastro, thoticket_banco, thocod_retorno, thonsu, thodt_nsu) VALUES (".$params['tituloId'].", ".$cd_usuario.", '".date('Y-m-d H:i:s')."', '".$ticket."', '".$tpetoid."', '', '')";

      $insertEvento = "INSERT INTO evento_titulo (evtititoid, evtitpetoid, evtidt_geracao, evticod_retorno_cobr_reg) VALUES (".$params['tituloId'].", ".$tpetoid.", '".date('Y-m-d H:i:s')."' , $retCode)";

      try {

        $this->queryExec($insertHist);
        $this->queryExec($insertEvento); 
        
      } catch (Exception $e) {
        return -1;
      }

      return true;

    }

    public function updateTitulo($rResponse = '', $dados = '') {

      $nsu = $rResponse->return->nsu; //resposta da cobrança
      $dt_nsu = $rResponse->return->dtNsu; //resposta da cobrança
      $cod_titulo = $dados['identificacao_titulo'];

      $query = "UPDATE titulo_historico_online
          SET thonsu = '".$nsu."',thodt_nsu = '".$dt_nsu."' 
          WHERE thotitoid = ".$cod_titulo."";

      return $this->queryExec($query);

    }

    public function consultarTitulo($rResponse) {
      $nsu = $rResponse->return->nsu;
      $query = "SELECT thonsu FROM titulo_historico_online WHERE thonsu = '".$nsu."'";

      $this->queryExec($query);
      return $this->getNumRows() > 0;
    }

    public function getErroCode($retCode) {
        $query = "SELECT tpetdescricao
            FROM tipo_evento_titulo
            WHERE tpetcodigo = $retCode
            AND tpettipo_evento = 'Registro_OnLine_Ticket'
            AND tpetcob_registrada IS TRUE
            AND tpetcfbbanco = 33";

        $this->queryExec($query);

        $retDesc = $this->getAssoc();

        return $retDesc['tpetdescricao'];
    }

    

    /**
     * STI 86970_1 - verificacao e validacao para chamada do update nosso numero
     *
     * @author  marcelo.brondani marcelo.brondani@meta.com.br
     * @since 21/08/2017
     * @version 21/08/2017
     * @param  array $dados dados de configuracao e nosso numero
     * @return booleano retorna o resultado se a atualizacao ocorreu com sucesso
     */
     public function updateNossoNumero($dados){
        $params = $dados[0];
        $tabelas = $dados[1];        

        foreach ($tabelas as $chave => $value) {
            if ($value['tabela'] == $params['tabela']) {
                return $result = $this->setUpdateNossoNumero($tabelas[$chave], $params);
            }
        }

        return true;
    }

    /**
    * STI 86970_1 - Realiza o update do nosso numero
    *
    * @author  marcelo.brondani marcelo.brondani@meta.com.br
    * @since 21/08/2017
    * @version 21/08/2017
    * @param array $tabela dados de configuracao para saber qual tabela referencia o nosso numero
    * @param array $params dados do nosso numero.
    * @return booleano retorna o resultado se a execucao da atualizacao ocorreu com sucesso
    */
    public function setUpdateNossoNumero($tabela, $params) {
        $query = "UPDATE $tabela[tabela] 
                  SET $tabela[colNossoNum] = $params[numero] 
                  where $tabela[colIdTitulo] = $params[id_titulo]";
        $this->queryExec($query);

        $query = "SELECT * 
                    FROM $tabela[tabela] 
                   WHERE $tabela[colNossoNum] = $params[numero]
                     AND $tabela[colIdTitulo] = $params[id_titulo]";

        $this->queryExec($query);
        return $this->getNumRows() > 0;
    }

    /**
    * STI 86970_2 - verificacao e validacao para chamada do update na forma de cobranca do titulo
    *
    * @author  marcelo.brondani marcelo.brondani@meta.com.br
    * @since 22/08/2017
    * @version 22/08/2017
    * @param  array $dados dados de configuracao e titulo a ser atualizado
    * @return booleano retorna o resultado se a atualizacao ocorreu com sucesso
    */
    public function updateAlterFormCobTitoReg($dados) {
        $params = $dados[0];
        $tabelas = $dados[1];
        $formCobrancaTitulo = $dados[2];

        foreach ($tabelas as $chave => $value) {
            if ($value['tabela'] == $params['tabela']) {
                return $result = $this->setAlterFormCobrancaTitulo($tabelas[$chave], $params, $formCobrancaTitulo);
            }
        }

        return true;
    }

    /**
    * STI 86970_2 - Realiza o (alteracao)update na forma de cobranca do titulo
    *
    * @author  marcelo.brondani marcelo.brondani@meta.com.br
    * @since 22/08/2017
    * @version 22/08/2017
    * @param array $tabela dados de configuracao para saber qual tabela referencia o titulo
    * @param array $params dados do titulo a ser atualizado.
    * @return booleano retorna o resultado se a execucao da atualizacao ocorreu com sucesso
    */
    public function setAlterFormCobrancaTitulo($tabela, $params, $cod) {
    	
        $query = "UPDATE $tabela[tabela]
                     SET $tabela[colFormCob] = $cod[codFormCobtit]
                   WHERE $tabela[colIdTitulo] = $params[id_titulo]  ";
        
        $this->queryExec($query);

        $query = "SELECT * FROM $tabela[tabela] WHERE $tabela[colFormCob] = $cod[codFormCobtit] AND $tabela[colIdTitulo] = $params[id_titulo]";
        $this->queryExec($query);
        
        return $this->getNumRows() > 0;
    }

    /**
     * STI 86970 1.1 - Executa e para retorna o id
     *
     * @author Marcelo.brondani.ext
     * @version 27/08/2017
     * @return int $retDesc
    */
    public function getId_tpetoid() {
        $query = "SELECT tpetoid
                    FROM tipo_evento_titulo
                    WHERE tpetcodigo = 02
                    AND tpettipo_evento = 'Retorno'
                    AND tpetcfbbanco = 33
                    AND tpetcob_registrada IS true;";

        $this->queryExec($query);

        $retDesc = $this->getAssoc();
        return $retDesc['tpetoid'];
    }

    /**
     * STI 86970 1.1 - Atualiza o status em titulo
     *
     * @author Marcelo.brondani.ext
     * @version 27/08/2017
     * @return booleano resultado do update
    */
    public function updateStatusInTitulo($tituloId, $tpetoid) {
        $query = "UPDATE titulo 
                    SET tittpetoid = $tpetoid
                    WHERE titoid   = $tituloId;";
                    
        return $this->queryExec($query);
    }

    /**
     * STI 86970 1.1 - Atualiza o status em titulo_retencao
     *
     * @author Marcelo.brondani.ext
     * @version 27/08/2017
     * @return booleano resultado do update
    */
    public function updateStatusInTituloRetencao($tituloId, $tpetoid) {
        $query = "UPDATE titulo_retencao 
                    SET tittpetoid = $tpetoid
                    WHERE titoid IN ($tituloId);";

        return $this->queryExec($query);
    }

    /**
     * STI 86970 1.1 - Atualiza o status em titulo_consolidado
     *
     * @author Marcelo.brondani.ext
     * @version 27/08/2017
     * @return booleano resultado do update
    */
    public function updateStatusInTituloConsolidado($tituloId, $tpetoid) {
        $query = "UPDATE titulo_consolidado 
                    SET titctpetoid = $tpetoid 
                    WHERE titcoid IN ($tituloId)";

        return $this->queryExec($query);
    }

 
}
?>