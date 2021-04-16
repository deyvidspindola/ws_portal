<?php

/**
 * Classe SincronizacaoLegado.
 * Sincroniza agendamentos futuros feitos pelo modolu do agendamento antigo
 * Cria atividades no OFSC para agendamentos realizados no modulo antigo
 *
 * @package  SmartAgenda
 * @author   Luiz Pontara
 *
 */


//classe de manipulação de dados no bd
require_once (_MODULEDIR_ . 'SmartAgenda/DAO/SincronizacaoLegadoDAO.php');


/**
 * classes pacote Smart Agenda
 */
require_once _MODULEDIR_ ."/SmartAgenda/Action/Capacity.php";
require_once _MODULEDIR_ ."/SmartAgenda/Action/Activity.php";
require_once _MODULEDIR_ ."/SmartAgenda/Action/Inbound.php";
require_once _MODULEDIR_ ."/SmartAgenda/Action/EstoqueAgenda.php";
require_once _MODULEDIR_ ."/SmartAgenda/Action/SmartAgenda.php";

require_once _MODULEDIR_ ."/Principal/Action/PrnAgendamentoUnitario.php";   
require_once _MODULEDIR_ ."/Principal/DAO/PrnAgendamentoUnitarioDAO.php";


class SincronizacaoLegado{

    private $dao;
    private $daoUnitario;

    /** propriedade para dados a serem utilizados na View. */
    private $view;


    private $agendamentosErro;
    private $agendamentos;

    public function __construct(){
        Global $conn;
        $this->dao              = is_object($dao) ? $this->dao = $dao : NULL;
        $this->view             = new stdClass();
        $this->agendamentosErro = array();
        $this->atividadesErro   = array();

        $this->dao = new SincronizacaoLegadoDAO($conn);

        $this->daoUnitario = new PrnAgendamentoUnitarioDAO($conn);
    }

    /**
     * Reponsável também por realizar a pesquisa invocando o método privado
     * @return void
     */
    public function index() {

        //Incluir a view padrão
        require_once _MODULEDIR_ . "SmartAgenda/View/sincronizacao_legado/index.php";
    }


    public function pesquisar(){

        $this->view->erros = array();

        $this->view->parametros = $this->tratarParametros();

        if($this->view->parametros->cmp_data_inicio == ''){
            $this->index();
            return false;
        }
        if($this->view->parametros->cmp_data_fim == ''){
            $this->index();
            return false;
        }

        if($this->view->parametros->arquivo['name'] != ''){
            $dadosImportados = $this->importar($this->view->parametros);
        }

        $agendamentos = $this->dao->pesquisar($this->view->parametros);
        $this->view->objAgendamentos = array();
        if(count($agendamentos)){
            foreach ($agendamentos as $chave => $dados) {

                $this->view->agendamentos[$chave]['idAgenda']  = $dados->osaoid;
                $this->view->agendamentos[$chave]['ordem']     = $dados->osaordoid;
                $this->view->agendamentos[$chave]['ponto']     = 'FIXO';

                //Verifica dados desta OS existe na importacao
                if(isset($dadosImportados[$dados->osaordoid])){
                    //Endereco
                    $this->view->agendamentos[$chave]['cep']         = '';
                    $this->view->agendamentos[$chave]['uf']          = (empty($dadosImportados[$dados->osaordoid][1]) ? '' : trim($dadosImportados[$dados->osaordoid][1]));
                    $this->view->agendamentos[$chave]['idUf']        = '';
                    $this->view->agendamentos[$chave]['cidade']      = (empty($dadosImportados[$dados->osaordoid][2]) ? '' : trim($dadosImportados[$dados->osaordoid][2]));
                    $this->view->agendamentos[$chave]['idCidade']    = '';
                    $this->view->agendamentos[$chave]['bairro']      = (empty($dadosImportados[$dados->osaordoid][3]) ? '' : trim($dadosImportados[$dados->osaordoid][3]));
                    $this->view->agendamentos[$chave]['idBairro']    = '';
                    $this->view->agendamentos[$chave]['logradouro']  = (empty($dadosImportados[$dados->osaordoid][4]) ? '' : trim($dadosImportados[$dados->osaordoid][4]));
                    $this->view->agendamentos[$chave]['num']         = (empty($dadosImportados[$dados->osaordoid][5]) ? '' : trim($dadosImportados[$dados->osaordoid][5]));
                    $this->view->agendamentos[$chave]['complemento'] = (empty($dadosImportados[$dados->osaordoid][6]) ? '' : trim($dadosImportados[$dados->osaordoid][6]));
                    $this->view->agendamentos[$chave]['referencia']  = (empty($dadosImportados[$dados->osaordoid][7]) ? '' : trim($dadosImportados[$dados->osaordoid][7]));

                    //Contato
                    $this->view->agendamentos[$chave]['responsavel'] = (empty($dadosImportados[$dados->osaordoid][8]) ? '' : trim($dadosImportados[$dados->osaordoid][8]));
                    $this->view->agendamentos[$chave]['celularresp'] = (empty($dadosImportados[$dados->osaordoid][9]) ? '' : trim($dadosImportados[$dados->osaordoid][9]));
                    $this->view->agendamentos[$chave]['contato']     = (empty($dadosImportados[$dados->osaordoid][10]) ? '' : trim($dadosImportados[$dados->osaordoid][10]));
                    $this->view->agendamentos[$chave]['celularcont'] = (empty($dadosImportados[$dados->osaordoid][11]) ? '' : trim($dadosImportados[$dados->osaordoid][11]));

                    $this->view->agendamentos[$chave]['observacao']  = (empty($dadosImportados[$dados->osaordoid][11]) ? '' : trim($dadosImportados[$dados->osaordoid][12]));

                    $this->view->agendamentos[$chave]['ponto']       = (empty($dadosImportados[$dados->osaordoid][11]) ? '' : trim($dadosImportados[$dados->osaordoid][13]));

                }else{

                    //Dados do endereco ponto fixo
                    if($dados->itlrepoid){
                        $dadosEndereco = $this->dao->enderecoPontoFixo($dados->itlrepoid);
                    }

                    if(isset($dadosEndereco[0])){
                        //Endereco
                        $this->view->agendamentos[$chave]['cep']         = $dadosEndereco[0]->cep;
                        $this->view->agendamentos[$chave]['uf']          = $dadosEndereco[0]->clcuf_sg;
                        $this->view->agendamentos[$chave]['idUf']        = $dadosEndereco[0]->clcestoid;
                        $this->view->agendamentos[$chave]['cidade']      = $dadosEndereco[0]->clcnome;
                        $this->view->agendamentos[$chave]['idCidade']    = $dadosEndereco[0]->clcoid;
                        $this->view->agendamentos[$chave]['bairro']      = $dadosEndereco[0]->cbanome;
                        $this->view->agendamentos[$chave]['idBairro']    = $dadosEndereco[0]->cbaoid;
                        $this->view->agendamentos[$chave]['logradouro']  = $dadosEndereco[0]->endvrua;
                        $this->view->agendamentos[$chave]['num']         = $dadosEndereco[0]->endvnumero;
                        $this->view->agendamentos[$chave]['complemento'] = $dadosEndereco[0]->endvcomplemento;
                        $this->view->agendamentos[$chave]['referencia']  = 'N/A';
                    }else{
                        //caso não tenha tratado o endereco
                        $this->agendamentosErro($agendamentos[$chave]);
                        unset($this->view->agendamentos[$chave]);
                        continue;
                    }
                        
                    //Contato e Responsavel
                    $dadosContato = $this->dao->getDadosEmailSms($dados->osaordoid);
                    if(count($dadosContato)){
                        $this->view->agendamentos[$chave]['responsavel'] = (empty($this->view->agendamentos[$chave]['osiresponsavel']) ? 'Smart Agenda' : $this->view->agendamentos[$chave]['osiresponsavel']);
                        $this->view->agendamentos[$chave]['celularresp'] = (empty($dadosContato['oscccelular']) ? '' : $this->trataTelefone($dadosContato['oscccelular']) );
                        $this->view->agendamentos[$chave]['contato']     = (empty($this->view->agendamentos[$chave]['osiresponsavel']) ? 'Smart Agenda' : $this->view->agendamentos[$chave]['osiresponsavel']);
                        $this->view->agendamentos[$chave]['celularcont'] = (empty($dadosContato['oscccelular']) ? '' : $this->trataTelefone($dadosContato['oscccelular']) );
                        $this->view->agendamentos[$chave]['emailcont']   = (empty($dadosContato['osecemail'])   ? '' : $dadosContato['osecemail']   );
                    }

                    $this->view->agendamentos[$chave]['observacao'] = trim(nl2br(strip_tags(addslashes($dados->osaobservacao))));
                }

                //data hora agendamento
                $this->view->agendamentos[$chave]['data']   = $dados->osadata;
                $this->view->agendamentos[$chave]['hora']   = $dados->osahora;
                $this->view->agendamentos[$chave]['timeslot'] = $this->setTimeSlot($dados->osahora);
                $this->view->agendamentos[$chave]['agrupamento'] = (strtoupper($dados->agccodigo) == 'CSC' ? 'CASCO' : 'CARGA');


                //tecnico e representante
                $this->view->agendamentos[$chave]['tecnico']       = $dados->osaitloid;
                $this->view->agendamentos[$chave]['representante'] = $dados->itlrepoid;

                $this->view->objAgendamentos[$dados->osaordoid] = $this->view->agendamentos[$chave];
            }

            $this->agendamentos = $this->view->agendamentos;

            if($this->view->parametros->radio_unitario === "on"){
                $this->agendamentoUnitario();
            }

            if($this->view->parametros->radio_xml === "on"){
                $this->view->gerarXml = true;
                $this->view->dadosXml = $this->dadosXml($this->view->agendamentos);
            }else{
                $this->view->gerarXml = false;
            }
        }

        $this->view->erros = $this->agendamentosErro;

        //Incluir a view padrão
        require_once _MODULEDIR_ . "SmartAgenda/View/sincronizacao_legado/index.php";
    }


    /**
     * Trata o endereco do banco
     * porém não atende todos os casos
     * pois nao há um padrão preenchimento dos campos
     * @param  [type] $endereco [description]
     * @return [type]           [description]
     */
    private function trataEndereco($endereco){

        $retorno = array();

        preg_match_all("/^(RUA|Rua|R.|AVENIDA|Avenida|AV.|TRAVESSA|Travessa|TRAV.|Trav.|AV|R|Rod|Rod.|Rodovia|Est|ALAMEDA|PRACA|RODOVIA|ROD|RUA:|ESTRADA|TV|AV.) ([a-zA-Z_\s]+)[, ]+(\d+)+[ ]+([a-zA-Z0-9\s]+)/i", trim($endereco), $retorno, PREG_SET_ORDER);

        if(!count($retorno)){
            preg_match_all("/^(RUA|Rua|R.|AVENIDA|Avenida|AV.|TRAVESSA|Travessa|TRAV.|Trav.|AV|R|Rod|Rod.|Rodovia|Est|ALAMEDA|PRACA|RODOVIA|ROD|RUA:|ESTRADA|TV|AV.) ([a-zA-Z_\s]+)[, ]+(\d+)/i", trim($endereco), $retorno, PREG_SET_ORDER);
        }

        return $retorno;
    }


    private function agendamentosErro($dadosAgendamento){

        array_push($this->agendamentosErro, $dadosAgendamento);

    }

    private function trataTelefone($telefone){

        $telefone = preg_replace("/[^0-9]/", "", $telefone);
        $tel3 = substr($telefone, -4);
        $tel2 = substr($telefone, 2, -4);
        $tel1 = substr($telefone, 0, 2);
        $retorno = "(".$tel1.") ".$tel2."-".$tel3;

        return $retorno;

    }


    /**
     * Gera dados xml para importação via Selenium ou agendamento via código
     * @param  [type] $agendamentos [description]
     * @return [type]               [description]
     */
    private function dadosXml($agendamentos){

        foreach ($agendamentos as $chave => $dados) {
            $dadosXml[$chave]['ordem']  = $dados['ordem'];

            //Endereco
            $dadosXml[$chave]['uf']          = strtoupper($dados['uf']);
            $dadosXml[$chave]['cidade']      = strtoupper($dados['cidade']);
            $dadosXml[$chave]['bairro']      = strtoupper($dados['bairro']);
            $dadosXml[$chave]['logradouro']  = strtoupper($dados['logradouro']);
            $dadosXml[$chave]['num']         = $dados['num'];
            $dadosXml[$chave]['complemento'] = $dados['complemento'];
            $dadosXml[$chave]['referencia']  = $dados['referencia'];

            //Contato e Responsavel
            $dadosXml[$chave]['responsavel'] = $dados['responsavel'];
            $dadosXml[$chave]['celularresp'] = $dados['celularresp'];
            $dadosXml[$chave]['contato']     = $dados['contato'];
            $dadosXml[$chave]['celularcont'] = $dados['celularcont'];

            $dadosXml[$chave]['observacao']  = '';

            $meses = array( 1 => 'JAN', 2 => 'FEV', 3 => 'MAR', 4 => 'ABR', 5 => 'MAI', 6 => 'JUN', 7 => 'JUL', 8 => 'AGO', 9 => 'SET', 10 => 'OUT', 11 => 'NOV', 12 => 'DEZ');
            $dataAgendamento = DateTime::createFromFormat('Y-m-d', $dados['data']);
            $dadosXml[$chave]['data']     = $dataAgendamento->format('d-') . $meses[$dataAgendamento->format('n')] . $dataAgendamento->format('-Y');
            $dadosXml[$chave]['timeslot'] = $dados['data'] . '/' . $dados['timeslot'] . '/PS' . $dados['representante'] . '/' . $dados['agrupamento'] . ' ' . $dados['ponto'] . '/' . $dados['ponto'];
        }

        return $dadosXml;
    }


    public function criarAtividade(){

        //$parametros = $this->tratarParametros();
        if(!isset($_POST['agendamentos'])){
            echo json_encode("erro");
            exit();
        }else{
            $agendamentos = $_POST['agendamentos'];
        }

        if(count($agendamentos) == 0){
            echo json_encode("erro");
            exit();
        }

        foreach ($agendamentos as $chave => $valor) {
            $this->agendamentos[$chave] = $agendamentos[$chave]['agendamento'];
        }

        $this->agendamentoUnitario();

        echo json_encode(implode(', ', $this->atividadesErro));
        exit();
    }

    private function agendamentoUnitario(){

        if(!count($this->agendamentos)){
            return false;
        }

        foreach ($this->agendamentos as $chave => $agendamento) {

            //WORK ZONE
            $dadosEndereco['XA_STATE_CODE']        = $agendamento['idUf'];
            $dadosEndereco['XA_CITY_CODE']         = $agendamento['idCidade'];
            $dadosEndereco['XA_NEIGHBORHOOD_CODE'] = $agendamento['idBairro'];

            //Endereco Atendimento
            $dadosEndereco['XA_NEIGHBORHOOD_NAME'] = $agendamento['bairro'];
            $dadosEndereco['XA_ADDRESS_REFERENCE'] = $agendamento['referencia'];
            $dadosEndereco['XA_ADDRESS_2']         = $agendamento['complemento'];
            $dadosEndereco['address']              = $agendamento['logradouro'] . ", " . $agendamento['num'];
            $dadosEndereco['city']                 = $agendamento['cidade'];
            $dadosEndereco['state']                = $agendamento['estado'];
            $dadosEndereco['zip']                  = $agendamento['cep'];

            $ordemServico = $this->daoUnitario->getOrdemServico($agendamento['ordem']);

            $property = array(
                array(
                    'label' => 'XA_WO_NUMBER',
                    'value' => $agendamento['ordem']
                ),
                array(
                    'label' => 'XA_COUNTRY_CODE',
                    'value' => 'BR'
                ),
                array(
                    'label' => 'XA_STATE_CODE',
                    'value' => $dadosEndereco['XA_STATE_CODE']
                ),
                array(
                    'label' => 'XA_CITY_CODE',
                    'value' => $dadosEndereco['XA_CITY_CODE']
                ),
                array(
                    'label' => 'XA_NEIGHBORHOOD_CODE',
                    'value' => $dadosEndereco['XA_NEIGHBORHOOD_CODE']
                ),
                array(
                    'label' => 'XA_ADDRESS_2',
                    'value' => $dadosEndereco['XA_ADDRESS_2']
                ),
                array(
                    'label' => 'XA_NEIGHBORHOOD_NAME',
                    'value' => $dadosEndereco['XA_NEIGHBORHOOD_NAME']
                ),
                array(
                    'label' => 'XA_ADDRESS_REFERENCE',
                    'value' => $dadosEndereco['XA_ADDRESS_REFERENCE']
                ),
                array(
                    'label' => 'XA_COUNTRY',
                    'value' => 'Brasil'
                ),
                array(
                    'label' => 'XA_WO_TYPE',
                    'value' => $ordemServico['tipo']
                ),
                array(
                    'label' => 'XA_WO_GROUP',
                    'value' => $ordemServico['grupo']
                ),
                array(
                    'label' => 'XA_WO_REASON',
                    'value' => $ordemServico['dificuldade']
                ),
                array(
                    'label' => 'XA_CONTRACT',
                    'value' => $ordemServico['ordconnumero']
                ),
                array(
                    'label' => 'XA_CONTRACT_CLASS',
                    'value' => $ordemServico['eqcoid']
                ),
                array(
                    'label' => 'XA_CONTRACT_CLASS_GROUP',
                    'value' => $ordemServico['eqcecgoid']
                ),
                array(
                    'label' => 'XA_CONTRACT_MODALITY',
                    'value' => $ordemServico['conmodalidade']
                ),
                array(
                    'label' => 'XA_CONTRACT_DATE',
                    'value' => date("d/m/Y", strtotime($ordemServico['condt_cadastro']))
                ),
                array(
                    'label' => 'XA_CONTRACT_EFFECTIVE_DATE',
                    'value' => date("d/m/Y", strtotime($ordemServico['condt_ini_vigencia']))
                ),
                array(
                    'label' => 'XA_VEHICLE_PLATE',
                    'value' => $ordemServico['veiplaca']
                ),
                array(
                    'label' => 'XA_VEHICLE_CHASSI',
                    'value' => $ordemServico['veichassi']
                ),
                array(
                    'label' => 'XA_VEHICLE_MODEL',
                    'value' => $ordemServico['mlomodelo']
                ),
                array(
                    'label' => 'XA_VEHICLE_YEAR',
                    'value' => $ordemServico['veino_ano']
                ),
                array(
                    'label' => 'XA_VEHICLE_COLOR',
                    'value' => $ordemServico['veicor']
                ),
                array(
                    'label' => 'XA_VEHICLE_RENAVAM',
                    'value' => $ordemServico['veino_renavan']
                ),
                array(
                    'label' => 'XA_CEL_NOTIFICATION',
                    'value' => $agendamento['celularcont']
                ),
                array(
                    'label' => 'XA_EMAIL_NOTIFICATION',
                    'value' => $agendamento['emailcont']
                ),
                array(
                    'label' => 'XA_CONTACT_NAME',
                    'value' => $agendamento['contato']
                ),
                array(
                    'label' => 'XA_CONTACT_CELL_PHONE',
                    'value' => $agendamento['celularcont']
                ),
                array(
                    'label' => 'XA_SCHEDULING_TYPE',
                    'value' => ($agendamento['ponto'] == 'FIXO') ? 'F' : 'M'
                ),
                array(
                    'label' => 'XA_GENERAL_NOTES',
                    'value' => $agendamento['observacao']
                ),
            );

            $servicosOS = $this->daoUnitario->getServicosOS($agendamento['ordem']);

            foreach ($servicosOS as $numero => $servico) {
                // Adiciona mais um ao número para evitar o zero
                $numero++;

                $propertyItem = array(
                    array(
                        'label' => "XA_SERVICE_ITEM_{$numero}",
                        'value' => $servico['codigo_tipo_os']
                    ),
                    array(
                        'label' => "XA_SERVICE_TYPE_{$numero}",
                        'value' => $servico['id_tipo_os']
                    ),
                    array(
                        'label' => "XA_SERVICE_REASON_{$numero}",
                        'value' => $servico['id_tipo_servico']
                    ),
                    array(
                        'label' => "XA_ALLEGED_DEFECT_{$numero}",
                        'value' => $servico['id_tipo_defeito_alegado']
                    ),
                    array(
                        'label' => "XA_SERVICE_NOTE_{$numero}",
                        'value' => $servico['status']
                    )
                );
                $property = array_merge($property, $propertyItem);
            }

            $provider = array(
                array(
                    'external_id' => 'TC' . $agendamento['tecnico'],
                    'type' => 'required'
                )
            );

            $labelTipoOrdemServico = removeAcentos($ordemServico['ostdescricao']);

            $ofsc = array(
                'date'        => $agendamento['data'],
                'type'        => 'update_activity',
                'external_id' => 'PS' . $agendamento['representante'],
                'appointment' => array(
                    'appt_number'     => $agendamento['idAgenda'],
                    'customer_number' => ($ordemServico['clitipo'] == 'J') ? $ordemServico['clino_cgc'] : $ordemServico['clino_cpf'],
                    'duration'        => '',
                    'worktype_label'  => $labelTipoOrdemServico,
                    'time_slot'       => $agendamento['timeslot'],
                    'name'            => $ordemServico['clinome'],
                    'address'         => $dadosEndereco['address'],
                    'city'            => $dadosEndereco['city'],
                    'state'           => $dadosEndereco['state'],
                    'zip'             => $dadosEndereco['zip'],
                    'provider_preferences' => array(
                        'preference'  => $provider
                    ),
                    'properties'      => array(
                        'property'    => $property
                    )
                )
            );

            //echo "<pre>";print_r($ofsc);echo "</pre>";

            if(!$this->criarAtividadeOFSC($ofsc)){
                $this->atividadesErro[] = $agendamento['ordem'];
            }
        }
        
    }

    private function criarAtividadeOFSC($dadosAtvidade){

        $inbound = new Inbound();

        // Salva o agendamento no OFSC
        $inbound->date = date('Y-m-d');

        $inbound->setCommands($this->converterUTF8($dadosAtvidade));

        $resultado = $inbound->entrada();

        if (!$resultado['resultado']) {
            return false;
        }
    }


    /**
     * Importa arquivo CSV
     */
    private function importar($parametros) {

        try {

            //valida se o formato do arquivo e CSV
            if(!$this->validaCsv($parametros->arquivo['type'])){
                throw new Exception("Formato do arquivo Errado");
            }

            $uploaddir = '/var/www/docs_temporario/';
            $uploadfile = $uploaddir . basename($parametros->arquivo["name"]);
            if(!move_uploaded_file($parametros->arquivo["tmp_name"], $uploadfile)) {
                throw new ErrorException("Erro ao importar");
            }

            // manipula arquivo
            $handle = fopen ($uploadfile,"r");
            $conteudo = array();
            while (($data = fgetcsv($handle, filesize($uploadfile), "\n")) !== FALSE) {

                //gera array separado por linhas e colunas
                $linha = explode(";", $data[0]);
                $conteudo[$linha[0]] = $linha;
            }

            //fecha CSV
            fclose ($handle);

            //remove arquivo do servidor
            unlink($uploadfile);

            return $conteudo;

        } catch (ErrorException $e) {
            $this->view->mensagemErro = $e->getMessage();
            $this->index();
        } catch (Exception $e) {
            $this->view->mensagemAlerta = $e->getMessage();
            $this->index();
        }
    }


    /**
     * Valida se arquivo é CSV
     * @return bool
     */
    private function validaCsv($formato){

        //verificando tipo do arquivo
        $csv_mimetypes = array(
            'text/csv',
            'text/plain',
            'application/csv',
            'text/comma-separated-values',
            'application/excel',
            'application/vnd.ms-excel',
            'application/vnd.msexcel',
            'text/anytext',
            'application/octet-stream',
            'application/txt'
        );

        if(in_array($formato, $csv_mimetypes)) {
            return true;
        }else{
            return false;
        }
    }

     public function setTimeSlot($horario){

        switch ($horario) {
            case '07:00:00':
            case '07:30:00':
            case '08:00:00':
            case '08:30:00':
            case '09:00:00':
            case '09:30:00':
                $timeSlot = '8-10';
                break;
            case '10:00:00':
            case '10:30:00':
            case '11:00:00':
            case '11:30:00':
                $timeSlot = '10-12';
                break;
            case '12:00:00':
            case '12:30:00':
            case '13:00:00':
            case '13:30:00':
                $timeSlot = '12-14';
                break;
            case '14:00:00':
            case '14:30:00':
            case '15:00:00':
            case '15:30:00':
                $timeSlot = '14-16';
                break;
            case '16:00:00':
            case '16:30:00':
            case '17:00:00':
            case '17:30:00':
            case '18:00:00':
            case '18:30:00':
            case '19:00:00':
            case '19:30:00':
            case '20:00:00':
            case '20:30:00':
            case '21:00:00':
            case '21:30:00':
            case '22:00:00':
                $timeSlot = '16-18';
                break;
            default:
                $timeSlot = false;
                break;
        }

        return $timeSlot;
    }

    protected function converterUTF8($array)
    {
        array_walk_recursive($array, function(&$item, $key){
            if(!mb_detect_encoding($item, 'utf-8', true)){
                $item = utf8_encode($item);
            }
        });
        return $array;
    }


    /**
     * Trata os parametros submetidos pelo formulario e popula um objeto com os parametros
     *k
     * @return stdClass Parametros tradados
     * @return stdClass
     */
    private function tratarParametros() {

        $retorno = new stdClass();

        if (count($_GET) > 0) {
            foreach ($_GET as $key => $value) {

                //Verifica se atributo ja existe e nao sobrescreve.
                if (!isset($retorno->$key)) {
                    $retorno->$key = isset($_GET[$key]) ? trim($value) : '';
                }
            }
        }

        if (count($_POST) > 0) {
            foreach ($_POST as $key => $value) {

                if(is_array($value)) {

                    //Tratamento de POST com Arrays
                    foreach ($value as $chave => $valor) {
                        $value[$chave] = trim($valor);
                    }
                    $retorno->$key = isset($_POST[$key]) ? $_POST[$key] : array();

                } else {
                    $retorno->$key = isset($_POST[$key]) ? trim($value) : '';
                }

            }
        }

        if (count($_FILES) > 0) {
            foreach ($_FILES as $key => $value) {

                //Verifica se atributo já existe e não sobrescreve.
                if (!isset($retorno->$key)) {
                    $retorno->$key = isset($_FILES[$key]) ? $value : '';
                }
            }
        }

        return $retorno;
    }


}   