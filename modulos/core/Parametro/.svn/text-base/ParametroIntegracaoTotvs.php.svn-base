<?php

namespace module\Parametro;

use infra\ParametroDAO as ParametroDAO;

class ParametroIntegracaoTotvs  {

    private static $parametroDAO;

    public static function getDAO()
    {
        if(empty(self::$parametroDAO)){
            self::$parametroDAO = new ParametroDAO('INTEGRACAO_TOTVS');
        }
        return self::$parametroDAO;
    }

    public static function getIntegracaoTotvsAtiva()
    {
        return (self::getDAO()->getParametro('INTEGRACAO_ATIVA') == 'true');
    }

    public static function getUrlWsProtheus()
    {
        return self::getDAO()->getParametro('URL_WS_PROTHEUS');
    }
    
    // [ORGMKTOTVS-1620] - cris
    public static function getIntegracao($param)
    {
         $integracao = (self::getDAO()->getParametro($param)) == 'true' ? true : false;
         return $integracao;
    }

    // ORGMKTOTVS-1547 - Leandro Corso
    // ORGMKTOTVS-1944 - Leandro Corso
    public static function message($message, $type='info', $literal = false) {

        if ($literal) {

            $text = '<div class="mensagem %1$s">%2$s</div>';
            return sprintf($text, $type, $message);

        } else{

            $singular = 'foi desativado';
            $plural = 'foram desativados';
            $text = '<div class="mensagem %1$s">%2$s %3$s devido a integração com o Totvs Protheus estar ativa.</div>';

            if (is_array($message)) {
                if (count($message) > 1) {
                    $strN = array_pop($message);
                    $str1 = implode(', ', $message) . ' e ' . $strN;
                    $str2 = $plural;
                } else {
                    $str1 = $message[0];
                    $str2 = $singular;
                }
            } else {
                $str1 = $message;
                $str2 = $singular;
            }

            return sprintf($text, $type, $str1, $str2);

        }
    }

    // ORGMKTOTVS-1547 - Leandro Corso
    public static function blockPage($param) {
        if ($param) {
            header('Location: bloqueio-totvs.php');
            die();
        }
    }
    
}