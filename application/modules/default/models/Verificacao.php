<?php

class Verificacao extends MinC_Db_Table_Abstract
{
    /**
     * Devolu&ccedil;&atilde;o ao Fundo Nacional de Cultura - FNC
     */
    const DEVOLUCAO_JUDICIAL = 349;
    const DEVOLUCAO_FUNDO_NACIONAL_CULTURA = 350;
    const OUTRAS_DEVOLUCOES_DE_RECURSOS_CAPTADOS = 351;
    const APROVACAO_INICIAL_DO_PROJETO = 626;
    const PROJETO_NORMAL = 610;
    const PROJETO_APROVADO_EM_EDITAIS = 618;
    const PROJETO_COM_CONTRATOS_DE_PATROCINIOS = 619;


    protected $_schema = "sac";
    protected $_name = "verificacao";

    /**
     * @param array $dados
     * @return integer (retorna o &uacute;ltimo id cadastrado)
     */
    public function cadastrarDados($dados)
    {
        return $this->insert($dados);
    }

    public function tipoDiligencia($consulta = array())
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(array($this->_name), array('idVerificacao', 'Descricao'));
//            $select->where('idTipo = ?', 8);
//            $select->where('stEstado = ?', 1);
        foreach ($consulta as $coluna => $valor) {
            $select->where($coluna, $valor);
        }
        
        return $this->fetchAll($select);
    }


    /**
     * Busca por tipo
     * @access public
     * @param array $where (filtros)
     * @param array $order (ordena&ccedil;&atilde;o)
     * @return object
     */
    public function buscarTipos($where = array(), $order = array())
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(
            array('v' => $this->_name),
            array('v.idVerificacao'
            , 'LTRIM(v.Descricao) AS dsVerificacao')
        );
        $select->joinInner(
            array('t' => 'Tipo'),
            'v.idTipo = t.idTipo',
            array('t.idTipo'
            , 't.Descricao AS dsTipo'),
            'SAC.dbo'
        );

        foreach ($where as $coluna => $valor) {
            $select->where($coluna, $valor);
        }

        $select->order($order);

        return $this->fetchAll($select);
    }

    public function combosNatureza($idTipo)
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(
            array($this->_name), // 'v' => $this->_schema . '.' . $this->_name),
            array('idVerificacao', 'Descricao')
        );
        $select->where('idTipo = ?', $idTipo);
        return $this->fetchAll($select);
    }


    public function buscarOrigemRecurso($where = array())
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(
            array($this->_name),
            array('idVerificacao', 'Descricao')
        );

        if ($where) {
            foreach ($where as $coluna => $valor) :
                $select->where($coluna, $valor);
            endforeach;
        }

        return $this->fetchAll($select);
    }
}
