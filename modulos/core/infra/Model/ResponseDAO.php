<?php
/**
 * Sascar - Sistema Corporativo
 *
 * LICENSE
 *
 * Sascar Tecnologia Automotiva S/A - Todos os Direitos Reservados
 *
 * @version 11/11/2013
 * @since 11/11/2013
 * @package Core
 * @subpackage Superclasse Model de Acesso a Dados (Response)
 * @copyright Copyright (c) Sascar Tecnologia Automotiva S/A (http://www.sascar.com.br)
 */
namespace infra;

use infra\ComumDAO;

class ResponseDAO extends ComumDAO{
	
    public function getMessage($codigo='0') {
    	$vMessage = array(
    	        '0' => 'Ação executada com sucesso.',
    			'CBR001' => 'Não foi possível registrar o título',
    			'CBR002' => 'Ocorreu um erro durante o preenchimento do boleto seco',
    			'CBR003' => 'Ocorreu um erro durante a criação do boleto em arquivo pdf',
    			'CBR004' => 'Ocorreu um erro durante o cancelamento do título. Verifique os dados e tente novamente',
    			'CLI001' => 'Não foi possível inserir os dados do cliente.',
    			'CLI002' => 'Não foi possível atualizar os dados do cliente.',
    			'CLI003' => 'Não foi possível inserir os dados de endereço do cliente.',
    			'CLI004' => 'Não foi possível atualizar os dados de endereço do cliente.',
    			'CLI005' => 'Não foi possível excluir o cliente.',
                'CLI006' => 'Cliente já existe na base de dados.',
    	        'CTT001' => 'Contrato criado com sucesso.', 
    	        'CTT002' => 'Não foi possível criar o contrato.',
    	        'CTT003' => 'Dados da proposta não localizados.',
    	        'CTT004' => 'Erro ao migrar dados da proposta: fase 1. Contrato NÃO foi gerado com sucesso!',
    	        'CTT005' => 'Contrato(s) gerado(s) com sucesso.',
    	        'CTT006' => 'Proposta não contém itens.',
    	        'CTT007' => 'Erro ao migrar servicos.',
    	        'CTT008' => 'Erro ao migrar Contaos do Cliente.',
    	        'CTT009' => 'Erro ao migrar dados de pagamento.',
    	        'CTT010' => 'Erro ao migrar benefícios de assistência.',
    	        'CTT011' => 'Erro ao migrar Pacote de benefícios.',
    	        'CTT012' => 'Erro ao migrar dados de comissão.',
    	        'CTT013' => 'Erro ao migrar dados de Zona e Região comercial.',
    	        'CTT014' => 'Erro ao migrar dados de Gerenciadoras.',
    	        'CTT015' => 'Erro ao migrar dados de Vencimento Cliente.',
    	        'CTT016' => 'Erro ao migrar dados de Telemetria.',
    	        'CTT017' => 'Erro ao migrar dados da proposta: fase 2. Contrato NÃO foi gerado com sucesso!',
    	        'CTT051' => 'Não foi encontrada uma proposta vinculada a este contrato.',
    	        'INF001' => 'Chave de busca inválida.', 
    	        'INF002' => 'Não há registros.', 
    	        'INF003' => 'Está faltando um ou mais campos obrigatórios.',
    			'INF004' => 'Operação inválida', 
    	        'INF005' => 'Verifique os parâmetros obrigatórios.', 
    	        'INF006' => 'Parâmetro esperado é inválido.',
                'INF007' => 'Não foi possível recuperar as informações: PARAMETROS_CONFIGURACOES_SISTEMAS, entre em contato com o Administrador do sistema.',
                'INF008' => 'Não foi possível recuperar as informações: COD_MOVIMENTO_PERMITE_ATERACAO, entre em contato com o Administrador do sistema.',
    			'INF009' => 'Não foi possível recuperar as informações: FORMAS_COBRANCA_PARA_REGISTRO, entre em contato com o Administrador do sistema.',
    			'PRP001' => 'Proposta criada com sucesso.', 
    	        'PRP002' => 'Não foi possível criar a proposta.',
    			'PRP003' => 'Proposta Histórico gravado com sucesso.', 
    	        'PRP004' => 'Não foi possível gravar Proposta Histórico.',
    			'PRP005' => 'Proposta atualizada com sucesso.', 
    	        'PRP006' => 'Não foi possível atualizar a proposta.',
    			'PRP007' => 'Proposta existente.', 
    	        'PRP008' => 'Essa proposta não existe.',
    			'PRP009' => 'Item da proposta criado com sucesso.', 
    	        'PRP010' => 'Não foi possível criar o item da proposta.',
    			'PRP011' => 'Item da proposta atualizado com sucesso.', 
    	        'PRP012' => 'Não foi possível atualizar o item da proposta.',
    			'PRP013' => 'Cliente vinculado a proposta com sucesso.', 
    	        'PRP014' => 'Não foi possível vincular o cliente a proposta.',
    			'PRP015' => 'Os dados de pagamento forão vinculados a proposta.', 
    	        'PRP016' => 'Não foi possível vincular os dados de pagamento a proposta.',
    			'PRP017' => 'Proposta Serviço excluída com sucesso.', 
    	        'PRP018' => 'Não foi possível excluir a proposta serviço.',
    			'PRP019' => 'Não é possível excluir uma proposta serviço do tipo Básico.', 
    	        'PRP020' => 'Não foi possível incluir um acessório na proposta.',
    			'PRP021' => 'Acessório(s) incluído(s) com sucesso.', 
    	        'PRP022' => 'Acessório da proposta excluído com sucesso.',
    			'PRP023' => 'Não foi possível excluir o acessório da proposta.', 
    	        'PRP024' => 'Não foi possível incluir as informações na Proposta Comissão.',
    			'PRP025' => 'Não é possível incluir uma nova gerenciadora, limite máximo atingido', 
    	        'PRP026' => 'Gerenciadora incluída com sucesso.',
    			'PRP027' => 'Não foi possível incluir a gerenciadora.', 
    	        'PRP028' => 'Gerenciadora excluída com sucesso.',
    			'PRP029' => 'Não foi possível excluir a gerenciadora.',	
    	        'PRP030' => 'Contato incluído com sucesso.',
    			'PRP031' => 'Não foi possível incluir o contato.', 
    	        'PRP032' => 'Contato excluído com sucesso.',
    			'PRP033' => 'Não foi possível excluir o contato.', 
    	        'PRP034' => 'Status da proposta alterado com sucesso.',
    			'PRP035' => 'Não foi possível alterar o status da proposta.', 
    	        'PRP036' => 'Não foi possível incluir o Opcional na proposta.',
    			'PRP037' => 'Opcional vinculado na proposta com sucesso.', 
    	        'PRP038' => 'Opcional excluído com sucesso.',
    			'PRP039' => 'Não foi possível excluir o Opcional da proposta.',
                'PRP040' => 'Classe/Produto inexistente ou desativada.',
                'PRP041' => 'Não foi possível vincular o número de série a proposta.',
                'PRP042' => 'Item da proposta excluído com sucesso.', 
                'PRP043' => 'Não foi possível excluir o item da proposta.',
                'PRP044' => 'Itens da proposta excluídos com sucesso.', 
                'PRP045' => 'Não foi possível excluir os itens da proposta.',
    	        'PEP001' => 'Proposta não possui cliente.',
    			'PEP002' => 'Proposta não possui itens.',
    			'PEP003' => 'Proposta não possui classe do equipamento.',
    			'PEP004' => 'Proposta não possui forma de pagamento.',
    			'PEP005' => 'Proposta não possui informações de valores.',
    			'PEP006' => 'O status financeiro da proposta não permite gerar contrato.',
    			'VEI001' => 'Veículo não localizado.', 
    	        'VEI002' => 'Não foi possível cadastrar o veículo.', 
    	        'VEI003' => 'Não foi possível atualizar o veículo',
    			'VEI004' => 'Não foi possível cadastrar os dados do proprietário do veículo.', 
    	        'VEI005' => 'Não foi possível atualizar os dados do proprietário do veículo.',
    			'VEI006' => 'Proprietário do veículo excluído com sucesso.', 
    	        'VEI007' => 'Não foi possível excluir o proprietário do veículo.',
    			'VEI008' => 'Veículo excluído com sucesso.', 
    	        'VEI009' => 'Não foi possível excluir o veículo.',
    			'ORD010' => 'Não foi possível cadastrar o item de ordem de serviço.',
    			
    			'TAX001' => 'Não foi possível armazenar o título.',
    			'TAX002' => 'Não foi possível armazenar o nosso número do título.',
    			'TAX003' => 'Não foi possível armazenar o título na controle de envio.',
    			'TAX004' => 'Não foi possível gerar o arquivo referente ao título.',
    			'TAX004' => 'Não foi possível gerar o arquivo referente ao título.',
    			'TAX005' => 'Não foi possível armazenar os dados de pagamento.',
    			'TAX006' => 'Não foi possível realizar o pagamento do título por cartão de crédito.',
                'TAX007' => 'Verifique os parâmetros obrigatórios para recuperar os dados bancários.',
                'TAX008' => 'O retorno dos dados bancários está vazio, entre em contato com o administrador do sistema.',
    			);
    	
    	return $vMessage[$codigo];
    }
}