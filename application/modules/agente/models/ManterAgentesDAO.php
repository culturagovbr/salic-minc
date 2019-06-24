<?php

class Agente_Model_ManterAgentesDAO extends MinC_Db_Table_Abstract
{
    /**
     * Metodo para buscar agentes
     * @access public
     * @static
     * @param string $cnpjcpf
     * @param string $nome
     * @param integer $idAgente
     * @return object
     */
    public static function buscarAgentes($cnpjcpf = null, $nome = null, $idAgente = null)
    {
        $db= Zend_Db_Table::getDefaultAdapter();
        $schemaAgentes = parent::getStaticTableName('agentes');
        $schemaSac = parent::getStaticTableName('sac');
        $schemaAcesso = parent::getStaticTableName('ControleDeAcesso');

        $a = array(
            'a.idagente'
            ,'a.cnpjcpf'
            ,'a.cnpjcpfsuperior'
            ,'a.tipopessoa'
            ,'a.dtatualizacao'
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

        $sql = $db->select()->distinct()->from(array('a' => 'agentes'), $a, $schemaAgentes)
            ->joinLeft(array('n' => 'nomes'), 'n.idagente = a.idagente', array('n.descricao as nome'), $schemaAgentes)
            ->joinLeft(array('e' => 'endereconacional'), 'e.idagente = a.idagente', $e, $schemaAgentes)
            ->joinLeft(array('m' => 'municipios'), 'm.idmunicipioibge = e.cidade', array('*', 'm.descricao as dscidade'), $schemaAgentes)
            ->joinLeft(array('u' => 'uf'), 'u.iduf = e.uf', 'u.sigla as dsuf', $schemaAgentes)
            ->joinLeft(array('ve' => 'verificacao'), 've.idverificacao = e.tipoendereco', 've.descricao as dstipoendereco', $schemaAgentes)
            ->joinLeft(array('vl' => 'verificacao'), 'vl.idverificacao = e.tipologradouro', 'vl.descricao as dstipologradouro', $schemaAgentes)
            ->joinLeft(array('t' => 'tbtitulacaoconselheiro'), 't.idagente = a.idagente', $t, $schemaAgentes)
            ->joinLeft(array('v' => 'visao'), 'v.idagente = a.idagente', '*', $schemaAgentes)
            ->joinLeft(array('sa' => 'area'), 'sa.codigo = t.cdarea', 'sa.descricao as dsarea', $schemaSac)
            ->joinLeft(array('ss' => 'segmento'), 'ss.codigo = t.cdsegmento', 'ss.descricao as dssegmento', $schemaSac)
            ->joinLeft(array('so' => 'SGCacesso'), 'a.Usuario= so.IdUsuario',
                ['so.IdUsuario as idResponsavel', 'so.Cpf as cpfResponsavel', 'so.Nome as nomeResponsavel'],
                $schemaAcesso)
            ->where('a.tipopessoa = 0 or a.tipopessoa = 1')
            ;

        if (!empty($cnpjcpf)) {
            # busca pelo cpf/cnpj
            $sql->where('a.cnpjcpf = ?', $cnpjcpf);
        }
        if (!empty($nome)) {
            # filtra pelo nome
            $sql->where('n.descricao LIKE ?', '%'.$nome.'%');
        }
        if (!empty($idAgente)) {
            # busca de acordo com o id do agente
            $sql->where('a.idagente = ?', $idAgente);
        }

        $sql->order(array('e.status Desc', 'n.descricao Asc'));
        $db->setFetchMode(Zend_DB::FETCH_OBJ);
        return $db->fetchAll($sql);
    }

    /**
     * M�todo para buscar agentes vinculados
     *
     * @access public
     * @static
     * @param string $cnpjcpfSuperior
     * @param string $nome
     * @param integer $idAgente
     * @param integer $idVinculado
     * @param integer $idVinculoPrincipal
     * @return object
     */
    public static function buscarVinculados($cnpjcpfSuperior = null, $nome = null, $idAgente = null, $idVinculado = null, $idVinculoPrincipal = null)
    {
        $db= Zend_Db_Table::getDefaultAdapter();

        $a = array(
            'a.idAgente'
            ,'a.CNPJCPF'
            ,'a.CNPJCPFSuperior'
        );

        $sql = $db->select()
            ->from(array('a' => 'Agentes'), $a, 'AGENTES.dbo')
            ->joinLeft(array('n' => 'Nomes'), 'N.idAgente = A.idAgente', array('n.Descricao AS Nome'), 'AGENTES.dbo')
            ->joinLeft(array('vis' => 'Visao'), 'a.idAgente = vis.idAgente', null, 'AGENTES.dbo')
            ->joinLeft(array('ver' => 'Verificacao'), 'ver.idVerificacao = vis.Visao', null, 'AGENTES.dbo')
            ->joinLeft(array('vin' => 'Vinculacao'), 'a.idAgente = vin.idAgente', null, 'AGENTES.dbo')
            ->joinLeft(array('tp' => 'Tipo'), 'tp.idTipo = ver.IdTipo', null, 'AGENTES.dbo')
            ->joinLeft(array('so' => 'SGCacesso'), 'a.Usuario= so.IdUsuario',
                ['so.IdUsuario as idResponsavel', 'so.Cpf as cpfResponsavel', 'so.Nome as nomeResponsavel'],
                'ControleDeAcesso.dbo')
            ->where('a.TipoPessoa = 0 OR a.TipoPessoa = 1')
            ->where('n.TipoNome = 18 OR n.TipoNome = 19')
            ->where('vis.Visao = 198')
            ;

        if (!empty($cnpjcpfSuperior)) { // busca pelo cnpj/cpf com o vinculo principal
            $sql->where('a.CNPJCPFSuperior = ?', $cnpjcpfSuperior);
        }
        if (!empty($nome)) { // filtra pelo nome
            $sql->where('n.Descricao LIKE ?', "$nome%");
        }
        if (!empty($idAgente)) { // busca pelo idAgente
            $sql->where('vin.idAgente =  ?', $idAgente);
        }
        if (!empty($idVinculado)) { // busca pelo idVinculado
            $sql->where('vin.idVinculado =  ?', $idVinculado);
        }
        if (!empty($idVinculoPrincipal)) {// busca pelo idVinculoPrincipal
            $sql->where('vin.idVinculoPrincipal =  ?', $idVinculoPrincipal);
        }

        $sql->order(array('n.Descricao'));

        $db->setFetchMode(Zend_DB::FETCH_OBJ);
        return $db->fetchAll($sql);
    }

    /**
     * M�todo para buscar os endere�os do agente
     *
     * @access public
     * @static
     * @param integer $idAgente
     * @return object
     * @deprecated utilizar buscarEnderecos da DbTable Endereco Nacional
     */
    public static function buscarEnderecos($idAgente = null)
    {
        $db = Zend_Db_Table::getDefaultAdapter();

        $ve = array(
            'VE.Descricao as TipoEndereco',
            'VE.idVerificacao as CodTipoEndereco',
        );

        $m = array(
            'M.Descricao as Municipio',
            'M.idMunicipioIBGE as CodMun',
        );

        $u = array(
            'U.Sigla as UF',
            'U.idUF as CodUF'
        );

        $e = array(
            'idEndereco',
            'idAgente',
            'Logradouro',
            'TipoLogradouro',
            'Numero',
            'Bairro',
            'Complemento',
            'Cep',
            'Status',
            'Divulgar',
            'Usuario',
        );

        $sql = $db->select()
            ->from(array('E' => 'EnderecoNacional'), $e, 'AGENTES.dbo')
            ->joinLeft(array('VE' => 'Verificacao'), 'VE.idVerificacao = E.TipoEndereco', $ve, 'AGENTES.dbo')
            ->joinLeft(array('M' => 'Municipios'), 'M.idMunicipioIBGE = E.Cidade', $m, 'AGENTES.dbo')
            ->joinLeft(array('U' => 'UF'), 'U.idUF = E.UF', $u, 'AGENTES.dbo')
            ->joinLeft(array('VL' => 'Verificacao'), 'VL.idVerificacao = E.TipoLogradouro', array('VL.Descricao as dsTipoLogradouro'), 'AGENTES.dbo')
            ->where('E.idAgente = ?', $idAgente)
            ->order(array('Status DESC'))
            ;

        $db->setFetchMode(Zend_DB::FETCH_OBJ);
        return $db->fetchAll($sql);
    }

    /**
     * M�todo para buscar os e-mails do agente
     *
     * @access public
     * @static
     * @param integer $idAgente
     * @return object
     * @deprecated Utilizar metodo da Dbtable
     */
    public static function buscarEmails($idAgente = null)
    {
        $tblAgentes = new Agente_Model_DbTable_Agentes();
        $db = Zend_Db_Table::getDefaultAdapter();

        $i = array(
            'i.idinternet'
            ,'i.idagente'
            ,'i.tipointernet'
            ,'i.descricao'
            ,'i.status'
            ,'i.divulgar'
        );

        $sql = $db->select()
            ->from(array('i' => 'internet'), $i, $tblAgentes->getSchema('agentes'))
            ->join(array('v' => 'verificacao'), 'i.tipointernet = v.idverificacao', 'v.descricao as tipo', $tblAgentes->getSchema('agentes'))
            ->join(array('t' => 'tipo'), 't.idtipo = v.idtipo', null, $tblAgentes->getSchema('agentes'));

        if (!empty($idAgente)) {// busca de acordo com o id do agente

            $sql->where('i.idagente = ?', $idAgente);
        }

        $db->setFetchMode(Zend_DB::FETCH_OBJ);
        return $db->fetchAll($sql);
    }

    /**
     * M�todo para buscar os telefones do agente
     *
     * @access public
     * @static
     * @param integer $idAgente
     * @return object
     * @deprecated Utilizar metodo da Dbtable
     */
    public static function buscarFones($idAgente = null)
    {
        $tblAgentes = new Agente_Model_DbTable_Agentes();
        $db = Zend_Db_Table::getDefaultAdapter();

        $tl = array(
            'tl.idtelefone',
            'tl.tipotelefone',
            'tl.numero',
            'tl.divulgar',
        );

        $ddd = array(
            'ddd.codigo as ddd',
            'ddd.codigo as codigo',
        );

        $sql = $db->select()
            ->from(array('tl' => 'telefones'), $tl, $tblAgentes->getSchema('agentes'))
            ->join(array('uf' => 'uf'), 'uf.iduf = tl.uf', array('uf.sigla as ufsigla'), $tblAgentes->getSchema('agentes'))
            ->join(array('ag' => 'agentes'), 'ag.idagente = tl.idagente', array('ag.idagente'), $tblAgentes->getSchema('agentes'))
            ->joinLeft(array('ddd' => 'ddd'), 'tl.ddd = ddd.codigo', $ddd, $tblAgentes->getSchema('agentes'))
            ->joinLeft(array('v' => 'verificacao'), 'v.idverificacao = tl.tipotelefone', array('v.descricao as dstelefone'), $tblAgentes->getSchema('agentes'))
            ;
        if (!empty($idAgente)) { // busca de acordo com o id do agente
            $sql->where('tl.idagente = ?', $idAgente);
        }
        $db->setFetchMode(Zend_DB::FETCH_OBJ);
        return $db->fetchAll($sql);
    }

    /**
     * Metodo para cadastrar dados do agente
     * @access public
     * @static
     * @param array $dados
     * @return boolean
     */
    public static function cadastrarAgente($dados)
    {
        $db= Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);

        $insert = $db->insert(MinC_Db_Table_Abstract::getStaticTableName('agentes', 'agentes'), $dados); // cadastra

        if ($insert) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * M�todo para cadastrar dados do agente
     * @access public
     * @static
     * @param array $dados
     * @return int (idAgente)
     */
    public static function cadastraAgente($dados)
    {
        //INSTANCIANDO UM OBJETO DE ACESSO AOS DADOS DA TABELA
        $Agentes = new Agente_Model_DbTable_Agentes();

        $rsAgente = $Agentes->createRow();

        //ATRIBUINDO VALORES AOS CAMPOS QUE FORAM PASSADOS
        if (isset($dados['stTipoRespPergunta'])) {
            $rsAgente->stTipoRespPergunta = $dados['stTipoRespPergunta'];
        }

        if (isset($dados['dsPergunta'])) {
            $rsAgente->dsPergunta = $dados['dsPergunta'];
        }

        if (isset($dados['dtCadastramento'])) {
            $rsAgente->dtCadastramento = $dados['dtCadastramento'];
        }

        if (isset($dados['idPessoaCadastro'])) {
            $rsAgente->idPessoaCadastro = $dados['idPessoaCadastro'];
        }

        //SALVANDO O OBJETO CRIADO
        $id = $rsAgente->save();

        if ($id) {
            return $id;
        } else {
            return false;
        }
    }

    /**
     * Metodo para alterar dados do agente
     * @access public
     * @static
     * @param integer $idAgente
     * @param array $dados
     * @return boolean
     *
     * @todo Existe uma trigger no db que impede o acesso direto a atualizacao. Pendente de verificacao
     */
    public static function alterarAgente($idAgente, $dados)
    {
        $db= Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);



        $where = "idAgente = " . $idAgente; // condicao para alteracao

        $update = $db->update('AGENTES.dbo.Agentes', $dados, $where); // altera

        if ($update) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * M�todo para cadastrar o v�nculo entre os agentes
     * @access public
     * @static
     * @param array $dados
     * @return boolean
     */
    public static function cadastrarVinculados($dados)
    {
        $db= Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);

        $insert = $db->insert('AGENTES.dbo.Vinculacao', $dados); // cadastra

        return ($insert) ? true : false;
    }
}
