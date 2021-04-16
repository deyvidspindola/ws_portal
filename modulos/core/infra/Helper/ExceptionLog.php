<?php
/**
 * Sascar - Sistema Corporativo
 *
 * LICENSE
 *
 * Sascar Tecnologia Automotiva S/A - Todos os Direitos Reservados
 *
 * @author Fabio Andrei Lorentz
 * @version 19/05/2014
 * @since 19/05/2014
 * @package Core
 * @subpackage ExceptionLog
 * @copyright Copyright (c) Sascar Tecnologia Automotiva S/A (http://www.sascar.com.br)
 */
namespace infra\Helper;

use infra\DBObjectHelper AS DBO,
	\Exception as Exception;

class ExceptionLog extends Exception {
	protected $mensagem;
	protected $codigoMensagem;
	protected $tabela;
	protected $tabelaPrefixo;
	protected $codigoTabela;
	protected $codigoUsuario;

	/**
	 * Constrуi uma exceзгo e grava um registro em uma tabela de LOG
	 * @param string  $mensagem       Mensagem da exceзгo
	 * @param string  $codigoMensagem Cуdigo da exceзгo
	 * @param string  $tabela         Tabela de LOG
	 * @param string  $tabelaPrefixo  Prefixo da tabela de LOG
	 * @param integer $codigoTabela   Cуdigo (Serial/ID) do item que gerou a exceзгo. Ex: A exceзгo foi gerada ao tentar mudar o ID da proposta 667691 (informar este nъmero)
	 * @param integer $codigoUsuario  Cуdigo do usuбrio logado no momento que gerou a exceзгo
	 */
	public function __construct($mensagem, $codigoMensagem, $tabela, $tabelaPrefixo, $codigoTabela, $codigoUsuario = 0) {		
		parent::__construct($mensagem, (int)$codigoMensagem);
		$this->mensagem = $mensagem;
		$this->codigoMensagem = $codigoMensagem;
		$this->tabela = $tabela;
		$this->tabelaPrefixo = $tabelaPrefixo;
		$this->codigoTabela = (int)$codigoTabela;
		$this->codigoUsuario = ($codigoUsuario > 0) ? (int)$codigoUsuario : (int)$_SESSION['usuario']['oid'];
		$this->gravaLog();
	}

	/**
	 * Retorna a mensagem da exceзгo
	 * @return string
	 */
	public function getMensagem() {
		return $this->mensagem;
	}

	/**
	 * Retorna o cуdigo da exceзгo
	 * @return string
	 */
	public function getCodigo() {
		return $this->codigoMensagem;
	}

	/**
	 * Grava um registro da exceзгo em uma tabela de LOG
	 * @return void
	 */
	protected function gravaLog() {
		$sql = "INSERT INTO %s
				({prefixo}dt_cadastro, {prefixo}usuoid_logado, {prefixo}codigo, {prefixo}codigo_mensagem, {prefixo}mensagem)
				VALUES (NOW(),%d,%d,'%s','%s')";

		$sql = str_replace("{prefixo}", $this->tabelaPrefixo, $sql);

		$stmt = sprintf($sql, $this->tabela, $this->codigoUsuario, $this->codigoTabela, $this->codigoMensagem, $this->mensagem);

		$db = new DBObjectHelper();
		$db->queryExec($stmt);
	}
}
?>