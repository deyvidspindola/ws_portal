<?php

namespace module\RoteadorBoleto;

class RoteadorBoletoModel {
    private $dao;

    const TITULO = 'titulo';
    const TITULO_RETENCAO = 'retencao';
    const TITULO_CONSOLIDADO = 'consolidado';

    public function __construct() {
        $this->dao = new RoteadorBoletoDAO;
    }

    public function getTitulo($idTitulo, $tipoTitulo)
    {
        switch ($tipoTitulo) {
            case self::TITULO: return $this->dao->getTitulo($idTitulo);
            case self::TITULO_RETENCAO: return $this->dao->getTituloRetencao($idTitulo);
            case self::TITULO_CONSOLIDADO: return $this->dao->getTituloConsolidado($idTitulo);
        }

        return array();
    }

    public function getTituloBoleto($idTitulo, $tipoTitulo)
    {
        switch ($tipoTitulo) {
            case self::TITULO: return $this->dao->getTituloBoleto($idTitulo);
            case self::TITULO_RETENCAO: return $this->dao->getTituloBoletoRetencao($idTitulo);
            case self::TITULO_CONSOLIDADO: return $this->dao->getTituloBoletoConsolidado($idTitulo);
        }

        return array();
    }

    public function isTituloAtivo($idTitulo, $tipoTitulo)
    {
        switch ($tipoTitulo) {
            case self::TITULO: return $this->dao->isTituloAtivo($idTitulo);
            case self::TITULO_RETENCAO: return $this->dao->isTituloRetencaoAtivo($idTitulo);
            case self::TITULO_CONSOLIDADO: return $this->dao->isTituloConsolidadoAtivo($idTitulo);
        }

        return false;
    }

    public function alteraStatus($idTitulo, $tipoTitulo, $novoStatus)
    {
        switch ($tipoTitulo) {
            case self::TITULO: return $this->dao->alteraStatus($idTitulo, $novoStatus);
            case self::TITULO_RETENCAO: return $this->dao->alteraStatusRetencao($idTitulo, $novoStatus);
            case self::TITULO_CONSOLIDADO: return $this->dao->alteraStatusConsolidado($idTitulo, $novoStatus);
        }

        return false;
    }

    public function getCodigoCancelamentoCnab()
    {
        return $this->dao->getCodigoCancelamentoCnab();
    }
    
    public function cancelarTituloCnab($idTitulo, $tipoTitulo, $codigo)
    {
        switch ($tipoTitulo) {
            case self::TITULO: return $this->dao->cancelarTituloCnab($idTitulo, $codigo);
            case self::TITULO_RETENCAO: return $this->dao->cancelarTituloRetencaoCnab($idTitulo, $codigo);
            case self::TITULO_CONSOLIDADO: return $this->dao->cancelarTituloConsolidadoCnab($idTitulo, $codigo);
        }

        return false;
    }
    
    public function cancelarTituloERP($idTitulo, $tipoTitulo)
    {
        switch ($tipoTitulo) {
            case self::TITULO: return $this->dao->cancelarTituloERP($idTitulo);
            case self::TITULO_RETENCAO: return $this->dao->cancelarTituloRetencaoERP($idTitulo);
            case self::TITULO_CONSOLIDADO: return $this->dao->cancelarTituloConsolidadoERP($idTitulo);
        }

        return false;
    }

    public function getCodigoExpiradoCnab()
    {
        return array_map(function ($codigo) {
            return $codigo->tpetoid;
        }, $this->dao->getCodigoExpiradoCnab());
    }

    public function isTituloExpirado($idTitulo, $tipoTitulo, $codigo)
    {
        switch ($tipoTitulo) {
            case self::TITULO: return $this->dao->isTituloExpirado($idTitulo, $codigo);
            case self::TITULO_RETENCAO: return $this->dao->isTituloRetencaoExpirado($idTitulo, $codigo);
            case self::TITULO_CONSOLIDADO: return $this->dao->isTituloConsolidadoExpirado($idTitulo, $codigo);
        }

        return false;
    }

    public function updateDataVencimento($idTitulo, $tipoTitulo, $dataVencimento)
    {
        switch ($tipoTitulo) {
            // case self::TITULO: return $this->dao->updateDataVencimentoTitulo($idTitulo, $dataVencimento);
            case self::TITULO_RETENCAO: return $this->dao->updateDataVencimentoTituloRetencao($idTitulo, $dataVencimento);
            // case self::TITULO_CONSOLIDADO: return $this->dao->updateDataVencimentoTituloConsolidado($idTitulo, $dataVencimento);
        }
    }
    
    public function isTituloRegistrado($idTitulo, $tipoTitulo)
    {
        switch ($tipoTitulo) {
            case self::TITULO: return $this->dao->isTituloRegistrado($idTitulo);
            case self::TITULO_RETENCAO: return $this->dao->isTituloRegistradoRetencao($idTitulo);
            case self::TITULO_CONSOLIDADO: return $this->dao->isTituloRegistradoConsolidado($idTitulo);
        }

        return false;
    }

    public function getCodigoAtivo()
    {
        return $this->dao->getCodigoAtivo();
    }

    public function getTipoTitulo($idTitulo)
    {
        if (count($this->dao->getTitulo($idTitulo)) > 0) {
            return self::TITULO;
        }

        if (count($this->dao->getTituloRetencao($idTitulo)) > 0) {
            return self::TITULO_RETENCAO;
        }

        if (count($this->dao->getTituloConsolidado($idTitulo)) > 0) {
            return self::TITULO_CONSOLIDADO;
        }

        return false;
    }

    public function getPcsiDescricao()
    {
        return $this->dao->getPcsiDescricao();
    }

    public function getCodigoBoletoRegistrado($pcsidescricao)
    {
        return $this->dao->getCodigoBoletoRegistrado($pcsidescricao);
    }
}
