<?php

/**
 * VO CreditoFuturoMovimento
 *  
 * @author Renato Teixeira Bueno <renato.bueno@meta.com.br>
 * 
 */
class CreditoFuturoHistoricoVO {
	
	public $usuarioInclusao;
    public $operacao;
    public $origem;
    public $creditoFuturoId;
    public $status;
    public $tipoDesconto;
    public $formaAplicacao;
    public $aplicarDescontoSobre;
    public $qtdParcelas;
    public $valor;
    public $saldo;
    public $observacao;
    public $justificativa;
    public $obrigacaoFinanceiraDesconto;
    public $cfhsaldo_parcelas;
    
    public $nf_numero;
    public $nf_serie;
    public $dt_emissao_nf;
    public $valor_total_nf;
    public $vl_total_itens_nf;
    public $valor_aplicado_desconto;
    public $num_parcela_aplicada;
	
}
