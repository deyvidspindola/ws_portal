<?php

 require_once _MODULEDIR_ ."/SmartAgenda/DAO/DAO.php";

/**
 * Classe de persistencia de dados das entidades de Solicitacoes de Produto
 */
class SolicitacaoProdutoDAO extends DAO {

    private $usuarioLogado;

    const STATUS_CANCELADO = 12;
    const STATUS_PENDENTE  = 1;
    const STATUS_ATENDIDO  = 7;

    public function __construct($conn){
        $this->conn = $conn;
        $this->usuarioLogado = $this->getUsuarioLogado();

    }


    public function setSolicitarProduto($isFaltaCritica ,$osaoid = NULL, $repoid, $ordoid, $produtos, $obs = NULL){

        $sagfalta_critica = 'FALSE';
        $insereAgenda = true;
        $sqlAgendamentoCampo = '';
        $sqlAgendamentoValor = '';

        if($isFaltaCritica){
            $sagfalta_critica = 'TRUE';
            $insereAgenda = false;
        }

        if($insereAgenda){
            $sqlAgendamentoCampo = 'sagosaoid,';
            $sqlAgendamentoValor =  intval($osaoid) . ',';
        }

        $sql = " INSERT INTO
                   solicitacao_agendamento
                   (
                        sagdt_cadastro,
                        sagordoid,
                        sagusuoid,
                        sagobservacao,
                        sagsaisoid,
                        sagrepoid,
                        ". $sqlAgendamentoCampo ."
                        sagfalta_critica
                    )
                    VALUES
                    (
                        NOW(),
                        ". intval($ordoid).",
                        ". $this->usuarioLogado .",
                        '".$obs."',
                        1,
                        ".intval($repoid).",
                        ".$sqlAgendamentoValor."
                        $sagfalta_critica
                    ) RETURNING sagoid; ";

        $rs = $this->executarQuery($sql);
        $sagoid = pg_fetch_result($rs, 0, 'sagoid');

        foreach ($produtos AS $itens){

            $sql_existe = "SELECT EXISTS
                            (
                                SELECT
                                    1
                                FROM
                                    solicitacao_agendamento_item
                                WHERE
                                    saisagoid = ".$sagoid."
                                AND
                                    saiprdoid = ".$itens['prdoid']."
                            ) AS existe";

            $rs = $this->executarQuery($sql_existe);
            $tupla = pg_fetch_object($rs);

            if($tupla->existe == 't') {

                 $sql_itens = " UPDATE
                                    solicitacao_agendamento_item
                                SET
                                    saiqtde_solicitacao = (saiqtde_solicitacao + ".intval($itens['quantidade']).")
                                WHERE
                                    saisagoid = ".$sagoid."
                                AND
                                    saiprdoid = ".$itens['prdoid']."
                                ";

            } else {

                $sql_itens = " INSERT INTO
                                solicitacao_agendamento_item
                                    (
                                        saidt_cadastro,
                                        saiprdoid,
                                        saiqtde_solicitacao,
                                        saisagoid,
                                        saisaisoid
                                    )
                                   VALUES
                                   (
                                        NOW(),
                                        ".$itens['prdoid'].",
                                        ".$itens['quantidade'].",
                                        ".$sagoid.",
                                        1
                                    ); ";

            }

            $rs = $this->executarQuery($sql_itens);
        }

        return $sagoid;
    }

    public function setCancelarSolicitacao($osaoid = 0, $ordoid = 0, $justificativa){

        if($ordoid != 0){
            $where = " sagordoid = $ordoid ";
        }elseif($osaoid != 0){
            $where = " sagosaoid = $osaoid ";
        }else{
            throw new Exception('Deve ser informado osaoid  ou ordoid para cancelar a solicitação de produto.');
        }

        $sql = "UPDATE
                    solicitacao_agendamento
               SET
                    sagdt_exclusao = NOW(),
                    sagsaisoid = ". self::STATUS_CANCELADO ."
                WHERE
                    ".$where."
                AND
                    sagsaisoid = ". self::STATUS_PENDENTE ."
                AND
                    sagdt_exclusao IS NULL
                AND
                    sagfalta_critica IS FALSE
                RETURNING sagoid;";

        $rs = $this->executarQuery($sql);

        if(pg_num_rows($rs) > 0 ){

                $resultado = pg_fetch_all($rs);

                foreach ( $resultado as $sagoid ) {

                    $sql_itens = " UPDATE
                                    solicitacao_agendamento_item
                                SET
                                    saidt_exclusao = NOW(),
                                    saisaisoid = ". self::STATUS_CANCELADO .",
                                    saijustificativa_recusa  = '".$justificativa."'
                                WHERE
                                    saisagoid = ".$sagoid['sagoid']."
                                AND
                                    saidt_exclusao IS NULL";

                     $rs = $this->executarQuery($sql_itens);
                }
        }

        return true;
    }

    public function setStatusSolicitacao($sagoid = 0, $sagordoid = 0, $sagosaoid = 0, $status = ""){
        
        $where  = " TRUE ";

        if($sagoid != 0){
            $where .= " AND sagoid = $sagoid ";
        }

        if($sagordoid != 0){
            $where .= " AND sagordoid = $sagordoid ";
        }

        if($sagosaoid != 0){
            $where .= " AND sagosaoid = $sagosaoid ";
        }

        if ($status == "STATUS_ATENDIDO"){
            $statusSolicitacao = self::STATUS_ATENDIDO;
        } elseif ($status == "STATUS_PENDENTE"){
            $statusSolicitacao = self::STATUS_PENDENTE;
        } else {
            throw new Exception('Informe o status correto para ser alterada a Solicitação.');
        }

        $sql = "UPDATE
                    solicitacao_agendamento
               SET
                    sagsaisoid = ". $statusSolicitacao ."
                WHERE
                    ".$where."
                AND
                    sagdt_exclusao IS NULL
                RETURNING sagoid;";

        $rs = $this->executarQuery($sql);

        if(pg_num_rows($rs) > 0 ){

                $resultado = pg_fetch_all($rs);

                foreach ( $resultado as $sagoid ) {

                    $sql_itens = " UPDATE
                                    solicitacao_agendamento_item
                                SET
                                    saisaisoid = ". $statusSolicitacao ."
                                WHERE
                                    saisagoid = ".$sagoid['sagoid']."
                                AND
                                    saidt_exclusao IS NULL
                                AND
                                    saisaisoid NOT IN (9,12)";

                     $rs = $this->executarQuery($sql_itens);
                }
        }

        return true;
    }

}

?>