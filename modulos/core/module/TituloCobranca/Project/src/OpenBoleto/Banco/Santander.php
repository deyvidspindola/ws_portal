<?php

/*
 * OpenBoleto - Geração de boletos bancários em PHP
 *
 * LICENSE: The MIT License (MIT)
 *
 * Copyright (C) 2013 Estrada Virtual
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy of this
 * software and associated documentation files (the "Software"), to deal in the Software
 * without restriction, including without limitation the rights to use, copy, modify,
 * merge, publish, distribute, sublicense, and/or sell copies of the Software, and to
 * permit persons to whom the Software is furnished to do so, subject to the following
 * conditions:
 *
 * The above copyright notice and this permission notice shall be included in all copies
 * or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED,
 * INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A
 * PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT
 * HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION
 * OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE
 * SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
 */

namespace module\Boleto;

use module\Boleto\BoletoAbstract;
use module\Boleto\Exception;

/**
 * Classe boleto Santander
 *
 * @package    OpenBoleto
 * @author     Daniel Garajau <http://github.com/kriansa>
 * @copyright  Copyright (c) 2013 Estrada Virtual (http://www.estradavirtual.com.br)
 * @license    MIT License
 * @version    1.0
 */
class Santander extends BoletoAbstract
{
    /**
     * Código do banco
     * @var string
     */
    protected $codigoBanco = '033';

    /**
     * Localização do logotipo do banco, referente ao diretório de imagens
     * @var string
     */
    protected $logoBanco = 'santander.jpg';

    /**
     * Linha de local de pagamento
     * @var string
     */
    protected $localPagamento = 'Pagar preferencialmente no Banco Santander';

    /**
     * Define as carteiras disponíveis para este banco
     * @var array
     */
    protected $carteiras = array('101', '102', '104', '201');

    /**
     * Define os nomes das carteiras para exibição no boleto
     * @var array
     */
    protected $carteirasNomes = array('101' => 'Cobran&ccedil;a Simples RCR', '102' => 'Cobran&ccedil;a Simples CSR', '104' => 'Cobran&ccedil;a Simples ECR');

    /**
     * Define o valor do IOS - Seguradoras (Se 7% informar 7. Limitado a 9%) - Demais clientes usar 0 (zero)
     * @var int
     */
    protected $ios;

    /**
     * Define o valor do IOS
     *
     * @param int $ios
     */
    public function setIos($ios)
    {
        $this->ios = $ios;
    }

    /**
     * Retorna o atual valor do IOS
     *
     * @return int
     */
    public function getIos()
    {
        return $this->ios;
    }

    /**
     * Gera o Nosso Número.
     *
     * @return string
     */
    protected function gerarNossoNumero()
    {
        return self::zeroFill($this->getSequencial(), 12).$this->getDigitoVerificador();
    }

    /**
     * Método para gerar o código da posição de 20 a 44
     *
     * @return string
     * @throws \OpenBoleto\Exception
     */
    public function getCampoLivre()
    {
        return '9' . self::zeroFill($this->getConta(), 7) .
            $this->getNossoNumero() .
            self::zeroFill($this->getIos(), 1) .
            self::zeroFill($this->getCarteira(), 3);
    }
    
    /**
     * Retorna o dígito verificador do Nosso Número
     * @override
     * @return int
     */
    protected function getDigitoVerificador()
    {
        $num = self::zeroFill($this->getSequencial(), 12);
        
        $modulo = static::modulo11($num);
        if ($modulo['resto'] == 0 || $modulo['resto'] == 1 || $modulo['resto'] == 10) {
            $dv = 1;
        } else {
            $dv = 11 - $modulo['resto'];
        }

        return $dv;
    }
    
    /**
     * Retorna o dígito verificador do Nosso Número
     * @param int $num
     * @return int
     */
    protected function getDigitoVerificadorCodigoBarras($num)
    {        
        $modulo = static::modulo11($num);
        if ($modulo['resto'] == 0 || $modulo['resto'] == 1 || $modulo['resto'] == 10) {
            $dv = 1;
        } else {
            $dv = 11 - $modulo['resto'];
        }

        return $dv;
    }
    
    /**
     * Retorna a linha digitável do boleto
     * @override
     * @return string
     */
    public function getLinhaDigitavel()
    {
        $chave = $this->getCampoLivre();

        // Break down febraban positions 20 to 44 into 3 blocks of 5, 10 and 10
        // characters each.
        $blocks = array(
            '20-24' => substr($chave, 0, 5),
            '25-34' => substr($chave, 5, 10),
            '35-44' => substr($chave, 15, 10),
        );

        // Concatenates bankCode + currencyCode + first block of 5 characters and
        // calculates its check digit for part1.
        $check_digit = static::modulo10($this->getCodigoBanco() . $this->getMoeda() . $blocks['20-24']);

        // Shift in a dot on block 20-24 (5 characters) at its 2nd position.
        $blocks['20-24'] = substr_replace($blocks['20-24'], '.', 1, 0);

        // Concatenates bankCode + currencyCode + first block of 5 characters +
        // checkDigit.
        $part1 = $this->getCodigoBanco(). $this->getMoeda() . $blocks['20-24'] . $check_digit;

        // Calculates part2 check digit from 2nd block of 10 characters.
        $check_digit = static::modulo10($blocks['25-34']);

        $part2 = $blocks['25-34'] . $check_digit;
        // Shift in a dot at its 6th position.
        $part2 = substr_replace($part2, '.', 5, 0);

        // Calculates part3 check digit from 3rd block of 10 characters.
        $check_digit = static::modulo10($blocks['35-44']);

        // As part2, we do the same process again for part3.
        $part3 = $blocks['35-44'] . $check_digit;
        $part3 = substr_replace($part3, '.', 5, 0);
        
        // Check digit for the human readable number.
        $num  = $this->codigoBanco.$this->moeda.$this->getFatorVencimento().$this->getValorZeroFill().$this->getCampoLivre();
        $cd   = $this->getDigitoVerificadorCodigoBarras($num);

        // Put part4 together.
        $part4  = $this->getFatorVencimento() . $this->getValorZeroFill();

        // Now put everything together.
        return "$part1 $part2 $part3 $cd $part4";
    }
    
    /**
     * Retorna o número Febraban
     * @override
     * @return string
     */
    public function getNumeroFebraban()
    {
        $num = $this->codigoBanco.$this->moeda.$this->getFatorVencimento().$this->getValorZeroFill().$this->getCampoLivre();
        $cd  = $this->getDigitoVerificadorCodigoBarras($num);
        
        return self::zeroFill($this->getCodigoBanco(), 3) . $this->getMoeda() . $cd . $this->getFatorVencimento() . $this->getValorZeroFill() . $this->getCampoLivre();
    }
    
    /**
     * Define variáveis da view específicas do boleto do Santander
     *
     * @return array
     */
    public function getViewVars()
    {
        return array(
            'esconde_uso_banco' => true,
        );
    }

    /**
     * Retorna o valor do boleto com 10 dígitos e remoção dos pontos/vírgulas
     *
     * @return string
     */
    protected function getValorZeroFill()
    {
        if($this->getValorCobrado() > 0 && $this->getValorCobrado() != ''){
            return str_pad(number_format($this->getValorCobrado(), 2, '', ''), 10, '0', STR_PAD_LEFT);
        } else{
            return str_pad(number_format($this->getValor(), 2, '', ''), 10, '0', STR_PAD_LEFT);
        }
    }    
}
