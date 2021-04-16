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
 * Classe boleto HSBC Bank Brasil S/A
 *
 * @package    module\Boleto
 * @author     Bruno Bonfim Affonso <bruno.bonfim@sascar.com.br>
 * @copyright  Copyright (c) 2016 Sascar - Tecnologia e Segurança Automotiva S/A (http://www.sascar.com.br)
 * @license    Sascar License
 * @version    1.0
 */
class Hsbc extends BoletoAbstract
{
    /**
     * Código do banco
     * @var string
     */
    protected $codigoBanco = '399';

    /**
     * Localização do logotipo do banco, referente ao diretório de imagens
     * @var string
     */
    protected $logoBanco = 'hsbc.jpg';

    /**
     * Linha de local de pagamento
     * @var string
     */
    protected $localPagamento = 'At&eacute; o vencimento, preferencialmente no HSBC. Ap&oacute;s o vencimento, somente no HSBC.';

    /**
     * Define as carteiras disponíveis para este banco
     * @var array
     */
    protected $carteiras = array('347');
    
    /**
     * Define os nomes das carteiras para exibição no boleto
     * @var array
     */
    protected $carteirasNomes = array('347' => 'CNR');

    /**
     * Campo obrigatório para emissão de boletos com carteira 198 fornecido pelo Banco com 5 dígitos
     * @var int
     */
    protected $codigoCliente;

    /**
     * Dígito verificador da carteira/nosso número para impressão no boleto
     * @var int
     */
    protected $carteiraDv;

    /**
     * Cache do campo livre para evitar processamento desnecessário.
     *
     * @var string
     */
    protected $campoLivre;

    /**
     * Define o código do cliente
     *
     * @param int $codigoCliente
     * @return $this
     */
    public function setCodigoCliente($codigoCliente)
    {
        $this->codigoCliente = $codigoCliente;
        return $this;
    }

    /**
     * Retorna o código do cliente
     *
     * @return int
     */
    public function getCodigoCliente()
    {
        return $this->codigoCliente;
    }
    
    /**
     * Retorna o campo Agência/Cedente do boleto
     *
     * Overrided porque o codigo do cedente do HSBC não contem agencia
     *
     * @return string
     */
    public function getAgenciaCodigoCedente()
    {
        $conta = $this->getConta();
        return $conta;
    }
    
    /**
     * Define o número da conta
     *
     * Overrided porque o cedente da HSBC TEM QUE TER 7 posições, senão não é válido
     *
     * @param int $conta
     * @return BoletoAbstract
     */
    public function setConta($conta)
    {
        $this->conta = self::zeroFill($conta, 7);
        return $this;
    }

    /**
     * Gera o Nosso Número.
     *
     * @return string
     */
    protected function gerarNossoNumero()
    {
        $this->getCampoLivre(); // Força o calculo do DV.
        $numero = self::zeroFill($this->getCarteira(), 3) . '/' . self::zeroFill($this->getSequencial(), 8);
        $numero .= '-' . $this->carteiraDv;

        return $numero;
    }

    /**
     * Método para gerar o código da posição de 20 a 44
     *
     * @return string
     * @throws \OpenBoleto\Exception
     */
    public function getCampoLivre()
    {
        if ($this->campoLivre) {
            return $this->campoLivre;
        }

        $sequencial = self::zeroFill($this->getSequencial(), 8);
        $carteira = self::zeroFill($this->getCarteira(), 3);
        $agencia = self::zeroFill($this->getAgencia(), 4);
        $conta = self::zeroFill($this->getConta(), 7);

        // Carteira 198 - (Nosso Número com 15 posições) - Anexo 5 do manual
        if (in_array($this->getCarteira(), array('107', '122', '142', '143', '196', '198'))) {
            $codigo = $carteira . $sequencial .
                self::zeroFill($this->getNumeroDocumento(), 7) .
                self::zeroFill($this->getCodigoCliente(), 5);

            // Define o DV da carteira para a view
            $this->carteiraDv = $modulo = static::modulo10($codigo);

            return $this->campoLivre = $codigo . $modulo . '0';
        }

        // Geração do DAC - Anexo 4 do manual
        if (!in_array($this->getCarteira(), array('126', '131', '146', '150', '168'))) {
            // Define o DV da carteira para a view
            $this->carteiraDv = $dvAgContaCarteira = static::modulo10($agencia . $conta . $carteira . $sequencial);
        } else {
            // Define o DV da carteira para a view
            $this->carteiraDv = $dvAgContaCarteira = static::modulo10($carteira . $sequencial);
        }

        // Módulo 10 Agência/Conta
        $dvAgConta = static::modulo10($agencia . $conta);

        return $this->campoLivre = $carteira . $sequencial . $dvAgContaCarteira . $agencia . $conta . $dvAgConta . '000';
    }

    /**
     * Define nomes de campos específicos do boleto do HSBC
     *
     * @return array
     */
    public function getViewVars()
    {
        return array(
            'esconde_uso_banco' => false,
        );
    }
}
