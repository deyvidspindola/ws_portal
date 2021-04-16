<?php

/**
 * Classe responsável pela atualização do ststaus das ações Gestão Meta
 *
 * @author 	André L. Zilz <andre.zilz@meta.com.br>
 * @package Cron
 * @since 11/02/2014
 */
class GestaoMetaAtualizaAcao {

    /**
     * Referencia da DAO
     */
    private $dao;

    /*
     * Mensagens do processo
     */
    public $msg;

    /**
     * Construtor da Classe
     */
    public function __construct(GestaoMetaAtualizaAcaoDAO $dao) {

        $this->dao = $dao;
    }


    /**
     * Executa a atualização dos staus das ações gestão meta
     */
    public function iniciarProcesso() {

        try {

            $this->dao->abrirTransacao();

            $resultado = $this->dao->atualizarAcaoMeta();

            $this->msg = "Foram atualizadas " . $resultado . " ações";

            $this->dao->encerrarTransacao();

        } catch (Exception $exc) {

            $this->dao->abortarTransacao();
            $this->msg = $exc->getMessage();
        }




    }

}