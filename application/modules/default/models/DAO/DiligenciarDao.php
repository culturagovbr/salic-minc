<?php
class DiligenciarDao extends Zend_Db_Table
{
    public function listarDocumentosExigido($idCodigoDocumentosExigidos = '')
    {
        $where = '';
        if ($idCodigoDocumentosExigidos) {
            $where = ' and Codigo = '.$idCodigoDocumentosExigidos;
        }

        $sql = "SELECT
                    Codigo,
                    Descricao,
                    Area,
                    Opcao,
                    stEstado
                FROM
                    Sac.dbo.DocumentosExigidos
                where Opcao in (1,2) $where
                " ;

        $db = Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);
        $resultado = $db->fetchAll($sql);
        return $resultado;
    }
    public function montarConsulta($dadosConsulta, $adicional = ' where ')
    {
        $where = '';
        if (is_array($dadosConsulta) and count($dadosConsulta)>0) {
            foreach ($dadosConsulta as $key => $value) {
                $where .= ' '.$key.' '.$value.' ';
            }
            $where = $adicional.$where;
        }
        return $where;
    }

    public function listarDiligencias($consulta = array())
    {
        $where = $this->montarConsulta($consulta);

        $sql = "select dil.idDiligencia,pro.NomeProjeto as nomeProjeto,pro.AnoProjeto+pro.Sequencial as pronac,dil.DtSolicitacao as dataSolicitacao,dil.DtResposta as dataResposta,ver.Descricao as tipoDiligencia,dil.Solicitacao,dil.Resposta,arq.nmArquivo,arq.idArquivo,dil.idCodigoDocumentosExigidos
                from SAC.dbo.Projetos pro
                inner join SAC.dbo.tbDiligencia dil on dil.idPronac = pro.IdPRONAC
                inner join SAC.dbo.Verificacao ver on ver.idVerificacao = dil.idTipoDiligencia
                left  join BDCORPORATIVO.scCorp.tbArquivo arq on arq.idArquivo = dil.idArquivo
                $where " ;
        
        $db = Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);
        $resultado = $db->fetchAll($sql);

        return $resultado;
    }
    public function listarDiligenciasPreProjeto($consulta = array())
    {
        $where = $this->montarConsulta($consulta);

        $sql = "select dil.idDiligencia,pre.NomeProjeto as nomeProjeto,pre.idPreProjeto as pronac,dil.DtSolicitacao as dataSolicitacao,dil.DtResposta as dataResposta,ver.Descricao as tipoDiligencia,dil.Solicitacao,dil.Resposta,arq.nmArquivo,arq.idArquivo,dil.idCodigoDocumentosExigidos
                from SAC.dbo.PreProjeto pre
                inner join SAC.dbo.tbDiligencia dil on dil.idPreProjeto = pre.idPreProjeto
                inner join SAC.dbo.Verificacao ver on ver.idVerificacao = dil.idTipoDiligencia
                left  join BDCORPORATIVO.scCorp.tbArquivo arq on arq.idArquivo = dil.idArquivo
                $where " ;

        $db = Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);
        $resultado = $db->fetchAll($sql);

        return $resultado;
    }

    public function dadosProjeto($consulta = array())
    {
        $where = $this->montarConsulta($consulta);

        $sql = "select pro.NomeProjeto as nomeProjeto,pro.AnoProjeto+pro.Sequencial as pronac
                from SAC.dbo.Projetos pro
                $where " ;

        $db = Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);
        $resultado = $db->fetchAll($sql);

        return $resultado;
    }
    public function dadosPreProjeto($consulta = array())
    {
        $where = $this->montarConsulta($consulta);

        $sql = "select pre.NomeProjeto as nomeProjeto,idPreProjeto from SAC.dbo.PreProjeto pre
                $where " ;

        $db = Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);
        $resultado = $db->fetchAll($sql);

        return $resultado;
    }


    public function tipoDiligencia($consulta = array())
    {
        $where = $this->montarConsulta($consulta, ' and ');

        $sql = "select idVerificacao,Descricao from SAC.dbo.Verificacao where idTipo = 8 and stEstado = 1 $where " ;

        $db = Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);
        $resultado = $db->fetchAll($sql);

        return $resultado;
    }

    public function cadastrarDiligencia($dados)
    {
        $atributos  =   '';
        $valores    =   '';
        foreach ($dados as $key=>$values) {
            if ($atributos!='' and $valores!='') {
                $atributos  .=   ',';
                $valores    .=   ',';
            }
            $atributos  .=  $key;
            $valores    .=  $values;
        }
        $sql = "Insert Into SAC.dbo.tbDiligencia({$atributos})values({$valores})";

        $db= Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);
        $dados =  $db->query($sql);

        if ($dados) {
            return true;
        } else {
            return false;
        }
    }

    public function responderDiligencia($dados, $consulta)
    {
        $where = $this->montarConsulta($consulta);
        $valores    =   '';
        foreach ($dados as $key=>$values) {
            if ($valores!='') {
                $valores    .=   ',';
            }
            $valores    .=  ' '.$key.'='.$values.' ';
        }
        $sql = "update SAC.dbo.tbDiligencia set $valores $where";

        $db= Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);
        $dados =  $db->query($sql);

        if ($dados) {
            return true;
        } else {
            return false;
        }
    }

    public function buscarAgenteProjeto($consulta = array())
    {
        $where = $this->montarConsulta($consulta);

        $sql = "select pre.idAgente from SAC.dbo.Projetos pro
                inner join SAC.dbo.PreProjeto pre on pro.idProjeto = pre.idPreProjeto $where " ;

        $db = Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);
        $resultado = $db->fetchAll($sql);

        return $resultado;
    }

    public function buscarAgentePreProjeto($consulta = array())
    {
        $where = $this->montarConsulta($consulta);

        $sql = "select pre.idAgente from SAC.dbo.PreProjeto pre $where " ;

        $db = Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);
        $resultado = $db->fetchAll($sql);

        return $resultado;
    }
}
