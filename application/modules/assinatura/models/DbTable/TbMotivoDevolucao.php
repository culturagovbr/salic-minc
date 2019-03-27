<?php

class Assinatura_Model_DbTable_TbMotivoDevolucao extends MinC_Db_Table_Abstract
{
    protected $_schema = 'sac';
    protected $_name = 'tbMotivoDevolucao';
    protected $_primary = 'idMotivoDevolucao';

    /**
     * @var Assinatura_Model_TbMotivoDevolucao $modeloTbMotivoDevolucao
     */
    public $modeloTbMotivoDevolucao;

    public function preencherModeloDevolucao(array $dados)
    {
        $this->modelo = new Assinatura_Model_TbMotivoDevolucao($dados);
        return $this;
    }

    public function obterDocumentosDevolvidos(
        $where = [],
        $order = ['tbDocumentoAssinatura.idDocumentoAssinatura DESC'],
        $inicio = -1,
        $tamanho = 100,
        $search = ''
    )
    {
        $query = $this->select();
        $query->setIntegrityCheck(false);
        $query->from(
            ["tbMotivoDevolucao" => $this->_name],
            [
                'tbMotivoDevolucao.idDocumentoAssinatura',
                'dtDevolucao' => 'dtDevolucao',
                'dias' => 'DATEDIFF(DAY, dtDevolucao, GETDATE())',
                new Zend_Db_Expr('CAST(dsMotivoDevolucao AS TEXT) AS dsMotivoDevolucao'),
            ],
            $this->_schema
        );

        $query->joinInner(
            ["tbDocumentoAssinatura" => "tbDocumentoAssinatura"],
            "tbDocumentoAssinatura.idDocumentoAssinatura = tbMotivoDevolucao.idDocumentoAssinatura",
            [],
            $this->_schema
        );

        $query->joinInner(
            ["Usuarios" => "Usuarios"],
            "tbMotivoDevolucao.idUsuario = Usuarios.usu_codigo",
            ['Usuarios.usu_nome as nomeAvaliador'],
            $this->getSchema('tabelas')
        );

        $query->joinInner(
            ["Projetos" => "Projetos"],
            "tbDocumentoAssinatura.IdPRONAC = Projetos.IdPRONAC",
            [
                'pronac' => new Zend_Db_Expr('Projetos.AnoProjeto + Projetos.Sequencial'),
                'Projetos.nomeProjeto',
                'Projetos.IdPRONAC',
            ],
            $this->_schema
        );

        $query->joinInner(
            ['TbAtoAdministrativo' => 'TbAtoAdministrativo'],
            "TbAtoAdministrativo.idTipoDoAto = tbDocumentoAssinatura.idTipoDoAtoAdministrativo",
            [],
            $this->_schema
        );

        $query->joinInner(
            ['Verificacao' => 'Verificacao'],
            "Verificacao.idVerificacao = tbDocumentoAssinatura.idTipoDoAtoAdministrativo",
            'Verificacao.Descricao as tipoDoAtoAdministrativo',
            $this->_schema
        );

        foreach ($where as $coluna => $valor) {
            if (!is_null($valor)) {
                $query->where($coluna, $valor);
            }
        }
        $query->where("tbDocumentoAssinatura.cdSituacao = ?", Assinatura_Model_TbDocumentoAssinatura::CD_SITUACAO_FECHADO_PARA_ASSINATURA);
        $query->where("tbDocumentoAssinatura.stEstado = ?", Assinatura_Model_TbDocumentoAssinatura::ST_ESTADO_DOCUMENTO_INATIVO);

        if (!empty($search) && strlen($search) > 4) {
            $query->where("Projetos.AnoProjeto+Projetos.Sequencial LIKE '$search%' OR Projetos.nomeProjeto LIKE '%$search%' OR Verificacao.Descricao = ?", $search);
        }

        if ($tamanho > -1) {
            $tmpInicio = 0;
            if ($inicio > -1) {
                $tmpInicio = $inicio;
            }
            $query->limitPage($tmpInicio, $tamanho);
        }

        $query->order($order);
        return $this->fetchAll($query);

    }

    public function obterTotalDocumentosDevolvidos($where = [])
    {
        $query = $this->select();
        $query->setIntegrityCheck(false);

        $query->from(
            ["tbMotivoDevolucao" => $this->_name],
            [],
            $this->_schema
        );

        $query->joinInner(
            ["tbDocumentoAssinatura" => "tbDocumentoAssinatura"],
            "tbDocumentoAssinatura.idDocumentoAssinatura = tbMotivoDevolucao.idDocumentoAssinatura",
            [
                new Zend_Db_Expr('count(tbMotivoDevolucao.idDocumentoAssinatura)'),
            ],
            $this->_schema
        );

        $query->joinInner(
            ['TbAtoAdministrativo' => 'TbAtoAdministrativo'],
            "TbAtoAdministrativo.idTipoDoAto = tbDocumentoAssinatura.idTipoDoAtoAdministrativo",
            [],
            $this->_schema
        );

        foreach ($where as $coluna => $valor) {
            if (!is_null($valor)) {
                $query->where($coluna, $valor);
            }
        }

        $query->where("tbDocumentoAssinatura.cdSituacao = ?", Assinatura_Model_TbDocumentoAssinatura::CD_SITUACAO_FECHADO_PARA_ASSINATURA);
        $query->where("tbDocumentoAssinatura.stEstado = ?", Assinatura_Model_TbDocumentoAssinatura::ST_ESTADO_DOCUMENTO_INATIVO);

        return $this->_db->fetchOne($query);

    }

    public function devolverDocumentoEncaminhadoParaAssinatura($idDocumentoAssinatura, $dsMotivoDevolucao)
    {
        $auth = Zend_Auth::getInstance();

        $dadosInclusao = array(
            'idDocumentoAssinatura' => $idDocumentoAssinatura,
            'dtDevolucao' => $this->getExpressionDate(),
            'dsMotivoDevolucao' => $dsMotivoDevolucao,
            'idUsuario' => $auth->getIdentity()->usu_codigo
        );

        return $this->inserir($dadosInclusao);
    }
}
