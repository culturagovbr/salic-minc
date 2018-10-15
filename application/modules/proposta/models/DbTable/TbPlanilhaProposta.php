<?php

/**
 * Class Proposta_Model_DbTable_TbPlanilhaProposta
 *
 * @name Proposta_Model_DbTable_TbPlanilhaProposta
 * @package Modules/Agente
 * @subpackage Models/DbTable
 *
 * @author Ruy Junior Ferreira Silva <ruyjfs@gmail.com>
 * @since 18/10/2016
 *
 * @link http://salic.cultura.gov.br
 */
class Proposta_Model_DbTable_TbPlanilhaProposta extends MinC_Db_Table_Abstract
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
    protected $_name = 'tbplanilhaproposta';

    /**
     * _primary
     *
     * @var bool
     * @access protected
     */
    protected $_primary = 'idPlanilhaProposta';


    public function salvarNovoProduto($idProposta, $idProduto, $idEtapa, $idItem, $unidade, $quantidade, $ocorrencia, $vlunitario, $qtdDias, $fonte, $idUf, $idMunicipio, $justificativa, $idUsuario)
    {
        try {
            $dados = array(
                'idProjeto'=>$idProposta,
                'idProduto'=>$idProduto,
                'idEtapa'=>$idEtapa,
                'idPlanilhaItem'=>$idItem,
                'Descricao'=>'',
                'Unidade'=>$unidade,
                'Quantidade'=>$quantidade,
                'Ocorrencia'=>$ocorrencia,
                'ValorUnitario'=>$vlunitario,
                'QtdeDias'=>$qtdDias,
                'TipoDespesa'=>0,
                'TipoPessoa'=>0,
                'Contrapartida'=>0,
                'FonteRecurso'=>$fonte,
                'UfDespesa'=>$idUf,
                'MunicipioDespesa'=>$idMunicipio,
                'dsJustificativa'=> substr($justificativa, 0, 450),
                'idUsuario'=>$idUsuario
            );

            return $this->insert($dados);
        } catch (Exception $e) {
            die("ERRO" . $e->getMessage());
        }
    }

    public function buscarDadosEditarProdutos(
        $idPreProjeto = null,
        $idEtapa = null,
        $idProduto = null,
        $idItem = null,
        $idPlanilhaProposta=null,
        $idUf = null,
        $municipio = null,
                                                     $unidade = null,
        $qtd = null,
        $ocorrencia = null,
        $valor = null,
        $qtdDias = null,
        $fonte = null
    ) {
        $db = Zend_Db_Table::getDefaultAdapter();

        $pp = array(
            'pp.idplanilhaproposta as idPlanilhaProposta',
            'pp.idetapa as idEtapa',
            'pp.ufdespesa AS IdUf',
            'pp.municipiodespesa as Municipio',
            'pp.idplanilhaitem AS idItem',
            'pp.fonterecurso as Recurso',
            'pp.quantidade as Quantidade',
            'pp.ocorrencia as Ocorrencia',
            'pp.valorunitario as ValorUnitario',
            'CAST(pp.dsjustificativa AS TEXT) as Justificativa',
            'pp.qtdedias as QtdDias',
            'pp.unidade as Unidade',
            'pp.stCustoPraticado as stCustoPraticado',
        );

        $sacSchema = $this->_schema;
        $sql = $db->select()->from(array('pre' => 'preprojeto'), 'pre.idpreprojeto as idProposta', $sacSchema)
            ->join(array('pp' => 'tbplanilhaproposta'), 'pre.idpreprojeto = pp.idprojeto', $pp, $sacSchema)
            ->join(array('p' => 'produto'), 'pp.idproduto = p.codigo', 'p.codigo AS CodigoProduto', $sacSchema)
            ->join(array('ti' => 'tbplanilhaitens'), 'ti.idplanilhaitens = pp.idplanilhaitem', 'ti.descricao as DescricaoItem', $sacSchema)
            ->join(array('uf' => 'uf'), 'uf.CodUfIbge = pp.ufdespesa', 'uf.descricao AS DescricaoUf', $sacSchema)
            ->join(array('mun' => 'municipios'), 'mun.idmunicipioibge = pp.municipiodespesa', 'mun.descricao as DescricaoMunicipio', $this->getSchema('agentes'))
            ->join(array('pe' => 'tbplanilhaetapa'), 'pp.idetapa = pe.idplanilhaetapa', 'pe.descricao as DescricaoEtapa', $sacSchema)
            ->join(array('rec' => 'verificacao'), 'rec.idverificacao = pp.fonterecurso', 'rec.descricao as DescricaoRecurso', $sacSchema)
            ->join(array('uni' => 'tbplanilhaunidade'), 'uni.idunidade = pp.unidade', 'uni.descricao as DescricaoUnidade', $sacSchema)
//            ->where('pp.idetapa = ?', $idEtapa)
        ;

        if ($idPreProjeto) {
            $sql->where('pre.idpreprojeto = ?', $idPreProjeto);
        }

        if ($idEtapa) {
            $sql->where('pp.idetapa = ?', $idEtapa);
        }

        if ($idProduto) {
            $sql->where('p.codigo = ?', $idProduto);
        }
        if ($idItem) {
            $sql->where('pp.idplanilhaitem = ?', $idItem);
        }

        if ($idPlanilhaProposta) {
            $sql->where('pp.idplanilhaproposta  = ?', $idPlanilhaProposta);
        }

        if ($idUf) {
            $sql->where('pp.ufdespesa = ?', $idUf);
        }

        if ($municipio) {
            $sql->where('pp.municipiodespesa = ?', $municipio);
        }

        if ($unidade) {
            $sql->where('pp.unidade = ?', $unidade);
        }

        if ($qtd) {
            $sql->where('pp.quantidade = ?', $qtd);
        }

        if ($ocorrencia) {
            $sql->where('pp.ocorrencia = ?', $ocorrencia);
        }

        if ($valor) {
            $sql->where('pp.valorunitario = ?', $valor);
        }

        if ($qtdDias) {
            $sql->where('pp.qtdedias = ?', $qtdDias);
        }

        if ($fonte) {
            $sql->where('pp.fonterecurso = ?', $fonte);
        }

        $db->setFetchMode(Zend_DB::FETCH_OBJ);

        return $db->fetchAll($sql);
    }

    public function buscarUltimosDadosCadastrados()
    {
        $db= Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_ASSOC);

        $sql = $db->select()
            ->from($this->_name, '*', $this->_schema)
            ->limit(1)
            ->order('idPlanilhaProposta DESC');

        return $db->fetchAll($sql);
    }

    public function editarPlanilhaProdutos($dados, $where)
    {
        try {
            $this->update($dados, $where);
        } catch (Zend_Exception $e) {
            die("Erro:".$e->getMessage());
        }
    }

    public function buscarDadosCustos($array = array())
    {
        $pp = array(
                    'pp.idEtapa as idEtapa',
                    'pp.idPlanilhaItem AS idItem',
                    'pp.UfDespesa AS IdUf',
                    'pp.MunicipioDespesa as Municipio',
                    'pp.FonteRecurso as Recurso',
                    'pp.Unidade as Unidade',
                    'pp.Quantidade as Quantidade',
                    'pp.Ocorrencia as Ocorrencia',
                    'pp.ValorUnitario as ValorUnitario',
                    'pp.QtdeDias as QtdDias',
                    'CAST(pp.dsJustificativa AS TEXT) as Justificativa'
        );

        $sql = $this->select()
                    ->setIntegrityCheck(false)
                    ->from(array('pre' => 'preprojeto'), array('pre.idpreprojeto'), $this->_schema);

        $sql->joinInner(array('pp' => 'tbPlanilhaProposta'), 'pre.idPreProjeto = pp.idProjeto', $pp, $this->_schema);

        $sql->joinInner(array('ti' => 'tbPlanilhaItens'), 'ti.idPlanilhaItens = pp.idPlanilhaItem', array('ti.Descricao as DescricaoItem'), $this->_schema);

        $sql->joinInner(array('uf' => 'Uf'), 'uf.CodUfIbge = pp.UfDespesa', array('uf.Descricao AS DescricaoUf'), $this->_schema);

        $sql->joinInner(array('mun' => 'Municipios'), 'mun.idMunicipioIBGE = pp.MunicipioDespesa', array('mun.Descricao as DescricaoMunicipio,'), $this->getSchema('agentes'));

        $sql->joinInner(array('pe' => 'tbPlanilhaEtapa'), 'pp.idEtapa = pe.idPlanilhaEtapa', array('pe.Descricao as DescricaoEtapa'), $this->_schema);

        $sql->joinLeft(array('rec' =>'Verificacao'), 'rec.idVerificacao = pp.FonteRecurso', array('rec.Descricao as DescricaoRecurso'), $this->_schema);

        $sql->joinLeft(array('uni' => 'tbPlanilhaUnidade'), 'uni.idUnidade = pp.Unidade', array('uni.Descricao as DescricaoUnidade'), $this->_schema);

        $sql->where('pre.idPreProjeto = ?', $array['idPreProjeto']);
        $sql->where('pp.idEtapa = ?', $array['etapa']);
        $sql->where('pp.idPlanilhaItem = ?', $array['item']);
        $sql->where('pp.idPlanilhaProposta = ?', $array['idPlanilhaProposta']);


//        $sql = "SELECT
//                    pe.Descricao as DescricaoEtapa,
//                    ti.Descricao as DescricaoItem,
//                    uf.Descricao AS DescricaoUf,
//                    mun.Descricao as DescricaoMunicipio,
//                    uni.Descricao as DescricaoUnidade,
//                    rec.Descricao as DescricaoRecurso,
//                    pp.idEtapa as idEtapa,
//                    pp.idPlanilhaItem AS idItem,
//                    pp.UfDespesa AS IdUf,
//                    pp.MunicipioDespesa as Municipio,
//                    pp.FonteRecurso as Recurso,
//                    pp.Unidade as Unidade,
//                    pp.Quantidade as Quantidade,
//                    pp.Ocorrencia as Ocorrencia,
//                    pp.ValorUnitario as ValorUnitario,
//                    pp.QtdeDias as QtdDias,
//                    CAST(pp.dsJustificativa AS TEXT) as Justificativa
//                  FROM SAC.dbo.PreProjeto AS pre
//                        INNER JOIN SAC.dbo.tbPlanilhaProposta pp ON pre.idPreProjeto = pp.idProjeto
//                        INNER JOIN SAC.dbo.tbPlanilhaItens ti ON ti.idPlanilhaItens = pp.idPlanilhaItem
//                        INNER JOIN SAC.dbo.Uf AS uf ON uf.CodUfIbge = pp.UfDespesa
//                        INNER JOIN AGENTES.dbo.Municipios mun ON mun.idMunicipioIBGE = pp.MunicipioDespesa
//                        INNER JOIN SAC.dbo.tbPlanilhaEtapa pe ON pp.idEtapa = pe.idPlanilhaEtapa
//                        LEFT JOIN SAC.dbo.Verificacao rec ON rec.idVerificacao = pp.FonteRecurso
//                        LEFT JOIN SAC.dbo.tbPlanilhaUnidade uni ON uni.idUnidade = pp.Unidade
//                WHERE (pre.idPreProjeto = {$array['idPreProjeto']} and  pp.idEtapa = {$array['etapa']} and pp.idPlanilhaItem = {$array['item']} )
//                       and pp.idPlanilhaProposta = {$array['idPlanilhaProposta']}";


        $db= Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);

        return $db->fetchAll($sql);
    }

    public function buscarDadosCadastrarCustos($idPreProjeto)
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
        $select->where('tpp.idprojeto = ?', $idPreProjeto);

        $db= Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);

        return $db->fetchRow($select);
    }

    public function somarPlanilhaProposta($idprojeto, $fonte = null, $outras = null, $where = array())
    {
        $somar = $this->select();
        $somar->from(
            $this,
            array(
                new Zend_Db_Expr('ROUND(sum(Quantidade*Ocorrencia*ValorUnitario), 2) as soma')
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

    public function somarPlanilhaPropostaPorEtapa($idprojeto, $fonte = null, $outras = null, $where = array())
    {
        $select = $this->select();
        $select->from(
            array('p' => $this->_name),
            array(
                new Zend_Db_Expr('ROUND(sum(Quantidade*Ocorrencia*ValorUnitario), 2) as soma')
            ),
            $this->_schema
        );

        $select->joinInner(
            array('e' => 'tbplanilhaetapa'),
            'e.idPlanilhaEtapa = p.idEtapa',
            array(),
            $this->_schema
        );

        $select->where('idProjeto = ?', $idprojeto);
        $select->where('idProduto <> ?', '206');

        if ($fonte) {
            $select->where('FonteRecurso = ?', $fonte);
        }

        if ($outras) {
            $select->where('FonteRecurso <> ?', $outras);
        }

        foreach ($where as $coluna => $valor) {
            $select->where($coluna, $valor);
        }
        $result = $this->fetchRow($select);

        return $result->soma;
    }

    public function excluirCustosVinculadosERemuneracaoDaPlanilha($idPreProjeto)
    {
        if (empty($idPreProjeto)) {
            return false;
        }

        $where = array(
            'idProjeto = ?' => $idPreProjeto,
            'idEtapa in (?)' => [
                Proposta_Model_TbPlanilhaEtapa::CUSTOS_VINCULADOS,
                Proposta_Model_TbPlanilhaEtapa::CAPTACAO_DE_RECURSOS]
        );

        return $this->delete($where);
    }

    //Criado no dia 07/10/2013 - Jefferson Alessandro
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

    public function Orcamento($id_projeto)
    {
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
            ->joinLeft(array('uf' => 'uf'), 'uf.iduf = tpp.ufdespesa', $uf, $this->getSchema('agentes'))
            ->joinLeft(array('municipio' => 'municipios'), 'municipio.idmunicipioibge = tpp.municipiodespesa', $mun, $this->getSchema('agentes'))
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

    public function buscarPlanilhaCompleta(
        $idPreProjeto = null,
        $idEtapa = null,
        $idProduto = null,
        $idItem = null,
        $idPlanilhaProposta=null,
        $idUf = null,
        $municipio = null,
                                              $unidade = null,
        $qtd = null,
        $ocorrencia = null,
        $valor = null,
        $qtdDias = null,
        $fonte = null
    ) {
        $db = Zend_Db_Table::getDefaultAdapter();

        $pp = array(
            'pp.idplanilhaproposta as idPlanilhaProposta',
            'pp.idetapa as idEtapa',
            'pp.ufdespesa AS IdUf',
            'pp.municipiodespesa as Municipio',
            'pp.idplanilhaitem AS idItem',
            'pp.fonterecurso as Recurso',
            'pp.quantidade as Quantidade',
            'pp.ocorrencia as Ocorrencia',
            'pp.valorunitario as ValorUnitario',
            'CAST(pp.dsjustificativa AS TEXT) as Justificativa',
            'pp.qtdedias as QtdDias',
            'pp.unidade as Unidade',
        );

        $sacSchema = $this->_schema;
        $sql = $db->select()->from(array('pp' => 'tbplanilhaproposta'), $this->_getCols(), $sacSchema)
            ->joinLeft(array('p' => 'produto'), 'pp.idproduto = p.codigo', array('p.codigo AS CodigoProduto', 'p.Descricao as DescricaoProduto'), $sacSchema)
            ->join(array('ti' => 'tbplanilhaitens'), 'ti.idplanilhaitens = pp.idplanilhaitem', 'ti.descricao as DescricaoItem', $sacSchema)
            ->joinLeft(array('uf' => 'uf'), 'uf.CodUfIbge = pp.ufdespesa', 'uf.descricao AS DescricaoUf', $sacSchema)
            ->joinLeft(array('mun' => 'municipios'), 'mun.idmunicipioibge = pp.municipiodespesa', 'mun.descricao as DescricaoMunicipio', $this->getSchema('agentes'))
            ->join(array('pe' => 'tbplanilhaetapa'), 'pp.idetapa = pe.idplanilhaetapa', ['pe.descricao as DescricaoEtapa',' pe.nrOrdenacao as OrdemEtapa'], $sacSchema)
            ->join(array('rec' => 'verificacao'), 'rec.idverificacao = pp.fonterecurso', 'rec.descricao as DescricaoRecurso', $sacSchema)
            ->join(array('uni' => 'tbplanilhaunidade'), 'uni.idunidade = pp.unidade', 'uni.descricao as DescricaoUnidade', $sacSchema)
        ;

        if ($idPreProjeto) {
            $sql->where('pp.idprojeto = ?', $idPreProjeto);
        }

        if ($idEtapa) {
            $sql->where('pp.idetapa = ?', $idEtapa);
        }

        if ($idProduto) {
            $sql->where('p.codigo = ?', $idProduto);
        }
        if ($idItem) {
            $sql->where('pp.idplanilhaitem = ?', $idItem);
        }

        if ($idUf) {
            $sql->where('pp.ufdespesa = ?', $idUf);
        }

        if ($municipio) {
            $sql->where('pp.municipiodespesa = ?', $municipio);
        }

        if ($unidade) {
            $sql->where('pp.unidade = ?', $unidade);
        }

        if ($qtd) {
            $sql->where('pp.quantidade = ?', $qtd);
        }

        if ($ocorrencia) {
            $sql->where('pp.ocorrencia = ?', $ocorrencia);
        }

        if ($valor) {
            $sql->where('pp.valorunitario = ?', $valor);
        }

        if ($qtdDias) {
            $sql->where('pp.qtdedias = ?', $qtdDias);
        }

        if ($fonte) {
            $sql->where('pp.fonterecurso = ?', $fonte);
        }

        $sql->order(array('DescricaoRecurso', 'DescricaoProduto DESC', 'OrdemEtapa ASC', 'DescricaoUf', 'DescricaoItem'));

        $db->setFetchMode(Zend_DB::FETCH_ASSOC);

        return $db->fetchAll($sql);
    }

    public function calcularMedianaItemOrcamento($idProduto, $idUnidade, $idPlanilhaItem, $idUFDespesa, $idMunicipioDespesa)
    {
        if (empty($idPlanilhaItem) or empty($idUnidade)) {
            return false;
        }

        $exec = new Zend_Db_Expr("EXEC SAC.dbo.spCalcularMedianaItemOrcamentario {$idProduto}, {$idPlanilhaItem}, {$idUFDespesa}, {$idMunicipioDespesa}, {$idUnidade}");

        try {
            $db= Zend_Db_Table::getDefaultAdapter();
            $db->setFetchMode(Zend_DB::FETCH_ASSOC);
        } catch (Zend_Exception_Db $e) {
            $this->view->message = $e->getMessage();
        }
        return $db->fetchRow($exec);
    }

    public function obterMunicipioUFdoProdutoPrincipalComMaiorCusto($idPreProjeto) {

        $select = $this->select()->setIntegrityCheck(false);
        $select->from(
            ['a' => $this->_name],
            [
                'a.UfDespesa',
                'a.MunicipioDespesa',
                new Zend_Db_Expr('sum(a.Quantidade * a.Ocorrencia * a.ValorUnitario) as totalMunicipioPorProduto')
            ],
            $this->_schema
        );

        $select->joinInner(
            ['b' => 'PlanoDistribuicaoProduto'],
            'b.idProjeto = a.idProjeto AND b.idProduto = a.idProduto',
            ['b.idProduto'],
            $this->_schema
        );

        $select->where('a.idProjeto = ?', $idPreProjeto)
            ->where('b.stPrincipal = ?', 1)
            ->where('b.idProduto <> 0')
            ->group(['b.idProduto', 'a.UfDespesa', 'a.MunicipioDespesa'])
            ->order('totalMunicipioPorProduto DESC')
            ->limit(1);

        return $this->fetchRow($select);

    }

    public function buscarFontesDeRecursos($idPreProjeto)
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(
            array('tpp'=>$this->_name),
            array(
                'Valor' => new Zend_Db_Expr('sum(tpp.Quantidade*tpp.Ocorrencia*tpp.ValorUnitario)')
            ),
            $this->_schema
        );

        $select->joinInner(array('ver' => 'Verificacao'), 'tpp.FonteRecurso = ver.idVerificacao', array('ver.Descricao'), $this->_schema);
        $select->where('tpp.idProjeto = ?',$idPreProjeto);
        $select->group(['ver.Descricao', 'tpp.idProjeto']);

        $db = Zend_Db_Table::getDefaultAdapter();
        return $db->fetchAll($select);
    }
}
