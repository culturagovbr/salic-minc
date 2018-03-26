<?php
class Edital extends MinC_Db_Table_Abstract
{
    protected $_banco = 'SAC';
    protected $_name = 'Edital';
    protected $_schema  = 'SAC';

    public function salvar($dados)
    {
        //INSTANCIANDO UM OBJETO DE ACESSO AOS DADOS DA TABELA
        $tmpTblEdital = new Edital();

        if (isset($dados['idEdital'])) {
            $tmpRsEdital = $tmpTblEdital->find($dados['idEdital'])->current();
        } else {
            $tmpRsEdital = $tmpTblEdital->createRow();
        }

        //ATRIBUINDO VALORES AOS CAMPOS QUE FORAM PASSADOS
        if (isset($dados['idOrgao'])) {
            $tmpRsEdital->idOrgao = $dados['idOrgao'];
        }
        if (isset($dados['NrEdital'])) {
            $tmpRsEdital->NrEdital = $dados['NrEdital'];
        }
        if (isset($dados['DtEdital'])) {
            $tmpRsEdital->DtEdital = $dados['DtEdital'];
        }
        if (isset($dados['CelulaOrcamentaria'])) {
            $tmpRsEdital->CelulaOrcamentaria = $dados['CelulaOrcamentaria'];
        }
        if (isset($dados['Objeto'])) {
            $tmpRsEdital->Objeto = $dados['Objeto'];
        }
        if (isset($dados['Logon'])) {
            $tmpRsEdital->Logon = $dados['Logon'];
        }
        if (isset($dados['qtAvaliador'])) {
            $tmpRsEdital->qtAvaliador = $dados['qtAvaliador'];
        }
        if (isset($dados['stDistribuicao'])) {
            $tmpRsEdital->stDistribuicao = $dados['stDistribuicao'];
        }
        if (isset($dados['stAdmissibilidade'])) {
            $tmpRsEdital->stAdmissibilidade = $dados['stAdmissibilidade'];
        }
        if (isset($dados['cdTipoFundo'])) {
            $tmpRsEdital->cdTipoFundo = $dados['cdTipoFundo'];
        }
        if (isset($dados['idAti'])) {
            $tmpRsEdital->idAti = $dados['idAti'];
        }

        echo "<pre>";


        //SALVANDO O OBJETO CRIADO
        $id = $tmpRsEdital->save();

        if ($id) {
            return $id;
        } else {
            return false;
        }
    }

    public function listaAvaliadores($where=array())
    {
        $slct = $this->select();
        $slct->setIntegrityCheck(false);
        $slct->from(
                array('edi' => $this->_name),
                array('')
        );
        $slct->joinInner(
                array('ava' => 'tbAvaliadorEdital'),
                'ava.idEdital = edi.idEdital',
                array('ava.idAvaliador')
        );
        $slct->joinInner(
            array('age' => 'Agentes'),
                'age.idAgente = ava.idAvaliador',
                array(''),
                'Agentes.dbo'
        );
        $slct->joinInner(
            array('nom' => 'Nomes'),
                'nom.idAgente = age.idAgente',
                array('age.Descricao'),
                'Agentes.dbo'
        );
        foreach ($where as $coluna => $valor) {
            $slct->where($coluna, $valor);
        }

        
        //return $this->fetchAll($slct);
    }


    public function buscaEditalFormDocumento($idusuario, $idEdital=null)
    {
        $slct = $this->select();
        $slct->setIntegrityCheck(false);
        $slct->from(
                array('edi' => $this->_name),
                array(
                    new Zend_Db_Expr('CONVERT(CHAR(10), edi.DtEdital, 103) AS DtEdital'),
                    'edi.Objeto', 'edi.CelulaOrcamentaria', 'edi.idOrgao',
                'edi.cdTipoFundo', 'edi.Logon', 'edi.NrEdital', 'edi.qtAvaliador', 'edi.idAti')
        );
        $slct->joinInner(
                array('tfd' => 'tbFormDocumento'),
                'tfd.idEdital = edi.idEdital',
                array('tfd.nmFormDocumento', 
                'tfd.stModalidadeDocumento','tfd.idClassificaDocumento', 
                'tfd.nrFormDocumento', 'tfd.nrVersaoDocumento'  ),
                'BDCORPORATIVO.scQuiz'
        );
        $slct->joinInner(
                array('exf' => 'tbEditalXtbFaseEdital'),
                'exf.idEdital = edi.idEdital',
                array('*'),
                'BDCORPORATIVO.scSac'
        );
        $slct->joinInner(
                array('ati' => 'atividade'),
                'ati.atiid = edi.idAti',
                array(new Zend_Db_Expr("ati.atianopi + RIGHT('00000' + CONVERT(VARCHAR(5),ati.atiseqpi),5) as pi")),
                'BDSIMEC.pde'
        );
        $slct->where(new Zend_Db_Expr('tfd.idClassificaDocumento not in (23,24,25)'));
        $slct->where('exf.idFaseEdital = 1');
        $slct->where('edi.Logon = ?', $idusuario);
        if (!empty($idEdital)) {
            $slct->where('edi.idEdital = ?', $idEdital);
        }

        return $this->fetchAll($slct);
    }

    public function buscar($where=array(), $order=array(), $tamanho=-1, $inicio=-1)
    {
        $slct = $this->select();

        //adiciona quantos filtros foram enviados
        foreach ($where as $coluna => $valor) {
            $slct->where($coluna, $valor);
        }

        //adicionando linha order ao select
        $slct->order($order);

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


    public static function saldoPi($idAti)
    {
        $valorGasto = null;
        $saldoPi = null;

        $sql = "SELECT
					SUM(pg.vlParcela) as parcelas
				FROM
					BDCORPORATIVO.scSAC.tbPagamento as pg
					INNER JOIN BDCORPORATIVO.scQuiz.tbPerguntaFormDocto as pfd ON pfd.nrPergunta=pg.nrPergunta
					INNER JOIN BDCORPORATIVO.scQuiz.tbFormDocumento as fd ON fd.nrFormDocumento=pfd.nrFormDocumento and fd.nrVersaoDocumento=pfd.nrVersaoDocumento
					INNER JOIN SAC.dbo.Edital as ed ON ed.idEdital= fd.idEdital
					INNER JOIN BDSIMEC.pde.atividade AS ati ON ati.atiid=ed.idAti
					INNER JOIN SAC.dbo.PreProjeto AS pp ON pp.idEdital=ed.idEdital
					INNER JOIN SAC.dbo.Projetos AS p ON p.idProjeto=pp.idPreProjeto
				WHERE
					ed.idAti=$idAti";

        $db= Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);

        $resultado = $db->fetchAll("SET TEXTSIZE 104857600");
        $resultado = $db->fetchAll($sql);

        return $resultado;
    }

    public static function recuperaValorPi($idAti)
    {
        $sql = "SELECT ati.atiorcamento/100 as valor FROM BDSIMEC.pde.atividade ati WHERE ati.atiid = $idAti";

        $db= Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);

        $resultado = $db->fetchAll("SET TEXTSIZE 104857600");
        $resultado = $db->fetchAll($sql);

        return $resultado;
    }

    public static function recuperaIdEdital($nrFormDocumento)
    {
        $conexao = new Conexao;
        $idEdital = null;

        $queryIdEdital = "select idEdital FROM BDCORPORATIVO.scQuiz.tbFormDocumento
        where BDCORPORATIVO.scQuiz.tbFormDocumento.nrFormDocumento = ?";

        $preparedStatement = $conexao->conexao->prepare($queryIdEdital);
        $preparedStatement->bindParam(1, $codFormDocumento);

        $codFormDocumento = $nrFormDocumento;

        if ($preparedStatement->execute()) {
            if ($edital = $preparedStatement->fetch()) {
                $idEdital = $edital[0];
            }
        }

        return $idEdital;
    }

    public static function buscarIdPi($idEdital)
    {
        $sql = "SELECT idAti FROM SAC.dbo.Edital where idEdital = $idEdital";

        $db= Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);

        $resultado = $db->fetchAll("SET TEXTSIZE 104857600");
        $resultado = $db->fetchAll($sql);

        return $resultado;
    }
}
