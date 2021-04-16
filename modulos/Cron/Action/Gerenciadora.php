<?php



/** require classe de persistencia **/
require dirname(__FILE__).'/../DAO/GerenciadoraDAO.php';

/*
 * @author	Renato Teixeira Bueno
 * @email	renato.bueno@meta.com.br
 * class Action
 */
class Gerenciadora {
    
    private $dao;
    
    /*
     * @author	Renato Teixeira Bueno
 	 * @email	renato.bueno@meta.com.br
     * Recebe como parametro a string de conexao do bdcentral
     * Construtor
     */
    public function Gerenciadora($conn_bdcentral = null){
        
        global $conn;
        
        $this->dao = new GerenciadoraDAO($conn, $conn_bdcentral);
        
    }
    
    /*
     * @author	Renato Teixeira Bueno
 	 * @email	renato.bueno@meta.com.br
     * Metodo que retorna todos os contratos de testes com um intervalo maior que 2 horas
     */
    public function getContratoTeste(){
        return $this->dao->getContratoTeste();
    }
    
    public function getTestesTecladoByOs($id_os){
        return $this->dao->getTestesTecladoByOs($id_os);
    }
    
    /*
     * @author	Renato Teixeira Bueno
 	 * @email	renato.bueno@meta.com.br
     * Metodo que retorna o veiculo e o numero do contrato vinculados a OS
     */
    public function getVeiculoContratoByOS($id_os){
        return $this->dao->getVeiculoContratoByOS($id_os);
    }
    
    /*
     * @author	Renato Teixeira Bueno
 	 * @email	renato.bueno@meta.com.br
     * Metodo que atualiza a gerenciadora (desvincula) na base bdcentral pelo id do veiculo
     */
    public function atualizaTerceiraGerenciadoraVeiculo($id_terceira_gerenciadora, $id_veiculo){
        
        $id_terceira_gerenciadora = (empty($id_terceira_gerenciadora)) ? 'null' : $id_terceira_gerenciadora;
        
        return $this->dao->atualizaTerceiraGerenciadoraVeiculo($id_terceira_gerenciadora, $id_veiculo);
    }
    
    /*
     * @author	Renato Teixeira Bueno
 	 * @email	renato.bueno@meta.com.br
     * Metodo que atualiza a gerenciadora (desvincula) na base Intranet pelo numero do contrato
     */
    public function atualizaTerceiraGerenciadoraContrato($id_terceira_gerenciadora, $numero_contrato){
        
        $id_terceira_gerenciadora = (empty($id_terceira_gerenciadora)) ? 'null' : $id_terceira_gerenciadora;
        
        return $this->dao->atualizaTerceiraGerenciadoraContrato($id_terceira_gerenciadora, $numero_contrato);
    }
    
    /*
     * @author	Renato Teixeira Bueno
 	 * @email	renato.bueno@meta.com.br
     * Desvincula o teste da gerenciadora
     */
    public function desvinculaTesteDeGerenciadora($id_contrato_teste_instalacao){
        return $this->dao->desvinculaTesteDeGerenciadora($id_contrato_teste_instalacao);
    }
    
}