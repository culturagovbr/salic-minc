<?php
class PlanoDistribuicao extends MinC_Db_Table_Abstract
{
    protected $_schema = "sac";
    protected $_name = "planodistribuicaoproduto";
    protected $_primary = "idPlanoDistribuicao";
    /**
     * Grava registro. Se seja passado um ID ele altera um registro existente
     * @param array $dados - array com dados referentes as colunas da tabela no formato "nome_coluna_1"=>"valor_1","nome_coluna_2"=>"valor_2"
     * @return ID do registro inserido/alterado ou FALSE em caso de erro
     */
    public function salvar($dados)
    {
        //INSTANCIANDO UM OBJETO DE ACESSO AOS DADOS DA TABELA
        $tmpTblPlanoDistribuicao = new PlanoDistribuicao();

        //DECIDINDO SE SERA FEITA UM INSERT OU UPDATE
        if (!empty($dados['idPlanoDistribuicao'])) {
            $tmpRsPlanoDistribuicao = $tmpTblPlanoDistribuicao->find($dados['idPlanoDistribuicao'])->current();
        } else {
            $tmpRsPlanoDistribuicao = $tmpTblPlanoDistribuicao->createRow();
        }
        //ATRIBUINDO VALORES AOS CAMPOS QUE FORAM PASSADOS
        if (isset($dados['idProjeto'])) {
            $tmpRsPlanoDistribuicao->idProjeto = $dados['idProjeto'];
        }
        if (isset($dados['idProduto'])) {
            $tmpRsPlanoDistribuicao->idProduto = $dados['idProduto'];
        }
        if (isset($dados['Area'])) {
            $tmpRsPlanoDistribuicao->Area = $dados['Area'];
        }
        if (isset($dados['Segmento'])) {
            $tmpRsPlanoDistribuicao->Segmento = $dados['Segmento'];
        }
        if (isset($dados['idPosicaoDaLogo'])) {
            $tmpRsPlanoDistribuicao->idPosicaoDaLogo = $dados['idPosicaoDaLogo'];
        }
        if (isset($dados['QtdeProduzida'])) {
            $tmpRsPlanoDistribuicao->QtdeProduzida = $dados['QtdeProduzida'];
        }
        if (isset($dados['QtdePatrocinador'])) {
            $tmpRsPlanoDistribuicao->QtdePatrocinador = $dados['QtdePatrocinador'];
        }
        if (isset($dados['QtdeProponente'])) {
            $tmpRsPlanoDistribuicao->QtdeProponente = $dados['QtdeProponente'];
        }
        if (isset($dados['QtdeOutros'])) {
            $tmpRsPlanoDistribuicao->QtdeOutros = $dados['QtdeOutros'];
        }
        if (isset($dados['QtdeVendaNormal'])) {
            $tmpRsPlanoDistribuicao->QtdeVendaNormal = $dados['QtdeVendaNormal'];
        }
        if (isset($dados['QtdeVendaPromocional'])) {
            $tmpRsPlanoDistribuicao->QtdeVendaPromocional = $dados['QtdeVendaPromocional'];
        }
        if (isset($dados['PrecoUnitarioNormal'])) {
            $tmpRsPlanoDistribuicao->PrecoUnitarioNormal = $dados['PrecoUnitarioNormal'];
        }
        if (isset($dados['PrecoUnitarioPromocional'])) {
            $tmpRsPlanoDistribuicao->PrecoUnitarioPromocional = $dados['PrecoUnitarioPromocional'];
        }
        if (isset($dados['stPrincipal'])) {
            $tmpRsPlanoDistribuicao->stPrincipal = $dados['stPrincipal'];
        }
        if (isset($dados['Usuario'])) {
            $tmpRsPlanoDistribuicao->Usuario = $dados['Usuario'];
        }
        if (isset($dados['dsJustificativaPosicaoLogo'])) {
            $tmpRsPlanoDistribuicao->dsJustificativaPosicaoLogo = $dados['dsJustificativaPosicaoLogo'] ;
        }
        if (isset($dados['stPlanoDistribuicaoProduto'])) {
            $tmpRsPlanoDistribuicao->stPlanoDistribuicaoProduto = $dados['stPlanoDistribuicaoProduto'] ;
        }

//        echo "<pre>";

        //SALVANDO O OBJETO CRIADO

        $id = $tmpRsPlanoDistribuicao->save();

        if ($id) {
            return $id;
        } else {
            return false;
        }
    }

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
        // criando objeto do tipo select
        $slct = $this->select();

        $slct->setIntegrityCheck(false);

        $cols = array_merge($this->_getCols(), array(
                "FORMAT(a.QtdeProponente, '0,0','pt-br') as QtdeProponente",
                "FORMAT(a.QtdeProduzida, '0,0','pt-br') as QtdeProduzida",
                "FORMAT(a.QtdePatrocinador, '0,0','pt-br') as QtdePatrocinador",
                "FORMAT(a.QtdeOutros, '0,0','pt-br') as QtdeOutros",
                "FORMAT(a.QtdeVendaPopularPromocional, '0,0','pt-br') as QtdeVendaPopularPromocional",
                "FORMAT(a.QtdeVendaNormal, '0,0','pt-br') as QtdeVendaNormal",
                "FORMAT(a.QtdeVendaPromocional, '0,0','pt-br') as QtdeVendaPromocional",
                "FORMAT(a.QtdeVendaPopularNormal, '0,0','pt-br') as QtdeVendaPopularNormal",
                "FORMAT(a.vlUnitarioPopularNormal, 'N','pt-br') as vlUnitarioPopularNormal",
                "FORMAT( a.vlUnitarioNormal, 'N','pt-br') AS vlUnitarioNormal",
                "FORMAT( a.ReceitaPopularNormal, 'N','pt-br') AS ReceitaPopularNormal",
                "FORMAT( a.ReceitaPopularPromocional, 'N','pt-br') AS ReceitaPopularPromocional",
                "FORMAT( a.PrecoUnitarioPromocional, 'N','pt-br') AS PrecoUnitarioPromocional",
                "FORMAT( a.PrecoUnitarioNormal, 'N', 'pt-br') AS PrecoUnitarioNormal",
                "FORMAT( a.vlReceitaTotalPrevista, 'N', 'pt-br') AS Receita",
                "FORMAT( a.ReceitaPopularNormal, 'N', 'pt-br') AS ValorMedioPopular",
                "FORMAT( a.ReceitaPopularPromocional, 'N', 'pt-br') AS ValorMedioProponente"
            ));

        $slct->from(array("a"=> $this->_name), $cols, $this->_schema);
        $slct->joinInner(
                array("b"=>"produto"),
                            "a.idproduto = b.codigo",
                            array("Produto"=>"b.descricao"),
                            $this->_schema
            );
        $slct->joinInner(
                array("ar"=>"area"),
                            "a.area = ar.codigo",
                            array("DescricaoArea"=>"ar.descricao"),
                $this->_schema
            );
        $slct->joinInner(
                array("s"=>"segmento"),
                            "a.segmento = s.codigo",
                            array("DescricaoSegmento"=>"s.descricao"),
                $this->_schema
            );

        $slct->where('a.stplanodistribuicaoproduto = ?', '1');

        // adicionando clausulas where
        foreach ($where as $coluna=>$valor) {
            $slct->where($coluna, $valor);
        }

        // adicionando linha order ao select
        $slct->order($order);

        // paginacao
        if ($tamanho > -1) {
            $tmpInicio = 0;
            if ($inicio > -1) {
                $tmpInicio = $inicio;
            }
            $slct->limit($tamanho, $tmpInicio);
        }
//            echo $slct;die;

        //SETANDO A QUANTIDADE DE REGISTROS
        $this->_totalRegistros = $this->pegaTotal($where);
        //$this->_totalRegistros = 100;
        // retornando os registros conforme objeto select
        return $this->fetchAll($slct);
    }

    /**
     * Retorna registros do banco de dados
     * @param array $where - array com dados where no formato "nome_coluna_1"=>"valor_1","nome_coluna_2"=>"valor_2"
     * @param array $order - array com orders no formado "coluna_1 desc","coluna_2"...
     * @param int $tamanho - numero de registros que deve retornar
     * @param int $inicio - offset
     * @return Zend_Db_Table_Rowset_Abstract
     */
    public function buscarPlanoDistribuicao($where=array())
    {
        $slct = $this->select();
        $slct->setIntegrityCheck(false);
        $slct->from(
                array('a' => $this->_name),
                array(
                    'a.idPlanoDistribuicao',
                    'a.idProjeto',
                    'a.idProduto',
                    'a.Area',
                    'a.Segmento',
                    'a.idPosicaoDaLogo',
                    'a.QtdeProduzida',
                    'a.QtdePatrocinador',
                    'a.QtdeProponente',
                    'a.QtdeOutros',
                    'a.QtdeVendaNormal',
                    'a.QtdeVendaPromocional',
                    'a.PrecoUnitarioNormal',
                    'a.PrecoUnitarioPromocional',
                    'a.stPrincipal',
                    'a.Usuario',
                    'CAST(a.dsJustificativaPosicaoLogo AS TEXT) AS dsJustificativaPosicaoLogo',
                    'a.Usuario'
                ),
                $this->_schema
        );

        foreach ($where as $coluna=>$valor) {
            $slct->where($coluna, $valor);
        }

        return $this->fetchRow($slct);
    }

    /**
     * Retorna registros do banco de dados
     * @param array $where - array com dados where no formato "nome_coluna_1"=>"valor_1","nome_coluna_2"=>"valor_2"
     * @param array $order - array com orders no formado "coluna_1 desc","coluna_2"...
     * @param int $tamanho - numero de registros que deve retornar
     * @param int $inicio - offset
     * @return Zend_Db_Table_Rowset_Abstract
     */
    public function pegaTotal($where=array(), $order=array(), $tamanho=-1, $inicio=-1)
    {
        // criando objeto do tipo select
        $slct = $this->select();

        $slct->setIntegrityCheck(false);

        $slct->from(array("a"=> $this->_name), '*', $this->_schema);
        $slct->joinInner(
                array("b"=>"Produto"),
                            "a.idProduto = b.Codigo",
                            array("Produto"=>"b.Descricao"),
                $this->_schema
            );
        $slct->joinLeft(
                array("c"=>"verificacao"),
                            "a.idPosicaoDaLogo = c.idVerificacao",
                            array("PosicaoLogomarca"=>"c.Descricao"),
                $this->_schema
            );

        $slct->where('a.stPlanoDistribuicaoProduto = ?', '1');

        // adicionando clausulas where
        foreach ($where as $coluna=>$valor) {
            $slct->where($coluna, $valor);
        }

        // adicionando linha order ao select
        $slct->order($order);

        // paginacao
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

    public function apagar($id)
    {
        $objApagar = $this->find($id)->current();

        return $objApagar->delete();
    }

    public function atualizarAreaESegmento($area, $segmento, $idProjeto)
    {
        try {
            $arrayDadosPlanoDistribuicaoProduto = array(
                'Area' => $area,
                'Segmento' => $segmento
            );

            $arrayWherePlanoDistribuicaoProduto = array(
                'idProjeto = ?' => $idProjeto,
                'stPrincipal = ?' => '1'
            );
            $this->update($arrayDadosPlanoDistribuicaoProduto, $arrayWherePlanoDistribuicaoProduto);
        } catch (Exception $objException) {
            throw new Exception($objException->getMessage(), 0, $objException);
        }
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
            array("b"=>"produto"),
            "p.idproduto = b.codigo",
            array("Produto"=>"b.descricao"),
            $this->_schema
        );

        $slct->joinInner(
            array("ar"=>"area"),
            "p.area = ar.codigo",
            array("DescricaoArea"=>"ar.descricao"),
            $this->_schema
        );
        $slct->joinInner(
            array("s"=>"segmento"),
            "p.segmento = s.codigo",
            array("DescricaoSegmento"=>"s.descricao"),
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

    /**
     * Faz medias e soma valores para salvar o resumo na tabela plano de distribuicao
     * Tem que salvar a media ponderada do preço popular(receitaPopularNormal) e do proponente(PrecoUnitarioNormal)
     * por isso, os campos receitaPopularParcial e precoUnitarioParcial devem ficar vazios.
     *
     * @param $idPlanoDistribuicao
     */
    public function updateConsolidacaoPlanoDeDistribuicao($idPlanoDistribuicao)
    {
        $cols = array(
            'COALESCE(sum(qtExemplares),0) as QtdeProduzida',
            'COALESCE(sum(qtGratuitaDivulgacao), 0) as QtdeProponente',
            'COALESCE(sum(qtGratuitaPatrocinador), 0) as QtdePatrocinador',
            'COALESCE(sum(qtGratuitaPopulacao), 0) as QtdeOutros',
            'COALESCE(sum(qtPopularIntegral), 0) as QtdeVendaPopularNormal',
            'COALESCE(sum(qtPopularParcial), 0) as QtdeVendaPopularPromocional',
            'COALESCE(avg(vlUnitarioPopularIntegral), 0) as vlUnitarioPopularNormal',
            'COALESCE(sum(vlReceitaPopularIntegral + vlReceitaPopularParcial) / nullif((sum(qtPopularIntegral + qtPopularParcial)), 0), 0) AS ReceitaPopularNormal', #valor médio ponderado do preco popular
            'COALESCE(sum(vlReceitaProponenteIntegral + vlReceitaProponenteParcial) / nullif((sum(qtProponenteIntegral + qtProponenteParcial)), 0), 0) AS PrecoUnitarioNormal', # valor médio ponderado do proponente
            'COALESCE(sum(qtProponenteIntegral), 0) as QtdeVendaNormal',
            'COALESCE(sum(qtProponenteParcial), 0) as QtdeVendaPromocional',
            'COALESCE(avg(vlUnitarioProponenteIntegral),0) as vlUnitarioNormal',
            'COALESCE(sum(vlReceitaPrevista), 0) as  vlReceitaTotalPrevista'
        );

        $sql = $this->select()
            ->setIntegrityCheck(false)
            ->from(
                array('tbDetalhaPlanoDistribuicao'),
                $cols,
                $this->_schema
            )
            ->where('idPlanoDistribuicao = ?', $idPlanoDistribuicao);

        $dados =  $this->fetchRow($sql);
        $dados = $dados->toArray();

        $db = Zend_Db_Table::getDefaultAdapter();
        $db->update('planodistribuicaoproduto', $dados, "idPlanoDistribuicao = " . $idPlanoDistribuicao);
    }

    public function buscarIdVinculada($idPreProjeto)
    {
        $sqlVinculada = "SELECT idOrgao as idVinculada
                                    FROM sac.dbo.PlanoDistribuicaoProduto t
                                    INNER JOIN vSegmento s on (t.Segmento = s.Codigo)
                                    WHERE t.stPrincipal = 1 and idProjeto = '{$idPreProjeto}'";

        $db = Zend_Db_Table::getDefaultAdapter();
        return $db->fetchOne($sqlVinculada);
    }
}
