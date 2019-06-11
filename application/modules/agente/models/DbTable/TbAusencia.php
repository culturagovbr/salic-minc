<?php
/**
 * Class Agente_Model_DbTable_TbAusencia
 *
 * @name Agente_Model_DbTable_TbAusencia
 * @package Modules/Agente
 * @subpackage Models/DbTable
 *
 * @author Ruy Junior Ferreira Silva <ruyjfs@gmail.com>
 * @since 05/10/2016
 *
 * @link http://salic.cultura.gov.br
 */
class Agente_Model_DbTable_TbAusencia extends MinC_Db_Table_Abstract
{

    /**
     * _banco
     *
     * @var bool
     * @access protected
     */
    protected $_banco = 'agentes';

    /**
     * _name
     *
     * @var bool
     * @access protected
     */
    protected $_name = 'tbausencia';

    /**
     * _schema
     *
     * @var string
     * @access protected
     */
    protected $_schema = 'agentes';

    public function carregarAusencia($idAgente, $ano, $tipo, $mes)
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(
            array('A'=>$this->_name),
                      array('*', $this->getExpressionToChar('dtinicioausencia') . ' as dtinicio',
                            $this->getExpressionToChar('dtfimausencia') . ' as dtfim',
                            $this->getExpressionDateDiff('dtinicioausencia', 'dtfimausencia') . ' as qtddias'
                      ),
                        $this->_schema
                      );

        $select->joinInner(
                array('n'=>'nomes'),
            'n.idagente = a.idagente',
                array('n.descricao as nome'),
            $this->_schema
        );

        $select->joinInner(
                array('tp'=>'tbtipoausencia'),
            'tp.idtipoausencia = a.idtipoausencia',
                array('tp.nmausencia as tipoausencia'),
            $this->_schema
        );

        $select->joinLeft(
                array('d'=>'tbdocumento'),
            'd.iddocumento = a.iddocumento',
                array('*'),
            $this->getschema('bdcorporativo', true, 'sccorp')
        );

        $select->joinLeft(
                array('ta'=>'tbarquivo'),
            'ta.idarquivo = d.idarquivo',
                array('*'),
            $this->getschema('bdcorporativo', true, 'sccorp')
        );

        $select->joinLeft(
                array('tai'=>'tbarquivoimagem'),
            'tai.idarquivo = ta.idarquivo',
                array('*'),
            $this->getschema('bdcorporativo', true, 'sccorp')
        );
        $select->where('a.idtipoausencia = ?', $tipo);
        if (!empty($idAgente)) {
            $select->where('a.idagente = ?', $idAgente);
        }
        $select->where($this->getExpressionDatePart('dtinicioausencia', 'year') . ' = ?', $ano);
        if (!empty($mes)) {
            $select->where($this->getExpressionDatePart('dtinicioausencia') . ' = ?', $mes);
        }
        $select->order('a.idalteracao');
        $select->order('a.idausencia');
        return $this->fetchAll($select);
    }

    public function BuscarAusenciaPainel($ano)
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(
            array('A'=>$this->_name),
            array(
                '*',
                 new Zend_Db_Expr('CONVERT(CHAR(10), dtInicioAusencia, 103) as dtInicio'),
                 new Zend_Db_Expr('CONVERT(CHAR(10), dtFimAusencia, 103) as dtFim'),
                 new Zend_Db_Expr('DATEDIFF ( DAY , dtInicioAusencia , dtFimAusencia ) as qtdDias')
            ),
            $this->_schema
          );

        $select->joinInner(
                array('N'=>'Nomes'),
            'N.idAgente = A.idAgente',
                array('N.Descricao as Nome'),
            $this->_schema
        );

        $select->joinInner(
                array('TP'=>'tbTipoAusencia'),
            'TP.idTipoAusencia = A.idTipoAusencia',
                array('TP.nmAusencia as tipoAusencia'),
            $this->_schema
        );

        $select->joinLeft(
                array('D'=>'tbDocumento'),
            'D.idDocumento = A.idDocumento',
                array('*'),
            $this->getSchema('bdcorporativo', true, 'sccorp')
        );

        $select->joinLeft(
                array('TA'=>'tbArquivo'),
            'TA.idArquivo = D.idArquivo',
                array('*'),
            $this->getSchema('bdcorporativo', true, 'sccorp')
        );

        $select->joinLeft(
                array('TAI'=>'tbArquivoImagem'),
            'TAI.idArquivo = TA.idArquivo',
                array('*'),
            $this->getSchema('bdcorporativo', true, 'sccorp')
        );

        $select->where('A.idTipoAusencia = ?', 2);

        $select->where('A.siAusencia = ?', 0);

        $select->where('YEAR(dtInicioAusencia) = ?', $ano);

        $select->order('A.idAlteracao');
        $select->order('A.idAusencia');


        return $this->fetchAll($select);
    }



    public function UltimoRegistro()
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(
            array('A'=>$this->_name),
                        array('MAX(idAusencia) as id')
        );

        return $this->fetchAll($select);
    }

    public function BuscarAusenciaRepetida($idAgente, $dtInicio, $dtFim)
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(
            array('A'=>$this->_name),
            array(
                '*',
                new Zend_Db_Expr('CONVERT(CHAR(10), dtInicioAusencia, 103) as dtInicio'),
                new Zend_Db_Expr('CONVERT(CHAR(10), dtFimAusencia, 103) as dtFim'),
                new Zend_Db_Expr('DATEDIFF ( DAY , dtInicioAusencia , dtFimAusencia ) as qtdDias')
            )
          );

        $select->joinInner(
                array('TP'=>'tbTipoAusencia'),
            'TP.idTipoAusencia = A.idTipoAusencia',
                array('TP.nmAusencia as tipoAusencia')
        );


        $select->where('A.idAgente = ?', $idAgente);
        $select->where("'{$dtInicio}' BETWEEN dtInicioAusencia AND dtFimAusencia");
        $select->orWhere('A.idAgente = ?', $idAgente);
        $select->where("'{$dtFim}' BETWEEN dtInicioAusencia AND dtFimAusencia");

        $select->order('A.siAusencia');

        return $this->fetchAll($select);
    }

    /**
     * @todo remover nao sera utilizado na nova versao do parecer
     */
    public function BuscarAusenciaAtiva($idAgente, $dtAtual, $tipoAusencia)
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(
            array('A'=>$this->_name),
                      array('*')
                      );

        $select->joinInner(
                array('TP'=>'tbTipoAusencia'),
            'TP.idTipoAusencia = A.idTipoAusencia',
                array('TP.nmAusencia as tipoAusencia')
        );

        $select->where('A.idAgente = ?', $idAgente);
        $select->where('A.idTipoAusencia = ?', $tipoAusencia);
        if ($tipoAusencia == 1) {
            $select->where('A.dtInicioAusencia >= ?', new Zend_Db_Expr('DATEADD(DAY, -10, GETDATE())'));
        } else {
            $select->where('A.dtInicioAusencia <= ?', new Zend_Db_Expr('GETDATE()'));
        }

        $select->where('A.dtFimAusencia >= ?', new Zend_Db_Expr('GETDATE()'));
        $select->where('A.siAusencia = ?', 1);

        return $this->fetchAll($select);
    }


    public function inserirAusencia($dados)
    {
        $insert = $this->insert($dados);
        return $insert;
    }

    public function alteraAusencia($dados, $idAusencia)
    {
        $where = "idAusencia = " . $idAusencia;

        return $this->update($dados, $where);
    }
}
