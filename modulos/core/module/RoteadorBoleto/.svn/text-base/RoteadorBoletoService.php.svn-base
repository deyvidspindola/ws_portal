<?php

namespace module\RoteadorBoleto;

class RoteadorBoletoService
{
	public static function __callStatic($method, $params)
	{

		list($idTitulo, $tipoRegistro) = $params;

		$params = array_slice($params, 2);
		$controller = new RoteadorBoletoController($idTitulo, $tipoRegistro);

		return call_user_func_array(array($controller, $method), $params);
	}
}
