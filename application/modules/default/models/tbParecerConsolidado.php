<?php
/**
 * DAO tbParecerConsolidado
 * @since 16/03/2011
 * @link http://www.cultura.gov.br
 */
class tbParecerConsolidado extends MinC_Db_Table_Abstract
{
    protected $_banco  = "SAC";
    protected $_schema = "SAC";
    protected $_name   = "tbParecerConsolidado";

    public function salvar($dados)
    {
        //INSTANCIANDO UM OBJETO DE ACESSO AOS DADOS DA TABELA
        $tmpTblParecerConsolidado = new ParecerConsolidado();

        //DECIDINDO SE SERA FEITA UM INSERT OU UPDATE
        if (isset($dados['idVinculo'])) {
            $tmpTblParecerConsolidado = $tmpTblParecerConsolidado->find($dados['idVinculo'])->current();
        } else {
            $tmpTblParecerConsolidado = $tmpTblParecerConsolidado->createRow();
        }
        //ATRIBUINDO VALORES AOS CAMPOS QUE FORAM PASSADOS
        if (isset($dados['dsParecer'])) {
            $tmpTblParecerConsolidado->dsParecer = $dados['dsParecer'];
        }
        if (isset($dados['idUsuario'])) {
            $tmpTblParecerConsolidado->idUsuario = $dados['idUsuario'];
        }
        if (isset($dados['idDocumento'])) {
            $tmpTblParecerConsolidado->idDocumento = $dados['idDocumento'];
        }
        if (isset($dados['idRelatorioConsolidado'])) {
            $tmpTblParecerConsolidado->idRelatorioConsolidado = $dados['idRelatorioConsolidado'];
        }
        if (isset($dados['idRelatorioFinal'])) {
            $tmpTblParecerConsolidado->idRelatorioFinal = $dados['idRelatorioFinal'];
        }
        if (isset($dados['idPerfilAvaliador'])) {
            $tmpTblParecerConsolidado->idPerfilAvaliador = $dados['idPerfilAvaliador'];
        }
        if (isset($dados['idAvaliador'])) {
            $tmpTblParecerConsolidado->idAvaliador = $dados['idAvaliador'];
        }

        //echo "<pre>";
        //print_r($tmpRsVinculo);
        //SALVANDO O OBJETO CRIADO
        $id = $tmpRsVinculo->save();

        if ($id) {
            return $id;
        } else {
            return false;
        }
    }

    public function buscarTudo($idRelatorioConsolidado)
    {
        $slct = $this->select();
        $slct->setIntegrityCheck(false);
        $slct->from(
                array('D' => $this->_name),
                array(
                    'idParecerConsolidado',
                    'CAST(D.dsParecer AS TEXT) AS dsParecer',
                    'idUsuario',
                    'idDocumento',
                    'idRelatorioConsolidado',
                    'stRelatorioFinal',
                    'idAvaliador',
                    'idPerfilAvaliador'
                    )
        );
        $slct->joinLeft(
                array('Doc' => 'tbDocumento'),
                'Doc.idDocumento = D.idDocumento',
                array("*"),
                'BDCORPORATIVO.scCorp'
        );

        $slct->joinLeft(
                array('Arq' => 'tbArquivo'),
                'Arq.idArquivo = Doc.idArquivo',
                array("*"),
                'BDCORPORATIVO.scCorp'
        );
        $slct->where('D.idRelatorioConsolidado = ?', $idRelatorioConsolidado);

        return $this->fetchAll($slct);
    }
    public function buscarAtoresCoordenadorAvaliacao($idPronac, $idusuario=null)
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(
                        array('pc'=>$this->_name),
                        array('Perfil3'=>new Zend_Db_Expr("'Coordenadador de Acompanhamento'"))
                      );

        $select->joinInner(
                            array('gr'=>'Grupos'),
                            'gr.gru_codigo = pc.idPerfilAvaliador',
                            array('Perfil'=>'gr.gru_nome','cdPerfil'=>'gr.gru_codigo'),
                            'TABELAS.dbo'
                           );
        $select->joinInner(
                            array('rc'=>'tbRelatorioConsolidado'),
                            'rc.idRelatorioConsolidado = pc.idRelatorioConsolidado',
                            array(),
                            'SAC.dbo'
                           );
        $select->joinInner(
                            array('rel'=>'tbRelatorio'),
                            'rel.idRelatorio = rc.idRelatorio',
                            array(),
                            'SAC.dbo'
                           );
        $select->joinInner(
                            array('usu'=>'Usuarios'),
                            'pc.idAvaliador = usu.usu_codigo',
                            array(),
                            'TABELAS.dbo'
                           );
        $select->joinInner(
                            array('ag'=>'Agentes'),
                            'ag.CNPJCPF = usu.usu_identificacao',
                            array('ag.idAgente'),
                            'AGENTES.dbo'
                           );
        $select->joinInner(
                            array('nm'=>'Nomes'),
                            'nm.idAgente = ag.idAgente',
                            array('Nome'=>'nm.Descricao'),
                            'AGENTES.dbo'
                           );
        $select->joinInner(
                            array('uog'=>'UsuariosXOrgaosXGrupos'),
                            'uog.uog_usuario = usu.usu_codigo and uog.uog_grupo = gr.gru_codigo',
                            array(),
                            'TABELAS.dbo'
                           );
        $select->joinInner(
                            array('org'=>'Orgaos'),
                            'org.Codigo = uog.uog_orgao',
                            array('Orgao'=>'org.Sigla'),
                            'SAC.dbo'
                           );

        $select->where('rel.idPRONAC = ?', $idPronac);
        //$select->where('usu.usu_codigo <> ?', $idusuario);
        return $this->fetchAll($select);
    }
}
