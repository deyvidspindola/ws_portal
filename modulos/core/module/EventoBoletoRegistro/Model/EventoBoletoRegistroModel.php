<?php

namespace module\EventoBoletoRegistro;

use module\EventoBoletoRegistro\EventoBoletoRegistroDAO;

class EventoBoletoRegistroModel{

    const EVENTO_BOLETO_ADICIONADO_FILA_CANCELAMENTO = 1;
    const EVENTO_BOLETO_PROCESSADO_FILA_CANCELAMENTO = 2;

    private $dao;

    private $id;
    private $boletoId;
    private $tipoEventoBoletoId;
    private $codigoMovimento;
    private $dataGeracao;
    private $codigoCnab;
    private $dataCnab;

    public function __construct($id = null){
        $this->dao = new EventoBoletoRegistroDAO();
        if(!empty($id)){
            $eventoBoleto = self::getById($id);
            if(!$eventoBoleto){
                throw new \Exception("Evento de Boleto não encontrado");
            }
            return $eventoBoleto;
        }
    }

    public function getId(){
		return $this->id;
	}

    public function getBoletoId(){
        return $this->boletoId;
    }

    public function getTipoEventoBoletoId(){
        return $this->tipoEventoBoletoId;
    }

    public function getCodigoMovimento(){
        return $this->codigoMovimento;
    }

    public function getDataGeracao(){
        return $this->dataGeracao;
    }

    public function getCodigoCnab(){
        return $this->codigoCnab;
    }

    public function getDataCnab(){
        return $this->dataCnab;
    }

    public function setId($id){
        $this->id = $id;
    }

    public function setBoletoId($boletoId){
        $this->boletoId = $boletoId;
    }

    public function setTipoEventoBoletoId($tipoEventoBoletoId){
        $this->tipoEventoBoletoId = $tipoEventoBoletoId;
    }

    public function setCodigoMovimento($codigoMovimento){
        $this->codigoMovimento = $codigoMovimento;
    }

    public function setDataGeracao($dataGeracao){
        $this->dataGeracao = $dataGeracao;
    }

    public function setCodigoCnab($codigoCnab){
        $this->codigoCnab = $codigoCnab;
    }

    public function setDataCnab($dataCnab){
        $this->dataCnab = $dataCnab;
    }

    public static function getById($id){

        $resultadoEventoBoleto = $this->dao->getById($id);

        if(!$resultadoEventoBoleto){

            $eventoBoleto = new EventoBoletoRegistroModel();

            $eventoBoleto->setId($id);
            $eventoBoleto->setBoletoId($boletoId);
            $eventoBoleto->setCodigoMovimento($codigoMovimento);
            $eventoBoleto->setDataGeracao($dataGeracao);
            $eventoBoleto->setCodigoCnab($codigoCnab);
            $eventoBoleto->setDataCnab($dataCnab);

            return $eventoBoleto;
        }
        return null;
    }


    public function inserir(){
		
        return $this->dao->inserir(
            $this->boletoId,
            $this->tipoEventoBoletoId,
            $this->codigoMovimento,
            $this->codigoCnab,
            $this->dataCnab
        );

    }
    
    public function atualizar(){
        
        return $this->dao->atualizar(
            $this->id,
            $this->boletoId,
            $this->codigoMovimento,
            $this->dataGeracao,
            $this->codigoCnab,
            $this->dataCnab
        );

    }

    public function getTipoEventoByCodigoRetornoXML($codigo){

        $codigo = str_pad($codigo, 5, "0", STR_PAD_LEFT);

        $dao = new EventoBoletoRegistroDAO();
        return $dao->getTipoEventoByCodigoRetornoXML($codigo);

    }

}

?>