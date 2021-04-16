<?php

namespace module\BoletoRegistrado;

use module\Boleto\Santander;

class BoletoRegistradoSantander extends Santander {

    public $nossoNumero;
    public $linhaDigitavel;

    public function setNossoNumero($nossoNumero)
    {
        $this->nossoNumero = $nossoNumero;
    }

    public function getNossoNumero()
    {
        return $this->nossoNumero;
    }
}