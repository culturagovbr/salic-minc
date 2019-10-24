<?php

/**
 * Class Agente_Model_DbTable_Agentes
 *
 * @name Agente_Model_DbTable_Agentes
 * @package Modules/Agente
 * @subpackage Models/DbTable
 * @version $Id$
 *
 * @author Ruy Junior Ferreira Silva <ruyjfs@gmail.com>
 * @since 01/09/2016
 *
 * @link http://salic.cultura.gov.br
 *
    idAgente
    CNPJCPF
    CNPJCPFSuperior
    TipoPessoa
    DtCadastro
    DtAtualizacao
    DtValidade
    Status
    Usuario
 */
class Agente_Model_DbTable_Agentes extends MinC_Db_Table_Abstract
{
    /**
     * _banco
     *
     * @var bool
     * @access protected
     */
    protected $_banco = 'agentes';

    /**
     * _name
     *
     * @var bool
     * @access protected
     */
    protected $_name = 'agentes';

    /**
     * _schema
     *
     * @var string
     * @access protected
     */
    protected $_schema = 'agentes';

    /**
     * _primary
     *
     * @var bool
     * @access protected
     */
    protected $_primary = 'idAgente';

    /**
     * Metodo para buscar agentes
     * @access public
     * @static
     * @param string $cnpjcpf
     * @param string $nome
     * @param integer $idAgente
     * @return object
     */
    public function buscarAgentes($cnpjcpf = null, $nome = null, $idAgente = null)
    {
//        $agentes = new Agente_Model_Agentes();
//        $agentesM = new Agente_Models_Mapper_Agentes();
        $agentesM = new Agente_Model_DbTable_Agentes();
//        $agentesM = new Agente_Model_Mapper_Agentes();
//        $db = Zend_Db_Table::getDefaultAdapter();

        $schemaAgentes = parent::getSchema('agentes');
        $schemaSac = parent::getSchema('sac');

        $a = array(
            'a.idagente'
            ,'a.cnpjcpf'
            ,'a.cnpjcpfsuperior'
            ,'a.tipopessoa'
        );

        $e = array(
            'e.tipologradouro'
            ,'e.cidade'
            ,'e.cep as cep'
            ,'e.uf'
            ,'e.status'
            ,'e.tipoendereco'
            ,'e.idendereco'
            ,'e.logradouro'
            ,'e.numero'
            ,'e.complemento'
            ,'e.bairro'
            ,'e.divulgar as divulgarendereco'
            ,'e.status as enderecocorrespondencia'
        );

        $t = array(
            't.sttitular'
            ,'t.cdarea'
            ,'t.cdsegmento'
        );

//        $sql = $db->select()->distinct()->from(['a' => 'agentes'], $a, $schemaAgentes)

        $select = $this->select()
            ->setIntegrityCheck(false)
            ->distinct()
            ->from(array('a' => 'agentes'), $a, $schemaAgentes)
            ->joinLeft(array('n' => 'nomes'), 'n.idagente = a.idagente', array('n.descricao as nome'), $schemaAgentes)
            ->joinLeft(array('e' => 'endereconacional'), 'e.idagente = a.idagente', $e, $schemaAgentes)
            ->joinLeft(array('m' => 'municipios'), 'm.idmunicipioibge = e.cidade', '*', $schemaAgentes)
            ->joinLeft(array('u' => 'uf'), 'u.iduf = e.uf', 'u.sigla as dsuf', $schemaAgentes)
            ->joinLeft(array('ve' => 'verificacao'), 've.idverificacao = e.tipoendereco', 've.descricao as dstipoendereco', $schemaAgentes)
            ->joinLeft(array('vl' => 'verificacao'), 'vl.idverificacao = e.tipologradouro', 'vl.descricao as dstipologradouro', $schemaAgentes)
            ->joinLeft(array('t' => 'tbtitulacaoconselheiro'), 't.idagente = a.idagente', $t, $schemaAgentes)
            ->joinLeft(array('v' => 'visao'), 'v.idagente = a.idagente', '*', $schemaAgentes)
            ->joinLeft(array('sa' => 'area'), 'sa.codigo = t.cdarea', 'sa.descricao as dsarea', $schemaSac)
            ->joinLeft(array('ss' => 'segmento'), 'ss.codigo = t.cdsegmento', 'ss.descricao as dssegmento', $schemaSac)
            ->where('a.tipopessoa = 0 or a.tipopessoa = 1')
        ;

        if (!empty($cnpjcpf)) {
            # busca pelo cpf/cnpj
            $select->where('a.cnpjcpf = ?', $cnpjcpf);
        }
        if (!empty($nome)) {
            # filtra pelo nome
            $select->where('n.descricao LIKE ?', '%'.$nome.'%');
        }
        if (!empty($idAgente)) {
            # busca de acordo com o id do agente
            $select->where('a.idagente = ?', $idAgente);
        }

        $select->order(array('e.status Desc', 'n.descricao Asc'));
        $result = $this->fetchAll($select);
        $result = ($result)? $result->toArray() : array();

        foreach ($result as &$value) {
            $value['cnpjcpf'] = Mascara::addMaskCpfCnpj($value['cnpjcpf']);
            $value['cep'] = Mascara::addMaskCEP($value['cep']);
        }

        return $result;
    }

    /**
     * BuscarComponente
     *
     * @access public
     * @return void
     */
    public function BuscarComponente()
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(
            array('A' => $this->_name)
        );
        $select->joinInner(
            array('N' => 'Nomes'),
            'N.idAgente = A.idAgente',
            array('N.Descricao as nomeConselheiro', 'N.idAgente as agente')
        );
        $select->joinInner(
            array('V' => 'Visao'),
            'V.idAgente = A.idAgente'
        );
        $select->where('V.Visao = ?', 210);
        $select->order('N.Descricao');

        return $this->fetchAll($select);
    }

    /**
     * BuscaAgente
     *
     * @param bool $cnpjcpf
     * @access public
     * @return void
     *
     * @todo renomear e substituir onde utiliza este metodo.
     */
    public function BuscaAgente($cnpjcpf = null)
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->where('cnpjcpf = ?', trim($cnpjcpf));
        return $this->fetchAll($select);
    }

    /**
     * inserirAgentes
     *
     * @param mixed $dados
     * @access public
     * @return void
     */
    public function inserirAgentes($dados)
    {
        $insert = $this->insert($dados);
        return $insert;
    }

    /**
     * Retorna registros do banco de dados referente a Agentes(Proponente)
     * @param array $where - array com dados where no formato "nome_coluna_1"=>"valor_1","nome_coluna_2"=>"valor_2"
     * @param array $order - array com orders no formado "coluna_1 desc","coluna_2"...
     * @param int $tamanho - numero de registros que deve retornar
     * @param int $inicio - offset
     * @return Zend_Db_Table_Rowset_Abstract
     */
    public function buscarAgenteENome($where=array(), $order=array(), $tamanho=-1, $inicio=-1)
    {
        $slct = $this->select();
        $slct->setIntegrityCheck(false);
        $slct->from(array('a' => $this->_name), '*', $this->_schema);
        $slct->joinInner(array('m' => 'nomes'), 'a.idagente=m.idagente', array('*'), $this->_schema);

        foreach ($where as $coluna => $valor) {
            $slct->where($coluna, $valor);
        }

        return $this->fetchAll($slct);
    }

    /**
     * buscarFornecedor
     *
     * @param mixed $where
     * @access public
     * @return void
     */
    public function buscarFornecedor($where)
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(
            array('A' => $this->_name),
            array('A.CNPJCPF', 'A.idAgente')
        );

        $select->joinInner(
            array('N' => 'Nomes'),
            'N.idAgente = A.idAgente',
            array('N.Descricao AS nome')
        );
        foreach ($where as $coluna => $valor) {
            $select->where($coluna, $valor);
        }
        $select->group('A.CNPJCPF');
        $select->group('A.idAgente');
        $select->group('N.Descricao');

        $select->order('N.Descricao');

        return $this->fetchAll($select);
    }

    /**
     * buscarFornecedorFiscalizacao
     *
     * @param mixed $where
     * @access public
     * @return void
     */
    public function buscarFornecedorFiscalizacao($where)
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(
            array('A' => $this->_name),
            array('A.CNPJCPF', 'A.idAgente')
        );
        $select->joinInner(
            array('U' => 'vwUsuariosOrgaosGrupos'),
            'U.usu_identificacao = A.CNPJCPF',
            array(),
            'tabelas.dbo'
        );
        $select->joinInner(
            array('N' => 'Nomes'),
            'N.idAgente = A.idAgente',
            array('N.Descricao AS nome')
        );
        foreach ($where as $coluna => $valor) {
            $select->where($coluna, $valor);
        }
        $select->group('A.CNPJCPF');
        $select->group('A.idAgente');
        $select->group('N.Descricao');

        $select->order('N.Descricao');

        return $this->fetchAll($select);
    }

    /**
     * buscarAgenteVinculoResponsavel
     *
     * @param bool $where
     * @param bool $order
     * @param mixed $tamanho
     * @param mixed $inicio
     * @access public
     * @return void
     */
    public function buscarAgenteVinculoResponsavel($where=array(), $order=array(), $tamanho=-1, $inicio=-1)
    {
        $objAgentes = $this->select();
        $objAgentes->distinct();
        $objAgentes->setIntegrityCheck(false);
        $objAgentes->from(
            array('ag' => $this->_name),
            array('ag.CNPJCPF'),
            $this->_schema
        );
        $objAgentes->joinInner(
            array('nm' => 'Nomes'),
            "nm.idAgente = ag.idAgente",
            array('nm.Descricao as NomeAgente'),
            $this->_schema
        );
        $objAgentes->joinLeft(
            array('vr' => 'tbVinculo'),
            "vr.idUsuarioResponsavel  = ag.idAgente",
            array("vr.idVinculo as idVinculoResponsavel"),
            $this->_schema
        );
        $objAgentes->joinInner(
            array('vprp' => 'tbVinculoProposta'),
            "vprp.idVinculo = vr.idVinculo",
            array("vprp.siVinculoProposta", "vprp.idPreProjeto", 'vprp.idVinculoProposta'),
            $this->_schema
        );
        $objAgentes->joinLeft(array('pr'=>'Projetos'), 'vprp.idPreProjeto = pr.idProjeto', array('(pr.AnoProjeto ' . parent::getConcatExpression() . ' pr.Sequencial) as pronac'), $this->getSchema('sac'));

        foreach ($where as $coluna => $valor) {
            $objAgentes->where($coluna, $valor);
        }
        return $this->fetchAll($objAgentes);
    }

    /**
     * buscarAgenteVinculoProponente
     *
     * @param bool $where
     * @param bool $order
     * @param mixed $tamanho
     * @param mixed $inicio
     * @access public
     */
    public function buscarAgenteVinculoProponente($where=array(), $order=array(), $tamanho=-1, $inicio=-1)
    {
        $slct = $this->select();
        $slct->distinct();
        $slct->setIntegrityCheck(false);
        $slct->from(
            array('ag' => $this->_name),
            array('ag.cnpjcpf', 'ag.idagente'),
            $this->_schema
        );
        $slct->joinInner(
            array('nm' => 'nomes'),
            "nm.idagente = ag.idagente",
            array('nm.descricao as nomeagente'),
            $this->_schema

        );
        $slct->joinLeft(
            array('vp' => 'tbvinculo'),
            "vp.idagenteproponente  = ag.idagente",
            array("vp.idvinculo as idvinculoproponente", "sivinculo", "idusuarioresponsavel"),
            $this->_schema
        );
        $slct->joinLeft(
            array('vprp' => 'tbvinculoproposta'),
            "vprp.idvinculo = vp.idvinculo",
            array("vprp.sivinculoproposta", "vprp.idpreprojeto", "vprp.idvinculo"),
            $this->_schema
        );

        $slct->joinLeft(
            array('pr' => 'projetos'),
            "pr.idprojeto = vprp.idpreprojeto",
            array('pr.idpronac'),
            $this->getSchema('sac')
        );

        $slct->joinLeft(
            array('usu' => 'usuarios'),
            "usu.usu_identificacao = ag.cnpjcpf",
            array('usu.usu_identificacao as usuariovinculo'),
            $this->getSchema('tabelas')
        );

        foreach ($where as $coluna => $valor) {
            $slct->where($coluna, $valor);
        }
        //echo $slct;die;
        return $this->fetchAll($slct);
    }

    /**
     * buscarNovoProponente
     *
     * @param bool $where
     * @param mixed $idResponsavel
     * @access public
     * @return void
     * @todo padrao orm
     */
    public function buscarNovoProponente($where=array(), $idResponsavel)
    {
        $select = $this->select();
        $select->distinct();
        $select->setIntegrityCheck(false);
        $select->from(
            array('ag' => $this->_name),
            array('ag.cnpjcpf', 'ag.idagente'),
            $this->_schema
        );

        $select->joinInner(
            array('nm' => 'nomes'),
            "nm.idagente = ag.idagente",
            array('nm.descricao as nomeagente'),
            $this->_schema
        );

        $select->joinLeft(
            array('vp' => 'tbvinculo'),
            "vp.idagenteproponente  = ag.idagente and vp.idusuarioresponsavel = $idResponsavel",
            array("vp.idvinculo as idvinculoproponente", "sivinculo", "idusuarioresponsavel"),
            $this->_schema
        );

        $select->joinLeft(
            array('v' => 'visao'),
            "v.idagente = ag.idagente and v.visao = 146",
            array('v.visao as usuariovinculo'),
            $this->_schema
        );

        foreach ($where as $coluna => $valor) {
            if ($valor) {
                $select->where($coluna, $valor);
            }
        }

        return $this->fetchAll($select);
    }

    /**
     * todosPareceristas
     *
     * @access public
     * @return void
     */
    public function todosPareceristas()
    {
        $slct = $this->select();
        $slct->setIntegrityCheck(false);
        $slct->from(array('a' => $this->_name), array('*'));

        $slct->joinInner(
            array('n' => 'Nomes'),
            'a.idAgente = n.idAgente',
            array('n.idAgente AS idParecerista', 'n.Descricao AS Nome')
        );

        $slct->joinInner(
            array('v' => 'Visao'),
            'n.idAgente = v.idAgente',
            array()
        );

        $slct->where('v.Visao = ?', 209);
        $slct->where('n.TipoNome = ?', 18);

        $slct->order('n.Descricao');
        return $this->fetchAll($slct);
    }

    /**
     * pareceristasDoOrgao
     *
     * @param mixed $idOrgao
     * @access public
     * @return void
     */
    public function pareceristasDoOrgao($idOrgao)
    {
        $slct = $this->select();
        $slct->setIntegrityCheck(false);
        $slct->from(array('a' => $this->_name), array());

        $slct->joinInner(array('n' => 'Nomes'), 'a.idAgente = n.idAgente', array('n.idAgente AS idParecerista', 'n.Descricao AS Nome'));

        $slct->joinInner(array('u' => 'vwUsuariosOrgaosGrupos'), 'a.CNPJCPF = u.usu_identificacao AND u.sis_codigo = 21 AND u.gru_codigo = 94 OR u.gru_codigo = 105', array('u.org_superior AS idOrgao'), 'TABELAS.dbo');

        $slct->joinInner(array('v' => 'Visao'), 'n.idAgente = v.idAgente', array());


        $dadosWhere = array('v.Visao = ?' => 209, 'n.TipoNome = ?' => 18, 'u.org_superior = ?' => $idOrgao);

        foreach ($dadosWhere as $coluna => $valor) {
            $slct->where($coluna, $valor);
        }

        $slct->order('n.Descricao');
        return $this->fetchAll($slct);
    }

    /**
     * consultaPareceristasDoOrgao
     *
     * @param bool $idOrgao
     * @access public
     * @return void
     */
    public function consultaPareceristasDoOrgao($idOrgao = null)
    {
        $slct = $this->select();
        $slct->setIntegrityCheck(false);
        $slct->distinct();
        $slct->from(array('a' => $this->_name), array(), array(), $this->getSchema('agentes'));

        $slct->joinInner(array('n' => 'Nomes'), 'a.idAgente = n.idAgente', array('n.idAgente AS idParecerista', 'n.Descricao AS Nome'));

        if ($idOrgao == null) {
            $slct->joinInner(array('u' => 'vwUsuariosOrgaosGrupos'), 'a.CNPJCPF = u.usu_identificacao', array(), 'TABELAS.dbo');
        } else {
            $slct->joinInner(array('u' => 'vwUsuariosOrgaosGrupos'), 'a.CNPJCPF = u.usu_identificacao', array('u.uog_orgao AS idOrgao'), 'TABELAS.dbo');
        }

        $slct->joinInner(array('v' => 'Visao'), 'n.idAgente = v.idAgente', array());

        $dadosWhere['v.Visao = ?'] = 209;
        $dadosWhere['n.TipoNome = ?'] = 18;
        $dadosWhere['u.sis_codigo = ?'] = 21;
        $dadosWhere['u.gru_codigo = ?'] = 94;

        if (!empty($idOrgao)) {
            $dadosWhere['u.uog_orgao = ?'] = $idOrgao;
        }
        foreach ($dadosWhere as $coluna => $valor) {
            $slct->where($coluna, $valor);
        }

        $slct->order('n.Descricao');
        return $this->fetchAll($slct);
    }

    /**
     * buscarPareceristas
     *
     * @param mixed $idOrgao
     * @param mixed $idArea
     * @param mixed $idSegmento
     * @access public
     * @return void
     */
    public function buscarPareceristas($idOrgao, $idArea, $idSegmento, $idVinculada = null)
    {
        $slct = $this->select();
        $slct->setIntegrityCheck(false);
        $slct->distinct();
        $slct->from(
            array('a' => $this->_name),
            array(),
            $this->getSchema('agentes')
        );
        $slct->joinInner(
            array('n' => 'Nomes'),
            'a.idAgente = n.idAgente',
            array('u.usu_codigo AS id', 'n.Descricao AS nome'),
            $this->getSchema('agentes')
        );
        $slct->joinInner(
            array('u' => 'vwUsuariosOrgaosGrupos'),
            'a.CNPJCPF = u.usu_Identificacao AND sis_codigo = 21 AND (gru_codigo = 94 OR gru_codigo = 105)',
            array(),
            'TABELAS.dbo'
        );
        $slct->joinInner(
            array('v' => 'Visao'),
            'n.idAgente = v.idAgente',
            array(),
            $this->getSchema('agentes')
        );
        $slct->joinInner(
            array('c' => 'tbCredenciamentoParecerista'),
            'a.idAgente = c.idAgente',
            array(),
            $this->getSchema('agentes')
        );

        if ($idVinculada) {
            $dadosWhere["u.uog_orgao = ?"] = $idVinculada;
        }

        $dadosWhere["v.Visao = ?"] = 209;
        $dadosWhere["n.TipoNome = ?"] = 18;
        $dadosWhere["c.idCodigoArea = ?"] = $idArea;
        $dadosWhere["c.idCodigoSegmento = ?"] = $idSegmento;
        $dadosWhere["c.idverificacao = ?"] = 251;
        $dadosWhere["u.org_superior = ?"] = $idOrgao;
        $dadosWhere["NOT EXISTS(SELECT TOP 1 * FROM Agentes.dbo.tbAusencia WHERE Getdate() BETWEEN dtInicioAusencia AND dtFimAusencia AND idAgente = a.idAgente)"] = '';

        foreach ($dadosWhere as $coluna => $valor) {
            $slct->where($coluna, $valor);
        }

        $slct->order('n.Descricao');

        return $this->fetchAll($slct);
    }

    /**
     * buscarPareceristasIphan
     *
     * @param mixed $idOrgao
     * @param mixed $idArea
     * @param mixed $idSegmento
     * @access public
     * @return void
     */
    public function buscarPareceristasIphan($idOrgao, $idArea, $idSegmento)
    {
        $slct = $this->select();
        $slct->setIntegrityCheck(false);
        $slct->distinct();
        $slct->from(
            array('a' => $this->_name),
            array(),
            'AGENTES.dbo'
        );
        $slct->joinInner(
            array('n' => 'Nomes'),
            'a.idAgente = n.idAgente',
            array('u.usu_codigo AS id', 'n.Descricao AS nome'),
            'AGENTES.dbo'
        );
        $slct->joinInner(
            array('u' => 'vwUsuariosOrgaosGrupos'),
            'a.CNPJCPF = u.usu_Identificacao AND sis_codigo = 21 AND (gru_codigo = 94 OR gru_codigo = 105)',
            array(),
            'TABELAS.dbo'
        );
        $slct->joinInner(
            array('v' => 'Visao'),
            'n.idAgente = v.idAgente',
            array(),
            'AGENTES.dbo'
        );
        $slct->joinInner(
            array('c' => 'tbCredenciamentoParecerista'),
            'a.idAgente = c.idAgente',
            array(),
            'AGENTES.dbo'
        );

        $dadosWhere["v.Visao = ?"] = 209;
        $dadosWhere["n.TipoNome = ?"] = 18;
        $dadosWhere["c.idCodigoArea = ?"] = $idArea;
        $dadosWhere["c.idCodigoSegmento = ?"] = $idSegmento;
        $dadosWhere["u.org_superior = ?"] = 91;
        $dadosWhere["u.usu_orgao = ?"] = $idOrgao;  // especificidade do IPHAN - puxa codigo do orgao
        $dadosWhere["NOT EXISTS(SELECT TOP 1 * FROM Agentes.dbo.tbAusencia WHERE Getdate() BETWEEN dtInicioAusencia AND dtFimAusencia AND idAgente = a.idAgente)"] = '';
        
        foreach ($dadosWhere as $coluna => $valor) {
            $slct->where($coluna, $valor);
        }

        $slct->order('n.Descricao');
        
        return $this->fetchAll($slct);
    }
    
    /**
     * consultaPareceristasPainel
     *
     * @param mixed $nome
     * @param mixed $cpf
     * @access public
     * @return void
     */
    public function consultaPareceristasPainel($nome, $cpf)
    {
        $slct = $this->select();
        $slct->setIntegrityCheck(false);
        $slct->from(array('a' => $this->_name), array('*'));

        $slct->joinInner(
            array('n' => 'Nomes'),
            'a.idAgente = n.idAgente',
            array('n.idAgente AS idParecerista', 'n.Descricao AS Nome')
        );

        $slct->joinInner(
            array('v' => 'Visao'),
            'n.idAgente = v.idAgente',
            array()
        );

        $slct->where('v.Visao = ?', 209);
        $slct->where('n.TipoNome = ?', 18);

        if (!empty($nome)) {
            $slct->where("n.Descricao like '%" . $nome . "%'");
        }
        if (!empty($cpf)) {
            $slct->where('a.CNPJCPF = ?', $cpf);
        }

        $slct->distinct();
        $slct->order('n.Descricao');
        return $this->fetchAll($slct);
    }

    /**
     * dadosParecerista
     *
     * @param mixed $where
     * @access public
     * @return void
     */
    public function dadosParecerista($where)
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(
            array("ag" => $this->_name),
            array("ag.idAgente")
        );
        $select->joinInner(
            array("nm" => "Nomes"),
            "nm.idAgente = ag.idAgente",
            array(
                "nmParecerista" => "nm.Descricao"
            ),
            $this->getSchema('agentes')
        );
        $select->joinLeft(
            array("cp" => "tbCredenciamentoParecerista"),
            "cp.idAgente = ag.idAgente",
            array(
                "nmParecerista" => "nm.Descricao",
                "qtPonto"
            ),
            $this->getSchema('agentes')
        );
        $select->joinLeft(
            array("ar" => "Area"),
            "ar.Codigo = cp.idCodigoArea",
            array(
                'Area' => 'ar.Descricao'
            ),
            $this->_schema
        );
        $select->joinLeft(
            array("seg" => "Segmento"),
            "seg.Codigo = cp.idCodigoSegmento",
            array(
                'Segmento' => 'seg.Descricao'
            ),
            $this->_schema
        );
        $select->joinLeft(
            array("au" => "tbAusencia"),
            "au.idAgente = ag.idAgente and au.idTipoAusencia = 2 and au.dtFimAusencia >= GETDATE()",
            array(
                'au.dtFimAusencia',
                'au.dtInicioAusencia',
            ),
            $this->getSchema('agentes')
        );
        $select->joinInner(
            array("usu" => "Usuarios"),
            "ag.CNPJCPF = usu.usu_identificacao",
            array(),
            'TABELAS.dbo'
        );
        $select->joinInner(
            array("uog" => "UsuariosXOrgaosXGrupos"),
            "usu.usu_codigo = uog.uog_usuario and uog.uog_status = 1",
            array(),
            'TABELAS.dbo'
        );
        $select->joinInner(
            array("org" => "Orgaos"),
            "org.Codigo = uog.uog_orgao",
            array(),
            $this->_schema
        );

        $select->where('uog.uog_grupo = 94');
        foreach ($where as $coluna => $valor) {
            $select->where($coluna, $valor);
        }
        return $this->fetchRow($select);
    }

    /**
     * buscarAgentesCpfVinculo
     *
     * @param bool $where
     * @access public
     * @return void
     */
    public function buscarAgentesCpfVinculo($where = array())
    {
        $sl = $this->select();
        $sl->setIntegrityCheck(false);
        $sl->from(
            array('ag' => $this->_name),
            array('ag.CNPJCPF', 'ag.idAgente')
        );
        $sl->joinLeft(
            array('v' => 'tbVinculo'),
            "v.idAgenteProponente = ag.idAgente ",
            array('v.siVinculo')
        );
        $sl->joinInner(
            array('nm' => 'nomes'),
            "nm.idAgente = ag.idAgente",
            array('nm.Descricao')
        );

        foreach ($where as $key => $valor) {
            $sl->where($key, $valor);
        }
        return $this->fetchAll($sl);
    }

    /**
     * buscarDirigentes
     *
     * @param bool $where
     * @param bool $order
     * @param mixed $tamanho
     * @param mixed $inicio
     * @access public
     * @return void
     */
    public function buscarDirigentes($where=array(), $order=array(), $tamanho=-1, $inicio=-1)
    {
        $slct = $this->select();
        $slct->setIntegrityCheck(false);
        $slct->from(
            array('a' => $this->_name),
            array('CNPJCPFDirigente'=>'a.CNPJCPF','idAgente'),
            $this->getSchema('agentes')
        );

        $slct->joinInner(
            array('v' => 'Vinculacao'),
            'a.idAgente = v.idAgente',
            array(),
            $this->getSchema('agentes')
        );

        $slct->joinInner(
            array('n' => 'Nomes'),
            'a.idAgente = n.idAgente',
            array('NomeDirigente'=>'n.Descricao'),
            $this->getSchema('agentes')
        );

        foreach ($where as $coluna=>$valor) {
            $slct->where($coluna, $valor);
        }

        //adicionando linha order ao select
        $slct->order($order);
        return $this->fetchAll($slct);
    }

    /**
     * buscarUfMunicioAgente
     *
     * @param bool $where
     * @param bool $order
     * @param mixed $tamanho
     * @param mixed $inicio
     * @param bool $retornaSelect
     * @access public
     * @return void
     */
    public function buscarUfMunicioAgente($where=array(), $order=array(), $tamanho=-1, $inicio=-1, $retornaSelect = false)
    {
        $slct = $this->select();
        $slct->setIntegrityCheck(false);
        $slct->from(
            array('a' => $this->_name),
            array('a.CNPJCPF',
                'a.idAgente'),
            $this->getSchema('agentes')
        );
        $slct->joinInner(
            array('e' => 'EnderecoNacional'),
            'a.idAgente = e.idAgente',
            array(),
            $this->getSchema('agentes')
        );
        $slct->joinInner(
            array('mun' => 'Municipios'),
            'mun.idMunicipioIBGE = e.Cidade',
            array('mun.idMunicipioIBGE',
                'mun.idUFIBGE',
                'mun.Descricao as DescricaoMunicipio'),
            $this->getSchema('agentes')
        );
        $slct->joinInner(
            array('uf' => 'UF'),
            'uf.idUF = mun.idUFIBGE',
            array('*'),
            $this->getSchema('agentes')
        );

        foreach ($where as $coluna=>$valor) {
            $slct->where($coluna, $valor);
        }

        if ($retornaSelect) {
            return $slct;
        } else {
            return $this->fetchAll($slct);
        }
    }


    /**
     * buscarAgenteVinculo
     *
     * @param bool $where
     * @param bool $order
     * @param mixed $tamanho
     * @param mixed $inicio
     * @access public
     * @return void
     */
    public function buscarAgenteVinculo($where=array(), $order=array(), $tamanho=-1, $inicio=-1)
    {
        $slct = $this->select();
        $slct->setIntegrityCheck(false);
        $slct->from(array('a' => $this->_name));
        $slct->joinInner(
            array('v' => 'TbVinculo'),
            'a.idAgente=v.idUsuarioResponsavel'
        );
        $slct->joinInner(
            array('n' => 'Nomes'),
            'a.idAgente=n.idAgente'
        );

        foreach ($where as $coluna=>$valor) {
            $slct->where($coluna, $valor);
        }
        return $this->fetchAll($slct);
    }

    /**
     * gerenciarResponsaveisListas
     *
     * @param mixed $siVinculo
     * @param mixed $idUsuario
     * @access public
     * @return void
     */
    public function gerenciarResponsaveisListas($siVinculo, $idUsuario)
    {
        $a = $this->select();
        $a->setIntegrityCheck(false);
        $a->from(
            array('a' => $this->_name),
            array('CNPJCPF as CNPJCPFProponente'),
            $this->_schema
        );
        $a->joinInner(
            array('b' => 'tbVinculo'),
            "a.idAgente = b.idAgenteProponente",
            array('idVinculo', 'siVinculo', 'idUsuarioResponsavel'),
            $this->_schema
        );
        $a->joinInner(
            array('c' => 'SGCacesso'),
            "c.IdUsuario = b.idUsuarioResponsavel",
            array('IdUsuario', 'Cpf as CPFResponsavel', 'Nome AS NomeResponsavel'),
            $this->getSchema('controledeacesso')
        );
        $a->joinInner(
            array('d' => 'Nomes'),
            "b.idAgenteProponente = d.idAgente",
            array('Descricao as Proponente'),
            $this->_schema
        );
        $a->where('c.IdUsuario = ?', $idUsuario);
        $a->where('b.siVinculo = ?', $siVinculo);
        $a->where(new Zend_Db_Expr('a.CNPJCPF <> c.Cpf'));

        $b = $this->select();
        $b->setIntegrityCheck(false);
        $b->from(
            array('a' => $this->_name),
            array('CNPJCPF as CNPJCPFProponente'),
            $this->_schema
        );
        $b->joinInner(
            array('b' => 'tbVinculo'),
            "a.idAgente = b.idAgenteProponente",
            array('idVinculo', 'siVinculo', 'idUsuarioResponsavel'),
            $this->_schema
        );
        $b->joinInner(
            array('c' => 'SGCacesso'),
            "a.CNPJCPF = c.Cpf",
            array('IdUsuario'),
            $this->getSchema('controledeacesso')
        );
        $b->joinInner(
            array('e' => 'SGCacesso'),
            "e.IdUsuario = b.idUsuarioResponsavel",
            array('Cpf AS CPFResponsavel', 'Nome AS NomeResponsavel'),
            $this->getSchema('controledeacesso')
        );
        $b->joinInner(
            array('d' => 'Nomes'),
            "b.idAgenteProponente = d.idAgente",
            array('Descricao as Proponente'),
            $this->_schema
        );
        $b->where('c.IdUsuario = ?', $idUsuario);
        $b->where('b.siVinculo = ?', $siVinculo);
        $b->where(new Zend_Db_Expr('a.CNPJCPF = c.Cpf'));

        $c = $this->select();
        $c->setIntegrityCheck(false);
        $c->from(
            array('a' => $this->_name),
            array('CNPJCPF as CNPJCPFProponente'),
            $this->_schema
        );
        $c->joinInner(
            array('b' => 'tbVinculo'),
            "a.idAgente = b.idAgenteProponente",
            array('idVinculo', 'siVinculo', 'idUsuarioResponsavel'),
            $this->_schema
        );
        $c->joinInner(
            array('c' => 'Vinculacao'),
            "b.idAgenteProponente = c.idVinculoPrincipal",
            array(),
            $this->_schema
        );
        $c->joinInner(
            array('d' => 'Visao'),
            "c.idAgente = d.idAgente",
            array(),
            $this->_schema
        );
        $c->joinInner(
            array('e' => 'Agentes'),
            "d.idAgente = e.idAgente",
            array(),
            $this->_schema
        );
        $c->joinInner(
            array('f' => 'SGCacesso'),
            "e.CNPJCPF = f.Cpf",
            array('IdUsuario'),
            $this->getSchema('controledeacesso')
        );
        $c->joinInner(
            array('g' => 'SGCacesso'),
            "g.IdUsuario = b.idUsuarioResponsavel",
            array('Cpf as CPFResponsavel', 'Nome as NomeResponsavel'),
            $this->getSchema('controledeacesso')
        );
        $c->joinInner(
            array('h' => 'Nomes'),
            "c.idVinculoPrincipal = h.idAgente",
            array('Descricao as Proponente'),
            $this->_schema
        );
        $c->where('d.Visao = ?', 198);
        $c->where('f.IdUsuario = ?', $idUsuario);
        $c->where('b.siVinculo = ?', $siVinculo);
        $c->where(new Zend_Db_Expr('a.CNPJCPF <> f.Cpf'));

        $slctUnion = $this->select()
            ->union(array('('.$a.')', '('.$b.')', '('.$c.')'))
            ->order('nomeresponsavel');

        return $this->fetchAll($slctUnion);
    }

    /**
     * listarVincularPropostaCombo
     *
     * @param mixed $idResponsavel
     * @access public
     * @return void
     */
    public function listarVincularPropostaCombo($idResponsavel)
    {
        $slct = $this->select();
        $slct->setIntegrityCheck(false);
        $slct->from(
            array('a' => $this->_name),
            array('CNPJCPF AS CNPJCPFProponente'),
            $this->_schema
        );
        $slct->joinInner(
            array('b' => 'tbVinculo'),
            'a.idAgente = b.idAgenteProponente',
            array('idVinculo', 'siVinculo', 'idUsuarioResponsavel'),
            $this->_schema
        );
        $slct->joinInner(
            array('c' => 'SGCacesso'),
            'a.CNPJCPF = c.Cpf',
            array('IdUsuario'),
            $this->getSchema('controledeacesso')
        );
        $slct->joinInner(
            array('d' => 'Nomes'),
            'b.idAgenteProponente = d.idAgente',
            array('Descricao AS Proponente'),
            $this->_schema
        );
        $slct->joinInner(
            array('e' => 'SGCacesso'),
            'e.IdUsuario = b.idUsuarioResponsavel',
            array('Cpf AS CPFResponsavel', 'Nome AS NomeResponsavel'),
            $this->getSchema('controledeacesso')
        );

        $slct->where('c.IdUsuario = ?', $idResponsavel);
        $slct->where('b.siVinculo = ?', 2);
        $slct->where(new Zend_Db_Expr('a.CNPJCPF = c.Cpf'));
//        echo $slct;
        return $this->fetchAll($slct);
    }
}
