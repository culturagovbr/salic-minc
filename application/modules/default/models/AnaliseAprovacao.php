<?php

/**
 * Description of AnaliseAprovacao
 *
 * @author augusto
 */
class AnaliseAprovacao extends MinC_Db_Table_Abstract
{
    protected $_banco = 'SAC';
    protected $_schema = 'SAC';
    protected $_name = 'tbAnaliseAprovacao';

    public function inserirAnaliseAprovacao($data)
    {
        try {
            $inserir = $this->insert($data);
            return $inserir;
        } catch (Zend_Db_Table_Exception $e) {
            return 'AnaliseAprovacao -> inserirAnaliseAprovacao. Erro:' . $e->getMessage();
        }
    }

    public function buscarAnaliseProduto($tpanalise, $idpronac, $order=array(), $where=array(), $tamanho=-1, $inicio=-1, $count=false)
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(
                array('aa' => $this->_name),
                array(
                    'aa.idAnaliseAprovacao',
                    'aa.idProduto',
                    'aa.stLei8313',
                    'aa.stArtigo3',
                    'aa.nrIncisoArtigo3',
                    'aa.dsAlineaArt3',
                    'aa.stArtigo18',
                    'aa.dsAlineaArtigo18',
                    'aa.stArtigo26',
                    'aa.stLei5761',
                    'aa.stArtigo27',
                    'aa.stIncisoArtigo27_I',
                    'aa.stIncisoArtigo27_II',
                    'aa.stIncisoArtigo27_III',
                    'aa.stIncisoArtigo27_IV',
                    'aa.stAvaliacao',
                    'aa.tpAnalise',
                    new Zend_Db_Expr('(Cast(aa.dsAvaliacao as TEXT)) as dsAvaliacao'),
                )
        );
        $select->joinInner(
                array('prod' => 'Produto'),
                'aa.idProduto = prod.Codigo',
                array('prod.Descricao as produto')
        );
        $select->joinInner(
                array('proj' => 'Projetos'),
                'proj.IdPRONAC = aa.idPRONAC',
                array()
        );
        $select->joinInner(
                array('PDP' => 'PlanoDistribuicaoProduto'),
                'PDP.idProjeto = proj.idProjeto and PDP.idProduto = aa.idProduto',
                array('PDP.stPrincipal')
        );
        $select->joinInner(
                array('AC' => 'tbAnaliseDeConteudo'),
                'aa.idAnaliseConteudo = AC.idAnaliseDeConteudo',
                array(
                    'AC.TipoParecer',
                    'AC.Lei8313 AS stLei8313_Antigo',
                    'AC.Artigo3 AS stArtigo3_Antigo',
                    'AC.IncisoArtigo3 AS nrIncisoArtigo3_Antigo',
                    'AC.AlineaArtigo3 AS dsAlineaArt3_Antigo',
                    'AC.Artigo18 AS stArtigo18_Antigo',
                    'AC.AlineaArtigo18 AS dsAlineaArtigo18_Antigo',
                    'AC.Artigo26 AS stArtigo26_Antigo',
                    'AC.Lei5761 AS stLei5761_Antigo',
                    'AC.Artigo27 AS stArtigo27_Antigo',
                    'AC.IncisoArtigo27_I AS stIncisoArtigo27_I_Antigo',
                    'AC.IncisoArtigo27_II AS stIncisoArtigo27_II_Antigo',
                    'AC.IncisoArtigo27_III AS stIncisoArtigo27_III_Antigo',
                    'AC.IncisoArtigo27_IV AS stIncisoArtigo27_IV_Antigo',
                    'AC.ParecerFavoravel AS stAvaliacao_Antigo',
                    new Zend_Db_Expr('Cast(AC.ParecerDeConteudo as Text)  AS dsAvaliacao_Antigo'),
                    'AC.idUsuario AS idAgente_Antigo',
                    new Zend_Db_Expr('SAC.dbo.fnNomeParecerista(AC.idUsuario) AS Parecerista')
                )
        );
        $select->where('aa.tpAnalise = ?', $tpanalise);
        $select->where('aa.idPronac = ?', $idpronac);
        $select->where('PDP.stPlanoDistribuicaoProduto = ?', 1);

        //adiciona quantos filtros foram enviados
        foreach ($where as $coluna => $valor) {
            $select->where($coluna, $valor);
        }

        if ($count) {
            $slctContador = $this->select();
            $slctContador->setIntegrityCheck(false);
            $slctContador->from(
                array("aa"=>$this->_name),
                            array("total" => "count(*)"),
                                  "SAC.dbo"
            );
            $slctContador->joinInner(
                    array('prod' => 'Produto'),
                    'aa.idProduto = prod.Codigo',
                    array()
            );
            $slctContador->joinInner(
                    array('proj' => 'Projetos'),
                    'proj.IdPRONAC = aa.idPRONAC',
                    array()
            );
            $slctContador->joinInner(
                    array('PDP' => 'PlanoDistribuicaoProduto'),
                    'PDP.idProjeto = proj.idProjeto and PDP.idProduto = aa.idProduto',
                    array()
            );
            $slctContador->joinInner(
                    array('AC' => 'tbAnaliseDeConteudo'),
                    'aa.idAnaliseConteudo = AC.idAnaliseDeConteudo',
                    array()
            );

            $slctContador->where('aa.tpAnalise = ?', $tpanalise);
            $slctContador->where('aa.idPronac = ?', $idpronac);

            //adiciona quantos filtros foram enviados
            foreach ($where as $coluna => $valor) {
                $slctContador->where($coluna, $valor);
            }
            $rs = $this->fetchAll($slctContador)->current();
            if ($rs) {
                return $rs->total;
            } else {
                return 0;
            }
        }

        //adicionando linha order ao select
        $select->order($order);

        // paginacao
        if ($tamanho > -1) {
            $tmpInicio = 0;
            if ($inicio > -1) {
                $tmpInicio = $inicio;
            }
            $select->limit($tamanho, $tmpInicio);
        }
        //x($select->assemble());
        return $this->fetchAll($select);
    }


    public function buscarAnalises($where=array(), $order=array(), $tamanho=-1, $inicio=-1, $qtdeTotal=false)
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(
                array('aa' => $this->_name),
                array(
                    new Zend_Db_Expr('
                    CASE
                        WHEN stPrincipal = 1
                        THEN 0
                        ELSE 1
                    END ordenacao'),
                    'idAnaliseAprovacao',
                    'idProduto',
                    'stLei8313',
                    'stArtigo3',
                    'nrIncisoArtigo3',
                    'dsAlineaArt3',
                    'stArtigo18',
                    'dsAlineaArtigo18',
                    'stArtigo26',
                    'stLei5761',
                    'stArtigo27',
                    'stIncisoArtigo27_I',
                    'stIncisoArtigo27_II',
                    'stIncisoArtigo27_III',
                    'stIncisoArtigo27_IV',
                    'stAvaliacao',
                    'tpAnalise',
                    new Zend_Db_Expr('(Cast(aa.dsAvaliacao as TEXT)) as dsAvaliacao'),
                )
        );
        $select->joinInner(
                array('prod' => 'Produto'),
            'aa.idProduto = prod.Codigo',
                array('prod.Descricao as produto'),
            'SAC.dbo'
        );
        $select->joinInner(
                array('proj' => 'Projetos'),
            'proj.IdPRONAC = aa.idPRONAC',
                array(),
            'SAC.dbo'
        );
        $select->joinInner(
                array('PDP' => 'PlanoDistribuicaoProduto'),
            'PDP.idProjeto = proj.idProjeto and PDP.idProduto = aa.idProduto',
                array('PDP.stPrincipal'),
            'SAC.dbo'
        );
        $select->joinInner(
                array('AC' => 'tbAnaliseDeConteudo'),
            'aa.idAnaliseConteudo = AC.idAnaliseDeConteudo',
                array(
                    'TipoParecer',
                    'Lei8313 AS stLei8313_Antigo',
                    'Artigo3 AS stArtigo3_Antigo',
                    'IncisoArtigo3 AS nrIncisoArtigo3_Antigo',
                    'AlineaArtigo3 AS dsAlineaArt3_Antigo',
                    'Artigo18 AS stArtigo18_Antigo',
                    'AlineaArtigo18 AS dsAlineaArtigo18_Antigo',
                    'Artigo26 AS stArtigo26_Antigo',
                    'Lei5761 AS stLei5761_Antigo',
                    'Artigo27 AS stArtigo27_Antigo',
                    'IncisoArtigo27_I AS stIncisoArtigo27_I_Antigo',
                    'IncisoArtigo27_II AS stIncisoArtigo27_II_Antigo',
                    'IncisoArtigo27_III AS stIncisoArtigo27_III_Antigo',
                    'IncisoArtigo27_IV AS stIncisoArtigo27_IV_Antigo',
                    'ParecerFavoravel AS stAvaliacao_Antigo',
                    new Zend_Db_Expr('Cast(AC.ParecerDeConteudo as Text)  AS dsAvaliacao_Antigo'),
                    'idUsuario AS idAgente_Antigo'
                )
        );

        //adiciona quantos filtros foram enviados
        foreach ($where as $coluna => $valor) {
            $select->where($coluna, $valor);
        }

        if ($qtdeTotal) {
            return $this->fetchAll($select)->count();
        }

        //adicionando linha order ao select
        $select->order($order);

        // paginacao
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
