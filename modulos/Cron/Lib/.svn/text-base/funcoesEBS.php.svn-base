<?php

/**
 * Funções
 *
 * @file    funcoesEBS.php
 * @author  BRQ
 * @since   16/08/2012
 * @version 16/08/2012
 *
 */
#############################################################################################################
#   Histórico
#       16/08/2012 - Diego C. Ribeiro(BRQ)
#           Criação do arquivo - DUM 79924
#       05/11/2012 - Diego C. Ribeiro (BRQ)
#           Adicionada rotina para confirmar o protocolo no 
#           FOX com erros (ConfirmarProcessamentoComDetalhes) - STI 80292
#############################################################################################################

/**
 * Aceita somente números
 * @param type $str
 * @return integer
 */
function soNumerosEBS($str){
    return preg_replace("/[^0-9]/", "", $str);
}

/**
 * Função compartilhada pelos conectores WS1 e WS2
 * 
 * Faz a confirmação do protocolo, quando confirmado, os dados do referido protocolo
 * são removidos automaticamente do canal de integração
 * @param type $protocolo
 */
function confirmacaoProtocolo($protocolo) {

#############################################################################################################
# CONFIRMAÇÃO DE RECEBIMENTO
#############################################################################################################

    echo "<hr>Confirmando recebimento protocolo $protocolo<hr>";

    $req = req();
    $req->protocolo = $protocolo;

    global $client;
    
    try {
        $client->ConfirmarProcessamento($req);
        echo "Protocolo confirmado com sucesso.\n\n";


    } catch (SoapFault $fault) {
        echo "SoapFault\n";
        echo "Code: " . $fault->faultcode . "\n";
        echo "String: " . $fault->faultstring . "\n";
    }
}

/**
 * Confirma o protocolo no FOX com Erros
 * @global type $client
 * @param string $protocolo
 * @param array $arrErros
 * 
 * $arrErros = array("Erros" => array("Codigo" => 0, "DocumentoOrigem" => "DOC001", "Mensagem" => "Mensagem de Erro"));
 */
function ConfirmarProcessamentoComDetalhes($protocolo, $arrErros = null) {

    echo "<hr>Confirmando recebimento protocolo $protocolo<hr>";

    $req = req();
    $req->detalhes = new stdClass();
    $req->detalhes->Avisos = array();
    $req->detalhes->Erros = array();
    
    if(isset($arrErros) and is_array($arrErros)){
        
        $count = 0;
        foreach ($arrErros as $arrErro) {
            
            $Erro{$count} = new stdClass();
            $Erro{$count}->Codigo           = $arrErro['Codigo'];
            $Erro{$count}->DocumentoOrigem  = $arrErro['DocumentoOrigem'];
            $Erro{$count}->Mensagem         = $arrErro['Mensagem'];
            $req->detalhes->Erros[]         = $Erro{$count};
            $count++;
        }
       
    // Se não foi setado um array com erros, insere 2 erros por padrão
    }else{
        $Erro = new stdClass();
        $Erro->Codigo = 0;
        $Erro->DocumentoOrigem = 'Erro 1';
        $Erro->Mensagem = "Mensagem de Erro 1";
        $req->detalhes->Erros[] = $Erro;

        $Erro2 = new stdClass();
        $Erro2->Codigo = 0;
        $Erro2->DocumentoOrigem = 'Erro 2';
        $Erro2->Mensagem = "Mensagem de Erro 2";
        $req->detalhes->Erros[] = $Erro2;
    }
            
    $req->detalhes->Protocolo = $protocolo;        

    global $client;
    
    try {
        $client->ConfirmarProcessamentoComDetalhes($req);
        echo "Protocolo confirmado com sucesso.\n\n";


    } catch (SoapFault $fault) {
        echo "SoapFault\n";
        echo "Code: " . $fault->faultcode . "\n";
        echo "String: " . $fault->faultstring . "\n";
    }
}

function validaCNPJ($CampoNumero){
    
    $RecebeCNPJ = $CampoNumero;
    
    $status = false;
    
    $s = "";
    
    for($x=1; $x<=strlen($RecebeCNPJ); $x=$x+1){
        
        $ch = substr($RecebeCNPJ,$x-1,1);
        
        if(ord($ch)>=48 && ord($ch)<=57){
            $s = $s.$ch;
        }
        
    }

    if($RecebeCNPJ=="00000000000000"){
        //echo "<h1>CNPJ Inv&aacute;lido</h1>";
    }else{
        
        $Numero[1]  = intval(substr($RecebeCNPJ,1-1,1));
        $Numero[2]  = intval(substr($RecebeCNPJ,2-1,1));
        $Numero[3]  = intval(substr($RecebeCNPJ,3-1,1));
        $Numero[4]  = intval(substr($RecebeCNPJ,4-1,1));
        $Numero[5]  = intval(substr($RecebeCNPJ,5-1,1));
        $Numero[6]  = intval(substr($RecebeCNPJ,6-1,1));
        $Numero[7]  = intval(substr($RecebeCNPJ,7-1,1));
        $Numero[8]  = intval(substr($RecebeCNPJ,8-1,1));
        $Numero[9]  = intval(substr($RecebeCNPJ,9-1,1));
        $Numero[10] = intval(substr($RecebeCNPJ,10-1,1));
        $Numero[11] = intval(substr($RecebeCNPJ,11-1,1));
        $Numero[12] = intval(substr($RecebeCNPJ,12-1,1));
        $Numero[13] = intval(substr($RecebeCNPJ,13-1,1));
        $Numero[14] = intval(substr($RecebeCNPJ,14-1,1));

        $soma = $Numero[1] * 5 + $Numero[2] * 4 + $Numero[3] * 3 + $Numero[4] * 2 + $Numero[5] * 9 + $Numero[6] * 8 + $Numero[7] * 7 + $Numero[8] * 6 + $Numero[9] * 5 + $Numero[10] * 4 + $Numero[11] * 3 + $Numero[12] * 2;

        $soma = $soma - (11 * (intval($soma / 11) ) );

        if($soma==0 || $soma==1){
            $resultado1 = 0;
        }else{
            $resultado1 = 11 - $soma;
        }
        
        if($resultado1==$Numero[13]){
            
            $soma = $Numero[1] * 6 + $Numero[2] * 5 + $Numero[3] * 4 + $Numero[4] * 3 + $Numero[5] * 2 + $Numero[6] * 9 + $Numero[7] * 8 + $Numero[8] * 7 + $Numero[9] * 6 + $Numero[10] * 5 + $Numero[11] * 4 + $Numero[12] * 3 + $Numero[13] * 2;
            
            $soma = $soma - (11 * (intval($soma / 11) ) );
            
            if($soma==0 || $soma==1){
                $resultado2 = 0;
            }else{
                $resultado2 = 11 - $soma;
            }
            
            if($resultado2==$Numero[14]){
                $status = true;
            }
            
        } 
        
    }
    
    return $status;
    
}

function validaCPF($cpf){
    
    $status = false;
    
    if( ($cpf == '11111111111') || ($cpf == '22222222222') ||
        ($cpf == '33333333333') || ($cpf == '44444444444') ||
        ($cpf == '55555555555') || ($cpf == '66666666666') ||
        ($cpf == '77777777777') || ($cpf == '88888888888') ||
        ($cpf == '99999999999') || ($cpf == '00000000000') ) {
            
        $status = false;
        
    }else{

        $dv_informado = substr($cpf, 9,2);

        for($i=0; $i<=8; $i++) {
            $digito[$i] = substr($cpf, $i,1);
        }
        
        $posicao = 10;
        $soma    = 0;

        for($i=0; $i<=8; $i++) {
            
            $soma    = $soma + $digito[$i] * $posicao;
            $posicao = $posicao - 1;
            
        }

        $digito[9] = $soma % 11;

        if($digito[9] < 2){
            $digito[9] = 0;
        }else{
            $digito[9] = 11 - $digito[9];
        }
    
        $posicao = 11;
        $soma    = 0;

        for($i=0; $i<=9; $i++){
            
            $soma    = $soma + $digito[$i] * $posicao;
            $posicao = $posicao - 1;
            
        }

        $digito[10] = $soma % 11;

        if($digito[10] < 2){
            $digito[10] = 0;
        }else{
            $digito[10] = 11 - $digito[10];
        }
        
        $dv = $digito[9] * 10 + $digito[10];
        
        if($dv != $dv_informado){
            $status = false;
        }else{
            $status = true;
        }
        
    }
    
    return $status;
    
}

function date_to($data, $to_americana = false) {
	if ($to_americana) {
		$data = str_replace("/", "-", $data);
		$dia = substr($data, 0, strpos($data, "-"));
		$data = substr($data, strpos($data, "-") + 1);
		$mes = substr($data, 0, strpos($data, "-"));
		$data = substr($data, strpos($data, "-") + 1);
		if (strpos($data, " "))
			$ano = substr($data, 0, strpos($data, " "));
		else
			$ano = $data;
		if (!(@ checkdate($mes, $dia, $ano)))
			$data = false;
		else
			$data = $ano . "-" . $mes . "-" . $dia;
	} else {
		$data = str_replace("/", "-", $data);
		$ano = substr($data, 0, strpos($data, "-"));
		$data = substr($data, strpos($data, "-") + 1);
		$mes = substr($data, 0, strpos($data, "-"));
		$data = substr($data, strpos($data, "-") + 1);
		if (strpos($data, " "))
			$dia = substr($data, 0, strpos($data, " "));
		else
			$dia = $data;
		if (!(@ checkdate($mes, $dia, $ano)))
			$data = false;
		else
			$data = $dia . "/" . $mes . "/" . $ano;
	}
	return $data;
}

function data_e_hora_to($data, $to_americana = false) {
	$data1 = date_to($data, $to_americana);
	if ($data1) {
		if (strpos($data, " ")) {
			$data1 = $data1 . " " . substr($data, strpos($data, " ") + 1, 5);
		}
	}
	return $data1;
}

// Valida se o CEP é válido (não valida se o CEP existe)
function validaCep($cep){
	$cep = trim(soNumerosEBS($cep));	
	if(is_numeric($cep) and strlen($cep) == 8 and (int)$cep != 0){
		return true;
	}else{
		return false;
	}
}


// Decodifica o objeto recebido do WS,  que é recebido no formato UTF-8
function utf8Decode_recursivo($object) {

    if(!empty($object)){
        foreach ($object as $key => $value) {

            if (is_object($value)) {
                $value = utf8Decode_recursivo($value);
            } else {
                if (is_string($value)) {
                    $object->$key = utf8_decode($value);
                } elseif (is_array($value)) {

                    foreach ($value as $key => $value2) {

                        if (is_object($value2) or is_array($value2)) {
                            $value[$key] = utf8Decode_recursivo($value2);
                        }
                    }
                    $object->$key = $value;
                }
            }
        }
    
    }
    return $object;
}


?>