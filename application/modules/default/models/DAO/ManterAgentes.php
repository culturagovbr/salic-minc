<?php

/**
 * ManterAgentes
 *
 * @uses MinC
 * @uses _Db_Table_Abstract
 * @author  wouerner <wouerner@gmail.com>
 */
class ManterAgentes extends MinC_Db_Table_Abstract
{
    protected $_name = 'area';
    protected $_schema = 'sac';


    public static function buscaAgentes($cnpjcpf = null, $nome = null, $idAgente = null)
    {
        $sql = "SELECT DISTINCT A.idAgente, A.CNPJCPF, A.CNPJCPFSuperior, A.TipoPessoa, N.Descricao Nome, E.Cep CEP, E.UF, U.Sigla dsUF, E.Cidade, M.Descricao dsCidade, E.TipoEndereco,
					   VE.Descricao dsTipoEndereco, E.TipoLogradouro, VL.Descricao dsTipoLogradouro, E.Logradouro, E.Numero,
					   E.Complemento, E.Bairro, T.stTitular, E.Divulgar DivulgarEndereco, E.Status EnderecoCorrespondencia,
					   T.cdArea, SA.Descricao dsArea, T.cdSegmento, SS.Descricao dsSegmento
				FROM AGENTES.dbo.Agentes A
						LEFT join AGENTES.dbo.Nomes N on N.idAgente = A.idAgente
						LEFT join AGENTES.dbo.EnderecoNacional E on E.idAgente = A.idAgente
						LEFT join AGENTES.dbo.Municipios M  on M.idMunicipioIBGE = E.Cidade
						LEFT join AGENTES.dbo.UF U on U.idUF = E.UF
						LEFT join AGENTES.dbo.Verificacao VE on VE.idVerificacao = E.TipoEndereco
						LEFT join AGENTES.dbo.Verificacao VL on VL.idVerificacao = E.TipoLogradouro
						LEFT join AGENTES.dbo.tbTitulacaoConselheiro T on T.idAgente = A.idAgente
						LEFT join AGENTES.dbo.Visao V on V.idAgente = A.idAgente
						LEFT join SAC.dbo.Area SA on SA.Codigo = T.cdArea
						LEFT join SAC.dbo.Segmento SS on SS.Codigo = T.cdSegmento
				WHERE (A.TipoPessoa = 0 OR A.TipoPessoa = 1) ";

        if (!empty($cnpjcpf)) {
            $sql.= " AND A.CNPJCPF = '$cnpjcpf'";
        }
        if (!empty($nome)) {
            $sql.= " AND N.Descricao LIKE '$nome%'";
        }
        if (!empty($idAgente)) {
            $sql.= " AND A.idAgente = $idAgente";
        }

        $sql.= " ORDER BY N.Descricao ASC";

        $db= Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);

        return $db->fetchAll($sql);
    } // fecha m�todo buscaAgentes()



    public static function buscaDirigentes($cnpjcpf = null, $nome = null)
    {
        $sql = "SELECT a.idAgente
				,a.CNPJCPF
				,a.CNPJCPFSuperior
				,n.Descricao AS Nome

			FROM Agentes.dbo.Agentes a
				,Agentes.dbo.Nomes n
				,Agentes.dbo.Visao vis
				,Agentes.dbo.Verificacao ver
				,Agentes.dbo.Vinculacao vin
				,Agentes.dbo.Tipo tp

			WHERE a.idAgente = n.idAgente
				AND a.idAgente = vis.idAgente
				AND a.idAgente = vin.idAgente
				AND tp.idTipo = ver.IdTipo
				AND ver.idVerificacao = vis.Visao
				AND (TipoPessoa = 0 OR TipoPessoa = 1)
				AND (n.TipoNome = '18' OR n.TipoNome = '19')
				AND vis.Visao = '198' ";

        if (!empty($cnpjcpf)) {
            $sql.= " AND a.CNPJCPFSuperior = '$cnpjcpf'";
        }
        if (!empty($nome)) {
            $sql.= " AND n.Descricao = '$nome'";
        }

        $sql.= " ORDER BY n.Descricao";

        $db= Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);

        return $db->fetchAll($sql);
    } // fecha m�todo buscaDirigentes()



    public static function buscaEmails($idAgente)
    {
        $Sql = "SELECT I.idInternet, " .
                        "I.idAgente, " .
                        "I.TipoInternet, " .
                        "V.Descricao tipo, " .
                        "I.Descricao, " .
                        "I.Status, " .
                        "I.Divulgar
							FROM " .
                            "AGENTES.dbo.Internet I, " .
                            "AGENTES.dbo.Tipo T, " .
                            "AGENTES.dbo.Verificacao V
								WHERE  I.TipoInternet = V.idVerificacao " .
                                    "AND T.idTipo = V.IdTipo " .
                                    "AND I.idAgente =".$idAgente;


        $db= Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);

        return $db->fetchAll($Sql);
    }



    public static function buscaFones($idAgente)
    {
        $Sql = "SELECT F.idTelefone, " .
                      "F.DDD, " .
                      "D.Codigo,  " .
                      "F.idAgente, " .
                      "F.TipoTelefone, " .
                      "V.Descricao dsTelefone, " .
                      "F.UF, " .
                      "UF.Sigla ufSigla, " .
                      "F.Numero, " .
                      "F.Divulgar " .
                            "FROM AGENTES.dbo.Telefones F, " .
                                 "AGENTES.dbo.Verificacao V, " .
                                 "AGENTES.dbo.DDD D, " .
                                 "AGENTES.dbo.UF UF	" .
                                    "WHERE F.TipoTelefone = V.idVerificacao " .
                                    "AND F.UF = UF.idUF	" .
                                    "AND F.DDD = D.idDDD " .
                                    "AND F.idAgente =".$idAgente;


        $db= Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);

        return $db->fetchAll($Sql);
    }



    public static function buscarAreasCulturais()
    {
        $sql = "SELECT Codigo AS id, Descricao AS descricao ";
        $sql.= "FROM SAC.dbo.Area ";
        $sql.= "WHERE Codigo <> 7 ";
        $sql.= "ORDER BY Descricao;";

        try {
            $db = Zend_Db_Table::getDefaultAdapter();
            $db->setFetchMode(Zend_DB::FETCH_OBJ);
        } catch (Zend_Exception_Db $e) {
            $this->view->message = "Erro ao buscar Área Cultural: " . $e->getMessage();
        }

        return $db->fetchAll($sql);
    }

    /**
     * listarAreasCulturais
     *
     * @access public
     * @return void
     */
    public function listarAreasCulturais()
    {
        $db = Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);

        $sql = $db->select()
            ->from(array('area'), array('codigo AS id', 'descricao AS descricao'), $this->getSchema('sac'))
            ->where('codigo <> ?', '7')
            ->order('descricao');

        return $db->fetchAll($sql);
    }

    // ************************** Cadatros Gerais ***************************************


    public static function cadastraAgente($cnpjcpf)
    {
        $sqlInsert = "Insert Into Agentes.dbo.Agentes (CNPJCPF) values ('".$cnpjcpf."')";

        try {
            $db = Zend_Db_Table::getDefaultAdapter();
            $db->setFetchMode(Zend_DB::FETCH_OBJ);
            $i = $db->query($sqlInsert);
        } catch (Zend_Exception_Db $e) {
            $this->view->message = "Erro ao cadastrar o Agente: " . $e->getMessage();
        }
    }




    public static function cadastraDirigente($cnpjcpf, $cnpjSuperior)
    {
        $sqlInsert = "Insert Into Agentes.dbo.Agentes (CNPJCPF, CNPJCPFSuperior) values ('".$cnpjcpf."', '".$cnpjSuperior."')";

        try {
            $db = Zend_Db_Table::getDefaultAdapter();
            $db->setFetchMode(Zend_DB::FETCH_OBJ);
            $i = $db->query($sqlInsert);
        } catch (Zend_Exception_Db $e) {
            $this->view->message = "Erro ao cadastrar o Dirigente: " . $e->getMessage();
        }
    }




    public static function associarAgenteDirigente($idAgente, $CNPJSuperior)
    {
        $sql = "UPDATE Agentes.dbo.Agentes SET CNPJCPFSuperior = $CNPJSuperior WHERE idAgente = $idAgente";

        try {
            $db = Zend_Db_Table::getDefaultAdapter();
            $db->setFetchMode(Zend_DB::FETCH_OBJ);
            $i = $db->query($sql);
        } catch (Zend_Exception_Db $e) {
            $this->view->message = "Erro ao associar o Dirigente: " . $e->getMessage();
        }
    }



    public static function verificarVincularDirigente($idAgente, $idVinculado, $idVinculoPrincipal)
    {
        $sql = "SELECT * FROM AGENTES.dbo.Vinculacao WHERE idAgente = $idAgente AND idVinculado = $idVinculado AND idVinculoPrincipal = $idVinculoPrincipal";

        try {
            $db = Zend_Db_Table::getDefaultAdapter();
            $db->setFetchMode(Zend_DB::FETCH_OBJ);
        } catch (Zend_Exception_Db $e) {
            $this->view->message = "Erro ao verificar dirigente vinculado: " . $e->getMessage();
        }

        return $db->fetchAll($sql);
    }



    public static function vincularDirigente($idAgente, $idVinculado, $idVinculoPrincipal, $Usuario)
    {
        $sql = "INSERT INTO AGENTES.dbo.Vinculacao (idAgente, idVinculado, idVinculoPrincipal, Usuario) " .
                "VALUES($idAgente, $idVinculado, $idVinculoPrincipal, $Usuario)";

        try {
            $db = Zend_Db_Table::getDefaultAdapter();
            $db->setFetchMode(Zend_DB::FETCH_OBJ);
            $i = $db->query($sql);
        } catch (Zend_Exception_Db $e) {
            $this->view->message = "Erro ao vincular o Dirigente: " . $e->getMessage();
        }
    }
} // fecha class
