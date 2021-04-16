<?php

/**
 * @author Felipe Ribeiro <felipe.ribeiro@ewave.com.br>
 * @since 27/11/2014
 */

class PrnUploadFotografiaOS {

    public function __construct() {	
        global $conn;
        $this->conn = $conn;
    }

    public function query($query) {
        return pg_query($this->conn, $query);
    }

    /**
    * Busca uma ordem de serviço através de seu ID
    * @param int $ordoid
    * @return array
    */
    public function getOrdemServicoById($ordoid) {

        $query = "SELECT ordoid FROM ordem_servico WHERE ordoid = " . intval($ordoid);

        $query = pg_query($this->conn, $query);

        $result = pg_fetch_all($query);
        
        return $result;
    }

    /**
    * Busca um usuário vinculado a uma O.S.
    * @param int $ordoid
    * @return array
    */
    public function getUsuarioByOS($ordoid) {
        
        $query = "SELECT 
                    cd_usuario
                FROM 
                    ordem_servico AS os
                INNER JOIN relacionamento_representante AS rr
                    ON rr.relroid = os.ordrelroid
                INNER JOIN usuarios AS us
                    ON us.usurefoid = rr.relrrepoid
                WHERE 
                    os.ordoid = $ordoid
                AND
                    us.dt_exclusao IS NULL
                LIMIT 1";

        $query = pg_query($this->conn, $query);

        $result = pg_fetch_all($query);
        
        return $result;
    }
    
    /**
    * Insere as fotografias através de webservice
    */
    public function create() {

        include_once("lib/nusoap.php");

        $response = array();
        $endpoint = $this->getEndpoint();
        $method = 'sendImageOS';
        $imagemD = base64_encode(file_get_contents($_FILES['ssio_foto_d_file']['tmp_name']));
        $imagemE = base64_encode(file_get_contents($_FILES['ssio_foto_e_file']['tmp_name']));
        
        $params = array(
            'usuario'=> 'testeImagemSend',
            'senha' => '12345',
            'imagem'=> $imagemD,
            'tipoImagem'=> 'png',
            'os'=> $_POST['ssioordoid'],
            'usuarioID'=> $_POST['usuarioID'],
            'lado'=> $_POST['ssio_foto_d'],
        );

        $nusoapClient = new nusoap_client($endpoint);
        $result = $nusoapClient->call($method, $params);

        if($result['header']['status']['codigo'] == '0000') {
            
            $params['lado'] = $_POST['ssio_foto_e'];
            $params['imagem'] = $imagemE;
            $params['tipoImagem'] = 'png';
            $result = $nusoapClient->call($method, $params);
            
        }

        // Erro
        if($result['header']['status']['codigo'] != '0000') {

            $codigo = $result['header']['status']['codigo'];
            $msg = '';
            $classe = '';

            switch ($codigo) {
                case '0012':
                    $msg = 'O.S. não vinculada a um usuário/representante.';
                    $classe = 'mensagem alerta';
                    break;

                case '0013':
                    $msg = 'A OS já possui fotos vinculadas';
                    $classe = 'mensagem alerta';
                    break;

                case '0014':
                    $msg = $result['header']['status']['descricao'];
                    $classe = 'mensagem erro';
                    break;
                
                default:
                    $msg = $result['header']['status']['descricao'];
                    $classe = 'mensagem alerta';
                    break;
            }

            $response[] = array(
                'message' => $msg,
                'class'   => $classe
            );
            
        }
        
        return $response;
    }

    /**
     * Retorna endereco do webservice
     * @return string
     */
    public function getEndpoint() {

        if(strstr($_SERVER['HTTP_HOST'], 'intranet') && !strstr($_SERVER['HTTP_HOST'], 'hom1')){
            return 'http://intranet.sascar.com.br/WS_SIGGO_SEGURADORA/ws_servers.php?wsdl';
        } elseif(strstr($_SERVER['HTTP_HOST'], 'hom1')){
            return 'http://hom1.intranet.sascar.com.br/sistemaWeb/WS_SIGGO_SEGURADORA/ws_servers.php?wsdl';
        } elseif(strstr($_SERVER['HTTP_HOST'], 'teste')){
            return 'http://teste.sascar.com.br/sistemaWeb/WS_SIGGO_SEGURADORA/ws_servers.php?wsdl';
        } else {
            // Desenvolvimento
            return 'http://desenvolvimento.sascar.com.br/sistemaWeb/WS_SIGGO_SEGURADORA/ws_servers.php?wsdl';
        }
        
    }

    /**
    * Valida as informações do formulário
    * @return array
    */
    public function formValidation() {
        
        $response = array();
        $ssioordoid = $_POST['ssioordoid'] != '' && is_numeric($_POST['ssioordoid']) ? $_POST['ssioordoid'] : 'NULL';
        
        if(empty($_POST['ssioordoid']) || empty($_FILES['ssio_foto_e_file']['name']) || empty($_FILES['ssio_foto_d_file']['name'])) {

            $response[] = array(
                "message" => "Necessário preencher os campos número OS, imagem direita e imagem esquerda",
                "class"   => "mensagem alerta",
                "input1"   => empty($_POST['ssioordoid']) ? "ssioordoid" : "",
                "input2"   => empty($_FILES['ssio_foto_e_file']['name']) ? "ssio_foto_e_file" : "",
                "input3"   => empty($_FILES['ssio_foto_d_file']['name']) ? "ssio_foto_d_file" : ""
            );
        }

        if(!empty($_POST["ssioordoid"]) && !is_numeric($_POST["ssioordoid"])) {

            $response[] = array(
                "message" => "Número de Ordem de Serviço não existe.",
                "class"   => "mensagem erro",
                "input"   => "ssioordoid"
            );
        }
        
        $ordemServico = $this->getOrdemServicoById($ssioordoid);

        if(!empty($_POST["ssioordoid"]) && empty($ordemServico)) {
            
            $response[] = array(
                "message" => "Número de Ordem de Serviço não existe.",
                "class"   => "mensagem alerta",
                "input"   => "ssioordoid"
            );    
        }
        
        $usuario = $this->getUsuarioByOS($ssioordoid);

        if(!empty($_POST["ssioordoid"]) && empty($usuario)) {
            
            $response[] = array(
                "message" => "O.S. não vinculada a um usuário/representante.",
                "class"   => "mensagem alerta",
                "input"   => "ssioordoid"
            );    
        }
        else {
            $_POST['usuarioID'] = $usuario[0]['cd_usuario'];
        }
        
        return $response;
    }

    public function myInArray($search, $array) {
        
        $in_keys = array();
        
        foreach($array as $key => $value) {
            
            if(in_array($search, $value)) {
                $in_keys[] = $key;
            }
        }

        if(count($in_keys) > 0) {
            return $in_keys;
        }
        else {
            return false;
        }
    }
}
?>