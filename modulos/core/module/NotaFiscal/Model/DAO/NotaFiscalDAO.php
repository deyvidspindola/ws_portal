<?php
namespace module\NotaFiscal;

use infra\ComumDAO;

class NotaFiscalDAO extends ComumDAO
{
	public function __construct()
	{
		parent::__construct();
	}

	public function getById($id)
	{
		$sql = "SELECT * FROM nota_fiscal WHERE nfloid = $id LIMIT 1;";
		$this->queryExec($sql);
		return $this->getNumRows() > 0 ? $this->getAssoc() : null;
	}

	public function getByNumero($numero)
	{
		$sql = "SELECT * FROM nota_fiscal WHERE nflno_numero = $numero LIMIT 1;";
		$this->queryExec($sql);
		return $this->getNumRows() > 0 ? $this->getAssoc() : null;
	}

	public function get()
	{
		// [ ] = campo para permitir marcar as NF que serão "liberadas para gerar o arquivo RPS";

		// Dt. Transmissão RPS = Data da transmissão do arquivo para a PMB; quando houver;
		// Dt. Retorno RPS = Data do processamento do arquivo na PMB; quando houver;
		// Situação de envio = (Será implementado na   ORGFIN-592 DETAILING  ); quando houver.
		// Contador de Registros = apresentar a quantidade de registros encontrados de acordo com os filtros informados.
		// Exemplo: 0004 Registro(s) encontrado(s) / 0002 Erros / 0002 Aguardando processamento
		$sql = "
			SELECT
				nflno_numero as numero_nota_fiscal,
				nflserie as serio_nota_fiscal,
				clinome as nome_cliente,
				clino_rg as rg_cliente,
				clino_cpf as cpf_cliente,
				nflvl_total as valor_total,
				nfldt_faturamento as data_faturamento,
				nfldt_cancelamento as data_cancelamento,
				nfeno_nfe as numero_nfe
			"
	}

}