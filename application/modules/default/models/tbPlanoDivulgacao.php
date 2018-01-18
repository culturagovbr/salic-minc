<?php
/**
 * DAO tbPlanoDivulgacao
 * @author jeffersonassilva@gmail.com - XTI
 * @since 28/03/2014
 * @version 1.0
 * @link http://www.cultura.gov.br
 */

class tbPlanoDivulgacao extends MinC_Db_Table_Abstract
{
    protected $_banco  = "SAC";
    protected $_schema = "SAC";
    protected $_name   = "tbPlanoDivulgacao";

    /*
     * Criada em 03/14
     * @author: Jefferson Alessandro
     */
    public function buscarPlanosDivulgacaoReadequacao($idPronac, $tabela = 'PlanoDeDivulgacao')
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(
            array('a' => 'Projetos'),
            array(
                new Zend_Db_Expr("b.idPlanoDivulgacao, b.idPeca, c.Descricao as Peca, b.idVeiculo, d.Descricao as Veiculo")
            )
        );
        if ($tabela == 'PlanoDeDivulgacao') {
            $select->joinInner(
                array('b' => 'PlanoDeDivulgacao'),
                'a.idProjeto = b.idProjeto AND b.stPlanoDivulgacao = 1',
                array(new Zend_Db_Expr("'N' as tpSolicitacao")),
                'SAC.dbo'
            );
        } else {
            $select->joinInner(
                array('b' => 'tbPlanoDivulgacao'),
                "a.idPronac = b.idPronac AND stAtivo='S'",
                array('b.tpSolicitacao'),
                'SAC.dbo'
            );
        }
        $select->joinLeft(
            array('c' => 'Verificacao'),
            'c.idVerificacao = b.idPeca',
            array(''),
            'SAC.dbo'
        );
        $select->joinLeft(
            array('d' => 'Verificacao'),
            'd.idVerificacao = b.idVeiculo',
            array(''),
            'SAC.dbo'
        );

        $select->where('a.IdPRONAC = ?', $idPronac);

        
        return $this->fetchAll($select);
    }

    public function buscarPlanosDivulgacaoConsolidadoReadequacao($idReadequacao)
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(
            array('a' => 'Projetos'),
            array(
                new Zend_Db_Expr("b.idPlanoDivulgacao, b.idPeca, c.Descricao as Peca, b.idVeiculo, d.Descricao as Veiculo")
            ),
            'SAC.dbo'
        );
        $select->joinInner(
            array('b' => 'tbPlanoDivulgacao'),
            "a.idPronac = b.idPronac",
            array('b.tpSolicitacao','b.tpAnaliseTecnica','b.tpAnaliseComissao'),
            'SAC.dbo'
        );
        $select->joinLeft(
            array('c' => 'Verificacao'),
            'c.idVerificacao = b.idPeca',
            array(''),
            'SAC.dbo'
        );
        $select->joinLeft(
            array('d' => 'Verificacao'),
            'd.idVerificacao = b.idVeiculo',
            array(''),
            'SAC.dbo'
        );

        $select->where('b.idReadequacao = ?', $idReadequacao);

        return $this->fetchAll($select);
    } // fecha m�todo historicoReadequacao()

    public function buscarDadosPlanosDivulgacaoAtual($where = array())
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(
            array('a' => 'PlanoDeDivulgacao'),
            array(
                new Zend_Db_Expr('a.*')
            ),
            'SAC.dbo'
        );

        // adiciona quantos filtros foram enviados
        foreach ($where as $coluna => $valor) :
            $select->where($coluna, $valor);
        endforeach;

        
        return $this->fetchAll($select);
    } // fecha m�todo historicoReadequacao()
}
