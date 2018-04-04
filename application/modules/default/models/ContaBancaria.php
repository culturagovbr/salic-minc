<?php
class ContaBancaria extends MinC_Db_Table_Abstract
{
    protected $_banco = 'SAC';
    protected $_schema = 'SAC';
    protected $_name = 'ContaBancaria';


    /**
     * Retorna registros do banco de dados
     * @param array $where - array com dados where no formato "nome_coluna_1"=>"valor_1","nome_coluna_2"=>"valor_2"
     * @param array $order - array com orders no formado "coluna_1 desc","coluna_2"...
     * @param int $tamanho - numero de registros que deve retornar
     * @param int $inicio - offset
     * @return Zend_Db_Table_Rowset_Abstract
     */
    public function buscar($where=array(), $order=array(), $tamanho=-1, $inicio=-1)
    {
        $slct = $this->select();
        $slct->setIntegrityCheck(false);
        $slct->distinct();
        $slct->from(array("c"=>$this->_name), array('IdContaBancaria', 'Banco', 'Agencia'));
        $slct->joinInner(
            array("a"=>"bancoagencia"),
            "c.Agencia = a.Agencia",
            array("Descricao", "NomeAgencia"=>"Descricao", "Cidade", "Uf", "Telefone", "Perfil")
        );
        $slct->joinInner(array("p"=>"projetos"), "c.AnoProjeto = p.AnoProjeto AND c.Sequencial = p.Sequencial", array());
        $slct->joinInner(array("i"=>"Interessado"), "p.CgcCpf = i.CgcCpf", array());
        $slct->order($order);

        //adiciona quantos filtros foram enviados
        foreach ($where as $coluna => $valor) {
            $slct->where($coluna, $valor);
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

    public function pegaTotal($where=array())
    {
        $slct = $this->select();
        $slct->distinct();
        $slct->setIntegrityCheck(false);
        $slct->from(
                array("c"=>$this->_name),
                array("Banco", "Agencia", "idContaBancaria")
                );
        $slct->joinInner(
                array("a"=>"bancoagencia"),
                "c.Agencia = a.Agencia",
                array("NomeAgencia"=>"Descricao", "Cidade", "Uf", "Telefone", "Perfil")
                );
        $slct->joinInner(
                array("p"=>"projetos"),
                "c.AnoProjeto = p.AnoProjeto AND c.Sequencial = p.Sequencial",
                array()
                );
        $slct->joinInner(
                array("i"=>"Interessado"),
                "p.CgcCpf = i.CgcCpf",
                array()
                );

        //adiciona quantos filtros foram enviados
        foreach ($where as $coluna => $valor) {
            $slct->where($coluna, $valor);
        }

        $slct2 = $this->select();
        $slct2->setIntegrityCheck(false);
        $slct2->from($slct, array("count(*) AS total"));

        
        return $this->fetchAll($slct2)->current();
    }

    public function contaPorProjeto($idPronac)
    {
        $select = $this->select();
        $select->distinct();
        $select->setIntegrityCheck(false);

        $select->from(
            ['c' => $this->_name],
            ['Banco', 'Agencia', 'DtLoteRemessaCB', 'ContaLivre','ContaBloqueada', 'DtLoteRemessaCL'],
            $this->_schema
        );

        $select->joinInner(
            ['p' => 'projetos'],
            'c.AnoProjeto = p.AnoProjeto AND c.Sequencial = p.Sequencial',
            [new Zend_Db_Expr('p.AnoProjeto+p.Sequencial AS NrProjeto'), 'NomeProjeto'],
            $this->_schema
        );

        $select->joinLeft(
            ['v'=>'Verificacao'],
            'c.OcorrenciaCB = SUBSTRING(v.Descricao,1,3) AND v.idTipo = 22',
            [new Zend_Db_Expr('v.Descricao AS OcorrenciaCB')],
            $this->_schema
        );

        $select->joinLeft(
            ['x'=>'Verificacao'],
            'c.OcorrenciaCL = SUBSTRING(x.Descricao,1,3) AND x.idTipo = 22',
            [new Zend_Db_Expr('x.Descricao AS OcorrenciaCL')],
            $this->_schema
        );

        $select->where('p.IdPRONAC = ?', $idPronac);

        return $this->fetchRow($select);
    }

    public function consultarDadosPorPronac($pronac, $orgao = null)
    {
        $slct = $this->select();
        $slct->setIntegrityCheck(false);
        $slct->from(
                array("cb"=>$this->_name),
                array('*')
                );

        $slct->joinInner(
                array("pr"=>"projetos"),
                "cb.AnoProjeto = pr.AnoProjeto AND cb.Sequencial = pr.Sequencial",
                array(
                        'Pronac'=>new Zend_Db_Expr('pr.AnoProjeto+pr.Sequencial'),
                        'pr.NomeProjeto', 'pr.Orgao', 'pr.IdPRONAC'
                    )
                );
        $slct->where('cb.AnoProjeto+cb.Sequencial = ?', $pronac);
        if (!empty($orgao)) {
            $slct->where('pr.Orgao = ?', $orgao);
        }

        return $this->fetchAll($slct);
    }


    public function buscarDados($pronac = null, $idPronac = null, $agencia = null, $conta = null, $buscarTodos = true)
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from($this);

        // busca pelo pronac
        if (!empty($pronac)) {
            $select->where('AnoProjeto+Sequencial = ?', $pronac);
        }

        // busca pelo idPronac
        if (!empty($idPronac)) {
            $select->where('idPronac = ?', $idPronac);
        }

        // busca pela agencia
        if (!empty($agencia)) {
            $select->where('Agencia = ?', $agencia);
        }

        // busca pela conta
        if (!empty($conta)) {
            $select->where('ContaBloqueada = ?', $conta);
            $select->orwhere('ContaLivre = ?', $conta);
        }

        return $buscarTodos ? $this->fetchAll($select) : $this->fetchRow($select);
    }

    public function buscarDadosBancarios($pronac = null)
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(
                    array("c"=>$this->_name),
                    array("Banco", "Agencia", "ContaBloqueada", "ContaLivre", "DtLoteRemessaCB", "DtLoteRemessaCL")
                );
        $select->where('AnoProjeto+Sequencial = ?', $pronac);

        return $this->fetchRow($select);
    }

    public function painelContasBancarias($where=array(), $order=array(), $tamanho=-1, $inicio=-1, $qtdeTotal=false)
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(
            array('c' => $this->_name),
            array(
                new Zend_Db_Expr("
                    p.idPronac,
                    p.AnoProjeto+p.Sequencial AS Pronac,
                    p.NomeProjeto,
                    x.Descricao AS Area,
                    p.Situacao,
                    p.CgcCpf,
                    n.Descricao AS Proponente,
                    a.TipoPessoa,
                    dbo.fnNrPortariaAprovacao(p.AnoProjeto,p.Sequencial) AS NrPortaria,
                    dbo.fnDtPortariaAprovacao(p.AnoProjeto,p.Sequencial) AS DtPublicacaoPortaria,
                    dbo.fnInicioCaptacao(p.AnoProjeto,p.Sequencial) AS DtInicioCaptacao,
                    dbo.fnFimCaptacao(p.AnoProjeto,p.Sequencial) AS DtFimCaptacao,
                    (SELECT dtNascimento FROM Agentes.dbo.tbAgenteFisico f WHERE a.idAgente = f.idAgente) AS DtNascimento,
                    c.Agencia,
                    c.ContaBloqueada AS ContaCaptacao,
                    c.ContaLivre AS ContaMovimento,
                    c.DtLoteRemessaCB,
                    c.OcorrenciaCB,
                    CASE
                        WHEN c.ContaBloqueada = '000000000000'
                        THEN 0
                        ELSE 1
                    END AS TemCaptacao
                ")
            )
        );

        $select->joinInner(
            array('p' => 'Projetos'),
            'c.AnoProjeto = p.AnoProjeto AND c.Sequencial = p.Sequencial',
            array(''),
            'SAC.dbo'
        );
        $select->joinInner(
            array('x' => 'Area'),
            'p.Area = x.Codigo',
            array(''),
            'SAC.dbo'
        );
        $select->joinInner(
            array('a' => 'Agentes'),
            'p.CgcCpf = a.CNPJCPF',
            array(''),
            'AGENTES.dbo'
        );
        $select->joinInner(
            array('n' => 'Nomes'),
            'a.idAgente = n.idAgente',
            array(''),
            'AGENTES.dbo'
        );

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
}
