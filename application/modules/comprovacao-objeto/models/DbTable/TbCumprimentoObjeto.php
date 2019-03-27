<?php

class ComprovacaoObjeto_Model_DbTable_TbCumprimentoObjeto extends MinC_Db_Table_Abstract
{
    const SITUACAO_PROPONENTE = 1;

    const SI_COM_PROPONENTE = 1;
    const SI_AGUARDANDO_ANALISE = 2;
    const SI_PARA_AVALIACAO_TECNICO = 3;
    const SI_EM_AVALIACAO_TECNICO = 4;
    const SI_PARA_AVALIACAO_COORDENADOR = 5;
    const SI_FINALIZADO_PELO_COORDENADOR = 6;

    const OBJETO_PARCIALMENTE_APROVADO = 'P';
    const OBJETO_APROVADO = 'A';
    const OBJETO_REPROVADO = 'R';

    protected $_banco = "SAC";
    protected $_schema = "SAC";
    protected $_name = "tbCumprimentoObjeto";
    protected $_primary = "idCumprimentoObjeto";

    private $idCumprimentoObjeto;
    private $idPronac;
    private $idUsuario;
    private $situacao;
    private $etapasConcluidas;
    private $medidasAcessibilidade;
    private $medidasFruicao;
    private $medidasPreventivas;
    private $totalEmpregosDiretos;
    private $totalEmpregosIndiretos;
    private $empregosGerados;
    private $medidasAcessibilidadeImagens;
    private $medidasFruicaoImagens;
    private $medidasPreventivasImagens;
    private $dataCadastro;

    /**
     *
     * @param int $idPronac
     * @param int $idUsuario
     * @param type $etapasConcluidas
     * @param type $medidasAcessibilidade
     * @param type $medidasFruicao
     * @param type $medidasPreventivas
     * @param type $totalEmpregosDiretos
     * @param type $totalEmpregosIndiretos
     * @param type $empregosGerados
     */
    public function __construct(
        $idPronac = null,
        $idUsuario = null,
        $situacao = null,
        $etapasConcluidas = null,
        $medidasAcessibilidade = null,
        $medidasFruicao = null,
        $medidasPreventivas = null,
        $totalEmpregosDiretos = null,
        $totalEmpregosIndiretos = null,
        $empregosGerados = null,
        $medidasAcessibilidadeImagens = null,
        $medidasFruicaoImagens = null,
        $medidasPreventivasImagens = null
    )
    {
        parent::__construct();
        $this->idPronac = $idPronac;
        $this->idUsuario = $idUsuario;
        $this->situacao = $situacao;
        $this->etapasConcluidas = $etapasConcluidas;
        $this->medidasAcessibilidade = $medidasAcessibilidade;
        $this->medidasFruicao = $medidasFruicao;
        $this->medidasPreventivas = $medidasPreventivas;
        $this->totalEmpregosDiretos = $totalEmpregosDiretos;
        $this->totalEmpregosIndiretos = $totalEmpregosIndiretos;
        $this->empregosGerados = $empregosGerados;
        $this->medidasAcessibilidadeImagens = $medidasAcessibilidadeImagens;
        $this->medidasFruicaoImagens = $medidasFruicaoImagens;
        $this->medidasPreventivasImagens = $medidasPreventivasImagens;
    }

    public function getIdCumprimentoObjeto()
    {
        return $this->idCumprimentoObjeto;
    }

    public function getIdPronac()
    {
        return $this->idPronac;
    }

    public function getIdUsuario()
    {
        return $this->idUsuario;
    }

    public function getSituacao()
    {
        return $this->situacao;
    }

    public function getEtapasConcluidas()
    {
        return $this->etapasConcluidas;
    }

    public function getMedidasAcessibilidade()
    {
        return $this->medidasAcessibilidade;
    }

    public function getMedidasFruicao()
    {
        return $this->medidasFruicao;
    }

    public function getMedidasPreventivas()
    {
        return $this->medidasPreventivas;
    }

    public function getTotalEmpregosDiretos()
    {
        return $this->totalEmpregosDiretos ? $this->totalEmpregosDiretos : 0;
    }

    public function getTotalEmpregosIndiretos()
    {
        return $this->totalEmpregosIndiretos ? $this->totalEmpregosIndiretos : 0;
    }

    public function getEmpregosGerados()
    {
        return $this->empregosGerados;
    }

    public function getMedidasAcessibilidadeImagens()
    {
        if (null === $this->medidasAcessibilidadeImagens) {
            if (null === $this->getIdCumprimentoObjeto()) {
                $this->setMedidasAcessibilidadeImagens(new ArrayObject());
                return $this->medidasAcessibilidadeImagens;
            }
            $cumprimentoObjetoArquivoModel = new ComprovacaoObjeto_Model_DbTable_TbCumprimentoObjetoXArquivo();
            $cumprimentoObjetoArquivoModel->setIdCumprimentoObjeto($this->getIdCumprimentoObjeto());
            $cumprimentoObjetoArquivoModel->setPosicao(
                ComprovacaoObjeto_Model_DbTable_TbCumprimentoObjetoXArquivo::ACESSIBILIDADE_FISICA
            );
            $this->setMedidasAcessibilidadeImagens($cumprimentoObjetoArquivoModel->buscar());
        }
        return $this->medidasAcessibilidadeImagens;
    }

    public function getMedidasFruicaoImagens()
    {
        if (null === $this->medidasFruicaoImagens) {
            if (null === $this->getIdCumprimentoObjeto()) {
                $this->setMedidasFruicaoImagens(new ArrayObject());
                return $this->medidasFruicaoImagens;
            }
            $cumprimentoObjetoArquivoModel = new ComprovacaoObjeto_Model_DbTable_TbCumprimentoObjetoXArquivo();
            $cumprimentoObjetoArquivoModel->setIdCumprimentoObjeto($this->getIdCumprimentoObjeto());
            $cumprimentoObjetoArquivoModel->setPosicao(
                ComprovacaoObjeto_Model_DbTable_TbCumprimentoObjetoXArquivo::FRUICAO_DE_DEMOCRATIZACAO_AO_ACESSO_PUBLICO
            );
            $this->setMedidasFruicaoImagens($cumprimentoObjetoArquivoModel->buscar());
        }
        return $this->medidasFruicaoImagens;
    }

    public function getMedidasPreventivasImagens()
    {
        if (null === $this->medidasPreventivasImagens) {
            if (null === $this->getIdCumprimentoObjeto()) {
                $this->setMedidasPreventivasImagens(new ArrayObject());
                return $this->medidasPreventivasImagens;
            }
            $cumprimentoObjetoArquivoModel = new ComprovacaoObjeto_Model_DbTable_TbCumprimentoObjetoXArquivo();
            $cumprimentoObjetoArquivoModel->setIdCumprimentoObjeto($this->getIdCumprimentoObjeto());
            $cumprimentoObjetoArquivoModel->setPosicao(
                ComprovacaoObjeto_Model_DbTable_TbCumprimentoObjetoXArquivo::IMPACTOS_AMBIENTAIS
            );
            $this->setMedidasPreventivasImagens($cumprimentoObjetoArquivoModel->buscar());
        }
        return $this->medidasPreventivasImagens;
    }

    public function getDataCadastro()
    {
        return $this->dataCadastro;
    }

    public function setIdCumprimentoObjeto($idCumprimentoObjeto)
    {
        $this->idCumprimentoObjeto = $idCumprimentoObjeto;
        return $this;
    }

    public function setIdPronac($idPronac)
    {
        $this->idPronac = $idPronac;
        return $this;
    }

    public function setIdUsuario($idUsuario)
    {
        $this->idUsuario = $idUsuario;
        return $this;
    }

    public function setSituacao($situacao)
    {
        $this->situacao = $situacao;
        return $this;
    }

    public function setEtapasConcluidas($etapasConcluidas)
    {
        $this->etapasConcluidas = $etapasConcluidas;
        return $this;
    }

    public function setMedidasAcessibilidade($medidasAcessibilidade)
    {
        $this->medidasAcessibilidade = $medidasAcessibilidade;
        return $this;
    }

    public function setMedidasFruicao($medidasFruicao)
    {
        $this->medidasFruicao = $medidasFruicao;
        return $this;
    }

    public function setMedidasPreventivas($medidasPreventivas)
    {
        $this->medidasPreventivas = $medidasPreventivas;
        return $this;
    }

    public function setTotalEmpregosDiretos($totalEmpregosDiretos)
    {
        $this->totalEmpregosDiretos = $totalEmpregosDiretos;
        return $this;
    }

    public function setTotalEmpregosIndiretos($totalEmpregosIndiretos)
    {
        $this->totalEmpregosIndiretos = $totalEmpregosIndiretos;
        return $this;
    }

    public function setEmpregosGerados($empregosGerados)
    {
        $this->empregosGerados = $empregosGerados;
        return $this;
    }

    public function setMedidasAcessibilidadeImagens($medidasAcessibilidadeImagens)
    {
        $this->medidasAcessibilidadeImagens = $medidasAcessibilidadeImagens;
        return $this;
    }

    public function setMedidasFruicaoImagens($medidasFruicaoImagens)
    {
        $this->medidasFruicaoImagens = $medidasFruicaoImagens;
        return $this;
    }

    public function setMedidasPreventivasImagens($medidasPreventivasImagens)
    {
        $this->medidasPreventivasImagens = $medidasPreventivasImagens;
        return $this;
    }

    public function setDataCadastro($dataCadastro)
    {
        $this->dataCadastro = $dataCadastro;
        return $this;
    }

    /**
     * Valida se todos os atributos obrigat�rios para persistir o objeto est�o
     * presentes no mesmo
     * @throws InvalidArgumentException
     */
    private function validarCadastrar()
    {
        if (!$this->idPronac) {
            throw new InvalidArgumentException('Necess&aacute;rio fornecer o Pronac');
        }
        if (!$this->idUsuario) {
            throw new InvalidArgumentException('Necess&aacute;rio fornecer o usu&aacute;rio logado no sistema');
        }
    }

    /**
     *
     */
    public function saveOrUpdate()
    {
        $this->validarCadastrar();
        $cumprimentoObjetoClone = clone($this);
        $cumprimentoObjetoRow = $this->buscarCumprimentoObjeto(
            array(
                'idPronac=?' => $this->idPronac,
                'siCumprimentoObjeto=?' => self::SITUACAO_PROPONENTE)
        );

        if (empty($cumprimentoObjetoRow)) {
            $cumprimentoObjetoRow = $this->createRow();
            $cumprimentoObjetoRow->idPronac = $this->getIdPronac();
            $cumprimentoObjetoRow->dtCadastro = new Zend_Db_Expr('GETDATE()');
            if ($this->getSituacao()) {
                $cumprimentoObjetoRow->siCumprimentoObjeto = $this->getSituacao();
            }
        }

        $cumprimentoObjetoRow->dsEtapasConcluidas = $cumprimentoObjetoClone->getEtapasConcluidas();
        $cumprimentoObjetoRow->dsMedidasAcessibilidade = $cumprimentoObjetoClone->getMedidasAcessibilidade();
        $cumprimentoObjetoRow->dsMedidasFruicao = $cumprimentoObjetoClone->getMedidasFruicao();
        $cumprimentoObjetoRow->dsMedidasPreventivas = $cumprimentoObjetoClone->getMedidasPreventivas();
        $cumprimentoObjetoRow->idUsuarioCadastrador = $cumprimentoObjetoClone->getIdUsuario();
        $cumprimentoObjetoRow->qtEmpregosDiretos = $cumprimentoObjetoClone->getTotalEmpregosDiretos();
        $cumprimentoObjetoRow->qtEmpregosIndiretos = $cumprimentoObjetoClone->getTotalEmpregosIndiretos();
        $cumprimentoObjetoRow->dsGeracaoEmpregos = $cumprimentoObjetoClone->getEmpregosGerados();

        $idCumprimentoDoObjeto = $cumprimentoObjetoRow->save();
        $cumprimentoObjetoXArquivoModel = new ComprovacaoObjeto_Model_DbTable_TbCumprimentoObjetoXArquivo();
        $cumprimentoObjetoXArquivoModel->save($idCumprimentoDoObjeto);
    }

    public function buscarCumprimentoObjeto($where = [], $all = false, $order = array())
    {
        // criando objeto do tipo select
        $select = $this
            ->select()
            ->from(
                $this->_name,
                array(
                    'idCumprimentoObjeto',
                    'idPronac',
                    'dtCadastro',
                    'dsEtapasConcluidas' => new Zend_Db_Expr('CAST(dsEtapasConcluidas AS TEXT)'),
                    'dsMedidasAcessibilidade' => new Zend_Db_Expr('CAST(dsMedidasAcessibilidade AS TEXT)'),
                    'dsMedidasFruicao' => new Zend_Db_Expr('CAST(dsMedidasFruicao AS TEXT)'),
                    'dsMedidasPreventivas' => new Zend_Db_Expr('CAST(dsMedidasPreventivas AS TEXT)'),
                    'dsInformacaoAdicional' => new Zend_Db_Expr('CAST(dsInformacaoAdicional AS TEXT)'),
                    'dsOrientacao' => new Zend_Db_Expr('CAST(dsOrientacao AS TEXT)'),
                    'dsConclusao' => new Zend_Db_Expr('CAST(dsConclusao AS TEXT)'),
                    'stResultadoAvaliacao',
                    'idUsuarioCadastrador',
                    'idTecnicoAvaliador',
                    'siCumprimentoObjeto',
                    'idChefiaImediata',
                    'qtEmpregosDiretos',
                    'qtEmpregosIndiretos',
                    'dsGeracaoEmpregos' => new Zend_Db_Expr('CAST(dsGeracaoEmpregos AS TEXT)'),
                )
            );

        // adicionando clausulas where
        foreach ($where as $coluna => $valor) {
            $select->where($coluna, $valor);
        }

        //adicionando linha order ao select
        $select->order($order);

        // retornando os registros
        if ($all) {
            return $this->fetchAll($select);
        } else {
            $cumprimentoObjetoRow = $this->fetchRow($select);
            if ($cumprimentoObjetoRow) {
                $this->setIdCumprimentoObjeto($cumprimentoObjetoRow->idCumprimentoObjeto)
                    ->setIdPronac($cumprimentoObjetoRow->idPronac)
                    ->setDataCadastro($cumprimentoObjetoRow->dtCadastro)
                    ->setEtapasConcluidas($cumprimentoObjetoRow->dsEtapasConcluidas)
                    ->setMedidasAcessibilidade($cumprimentoObjetoRow->dsMedidasAcessibilidade)
                    ->setMedidasFruicao($cumprimentoObjetoRow->dsMedidasFruicao)
                    ->setMedidasPreventivas($cumprimentoObjetoRow->dsMedidasPreventivas)
                    ->setIdUsuario($cumprimentoObjetoRow->idUsuarioCadastrador)
                    ->setSituacao($cumprimentoObjetoRow->siCumprimentoObjeto)
                    ->setTotalEmpregosDiretos($cumprimentoObjetoRow->qtEmpregosDiretos)
                    ->setTotalEmpregosIndiretos($cumprimentoObjetoRow->qtEmpregosIndiretos)
                    ->setEmpregosGerados($cumprimentoObjetoRow->dsGeracaoEmpregos);
            }
            return $cumprimentoObjetoRow;
        }
    }

    public function listaRelatorios($where = array(), $order = array(), $tamanho = -1, $inicio = -1, $qtdeTotal = false, $filtro = '')
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(
            array('a' => $this->_name),
            array(
                'a.dtCadastro',
                'a.idTecnicoAvaliador',
                'a.stResultadoAvaliacao',
                'a.DtEnvioDaPrestacaoContas'
            ),
            $this->_schema
        );
        $select->joinInner(
            array('b' => 'Projetos'),
            'a.idPronac = b.IdPRONAC',
            array(
                new Zend_Db_Expr('b.IdPRONAC,b.AnoProjeto+b.Sequencial as Pronac'),
                'b.NomeProjeto',
                'b.UfProjeto',
                'b.Mecanismo',
                'b.Situacao'
            ),
            $this->_schema
        );

        $select->joinInner(
            array('c' => 'Situacao'),
            'b.Situacao = c.Codigo',
            array(
                'c.Descricao as dsSituacao'
            ),
            $this->_schema
        );

        $idAtoAdministrativo = Assinatura_Model_DbTable_TbAssinatura::TIPO_ATO_PARECER_AVALIACAO_OBJETO;
        if ($filtro == 'painel-tecnico') {
            $select->joinLeft(
                array('d' => 'tbDocumentoAssinatura'),
                "a.IdPRONAC = d.idPronac 
                    AND d.cdSituacao = 2 
                    AND d.stEstado = 0
                    AND d.idTipoDoAtoAdministrativo = {$idAtoAdministrativo}
                    ",
                array(
                    'd.idDocumentoAssinatura',
                ),
                $this->_schema
            );
        }

        if ($filtro == 'analisados') {
            $select->joinLeft(
                array('d' => 'tbDocumentoAssinatura'),
                "a.IdPRONAC = d.idPronac 
                    AND d.cdSituacao = 1 
                    AND d.stEstado = 1
                    AND d.idTipoDoAtoAdministrativo = {$idAtoAdministrativo}
                    ",
                array(
                    'd.idDocumentoAssinatura',
                    new Zend_Db_Expr("(select count(*)
                                        from sac.dbo.TbAssinatura as  tbAssinatura
                                       where tbAssinatura.idPronac = d.idPronac
                                         and tbAssinatura.idDocumentoAssinatura = d.idDocumentoAssinatura) as quantidadeAssinaturas")
                ),
                $this->_schema
            );
        }

        if ($analisados) {
            $idAtoAdministrativo = Assinatura_Model_DbTable_TbAssinatura::TIPO_ATO_PARECER_AVALIACAO_OBJETO;
            $select->joinLeft(
                array('d' => 'tbDocumentoAssinatura'),
                "a.IdPRONAC = d.idPronac 
                    AND d.cdSituacao = 1 
                    AND d.stEstado = 1
                    AND d.idTipoDoAtoAdministrativo = {$idAtoAdministrativo}
                    ",
                array(
                    'd.idDocumentoAssinatura',
                    new Zend_Db_Expr("(select count(*)
                                        from sac.dbo.TbAssinatura as  tbAssinatura
                                       where tbAssinatura.idPronac = d.idPronac
                                         and tbAssinatura.idDocumentoAssinatura = d.idDocumentoAssinatura) as quantidadeAssinaturas")
                ),
                $this->_schema
            );
        }

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

    /**
     * Método que diz se projeto possui ou não um relatório de cumprimento de objeto
     *
     * @param integer $idPronac
     * @return bool
     */
    public function possuiRelatorioDeCumprimento($idPronac)
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(
            array('a' => $this->_name),
            ('idCumprimentoObjeto'),
            $this->_schema
        );

        $select->where('siCumprimentoObjeto <> ?', 1);
        $select->where('idPronac = ?', $idPronac);
        $select->limit(1);

        $result = $this->fetchAll($select);

        if (count($result) > 0) {
            return true;
        } else {
            return false;
        }
    }
}
