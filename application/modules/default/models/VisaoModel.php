<?php

/**
 * Description of VisaoModel
 *
 * @author colaborador
 * @todo migrar para o módulo de agentes
 */
class VisaoModel implements ModelInterface
{
    const SITUACAO_ATIVO = 'A';

    const PROPONENTE = 144;
    const INCENTIVADOR = 145;
    const SERVIDOR = 146;
    const DIRIGENTE_DE_INSTITUICAO = 198;
    const PARECERISTA_DE_PROJETO_CULTURAL = 209;
    const COMPONENTE_DA_COMISSAO = 210;
    const TECNICO = 216;
    const VOTANTES_DA_CNIC = 217;
    const PROCURADOR  = 247;
    const FORNECEDOR = 248;
    const SERVIDOR_COMISSIONADO = 266;
    const BENEFICIARIO_DE_PRODUTOS = 199;

    private $table = null;

    public function __construct()
    {
        $this->table = new Visao();
    }

    /**
     *
     * @throws Exception
     */
    public function atualizar()
    {
        throw new Exception('M&eacute;todo n&atilde;o implementado');
    }

    /**
     *
     * @param type $id
     * @throws Exception
     */
    public function buscar($id = null)
    {
        throw new Exception('M&eacute;todo n&atilde;o implementado');
    }

    /**
     *
     * @param type $id
     * @throws Exception
     */
    public function deletar($id)
    {
        throw new Exception('M&eacute;todo n&atilde;o implementado');
    }

    /**
     *
     * @throws Exception
     */
    public function salvar()
    {
        throw new Exception('M&eacute;todo n&atilde;o implementado');
    }

    /**
     *
     * @param type $cpfCnpjAgente
     * @param type $tipoVisao
     */
    public function adicionaVisao($cpfCnpjAgente, $tipoVisao)
    {
        $agentesTable = new Agente_Model_DbTable_Agentes();

        $select = $this->table->select()
          ->setIntegrityCheck(false)
          ->from(array('Visao' => $this->table->info(Zend_Db_Table::NAME)))
          ->joinInner(array('Agentes', $agentesTable->info(Zend_Db_Table::NAME)), 'Agentes.idAgente = Visao.idAgente')
          ->where('Agentes.CNPJCPF = ?', $cpfCnpjAgente)
          ->where('Visao.Visao = ?', $tipoVisao);

        $visaoRow = $this->table->fetchRow($select);

        if (empty($visaoRow)) {
            $agenteRow = $agentesTable->fetchRow($agentesTable->select()->where('CNPJCPF = ?', $cpfCnpjAgente));
            $this->table->inserir(
                array(
                    'idAgente' => $agenteRow->idAgente,
                    'Visao' => $tipoVisao,
                    'Usuario' => Zend_Auth::getInstance()->getIdentity()->usu_codigo,
                    'stAtivo' => self::SITUACAO_ATIVO
                )
            );
        }
    }
}
