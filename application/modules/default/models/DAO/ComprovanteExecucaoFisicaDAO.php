<?php
class ComprovanteExecucaoFisicaDAO extends Zend_Db_Table
{
    protected $_schema  = "";
    protected $_name    = "BDCORPORATIVO.scSAC.tbComprovanteExecucao";
    protected $_primary = "idComprovante";

    public static function cadastrar($dados)
    {
        $db= Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);

        $cadastrar = $db->insert("BDCORPORATIVO.scSAC.tbComprovanteExecucao", $dados);

        if ($cadastrar) {
            return true;
        } else {
            return false;
        }
    } // fecha m�todo cadastrar()

    /**
     * M�todo para alterar os comprovantes
     * @access public
     * @static
     * @param array $dados
     * @param integer $id
     * @return bool
     */
    public static function alterar($dados, $id)
    {
        $db= Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);

        $where   = "idComprovante = $id";
        $alterar = $db->update("BDCORPORATIVO.scSAC.tbComprovanteExecucao", $dados, $where);

        if ($alterar) {
            return true;
        } else {
            return false;
        }
    } // fecha m�todo alterar()



    /**
     * M�todo para buscar o id do �ltimo comprovante cadastrado
     * @access public
     * @static
     * @param void
     * @return object || integer
     */
    public static function buscarIdComprovante()
    {
        $sql = "SELECT MAX(idComprovante) AS id FROM BDCORPORATIVO.scSAC.tbComprovanteExecucao";

        $db= Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);
        return $db->fetchAll($sql);
    } // fecha m�todo buscarIdComprovante()




    /**
     * M�todo para buscar todas as informa��es dos comprovantes ativos
     * @access public
     * @static
     * @param integer $idPRONAC
     * @param integer $idComprovante
     * @param integer $idProponente
     * @return object || bool
     */
    public static function buscar($idPRONAC = null, $idComprovante = null, $idProponente = null)
    {
        $db= Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);

        $sql = "SELECT doc.idComprovante
					,doc.idPRONAC
					,proj.AnoProjeto+proj.Sequencial AS pronac
					,tipodoc.idTipoDocumento
					,tipodoc.dsTipoDocumento
					,doc.nmComprovante
					,CAST(doc.dsComprovante AS TEXT) AS dsComprovante
					,arq.idArquivo
					,arq.nmArquivo
					,arq.sgExtensao AS sgExtensaoArquivo
					,arq.dsTipoPadronizado AS dsTipoArquivo
					,arq.nrTamanho AS nrTamanhoArquivo
					,CONVERT(CHAR(10), arq.dtEnvio,103) + ' ' + CONVERT(CHAR(8), arq.dtEnvio,108) AS dtEnvioArquivo
					,arq.dsHash AS dsHashArquivo
					,arq.stAtivo AS stArquivo
					,arqimg.biArquivo
					,doc.idSolicitante
					,CONVERT(CHAR(10), doc.dtEnvioComprovante,103) + ' ' + CONVERT(CHAR(8), doc.dtEnvioComprovante,108) AS dtEnvioComprovante
					,CAST(doc.dsJustificativaAlteracao AS TEXT) AS dsJustificativaAlteracao
					,CAST(doc.dsParecerComprovante AS TEXT) AS dsParecerComprovante
					,doc.stParecerComprovante
					,CONVERT(CHAR(10), doc.dtParecer,103) + ' ' + CONVERT(CHAR(8), doc.dtParecer,108) AS dtParecer
					,doc.idAvaliadorComprovante
					,CAST(doc.dsJustificativaCoordenador AS TEXT) AS dsJustificativaCoordenador
					,CONVERT(CHAR(10), doc.dtJustificativaCoordenador,103) + ' ' + CONVERT(CHAR(8), doc.dtJustificativaCoordenador,108) AS dtJustificativaCoordenador
					,doc.idCoordenador
					,doc.idComprovanteAnterior 

				FROM BDCORPORATIVO.scSAC.tbComprovanteExecucao doc
					,SAC.dbo.tbTipoDocumento tipodoc
					,BDCORPORATIVO.scCorp.tbArquivo arq
					,BDCORPORATIVO.scCorp.tbArquivoImagem arqimg
					,SAC.dbo.Projetos proj

				WHERE doc.idPRONAC = proj.IdPRONAC 
					AND doc.idTipoDocumento = tipodoc.idTipoDocumento 
					AND doc.idArquivo     = arq.idArquivo 
					AND arq.idArquivo     = arqimg.idArquivo 
					AND doc.stComprovante = 'A' 
					AND arq.stAtivo       = 'A'";

        if (!empty($idPRONAC)) { // restringe pelo id do projeto
            $sql.= "AND doc.idPRONAC      = {$idPRONAC} ";
        }
        if (!empty($idComprovante)) { // restringe pelo id do comprovante
            $sql.= "AND doc.idComprovante = {$idComprovante} ";
        }
        if (!empty($idProponente)) { // restringe pelo id do proponente
            $sql.= "AND doc.idSolicitante = {$idProponente} ";
        }

        $sql.= "ORDER BY doc.dtEnvioComprovante DESC"; // ordem decrescente pela data

        return $db->fetchAll($sql);
    } // fecha m�todo buscar()



    /**
     * M�todo para buscar todas as informa��es dos �timos hist�ricos de comprovantes ativos
     * @access public
     * @static
     * @param integer $idPRONAC
     * @param integer $idComprovante
     * @param integer $idProponente
     * @return object || bool
     */
    public static function buscarDocumentos($idPRONAC = null, $idComprovante = null, $idProponente = null)
    {
        $db= Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);

        $sql = "SELECT doc.idComprovante
					,doc.idPRONAC
					,tipodoc.idTipoDocumento
					,tipodoc.dsTipoDocumento
					,doc.nmComprovante
					,doc.dsComprovante
					,arq.idArquivo
					,arq.nmArquivo
					,arq.sgExtensao AS sgExtensaoArquivo
					,arq.dsTipoPadronizado AS dsTipoArquivo
					,arq.nrTamanho AS nrTamanhoArquivo
					,CONVERT(CHAR(10), arq.dtEnvio,103) + ' ' + CONVERT(CHAR(8), arq.dtEnvio,108) AS dtEnvioArquivo
					,arq.dsHash AS dsHashArquivo
					,arq.stAtivo AS stArquivo
					,arqimg.biArquivo
					,doc.idSolicitante
					,CONVERT(CHAR(10), doc.dtEnvioComprovante,103) + ' ' + CONVERT(CHAR(8), doc.dtEnvioComprovante,108) AS dtEnvioComprovante
					,doc.dsJustificativaAlteracao
					,doc.dsParecerComprovante
					,doc.stParecerComprovante
					,CONVERT(CHAR(10), doc.dtParecer,103) + ' ' + CONVERT(CHAR(8), doc.dtParecer,108) AS dtParecer
					,doc.idAvaliadorComprovante
					,doc.dsJustificativaCoordenador
					,CONVERT(CHAR(10), doc.dtJustificativaCoordenador,103) + ' ' + CONVERT(CHAR(8), doc.dtJustificativaCoordenador,108) AS dtJustificativaCoordenador
					,doc.idCoordenador
					,doc.idComprovanteAnterior 

				FROM BDCORPORATIVO.scSAC.tbComprovanteExecucao doc
					,SAC.dbo.tbTipoDocumento tipodoc
					,BDCORPORATIVO.scCorp.tbArquivo arq
					,BDCORPORATIVO.scCorp.tbArquivoImagem arqimg
					,(SELECT idComprovanteAnterior, MAX(dtEnvioComprovante) dtEnvioComprovante
					  FROM BDCORPORATIVO.scSAC.tbComprovanteExecucao
					  WHERE stComprovante = 'A'
					  GROUP BY idComprovanteAnterior) AS tmp 

				WHERE doc.idTipoDocumento      = tipodoc.idTipoDocumento 
					AND doc.idArquivo          = arq.idArquivo 
					AND arq.idArquivo          = arqimg.idArquivo
					AND doc.dtEnvioComprovante = tmp.dtEnvioComprovante  
					AND doc.stComprovante      = 'A' 
					AND arq.stAtivo            = 'A'";

        if (!empty($idPRONAC)) { // restringe pelo id do projeto
            $sql.= "AND doc.idPRONAC      = {$idPRONAC} ";
        }
        if (!empty($idComprovante)) { // restringe pelo id do comprovante
            $sql.= "AND doc.idComprovante = {$idComprovante} ";
        }
        if (!empty($idProponente)) { // restringe pelo id do proponente
            $sql.= "AND doc.idSolicitante = {$idProponente} ";
        }

        $sql.= "ORDER BY doc.dtEnvioComprovante DESC"; // ordem decrescente pela data

        return $db->fetchAll($sql);
    } // fecha m�todo buscarDocumentos()



    /**
     * M�todo para buscar o hist�rico de cada comprovante
     * @access public
     * @static
     * @param integer $idComprovante
     * @param integer $idComprovanteAnterior
     * @param integer $idProponente
     * @return object || bool
     */
    public static function buscarHistorico($idComprovante, $idComprovanteAnterior, $idProponente = null)
    {
        $db= Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);

        $sql = "SELECT doc.idComprovante
					,doc.idPRONAC
					,tipodoc.idTipoDocumento
					,tipodoc.dsTipoDocumento
					,doc.nmComprovante
					,doc.dsComprovante
					,arq.idArquivo
					,arq.nmArquivo
					,arq.sgExtensao AS sgExtensaoArquivo
					,arq.dsTipoPadronizado AS dsTipoArquivo
					,arq.nrTamanho AS nrTamanhoArquivo
					,CONVERT(CHAR(10), arq.dtEnvio,103) + ' ' + CONVERT(CHAR(8), arq.dtEnvio,108) AS dtEnvioArquivo
					,arq.dsHash AS dsHashArquivo
					,arq.stAtivo AS stArquivo
					,arqimg.biArquivo
					,doc.idSolicitante
					,CONVERT(CHAR(10), doc.dtEnvioComprovante,103) + ' ' + CONVERT(CHAR(8), doc.dtEnvioComprovante,108) AS dtEnvioComprovante
					,doc.dsJustificativaAlteracao
					,doc.dsParecerComprovante
					,doc.stParecerComprovante
					,CONVERT(CHAR(10), doc.dtParecer,103) + ' ' + CONVERT(CHAR(8), doc.dtParecer,108) AS dtParecer
					,doc.idAvaliadorComprovante
					,doc.dsJustificativaCoordenador
					,CONVERT(CHAR(10), doc.dtJustificativaCoordenador,103) + ' ' + CONVERT(CHAR(8), doc.dtJustificativaCoordenador,108) AS dtJustificativaCoordenador
					,doc.idCoordenador
					,doc.idComprovanteAnterior 

				FROM BDCORPORATIVO.scSAC.tbComprovanteExecucao doc
					,SAC.dbo.tbTipoDocumento tipodoc
					,BDCORPORATIVO.scCorp.tbArquivo arq
					,BDCORPORATIVO.scCorp.tbArquivoImagem arqimg

				WHERE doc.idTipoDocumento         = tipodoc.idTipoDocumento 
					AND doc.idArquivo             = arq.idArquivo 
					AND arq.idArquivo             = arqimg.idArquivo
					AND doc.stComprovante         = 'A' 
					AND arq.stAtivo               = 'A'
					AND doc.idComprovanteAnterior = {$idComprovanteAnterior} 
					AND doc.idComprovante         <> {$idComprovante} ";

        if (!empty($idProponente)) { // restringe pelo id do proponente
            $sql.= "AND doc.idSolicitante = {$idProponente} ";
        }

        $sql.= "ORDER BY doc.dtEnvioComprovante DESC"; // ordem decrescente pela data

        return $db->fetchAll($sql);
    } // fecha m�todo buscarHistorico()



    /**
     * Busca os projetos com os status 'Aguardando Avalia��o', 'Em Avalia��o', 'Em Aprova��o' e 'Avaliado'
     * @access public
     * @param string $pronac
     * @param string $status
     * @param date $dt_inicio
     * @param date $dt_fim
     * @return object || bool
     */
    public static function buscarProjetos(
        $pronac = null,
        $status = null,
    $dt_inicio = null,
        $dt_fim = null
    ) {
        $db= Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);

        // busca os dados
        $sql = "SELECT pro.IdPRONAC
					,pro.NomeProjeto
					,pro.AnoProjeto+pro.Sequencial AS pronac
					,doc.stParecerComprovante
					,CONVERT(CHAR(10), doc.dtEnvioComprovante,103) + ' ' + CONVERT(CHAR(8), doc.dtEnvioComprovante,108) AS dtEnvioComprovante
					,CONVERT(CHAR(10), doc.dtJustificativaCoordenador,103) + ' ' + CONVERT(CHAR(8), doc.dtJustificativaCoordenador,108) AS dtJustificativaCoordenador

				FROM BDCORPORATIVO.scSAC.tbComprovanteExecucao doc
					,SAC.dbo.Projetos pro
					,(SELECT idPronac, MAX(dtEnvioComprovante) dtEnvioComprovante
					  FROM BDCORPORATIVO.scSAC.tbComprovanteExecucao
					  WHERE stComprovante = 'A'
					  GROUP BY idPronac) AS tmp

				WHERE doc.idPRONAC = pro.IdPRONAC 
					AND doc.dtEnvioComprovante = tmp.dtEnvioComprovante 
					AND stComprovante = 'A' ";

        // consulta pelo pronac
        if (!empty($pronac)) {
            $sql.= "AND pro.AnoProjeto+pro.Sequencial = '$pronac' ";
        }
        // consulta pelo status do pronac
        if (!empty($status)) {
            // se o projeto tiver pelo menos um comprovante
            // com o status 'Aguardando Avalia��o'
            if ($status == "AG") {
                $sql.= "AND doc.stParecerComprovante = 'AG' ";
            }

            // se o projeto tiver pelo menos um comprovante
            // com o status 'Em Aprova��o'
            if ($status == "EA") {
                $sql.= "AND doc.stParecerComprovante = 'EA' ";
            }

            // se o projeto tiver pelo menos um comprovante
            // com o status 'Em Avalia��o'
            if ($status == "AV") {
                $sql.= "AND doc.stParecerComprovante = 'AV' ";
            }

            // se o projeto n�o tiver comprovantes
            // com os status 'Aguardando Avalia��o', 'Em Aprova��o' em 'Em Avalia��o'
            if ($status == "AA") {
                $sql.= "AND doc.stParecerComprovante <> 'AG' ";
                $sql.= "AND doc.stParecerComprovante <> 'EA' ";
                $sql.= "AND doc.stParecerComprovante <> 'AV' ";
            }
        } // fecha if

        // busca pela data
        if (!empty($dt_inicio) && !empty($dt_fim)) {
            $sql.= "AND doc.dtEnvioComprovante BETWEEN '$dt_inicio' AND '$dt_fim' ";
        } else {
            if (!empty($dt_inicio)) {
                $sql.= "AND doc.dtEnvioComprovante > '$dt_inicio' ";
            }
            if (!empty($dt_fim)) {
                $sql.= "AND doc.dtEnvioComprovante < '$dt_fim' ";
            }
        }

        $sql.= "ORDER BY doc.dtEnvioComprovante DESC;";

        return $db->fetchAll($sql);
    } // fecha buscarProjetos()



    /**
     * Busca o �ltimo comprovante aprovado de acordo com seu hist�rico
     * @access public
     * @param integer $idPRONAC
     * @param integer $idComprovante
     * @param integer $idComprovanteAnterior
     * @return object || bool
     */
    public static function buscarUltimoComprovanteAprovado($idPRONAC, $idComprovante, $idComprovanteAnterior)
    {
        $db= Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);

        // busca os dados
        $sql = "SELECT doc.idComprovante
					,doc.idPRONAC
					,tipodoc.idTipoDocumento
					,tipodoc.dsTipoDocumento
					,doc.nmComprovante
					,doc.dsComprovante
					,arq.idArquivo
					,arq.nmArquivo
					,arq.sgExtensao AS sgExtensaoArquivo
					,arq.dsTipoPadronizado AS dsTipoArquivo
					,arq.nrTamanho AS nrTamanhoArquivo
					,CONVERT(CHAR(10), arq.dtEnvio,103) + ' ' + CONVERT(CHAR(8), arq.dtEnvio,108) AS dtEnvioArquivo
					,arq.dsHash AS dsHashArquivo
					,arq.stAtivo AS stArquivo
					,arqimg.biArquivo
					,doc.idSolicitante
					,CONVERT(CHAR(10), doc.dtEnvioComprovante,103) + ' ' + CONVERT(CHAR(8), doc.dtEnvioComprovante,108) AS dtEnvioComprovante
					,doc.dsJustificativaAlteracao
					,doc.dsParecerComprovante
					,doc.stParecerComprovante
					,CONVERT(CHAR(10), doc.dtParecer,103) + ' ' + CONVERT(CHAR(8), doc.dtParecer,108) AS dtParecer
					,doc.idAvaliadorComprovante
					,doc.dsJustificativaCoordenador
					,CONVERT(CHAR(10), doc.dtJustificativaCoordenador,103) + ' ' + CONVERT(CHAR(8), doc.dtJustificativaCoordenador,108) AS dtJustificativaCoordenador
					,doc.idCoordenador
					,doc.idComprovanteAnterior 

				FROM BDCORPORATIVO.scSAC.tbComprovanteExecucao doc
					,SAC.dbo.tbTipoDocumento tipodoc
					,BDCORPORATIVO.scCorp.tbArquivo arq
					,BDCORPORATIVO.scCorp.tbArquivoImagem arqimg 

				WHERE doc.idTipoDocumento         = tipodoc.idTipoDocumento 
					AND doc.idArquivo             = arq.idArquivo 
					AND arq.idArquivo             = arqimg.idArquivo 
					AND doc.idPRONAC              = {$idPRONAC}  
					AND doc.stParecerComprovante  = 'AD' 
					AND doc.idComprovante         <> {$idComprovante} 
					AND doc.idComprovanteAnterior = {$idComprovanteAnterior}";

        return $db->fetchAll($sql);
    }
} 
