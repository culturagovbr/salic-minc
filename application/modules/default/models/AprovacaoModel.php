<?php

/**
 * @author Mikhail Cavalcanti <mikhail.leite@xti.com.br>
 */
class AprovacaoModel implements ModelInterface
{

    /**
     *
     */
    const TIPO_ANALISE_INICIAL = 1;

    /**
     *
     */
    const TIPO_COMPLEMENTACAO = 2;

    /**
     *
     */
    const TIPO_PRORROGACAO = 3;

    /**
     *
     */
    const TIPO_REDUCAO = 3;

    /**
     *
     * @var Aprovacao
     */
    private $table = null;

    /**
     *
     * @var int
     */
    private $idPronac = null;

    /**
     *
     * @var int
     */
    private $idProrrogacao = null;

    /**
     *
     * @var int
     */
    private $anoProjeto = null;

    /**
     *
     * @var int
     */
    private $sequencial = null;

    /**
     *
     * @var int
     */
    private $tipo = null;

    /**
     *
     * @var datetime
     */
    private $dataAprovacao = null;

    /**
     *
     * @var string
     */
    private $resumo = null;

    /**
     *
     * @var int
     */
    private $usuarioLogado = null;

    /**
     *
     */
    public function __construct()
    {
        $this->table = new Aprovacao();
    }

    public function getIdPronac()
    {
        return $this->idPronac;
    }

    public function getIdProrrogacao()
    {
        return $this->idProrrogacao;
    }

    public function getAnoProjeto()
    {
        return $this->anoProjeto;
    }

    public function getSequencial()
    {
        return $this->sequencial;
    }

    public function getTipo()
    {
        return $this->tipo;
    }

    public function getDataAprovacao()
    {
        return $this->dataAprovacao;
    }

    public function getResumo()
    {
        return $this->resumo;
    }

    public function getUsuarioLogado()
    {
        return $this->usuarioLogado;
    }

    public function setIdPronac($idPronac)
    {
        $this->idPronac = $idPronac;
        return $this;
    }

    public function setIdProrrogacao($idProrrogacao)
    {
        $this->idProrrogacao = $idProrrogacao;
        return $this;
    }

    public function setAnoProjeto($anoProjeto)
    {
        $this->anoProjeto = $anoProjeto;
        return $this;
    }

    public function setSequencial($sequencial)
    {
        $this->sequencial = $sequencial;
        return $this;
    }

    public function setTipo($tipo)
    {
        $this->tipo = $tipo;
        return $this;
    }

    public function setDataAprovacao(datetime $dataAprovacao)
    {
        $this->dataAprovacao = $dataAprovacao;
        return $this;
    }

    public function setResumo($resumo)
    {
        $this->resumo = $resumo;
        return $this;
    }

    public function setUsuarioLogado($usuarioLogado)
    {
        $this->usuarioLogado = $usuarioLogado;
        return $this;
    }

    /**
     *
     */
    public function salvar()
    {
        $dados = array(
                'IdPRONAC' => $this->getIdPronac(),
                'idProrrogacao' => $this->getIdProrrogacao(),
                'AnoProjeto' => $this->getAnoProjeto(),
                'Sequencial' => $this->getSequencial(),
                'TipoAprovacao' => $this->getTipo(),
                'DtAprovacao' => new Zend_Db_Expr('GETDATE()'),
                'ResumoAprovacao' => $this->getResumo(),
                'Logon' => $this->getUsuarioLogado(),
         );

        $this->table->inserir($dados);
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
     * @param type $id
     * @throws Exception
     */
    public function buscar($id = null)
    {
        throw new Exception('Não implementado');
    }

    /**
     *
     * @param type $id
     * @throws Exception
     */
    public function deletar($id)
    {
        throw new Exception('Não implementado');
    }
}
