<?php

class Recurso_Model_TbRecursoMapper extends MinC_Db_Mapper
{
    public function __construct()
    {
        parent::setDbTable('Recurso_Model_DbTable_TbRecurso');
    }

    public function save($model)
    {
        return parent::save($model);
    }

    public function isProjetoComDireitoARecursoPorFase($idPronac, $siFaseRecursoProjeto)
    {
        try {
            if (empty($idPronac)) {
                throw new Exception("IdPronac n&atilde;o informado");
            }

            $recurso = $this->obterRecursoJaCadastradoPorFase($idPronac, $siFaseRecursoProjeto);

            if ($recurso && !$this->isDireitoASegundoRecurso($recurso)) {
                return false;
            }

            return true;
        } catch (Exception $objException) {
            throw $objException;
        }
    }

    public function obterRecursoJaCadastradoPorFase($idPronac, $siFaseRecursoProjeto)
    {
        try {
            $tbRecurso = new tbRecurso();
            $recurso = $tbRecurso->buscar([
                'IdPRONAC = ?' => $idPronac,
                'siFaseProjeto = ?' => $siFaseRecursoProjeto
            ], ['idRecurso DESC'])->current();

            return ($recurso) ? $recurso->toArray() : false;
        } catch (Exception $objException) {
            throw $objException;
        }
    }

    public function obterProjetoPassivelDeRecurso($idPronac, $cpfCnpj = null, $siFaseRecursoProjeto = null)
    {
        try {
            if (empty($idPronac)) {
                throw new Exception("IdPronac n&atilde;o informado");
            }

            $whereProjeto = [];
            $whereProjeto['projeto.IdPRONAC = ?'] = $idPronac;

            if ($cpfCnpj) {
                $whereProjeto['projeto.CgcCpf = ?'] = $cpfCnpj;
            }

            $dbTableProjetos = new Projeto_Model_DbTable_Projetos();
            $projeto = $dbTableProjetos->obterProjetosESituacao($whereProjeto)->current()->toArray();

            if (empty($projeto)) {
                throw new Exception("Projeto n&atilde;o encontrado");
            }

            $dadosRecurso = $this->obterDadosDoRecurso($projeto, $siFaseRecursoProjeto);

            if (empty($dadosRecurso)) {
                return false;
            }

            return array_merge($projeto, $dadosRecurso);
        } catch (Exception $objException) {
            throw $objException;
        }
    }

    public function obterDadosDoRecurso($projeto, $siFaseRecursoProjeto = null)
    {
        if (empty($siFaseRecursoProjeto)) {
            $siFaseRecursoProjeto = $this->obterFaseRecursoPorSituacao($projeto['situacao']);
        }

        if (empty($siFaseRecursoProjeto)) {
            return false;
        }

        $dadosRecurso['siFaseProjeto'] = $siFaseRecursoProjeto;

        $recurso = $this->obterRecursoJaCadastradoPorFase($projeto['idPronac'], $siFaseRecursoProjeto);

        $dadosRecurso['prazoRecurso'] = $this->obterPrazoPrimeiroRecurso($projeto);

        if (!empty($recurso)) {
            if (!$this->isDireitoASegundoRecurso($recurso)) {
                return false;
            }
            $dadosRecurso['prazoRecurso'] = $this->obterPrazoSegundoRecurso($recurso);
        }

        if ($dadosRecurso['prazoRecurso'] <= 0) {
            return false;
        }

        $dadosRecurso['tpSolicitacao'] = $this->obterTipoDaSolicitacao($projeto['situacao']);
        $dadosRecurso['tpRecurso'] = $this->obterTipoRecurso($recurso);

        return $dadosRecurso;
    }

    public function isDireitoASegundoRecurso($recurso)
    {
        if ($recurso['stEstado'] == Recurso_Model_TbRecurso::SITUACAO_RECURSO_ATIVO
            || $recurso['tpRecurso'] == Recurso_Model_TbRecurso::RECURSO
            || $recurso['tpSolicitacao'] == Recurso_Model_TbRecurso::TIPO_RECURSO_DESISTENCIA_DO_PRAZO_RECURSAL) {
            return false;
        }

        return ($this->obterPrazoSegundoRecurso($recurso) > 0);
    }

    public function obterFaseRecursoPorSituacao($situacao)
    {
        if (!$this->isSituacaoDeRecurso($situacao)) {
            return false;
        }

        if (in_array($situacao, Recurso_Model_TbRecurso::obterSituacoesPassiveisDeRecursoFase2())) {
            return Recurso_Model_TbRecurso::FASE_HOMOLOGACAO;
        }

        return Recurso_Model_TbRecurso::FASE_ADMISSIBILIDADE;
    }

    public function obterTipoDaSolicitacao($situacao)
    {
        $tipoSolicitacao = Recurso_Model_TbRecurso::TIPO_RECURSO_DESISTENCIA_DO_PRAZO_RECURSAL;
        if (in_array($situacao, Recurso_Model_TbRecurso::SITUACOES_RECURSO_PROJETO_INDEFERIDO)) {
            $tipoSolicitacao = Recurso_Model_TbRecurso::TIPO_RECURSO_PROJETO_INDEFERIDO;
        }

        if (in_array($situacao, Recurso_Model_TbRecurso::SITUACOES_RECURSO_ORCAMENTO)) {
            $tipoSolicitacao = Recurso_Model_TbRecurso::TIPO_RECURSO_ORCAMENTO;
        }

        return $tipoSolicitacao;
    }

    public function obterPrazoPrimeiroRecurso($projeto)
    {
        $prazoRecursal = Recurso_Model_TbRecurso::PRAZO_RECURSAL - (int)$projeto['diasSituacao'];

        if (in_array($projeto['situacao'], Recurso_Model_TbRecurso::SITUACOES_RECURSO_PROJETO_INDEFERIDO)) {
            $tbReuniao = new tbreuniao();
            $reuniao = $tbReuniao->obterReuniaoDeAvaliacaoDoProjeto($projeto['idPronac']);
            $prazoRecursal = Recurso_Model_TbRecurso::PRAZO_RECURSAL - (int)$reuniao->diasAnaliseProjeto;
        }

        return $prazoRecursal;
    }

    public function obterPrazoSegundoRecurso($recurso)
    {
        if (empty($recurso['dtAvaliacao'])) {
            return false;
        }

        return $dadosRecurso['prazoRecurso'] = Recurso_Model_TbRecurso::PRAZO_RECURSAL - Data::datadiff($recurso['dtAvaliacao'], 'now');
    }

    public function obterTipoRecurso($recurso)
    {
        if (count($recurso) > 0) {
            return Recurso_Model_TbRecurso::RECURSO;
        }

        return Recurso_Model_TbRecurso::PEDIDO_DE_RECONSIDERACAO;
    }

    public function isSituacaoDeRecurso($situacao)
    {
        if (in_array($situacao, Recurso_Model_TbRecurso::obterSituacoesPassiveisDeRecurso())) {
            return true;
        }

        return false;
    }

    public function inserirRecurso($recurso)
    {
        try {

            if (empty($recurso->idPronac)) {
                throw new Exception("IdPronac &eacute; obrigat&oacute;rio");
            }

            if (empty($recurso->dsSolicitacaoRecurso) || strlen($recurso->dsSolicitacaoRecurso) < 5) {
                throw new Exception ("Texto do recurso &eacute; obrigat&oacute;rio!");
            }

            $projeto = $this->obterProjetoPassivelDeRecurso($recurso->idPronac);

            if (empty($projeto)) {
                throw new Exception("Projeto para recurso n&atilde;o encontrado!");
            }

            $auth = Zend_Auth::getInstance();
            $dados = [
                'IdPRONAC' => $recurso->idPronac,
                'dtSolicitacaoRecurso' => new Zend_Db_Expr('GETDATE()'),
                'dsSolicitacaoRecurso' => $recurso->dsSolicitacaoRecurso,
                'idAgenteSolicitante' => $auth->getIdentity()->IdUsuario,
                'stAtendimento' => 'N',
                'tpSolicitacao' => $projeto['tpSolicitacao'],
                'siFaseProjeto' => $projeto['siFaseProjeto'],
                'tpRecurso' => $projeto['tpRecurso']
            ];

            $tbRecurso = new tbRecurso();
            if (!$tbRecurso->inserir($dados)) {
                return false;
            }

            ProjetoDAO::alterarSituacao($recurso->idPronac, 'D20');

            return true;
        } catch (Exception $objException) {
            throw $objException;
        }
    }

    public function inserirDesistenciaRecursal($recurso)
    {
        try {
            if (empty($recurso->idPronac)) {
                throw new Exception('IdPronac &eacute; obrigat&oacute;rio');
            }

            if (!$recurso->deacordo) {
                throw new Exception('Voc&ecirc; deve concordar com os termos para desistir do recurso');
            }

            $projeto = $this->obterProjetoPassivelDeRecurso($recurso->idPronac);

            if (empty($projeto)) {
                throw new Exception("Projeto para recurso n&atilde;o encontrado!");
            }

            $auth = Zend_Auth::getInstance();
            $dados = array(
                'IdPRONAC' => $recurso->idPronac,
                'dtSolicitacaoRecurso' => new Zend_Db_Expr('GETDATE()'),
                'dsSolicitacaoRecurso' => 'Desist&ecirc;ncia do prazo recursal',
                'idAgenteSolicitante' => $auth->getIdentity()->IdUsuario,
                'stAtendimento' => 'N',
                'siFaseProjeto' => $projeto['siFaseProjeto'],
                'siRecurso' => 0,
                'tpSolicitacao' => Recurso_Model_TbRecurso::TIPO_RECURSO_DESISTENCIA_DO_PRAZO_RECURSAL,
                'tpRecurso' => $projeto['tpRecurso'],
                'stAnalise' => null,
                'stEstado' => Recurso_Model_TbRecurso::SITUACAO_RECURSO_INATIVO
            );

            $tbRecurso = new tbRecurso();
            $tbRecurso->inserir($dados);
        } catch (Exception $objException) {
            throw $objException;
        }
    }

}
