<?php

class RetornoBoletoSantanderReader {

	const CODIGO_BENEFICIARIO_ONLINE = 8528748;
	const CODIGO_BENEFICIARIO_OFFLINE = 8144958;

	private $codigoBeneficiario;
	private $empresa;
	private $agenciaCentralizadora;
	private $contaCentralizadora;
	private $contaCobranca;
	private $tipoCobranca;
	private $modalidadeCobranca;
	private $situacao;
	private $dataInicial;
	private $dataFinal;
	private $titulos = array();

	public function __construct($data){
		$this->parseFile($data);
	}

	public function isArquivoOnline(){
		return $this->getCodigoBeneficiario() == self::CODIGO_BENEFICIARIO_ONLINE;
	}

	public function isArquivoOffline(){
		return $this->getCodigoBeneficiario() == self::CODIGO_BENEFICIARIO_OFFLINE;
	}

	public function parseFile($data){

		$rowCount = 1;

		while(($row = fgetcsv($data, 0, ";")) !== false){

    	if($rowCount === 2){

    		$expAgenciaContaCentralizadora = explode('-', $row[2]);

    		$codigoBeneficiario = !empty($row[0]) ? trim($row[0]) : null;
    		$empresa = !empty($row[1]) ? trim($row[1]) : null;
    		$agenciaCentralizadora = !empty($expAgenciaContaCentralizadora[0]) ? trim($expAgenciaContaCentralizadora[0]) : null;
    		$contaCentralizadora = !empty($expAgenciaContaCentralizadora[1]) ? trim($expAgenciaContaCentralizadora[1]) : null;

    		$this->setCodigoBeneficiario($codigoBeneficiario);
    		$this->setEmpresa($empresa);
    		$this->setAgenciaCentralizadora($agenciaCentralizadora);
    		$this->setContaCentralizadora($contaCentralizadora);

    	}elseif($rowCount === 5){

    		$expTipoModalidadeCobranca = explode('-', $row[1]);

    		$contaCobranca = !empty($row[0]) ? trim($row[0]) : null;
    		$tipoCobranca = !empty($expTipoModalidadeCobranca[0]) ? trim($expTipoModalidadeCobranca[0]) : null;
    		$modalidadeCobranca = !empty($expTipoModalidadeCobranca[1]) ? trim($expTipoModalidadeCobranca[1]) : null;
    		$situacao = !empty($row[2]) ? trim($row[2]) : null;

    		$this->setContaCobranca($contaCobranca);
    		$this->setTipoCobranca($tipoCobranca);
    		$this->setModalidadeCobranca($modalidadeCobranca);
    		$this->setSituacao($situacao);

    	}elseif($rowCount === 7){

    		$dataInicial = !empty($row[0]) ? trim($row[0]) : null;
    		$dataFinal = !empty($row[1]) ? trim($row[1]) : null;    			

    		// check if is valid date else throw Exception
    		// verificar se sempre as 2 datas serão necessários (principalmente para os casos de intervalos)

    		$this->setDataInicial($dataInicial);
    		$this->setDataFinal($dataFinal);

    	}elseif($rowCount >= 10){

    		if(!empty($row[0]) && !empty($row[1])){

    			$seuNumero = (string)$row[0];

    			if($this->isArquivoOnline()){
    				$seuNumero = substr($seuNumero, 0, -1);
    			}

	    		$titulo = array(
	    			'seu_numero' 		=> $seuNumero,
	    			'nosso_numero'	=> $row[1],
	    			'valor' 				=> $row[2],
	    			'vencimento' 		=> $row[3],
	    			'pagador' 			=> $row[4]
	    		);

	    		$this->addTitulo($titulo);

    		}

    	}

      $rowCount++;

		}

	}

	public function getCodigoBeneficiario(){
		return $this->codigoBeneficiario;
	}
	private function setCodigoBeneficiario($codigoBeneficiario){
		
		if(self::CODIGO_BENEFICIARIO_ONLINE != $codigoBeneficiario && self::CODIGO_BENEFICIARIO_OFFLINE != $codigoBeneficiario)
			throw new Exception("Código do beneficiário inválido");
			
		$this->codigoBeneficiario = $codigoBeneficiario;

	}

	public function getEmpresa(){
		return $this->empresa;
	}
	private function setEmpresa($empresa){
		$this->empresa = $empresa;
	}

	public function getAgenciaCentralizadora(){
		return $this->agenciaCentralizadora;
	}
	private function setAgenciaCentralizadora($agenciaCentralizadora){
		$this->agenciaCentralizadora = $agenciaCentralizadora;
	}

	public function getContaCentralizadora(){
		return $this->contaCentralizadora;
	}
	private function setContaCentralizadora($contaCentralizadora){
		$this->contaCentralizadora = $contaCentralizadora;
	}

	public function getContaCobranca(){
		return $this->contaCobranca;
	}
	private function setContaCobranca($contaCobranca){
		$this->contaCobranca = $contaCobranca;
	}

	public function getTipoCobranca(){
		return $this->tipoCobranca;
	}
	private function setTipoCobranca($tipoCobranca){
		$this->tipoCobranca = $tipoCobranca;
	}

	public function getModalidadeCobranca(){
		return $this->modalidadeCobranca;
	}
	private function setModalidadeCobranca($modalidadeCobranca){
		$this->modalidadeCobranca = $modalidadeCobranca;
	}

	public function getSituacao(){
		return $this->situacao;
	}
	private function setSituacao($situacao){
		$this->situacao = $situacao;
	}

	public function getDataInicial($formato = 'Y-m-d'){
		return date($formato, strtotime($this->dataInicial));
	}
	
	private function setDataInicial($dataInicial){

		$dataInicialExplode = explode("/", $dataInicial);

		if(empty($dataInicial) || !$dataInicialExplode)
			throw new Exception("Arquivo com títulos registrados no banco com período inválido");

		$dataInicialFormatada = $dataInicialExplode[2] .'-'. $dataInicialExplode[1] .'-'. $dataInicialExplode[0];

		$this->dataInicial = $dataInicialFormatada;
	}

	public function getDataFinal($formato = 'Y-m-d'){
		return date($formato, strtotime($this->dataFinal));
	}

	private function setDataFinal($dataFinal){

		$dataFinalExplode = explode("/", $dataFinal);

		if(empty($dataFinal) || !$dataFinalExplode)
			throw new Exception("Arquivo com títulos registrados no banco com período inválido");

		$dataFinalFormatada = $dataFinalExplode[2] .'-'. $dataFinalExplode[1] .'-'. $dataFinalExplode[0];

		$this->dataFinal = $dataFinalFormatada;

	}

	public function getTitulos(){
		return $this->titulos;
	}
	private function addTitulo($titulo){
		$this->titulos[] = $titulo;
	}

}