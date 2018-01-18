<?php
class UploadDAO extends MinC_Db_Table_Abstract
{
    protected $_schema = "SAC";

    /**
     * M�todo para abrir o arquivo
     * @access public
     * @static
     * @param integer $id
     * @return object || bool
     */
    public static function abrir($id)
    {
        $table = Zend_Db_Table::getDefaultAdapter();
        $db = Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);

        $select = $table->select()
            ->from(
                'tbArquivo',
                array('dsTipoPadronizado', 'nmArquivo'),
                'BDCORPORATIVO.scCorp'
            )
            ->where('tbArquivo.idArquivo = ?', $id)
            ->joinInner(
                'tbArquivoImagem',
                'tbArquivo.idArquivo = tbArquivoImagem.idArquivo',
                array('biArquivo'),
                'BDCORPORATIVO.scCorp'
            );

        $resultado = $db->fetchAll('SET TEXTSIZE 2147483647');
        return $db->fetchAll($select);
    }

    /**
     * M�todo para abrir o arquivo
     * @access public
     * @static
     * @param integer $id
     * @return object || bool
     */
    public static function abrirdocumentosanexados($id, $busca)
    {
        $db = Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);

        if ($busca == "tbDocumentosAgentes") { //acrescentado-jass
            // busca o arquivo

            $select = $db->select()
                ->from(
                    'tbDocumentosAgentes',
                    array('NoArquivo AS nmArquivo','imDocumento AS biArquivo',new Zend_db_Expr('1 AS biArquivo2')),
                    'SAC.dbo'
                )
                ->where('idDocumentosAgentes = ?', $id)
                ->joinInner(
                    'DocumentosExigidos',
                    'tbDocumentosAgentes.CodigoDocumento = DocumentosExigidos.Codigo',
                    array(''),
                    'SAC.dbo'
                );
        } elseif ($busca == "tbDocumentosPreProjeto") { //acrescentado-jass
            // busca o arquivo

            $select = $db->select()
                ->from(
                    'tbDocumentosPreprojeto',
                    array('NoArquivo AS nmArquivo','imDocumento AS biArquivo',new Zend_db_Expr('1 AS biArquivo2')),
                    'SAC.dbo'
                )
                ->where('idDocumentosPreprojetos = ?', $id)
                ->joinInner(
                    'DocumentosExigidos',
                    'tbDocumentosPreprojeto.CodigoDocumento = DocumentosExigidos.Codigo',
                    array(''),
                    'SAC.dbo'
                );
        } elseif ($busca == "tbDocumento") { //acrescentado-jass
            // busca o arquivo

            $select = $db->select()
                ->from(
                    'tbDocumento',
                    array('NoArquivo AS nmArquivo', 'imDocumento AS biArquivo', 'biDocumento AS biArquivo2'),
                        'SAC.dbo'
                )
                ->where('tbDocumento.idDocumento = ?', $id)
                ->joinInner(
                    'tbTipoDocumento',
                    'tbDocumento.idTipoDocumento = tbTipoDocumento.idTipoDocumento',
                     array(''),
                    'SAC.dbo'
                );
        }

        $resultado = $db->fetchAll("SET TEXTSIZE 104857600");
        $resultado = $db->fetchAll($select);
        return $resultado;
    }
}
