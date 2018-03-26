<?php

class Proposta_Model_TbPreProjetoMetaMapper extends MinC_Db_Mapper
{
    public function __construct()
    {
        parent::setDbTable('Proposta_Model_DbTable_TbPreProjetoMeta');
    }

    public function save($model)
    {
        return parent::save($model);
    }

    public function serializarObjeto($object, $where)
    {
        $result = $object->findAll($where);

        if (!$result) {
            return false;
        }

        return serialize($result);
    }

    public function unserializarObjeto($object, $idPreProjeto, $metakey = null)
    {
        if (empty($idPreProjeto)) {
            return false;
        }

        # se não passar o metakey, tenta recuperar a tabela do objeto
        if (empty($metakey)) {
            $metakey = str_replace('dbo.', '', $object->getTableName());
        }

        $TbPreProjetoMeta = new Proposta_Model_DbTable_TbPreProjetoMeta();
        $result = $TbPreProjetoMeta->buscarMeta($idPreProjeto, $metakey);

        return unserialize($result);
    }

    public function salvarObjetoSerializado($object, $idPreProjeto, $metakey = null, $where = null)
    {
        if (empty($where)) {
            $where = array('idProjeto' => $idPreProjeto);
        }

        $serializado = $this->serializarObjeto($object, $where);

        # se não passar o metakey, salva o nome da tabela do objeto
        if (empty($metakey)) {
            $metakey = str_replace('dbo.', '', $object->getTableName());
        }

        $TbPreProjetoMeta = new Proposta_Model_DbTable_TbPreProjetoMeta();
        return $TbPreProjetoMeta->salvarMeta($idPreProjeto, $metakey, $serializado);
    }

    public function salvarArraySerializado($array, $idPreProjeto, $metakey)
    {
        if (empty($metakey)) {
            return false;
        }

        $serializado = serialize($array);

        $TbPreProjetoMeta = new Proposta_Model_DbTable_TbPreProjetoMeta();
        return $TbPreProjetoMeta->salvarMeta($idPreProjeto, $metakey, $serializado);
    }

    public function salvarMatrizSerializada($array, $idPreProjeto, $prefix)
    {

        if (empty($array) || empty($idPreProjeto) || empty($prefix)) {
            return false;
        }

        $response = [];
        foreach ($array as $key => $item) {
            $response[$key] = $this->salvarArraySerializado($item, $idPreProjeto, $prefix . '_' . $key);
        }

        return $response;
    }

    public function salvarPropostaCulturalSerializada($idPreProjeto, $prefix = 'alterarprojeto')
    {
        if (empty($idPreProjeto)) {
            return false;
        }

        $response = false;

        $preProjetoMapper = new Proposta_Model_PreProjetoMapper();
        $propostaCompleta = $preProjetoMapper->obterPropostaCulturalCompleta($idPreProjeto);

        if (!empty($propostaCompleta)) {
            $response = $this->salvarMatrizSerializada($propostaCompleta, $idPreProjeto, $prefix);
        }

        return $response;
    }

    public function unserializarPropostaCulturalCompleta($idPreProjeto, $prefix)
    {
        if (empty($idPreProjeto) || empty($prefix)) {
            return false;
        }

        $propostaCultural = [];

        $tbPreProjetoMeta = new Proposta_Model_DbTable_TbPreProjetoMeta();

        $metas = $tbPreProjetoMeta->buscarMetas($idPreProjeto, null, $prefix);

        if ($metas) {
            foreach ($metas as $meta) {
                $key = str_replace($prefix . '_', '', $meta['metaKey']);
                $propostaCultural[$key] = unserialize($meta['metaValue']);
            }
        }

        return $propostaCultural;

    }

    /**
     * @param $object
     * @param $idPreProjeto
     * @param $metakey
     * @param null $whereDelete
     * @return bool
     */
    public function restaurarObjetoSerializadoParaTabela($object, $idPreProjeto, $metakey, $whereDelete = null)
    {
        if (empty($idPreProjeto)) {
            return false;
        }

        if (empty($metakey)) {
            return false;
        }

        # metakey de backup para o objeto atual
        $tableName = str_replace('dbo.', '', $object->getTableName());

        if ($tableName == 'preprojeto' || $tableName == 'tbplanodistribuicao') {
            return false;
        }

        # recupera e verifica se os itens existem
        $itens = $this->unserializarObjeto($object, $idPreProjeto, $metakey);

        # se não tiver itens, não eh pra restaurar
        if (empty($itens) || !is_array($itens)) {
            return false;
        }

        # metakey de backup para o objeto atual
        $metakeybkp = $metakey . "_bkp";

        # salvar objeto atual
        $salvarBkp = $this->salvarObjetoSerializado($object, $idPreProjeto, $metakeybkp);

        # excluir itens atuais
        if ($salvarBkp) {
            if (empty($whereDelete)) {
                $whereDelete = array('idProjeto' => $idPreProjeto);
            }

            $delete = $object->deleteBy($whereDelete);
        }

        #incluir os novos itens
        if ($delete >= 0) {
            foreach ($itens as $item) {
                $PK = $object->getPrimary();
                $PK = $PK[1];

                if ($item[$PK]) {
                    unset($item[$PK]);
                }

                $object->insert($item);
            }

            return true;
        }

        return false;
    }

    /**
     *  Devido ao desenho do banco para a tabela tbdetalhaplanodistribuicao, para restaurar o detalhamento dos produtos,
     *  eu tenho que saber o novo id dos produtos inseridos. Tendo em isso em mente, quando for salvar o Plano de distribuicao
     *  do produto, pega o id dele e salva os detalhamentos referentes a ele.
     *
     * @param $idPreProjeto
     * @return bool
     */
    public function restaurarPlanoDistribuicaoDetalhado($idPreProjeto)
    {
        if (empty($idPreProjeto)) {
            return false;
        }

        $TPD = new PlanoDistribuicao();
        $produtos = $this->unserializarObjeto($TPD, $idPreProjeto, 'alterarprojeto_planodistribuicaoproduto');

        $TPDD = new Proposta_Model_DbTable_TbDetalhamentoPlanoDistribuicaoProduto();
        $detalhamentoProdutos = $this->unserializarObjeto($TPDD, $idPreProjeto, 'alterarprojeto_tbdetalhaplanodistribuicao');

        # se não tiver itens, não eh pra restaurar
        if (empty($produtos) || !is_array($produtos)) {
            return false;
        }

        if (empty($detalhamentoProdutos) || !is_array($detalhamentoProdutos)) {
            return false;
        }

        # metakey de backup para os objetos atuais
        $bkpPDP = "alterarprojeto_planodistribuicaoproduto_bkp";
        $bkpPDPD = "alterarprojeto_tbdetalhaplanodistribuicao_bkp";

        # salvar os objetos atuais
        $salvarPDP = $this->salvarObjetoSerializado($TPD, $idPreProjeto, $bkpPDP);

        $PlanoDetalhado = $TPD->buscarPlanoDistribuicaoDetalhadoByIdProjeto($idPreProjeto);
        $salvarPDPD = $this->salvarArraySerializado($PlanoDetalhado, $idPreProjeto, $bkpPDPD);

        # excluir itens atuais
        if ($salvarPDP && $salvarPDPD) {
            $TPD->delete(array('idProjeto = ?' => $idPreProjeto)); # produto
            $TPDD->excluirByIdPreProjeto($idPreProjeto); # detalhamento
        }

        $novosDetalhamento = [];
        foreach ($produtos as $produto) {
            # Guarda a chave primeira antiga do plano de distribuicao
            $oldIdPlanoDistribuicao = $produto['idPlanoDistribuicao'];

            # Remove a chave primaria antiga
            unset($produto['idPlanoDistribuicao']);

            # Salva como um novo item
            $novoID = $TPD->insert($produto);

            # Varre os detalhamentos do plano de distribuicao anterior e substitui o id pelo atual
            if ($novoID) {
                foreach ($detalhamentoProdutos as $detalhamento) {
                    if ($oldIdPlanoDistribuicao == $detalhamento['idPlanoDistribuicao']) {
                        $detalhamento['idPlanoDistribuicao'] = $novoID;
                        $novosDetalhamento[] = $detalhamento;
                    }
                }
            }
        }
        if ($novosDetalhamento) {
            # Salva o detalhamento dos produtos
            foreach ($novosDetalhamento as $detalhamento) {
                unset($detalhamento['idDetalhaPlanoDistribuicao']);
                $TPDD->insert($detalhamento);
            }
        }
        return true;
    }

    public function verificarSeExisteVersaoDaProposta($idPreProjeto, $prefix)
    {
        if (empty($idPreProjeto) || empty($prefix)) {
            return false;
        }

        $TbPreProjetoMeta = new Proposta_Model_DbTable_TbPreProjetoMeta();
        $response = $TbPreProjetoMeta->buscarMeta($idPreProjeto, $prefix . '_identificacaoproposta');

        if (empty($response)) {
            return false;
        }

        return true;
    }
}