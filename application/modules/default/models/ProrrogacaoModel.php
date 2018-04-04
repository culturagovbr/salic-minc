<?php

class ProrrogacaoModel implements ModelInterface
{

    /**
     *
     */
    const DEFERIDO = 'N';

    /**
     *
     */
    const INDEFERIDO = 'I';

    /**
     *
     */
    const EM_ANALISE = 'A';

    /**
     *
     */
    const PROCESSADO = 'S';

    /**
     *
     */
    const PUBLICAR_DOU = 'publicar_dou';

    /**
     *
     */
    const ENCAMINHAR_COORDENADOR = 'encaminhar_coordenador';

    /**
     *
     * @var boolean
     */
    private $hasErrors = false;

    /**
     *
     * @var array
     */
    private $errors = null;

    /**
     *
     * @var Prorrogacao
     */
    private $table = null;

    /**
     *
     */
    public function __construct()
    {
        $this->table = new Prorrogacao();
    }

    /**
     *
     * @return type
     */
    public function hasErros()
    {
        return (boolean) $this->getErros();
    }

    /**
     *
     * @return type
     */
    public function getErros()
    {
        return $this->errors;
    }

    /**
     *
     * @return type
     */
    public function setErros($erros)
    {
        $this->errors = $erros;
        return $this;
    }

    /**
     *
     */
    public function salvar()
    {
        throw new Exception('Não implementado');
    }

    /**
     *
     */
    public function atualizar()
    {
        throw new Exception('Não implementado');
    }

    /**
     *
     * @param int $id
     */
    public function buscar($idProrrogacao = null)
    {
        if ($idProrrogacao) {
            return $this->table->buscarDadosProrrogacao($idProrrogacao);
        }
        throw new Exception('Não Implementado');
    }

    /**
     *
     * @param int $id
     */
    public function deletar($idProrrogacao)
    {
        $this->table->delete("idProrrogacao = $idProrrogacao");
    }

    /**
     *
     * @throws InvalidArgumentException
     */
    private function validarIndeferimento()
    {
        foreach (func_get_args() as $argument) {
            if (empty($argument)) {
                throw new InvalidArgumentException();
            }
        }
    }

    /**
     *
     * @param type $id
     * @param type $observacao
     * @param type $atendimento
     * @param type $logon
     * @param type $dataInicial
     * @param type $dataFinal
     * @param datetime $opcaoDeferimento Decide se vai deferir enviando para análise do coordenador
     * ou diretamente para o publicação do diário oficial
     * @throws InvalidArgumentException
     */
    private function validarDeferimento($id, $observacao, $atendimento, $logon, $dataInicial, $dataFinal, $idPronac, $opcaoDeferimento)
    {
        $this->validarIndeferimento($id, $observacao, $atendimento, $logon, $dataInicial, $dataFinal, $idPronac, $opcaoDeferimento);

        $dataI = explode('/', $dataInicial);
        $dtI = checkdate($dataI[1], $dataI[0], $dataI[2]);
        if (!$dtI) {
            throw new DateException('Data inicinal inválida.');
        }

        $dataF = explode('/', $dataFinal);
        $dtF = checkdate($dataF[1], $dataF[0], $dataF[2]);
        if (!$dtF) {
            throw new DateException('Data final inválida.');
        }

        $checklistSolicitacaoProrrogacaoPrazo = new paChecklistSolicitacaoProrrogacaoPrazo();
        $this->setErros($checklistSolicitacaoProrrogacaoPrazo->checkSolicitacao($idPronac, Data::dataAmericana($dataInicial), Data::dataAmericana($dataFinal), 'A'));
        if ($this->hasErros()) {
            throw new Exception('A procedimento armazenado retornou erros do banco de dados');
        }
    }

    /**
     *
     * @param type $idProrrogacao
     * @param type $observacao
     * @param type $atendimento
     * @param type $logon
     * @throws InvalidArgumentException
     */
    public function indeferir($idProrrogacao, $observacao, $atendimento, $logon)
    {
        $this->validarIndeferimento($idProrrogacao, $observacao, $atendimento, $logon);
        $prorrogacaoRow = $this->buscar($idProrrogacao);
        $prorrogacaoRow->Observacao = $observacao;
        $prorrogacaoRow->Atendimento = $atendimento;
        $prorrogacaoRow->Logon = $logon;
        $prorrogacaoRow->save();
    }

    /**
     *
     * @param int $idProrrogacao
     * @param string $observacao
     * @param string $atendimento
     * @param int $logon
     * @param datetime $dataInicial
     * @param datetime $dataFinal
     * @param datetime $opcaoDeferimento Decide se vai deferir enviando para análise do coordenador
     * ou diretamente para o publicação do diário oficial
     * @throws InvalidArgumentException
     */
    public function deferir($idProrrogacao, $observacao, $atendimento, $logon, $dataInicial, $dataFinal, $opcaoDeferimento)
    {
        $prorrogacaoRow = $this->buscar($idProrrogacao);
        $this->validarDeferimento($idProrrogacao, $observacao, $atendimento, $logon, $dataInicial, $dataFinal, $prorrogacaoRow->idPronac, $opcaoDeferimento);

        if (!$this->hasErros()) {
            $grupoUsuarioLogado = new Zend_Session_Namespace('GrupoAtivo');
            if (self::PUBLICAR_DOU === $opcaoDeferimento || PerfilModel::COORDENADOR_DE_ACOMPANHAMENTO == $grupoUsuarioLogado->codGrupo) {
                if (PerfilModel::COORDENADOR_DE_ACOMPANHAMENTO == $grupoUsuarioLogado->codGrupo) {
                    //Se for o coordenador de acompanhamento deferindo a prorrogação, o campo Atendimento passará de 'N' para 'S' para que a rotina de banco não duplique a informação;
                    $atendimento = ProrrogacaoModel::PROCESSADO;
                }
                $this->salvarAprovando($prorrogacaoRow, $observacao, $atendimento, $logon, $dataInicial, $dataFinal);
            } elseif (self::ENCAMINHAR_COORDENADOR === $opcaoDeferimento) {
                $this->salvarEncaminhandoAoCoordenador($prorrogacaoRow, $observacao, $atendimento, $logon, $dataInicial, $dataFinal);
            }
        }
    }

    /**
     *
     * @param type $prorrogacaoRow
     * @param type $observacao
     * @param type $atendimento
     * @param type $logon
     * @param type $dataInicial
     * @param type $dataFinal
     */
    public function atualizaAnaliseProrrogacao($prorrogacaoRow, $observacao, $atendimento, $logon, $dataInicial, $dataFinal)
    {
        $prorrogacaoRow->Observacao = $observacao;
        $prorrogacaoRow->Atendimento = $atendimento;
        $prorrogacaoRow->Logon = $logon;
        $prorrogacaoRow->DtInicio = Data::dataAmericana($dataInicial);
        $prorrogacaoRow->DtFinal = Data::dataAmericana($dataFinal);
        $prorrogacaoRow->save();
    }

    /**
     *
     * @param Zend_Db_Table_Row $prorrogacaoRow
     * @param string $observacao
     * @param string $atendimento
     * @param int $logon
     * @param datetime $dataInicial
     * @param datetime $dataFinal
     */
    private function salvarAprovando($prorrogacaoRow, $observacao, $atendimento, $logon, $dataInicial, $dataFinal)
    {
        try {
            $this->atualizaAnaliseProrrogacao($prorrogacaoRow, $observacao, $atendimento, $logon, $dataInicial, $dataFinal);

            $projetoTable = new Projetos();
            $projetoTable->alterarSituacao($prorrogacaoRow->idPronac, null, SituacaoModel::AGUARDANDO_ELABORACAO_DE_PROTARIA_DE_PRORROGACAO);

            $aprovacaoModel = new AprovacaoModel();
            $aprovacaoModel
              ->setIdPronac($prorrogacaoRow->idPronac)
              ->setIdProrrogacao($prorrogacaoRow->idProrrogacao)
              ->setAnoProjeto($prorrogacaoRow->AnoProjeto)
              ->setSequencial($prorrogacaoRow->Sequencial)
              ->setTipo(AprovacaoModel::TIPO_PRORROGACAO)
              ->setResumo('Parecer favorável para prorrogação')
              ->setUsuarioLogado($logon);
            $aprovacaoModel->salvar();
        } catch (Exception $exception) {
            throw new Exception('Erro ao tentar prorrogar o prazo de captação', null, $exeception);
        }
    }

    /**
     *
     * @param Zend_Db_Table_Row $prorrogacaoRow
     * @param string $observacao
     * @param string $atendimento
     * @param int $logon
     * @param datetime $dataInicial
     * @param datetime $dataFinal
     */
    private function salvarEncaminhandoAoCoordenador($prorrogacaoRow, $observacao, $atendimento, $logon, $dataInicial, $dataFinal)
    {
        $this->atualizaAnaliseProrrogacao($prorrogacaoRow, $observacao, $atendimento, $logon, $dataInicial, $dataFinal);
    }

    /**
     *
     * @param int $idProrrogacao
     * @return object
     */
    public function getProjeto($idProrrogacao)
    {
        $projetoTable = new Projetos();
        $select = $projetoTable
          ->select()
          ->setIntegrityCheck(false)
          ->from(array('projeto' => $projetoTable->info(Zend_Db_Table::NAME)), '*')
          ->joinInner(array('prorrogacao' => $this->table->info(Zend_Db_Table::NAME)), 'prorrogacao.idPronac = projeto.idPRONAC', array())
          ->where('idProrrogacao = ?', $idProrrogacao)
        ;
        $projetoTable->getDefaultAdapter()->setFetchMode(Zend_DB :: FETCH_OBJ);
        return $projetoTable->getDefaultAdapter()->fetchRow($select);
    }
}
