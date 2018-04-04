<?php
/**
 * DAO tbPlanilhaEtapa
 * @author jeffersonassilva@gmail.com - XTI
 * @since 07/03/2014
 * @version 1.0
 * @package application
 * @subpackage application.model
 * @copyright � 2011 - Minist�rio da Cultura - Todos os direitos reservados.
 * @link http://www.cultura.gov.br
 */

class Proposta_Model_DbTable_TbPlanilhaEtapa extends MinC_Db_Table_Abstract
{
    protected $_schema = 'sac';
    protected $_name   = 'tbplanilhaetapa';
    protected $_primary = 'idPlanilhaEtapa';

    public function listarEtapasProdutos($idPreProjeto)
    {
        $db = Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);

        $sql = $db->select()
            ->from(array('tbplanilhaetapa'), array('idPlanilhaEtapa as idEtapa', 'Descricao as DescricaoEtapa'), $this->getSchema('sac'))
            ->where("tpCusto = 'P'")
            ->where("stEstado = 1")
            ->order("idPlanilhaEtapa ASC")
        ;

        //$sql = " SELECT idPlanilhaEtapa as idEtapa, Descricao as DescricaoEtapa FROM SAC.dbo.tbPlanilhaEtapa WHERE tpCusto = 'P' ";

        return $db->fetchAll($sql);
    }

    public function listarEtapas()
    {
        $db= Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);

        $sql = $db->select()
            ->from(
                array($this->_name),
                array('idplanilhaetapa as codetapa', 'descricao as Etapa'),
                $this->_schema
            )
            ->where('stEstado = ?', 1)
            ->where('tpCusto = ?', 'P')
            ->where('idplanilhaetapa <> ?', 9) # assessoria juridica
        ;

        return $db->fetchAll($sql);
    }

    public function buscarEtapas($tipoEtapa, $fetchMode = Zend_DB::FETCH_OBJ)
    {
        $db = Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode($fetchMode);

        $sql = $db->select()
            ->from(array('tbplanilhaetapa'), array('idPlanilhaEtapa as idEtapa', 'Descricao as DescricaoEtapa', 'tpGrupo'), $this->getSchema('sac'))
            ->where("tpCusto = ?", $tipoEtapa)
            ->where("stEstado = 1")
            ->order("nrOrdenacao ASC")
        ;

        return $db->fetchAll($sql);
    }

    public function buscarEtapasCadastrarProdutos()
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(
            array($this->_name),
            array(
                'idPlanilhaEtapa',
                'Descricao'
            ),
            $this->_schema
        );
        $select->where('tpCusto = ?', 'P');
        $select->order('Descricao');
//        $sql.= " ORDER BY Descricao ";

        try {
            $db = Zend_Db_Table::getDefaultAdapter();
            $db->setFetchMode(Zend_DB::FETCH_OBJ);
        } catch (Zend_Exception_Db $e) {
            $this->view->message = "Erro ao buscar Etapas: " . $e->getMessage();
        }
        //die($sql);
        return $db->fetchAll($select);
    }

    public function buscarEtapasCusto()
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(
            array($this->_name),
            array(
                'idPlanilhaEtapa',
                'Descricao'
            ),
            $this->_schema
        );
        $select->where('tpcusto = ?', 'A');
        $select->order('Descricao');

        try {
            $db = Zend_Db_Table::getDefaultAdapter();
            $db->setFetchMode(Zend_DB::FETCH_OBJ);
        } catch (Zend_Exception_Db $e) {
            $this->view->message = "Erro ao buscar Etapas: " . $e->getMessage();
        }

        return $db->fetchAll($select);
    }

    public function listarCustosAdministrativos()
    {

//        $sql = $db->select()
//            ->from('tbplanilhaetapa', ['idplanilhaetapa as idEtapa', 'Descricao as DescricaoEtapa'], $this->getSchema('sac'))
//            ->where("tpCusto = 'A' AND idPlanilhaEtapa <> 6")
//        ;

        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(
            array('pet'=>$this->_name),
            array(
                'idplanilhaetapa as idetapa',
                'Descricao as descricaoEtapa'
            ),
            $this->_schema
        );
        $select->where('tpcusto = ?', 'A');
        $select->where('idPlanilhaEtapa != ? ', '6');

        $db = Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);
        return $db->fetchAll($select);
    }

    public function listarItensCustosAdministrativos($idPreProjeto, $tipoCusto)
    {
        $db = Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);

        $tpp = array(
            'tpp.idusuario',
            'tpp.idprojeto as idProposta',
            'tpp.idPlanilhaProposta',
            'tpp.Quantidade',
            'tpp.ocorrencia',
            'tpp.valorunitario',
            'tpp.qtdedias',
        );

        $tpe = array(
            'tpe.tpcusto as custo',
            'tpe.descricao as etapa',
            'tpe.idPlanilhaEtapa as idEtapa',
            'tpe.tpcusto',
        );

        $tpi = array(
            'tpi.descricao as DescricaoItem',
            'tpi.idplanilhaitens as idItem',
        );

        $uf = array(
            'uf.descricao as DescricaoUf',
            'uf.sigla as SiglaUF',
        );

        $veri = array(
            'veri.idverificacao as idFonteRecurso',
            'veri.descricao as DescricaoFonteRecurso'
        );

        $sql = $db->select()
            ->from(array('tpp' => 'tbplanilhaproposta'), $tpp, $this->getSchema('sac'))
            ->joinLeft(array('pd' => 'produto'), 'pd.codigo = tpp.idproduto', null, $this->getSchema('sac'))
            ->join(array('tpe' => 'tbplanilhaetapa'), 'tpe.idPlanilhaEtapa = tpp.idetapa', $tpe, $this->getSchema('sac'))
            ->join(array('tpi' => 'tbplanilhaitens'), 'tpi.idplanilhaitens = tpp.idplanilhaitem', $tpi, $this->getSchema('sac'))
            ->joinLeft(array('uf' => 'uf'), 'uf.iduf = tpp.ufdespesa', $uf, $this->getSchema('agentes'))
            ->joinLeft(array('municipio' => 'municipios'), 'municipio.idmunicipioibge = tpp.municipiodespesa', 'municipio.descricao as Municipio', $this->getSchema('agentes'))
            ->join(array('prep' => 'preprojeto'), 'prep.idpreprojeto = tpp.idprojeto', null, $this->getSchema('sac'))
            ->join(array('mec' => 'mecanismo'), 'mec.codigo = prep.mecanismo', 'mec.descricao as mecanismo', $this->getSchema('sac'))
            ->join(array('un' => 'tbplanilhaunidade'), 'un.idunidade = tpp.unidade', 'un.descricao as Unidade', $this->getSchema('sac'))
            ->join(array('veri' => 'verificacao'), 'veri.idverificacao = tpp.fonterecurso', $veri, $this->getSchema('sac'))
            ->where('tpe.tpcusto = ?', $tipoCusto)
            ->where('tpp.idprojeto = ?', $idPreProjeto)
            ->order('tpe.descricao');
//        echo '<pre>';
//        echo($sql->assemble());
//        exit;
        return $db->fetchAll($sql);
    }



    public function listarDadosCadastrarCustos($idPreProjeto)
    {
        $db= Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);
        $sql = $db->select()
            ->from(array('tpp' => 'tbplanilhaproposta'), 'tpp.idprojeto as idProposta', $this->getSchema('sac'))
            ->where('tpp.idProjeto = ?', $idPreProjeto)
            ->limit(1)
        ;

        return $db->fetchAll($sql);
    }

    public function listarEtapasCusto()
    {
        $db = Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);

        $sql = $db->select()
            ->from(array('tbplanilhaetapa'), array('idPlanilhaEtapa', 'descricao'), $this->getSchema('sac'))
            ->where("tpcusto = 'A'")
            ->order('descricao')
        ;

        //$sql = "SELECT
        //idPlanilhaEtapa ,
        //Descricao
        //FROM SAC..tbPlanilhaEtapa where tpcusto = 'A'";

        //$sql.= " ORDER BY Descricao ";

        try {
        } catch (Zend_Exception_Db $e) {
            $this->view->message = "Erro ao buscar Etapas: " . $e->getMessage();
        }

        return $db->fetchAll($sql);
    }
} // fecha class
