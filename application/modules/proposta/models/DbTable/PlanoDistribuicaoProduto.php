<?php

class Proposta_Model_DbTable_PlanoDistribuicaoProduto extends MinC_Db_Table_Abstract
{
    protected $_schema = 'sac';
    protected $_name = 'PlanoDistribuicaoProduto';
    protected $_primary = 'idPlanoDistribuicao';

    public function buscar($where = array(), $order = array(), $tamanho = -1, $inicio = -1)
    {
        $slct = $this->select();

        $slct->setIntegrityCheck(false);

        $cols = array_merge($this->_getCols(), array(
            new Zend_Db_Expr("FORMAT(a.QtdeProponente, '0,0','pt-br') as QtdeProponente"),
            new Zend_Db_Expr("FORMAT(a.QtdeProduzida, '0,0','pt-br') as QtdeProduzida"),
            new Zend_Db_Expr("FORMAT(a.QtdePatrocinador, '0,0','pt-br') as QtdePatrocinador"),
            new Zend_Db_Expr("FORMAT(a.QtdeOutros, '0,0','pt-br') as QtdeOutros"),
            new Zend_Db_Expr("FORMAT(a.QtdeVendaPopularPromocional, '0,0','pt-br') as QtdeVendaPopularPromocional"),
            new Zend_Db_Expr("FORMAT(a.QtdeVendaNormal, '0,0','pt-br') as QtdeVendaNormal"),
            new Zend_Db_Expr("FORMAT(a.QtdeVendaPromocional, '0,0','pt-br') as QtdeVendaPromocional"),
            new Zend_Db_Expr("FORMAT(a.QtdeVendaPopularNormal, '0,0','pt-br') as QtdeVendaPopularNormal"),
            new Zend_Db_Expr("FORMAT(a.vlUnitarioPopularNormal, 'N','pt-br') as vlUnitarioPopularNormal"),
            new Zend_Db_Expr("FORMAT( a.vlUnitarioNormal, 'N','pt-br') AS vlUnitarioNormal"),
            new Zend_Db_Expr("FORMAT( a.ReceitaPopularNormal, 'N','pt-br') AS ReceitaPopularNormal"),
            new Zend_Db_Expr("FORMAT( a.ReceitaPopularPromocional, 'N','pt-br') AS ReceitaPopularPromocional"),
            new Zend_Db_Expr("FORMAT( a.PrecoUnitarioPromocional, 'N','pt-br') AS PrecoUnitarioPromocional"),
            new Zend_Db_Expr("FORMAT( a.PrecoUnitarioNormal, 'N', 'pt-br') AS PrecoUnitarioNormal"),
            new Zend_Db_Expr("FORMAT( a.vlReceitaTotalPrevista, 'N', 'pt-br') AS Receita"),
            new Zend_Db_Expr("FORMAT( a.ReceitaPopularNormal, 'N', 'pt-br') AS ValorMedioPopular"),
            new Zend_Db_Expr("FORMAT( a.ReceitaPopularPromocional, 'N', 'pt-br') AS ValorMedioProponente")
        ));

        $slct->from(array("a" => $this->_name), $cols, $this->_schema);
        $slct->joinInner(
            array("b" => "produto"),
            "a.idproduto = b.codigo",
            array("Produto" => "b.descricao"),
            $this->_schema
        );
        $slct->joinInner(
            array("ar" => "area"),
            "a.area = ar.codigo",
            array("DescricaoArea" => "ar.descricao"),
            $this->_schema
        );
        $slct->joinInner(
            array("s" => "segmento"),
            "a.segmento = s.codigo",
            array("DescricaoSegmento" => "s.descricao"),
            $this->_schema
        );

        $slct->where('a.stplanodistribuicaoproduto = ?', '1');

        foreach ($where as $coluna => $valor) {
            $slct->where($coluna, $valor);
        }

        $slct->order($order);

        if ($tamanho > -1) {
            $tmpInicio = 0;
            if ($inicio > -1) {
                $tmpInicio = $inicio;
            }
            $slct->limit($tamanho, $tmpInicio);
        }

        $this->_totalRegistros = $this->pegaTotal($where);
        return $this->fetchAll($slct);
    }

    public function pegaTotal($where = array(), $order = array(), $tamanho = -1, $inicio = -1)
    {
        $slct = $this->select();

        $slct->setIntegrityCheck(false);

        $slct->from(array("a" => $this->_name), '*', $this->_schema);
        $slct->joinInner(
            array("b" => "Produto"),
            "a.idProduto = b.Codigo",
            array("Produto" => "b.Descricao"),
            $this->_schema
        );
        $slct->joinLeft(
            array("c" => "verificacao"),
            "a.idPosicaoDaLogo = c.idVerificacao",
            array("PosicaoLogomarca" => "c.Descricao"),
            $this->_schema
        );

        $slct->where('a.stPlanoDistribuicaoProduto = ?', '1');

        foreach ($where as $coluna => $valor) {
            $slct->where($coluna, $valor);
        }

        $slct->order($order);

        if ($tamanho > -1) {
            $tmpInicio = 0;
            if ($inicio > -1) {
                $tmpInicio = $inicio;
            }
            $slct->limit($tamanho, $tmpInicio);
        }
        try {
            $rows = $this->fetchAll($slct);
            return $rows->count();
        } catch (Exception $e) {
            echo($slct->assemble());
            die;
        }
    }

    /**
     * Metodo para cadastrar
     * @access public
     * @param array $dados
     * @return integer (retorna o ultimo id cadastrado)
     */
    public function cadastrarDados($dados)
    {
        return $this->insert($dados);
    } // fecha metodo cadastrarDados()


    public function buscarPlanoDeDistribuicao($idPronac)
    {
        $a = $this->select();
        $a->setIntegrityCheck(false);
        $a->from(
            array('a' => $this->_name),
            array('idPlanoDistribuicao', 'idProduto', 'QtdePatrocinador', 'QtdeProponente', 'QtdeOutros')
        );
        $a->joinInner(
            array('b' => 'Projetos'),
            "a.idProjeto = b.idProjeto",
            array('IdPRONAC'),
            'SAC.dbo'
        );
        $a->joinInner(
            array('c' => 'Produto'),
            "a.idProduto = c.Codigo",
            array('Descricao as Produto'),
            'SAC.dbo'
        );
        $a->where('b.IdPRONAC = ?', $idPronac);
        return $this->fetchAll($a);
    }

    public function buscarProdutosProjeto($idPronac)
    {
        $a = $this->select();
        $a->setIntegrityCheck(false);
        $a->from(
            array('a' => $this->_name),
            array('stPrincipal', 'idProduto')
        );
        $a->joinInner(
            array('b' => 'Projetos'),
            "a.idProjeto = b.idProjeto",
            array(''),
            'SAC.dbo'
        );
        $a->joinInner(
            array('c' => 'Produto'),
            "a.idProduto = c.Codigo",
            array('Descricao as Produto'),
            'SAC.dbo'
        );
        $a->joinInner(
            array('d' => 'tbAnaliseDeConteudo'),
            "a.idProduto = d.idProduto AND b.IdPRONAC = d.idPronac",
            array('*'),
            'SAC.dbo'
        );
        $a->where('b.IdPRONAC = ?', $idPronac);
        $a->where('d.idPronac = ?', $idPronac);
        $a->order(array('1 DESC', '3 ASC'));

        return $this->fetchAll($a);
    }

    public function comboProdutosParaInclusaoReadequacao($idPronac)
    {
        $a = $this->select();
        $a->setIntegrityCheck(false);
        $a->from(
            array('a' => $this->_name),
            array('idProduto')
        );
        $a->joinInner(
            array('b' => 'Produto'),
            "a.idProduto = b.codigo",
            array('Descricao AS Produto'),
            'SAC.dbo'
        );
        $a->joinInner(
            array('c' => 'Projetos'),
            "a.idProjeto = c.idProjeto",
            array(''),
            'SAC.dbo'
        );
        $a->where('c.IdPRONAC = ?', $idPronac);


        $b = $this->select();
        $b->setIntegrityCheck(false);
        $b->from(
            array('a' => $this->_name),
            array(
                new Zend_Db_Expr("'0', 'Administra&ccedil;&atilde;o do Projeto'")
            )
        );
        $b->joinInner(
            array('c' => 'Projetos'),
            "a.idProjeto = c.idProjeto",
            array(''),
            'SAC.dbo'
        );
        $b->where('c.IdPRONAC = ?', $idPronac);


        $slctUnion = $this->select()
            ->union(array('(' . $a . ')', '(' . $b . ')'))
            ->order('1', '2');

        return $this->fetchAll($slctUnion);
    }

    public function buscarDadosCadastrarProdutos($idPreProjeto, $idProduto)
    {
        $select = $this->select();

        $select->setIntegrityCheck(false);

        $select->distinct(true);

        $select->from(
            array('pd' => $this->getName('PlanoDistribuicaoProduto')),
            array('CodigoProduto' => 'pd.idproduto',
                'idProposta' => 'pd.idprojeto'
            ),
            $this->_schema
        );

        $select->where('idproduto = ?', $idProduto);

        $select->where('idprojeto = ?', $idPreProjeto);

        $select->where('pd.stplanodistribuicaoproduto = ?', 1);

        $db = Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);

        return $db->fetchAll($select);
    }

    public function obterUfsMunicipiosDoDetalhamento($idPreProjeto)
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(
            array('p' => $this->_name),
            array(),
            $this->_schema
        );
        $select->joinInner(
            array('d' => 'tbDetalhaPlanoDistribuicao'),
            "p.idPlanoDistribuicao = d.idPlanoDistribuicao",
            array('d.idUF', 'd.idMunicipio'),
            $this->_schema
        );
        $select->joinInner(
            array('u' => 'uf'),
            "d.idUF = u.idUf",
            array('u.Sigla as UF'),
            $this->getSchema('agentes')
        );
        $select->where('p.idProjeto = ?', $idPreProjeto);
        return $this->fetchAll($select);
    }

    public function validatePlanoDistribuicao($idPreProjeto)
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);

        $select->from(
            array('pd' => $this->getName('PlanoDistribuicaoProduto')),
            array('pd.canalAberto', 'pd.PrecoUnitarioNormal')
        )
            ->joinInner(
                array('p' => 'Produto'),
                "pd.idProduto = p.Codigo",
                array('Descricao as Produto'),
                'SAC.dbo'
            )
            ->where('pd.idProjeto = ?', $idPreProjeto);

        $planosDistribuicao = $this->fetchAll($select);

        $errors = array();

        foreach ($planosDistribuicao as $planoDistribuicao) {
            if (!$planoDistribuicao['canalAberto']) {
                if ((int)$planoDistribuicao['PrecoUnitarioNormal'] > 225) {
                    $error = new StdClass();
                    $error->idPreProjeto = $idPreProjeto;
                    $error->dsChamada = '';
                    $error->dsInconsistencia = "O pre&ccedil;o m&eacute;dio do produto '" . $planoDistribuicao['Produto'] . "' ultrapassou o limite de 225,00.";
                    $error->Observacao = 'PENDENTE';

                    $errors[] = $error;
                }
            }
        }

        return $errors;
    }

    public function buscarPlanoDistribuicaoDetalhadoByIdProjeto($idPreProjeto, $where = array(), $order = null)
    {
        $slct = $this->select();
        $slct->setIntegrityCheck(false);
        $slct->from(array("p" => 'PlanoDistribuicaoProduto'), $this->_getCols(), $this->_schema);

        $slct->joinInner(
            array("d" => "tbDetalhaPlanoDistribuicao"),
            "p.idPlanoDistribuicao = d.idPlanoDistribuicao",
            '*',
            $this->_schema
        );

        $slct->joinInner(array('uf' => 'uf'), 'uf.CodUfIbge = d.idUF', 'uf.descricao AS DescricaoUf', $this->_schema);

        $slct->joinInner(array('mun' => 'municipios'), 'mun.idmunicipioibge = d.idMunicipio', 'mun.descricao as DescricaoMunicipio', $this->getSchema('agentes'));

        $slct->joinInner(
            array("b" => "produto"),
            "p.idproduto = b.codigo",
            array("Produto" => "b.descricao"),
            $this->_schema
        );

        $slct->joinInner(
            array("ar" => "area"),
            "p.area = ar.codigo",
            array("DescricaoArea" => "ar.descricao"),
            $this->_schema
        );
        $slct->joinInner(
            array("s" => "segmento"),
            "p.segmento = s.codigo",
            array("DescricaoSegmento" => "s.descricao"),
            $this->_schema
        );

        $slct->where('p.idProjeto = ?', $idPreProjeto);

        foreach ($where as $coluna => $valor) {
            $slct->where($coluna, $valor);
        }

        $slct->order($order);

        try {
            return $this->fetchAll($slct)->toArray();
        } catch (Exception $e) {
            echo($slct->assemble());
            die;
        }
    }

    public function buscarIdVinculada($idPreProjeto)
    {

        $slct = $this->select();
        $slct->setIntegrityCheck(false);
        $slct->from(
            array(
                "p" => $this->_name
            ),
            [],
            $this->_schema
        );

        $slct->joinInner(
            array("s" => "vSegmento"),
            "p.Segmento = s.Codigo",
            array("idVinculada" => "s.idOrgao"),
            $this->_schema
        );

        $slct->joinInner(
            array("o" => "Orgaos"),
            "s.idOrgao = o.Codigo",
            array("Vinculada" => "o.Sigla"),
            $this->_schema
        );

        $slct->where('p.stPrincipal = ?', 1);
        $slct->where('p.idProjeto = ?', $idPreProjeto);

        return $this->fetchRow($slct);
    }
}
