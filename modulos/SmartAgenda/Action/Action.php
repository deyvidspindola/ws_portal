<?php

/**
 * Classe pai para demais classes do tipo <Action>
 */
class Action {

    const ERRO_PROCESSAMENTO = 'Houve um erro no processamento dos dados.';

    public function getUsuarioLogado() {
        return isset($_SESSION['usuario']['oid']) ? $_SESSION['usuario']['oid'] : '2750';
    }

}