<?php

require_once _MODULEDIR_ . "Principal/Operadoras/ClaroOperadora.php";
require_once _MODULEDIR_ . "Principal/Operadoras/OiOperadora.php";
require_once _MODULEDIR_ . "Principal/Operadoras/TimOperadora.php";
require_once _MODULEDIR_ . "Principal/Operadoras/VivoOperadora.php";

abstract class Operadora {

    protected $loginOperadora;
    protected $nomeOperadora;
    protected $senhaOperadora;

    public function __get($var) {
        return $this->$var;
    }

    public static function buscarOperadora( $nomeOperadora ) {

        $nomeClasse = $nomeOperadora . 'Operadora';

        return new $nomeClasse();
    }

}