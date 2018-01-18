<?php
/**
 * Class Proposta_Model_DbTable_PlanilhaProposta
 *
 * @name Proposta_Model_DbTable_PlanilhaProposta
 * @package Modules/Agente
 * @subpackage Models/DbTable
 *
 * @author Ruy Junior Ferreira Silva <ruyjfs@gmail.com>
 * @author Cleber Santos <oclebersantos@gmail.com>
 * @since 18/10/2016
 * @deprecated esse arquivo deverá ter seus metodos transferidos para Proposta_Model_DbTable_TbPlanilhaProposta
 * @link http://salic.cultura.gov.br
 */
class Proposta_Model_DbTable_PlanilhaProposta extends MinC_Db_Table_Abstract
{

    /**
     * _schema
     *
     * @var string
     * @access protected
     */
    protected $_schema = 'sac';

    /**
     * _name
     *
     * @var bool
     * @access protected
     */
    protected $_name = 'tbPlanilhaProposta';

    /**
     * _primary
     *
     * @var bool
     * @access protected
     */
    protected $_primary = 'idPlanilhaProposta';

//    @todo : esse arquivo deverá ter seus metodos transferidos para Proposta_Model_DbTable_TbPlanilhaProposta e o arquivo apagado
    /*
     * @deprecated Todos os metodos deste arquivo, foram migrados para o arquivo TbPlanilhaPrposta.php (Proposta_Model_DbTable_TbPlanilhaProposta).
     */
    public function somarPlanilhaProposta($idprojeto, $fonte = null, $outras = null, $where = array())
    {
        $somar = $this->select();
        $somar->from(
            $this,
                        array(
                            'sum(Quantidade*Ocorrencia*ValorUnitario) as soma'
                        )
                )
                ->where('idProjeto = ?', $idprojeto)
                ->where('idProduto <> ?', '206');
        if ($fonte) {
            $somar->where('FonteRecurso = ?', $fonte);
        }
        if ($outras) {
            $somar->where('FonteRecurso <> ?', $outras);
        }

        //adiciona quantos filtros foram enviados
        foreach ($where as $coluna => $valor) {
            $somar->where($coluna, $valor);
        }

        return $this->fetchRow($somar);
    }

    public function somarPlanilhaPropostaDivulgacao($idprojeto, $fonte = null, $outras = null)
    {
        $somar = $this->select();
        $somar->from(
            $this,
            array(
                'sum(Quantidade*Ocorrencia*ValorUnitario) as soma'
            )
        )
            ->where('idProjeto = ?', $idprojeto)
            ->where('idEtapa = ?', 3);
        if ($fonte) {
            $somar->where('FonteRecurso = ?', $fonte);
        }
        if ($outras) {
            $somar->where('FonteRecurso <> ?', $outras);
        }
        return $this->fetchRow($somar);
    }

    /*
    * Criado no dia 07/10/2013 - Jefferson Alessandro
    * @deprecated Todos os metodos deste arquivo, foram migrados para o arquivo TbPlanilhaPrposta.php (Proposta_Model_DbTable_TbPlanilhaProposta).
    */
    public function buscarDadosAvaliacaoDeItem($idPlanilhaProposta)
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(
            array('a' => $this->_name),
            array(
                new Zend_Db_Expr(
                    'a.idPlanilhaProposta, a.idProduto, b.Descricao as descProduto, a.idEtapa,
                        c.Descricao as descEtapa, a.idPlanilhaItem, d.Descricao as descItem, a.Unidade, e.Descricao as descUnidade,
                        a.Quantidade, a.Ocorrencia, a.ValorUnitario, a.QtdeDias'
                )
            )
        );
        $select->joinLeft(
            array('b' => 'Produto'),
            "a.idProduto = b.Codigo",
            array(),
            'SAC.dbo'
        );
        $select->joinInner(
            array('c' => 'tbPlanilhaEtapa'),
            "a.idEtapa = c.idPlanilhaEtapa",
            array(),
            'SAC.dbo'
        );
        $select->joinInner(
            array('d' => 'tbPlanilhaItens'),
            "a.idPlanilhaItem = d.idPlanilhaItens",
            array(),
            'SAC.dbo'
        );
        $select->joinInner(
            array('e' => 'tbPlanilhaUnidade'),
            "a.Unidade = e.idUnidade",
            array(),
            'SAC.dbo'
        );
        $select->where('a.idPlanilhaProposta = ?', $idPlanilhaProposta);

        return $this->fetchAll($select);
    }

    /*
    * @deprecated Todos os metodos deste arquivo, foram migrados para o arquivo TbPlanilhaPrposta.php (Proposta_Model_DbTable_TbPlanilhaProposta).
    */
    public function Orcamento($id_projeto)
    {
        // @todo limpar
//        $sql = "SELECT
//                    idPlanilhaProposta,
//                    idProjeto,
//                    idProduto,
//                    idEtapa,
//                    idPlanilhaItem,
//                    Descricao,
//                    Unidade,
//                    Quantidade,
//                    Ocorrencia,
//                    ValorUnitario,
//                    QtdeDias,
//                    TipoDespesa,
//                    TipoPessoa,
//                    Contrapartida,
//                    FonteRecurso,
//                    UfDespesa,
//                    MunicipioDespesa,
//                    idUsuario,
//                    CAST(dsJustificativa AS TEXT) AS dsJustificativa,
//                    (SELECT Descricao FROM SAC.dbo.tbPlanilhaEtapa WHERE idPlanilhaEtapa = P.idEtapa) as Etapa,
//                    (select Descricao from SAC.dbo.tbPlanilhaItens where idPlanilhaItens = P.idPlanilhaItem) as Item,
//                    (select descricao from SAC.dbo.tbPlanilhaUnidade where idUnidade = P.Unidade) as UnidadeF,
//                    (select Descricao from SAC.dbo.Verificacao where idVerificacao=P.FonteRecurso)as FonteRecursoF,
//                    (select descricao from Agentes.dbo.uf where iduf = P.UfDespesa) as UfDespesaF,
//                    (select descricao from agentes.dbo.Municipios where idMunicipioIBGE=P.MunicipioDespesa) as MunicipioDespesaF,
//                    (SELECT Descricao from SAC.dbo.Produto where Codigo = P.idProduto) as ProdutoF
//                FROM
//                    SAC.dbo.tbPlanilhaProposta as P
//                WHERE
//                    idProjeto = ".$id_projeto."
//                ORDER BY
//                    idEtapa,idProduto";

        try {
            $db = Zend_Db_Table::getDefaultAdapter();
            $db->setFetchMode(Zend_DB::FETCH_OBJ);
        } catch (Zend_Exception_Db $e) {
            $this->view->message = $e->getMessage();
        }

        $db= Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);

        $sql = $this->select()
            ->setIntegrityCheck(false)
            ->from(array('p' => $this->_name), $this->_getCols(), $this->_schema);

        $sql->joinLeft(array('e' => 'tbplanilhaetapa'), 'e.idplanilhaetapa = p.idetapa', array( 'etapa' => 'e.descricao'), $this->_schema);

        $sql->joinLeft(array('i' => 'tbplanilhaitens'), 'i.idplanilhaitens = p.idplanilhaitem', array( 'item' => 'i.descricao'), $this->_schema);

        $sql->joinLeft(array('u' => 'tbplanilhaunidade'), 'u.idUnidade = p.unidade', array('unidadef' => 'u.descricao'), $this->_schema);

        $sql->joinLeft(array('v' => 'verificacao'), 'v.idverificacao = p.fonterecurso', array('fonterecursof' => 'v.descricao'), $this->_schema);

        $sql->joinLeft(array('pr' => 'produto'), 'pr.codigo = p.idproduto', array('ProdutoF' => 'pr.descricao'), $this->_schema);

        $sql->joinLeft(array('uf' => 'uf'), 'uf.iduf = p.ufdespesa', array('ufdespesaf' => 'uf.descricao'), $this->getSchema('agentes'));

        $sql->joinLeft(array('m' => 'municipios'), 'm.idmunicipioibge = p.municipiodespesa', array('municipiodespesaf' => 'm.descricao'), $this->getSchema('agentes'));

        $sql->where('p.idprojeto = ?', $id_projeto);
        $sql->order('p.idetapa', 'p.idproduto');

        return $db->fetchAll($sql);
    }

    /*
    * @deprecated Todos os metodos deste arquivo, foram migrados para o arquivo TbPlanilhaPrposta.php (Proposta_Model_DbTable_TbPlanilhaProposta).
    */
    public function buscarCustos(
        $idPreProjeto,
        $tipoCusto,
        $idEtapa = null,
        $idItem = null,
        $idUf = null,
        $idMunicipio = null,
                                        $fonte = null,
        $unidade = null,
        $quantidade = null,
        $ocorrencia = null,
        $vlunitario = null,
        $qtdDias = null,
        $dsJustificativa = null
    ) {
        $tpp = array(
            'tpp.idusuario',
            'tpp.idprojeto as idProposta',
            'tpp.idPlanilhaProposta',
            'tpp.quantidade',
            'tpp.ocorrencia',
            'tpp.valorunitario',
            'tpp.qtdedias',
            'tpp.dsjustificativa as justificativa',
        );

        $tpe = array(
            'tpe.tpcusto as custo',
            'tpe.descricao as etapa',
            'tpe.idplanilhaetapa as idEtapa',
            'tpe.tpcusto',
        );

        $tpi = array(
            'tpi.descricao as Item',
            'tpi.idplanilhaitens as idItem',
        );

        $uf = array(
            'uf.descricao as Uf',
            'uf.sigla as SiglaUF',
            'uf.idUF as idUF',
        );

        $mun = array(
            'municipio.descricao as Municipio',
            'municipio.idMunicipioIBGE as idMunicipio'
        );

        $mec = array(
            'mec.descricao as mecanismo'
        );

        $un = array(
            'un.idunidade as idunidade',
            'un.descricao as unidade',
        );

        $veri = array(
            'veri.idverificacao as idFonteRecurso',
            'veri.descricao as DescricaoFonteRecurso'
        );

        $sql = $this->select()
            ->setIntegrityCheck(false)
            ->from(array('tpp' => 'tbplanilhaproposta'), $tpp, $this->getSchema('sac'))
            ->joinLeft(array('pd' => 'produto'), 'pd.codigo = tpp.idproduto', null, $this->getSchema('sac'))
            ->join(array('tpe' => 'tbplanilhaetapa'), 'tpe.idplanilhaetapa = tpp.idetapa', $tpe, $this->getSchema('sac'))
            ->join(array('tpi' => 'tbplanilhaitens'), 'tpi.idplanilhaitens = tpp.idplanilhaitem', $tpi, $this->getSchema('sac'))
            ->join(array('uf' => 'uf'), 'uf.iduf = tpp.ufdespesa', $uf, $this->getSchema('agentes'))
            ->join(array('municipio' => 'municipios'), 'municipio.idmunicipioibge = tpp.municipiodespesa', $mun, $this->getSchema('agentes'))
            ->join(array('prep' => 'preprojeto'), 'prep.idpreprojeto = tpp.idprojeto', null, $this->getSchema('sac'))
            ->join(array('mec' => 'mecanismo'), 'mec.codigo = prep.mecanismo', 'mec.descricao as mecanismo', $this->getSchema('sac'))
            ->join(array('un' => 'tbplanilhaunidade'), 'un.idunidade = tpp.unidade', 'un.descricao as Unidade', $this->getSchema('sac'))
            ->join(array('veri' => 'verificacao'), 'veri.idverificacao = tpp.fonterecurso', $veri, $this->getSchema('sac'))
            ->where('tpe.tpcusto = ?', $tipoCusto)
            ->where('tpp.idprojeto = ?', $idPreProjeto)
            ->order('tpe.descricao');

        if ($idEtapa) {
            $sql->where('tpe.idPlanilhaEtapa = ?', $idEtapa);
        }
        if ($idItem) {
            $sql->where('tpi.idPlanilhaItens = ?', $idItem);
            ;
        }

        if ($idUf) {
            $sql->where('tpp.UfDespesa = ?', $idUf);
        }

        if ($idMunicipio) {
            $sql->where('tpp.MunicipioDespesa = ?', $idMunicipio);
        }

        if ($fonte) {
            $sql->where('veri.idVerificacao = ?', $fonte);
        }

        if ($unidade) {
            $sql->where('un.idUnidade = ?', $unidade);
        }

        if ($quantidade) {
            $sql->where('tpp.Quantidade = ?', $quantidade);
        }

        if ($ocorrencia) {
            $sql->where('tpp.Ocorrencia = ?', $ocorrencia);
        }

        if ($vlunitario) {
            $sql->where('tpp.ValorUnitario = ?', $vlunitario);
        }

        if ($qtdDias) {
            $sql->where('tpp.QtdeDias = ?', $qtdDias);
        }

        $db= Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);


        return $db->fetchAll($sql);
    }

    /*
    * @deprecated Todos os metodos deste arquivo, foram migrados para o arquivo TbPlanilhaPrposta.php (Proposta_Model_DbTable_TbPlanilhaProposta).
    */
    public function buscarDadosCadastrarCustos($idPreProjeto)
    {
//        $sql = "
//            SELECT TOP 1
//            tpp.idProjeto as idProposta
//            FROM SAC..tbPlanilhaProposta tpp
//            WHERE tpp.idProjeto = $idPreProjeto";

        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(
            array('tpp'=>$this->_name),
            array(
                'idproposta'=>'tpp.idprojeto',
            ),
            $this->_schema
        );
        $select->where('tpp.idprojeto = ?', $idPreProjeto);

        $db= Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);

        return $db->fetchRow($select);
    }

    /*
    * @deprecated Todos os metodos deste arquivo, foram migrados para o arquivo TbPlanilhaPrposta.php (Proposta_Model_DbTable_TbPlanilhaProposta).
    */
    public function buscarItensUfRegionalizacao($idProposta)
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(
                array('tpp'=>$this->_name),
                array(
                    'idproposta'=>'tpp.idprojeto',
                ),
                $this->_schema
            );
        $select->joinInner(array('uf' => 'UF'), 'uf.idUF = tpp.UfDespesa', array('idUF'=>'uf.idUF', 'UF'=>'uf.Sigla'), $this->getSchema('agentes'));
        $select->joinInner(array('mun' => 'Municipios'), 'mun.idMunicipioIBGE = tpp.MunicipioDespesa', array('idMunicipio'=>'mun.idMunicipioIBGE', 'Municipio'=>'mun.Descricao'), $this->getSchema('agentes'));
        $select->where('tpp.idprojeto = ?', $idProposta);
        $select->where('tpp.idEtapa <> ?', '8');
        $select->where("uf.Regiao = 'Sul' OR uf.Regiao = 'Sudeste'");
        $select->order('tpp.idprojeto DESC');
        $select->limit(1);

        $db= Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);
        return $db->fetchRow($select);
    }
}
