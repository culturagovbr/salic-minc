<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Pauta
 *
 * @author augusto
 */
class Pauta extends MinC_Db_Table_Abstract
{
    protected $_schema = 'BDCORPORATIVO.scsac';
    protected $_name = 'tbPauta';

    public function PautaAprovada($idNrReuniao, $idpronac=null)
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(
                array('tp' => $this->_name),
                array(
                    'tp.stAnalise',
                    'tp.IdPRONAC'
                )
        );
        $select->joinInner(
                array('pr' => 'Projetos'),
                'pr.IdPRONAC = tp.IdPRONAC',
                array(
                    'pr.Area',
                    new Zend_Db_Expr('(pr.AnoProjeto+pr.Sequencial) as pronac'),
                    'pr.NomeProjeto',
                    'pr.DtProtocolo',
                    new Zend_Db_Expr('SAC.dbo.fnDtAprovacao(pr.AnoProjeto,pr.Sequencial) as DtAprovacao')
                ),
                'SAC.dbo'
        );
        $select->joinInner(
                array('ar' => 'Area'),
                'ar.Codigo = pr.Area',
                array('ar.Descricao as Area'),
                'SAC.dbo'
        );
        $select->joinInner(
                array('seg' => 'Segmento'),
                'seg.Codigo = pr.Segmento',
                array('seg.Descricao as Segmento'),
                'SAC.dbo'
        );
        $select->joinInner(
                array('pa' => 'Parecer'),
                'pa.IdPRONAC = pr.IdPRONAC and pa.stAtivo = 1',
                array(
                    'pa.ResumoParecer',
                    'pa.ParecerFavoravel'
                ),
                'SAC.dbo'
        );
        $select->joinLeft(
                array('cv' => 'tbConsolidacaoVotacao'),
                'cv.IdPRONAC = pr.IdPRONAC',
                array(
                    new Zend_Db_Expr('Cast(cv.dsConsolidacao as TEXT) as dsConsolidacao')
                ),
                'BDCORPORATIVO.scSAC'
        );
        $select->joinLeft(
                array('ap' => 'aprovacao'),
                'ap.IdPRONAC = tp.IdPRONAC',
                array(
                    'ap.AprovadoReal'
                ),
                'SAC.dbo'
        );
        $select->where('tp.idNrReuniao = ?', $idNrReuniao);
        if ($idpronac) {
            $select->where('tp.IdPRONAC = ?', $idpronac);
        }
        $select->order('pr.Area');
        $select->order('pr.Segmento');
        $select->order('pr.IdPRONAC');

        return $this->fetchAll($select);
    }

    public function PautaReuniaoAtual($idNrReuniao)
    {
        $slct = $this->select();
        $slct->setIntegrityCheck(false);
        $slct->from(
                array('tp' => $this->_name),
                array(
                    'tp.dtEnvioPauta',
                    'tp.stEnvioPlenario',
                    'tp.stAnalise'
                )
        );
        $slct->joinInner(
                array('pr' => 'Projetos'),
                "pr.IdPRONAC = tp.IdPRONAC",
                array(
                    new Zend_Db_Expr('(pr.AnoProjeto+pr.Sequencial) as pronac'),
                    'pr.NomeProjeto',
                    'pr.IdPRONAC'
                ),
                "SAC.dbo"
        );
        $slct->joinInner(
                array('ar' => 'Area'),
                "pr.Area = ar.Codigo",
                array('ar.Descricao as area'),
                "SAC.dbo"
        );
        $slct->joinInner(
                array('seg' => 'Segmento'),
                "pr.Segmento = seg.Codigo",
                array('seg.Descricao as segmento'),
                "SAC.dbo"
        );
        $slct->joinInner(
                array('par' => 'Parecer'),
                "par.IdPRONAC = tp.IdPRONAC",
                array('par.ParecerFavoravel'),
                "SAC.dbo"
        );
        $slct->joinInner(
                array('dpc' => 'tbDistribuicaoProjetoComissao'),
                "pr.IdPRONAC = dpc.idPRONAC",
                array()
        );
        $slct->joinInner(
                array('nm' => 'Nomes'),
                "nm.idAgente = dpc.idAgente",
                array('Descricao as nomeComponente'),
                'AGENTES'
        );
        $slct->where('tp.idNrReuniao = ?', $idNrReuniao);
        $slct->where('par.stAtivo = ?', 1);
        $slct->where('dpc.stDistribuicao = ?', 'A');
        $slct->where("tp.stAnalise not in ('AS', 'IS', 'AR')");
        
        return $this->fetchAll($slct);
    }


    public function pronacVotacaoAtual($idnrreuniao, $idpronac, $idTipoReadequacao)
    {
        //INICIAL
        $a = $this->select();
        $a->setIntegrityCheck(false);
        $a->from(
                array('tp' => $this->_name),
                array(
                    new Zend_Db_Expr('1 as tpConsolidacaoVotacao'),
                    'tp.stAnalise',
                    'tp.idNrReuniao'
                )
        );
        $a->joinInner(
                array('pr' => 'projetos'),
                'pr.IdPRONAC = tp.idPRONAC',
                array(
                    new Zend_Db_Expr('(pr.AnoProjeto+pr.Sequencial) as pronac'),
                    'pr.NomeProjeto',
                    'pr.IdPRONAC'
                ),
            'SAC.dbo'
        );
        $a->where('tp.idPRONAC = ?', $idpronac);
        $a->where('tp.idNrReuniao = ?', $idnrreuniao);
        
        
        //RECURSO
        $b = $this->select();
        $b->setIntegrityCheck(false);
        $b->from(
                array('a' => 'tbRecurso'),
                array(
                    new Zend_Db_Expr('2 as tpConsolidacaoVotacao'),
                    'a.stAnalise',
                    'a.idNrReuniao'
                ),
            'SAC.dbo'
        );
        $b->joinInner(
                array('b' => 'projetos'),
                'a.IdPRONAC = b.idPRONAC',
                array(
                    new Zend_Db_Expr('(b.AnoProjeto+b.Sequencial) as pronac'),
                    'b.NomeProjeto',
                    'b.IdPRONAC'
                ),
            'SAC.dbo'
        );
        $b->where('b.idPRONAC = ?', $idpronac);
        $b->where('a.idNrReuniao = ?', $idnrreuniao);
        
        
        //READEQUACAO
        $c = $this->select();
        $c->setIntegrityCheck(false);
        $c->from(
                array('a' => 'tbReadequacao'),
                array(
                    new Zend_Db_Expr('3 as tpConsolidacaoVotacao'),
                    'a.stAnalise',
                    'a.idNrReuniao'
                ),
            'SAC.dbo'
        );
        $c->joinInner(
                array('b' => 'projetos'),
                'a.IdPRONAC = b.idPRONAC',
                array(
                    new Zend_Db_Expr('(b.AnoProjeto+b.Sequencial) as pronac'),
                    'b.NomeProjeto',
                    'b.IdPRONAC'
                ),
            'SAC.dbo'
        );
        if (empty($idTipoReadequacao)) {
            $idTipoReadequacao = 0;
        }
        
        $c->where('b.idPRONAC = ?', $idpronac);
        $c->where('a.idNrReuniao = ?', $idnrreuniao);
        $c->where('a.idTipoReadequacao = ?', $idTipoReadequacao);
        
        $slctUnion = $this->select()->union(array('('.$a.')', '('.$b.')', '('.$c.')'));

        
        return $this->fetchRow($slctUnion);
    }
    

    public function buscarpautacomponente($idAgente, $aprovacao=false)
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(
                array('tp' => $this->_name),
                array('tp.idPRONAC')
        );
        $select->joinInner(
                array('dp' => 'tbDistribuicaoProjetoComissao'),
                'dp.IdPRONAC = tp.idPRONAC',
                array(),
                "BDCORPORATIVO.scSAC"
        );
        $select->where("dp.idAgente = ?", $idAgente);
        if ($aprovacao) {
            $select->where(new Zend_Db_Expr('(tp.idPRONAC not in (select idpronac from sac.dbo.aprovacao where idpronac = tp.idPRONAC))'), '');
        }

        return $this->fetchAll($select);
    }

    public function dadosiniciaistermoaprovacao($idpronac = array())
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(
                array('pt' => $this->_name),
                array(
                    'pt.IdPRONAC',
                    'pt.stAnalise'
                )
        );
        $select->joinInner(
                array('pr' => 'Projetos'),
                "pt.IdPRONAC = pr.IdPRONAC",
                array(
                    new Zend_Db_Expr('(pr.AnoProjeto+pr.Sequencial) as pronac'),
                    'pr.NomeProjeto',
                    'pr.Orgao',
                    'pr.Area'
                ),
                'SAC.dbo'
        );
        $select->joinInner(
                array('pa' => 'Parecer'),
                'pa.IdPRONAC = pt.IdPRONAC and pa.stAtivo = 1',
                array('pa.TipoParecer'),
                'SAC.dbo'
        );
        $select->joinInner(
                array('r' => 'tbReuniao'),
                'pt.idNrReuniao = r.idNrReuniao',
                array(
                    'r.NrReuniao',
                    'r.DtInicio',
                    'r.DtFinal'
                ),
                'SAC.dbo'
        );
        $select->joinLeft(
            array('cv' => 'tbConsolidacaoVotacao'),
            "cv.IdPRONAC = pt.IdPRONAC and cv.IdNrReuniao = pt.IdNrReuniao",
            array(
                new Zend_Db_Expr('CAST(cv.dsConsolidacao as TEXT) as dsConsolidacao')
            ),
            "BDCORPORATIVO.scSAC"
        );
        foreach ($idpronac as $resu) {
            $select->orwhere('pt.IdPRONAC = ?', $resu);
        }
        return $this->fetchAll($select);
    }

    public function PautaProximaReuniao($NrReuniao)
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(
                array('pt' => $this->_name),
                array('pt.IdPRONAC',
                    'pt.idNrReuniao'
                )
        );
        $select->joinInner(
                array('r' => 'tbReuniao'),
                'r.idNrReuniao = pt.idNrReuniao',
                array(''),
                'SAC.dbo'
        );
        $select->where('r.NrReuniao = ?', $NrReuniao);
        $select->where('(pt.stAnalise in (?)', array('AC', 'IC'));
        $select->where('pt.dtEnvioPauta < r.dtFinal');
        $select->Where('pt.stEnvioPlenario = ?)', 'S');
        return $this->fetchAll($select);
    }

    //reescrevendo metodo generico devido a necessidade de realizar o CAST para o cmapo descricao que era retornado incompleto
    public function buscar($where=array(), $order=array(), $tamanho=-1, $inicio=-1)
    {
        $slct = $this->select();
        $slct->setIntegrityCheck(false);
        $slct->from(
                array($this->_name),
                array('*',
                      new Zend_Db_Expr('CAST(dsAnalise AS TEXT) AS dsAnalise')
                )
        );
        //adiciona quantos filtros foram enviados
        foreach ($where as $coluna => $valor) {
            $slct->where($coluna, $valor);
        }

        //adicionando linha order ao select
        $slct->order($order);

        // paginacao
        if ($tamanho > -1) {
            $tmpInicio = 0;
            if ($inicio > -1) {
                $tmpInicio = $inicio;
            }
            $slct->limit($tamanho, $tmpInicio);
        }
        
        return $this->fetchAll($slct);
    }
}