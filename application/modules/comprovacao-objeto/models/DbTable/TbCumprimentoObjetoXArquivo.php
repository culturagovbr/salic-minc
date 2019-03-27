<?php

class ComprovacaoObjeto_Model_DbTable_TbCumprimentoObjetoXArquivo extends MinC_Db_Table_Abstract
{
    const ACESSIBILIDADE_FISICA = 0;
    const FRUICAO_DE_DEMOCRATIZACAO_AO_ACESSO_PUBLICO = 1;
    const IMPACTOS_AMBIENTAIS = 2;

    protected $_banco = "SAC";
    protected $_schema = "SAC";
    protected $_name = "tbCumprimentoObjetoXArquivo";

    private $idCumprimentoObjetoXArquivo;
    private $idCumprimentoObjeto;
    private $arquivo;
    private $posicao;

    /**
     *
     * @param int $idCumprimentoObjetoXArquivo
     * @param int $idCumprimentoObjeto
     * @param int $arquivo
     * @param int $posicao
     */
    public function __construct(
            $idCumprimentoObjetoXArquivo = null,
            $idCumprimentoObjeto = null,
            $arquivo = null,
            $posicao = null
            ) {
        parent::__construct();
        $this->idCumprimentoObjetoXArquivo = $idCumprimentoObjetoXArquivo;
        $this->idCumprimentoObjeto = $idCumprimentoObjeto;
        $this->arquivo = $arquivo;
        $this->posicao = $posicao;
    }

    public function getIdCumprimentoObjetoXArquivo()
    {
        return $this->idCumprimentoObjetoXArquivo;
    }

    public function getIdCumprimentoObjeto()
    {
        return $this->idCumprimentoObjeto;
    }

    public function getArquivo()
    {
        return $this->arquivo;
    }

    public function getPosicao()
    {
        return $this->posicao;
    }

    public function setIdCumprimentoObjetoXArquivo($idCumprimentoObjetoXArquivo)
    {
        $this->idCumprimentoObjetoXArquivo = $idCumprimentoObjetoXArquivo;
        return $this;
    }

    public function setIdCumprimentoObjeto($idCumprimentoObjeto)
    {
        $this->idCumprimentoObjeto = $idCumprimentoObjeto;
        return $this;
    }

    public function setArquivo($arquivo)
    {
        $this->arquivo = $arquivo;
        return $this;
    }

    public function setPosicao($idPosicao)
    {
        $this->posicao = $idPosicao;
        return $this;
    }

    /**
     * Salva imagens que foram feitas upload no caso de uso de cumprimento
     * do objeto
     * @param int $idCumprimentoDoObjeto
     */
    public function save($idCumprimentoDoObjeto)
    {
        $arquivoModel = new ArquivoModel();
        $cumprimentoObjetoArquivo = $this;
        array_walk($_FILES, function ($file, $filename) use ($idCumprimentoDoObjeto, $arquivoModel, $cumprimentoObjetoArquivo) {
            // Se n�o houve nenhum erro pode persistir o arquivo no banco
            if (UPLOAD_ERR_OK === $file['error']) {
                $arquivoModel->cadastrar($filename);
                $cumprimentoObjetoArquivoRow = $cumprimentoObjetoArquivo->createRow();
                $cumprimentoObjetoArquivoRow->idCumprimentoObjeto = $idCumprimentoDoObjeto;
                $cumprimentoObjetoArquivoRow->idArquivo = $arquivoModel->getId();
                $cumprimentoObjetoArquivoRow->idPosicao = $cumprimentoObjetoArquivo->getTipoDeArquivo($filename);
                $cumprimentoObjetoArquivoRow->save();
            }
        });
    }

    /**
     *
     * @param string $filename nome do arquivo que est� sendo passado no atributo
     * 'name' do formulario que est� postando os arquivos
     * @return int O numero inteiro que representa a natureza a qual se destina
     * a imagem que est� sendo feito o upload do arquivo no formul�rio
     * @throws InvalidArgumentException
     */
    public function getTipoDeArquivo($filename)
    {
        switch ($filename) {
            case 'imagensMedidasAcessibilidadeFisica':
                return self::ACESSIBILIDADE_FISICA;
            case 'imagensMedidasAcessibilidadePublica':
                return self::FRUICAO_DE_DEMOCRATIZACAO_AO_ACESSO_PUBLICO;
            case 'imagensImpactosAmbientais':
                return self::IMPACTOS_AMBIENTAIS;
            default:
                throw new InvalidArgumentException("Tipo do arquivo do cumprimento do objeto ('{$filename}') n�o existe");
        }
    }

    public function buscar($where=array(), $order=array(), $tamanho=-1, $inicio=-1)
    {
        $select = $this->select();
        if (null !== $this->getIdCumprimentoObjeto()) {
            $select->where('idCumprimentoObjeto = ?', $this->getIdCumprimentoObjeto());
        }
        if (null !== $this->getPosicao()) {
            $select->where('idPosicao = ?', $this->getPosicao());
        }
        if (0 === count($select->getPart(Zend_Db_Select::WHERE))) {
            throw new BadMethodCallException('N�o foi usado nenhum filtro');
        }

        $imagensDoObjeto = new ArrayObject();
        $arquivoModel = new ArquivoModel();
        $this->getAdapter()->getProfiler()->setEnabled(true);

        foreach ($this->fetchAll($select) as $ImagemDoObjetoRow) {
            $arquivoModel->setId($ImagemDoObjetoRow->idArquivo);
            $imagemDoObjeto = new self();
            $imagemDoObjeto
                    ->setIdCumprimentoObjetoXArquivo($ImagemDoObjetoRow->idCumprimentoObjetoXArquivo)
                    ->setIdCumprimentoObjeto($ImagemDoObjetoRow->idCumprimentoObjeto)
                    ->setArquivo($arquivoModel->buscar())
                    ->setPosicao($ImagemDoObjetoRow->idPosicao);
            $imagensDoObjeto->append($imagemDoObjeto);
        }
        return $imagensDoObjeto;
    }

    public function apagarArquivo()
    {
        if (null === $this->getIdCumprimentoObjeto()) {
            throw new BadMethodCallException('Cumprimento do objeto n�o encontrado');
        }
        if (null === $this->getArquivo()) {
            throw new BadMethodCallException('Arquivo n�o encontrado');
        }
        parent::apagar(array(
            'idCumprimentoObjeto = ?' => $this->getIdCumprimentoObjeto(),
            'idArquivo = ?' => $this->getArquivo(),
        ));
    }
}
