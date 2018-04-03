<?php

/**
 * Class Arquivo_Model_TbDocumentoMapper
 */
class Arquivo_Model_TbDocumentoMapper extends MinC_Db_Mapper
{
    protected $_idUsuario;

    public function __construct()
    {
        $this->setDbTable('Arquivo_Model_DbTable_TbDocumento');

        $auth = Zend_Auth::getInstance();
        $arrAuth = array_change_key_case((array) $auth->getIdentity());

        $this->_idUsuario = !empty($arrAuth['usu_codigo']) ? $arrAuth['usu_codigo'] : $arrAuth['idusuario'];

    }

    public function save($model)
    {
        return parent::save($model);
    }

    /**
     * @param $arrData
     * @param Zend_File_Transfer $file
     * @param array $formats
     * @param int $maxSizeFile
     * @return int|string
     * @throws Exception
     */
    public function saveCustom($arrData, Zend_File_Transfer $file, $formats = array(), $maxSizeFile = 10485760)
    {

        $idDocumento = 0;

        $files = $file->getFileInfo();

        try {
            if (!$file->isUploaded())
                throw new Exception("Falha ao anexar arquivo! Verifique o documento e tente novamente!");

            $arquivoNome = $files['arquivo']['name']; # nome
            $arquivoTemp = $files['arquivo']['tmp_name']; # nome temporario
            $arquivoTipo = $files['arquivo']['type']; # tipo
            $arquivoTamanho = $files['arquivo']['size']; # tamanho

            if (!empty($arquivoNome) && !empty($arquivoTemp)) {
                $arquivoExtensao = Upload::getExtensao($arquivoNome); # extensao
                $arquivoBinario = Upload::setBinario($arquivoTemp); # binario
                $arquivoHash = Upload::setHash($arquivoTemp); # hash
            }

            if (!empty($formats) && !in_array(strtolower($arquivoExtensao), $formats)) {
                $extensoesPermitidas = implode(", ", $formats);
                throw new Exception("Erro! Extens&otilde;es permitidas {$extensoesPermitidas}!");
            }

            if ($arquivoTamanho > $maxSizeFile) {
                throw new Exception("O arquivo n&atilde;o pode ser maior do que 10MB!");
            }

            $dadosArquivo=[];
            $dadosArquivo['NmArquivo'] = $arquivoNome;
            $dadosArquivo['SgExtensao'] = $arquivoExtensao;
            $dadosArquivo['NrTamanho'] = $arquivoTamanho;
            $dadosArquivo['DtEnvio'] = date('Y-m-d h:i:s');
            $dadosArquivo['StAtivo'] = 'I';
            $dadosArquivo['DsHash'] = $arquivoHash;
            $dadosArquivo['idUsuario'] = $this->_idUsuario;


            $dadosTbArquivoImagem = [
                'biArquivo' => new Zend_Db_Expr("CONVERT(varbinary(MAX), {$arquivoBinario})")
            ];

            $dadosTbDocumento = [
                'idTipoDocumento' => $arrData['idTipoDocumento'],
                'dsDocumento' => $arrData['dsDocumento'],
            ];

            $tbArquivo = new Arquivo_Model_DbTable_TbDocumento();
            $idDocumento = $tbArquivo->inserirDocumento($dadosArquivo, $dadosTbArquivoImagem, $dadosTbDocumento);

            return $idDocumento;
        } catch (Exception $e) {
            throw $e;
        }
    }

}
