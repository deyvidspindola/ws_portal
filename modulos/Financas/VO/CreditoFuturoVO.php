<?php

/**
 * VO CreditoFuturo
 * 
 * @author Renato Teixeira Bueno <renato.bueno@meta.com.br>
 * 
 */
class CreditoFuturoVO {
	
	/*@Type  integer */
	public $id;
	
	/*@Type  integer */
	public $cliente;
	
	/*@Type  integer */
	public $tipoDesconto;
	
	/*@Type  integer */
	public $status;
	
	/*@Type  double */
	public $valor;
	
	/*@Type  integer */
	public $formaAplicacao;
	
	/*@Type  double */
	public $saldo;
	
	/*@Type  string */
	public $aplicarDescontoSobre;
	
	/*@Type  string */
	public $observacao;
	
	/*@Type  string */
	public $dataInclusao;
	
	/*@Type  string */
	public $dataExclusao;
	
	/*@Type  string */
	public $dataEncerramento;
	
	/*@Type  string */
	public $dataAvaliacao;
	
	/*@Type  integer */
	public $usuarioInclusao;
	
	/*@Type  integer */
	public $usuarioExclusao;
	
	/*@Type  integer */
	public $usuarioEncerramento;
	
	/*@Type  integer */
	public $usuarioAvaliador;
	
	/*@Type  object(CreditoFututoParcela) */
	public $Parcelas;
	
	/*@Type  object(CreditoFuturoMovimento) */
	public $Movimentos;
	
	/*@Type  integer */
	public $protocolo;
	
	/*@Type  integer */
	public $contratoIndicado;
	
	/*@Type  object(CreditoFuturoMovitoCreditoVO) */
	public $MotivoCredito;
	
	/*@Type  object(CreditoFuturoCampanhaPromocionalVO) */
	public $CampanhaPromocional;
	
	/*@Type  integer */
	public $obrigacaoFinanceiraDesconto;
	
	/*@Type  integer */
	public $formaInclusao;
	
	/*@Type  integer */
	public $qtdParcelas;	
	
	/*@Type  integer */
	public $origem;
	
	
}
