<?php

/**
 * Description of ItemCustoxComprovantePagamento
 *
 * @author 01610881125
 */
class ComprovantePagamentoxPlanilhaAprovacao extends MinC_Db_Table_Abstract
{
    protected $_name    = 'tbComprovantePagamentoxPlanilhaAprovacao';
    protected $_schema  = 'bdcorporativo.scSAC';

    public function inserirItemCustoxComprovantePagamento($data)
    {
        $insert = $this->insert($data);
        return $insert;
    }

    public function alterarItemCustoxComprovantePagamento($data, $where)
    {
        $update = $this->update($data, $where);
        return $update;
    }

    public function deletarItemCustoxComprovantePagamento($where)
    {
        $delete = $this->delete($where);
        return $delete;
    }

    public function valorTotalPorItem($idPlanilhaAprovacao)
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(
                        array('cpa'=>$this->_name),
                        array(
                                'Total'=>new Zend_Db_Expr('sum(cpa.vlComprovado)'),
                              )
                      );

        $select->where('cpa.idPlanilhaAprovacao = ?', $idPlanilhaAprovacao);

        return $this->fetchAll($select);
    }

    public function valorComprovadoItem($retornaSelect = false)
    {
        $select = $this->select()->setIntegrityCheck(false);
        $select->from(
            array('cpxpa' => $this->_name),
            array(
                'vlComprovado' => new Zend_Db_Expr('sum(cpxpa.vlComprovado)'),
                'idPlanilhaAprovacao'
            ),
            $this->_schema
        );
        $select->where('stItemAvaliado = ?', 1);
        $select->group('idPlanilhaAprovacao');

        if ($retornaSelect) {
            return $select;
        } else {
            return $this->fetchAll($select);
        }
    }

    /**
     * Validacao do valor a ser comprovado, verifica o valor aprovado - total ja aprovado
     * identificando o valor m�ximo permitido para comprova��o
     */
    public function validarValorComprovado($idPronac, $idPlanilhaAprovacao, $idPlanilhaItem, $vlComprovado)
    {
        $planilhaAprovacaoModel = new PlanilhaAprovacao();
        $planilhaItem = $planilhaAprovacaoModel->buscar(array('idPlanilhaAprovacao = ?' => $idPlanilhaAprovacao))->current();
        $valorAprovado = $planilhaItem->qtItem * $planilhaItem->nrOcorrencia * $planilhaItem->vlUnitario;

        $comprovantesPagamento = $planilhaAprovacaoModel->buscarcomprovantepagamento($idPronac, $idPlanilhaItem);
        $totalComprovado = 0;
        foreach ($comprovantesPagamento as $comprovante) {
            if (2 == $comprovante->stItemAvaliado) {
                $totalComprovado += $comprovante->vlComprovadoPlanilhaAprovacao;
            }
        }
        if ($valorAprovado < ($totalComprovado + $vlComprovado)) {
            throw new Exception('Comprova��o de pagamento do item acima do valor aprovado.');
        }
    }
}
