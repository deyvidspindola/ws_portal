<?php
/**
 * Sascar - Sistema Corporativo
 *
 * LICENSE
 *
 * Sascar Tecnologia Automotiva S/A - Todos os Direitos Reservados
 *
 * @author Jorge A. D. kautzmann <jorge.kautzmann@sascar.com.br>
 * @version 29/08/2013
 * @since 29/08/2013
 * @package Core
 * @subpackage Classe core / cadastro de veículos
 * @copyright Copyright (c) Sascar Tecnologia Automotiva S/A (http://www.sascar.com.br)
 */

namespace module\Veiculo;

class VeiculoService{
    
    /**
     * Busca dados de um veículo
     *
     * @author Jorge A. D. kautzmann <jorge.kautzmann@sascar.com.br>
     * @version 20/09/2013
     * @param mixed $valKey (valor da chave de busca)
     * @param string $tpKey (tipo da chave de busca ID=ID/PL=PLACA/RE=RENAVAN/CH=CHASSI)
     * @return Response $response:
     *     mixed $response->dados (Array com dados do veículo=OK /false = falha)
     *     string $response->codigo (código do erro)
     *     string $response->mensagem (mensagem emitida)
    */
    public static function veiculoGetDados($valKey='', $tpKey='ID') {
        $veiculo = new VeiculoController();
        return $veiculo->getDados($valKey, $tpKey);
    }
    
    /**
     * Busca dados do proprietário de um veículo.
     * Caso veiveipoid não for NULO, busca da tabela veiculo_proprietario, senão busca na tabela veiculo.
     *
     * @author Jorge A. D. kautzmann <jorge.kautzmann@sascar.com.br>
     * @version 20/09/2013
     * @param int $veioid (ID do veículo)
     * @return Response $response:
     *     mixed $response->dados (Array com dados do proprietário veículo=OK /false = falha)
     *     string $response->codigo (código do erro)
     *     string $response->mensagem (mensagem emitida)
    */
    public static function veiculoProprietarioGetDados($veioid=0) {
        $veiculo = new VeiculoController();
        return $veiculo->getVeiculoProprietario($veioid);
    }

    /**
     * Grava um registro de veículo
     *
     * @author Jorge A. D. kautzmann <jorge.kautzmann@sascar.com.br>
     * @version 20/09/2013
     * @param int $usuoid (ID do usuario que deletou o registro)
     * @param array $arrayVeiculo (array associativo com dados do veículo)
     *        OBS: campos obrigatórios:
     *         string veiplaca, 
     *         string veino_renavan, 
     *         string veichassi, 
     *         int veimlooid, 
     *         string veicor, 
     *         int veino_ano 
     *        OBS: campos NÃO obrigatórios:
     *         demais campos tabela veículo
     * @return Response $response:
     *     mixed $response->dados ($veioid=OK /false = falha)
     *     string $response->codigo (código do erro)
     *     string $response->mensagem (mensagem emitida)
    */
    public static function veiculoInclui($usuoid=0, $arrayVeiculo=array()) {
        $veiculo = new VeiculoController();
        $arrayVeiculo['veiusuoid'] = (int) $usuoid;
        return $veiculo->veiculoSetDados($arrayVeiculo, 'I');
    }


    /**
     * Exclusão lógica de registro de veículo
     *
     * @author Jorge A. D. kautzmann <jorge.kautzmann@sascar.com.br>
     * @version 25/09/2013
     * @param int $veioid (ID do veículo)
     * @param int $usuoid (ID do usuario que deletou o registro)
     * @return Response $response:
     *     boolean $response->dados (true=OK /false = falha)
     *     string $response->codigo (código do erro)
     *     string $response->mensagem (mensagem emitida)
    */
    public static function veiculoExclui($veioid=0, $usuoid=0) {
        $veiculo = new VeiculoController();
        return $veiculo->veiculoDelete($veioid, $usuoid);
    }

    /**
     * Altera um registro de veículo
     *
     * @author Jorge A. D. kautzmann <jorge.kautzmann@sascar.com.br>
     * @version 20/09/2013
     * @param int $veioid (ID do veículo)
     * @param array $arrayVeiculo (array associativo com dados do veículo)
     *        OBS: campos obrigatórios:
     *         string veiplaca, 
     *         string veino_renavan, 
     *         string veichassi, 
     *         int veimlooid, 
     *         string veicor, 
     *         int veino_ano 
     *        OBS: campos NÃO obrigatórios:
     *         demais campos tabela veículo
     * @return Response $response:
     *     mixed $response->dados ($veioid=OK /false = falha)
     *     string $response->codigo (código do erro)
     *     string $response->mensagem (mensagem emitida)
    */
    public static function veiculoAtualiza($veioid=0, $arrayVeiculo=array()) {
        $veiculo = new VeiculoController();
        $arrayVeiculo['veioid'] = (int) $veioid;
        return $veiculo->veiculoSetDados($arrayVeiculo, 'U');
    }

    /**
     * Grava um registro de proprietário em veiculo_proprietario
     * OBS: atualiza também os dados do proprietário na tabela veiculo
     *
     * @author Jorge A. D. kautzmann <jorge.kautzmann@sascar.com.br>
     * @version 20/09/2013
     * @param int $usuoid (ID do usuario que deletou o registro)
     * @param array $arrayProprietario (array associativo com dados do Proprietario)
     *     campos obrigatórios: 
     *      char(1) veiptipopessoa, 
     *      string veipnome, 
     *      string veipcnpjcpf
     *     campos NÃO obrigatorios de endereço conforme Tabela veiculo_proprietario
     *      string veipcep-> endereço CEP
     *      char(2) veipuf-> endereço UF
     *      string veipcidade -> endereço cidade
     *      string veipbairro -> endereço bairro
     *      string veiplogradouro -> endereço logradouro
     *      int veipnumero -> endereço número
     *      string veipcomplemento -> endereço complemento
     *      string veipfone -> endereço fone
     *     
     * @return Response $response:
     *     mixed $response->dados ($veipoid=OK /false = falha)
     *     string $response->codigo (código do erro)
     *     string $response->mensagem (mensagem emitida)
    */
    public static function veiculoProprietarioInclui($usuoid=0, $arrayProprietario=array()) {
        $veiculo = new VeiculoController();
        $arrayProprietario['veipusuoid_cadastro'] = (int) $usuoid;
        return $veiculo->veiculoProprietarioSetDados($arrayProprietario, 'I');
    }

    /**
     * Exclusão lógica de um registro de proprietário
     *
     * @author Jorge A. D. kautzmann <jorge.kautzmann@sascar.com.br>
     * @version 20/09/2013
     * @param int $veipoid (ID do Proprietario)
     * @param int $usuoid (ID do usuário que excluiu o registro)
     * @return Response $response:
     *     boolean $response->dados (true=OK /false = falha)
     *     string $response->codigo (código do erro)
     *     string $response->mensagem (mensagem emitida)
    */
    public static function veiculoProprietarioExclui($veipoid=0, $usuoid=0) {
        $veiculo = new VeiculoController();
        return $veiculo->veiculoProprietarioDelete($veipoid, $usuoid);
    }

    
   /**
     * Atualiza um registro de proprietário em veiculo_proprietario
     * OBS: atualiza também os dados do proprietário na tabela veiculo
     *
     * @author Jorge A. D. kautzmann <jorge.kautzmann@sascar.com.br>
     * @version 20/09/2013
     * @param int $veipoid (ID do Proprietario)
     * @param array $arrayProprietario (array associativo com dados do Proprietario)
     *     campos obrigatórios: 
     *      char(1) veiptipopessoa, 
     *      string veipnome, 
     *      string veipcnpjcpf
     *     campos NÃO obrigatorios de endereço conforme Tabela veiculo_proprietario
     *      string veipcep-> endereço CEP
     *      char(2) veipuf-> endereço UF
     *      string veipcidade -> endereço cidade
     *      string veipbairro -> endereço bairro
     *      string veiplogradouro -> endereço logradouro
     *      int veipnumero -> endereço número
     *      string veipcomplemento -> endereço complemento
     *      string veipfone -> endereço fone
     *     
     * @return Response $response:
     *     mixed $response->dados ($veipoid=OK /false = falha)
     *     string $response->codigo (código do erro)
     *     string $response->mensagem (mensagem emitida)
    */
    public static function veiculoProprietarioAtualiza($veipoid=0, $arrayProprietario=array()) {
        $veiculo = new VeiculoController();
        $arrayProprietario['veipoid'] = (int) $veipoid;
        return $veiculo->veiculoProprietarioSetDados($arrayProprietario, 'U');
    }
    
        
}