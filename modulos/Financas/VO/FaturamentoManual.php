<?php

/**
 * Classe de persistência de dados
 * 
 * @author Marcelo Fuchs <marcelo.fuchs@meta.com.br>
 * 
 */
class FaturamentoManual {

    const NAT_PRESTACAO_SERVICOS = 'PRESTACAO DE SERVICOS';
	private $parametros;
    
    public function __construct($ids_notas) {
        $this->ids_notas = $ids_notas;
        $key=$this->getKey();
        if(isset($_SESSION['fat_manual'][$key]))
        	$this->parametros = unserialize($_SESSION['fat_manual'][$key]);
    }
    
    public function __get($param){
    	return $this->parametros[$param];
    }  
    
    public function __set($param, $valor){
    	$this->parametros[$param] = $valor;
    }

	public function getKey(){
		return md5($this->ids_notas);
	}   

	public function getNatureza(){
		return FaturamentoManual::NAT_PRESTACAO_SERVICOS;
	}
    
    public function __destruct() {;
    	$key = $this->getKey();
    	$_SESSION['fat_manual'][$key]=serialize($this->parametros);	
    }    
    
    /**
     * Controle de itens
     */
    public function addItem($item, $verifica) {
    	$arr = $this->getItens();
    	if(isset($item['connumero']) && intval($item['connumero']) > 0 && count($arr) > 0 && $verifica == true)
    		foreach ($arr as $k => $i)
    			if($i['connumero']==$item['connumero'] && $i['obroid']==$item['obroid'])
           			throw new Exception('Este item j&aacute; foi inclu&iacute;do anteriormente.');
    	$arr[]=$item;
    	$this->setItens($arr);
    }

    public function editItem($item, $posicaoItem) {
    	$arr = $this->getItens();
        $itemAlterado = $arr[$posicaoItem];
        foreach ($itemAlterado as $key => $value){
            if(isset($item[$key])){
                $itemAlterado[$key] = $item[$key];
            }
        }
        
    	$arr[$posicaoItem]=$itemAlterado;
    	$this->setItens($arr);
    }
    
    public function removeItem($key) {
    	$arr = $this->getItens();
    	unset($arr[$key]);
    	$this->setItens($arr);
    }
    
    public function getItens() {
    	return $this->itens;
    }
    
    public function setItens($listItens) {
    	$this->itens = $listItens;
    }


    /**
     * Controle de Crédito Futuro
     */
    public function addCredito($credito) {
        $arrCredito = $this->getCreditos();
        $arrCredito[$credito['credito_id']] = $credito;
        $this->setCredito($arrCredito);
    }

    public function editCredito($credito) {
        $arr = $this->getCreditos();
        $creditoAlterado = $arr[$credito['credito_id']];
        foreach ($creditoAlterado as $key => $value){
            if(isset($credito[$key])){
                $creditoAlterado[$key] = $credito[$key];
            }
        }
        
        $arr[$credito['credito_id']]=$creditoAlterado;
        $this->setCredito($arr);
    }

    public function deleteCredito() {
        $this->creditos = array();
    }

    public function getCreditos() {
        return $this->creditos;
    }

    public function setCredito($creditos) {
        $this->creditos = $creditos;
    }
}
