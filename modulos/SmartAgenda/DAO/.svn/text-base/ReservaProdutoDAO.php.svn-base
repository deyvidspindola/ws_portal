<?php

 require_once _MODULEDIR_ ."/SmartAgenda/DAO/DAO.php";

/**
 * Classe de persistencia de dados das entidades de Reserva de Produto
 */
class ReservaProdutoDAO extends DAO {

    private $usuarioLogado;

    const STATUS_PRE_RESERVA = 1;
    const STATUS_CANCELADO   = 2;
    const STATUS_RESERVADO   = 3;
    const STATUS_INSTALADO   = 4;

    public function __construct($conn){
        $this->conn = $conn;
        $this->usuarioLogado = $this->getUsuarioLogado();

    }

    public function setReservarProduto($listaDados, $produtos){

        $ragosaoid = ($listaDados->codigoAgendamento != "" ? $listaDados->codigoAgendamento  : 'NULL');
        $ragsagoid = ($listaDados->codigoSolicitacao != "" ? $listaDados->codigoSolicitacao  : 'NULL');        

        $cod_status = self::STATUS_RESERVADO;

        $sql = " INSERT INTO
                  reserva_agendamento
                  (
                        ragdt_cadastro,
                        ragordoid,
                        ragrasoid,
                        ragrepoid,
                        ragusuoid,
                        ragosaoid,
                        ragsagoid
                   )
                  VALUES (
                        NOW(),
                        ". $listaDados->numeroOrdemServico .",
                        ". $cod_status . ",
                        ". $listaDados->codigoPrestador .",
                        ". $this->usuarioLogado .",
                        ". $ragosaoid .",
                        ". $ragsagoid ."
                   ) RETURNING ragoid;";

        $rs     = $this->executarQuery($sql);
        $ragoid = pg_fetch_result($rs, 0, 'ragoid');

        foreach ($produtos AS $itens){

            $raiqtde_transito = 0;
            $raiqtde_estoque  = 0;
            $sqlRemessaCampo  = '';
            $sqlRemessaValor  = '';

            if( isset($itens['esroid']) ){
                $sqlRemessaCampo =  'raiesroid,';
                $sqlRemessaValor =  $itens['esroid'].',';
            }

            if($itens['tipo'] == 'representante' || $itens['tipo'] == 'estoque_cd'){
                $raiqtde_estoque = $itens['quantidade'];
            }elseif($itens['tipo'] == 'transito'){
                $raiqtde_transito = $itens['quantidade'];
            }

            $sql_itens = " INSERT INTO
                            reserva_agendamento_item
                            (
                                raidt_cadastro,
                                raiprdoid,
                                raiqtde_estoque,
                                raiqtde_transito,
                                ".$sqlRemessaCampo."
                                rairagoid
                            ) VALUES (
                                NOW(),
                                ".$itens['prdoid'].",
                                ". intval($raiqtde_estoque).",
                                ". intval($raiqtde_transito).",
                                ". $sqlRemessaValor."
                                ". intval($ragoid)."
                            );";

            $rs = $this->executarQuery($sql_itens);
        }

        return true;
    }


    public function setCancelarReserva( $listaDados, $justificativa){

        $sql = "UPDATE
                    reserva_agendamento
                SET
                    ragrasoid = ". self::STATUS_CANCELADO .",
                    ragjustificativa_cancelamento = '" . $justificativa . "',
                    ragdt_cancelamento = NOW()
                WHERE TRUE ";

        $sql .= !empty($listaDados->codigoAgendamento)  ? ' AND ragosaoid = ' . $listaDados->codigoAgendamento  : '';
        $sql .= !empty($listaDados->numeroOrdemServico) ? ' AND ragordoid = ' . $listaDados->numeroOrdemServico : '';
        $sql .= !empty($listaDados->codigoPrestador)    ? ' AND ragrepoid = ' . $listaDados->codigoPrestador    : '';

        $sql .=" AND
                    ragrasoid IN (". self::STATUS_RESERVADO .")
                AND
                    ragdt_cancelamento IS NULL
                RETURNING
                    ragoid;";

        $rs = $this->executarQuery($sql);

        if(pg_num_rows($rs) > 0 ){

            $result = pg_fetch_all($rs);

            foreach ($result as $ragoid){

                $sql_itens = "
                            UPDATE
                                reserva_agendamento_item
                             SET
                                raidt_exclusao = NOW()
                            WHERE
                                rairagoid = ".$ragoid['ragoid']."
                            AND
                                raidt_exclusao IS NULL";

                 $rs = $this->executarQuery($sql_itens);
            }

        }

        return true;
    }

     public function setStatusProdutoInstalado( $listaDados ){

        $sql = " UPDATE
                    reserva_agendamento
                SET
                    ragrasoid = ". self::STATUS_INSTALADO ."
                WHERE TRUE ";

        $sql .= !empty($listaDados->codigoAgendamento)  ? ' AND ragosaoid =' . $listaDados->codigoAgendamento  : '';
        $sql .= !empty($listaDados->numeroOrdemServico) ? ' AND ragordoid =' . $listaDados->numeroOrdemServico : '';

        $sql .=  " AND
                    ragrasoid = ". self::STATUS_RESERVADO . "
                AND
                    ragdt_cancelamento IS NULL;";

        $rs = $this->executarQuery($sql);

        return true;
    }

    public function setReservarProdutoNoCD($numeroOrdemServico, $codigoAgendamento = null, $codigoPrestador, $codigoSolicitacao = null, $prdoid, $quantidade){

        $ragosaoid = $codigoAgendamento;
        $ragsagoid = $codigoSolicitacao;

        $cod_status = self::STATUS_RESERVADO;

        $sql = " INSERT INTO
                  reserva_agendamento
                  (
                        ragdt_cadastro,
                        ragordoid,
                        ragrasoid,
                        ragrepoid,
                        ragusuoid,
                        ragosaoid,
                        ragsagoid
                   )
                  VALUES (
                        NOW(),
                        ". $numeroOrdemServico .",
                        ". $cod_status . ",
                        ". $codigoPrestador .",
                        ". $this->usuarioLogado .",
                        ". $ragosaoid .",
                        ". $ragsagoid ."
                   ) RETURNING ragoid;";

        $rs     = $this->executarQuery($sql);
        $ragoid = pg_fetch_result($rs, 0, 'ragoid');

        $raiqtde_transito = 0;
        $raiqtde_estoque  = $quantidade;

        $sql_itens = " INSERT INTO
                        reserva_agendamento_item
                        (
                            raidt_cadastro,
                            raiprdoid,
                            raiqtde_estoque,
                            raiqtde_transito,
                            rairagoid
                        ) VALUES (
                            NOW(),
                            ".$prdoid.",
                            ". intval($raiqtde_estoque).",
                            ". intval($raiqtde_transito).",
                            ". intval($ragoid)."
                        );";

        $rs = $this->executarQuery($sql_itens);

        return true;
    }

}

?>