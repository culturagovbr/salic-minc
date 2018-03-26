<?php

class HistoricoSituacao extends MinC_Db_Table_Abstract
{
    protected $_banco = "SAC";
    protected $_schema = "SAC";
    protected $_name = "HistoricoSituacao";

    /**
     * M&eacute;todo para buscar a situa&ccedil;&atilde;o anterior de um projeto
     * @access public
     * @param string $pronac
     * @return array
     */
    public function inserirHistoricoSituacao($dados)
    {
        try {
            $inserir = $this->insert($dados);
            return $inserir;
        } catch (Zend_Db_Table_Exception $e) {
            return 'Class:Aprovacao Method: inserirAprovacao -> Erro: ' . $e->__toString();
        }
    }

    public function buscarSituacaoAnterior($pronac = null)
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from($this->_name);

        if (!empty($pronac)) {
            $select->where("(AnoProjeto+Sequencial) = ?", $pronac);
        }

        $select->order("Contador DESC");

        return $this->fetchRow($select);
    }

    public function cadastrarDados($dados)
    {
        return $this->insert($dados);
    }

    public function buscarHistoricosEncaminhamento($where = array(), $order = array(), $tamanho = -1, $inicio = -1, $qtdeTotal = false)
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(
            array('h' => $this->_name),
            array(
                new Zend_Db_Expr(
                    'h.Contador, h.AnoProjeto+h.Sequencial as Pronac, h.DtSituacao, h.Situacao,
                     h.ProvidenciaTomada, u.usu_identificacao as cnpjcpf, u.usu_nome as usuario'
                )
            )
        );

        $select->joinInner(
            array('u' => 'Usuarios'),
            'u.usu_codigo = h.Logon',
            array(),
            'TABELAS.dbo'
        );

        foreach ($where as $coluna => $valor) {
            $select->where($coluna, $valor);
        }

        if ($qtdeTotal) {
            return $this->fetchAll($select)->count();
        }

        $select->order($order);
        if ($tamanho > -1) {
            $tmpInicio = 0;
            if ($inicio > -1) {
                $tmpInicio = $inicio;
            }
            $select->limit($tamanho, $tmpInicio);
        }

        return $this->fetchAll($select);
    }

    public function buscarHistoricosEncaminhamentoIdPronac($where = array(), $order = array(), $tamanho = -1, $inicio = -1, $qtdeTotal = false)
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(
            array('h' => $this->_name),
            array(
                new Zend_Db_Expr(
                    'h.Contador, h.AnoProjeto+h.Sequencial as Pronac, h.DtSituacao, h.Situacao,
                            CAST(h.ProvidenciaTomada AS TEXT) as ProvidenciaTomada, u.usu_identificacao as cnpjcpf, u.usu_nome as usuario'
                )
            )
        );

        $select->joinLeft(
            array('u' => 'Usuarios'),
            'u.usu_codigo = h.Logon',
            array(),
            'TABELAS.dbo'
        );

        $select->joinInner(
            array('p' => 'Projetos'),
            'h.AnoProjeto = p.AnoProjeto AND h.Sequencial = p.Sequencial',
            array(),
            'SAC.dbo'
        );

        foreach ($where as $coluna => $valor) {
            $select->where($coluna, $valor);
        }

        if ($qtdeTotal) {
            return $this->fetchAll($select)->count();
        }

        $select->order($order);

        if ($tamanho > -1) {
            $tmpInicio = 0;
            if ($inicio > -1) {
                $tmpInicio = $inicio;
            }
            $select->limit($tamanho, $tmpInicio);
        }

        return $this->fetchAll($select);
    }
}
