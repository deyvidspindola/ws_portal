<?php

namespace module\Parametro;

use infra\ParametroDAO as ParametroDAO;

class ParametroNotaFiscalEletronica  {
	
	private static $parametroDAO;

	public static function getDAO(){
		if(empty(self::$parametroDAO)){
			self::$parametroDAO = new ParametroDAO('NOTA_FISCAL_ELETRONICA_BARUERI');
		}
		return self::$parametroDAO;
	}

	public static function getCodigoInscricaoContribuinte(){
		if(!empty($_SESSION['servidor_teste']) && $_SESSION['servidor_teste'] == 1){
			return self::getCodigoInscricaoContribuinteTeste();
		}else{
			return self::getCodigoInscricaoContribuinteProducao();
		}
	}

	private static function getCodigoInscricaoContribuinteTeste(){
		return self::getDAO()->getParametro('CODIGO_INSCRICAO_CONTRIBUINTE_TESTE');
	}

	private static function getCodigoInscricaoContribuinteProducao(){
		return self::getDAO()->getParametro('CODIGO_INSCRICAO_CONTRIBUINTE_PRODUCAO');
	}

}