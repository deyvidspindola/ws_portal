<?php

/**
 * @author Ricardo Marangoni da Mota <ricardo.mota@meta.com.br>
 */

/**
 * Arquivo DAO responsável pelas requisições ao banco de dados
 */
require _MODULEDIR_ . 'Cadastro/DAO/CadAtendimentoProntaRespostaDAO.php';

/**
 * Classes utilizadas na geração de pdf
 */
include_once _SITEDIR_."/lib/tcpdf_php4/config/lang/eng.php";
include_once _SITEDIR_."/lib/tcpdf_php4/tcpdf.php";

class CadAtendimentoProntaResposta {
    
    private $dao;
    
    public function index() {
        
        $this->comboEquipes = $this->dao->getComboEquipes();
        
        require _MODULEDIR_ . 'Cadastro/View/cad_atendimento_pronta_resposta/index.php';
        
    }
	
	public function pesquisar() {
	
		$placa_veiculo 	= (!empty($_POST['placa'])) ? $_POST['placa'] : '';
		$dt_ini 		= (!empty($_POST['dt_ini'])) ? $_POST['dt_ini'] : '';
		$dt_fim 		= (!empty($_POST['dt_fim'])) ? $_POST['dt_fim'] : '';
		$equipe 		= (!empty($_POST['equipe'])) ? $_POST['equipe'] : '';
		$tipo 			= (!empty($_POST['tipo'])) ? $_POST['tipo'] : '';
		$recuperado 	= (!empty($_POST['recuperado'])) ? $_POST['recuperado'] : '';
		
		$where_array = array();
		
		if(!empty($dt_ini) && !empty($dt_fim)) {
			array_push($where_array, "prerdt_atendimento BETWEEN '$dt_ini 00:00:00' AND '$dt_fim 23:59:59'");
		}
		
		if(!empty($placa_veiculo)) {
			array_push($where_array, "prerplaca_veiculo = '$placa_veiculo'");
		}
		
		if(!empty($equipe)) {
			array_push($where_array, "prertetoid = $equipe");
		}
		
		if(!empty($tipo)) {
			array_push($where_array, "prrtp_ocorrencia = $tipo");
		}
		
		if(!empty($recuperado)) {
			array_push($where_array, "prerrecuperado = '$recuperado'");
		}
		
		$where = implode(',', $where_array);
		
		$where = str_replace(',', ' AND ', $where);
		
		$resultado = $this->dao->pesquisar($where);
		
		$this->pronta_resposta = array();
		
		if(count($resultado) > 0) {
		
			foreach($resultado as $res) {
				
				$this->pronta_resposta[] = array(
                    'id_atendimento' 	=> $res['id_atendimento'],
					'equipe'            => $res['equipe'],
					'placa_veiculo'     => $res['placa_veiculo'],
					'tipo'              => $res['tipo'],
					'uf'                => $res['uf'],
					'cidade'            => $res['cidade'],
					'is_recuperado'     => $res['is_recuperado'],
					'aprovado'          => $res['aprovado']
				);
			
			}
		}
				
		require _MODULEDIR_ . 'Cadastro/View/cad_atendimento_pronta_resposta/grid_pesquisa.php';
		exit;
		
	}
    
    public function novo() {
        
        $this->marcas = $this->dao->getMarcasVeiculo();
        $this->operadores_sascar = $this->dao->getOperadoresSascar();
        
        require _MODULEDIR_ . 'Cadastro/View/cad_atendimento_pronta_resposta/novo.php';
        
    }
    
    public function editar() {
        
        $id_atendimento = isset($_GET['id_atendimento']) ? $_GET['id_atendimento']: '';
        
        if(empty($id_atendimento)) {
            header('Location: cad_atendimento_pronta_resposta.php');
        }
        
        $this->atendimento = $this->dao->getAtendimentoById($id_atendimento);
        
        if($this->atendimento['aprovado'] == 't' && !$_SESSION['funcao']['permissao_total_ocorrencia']){
            header('Location: cad_atendimento_pronta_resposta.php?acao=visualizar&id_atendimento='.$id_atendimento);
        }
        
        $this->latitude = $this->atendimento['latitude'];
        $this->longitude = $this->atendimento['longitude'];
        /*
        $latitude_graus = explode('°', $this->atendimento['latitude']);
        $longitude_graus = explode('°', $this->atendimento['longitude']);
        
        
        
        $this->latitude_graus = $latitude_graus[0].'°';
        $this->longitude_graus = $longitude_graus[0].'°';
        
        $latitude_horas = explode("'", $latitude_graus[1]);
        $longitude_horas = explode("'", $longitude_graus[1]);
        
        $this->latitude_horas = $latitude_horas[0]."'";
        $this->longitude_horas = $longitude_horas[0]."'";
        
        $this->latitude_minutos = $latitude_horas[1];
        $this->longitude_minutos = $longitude_horas[1];
        */
        $this->marcas = $this->dao->getMarcasVeiculo();
        
        $this->id_marca_veiculo = $this->dao->getIdMarca($this->atendimento['veiculo_marca']);
        $this->modelos_veiculo = $this->dao->getModelosByMarca($this->id_marca_veiculo['id_marca']);
        
        $this->id_marca_carreta = $this->dao->getIdMarca($this->atendimento['carreta_marca']);
        $this->modelos_carreta = $this->dao->getModelosByMarca($this->id_marca_carreta['id_marca']);
        
        $this->operadores_sascar = $this->dao->getOperadoresSascar();        
        $this->anexos_atendimento = $this->dao->getAnexosAtendimento($id_atendimento);

        require _MODULEDIR_ . 'Cadastro/View/cad_atendimento_pronta_resposta/editar.php';
        
    }
    
    public function visualizar() {
        
        $id_atendimento = isset($_GET['id_atendimento']) ? $_GET['id_atendimento']: '';
        
        if(empty($id_atendimento)) {
            header('Location: cad_atendimento_pronta_resposta.php');
        }
        
        $this->atendimento = $this->dao->getAtendimentoById($id_atendimento);
        
        $this->anexos_atendimento = $this->dao->getAnexosAtendimento($id_atendimento);
        
        switch ($this->atendimento['aprovado']) {
            case 't':
                $this->atendimento['aprovado'] = 'Sim';
                break;
            
            case 'f':
                $this->atendimento['aprovado'] = 'Não';
                break;
        }
        
        switch ($this->atendimento['recuperado']) {
            case 't':
                $this->atendimento['recuperado'] = 'Sim';
                break;
            
            case 'f':
                $this->atendimento['recuperado'] = 'Não';
                break;
        }
        
        switch ($this->atendimento['tipo']) {
            case 0:
                $this->atendimento['tipo'] = 'Cerca';
                break;
            
            case 1:
                $this->atendimento['tipo'] = 'Roubo';
                break;
            
            case 2:
                $this->atendimento['tipo'] = 'Furto';
                break;
            
            case 3:
                $this->atendimento['tipo'] = 'Suspeita';
                break;
            
            case 4:
                $this->atendimento['tipo'] = 'Sequestro';
                break;
        }
        
        require _MODULEDIR_ . 'Cadastro/View/cad_atendimento_pronta_resposta/visualizar.php';
        
    }
    
    public function salvar() {
        
        /**
         * Elementos html que terão destaque caso haja exceção
         */
        $elements_with_error = array();
        $has_error = false;
        $defult_message = utf8_encode('Campo obrigatório não preenchido');
        
        $required_fields = array(
            'data', 'hora_acionamento', 'hora_chegada_local', 'hora_encerramento',
            'cep', 'uf', 'cidade', 'bairro', 'logradouro', 'end_numero', 'latitude',
            'longitude', 'operador_sascar', 'veiculo_placa', 'veiculo_cor', 'veiculo_ano', 
            'veiculo_marca', 'veiculo_modelo', 'placa_veiculo_busca', 'descricao_ocorrencia'
        );
        
        $required_fields_recup = array(
            'cep_recup', 'uf_recup', 'cidade_recup', 'bairro_recup', 'logradouro_recup',
            'destino_veiculo'
        );
        
        $check_hour = array(
            'hora_acionamento', 'hora_chegada_local', 'hora_encerramento'
        );
        
        try {
            
            $this->dao->preroid              = !empty($_POST['preroid']) ? $_POST['preroid'] : null;
            
            /* Dados Principais */
            $this->dao->aprovacao              = !empty($_POST['aprovacao']) ? 'TRUE' : 'FALSE';
            $this->dao->data                   = (isset($_POST['data'])) ? trim($_POST['data']) : '';
            $this->dao->hora_acionamento       = !empty($_POST['hora_acionamento']) ? trim($_POST['hora_acionamento']) . ':00' : '';
            $this->dao->hora_chegada_local     = !empty($_POST['hora_chegada_local']) ? trim($_POST['hora_chegada_local']) . ':00' : '';
            $this->dao->hora_encerramento      = !empty($_POST['hora_encerramento']) ? trim($_POST['hora_encerramento']) . ':00' : '';
            
            /* Local do Acionamento */
            $this->dao->cep                    = (isset($_POST['cep'])) ? trim($_POST['cep']) : '';
            $this->dao->uf                     = (isset($_POST['uf'])) ? $_POST['uf'] : $_POST['uf_hidden'];
            $this->dao->cidade                 = (isset($_POST['cidade'])) ? trim($_POST['cidade']) : '';
            $this->dao->bairro                 = (isset($_POST['bairro'])) ? trim($_POST['bairro']) : '';
            $this->dao->logradouro             = (isset($_POST['logradouro'])) ? trim($_POST['logradouro']) : '';
            $this->dao->end_numero             = (isset($_POST['end_numero'])) ? trim($_POST['end_numero']) : '';
            $this->dao->zona                   = (!empty($_POST['zona'])) ? trim($_POST['zona']) : '';
            
            if($this->dao->cidade == utf8_encode('São Paulo')) {                
                array_push($required_fields, 'zona');
            }
            
            /* Dados da Ocorrência */            
            $this->dao->latitude               = (isset($_POST['latitude'])) ? addslashes(trim($_POST['latitude'])) : '';
            $this->dao->longitude              = (isset($_POST['longitude'])) ? addslashes(trim($_POST['longitude'])) : '';
            /*$this->dao->latitude_graus         = (isset($_POST['latitude_graus'])) ? addslashes(trim($_POST['latitude_graus'])) : '';
            $this->dao->latitude_horas         = (isset($_POST['latitude_horas'])) ? addslashes(trim($_POST['latitude_horas'])) : '';
            $this->dao->latitude_minutos       = (isset($_POST['latitude_minutos'])) ? addslashes(trim($_POST['latitude_minutos'])) : '';
            $this->dao->longitude_graus        = (isset($_POST['longitude_graus'])) ? addslashes(trim($_POST['longitude_graus'])) : '';
            $this->dao->longitude_horas        = (isset($_POST['longitude_horas'])) ? addslashes(trim($_POST['longitude_horas'])) : '';
            $this->dao->longitude_minutos      = (isset($_POST['longitude_minutos'])) ? addslashes(trim($_POST['longitude_minutos'])) : '';
            */
            $this->dao->operador_sascar        = (isset($_POST['operador_sascar'])) ? trim($_POST['operador_sascar']) : '';
            $this->dao->tipo_ocorrencia        = (isset($_POST['tipo_ocorrencia'])) ? trim($_POST['tipo_ocorrencia']) : '';
            $this->dao->recuperado             = $_POST['recuperado'] == '1' ? 'TRUE' : 'FALSE';
            
         //   $this->dao->latitude = utf8_decode($this->dao->latitude_graus . $this->dao->latitude_horas . $this->dao->latitude_minutos);
         //   $this->dao->longitude = utf8_decode($this->dao->longitude_graus . $this->dao->longitude_horas . $this->dao->longitude_minutos);            
            
            /* Veículo */
            $this->dao->veiculo_placa          = (isset($_POST['veiculo_placa'])) ? strtoupper(trim($_POST['veiculo_placa'])) : '';
            $this->dao->veiculo_cor            = (isset($_POST['veiculo_cor'])) ? trim($_POST['veiculo_cor']) : '';
            $this->dao->veiculo_ano            = (isset($_POST['veiculo_ano'])) ? trim($_POST['veiculo_ano']) : '';
            $this->dao->veiculo_marca          = (isset($_POST['veiculo_marca'])) ? trim($_POST['veiculo_marca']) : '';
            $this->dao->veiculo_modelo         = (isset($_POST['veiculo_modelo'])) ? trim($_POST['veiculo_modelo']) : '';
            
            /* Carreta */
            $this->dao->carreta_placa          = (!empty($_POST['carreta_placa'])) ? strtoupper(trim($_POST['carreta_placa'])) : '';
            $this->dao->carreta_cor            = (!empty($_POST['carreta_cor'])) ? trim($_POST['carreta_cor']) : '';
            $this->dao->carreta_ano            = (!empty($_POST['carreta_ano'])) ? trim($_POST['carreta_ano']) : '';
            $this->dao->carreta_marca          = (!empty($_POST['carreta_marca'])) ? trim($_POST['carreta_marca']) : '';
            $this->dao->carreta_modelo         = (!empty($_POST['carreta_modelo'])) ? trim($_POST['carreta_modelo']) : '';
            $this->dao->carreta_carga          = (!empty($_POST['carreta_carga'])) ? trim($_POST['carreta_carga']) : '';
            
            /* Agente de Apoio */
            $this->dao->placa_veiculo_busca    = (isset($_POST['placa_veiculo_busca'])) ? trim($_POST['placa_veiculo_busca']) : '';
            
            /* Descrição da Ocorrência */
            $this->dao->descricao_ocorrencia   = (isset($_POST['descricao_ocorrencia'])) ? trim($_POST['descricao_ocorrencia']) : '';
            
            /* Endereço da Recuperação */
            $this->dao->cep_recup              = (isset($_POST['cep_recup'])) ? trim($_POST['cep_recup']) : '';
            $this->dao->uf_recup               = (isset($_POST['uf_recup'])) ? trim($_POST['uf_recup']) : $_POST['uf_recup_hidden'];
            
            $this->dao->cidade_recup           = (isset($_POST['cidade_recup'])) ? trim($_POST['cidade_recup']) : '';
            $this->dao->bairro_recup           = (isset($_POST['bairro_recup'])) ? trim($_POST['bairro_recup']) : '';
            $this->dao->logradouro_recup       = (isset($_POST['logradouro_recup'])) ? trim($_POST['logradouro_recup']) : '';
            $this->dao->numero_recup           = (isset($_POST['numero_recup'])) ? trim($_POST['numero_recup']) : '';
            $this->dao->zona_recup             = (!empty($_POST['zona_recup'])) ? trim($_POST['zona_recup']) : '';
            
            if($this->dao->cidade_recup == utf8_encode('São Paulo')) {                
                array_push($required_fields, 'zona_recup');
            }
            
            /* Destinação do Veículo Pós Recuperação */
            $this->dao->destino_veiculo        = (isset($_POST['destino_veiculo'])) ? trim($_POST['destino_veiculo']) : '';
            
            /**
             * Os campos do array $required_fields são obrigatórios
             */
            foreach($required_fields as $field) {                                
                
                if(empty($this->dao->$field)) {
                    $has_error = true;
                    $elements_with_error[] = array(
                        'input'      => '#'.$field,
                        'message'    => $defult_message
                    );
                }
                
            }
            
            /**
             * Caso a combo "Recuperado" venha com o valor "Sim(1)" é obrigatório
             * os preenchimento dos campos do array $required_fields_recup
             */            
            foreach($required_fields_recup as $field) {                                
                if($this->dao->recuperado == 'TRUE') {
                    if(empty($this->dao->$field)) {
                        $has_error = true;
                        $elements_with_error[] = array(
                            'input'      => '#'.$field,
                            'message'    => $defult_message
                        );
                    }
                } 
            }
            
            foreach($check_hour as $field) {                                                
                if(!$this->validaHora($this->dao->$field)) {
                    $has_error = true;
                    $elements_with_error[] = array(
                        'input'      => '#'.$field,
                        'message'    => utf8_encode('A hora digitada está invalida. O formato válido vai de 00:00 até 23:59')
                    );
                }
            }
            
           /* if((int)$this->dao->latitude_graus > 180 || (int)$this->dao->latitude_graus < -180) {
                $has_error = true;
                $elements_with_error[] = array(
                    'input'      => '#latitude_graus',
                    'message'    => utf8_encode('Valor digitado inválido')
                );
            }
            
            if((int)$this->dao->latitude_horas > 60) {
                $has_error = true;
                $elements_with_error[] = array(
                    'input'      => '#latitude_horas',
                    'message'    => utf8_encode('Valor digitado inválido')
                );
            }
            
            if((int)$this->dao->latitude_minutos > 60) {
                $has_error = true;
                $elements_with_error[] = array(
                    'input'      => '#latitude_minutos',
                    'message'    => utf8_encode('Valor digitado inválido')
                );
            }
            
            if((int)$this->dao->longitude_graus > 180 || (int)$this->dao->longitude_graus < -180) {
                $has_error = true;
                $elements_with_error[] = array(
                    'input'      => '#longitude_graus',
                    'message'    => utf8_encode('Valor digitado inválido')
                );
            }
            
            if((int)$this->dao->longitude_horas > 60) {
                $has_error = true;
                $elements_with_error[] = array(
                    'input'      => '#longitude_horas',
                    'message'    => utf8_encode('Valor digitado inválido')
                );
            }
            
            if((int)$this->dao->longitude_minutos > 60) {
                $has_error = true;
                $elements_with_error[] = array(
                    'input'      => '#longitude_minutos',
                    'message'    => utf8_encode('Valor digitado inválido')
                );
            }
            */
            if(strlen($this->dao->cep) < 8) {
                $has_error = true;
                $elements_with_error[] = array(
                    'input'      => '#cep',
                    'message'    => utf8_encode('Valor digitado inválido, o cep deve conter 8 caracteres')
                );
            }
            
            if($this->dao->recuperado == 'TRUE' && strlen($this->dao->cep_recup) < 8) {
                $has_error = true;
                $elements_with_error[] = array(
                    'input'      => '#cep_recup',
                    'message'    => utf8_encode('Valor digitado inválido, o cep deve conter 8 caracteres')
                );
            }
            
            /**
             * Se houver erro lança a exceção
             */
            if($has_error) {
                throw new Exception;
            }
            
            $inserted = $this->dao->salvar();
            
            if(!empty($inserted['preroid'])){
                echo json_encode(array('error' => false, 'preroid' => $inserted['preroid'], 'message' => $inserted['msg']));
            }else{
                echo json_encode(array('error' => true, 'message' => 'Houve um erro ao inserir o atendimento.'));
            }
            
            
            
        }catch(Exception $e){
            
            echo json_encode(array('error_validate_fields' => true, 'error_list' => $elements_with_error));
            
        }
        
    }
    
    public function uploadAnexo() {
        
        $file_uploaded = $_FILES['arquivo'];
        $id_atendimento = $_POST['id_atendimento'];
        
        $tipo_arquivo = $_POST['tipo_arquivo'] == 'documento' ? 0 : 1;        
        
        try {
            
            pg_query($this->dao->conn, 'BEGIN');
            
            $id_arquivo = $this->dao->inserirAnexo($file_uploaded, $tipo_arquivo, $id_atendimento);

            if(!$id_arquivo) {                
                throw new Exception('Erro ao inserir o arquivo de anexo.');
            }
            
            if($file_uploaded['error']) {                
                throw new Exception('Houve um erro no upload da imagem.');
            }

            $dir = '/var/www/anexos_ocorrencia';

            if(!is_dir($dir)) {
                if(!mkdir($dir, '0777')) {
                    throw new Exception('Houve um erro ao criar a pasta '. $dir);                    
                }
            }

            $destination = $dir . '/' . $id_atendimento;

            if(!is_dir($destination)) {
                if(!mkdir($destination, '0777')) {
                    throw new Exception('Houve um erro ao criar a pasta. '. $destination);                                                 
                }
                else{
                    if (!chmod($destination, 0755)){
                        throw new Exception('Houve um erro ao dar permissão na pasta. '. $destination);                                                 
                    }
                }
            }

            $moved = move_uploaded_file($file_uploaded['tmp_name'], $destination. '/'. $file_uploaded['name']);

            if(!$moved) {
                throw new Exception('Houve um erro ao mover a imagem.');                
            }

            echo json_encode(
                    array(
                        'error'         => false,
                        'message'       => 'Arquivo anexado com sucesso.',
                        'id_arquivo'    => $id_arquivo,
                        'tipo_arquivo'  => ucwords($_POST['tipo_arquivo']),
                        'data_inclusao' => date('d/m/Y'),
                        'nome_arquivo'  => $file_uploaded['name'],
                        'usuario'       => utf8_encode($_SESSION['usuario']['nome'])
                    )
            );
            
            pg_query($this->dao->conn, 'COMMIT');
        
        } catch(Exception $e) {
            
            pg_query($this->dao->conn, 'ROLLBACK');
            
            echo json_encode(
                    array(
                        'error'     => true,
                        'message'   => $e->getMessage()
                    )             
            );
            
        }
    }
    
    public function excluirAnexo() {
        $id_anexo       = $_POST['id_anexo'];
        $id_atendimento = $_POST['id_atendimento'];
        $nome_arquivo = $_POST['nome_arquivo'];
        
        //var_dump($id_atendimento);
        
        pg_query($this->dao->conn, 'BEGIN');
        
        $is_deleted = $this->dao->excluirAnexo($id_anexo);
        
        if($is_deleted) {
            
            $dir = '/var/www/anexos_ocorrencia/';
            
            $is_deleted_file = unlink($dir . $id_atendimento . '/' . $nome_arquivo);
            
            if(!$is_deleted_file) {
                
               pg_query($this->dao->conn, 'ROLLBACK');
                
               echo json_encode(
                    array(
                        'error'     => true,
                        'message'   => utf8_encode('Erro ao excluir o arquivo da pasta.')
                    )
               ); 
               
               return false;
               
            }
            
            echo json_encode(
                    array(
                        'error'     => false,
                        'message'   => utf8_encode('Arquivo excluído com sucesso.')
                    )
            );
            
            pg_query($this->dao->conn, 'COMMIT');
            
        } else {
            
            pg_query($this->dao->conn, 'ROLLBACK');
            
            echo json_encode(
                    array(
                        'error'     => true,
                        'message'   => 'Erro ao excluir o arquivo.'
                    )
            );
        }
    }


    public function getDadosVeiculo() {
        
        $placa = trim($_POST['placa']);
        
        echo json_encode($this->dao->getDadosVeiculo($placa));
        
    }
    
    public function getModelosByMarca() {
        
        $id_marca = $_POST['id_marca'];
        
        echo json_encode($this->dao->getModelosByMarca($id_marca));
        
    }
    
    public function getIdMarca(){
    	
    	$marca = $_POST['marca'];
    	
    	echo json_encode($this->dao->getIdMarca($marca));
    }
    
    
	
	public function gerarPDF(){
	
		$id_atendimento = (!empty($_POST['id'])) ? $_POST['id'] : ''; 
		$this->image_temp = array();
        
		$this->atendimento = $this->dao->getAtendimentoById($id_atendimento);
		
		$this->anexos = $this->dao->getAnexosAtendimento($id_atendimento);
		
    	$pdf = new TCPDF('P', PDF_UNIT, PDF_PAGE_FORMAT, false, 'ISO-8859-1', false);
    	
    	$pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
    	$pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));
    	
        $pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
        $pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
        $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);        
    	
    	$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
    	$pdf->setPrintHeader(false);
    	$pdf->setPrintFooter(false);
		$pdf->SetFont('Arial', '', 8);
		
		ob_start();
		
		require _MODULEDIR_ . 'Cadastro/View/cad_atendimento_pronta_resposta/pdf.php';		
		
		$relatorioHTML = ob_get_contents();
		
		ob_end_clean();
		
    	$pdf->AddPage();
    	$pdf->writeHTML($relatorioHTML, true, 0, true, 0);
    	$pdf->lastPage();
        
        $pdf->Output('cad_atendimento_pronta_resposta.pdf', 'D');
        
        foreach($this->image_temp as $image) {            
            if(file_exists($image)) {
                unlink($image);
            }            
        }  
	
	}
	
	public function gerarDoc(){
        
		$id_atendimento = (!empty($_POST['preroid'])) ? $_POST['preroid'] : ''; 		
        $this->image_temp = array();
        
		$this->atendimento = $this->dao->getAtendimentoById($id_atendimento);
		
		$this->anexos = $this->dao->getAnexosAtendimento($id_atendimento);
	
		ob_start();
		
		require _MODULEDIR_ . 'Cadastro/View/cad_atendimento_pronta_resposta/doc.php';		
		
		$relatorioHTML = ob_get_contents();
		file_put_contents('/var/www/docs_temporario/cad_atendimento_pronta_resposta.doc', $relatorioHTML);
        ob_end_clean();
        
        $doc = file_get_contents('/var/www/docs_temporario/cad_atendimento_pronta_resposta.doc');
		
		header( "Content-type: application/msword" );
		header( "Content-Disposition: inline, filename=cad_atendimento_pronta_resposta");

		echo $doc;
        
        foreach($this->image_temp as $image) {            
            if(file_exists($image)) {
                unlink($image);
            }            
        }  
	}
    
    private function validaHora($time) {
        
        $time = explode(':', $time);
        
        $horas      = (int)$time[0];
        $minutos    = (int)$time[1];
                
        if($horas > 23) {
            return false;
        }
        
        if($minutos > 59) {
            return false;
        }
        
        return true;
        
    }
    
    public function verificaCepExiste() {
        
        $cep = $_POST['cep'];
        
        echo $this->dao->verificaCepExiste($cep);
        
    }
    
    public function __set($var, $value) {
        $this->$var = $value;
    }
    
    public function __get($var) {
        return $this->$var;
    }
    
    public function __construct() {
        
        $this->dao = new CadAtendimentoProntaRespostaDAO();
        
    }
    
}
