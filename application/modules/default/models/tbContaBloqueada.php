<?php

class tbContaBloqueada extends MinC_Db_Table_Abstract
{
    protected $_banco = "SAC";
    protected $_schema = 'SAC';
    protected $_name = "tbContaBloqueada";

    public function buscaCompelta($where=array(), $order=array(), $tamanho=-1, $inicio=-1)
    {
        $slct = $this->select();
        $slct->setIntegrityCheck(false);
        $slct->from(
            array('cb'=>$this->_name),
                    array('*')
        );

        $select->joinInner(
            array('pr'=>'Projetos'),
                           'pr.IdPRONAC = cb.IdPRONAC',
                           array()
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

    public function buscarContasDesbloqueioSistemico($where=array(), $order=array(), $tamanho=-1, $inicio=-1, $blnRetornaSelect=false)
    {
        $slct = $this->select();
        $slct->setIntegrityCheck(false);
        $slct->from(
            array('cb'=>$this->_name),
                    array('*')
        );

        $slct->joinInner(
            array('pr'=>'Projetos'),
                           new Zend_Db_Expr("pr.IdPRONAC = cb.IdPRONAC
                                AND pr.CgcCpf NOT IN(SELECT TOP 1 CgcCpf FROM SAC..Inabilitado WHERE Habilitado='N' and CgcCpf=pr.CgcCpf)
                                AND cb.IdPRONAC IN
                                    (
                                        CASE
                                            WHEN (pr.Situacao IN ('E15','E23')) AND (pr.IdPRONAC IN(SELECT idPronac FROM SAC..Prorrogacao WHERE Atendimento = 'N' AND idPronac=pr.IdPRONAC))
                                                    THEN pr.IdPRONAC
                                            WHEN (pr.Situacao NOT IN ('E15','E23'))
                                                    THEN pr.IdPRONAC
                                            ELSE
                                                    NULL
                                            END
                                     )"),
                           array(new Zend_Db_Expr('(pr.AnoProjeto + pr.Sequencial) AS PRONAC'),
                                 'pr.NomeProjeto',
                                 'pr.DtInicioExecucao',
                                 'pr.DtFimExecucao')
                           );
        $slct->joinInner(
            array("ap"=>"Aprovacao"),
            new Zend_Db_Expr("ap.idPronac = pr.idPronac AND ap.DtAprovacao in (select TOP 1 max(DtAprovacao) from SAC..Aprovacao where IdPRONAC = pr.IdPRONAC AND DtInicioCaptacao IS NOT NULL)"),
                            array(
                                    'ap.DtInicioCaptacao',
                                    'ap.DtFimCaptacao')
                          );
        $slct->joinLeft(
            array("inb"=>"Inabilitado"),
            new Zend_Db_Expr("inb.CgcCpf = pr.CgcCpf AND inb.AnoProjeto in (select TOP 1 max(AnoProjeto) from SAC..Inabilitado where CgcCpf = pr.CgcCpf)"),
                            array("Habilitado")
                          );
        //adiciona quantos filtros foram enviados
        foreach ($where as $coluna => $valor) {
            $slct->where($coluna, $valor);
        }
        //adicionando linha order ao select
        $slct->order($order);

        if ($blnRetornaSelect) {
            return $slct;
            die;
        }
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

    public function queryContasDesbloqueioSistemico($where=array(), $order=array(), $tamanho=-1, $inicio=-1, $blnRetornaSelect=false)
    {
        $slct = $this->select();
        $slct->setIntegrityCheck(false);
        $slct->from(
            array('a'=>$this->_name),
                    array('a.idContaBloqueada')
        );

        $slct->joinInner(
            array('b'=>'Projetos'),
            new Zend_Db_Expr("b.IdPRONAC = b.IdPRONAC
                                AND b.CgcCpf NOT IN(SELECT TOP 1 CgcCpf FROM SAC..Inabilitado WHERE Habilitado='N' and CgcCpf=b.CgcCpf)
                                AND a.IdPRONAC IN
                                    (
                                        CASE
                                            WHEN (b.Situacao IN ('E15','E23')) AND (b.IdPRONAC IN(SELECT idPronac FROM SAC..Prorrogacao WHERE Atendimento = 'N' AND idPronac=b.IdPRONAC))
                                                    THEN b.IdPRONAC
                                            WHEN (b.Situacao NOT IN ('E15','E23'))
                                                    THEN b.IdPRONAC
                                            ELSE
                                                    NULL
                                            END
                                     )"),
                           array()
                           );

        //adiciona quantos filtros foram enviados
        foreach ($where as $coluna => $valor) {
            $slct->where($coluna, $valor);
        }
        //adicionando linha order ao select
        $slct->order($order);

        if ($blnRetornaSelect) {
            return $slct;
            die;
        }
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

    public function buscarContasDesbloqueioJudicial($where=array(), $order=array(), $tamanho=-1, $inicio=-1)
    {
        $slct = $this->select();
        $slct->setIntegrityCheck(false);
        $slct->from(
            array('cb'=>$this->_name),
                    array('*')
        );

        $slct->joinInner(
            array('pr'=>'Projetos'),
                           "pr.IdPRONAC = cb.IdPRONAC",
                           array(new Zend_Db_Expr('(pr.AnoProjeto + pr.Sequencial) AS PRONAC'),
                                 'pr.NomeProjeto',
                                 'pr.DtInicioExecucao',
                                 'pr.DtFimExecucao')
                           );
        $slct->joinInner(
            array("ap"=>"Aprovacao"),
            new Zend_Db_Expr("ap.idPronac = pr.idPronac AND ap.DtAprovacao in (select TOP 1 max(DtAprovacao) from SAC..Aprovacao where IdPRONAC = pr.IdPRONAC AND DtInicioCaptacao IS NOT NULL)"),
                            array('ap.DtInicioCaptacao',
                                  'ap.DtFimCaptacao')
                          );
        $slct->joinLeft(
            array("inb"=>"Inabilitado"),
            new Zend_Db_Expr("inb.CgcCpf = pr.CgcCpf AND inb.AnoProjeto in (select TOP 1 max(AnoProjeto) from SAC..Inabilitado where CgcCpf = pr.CgcCpf)"),
                            array("Habilitado")
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

    public function buscarContasBloqueadas($where=array(), $order=array(), $tamanho=-1, $inicio=-1)
    {
        $slcContasDesbloqueioSistemico = $this->queryContasDesbloqueioSistemico($where, null, null, null, true);
        //x($slcContasDesbloqueioSistemico->assemble());
        $slct = $this->select();
        $slct->setIntegrityCheck(false);
        $slct->from(
            array('cb'=>$this->_name),
                    array('*')
        );
        /*$slct2->joinInner(
                array('teste' => $slcContasDesbloqueioSistemico)
        );*/

        $slct->joinInner(
            array('pr'=>'Projetos'),
                           "pr.IdPRONAC = cb.IdPRONAC",
                           array(new Zend_Db_Expr('(pr.AnoProjeto + pr.Sequencial) AS PRONAC'),
                                 'pr.NomeProjeto',
                                 'pr.DtInicioExecucao',
                                 'pr.DtFimExecucao')
                           );
        $slct->joinInner(
            array("ap"=>"Aprovacao"),
            new Zend_Db_Expr("ap.idPronac = pr.idPronac AND ap.DtAprovacao in (select TOP 1 max(DtAprovacao) from SAC..Aprovacao where IdPRONAC = pr.IdPRONAC AND DtInicioCaptacao IS NOT NULL)"),
                            array(
                                    'ap.DtInicioCaptacao',
                                    'ap.DtFimCaptacao')
                          );
        $slct->joinLeft(
            array("inb"=>"Inabilitado"),
            new Zend_Db_Expr("inb.CgcCpf = pr.CgcCpf AND inb.AnoProjeto in (select TOP 1 max(AnoProjeto) from SAC..Inabilitado where CgcCpf = pr.CgcCpf)"),
                            array("Habilitado")
                          );
        //$slct->where("cb.idContaBloqueada not in ({$slcContasDesbloqueioSistemico})");
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
