<?php 

class UraAtivaParamVO {

	public $codigoIdentificador; // pânico_oid, vegoid, osoid
	public $idTelefoneContato; // IdContatoExterno
	public $telefoneContato;
	public $opcaoSelecionada;
	public $data;

	/**
	 * Preenche o objeto de paramentros com retorno da URA
	 * @param string $codigoIdentificador oid(panico_oid, vegoid, osoid);IdContatoExterno;telefoneContato
	 * @param int $opcaoSelecionada
	 * @param date $data No formato ddmmYYYY
	 */
	public function __construct($codigoIdentificador, $opcaoSelecionada = '', $data = '') {

		$codigos = explode('#', $codigoIdentificador);

		$this->codigoIdentificador 	= isset($codigos[0]) ? preg_replace("/[^0-9, -]/", "", trim($codigos[0])) : '';
		$this->idTelefoneContato 	= isset($codigos[1]) ? preg_replace("/[^0-9]/", "", trim($codigos[1])) : '';
		$this->telefoneContato 		= isset($codigos[2]) ? preg_replace("/[^0-9]/", "", trim($codigos[2])) : '';
		$this->opcaoSelecionada 	= trim($opcaoSelecionada);
		$this->data 				= trim($data);

		
		if (empty($this->codigoIdentificador) ||
			empty($this->idTelefoneContato) ||
			empty($this->telefoneContato))
		{
			throw new Exception('0160');
			
		}
		
		if (!empty($this->data)) {
			
			$dia = substr($this->data, 0, 2);
			$mes = substr($this->data, 2, 2);
			$ano = substr($this->data, 4);
			
			if (strlen($this->data) != 8 || !checkdate($mes, $dia, $ano)) {
				throw new Exception('0220'); // Data inválida.
			}
			
			$this->data = $ano.'-'.$mes.'-'.$dia;
		}
	}
}