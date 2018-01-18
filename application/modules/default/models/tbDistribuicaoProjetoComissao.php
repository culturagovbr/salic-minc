<?php
class tbDistribuicaoProjetoComissao extends MinC_Db_Table_Abstract
{
    protected $_banco = "BDCORPORATIVO";
    protected $_schema = "BDCORPORATIVO.scSAC";
    protected $_name = "tbDistribuicaoProjetoComissao";

    public function buscarProjetoEmPauta_ORIGINAL($where=array(), $order=array(), $tamanho=-1, $inicio=-1, $count=false)
    {
        $slct = $this->select();
        $slct->setIntegrityCheck(false);
        $slct->from(
                        array('dpc'=>$this->_name),
                        array('DtDistribuicao'=>'CONVERT(CHAR(20),DtDistribuicao, 120)', 'idAgente', 'Dias'=>new Zend_Db_Expr('DATEDIFF(DAY,dpc.DtDistribuicao,GETDATE())'))
                     );
        $slct->joinInner(
                            array('pr'=>'Projetos'),
                            'pr.idPronac = dpc.idPronac',
                            array('Area', 'idPronac', 'Pronac'=>new Zend_Db_Expr('pr.AnoProjeto + pr.Sequencial'), 'NomeProjeto', 'Situacao'=>new Zend_Db_Expr("pr.Situacao + ' - ' + s.Descricao"), 'Segmento', 'CgcCpf'),
                            'SAC.dbo'
                          );
        $slct->joinInner(
                            array('pa'=>'Parecer'),
                            'pr.idPronac = pa.idPronac',
                            array('Avaliacao'=>new Zend_Db_Expr("case when pa.ParecerFavoravel = '1' then 'Desfavor&aacute;vel' when pa.ParecerFavoravel = '2' then 'Favor&aacute;vel' end"), 'SugeridoReal'),
                            'SAC.dbo'
                          );
        $slct->joinInner(
                            array('s'=>'Situacao'),
                            'pr.Situacao = s.Codigo',
                            array(),
                            'SAC.dbo'
                          );
        $slct->joinInner(
                            array('ar'=>'Area'),
                            'pr.Area = ar.Codigo',
                            array('DescArea'=>'Descricao'),
                            'SAC.dbo'
                          );
        $slct->joinInner(
                            array('se'=>'Segmento'),
                            'pr.Segmento = se.Codigo',
                            array('DescSegmento'=>'Descricao'),
                            'SAC.dbo'
                          );
        $slct->joinInner(
                            array('nm'=>'Nomes'),
                            'dpc.idAgente = nm.idAgente',
                            array('Componente'=>'Descricao'),
                            'Agentes.dbo'
                          );

        $slct->where("pr.Situacao IN (?)", array("C10", "C30"));
        $slct->where("dpc.stDistribuicao = ?", "A");
        $slct->where("pa.stAtivo = ?", 1);
        $slctInterno = $this->select();
        $slctInterno->setIntegrityCheck(false);
        $slctInterno->from(
                        array('tbpa'=>'tbPauta'),
                        array('*'),
                        'BDCORPORATIVO.scSAC'
                     );
        $slctInterno->where("tbpa.IdPRONAC = pr.idPronac");
        $slctInterno->limit(1);
        $slct->where("NOT EXISTS (?)", new Zend_Db_Expr($slctInterno));

        //adiciona quantos filtros foram enviados
        foreach ($where as $coluna => $valor) {
            $slct->where($coluna, $valor);
        }

        if ($count) {
            $slct2 = $this->select();
            $slct2->setIntegrityCheck(false);
            $slct2->from(
                            array('dpc'=>$this->_name),
                            array('total'=>'count(*)')
                         );
            $slct2->joinInner(
                                array('pr'=>'Projetos'),
                                'pr.idPronac = dpc.idPronac',
                                array(),
                                'SAC.dbo'
                              );
            $slct2->joinInner(
                                array('pa'=>'Parecer'),
                                'pr.idPronac = pa.idPronac',
                                array(),
                                'SAC.dbo'
                              );
            $slct2->joinInner(
                                array('s'=>'Situacao'),
                                'pr.Situacao = s.Codigo',
                                array(),
                                'SAC.dbo'
                              );
            $slct2->joinInner(
                                array('ar'=>'Area'),
                                'pr.Area = ar.Codigo',
                                array(),
                                'SAC.dbo'
                              );
            $slct2->joinInner(
                                array('se'=>'Segmento'),
                                'pr.Segmento = se.Codigo',
                                array(),
                                'SAC.dbo'
                              );
            $slct2->joinInner(
                                array('nm'=>'Nomes'),
                                'dpc.idAgente = nm.idAgente',
                                array(),
                                'Agentes.dbo'
                              );

            $slct2->where("pr.Situacao IN (?)", array("C10", "C30"));
            $slct2->where("dpc.stDistribuicao = ?", "A");
            $slct2->where("pa.stAtivo = ?", 1);
            $slctInterno2 = $this->select();
            $slctInterno2->setIntegrityCheck(false);
            $slctInterno2->from(
                            array('tbpa'=>'tbPauta'),
                            array('*'),
                            'BDCORPORATIVO.scSAC'
                         );
            $slctInterno2->where("tbpa.IdPRONAC = pr.idPronac");
            $slctInterno2->limit(1);
            $slct->where("NOT EXISTS (?)", new Zend_Db_Expr($slctInterno2));

            //adiciona quantos filtros foram enviados
            foreach ($where as $coluna => $valor) {
                $slct2->where($coluna, $valor);
            }

            
            $rs = $this->fetchAll($slct2)->current();
            if ($rs) {
                return $rs->total;
            } else {
                return 0;
            }
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

    public function buscarProjetoEmPauta($where=array(), $order=array(), $tamanho=-1, $inicio=-1, $count=false, $analise=null, $arrReuniao=array(), $retornaResultadoIndividual=null)
    {
        /*================ PROJETO ANALISADOS ================*/
        $slctAnalisados = $this->select();
        $slctAnalisados->setIntegrityCheck(false);
        $slctAnalisados->from(
                        array('dpc'=>$this->_name),
                        array(new Zend_Db_Expr("'Analisado' as Analise"),
                        'DtDistribuicao'=>'CONVERT(CHAR(20),DtDistribuicao, 120)', 'idAgente',
                        'Dias'=>new Zend_Db_Expr('DATEDIFF(DAY,dpc.DtDistribuicao,GETDATE())')),
            $this->_schema
        );
        $slctAnalisados->joinInner(
                            array('pa'=>'tbPauta'),
                            '(pa.IdPRONAC = dpc.idPRONAC)',
                            array('idNrReuniao','stAnalise'),
                            'BDCORPORATIVO.scSAC'
                          );

        $slctAnalisados->joinInner(
                            array('pr'=>'Projetos'),
                            'pr.idPronac = dpc.idPronac',
                            array('idPronac', 'Pronac'=>new Zend_Db_Expr('pr.AnoProjeto + pr.Sequencial'), 'NomeProjeto', 'Situacao as CodSituacao', 'Situacao'=>new Zend_Db_Expr("pr.Situacao + ' - ' + s.Descricao"), 'DtInicioExecucao'=>'CONVERT(CHAR(20),DtInicioExecucao, 120)','DtFimExecucao'=>'CONVERT(CHAR(20),DtFimExecucao, 120)', 'Area', 'Segmento', 'CgcCpf', 'SolicitadoReal', 'ResumoProjeto'=>'CONVERT(VARCHAR(MAX), ResumoProjeto)'),
                            'SAC.dbo'
                          );
        $slctAnalisados->joinInner(
                            array('par'=>'Parecer'),
                            'pr.idPronac = par.idPronac AND par.DtParecer = (SELECT TOP 1 max(DtParecer) from SAC..Parecer where IdPRONAC = pr.IdPRONAC)',
                            array('Avaliacao'=>new Zend_Db_Expr("CASE WHEN par.ParecerFavoravel = '1' THEN 'Desfavor&aacute;vel' WHEN par.ParecerFavoravel = '2' THEN 'Favor&aacute;vel' END"), 'SugeridoReal'),
                            'SAC.dbo'
                          );
        $slctAnalisados->joinInner(
                            array('s'=>'Situacao'),
                            'pr.Situacao = s.Codigo',
                            array(),
                            'SAC.dbo'
                          );
        $slctAnalisados->joinInner(
                            array('ar'=>'Area'),
                            'pr.Area = ar.Codigo',
                            array('DescArea'=>'Descricao'),
                            'SAC.dbo'
                          );
        $slctAnalisados->joinInner(
                            array('se'=>'Segmento'),
                            'pr.Segmento = se.Codigo',
                            array('DescSegmento'=>'Descricao'),
                            'SAC.dbo'
                          );
        $slctAnalisados->joinInner(
                            array('nm'=>'Nomes'),
                            'dpc.idAgente = nm.idAgente',
                            array('Componente'=>'Descricao'),
                            'Agentes.dbo'
                          );
        $slctAnalisados->joinInner(
                            array('ag'=>'Agentes'),
                            'pr.CgcCpf = ag.CNPJCPF',
                            array(),
                            'Agentes.dbo'
                          );
        $slctAnalisados->joinInner(
                            array('nm2'=>'Nomes'),
                            'ag.idAgente = nm2.idAgente',
                            //array( new Zend_Db_Expr('CAST(nm2.Descricao as VARCHAR) as Proponente')),
                            array( new Zend_Db_Expr('convert(varchar(150), nm2.Descricao) as Proponente')),
                            'Agentes.dbo'
                          );
        $slctAnalisados->joinInner(
                            array('r'=>'tbReuniao'),
                            'pa.idNrReuniao = r.idNrReuniao',
                            array('NrReuniao'=>'r.NrReuniao'),
                            'SAC.dbo'
                          );

        $slctAnalisados->where("par.idTipoAgente = ?", 6);
        $slctAnalisados->where("par.stAtivo = ?", 1);
        //se NAO estiver informando numero de reunioes anteriores, pega a reuniao atual
        if (count($arrReuniao)<=0) {
            $slctAnalisados->where("r.stEstado = ?", 0);
        }
        $slctAnalisados->where("nm2.Status = ?", 0);

        //adiciona quantos filtros foram enviados
        foreach ($where as $coluna => $valor) {
            $slctAnalisados->where($coluna, $valor);
        }

        //RETORNA RESULTADO DA PRIMEIRA QUERY - PROJETO ANALISADOS
        if ($retornaResultadoIndividual == "1") {
            return $this->fetchAll($slctAnalisados);
        }

        /*========================================================*/
        /*================ PROJETO NAO ANALISADOS ================*/
        /*========================================================*/
        $slctNaoAnalisados = $this->select();
        $slctNaoAnalisados->setIntegrityCheck(false);
        $slctNaoAnalisados->from(
                        array('dpc'=>$this->_name),
                        array(new Zend_Db_Expr("'N&atilde;o analisado' as Analise"),'DtDistribuicao'=>'CONVERT(CHAR(20),DtDistribuicao, 120)', 'idAgente', 'Dias'=>new Zend_Db_Expr('DATEDIFF(DAY,dpc.DtDistribuicao,GETDATE())')),
            $this->_schema
                     );
        $slctNaoAnalisados->joinInner(
                            array('pr'=>'Projetos'),
                            'pr.idPronac = dpc.idPronac',
                            array(new Zend_Db_Expr('null as idNrReuniao'), new Zend_Db_Expr('null as stAnalise'),'idPronac', 'Pronac'=>new Zend_Db_Expr('pr.AnoProjeto + pr.Sequencial'), 'NomeProjeto', 'Situacao as CodSituacao', 'Situacao'=>new Zend_Db_Expr("pr.Situacao + ' - ' + s.Descricao"), 'DtInicioExecucao'=>'CONVERT(CHAR(20),DtInicioExecucao, 120)','DtFimExecucao'=>'CONVERT(CHAR(20),DtFimExecucao, 120)', 'Area', 'Segmento', 'CgcCpf', 'SolicitadoReal', 'ResumoProjeto'=>'CONVERT(VARCHAR(MAX), ResumoProjeto)'),
                            'SAC.dbo'
                          );
        $slctNaoAnalisados->joinInner(
                            array('par'=>'Parecer'),
                            'pr.idPronac = par.idPronac AND par.DtParecer = (SELECT TOP 1 max(DtParecer) from SAC..Parecer where IdPRONAC = pr.IdPRONAC)',
                            array('Avaliacao'=>new Zend_Db_Expr("CASE WHEN par.ParecerFavoravel = '1' THEN 'Desfavor&aacute;vel' WHEN par.ParecerFavoravel = '2' THEN 'Favor&aacute;vel' END"), 'SugeridoReal'),
                            'SAC.dbo'
                          );
        $slctNaoAnalisados->joinInner(
                            array('s'=>'Situacao'),
                            'pr.Situacao = s.Codigo',
                            array(),
                            'SAC.dbo'
                          );
        $slctNaoAnalisados->joinInner(
                            array('ar'=>'Area'),
                            'pr.Area = ar.Codigo',
                            array('DescArea'=>'Descricao'),
                            'SAC.dbo'
                          );
        $slctNaoAnalisados->joinInner(
                            array('se'=>'Segmento'),
                            'pr.Segmento = se.Codigo',
                            array('DescSegmento'=>'Descricao'),
                            'SAC.dbo'
                          );
        $slctNaoAnalisados->joinInner(
                            array('nm'=>'Nomes'),
                            'dpc.idAgente = nm.idAgente',
                            array('Componente'=>'Descricao'),
                            'Agentes.dbo'
                          );
        $slctNaoAnalisados->joinInner(
                            array('ag'=>'Agentes'),
                            'pr.CgcCpf = ag.CNPJCPF',
                            array(),
                            'Agentes.dbo'
                          );
        $slctNaoAnalisados->joinInner(
                            array('nm2'=>'Nomes'),
                            'ag.idAgente = nm2.idAgente',
                            array( new Zend_Db_Expr('convert(varchar(150), nm2.Descricao) as Proponente'), new Zend_Db_Expr('null as NrReuniao')),
                            'Agentes.dbo'
                          );
        $slctNaoAnalisados->where("pr.Situacao IN (?)", array("C10", "C30"));
        $slctNaoAnalisados->where("dpc.stDistribuicao = ?", "A");
        $slctNaoAnalisados->where("par.stAtivo = ?", 1);
        $slctNaoAnalisados->where("nm2.Status = ?", 0);


        /*=== INICIO EXCLUI PROJETOS CADASTRADOS NA PAUTA ====*/
        $slctInterno = $this->select();
        $slctInterno->setIntegrityCheck(false);
        $slctInterno->from(
                        array('tbpa'=>'tbPauta'),
                        array('*'),
                        'BDCORPORATIVO.scSAC'
                     );
        $slctInterno->where("tbpa.IdPRONAC = pr.idPronac");
        $slctInterno->limit(1);
        /*=== FIM EXCLUI PROJETOS CADASTRADOS NA PAUTA ====*/

        $slctNaoAnalisados->where("NOT EXISTS (?)", new Zend_Db_Expr($slctInterno));

        //adiciona quantos filtros foram enviados
        foreach ($where as $coluna => $valor) {
            $slctNaoAnalisados->where($coluna, $valor);
        }

        //RETORNA RESULTADO DA PRIMEIRA QUERY - PROJETO NAO ANALISADOS
        if ($retornaResultadoIndividual == "2") {
            return $this->fetchAll($slctNaoAnalisados);
        }
        /*========================================================*/
        /*========================  UNION  =======================*/
        /*========================================================*/

        $slctUnion = $this->select();
        $slctUnion->union(array($slctAnalisados, $slctNaoAnalisados));//->order($order);

        $db = Zend_Db_Table::getDefaultAdapter();
//        $db->setFetchMode(Zend_DB::FETCH_OBJ);

        $slctMaster = $db->select();
        $slctMaster->from(array('Master'=>$slctUnion));

        //BUSCA PELO STATUS DO PROJETO
        if ($analise != null) {
            $slctMaster->where("Analise = ?", $analise);
        }

        //BUSCA PELOS DADOS DA REUNIAO INFORMADA
        if (count($arrReuniao)>0) {
            foreach ($arrReuniao as $coluna => $valor) {
                $slctMaster->where($coluna, $valor);
            }
        }

        //RETORNA QTDE. DE REGISTRO PARA PAGINACAO
        if ($count) {
            return $db->fetchAll($slctMaster)->count();
        }

        //adicionando linha order ao select
        $slctMaster->order($order);

        // paginacao
        if ($tamanho > -1) {
            $tmpInicio = 0;
            if ($inicio > -1) {
                $tmpInicio = $inicio;
            }
            $slctMaster->limit($tamanho, $tmpInicio);
        }

        return $db->fetchAll($slctMaster);
    }

    public function buscaProjetosEmPauta($where=array(), $order=array(), $tamanho=-1, $inicio=-1, $count=false, $analise=null)
    {

        /*========================================================*/
        /*================ PROJETOS NAO ANALISADOS ===============*/
        /*========================================================*/
        $slctNaoAnalisados = $this->select();
        $slctNaoAnalisados->setIntegrityCheck(false);
        $slctNaoAnalisados->distinct();
        $slctNaoAnalisados->from(
            array('t'=>$this->_name),
            array(new Zend_Db_Expr("
                'N&atilde;o analisado' AS Analise
                ,t.idAgente
                ,n.Descricao AS Componente
                ,p.IdPronac AS idPronac
                ,p.AnoProjeto+p.Sequencial AS Pronac
                ,p.NomeProjeto
                ,CONVERT(VARCHAR(3000), p.ResumoProjeto) as ResumoProjeto
                --,CAST(p.ResumoProjeto AS TEXT) AS ResumoProjeto
                ,z.Descricao AS Proponente
                ,p.Situacao as CodSituacao
                ,p.Situacao + ' - ' + s.Descricao as Situacao
                ,p.UfProjeto AS UF
                ,(SELECT TOP 1 m1.Descricao FROM Agentes.dbo.EnderecoNacional x1
                    INNER JOIN Agentes.dbo.UF u1 ON (x1.UF = u1.idUF)
                    INNER JOIN Agentes.dbo.Municipios m1 ON (x1.UF = m1.idUFIBGE AND x1.Cidade = m1.idMunicipioIBGE)
                    WHERE x.idAgente = x1.idAgente) AS Cidade
                ,t.DtDistribuicao
                ,p.Area
                ,a.Descricao AS DescArea
                ,p.Segmento
                ,se.Descricao AS DescSegmento
                ,p.DtInicioExecucao
                ,p.DtFimExecucao
                ,DATEDIFF(DAY,t.DtDistribuicao,GETDATE()) AS Dias
                ,null AS idNrReuniao
                ,null as NrReuniao
                ,null AS stAnalise
                ,CASE
                    WHEN pr.ParecerFavoravel = '1' THEN 'Desfavor&aacute;vel'
                    WHEN pr.ParecerFavoravel = '2' THEN 'Favor&aacute;vel'
                END AS Avaliacao
                ,p.SolicitadoReal
                ,(SELECT SUM(qtItem*nrOcorrencia*vlUnitario) FROM SAC.dbo.tbPlanilhaAprovacao pa WHERE pa.IdPRONAC = p.IdPRONAC AND stAtivo = 'S' and pa.nrFonteRecurso=109) AS SugeridoReal
                ,e.Enquadramento as Enquadramento ")
            ),
            'BDCORPORATIVO.scSAC'
        );

        $slctNaoAnalisados->joinInner(
            array('p'=>'Projetos'),
            't.idPronac = p.idPronac',
            array(),
            'SAC.dbo'
        );
        $slctNaoAnalisados->joinInner(
            array('pr'=>'Parecer'),
            'pr.idPronac = p.idPronac',
            array(),
            'SAC.dbo'
        );
        $slctNaoAnalisados->joinLeft(
            array('e'=>'Enquadramento'),
            'e.idPronac = p.idPronac',
            array(),
            'SAC.dbo'
        );
        $slctNaoAnalisados->joinInner(
            array('s'=>'Situacao'),
            'p.Situacao = s.Codigo',
            array(),
            'SAC.dbo'
        );
        $slctNaoAnalisados->joinInner(
            array('a'=>'Area'),
            'p.Area = a.Codigo',
            array(),
            'SAC.dbo'
        );
        $slctNaoAnalisados->joinInner(
            array('se'=>'Segmento'),
            'p.Segmento = se.Codigo',
            array(),
            'SAC.dbo'
        );
        $slctNaoAnalisados->joinInner(
            array('n'=>'Nomes'),
            't.idAgente = n.idAgente',
            array(),
            'AGENTES.dbo'
        );
        $slctNaoAnalisados->joinInner(
            array('x'=>'Agentes'),
            'p.CgcCpf = x.CNPJCPF',
            array(),
            'AGENTES.dbo'
        );
        $slctNaoAnalisados->joinInner(
            array('z'=>'Nomes'),
            'x.idAgente = z.idAgente',
            array(),
            'AGENTES.dbo'
        );

        $slctNaoAnalisados->where("t.stDistribuicao = ?", 'A');
        $slctNaoAnalisados->where("pr.stAtivo = ?", 1);
        $slctNaoAnalisados->where("z.Status = ?", 0);
        $slctNaoAnalisados->where("p.Situacao in (?)", array('C10','C30'));
        $slctNaoAnalisados->where("NOT EXISTS(SELECT TOP 1 * FROM BDCORPORATIVO.scSAC.tbPauta o WHERE o.IdPRONAC = p.IdPronac)", '');

        //adiciona quantos filtros foram enviados
        foreach ($where as $coluna => $valor) {
            $slctNaoAnalisados->where($coluna, $valor);
        }

        //RETORNA RESULTADO DA PRIMEIRA QUERY - PROJETO NAO ANALISADOS
        if ($analise == "1") {
            if ($count) {
                return $this->fetchAll($slctNaoAnalisados)->count();
            }
            return $this->fetchAll($slctNaoAnalisados);
        }


        /*========================================================*/
        /*================== PROJETOS ANALISADOS =================*/
        /*========================================================*/
        $slctAnalisados = $this->select();
        $slctAnalisados->setIntegrityCheck(false);
        $slctAnalisados->distinct();
        $slctAnalisados->from(
            array('t'=>'tbPauta'),
            array(new Zend_Db_Expr("
                'Analisado' AS Analise
                ,z.idAgente
                ,n.Descricao AS Componente
                ,p.IdPronac AS idPronac
                ,p.AnoProjeto+p.Sequencial AS Pronac
                ,p.NomeProjeto
                ,CONVERT(VARCHAR(3000), p.ResumoProjeto) as ResumoProjeto
                --,CAST(p.ResumoProjeto AS TEXT) AS ResumoProjeto
                ,y.Descricao AS Proponente
                ,p.Situacao AS CodSituacao
                ,p.Situacao + ' - ' + s.Descricao AS Situacao
                ,p.UfProjeto AS UF
                ,(SELECT TOP 1 m1.Descricao FROM Agentes.dbo.EnderecoNacional x1
                    INNER JOIN Agentes.dbo.UF u1 ON (x1.UF = u1.idUF)
                    INNER JOIN Agentes.dbo.Municipios m1 ON (x1.UF = m1.idUFIBGE and x1.Cidade = m1.idMunicipioIBGE)
                    WHERE x.idAgente = x1.idAgente) AS Cidade
                ,z.DtDistribuicao
                ,p.Area
                ,a.Descricao AS DescArea
                ,p.Segmento
                ,se.Descricao AS DescSegmento
                ,p.DtInicioExecucao
                ,p.DtFimExecucao
                ,DATEDIFF(DAY,z.DtDistribuicao,GETDATE()) AS Dias
                ,t.idNrReuniao
                ,r.NrReuniao AS NrReuniao
                ,stAnalise
                ,CASE
                    WHEN pr.ParecerFavoravel = '1' THEN 'Desfavor&aacute;vel'
                    WHEN pr.ParecerFavoravel = '2' THEN 'Favor&aacute;vel'
                END AS Avaliacao
                ,p.SolicitadoReal
                ,(SELECT SUM(qtItem*nrOcorrencia*vlUnitario) FROM SAC.dbo.tbPlanilhaAprovacao pa WHERE pa.IdPRONAC = p.IdPRONAC AND stAtivo = 'S' AND pa.nrFonteRecurso=109) AS SugeridoReal
                ,e.Enquadramento as Enquadramento ")
            ),
            'BDCORPORATIVO.scSAC'
        );
        $slctAnalisados->joinInner(
            array('z'=>'tbDistribuicaoProjetoComissao'),
            't.IdPRONAC = z.idPRONAC',
            array(),
            'BDCORPORATIVO.scSAC'
        );
        $slctAnalisados->joinInner(
            array('p'=>'Projetos'),
            't.idPronac = p.idPronac',
            array(),
            'SAC.dbo'
        );
        $slctAnalisados->joinInner(
            array('pr'=>'Parecer'),
            'pr.idPronac = p.idPronac',
            array(),
            'SAC.dbo'
        );
        $slctAnalisados->joinLeft(
            array('e'=>'Enquadramento'),
            'e.idPronac = p.idPronac',
            array(),
            'SAC.dbo'
        );
        $slctAnalisados->joinInner(
            array('s'=>'Situacao'),
            'p.Situacao = s.Codigo',
            array(),
            'SAC.dbo'
        );
        $slctAnalisados->joinInner(
            array('a'=>'Area'),
            'p.Area = a.Codigo',
            array(),
            'SAC.dbo'
        );
        $slctAnalisados->joinInner(
            array('se'=>'Segmento'),
            'p.Segmento = se.Codigo',
            array(),
            'SAC.dbo'
        );
        $slctAnalisados->joinInner(
            array('n'=>'Nomes'),
            'z.idAgente = n.idAgente',
            array(),
            'AGENTES.dbo'
        );
        $slctAnalisados->joinInner(
            array('x'=>'Agentes'),
            'p.CgcCpf = x.CNPJCPF',
            array(),
            'AGENTES.dbo'
        );
        $slctAnalisados->joinInner(
            array('y'=>'Nomes'),
            'x.idAgente = y.idAgente',
            array(),
            'AGENTES.dbo'
        );
        $slctAnalisados->joinInner(
            array('r'=>'tbReuniao'),
            't.idNrReuniao = r.idNrReuniao',
            array(),
            'SAC.dbo'
        );

        $slctAnalisados->where("z.stDistribuicao = ?", 'A');
        $slctAnalisados->where("pr.idTipoAgente = ?", 6);
        $slctAnalisados->where("pr.stAtivo = ?", 1);
        $slctAnalisados->where("r.stEstado = ?", 0);
        $slctAnalisados->where("y.Status = ?", 0);

        //adiciona quantos filtros foram enviados
        foreach ($where as $coluna => $valor) {
            $slctAnalisados->where($coluna, $valor);
        }

        //RETORNA RESULTADO DA SEGUNDA QUERY - PROJETO ANALISADOS
        if ($analise == "2") {
            if ($count) {
                return $this->fetchAll($slctAnalisados)->count();
            }
            return $this->fetchAll($slctAnalisados);
        }


        /*========================================================*/
        /*================== PROJETOS READEQUADOS ================*/
        /*========================================================*/
        $slctReadequados = $this->select();
        $slctReadequados->setIntegrityCheck(false);
        $slctReadequados->distinct();
        $slctReadequados->from(
            array('p'=>'Projetos'),
            array(new Zend_Db_Expr("
                'Readequa&ccedil;&atilde;o' AS Analise
                ,''
                ,'' AS Componente
                ,p.IdPronac AS idPronac
                ,p.AnoProjeto+p.Sequencial AS Pronac
                ,p.NomeProjeto
                ,CONVERT(VARCHAR(3000), p.ResumoProjeto) as ResumoProjeto
                --,CAST(p.ResumoProjeto AS TEXT) AS ResumoProjeto
                ,i.Nome as Proponente
                ,p.Situacao AS CodSituacao
                ,p.Situacao + ' - ' + s.Descricao AS Situacao
                ,p.UfProjeto AS UF
                ,''
                ,''
                ,p.Area
                ,a.Descricao AS DescArea
                ,p.Segmento AS DescSegmento
                ,se.Descricao
                ,p.DtInicioExecucao
                ,p.DtFimExecucao
                ,'' AS Dias
                ,''
                ,'' AS NrReuniao
                ,''
                ,CASE
                    WHEN pr.ParecerFavoravel = '1' THEN 'Desfavor&aacute;vel'
                    WHEN pr.ParecerFavoravel = '2' THEN 'Favor&aacute;vel'
                END AS Avaliacao
                ,p.SolicitadoReal
                ,(SELECT SUM(qtItem*nrOcorrencia*vlUnitario) FROM SAC.dbo.tbPlanilhaAprovacao pa WHERE pa.IdPRONAC = p.IdPRONAC AND stAtivo = 'S' AND pa.nrFonteRecurso=109) AS SugeridoReal
                ,e.Enquadramento as Enquadramento ")
            ),
            'SAC.dbo'
        );
        $slctReadequados->joinInner(
            array('pr'=>'Parecer'),
            'pr.idPronac = p.idPronac',
            array(),
            'SAC.dbo'
        );
        $slctReadequados->joinLeft(
            array('e'=>'Enquadramento'),
            'e.idPronac = p.idPronac',
            array(),
            'SAC.dbo'
        );
        $slctReadequados->joinInner(
            array('s'=>'Situacao'),
            'p.Situacao = s.Codigo',
            array(),
            'SAC.dbo'
        );
        $slctReadequados->joinInner(
            array('a'=>'Area'),
            'p.Area = a.Codigo',
            array(),
            'SAC.dbo'
        );
        $slctReadequados->joinInner(
            array('se'=>'Segmento'),
            'p.Segmento = se.Codigo',
            array(),
            'SAC.dbo'
        );
        $slctReadequados->joinInner(
            array('i'=>'Interessado'),
            'p.cgccpf = i.cgccpf',
            array(),
            'SAC.dbo'
        );
        $slctReadequados->joinInner(
            array('w1'=>'tbReuniao'),
            'pr.NumeroReuniao = w1.NrReuniao',
            array(),
            'SAC.dbo'
        );

        $slctReadequados->where("pr.TipoParecer = ?", 1);
        $slctReadequados->where("pr.idTipoAgente = ?", 1);
        $slctReadequados->where("w1.stEstado = ?", 0);
        $slctReadequados->where("p.Situacao in (?)", array('C13','C21','C22'));

        //adiciona quantos filtros foram enviados
        foreach ($where as $coluna => $valor) {
            $slctReadequados->where($coluna, $valor);
        }

        /*========================================================*/
        /*========================  UNION  =======================*/
        /*========================================================*/

        $slctUnion = $this->select()
                            ->union(array('('.$slctNaoAnalisados.')', '('.$slctAnalisados.')', '('.$slctReadequados.')'));

        $slctMaster = $this->select();
        $slctMaster->setIntegrityCheck(false);
        $slctMaster->from(
            array('Master'=>$slctUnion),
            array('*')
        );

        //RETORNA QTDE. DE REGISTRO PARA PAGINACAO
        if ($count) {
            return $this->fetchAll($slctMaster)->count();
        }
        //adicionando linha order ao select
        $slctMaster->order($order);

        // paginacao
        if ($tamanho > -1) {
            $tmpInicio = 0;
            if ($inicio > -1) {
                $tmpInicio = $inicio;
            }
            $slctMaster->limit($tamanho, $tmpInicio);
        }
        return $this->fetchAll($slctMaster);
    }

    public function buscaProjetosEmPautaXLS($dados)
    {
        $whereAdd = '';
        $whereReadequacao = '';
        if (isset($dados['pronac']) && !empty($dados['pronac'])) {
            $whereAdd .= ' AND '.$dados['pronac'];
        }
        if (isset($dados['NomeProjeto']) && !empty($dados['NomeProjeto'])) {
            $whereAdd .= ' AND '.$dados['NomeProjeto'];
        }
        if (isset($dados['Codigo']) && !empty($dados['Codigo'])) {
            $whereAdd .= ' AND '.$dados['Codigo'];
        }
        if (isset($dados['Segmento']) && !empty($dados['Segmento'])) {
            $whereAdd .= ' AND '.$dados['Segmento'];
        }
        if (isset($dados['idAgente']) && !empty($dados['idAgente'])) {
            $whereAdd .= ' AND '.$dados['idAgente'];
        }

        $sql = '';

        if ($dados['status'] == 0 || $dados['status'] == 1) {
            $sql .= "(
                SELECT
                    'N&atilde;o analisado' AS Analise
                    ,t.idAgente
                    ,n.Descricao AS Componente
                    ,p.IdPronac
                    ,p.AnoProjeto+p.Sequencial AS Pronac
                    ,p.NomeProjeto
                    ,CAST(p.ResumoProjeto AS TEXT) as ResumoProjeto
                    ,z.Descricao AS Proponente
                    ,p.Situacao as CodSituacao
                    ,p.Situacao + ' - ' + s.Descricao as Situacao
                    ,p.UfProjeto AS UF
                    ,(SELECT TOP 1 m1.Descricao FROM Agentes.dbo.EnderecoNacional x1
                        INNER JOIN Agentes.dbo.UF u1 ON (x1.UF = u1.idUF)
                        INNER JOIN Agentes.dbo.Municipios m1 ON (x1.UF = m1.idUFIBGE AND x1.Cidade = m1.idMunicipioIBGE)
                        WHERE x.idAgente = x1.idAgente) AS Cidade
                    ,t.DtDistribuicao
                    ,p.Area
                    ,a.Descricao AS DescArea
                    ,p.Segmento
                    ,se.Descricao AS DescSegmento
                    ,p.DtInicioExecucao
                    ,p.DtFimExecucao
                    ,DATEDIFF(DAY,t.DtDistribuicao,GETDATE()) AS Dias
                    ,null AS idNrReuniao
                    ,null as NrReuniao
                    ,null AS stAnalise
                    ,CASE
                        WHEN pr.ParecerFavoravel = '1' THEN 'Desfavor&aacute;vel'
                        WHEN pr.ParecerFavoravel = '2' THEN 'Favor&aacute;vel'
                    END AS Avaliacao
                    ,p.SolicitadoReal
                    ,(SELECT SUM(qtItem*nrOcorrencia*vlUnitario) FROM SAC.dbo.tbPlanilhaAprovacao pa WHERE pa.IdPRONAC = p.IdPRONAC AND stAtivo = 'S' and pa.nrFonteRecurso=109) AS SugeridoReal
                    ,e.Enquadramento as Enquadramento
                FROM BDCORPORATIVO.scSAC.tbDistribuicaoProjetoComissao AS t
                    INNER JOIN SAC.dbo.Projetos AS p ON t.idPronac = p.idPronac
                    INNER JOIN SAC.dbo.Parecer AS pr ON pr.idPronac = p.idPronac
                    LEFT JOIN SAC.dbo.Enquadramento AS e ON e.idPronac = p.idPronac
                    INNER JOIN SAC.dbo.Situacao AS s ON p.Situacao = s.Codigo
                    INNER JOIN SAC.dbo.Area AS a ON p.Area = a.Codigo
                    INNER JOIN SAC.dbo.Segmento AS se ON p.Segmento = se.Codigo
                    INNER JOIN AGENTES.dbo.Nomes AS n ON t.idAgente = n.idAgente
                    INNER JOIN AGENTES.dbo.Agentes AS x ON p.CgcCpf = x.CNPJCPF
                    INNER JOIN AGENTES.dbo.Nomes AS z ON x.idAgente = z.idAgente
                WHERE (t.stDistribuicao = 'A')
                AND (pr.stAtivo = 1)
                AND (z.Status = 0)
                AND (p.Situacao in ('C10', 'C30'))
                AND (NOT EXISTS(SELECT TOP 1 * FROM BDCORPORATIVO.scSAC.tbPauta o WHERE o.IdPRONAC = p.IdPronac))
                $whereAdd
            )

            UNION ALL";
        }

        if ($dados['status'] == 0 || $dados['status'] == 2) {
            $sql .= "(
                SELECT
                    'Analisado' AS Analise
                    ,z.idAgente
                    ,n.Descricao AS Componente
                    ,p.IdPronac
                    ,p.AnoProjeto+p.Sequencial AS Pronac
                    ,p.NomeProjeto
                    ,CAST(p.ResumoProjeto AS TEXT) as ResumoProjeto
                    ,y.Descricao AS Proponente
                    ,p.Situacao AS CodSituacao
                    ,p.Situacao + ' - ' + s.Descricao AS Situacao
                    ,p.UfProjeto AS UF
                    ,(SELECT TOP 1 m1.Descricao FROM Agentes.dbo.EnderecoNacional x1
                        INNER JOIN Agentes.dbo.UF u1 ON (x1.UF = u1.idUF)
                        INNER JOIN Agentes.dbo.Municipios m1 ON (x1.UF = m1.idUFIBGE and x1.Cidade = m1.idMunicipioIBGE)
                        WHERE x.idAgente = x1.idAgente) AS Cidade
                    ,z.DtDistribuicao
                    ,p.Area
                    ,a.Descricao AS DescArea
                    ,p.Segmento
                    ,se.Descricao AS DescSegmento
                    ,p.DtInicioExecucao
                    ,p.DtFimExecucao
                    ,DATEDIFF(DAY,z.DtDistribuicao,GETDATE()) AS Dias
                    ,t.idNrReuniao
                    ,r.NrReuniao AS NrReuniao
                    ,stAnalise
                    ,CASE
                        WHEN pr.ParecerFavoravel = '1' THEN 'Desfavor&aacute;vel'
                        WHEN pr.ParecerFavoravel = '2' THEN 'Favor&aacute;vel'
                    END AS Avaliacao
                    ,p.SolicitadoReal
                    ,(SELECT SUM(qtItem*nrOcorrencia*vlUnitario) FROM SAC.dbo.tbPlanilhaAprovacao pa WHERE pa.IdPRONAC = p.IdPRONAC AND stAtivo = 'S' AND pa.nrFonteRecurso=109) AS SugeridoReal
                    ,e.Enquadramento as Enquadramento
                FROM BDCORPORATIVO.scSAC.tbPauta AS t
                    INNER JOIN BDCORPORATIVO.scSAC.tbDistribuicaoProjetoComissao AS z ON t.IdPRONAC = z.idPRONAC
                    INNER JOIN SAC.dbo.Projetos AS p ON t.idPronac = p.idPronac
                    INNER JOIN SAC.dbo.Parecer AS pr ON pr.idPronac = p.idPronac
                    LEFT JOIN SAC.dbo.Enquadramento AS e ON e.idPronac = p.idPronac
                    INNER JOIN SAC.dbo.Situacao AS s ON p.Situacao = s.Codigo
                    INNER JOIN SAC.dbo.Area AS a ON p.Area = a.Codigo
                    INNER JOIN SAC.dbo.Segmento AS se ON p.Segmento = se.Codigo
                    INNER JOIN AGENTES.dbo.Nomes AS n ON z.idAgente = n.idAgente
                    INNER JOIN AGENTES.dbo.Agentes AS x ON p.CgcCpf = x.CNPJCPF
                    INNER JOIN AGENTES.dbo.Nomes AS y ON x.idAgente = y.idAgente
                    INNER JOIN SAC.dbo.tbReuniao AS r ON t.idNrReuniao = r.idNrReuniao
                WHERE (z.stDistribuicao = 'A')
                AND (pr.idTipoAgente = 6)
                AND (pr.stAtivo = 1)
                AND (r.stEstado = 0)
                AND (y.Status = 0)
                $whereAdd
            )

            UNION ALL";
        }

        $sql .= "(
            SELECT
                'Readequa&ccedil;&atilde;o' AS Analise
                ,''
                ,'' AS Componente
                ,p.IdPronac
                ,p.AnoProjeto+p.Sequencial AS Pronac
                ,p.NomeProjeto
                ,CAST(p.ResumoProjeto AS TEXT) as ResumoProjeto
                ,i.Nome AS Proponente
                ,p.Situacao AS CodSituacao
                ,p.Situacao + ' - ' + s.Descricao AS Situacao
                ,p.UfProjeto AS UF
                ,''
                ,''
                ,p.Area
                ,a.Descricao AS DescArea
                ,p.Segmento
                ,se.Descricao AS DescSegmento
                ,p.DtInicioExecucao
                ,p.DtFimExecucao,'' AS Dias
                ,''
                ,'' AS NrReuniao
                ,''
                ,CASE
                    WHEN pr.ParecerFavoravel = '1' THEN 'Desfavor&aacute;vel'
                    WHEN pr.ParecerFavoravel = '2' THEN 'Favor&aacute;vel'
                END AS Avaliacao
                ,p.SolicitadoReal
                ,(SELECT SUM(qtItem*nrOcorrencia*vlUnitario) FROM SAC.dbo.tbPlanilhaAprovacao pa WHERE pa.IdPRONAC = p.IdPRONAC AND stAtivo = 'S' AND pa.nrFonteRecurso=109) AS SugeridoReal
                ,e.Enquadramento as Enquadramento
            FROM SAC.dbo.Projetos AS p
                INNER JOIN SAC.dbo.Parecer AS pr ON pr.idPronac = p.idPronac
                LEFT JOIN SAC.dbo.Enquadramento AS e ON e.idPronac = p.idPronac
                INNER JOIN SAC.dbo.Situacao AS s ON p.Situacao = s.Codigo
                INNER JOIN SAC.dbo.Area AS a ON p.Area = a.Codigo
                INNER JOIN SAC.dbo.Segmento AS se ON p.Segmento = se.Codigo
                INNER JOIN SAC.dbo.Interessado AS i ON p.cgccpf = i.cgccpf
                INNER JOIN SAC.dbo.tbReuniao AS w1 ON pr.NumeroReuniao = w1.NrReuniao
            WHERE (pr.TipoParecer = 1)
            AND (pr.idTipoAgente = 1)
            AND (w1.stEstado = 0)
            AND (p.Situacao in ('C13', 'C21', 'C22'))
            $whereAdd
        )
        ORDER BY 6 ASC ";

//
        $db= Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);
        return $db->fetchAll($sql);
    }

    public function buscarComponente($idPronac)
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);

        $select->from(
                        array('dpc'=>$this->_name),
                        array()
                      );

        $select->joinInner(
                            array('tc'=>'tbTitulacaoConselheiro'),
                            'tc.idAgente = dpc.idAgente',
                            array(),
                            'AGENTES.dbo'
                           );
        $select->joinInner(
                            array('tcAux'=>new Zend_Db_Expr('(select nm.idAgente,nm.Descricao as Nome,tc.cdArea,tc.stTitular from AGENTES.dbo.tbTitulacaoConselheiro tc
 inner join AGENTES.dbo.Nomes nm on tc.idAgente = nm.idAgente)')),
                            'tcAux.cdArea = tc.cdArea',
                            array('tcAux.idAgente','tcAux.Nome','Perfil'=>new Zend_Db_Expr("CASE WHEN tcAux.stTitular =1 THEN 'Componente da Comissao Titular' ELSE 'Componente da Comissao Suplente' END"))
                            );
        $select->joinInner(
                            array('a'=>'Area'),
                            'a.Codigo = tc.cdArea',
                            array('Area'=>'a.Descricao'),
                            'SAC.dbo'
                           );

        $select->where('dpc.idPRONAC = ?', $idPronac);
        return $this->fetchAll($select);
    }

    public function qtdProjetoNaoAnalisados()
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);

        $select->from(
                array('dpc' => "tbdistribuicaoprojetocomissao"),
                array(new Zend_Db_Expr('count(*)')),
                "BDCORPORATIVO.scSAC"
              )
            ->join(
                array('pr' => 'Projetos'),
                'pr.idPronac = dpc.idPronac',
                array(),
                'SAC.dbo'
               )
            ->joinInner(
                array('par'=>"Parecer"),
                'pr.idPronac = par.idPronac
                AND par.DtParecer =(
                    SELECT
                        TOP 1 max( DtParecer )
                    from
                        SAC.dbo.Parecer
                    where
                        IdPRONAC = pr.IdPRONAC
                )',
                array(),
                'SAC.dbo'
            )
            ->join(
                array("s" => "Situacao"),
                'pr.Situacao = s.Codigo',
                array(),
                'SAC.dbo'
            )
            ->join(
                array("ar" => "Area"),
                'pr.Area = ar.Codigo',
                array(),
                'SAC.dbo'
            )
            ->join(
                array("se" => "Segmento"),
                'pr.Segmento = se.Codigo',
                array(),
                'SAC.dbo'
            )
            ->join(
                array("nm" => "Nomes" ),
                'dpc.idAgente = nm.idAgente',
                array(),
                'Agentes.dbo'
            )
            ->join(
                array("ag" => "Agentes"),
                'pr.CgcCpf = ag.CNPJCPF',
                array(),
                "Agentes.dbo"
            )
            ->join(
                array("nm2" => "Nomes"),
                'ag.idAgente = nm2.idAgente',
                array(),
                "Agentes.dbo"
            )
            ->where(
                    "pr.Situacao IN(
                        'C10',
                        'C30'
                    )"
            )
            ->where("dpc.stDistribuicao = 'A'")
            ->where("par.stAtivo = 1")
            ->where("nm2.Status = 0")
            ->where('(
                    NOT EXISTS(
                        SELECT
                            TOP 1 "tbpa" .*
                        FROM
                            "BDCORPORATIVO"."scSAC"."tbPauta" AS "tbpa"
                        WHERE
                            (
                                tbpa.IdPRONAC = pr.idPronac
                            )
                    )
                )')
            ;
        //echo $select;die;

        return $this->fetchAll($select);
    }
}
