<?

namespace module\RegistroOnline;

use module\RegistroOnline\RegistrarBoletoDAO as DAO;

    class RegistrarBoletoModel{
    private $dao;

    public function __construct() {
        $this->dao = new DAO();
    }

    /**
     * Método para recuperar os parametros que foram persistidos na base de dados
     *
     * @author Dimitrius Passos<dimitrius.passos@meta.com.br>
     * @version 03/08/2017
     * @param mixed $pcsipcsoid (valor da chave de busca)
     * @param string $pcsioid (tipo da chave de busca ID/DOC)
     * @return Response $response:
     *     mixed $response->pcsidescricao
    */

    public function getParametros($pcsipcsoid, $pcsioid){

        return $this->dao->getParametros($pcsipcsoid, $pcsioid);
    }

    /**
     * Método para recuperar os parametros que foram persistidos na base de dados
     *
     * @author Dimitrius Passos<dimitrius.passos@meta.com.br>
     * @version 03/08/2017
     * @param mixed $clienteID (valor da chave de busca)
     * @param string $tituloID (tipo da chave de busca ID/DOC)
     * @return Response $response:
     *     json $response (retorna o xml com os dados do registro preenchidos)
    */

    public function getDadosRegistro($clienteID, $tituloID){
        return $this->dao->getDadosRegistro($clienteID, $tituloID);
    }

    /**
     * Método para recuperar os parametros que foram persistidos na base de dados
     *
     * @author Dimitrius Passos<dimitrius.passos@meta.com.br>
     * @version 03/08/2017
     * @param mixed $params ( )
     * @return Response $response:
     *     boolean $response (retorna o o boolean se for inserido)
    */

    public function getErroCode($retCode){

        return $this->dao->getErroCode($retCode);
    }

    public function registraTitulo($cResponse, $params, $xmlCreate) {
        
        return $this->dao->registraTitulo($cResponse, $params, $xmlCreate);
    }

    public function updateTitulo($rResponse, $dados) {
        
        return $this->dao->updateTitulo($rResponse, $dados);
    }

    public function consultarTitulo($rResponse) {
        
        return $this->dao->consultarTitulo($rResponse);
    }

    /**
     * STI 86970 1.1 - chama o DAO para retorna o id
     *
     * @author Marcelo.brondani.ext
     * @version 27/08/2017
     * @return int retorna o id
    */
    public function getId_tpetoid() {
        return $this->dao->getId_tpetoid();
    }

    /**
     * STI 86970 1.1 - chama o DAO para executa o update do status em titulo
     *
     * @author Marcelo.brondani.ext
     * @version 27/08/2017
     * @return booleano resultado do update
    */
    public function updateStatusInTitulo($tituloId, $tpetoid) {
        return $this->dao->updateStatusInTitulo($tituloId, $tpetoid);
    }

    /**
     * STI 86970 1.1 - chama o DAO para executa o update do status em titulo retencao
     *
     * @author Marcelo.brondani.ext
     * @version 27/08/2017
     * @return booleano resultado do update
    */
    public function updateStatusInTituloRetencao($tituloId, $tpetoid) {
        return $this->dao->updateStatusInTituloRetencao($tituloId, $tpetoid);
    }

    /**
     * STI 86970 1.1 - chama o DAO para executa o update do status em titulo consolidado
     *
     * @author Marcelo.brondani.ext
     * @version 27/08/2017
     * @return booleano resultado do update
    */
    public function updateStatusInTituloConsolidado($tituloId, $tpetoid) {
        return $this->dao->updateStatusInTituloConsolidado($tituloId, $tpetoid);
    }
    
    /**
    * STI 86970_1 - Responsaver a enviar os dados para chamada de metodo para atualizar nosso numero
    *
    * @author  marcelo.brondani marcelo.brondani@meta.com.br
    * @since 21/08/2017
    * @version 21/08/2017
    * @param  array $dados conjunto de dados com as configuracoes e nosso numero a ser atualizado
    * @return boleano retorna o resultado se a atualizacao ocorreu com sucesso
    */
    public function updateNossoNumero($dados) {
        return $this->dao->updateNossoNumero($dados);
    }

    /**
    * STI 86970_2 - updateAlterFormCobTitoReg - Chamada para o DAO responsavel por alterar a forma de cobranca do titulo
    *
    * @author  marcelo.brondani marcelo.brondani@meta.com.br
    * @since 22/08/2017
    * @version 22/08/2017
    * @param  array $dados conjunto de dados com as configuracoes e nosso tipo de cobranca a ser alterada
    * @return boleano retorna o resultado se a alteracao ocorreu com sucesso
    */
    public function updateAlterFormCobTitoReg($dados) {
        return $this->dao->updateAlterFormCobTitoReg($dados);
    }

}

?>