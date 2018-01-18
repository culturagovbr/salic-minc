<?php
class DistribuicaoProjetoComissao extends MinC_Db_Table_Abstract
{
    protected $_schema = 'BDCORPORATIVO.scSAC';
    protected $_name = 'tbDistribuicaoProjetoComissao';

    public function buscarProjetosDistribuidos($idAgente, $idNrReuniao)
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(
            array('DPC' => $this->_name),
            array(
                'CONVERT(CHAR(10),DPC.dtDistribuicao,103) AS DataRecebimento',
                new Zend_Db_Expr('ISNULL((SELECT tpAcao FROM BDCORPORATIVO.scSAC.tbRetirarDePauta x WHERE stAtivo = 1 and  x.idPronac = pr.idPronac),0) as Acao'),
                new Zend_Db_Expr('ISNULL((SELECT idRetirardepauta FROM BDCORPORATIVO.scSAC.tbRetirarDePauta x WHERE stAtivo = 1 and  x.idPronac = pr.idPronac),0) as idRetiradaPauta')
            )
        );
        $select->joinInner(
            array('Pr' => 'Projetos'),
            'DPC.idPRONAC = Pr.IdPRONAC',
            array(
            '(Pr.AnoProjeto + Pr.Sequencial) AS PRONAC',
            'Pr.NomeProjeto',
            'Pr.idPRONAC',
            'Pr.Situacao'
                ),
            'SAC.dbo'
        );
        $select->joinInner(
            array('Pa' => 'Parecer'),
            'Pa.idPRONAC = Pr.IdPRONAC',
            array(
            "CASE WHEN Pa.ParecerFavoravel in ('2','3')
                THEN 'Sim'
                ELSE 'Nao'
                End AS ParecerFavoravel",
            "Pa.idTipoAgente"
                ),
            'SAC.dbo'
        );
        $select->joinLeft(
                array('pt' => 'tbPauta'),
            "pt.IdPRONAC = Pr.IdPRONAC",
            array("pt.idNrReuniao"),
            'BDCORPORATIVO.scSAC'
        );

        $select->where('Pr.Situacao in (?)', array('C10', 'D01'));
        $select->where('Pa.TipoParecer = ?', 1);
        $select->where("Pr.IdPRONAC not in (select idPRONAC from SAC.dbo.tbDiligencia where idPRONAC = Pr.IdPRONAC and idTipoDiligencia = 126 and stEnviado = 'S' and stEstado = 0)");
        $select->where('DPC.idAgente = ?', $idAgente);
        $select->where('DPC.stDistribuicao = ?', 'A');
        $select->where('Pa.stAtivo = ?', 1);
        $select->order('Pa.idTipoAgente');
        $select->order('Pr.idPRONAC');

        return $this->fetchAll($select);
    }

    //LISTAR PROJETOS QUE FORAM ENCAMINHADOS ATRAV�S DO UC53
    public function buscarProjetosDistribuidosReadequados($where=array(), $order=array())
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(
            array('DPC' => $this->_name),
                      array('CONVERT(CHAR(10),DPC.dtDistribuicao,103) AS DataRecebimento')
        );
        $select->joinInner(
            array('Pr' => 'Projetos'),
                                 'DPC.idPRONAC = Pr.IdPRONAC',
                           array('(Pr.AnoProjeto + Pr.Sequencial) AS PRONAC',
                                 'Pr.NomeProjeto',
                                 'Pr.idPRONAC',
                                 'Pr.Situacao'),
            'SAC.dbo'
        );
        $select->joinInner(
            array('Pa' => 'Parecer'),
                                 'Pa.idPRONAC = Pr.IdPRONAC',
                            array("CASE WHEN Pa.ParecerFavoravel in ('2','3')
                                    THEN 'Sim'
                                    ELSE 'Não'
                                    End AS ParecerFavoravel",
                                    "Pa.idTipoAgente"
                                ),
            'SAC.dbo'
        );
        $select->joinLeft(
            array('pt'=>'tbPauta'),
                                "pt.IdPRONAC = Pr.IdPRONAC",
                          array("pt.idNrReuniao"),
            'BDCORPORATIVO.scSAC'
        );

        $select->where(" NOT EXISTS(
                              SELECT idpronac
                              FROM SAC.dbo.Aprovacao
                              WHERE idpronac = Pr.idPRONAC
                                AND TipoAprovacao = Pa.TipoParecer) ");
        $select->where(" EXISTS(
                                  SELECT top 1
                                  idpronac
                                  FROM SAC.dbo.tbPlanilhaAprovacao
                                  WHERE idpronac = Pr.idPRONAC and tpPlanilha = 'SR') ");
        $select->where(" EXISTS(
                                  select top 1
                                  idpronac
                                  from SAC.dbo.tbPlanilhaAprovacao
                                  WHERE idpronac = Pr.idPRONAC and tpPlanilha = 'PA')");
        //adiciona outras condicoes enviadas
        foreach ($where as $chave => $valor) {
            $select->where($chave, $valor);
        }

        return $this->fetchAll($select);
    }

    public function BuscarComponentes()
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(
            array('SDPC' => $this->_schema . "." . $this->_name)
//                      array(' (SELECT COUNT(SDPC.idPronac) as QTD')
        );
        $select->joinInner(
                array('N' => 'Nomes'),
            ""
        );
    }

    public function buscarProjetosPorComponente($where=array())
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(
                array('D' =>  $this->_name),
            array(
            'D.idAgente',
            'DATEDIFF(DAY,D.dtDistribuicao,GETDATE()) as Dias',
            'CONVERT(CHAR(10), D.dtDistribuicao,103) AS dtDistribuicao',
            'CONVERT(CHAR(10), D.dtDistribuicao,103) AS dtCompleta',
                ),
            $this->_schema
        );
        $select->joinInner(
                array('P' => 'Projetos'),
            "D.idPRONAC = P.idPRONAC",
            array(
            'P.idPRONAC',
            '(P.AnoProjeto + P.Sequencial) AS PRONAC',
            'P.NomeProjeto',
            'P.Area'
                ),
            'SAC.dbo'
        );
        foreach ($where as $key => $valor) {
            $select->where($key, $valor);
        }
        $select->order('D.dtDistribuicao');
        $select->order('NomeProjeto asc');
        //echo $select;die;
        return $this->fetchAll($select);
    }

    public function buscarProjetosCnicAtual($idagente = null)
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(
            array('dpc' => $this->_name),
            array('dpc.IdPRONAC')
        );
        $select->where('not exists(select IdPRONAC from BDCORPORATIVO.scSAC.tbPauta where idpronac = dpc.IdPRONAC)');
        $select->where('not exists(select IdPRONAC from SAC.dbo.Parecer where idpronac = dpc.IdPRONAC and idTipoAgente = 6)');
        if ($idagente) {
            $select->where('dpc.idAgente = ?', $idagente);
        }
        $select->where('dpc.stDistribuicao = ?', 'A');

        return $this->fetchAll($select);
    }

    public function AgenteDistribuido($idpronac)
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(
            array('dpc' => $this->_name),
            array('dpc.IdPRONAC')
        );
        $select->joinInner(
                array('nm' => 'nomes'),
            'dpc.idAgente = nm.idAgente',
            array('nm.Descricao as nome'),
            'Agentes.dbo'
        );
        $select->where('dpc.idPronac = ?', $idpronac);

        return $this->fetchAll($select);
    }

    public function projetosNaoAnalisados($nrreuniao)
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(
                array('dpc' => $this->_name),
            array('dpc.idPRONAC')
        );
        $select->where('dpc.idPRONAC not in(select idPRONAC from SAC.dbo.Parecer where idPronac = dpc.idPRONAC and idTipoAgente=6)', '');
        $select->where(new Zend_Db_Expr('NOT EXISTS(SELECT TOP 1 * FROM BDCORPORATIVO.scSAC.tbPauta  o  WHERE o.IdPRONAC = dpc.idPRONAC)'));
        $select->where('dpc.stDistribuicao = ?', 'A');

        return $this->fetchAll($select);
    }

    public function projetosAnalisados($idnrreuniao)
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(
                array('dpc' => $this->_name),
            array('dpc.idPRONAC')
        );
        $select->where("dpc.idPRONAC in (select idPRONAC from BDCORPORATIVO.scSAC.tbPauta where idPronac = dpc.idPRONAC and idNrReuniao = $idnrreuniao )", "");
        $select->where('dpc.stDistribuicao = ?', 'A');
        return $this->fetchAll($select);
    }

    public function buscarcomponentebalanceamento($cdarea)
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(
                array('SDPC' => $this->_name),
            array('SDPC.idAgente')
        );
        $select->joinInner(
                array('pr' => 'projetos'),
            'pr.IdPRONAC = SDPC.idPronac',
            array(
            '(COUNT(SDPC.idPronac)) as QTD'
                ),
            'SAC.dbo'
        );
        $select->joinInner(
                array('tc' => 'tbTitulacaoConselheiro'),
            'tc.idAgente = SDPC.idAgente',
            array(),
            'Agentes.dbo'
        );
        $select->where('pr.Situacao = ? ', 'C10');
        $select->where('SDPC.stDistribuicao = ? ', 'A');
        $select->where('tc.cdArea = ? ', $cdarea);
        $select->where('not exists (select idpronac from BDCORPORATIVO.scSAC.tbPauta where idpronac = SDPC.idPronac)', '');
        $select->group('SDPC.idAgente');
        $select->order('QTD desc');

        xd($select->assemble());

//        SELECT SDPC.idAgente,  COUNT(SDPC.idPronac) as QTD
//FROM BDCORPORATIVO.scSAC.tbDistribuicaoProjetoComissao SDPC
//INNER JOIN SAC.dbo.projetos pr on pr.IdPRONAC = SDPC.idPronac
//INNER JOIN Agentes.dbo.tbTitulacaoConselheiro tc on tc.idAgente = SDPC.idAgente
//WHERE pr.Situacao = 'C10' AND SDPC.stDistribuicao = 'A' and tc.cdArea='6'
//and not exists (select idpronac from BDCORPORATIVO.scSAC.tbPauta where idpronac = SDPC.idPronac)
//group by SDPC.idAgente
    }
}
