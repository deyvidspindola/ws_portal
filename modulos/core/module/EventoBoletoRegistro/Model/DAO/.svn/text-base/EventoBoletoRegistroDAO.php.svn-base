<?php

namespace module\EventoBoletoRegistro;
use infra\ComumDAO;

class EventoBoletoRegistroDAO extends ComumDAO {

    public function __construct(){
		parent::__construct();
    }
    
    public function getById($id){
        
        $sql = "SELECT * FROM evento_boleto_registro WHERE evbroid = $id LIMIT 1;";
        $this->queryExec($sql);
        return $this->getNumRows() > 0 ? $this->getAssoc() : null;

    }

    public function inserir(
        $boletoId,
        $tipoEventoBoletoId,
        $codigoMovimento,
        $codigoCnab,
        $dataCnab
    ){

        $tipoEventoBoletoId = !is_null($tipoEventoBoletoId) ? $tipoEventoBoletoId: "null";
        $codigoMovimento = !is_null($codigoMovimento) ? $codigoMovimento: "null";
        $codigoCnab = !is_null($codigoCnab) ? $codigoCnab : "null";
        $dataCnab = !is_null($dataCnab) ? "'$dataCnab'" : "null";


        $sql = "INSERT INTO evento_boleto_registro (
            evbrtbreoid,
            evbrtpeboid,
            evbrtpetoid,
            evbrcod_cnab,
            evbrdt_cnab
        ) VALUES (
            $boletoId,
            $tipoEventoBoletoId,
            $codigoMovimento,
            $codigoCnab,
            $dataCnab
        );";

        $this->queryExec($sql);

        return $this->getNumRows() > 0 ? $this->getAssoc() : false;

    }

    public function atualizar(
        $id,
        $boletoId,
        $codigoMovimento,
        $dataGeracao,
        $codigoCnab,
        $dataCnab
    ){

        $sql = "UPDATE evento_boleto_registro SET 
        evbrtbreoid = $boletoId,
        evbrtpetoid = $codigoMovimento,
        evbrdt_geracao = $dataGeracao,
        evbrcod_cnab = $codigoCnab,
        evbrdt_cnab = '$dataCnab'
        WHERE evbroid = $id";
        $this->queryExec($sql);

        if($this->getNumRows() > 0){
            return $this->getAssoc();
        } else{
            return array();
        }
        
    }

    public function getTipoEventoByCodigoRetornoXML($codigo){

        $sql = "
            SELECT
                tpetoid
            FROM
                tipo_evento_titulo
            WHERE
                tpettipo_evento = '$codigo' OR tpetcodigo_online = '$codigo'
            LIMIT 1
        ";

        $this->queryExec($sql);

        return $this->getNumRows() > 0 ? $this->getObject(0)->tpetoid : null;

    }

}

?>