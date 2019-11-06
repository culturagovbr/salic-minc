<?php

class Mensagem extends MinC_Db_Table_Abstract
{
    protected $_primary = '';
    protected $_name = 'tbMensagem';
    protected $_schema = 'SAC';

    /**
     * Salva a informaç&atilde;o de para quais dispositivos foram enviadas as mensagens no banco de dados.
     *
     * @param Zend_Db_Table_Row_Abstract $messageRow
     */
    public function saveListDevice(Zend_Db_Table_Row_Abstract $messageRow, $listDeviceId = array())
    {
        if ($listDeviceId) {
            $modelMensagemDispositivo = new MensagemDispositivoMovel();
            foreach ($listDeviceId as $deviceId) {
                $messageDeviceRow = $modelMensagemDispositivo->createRow();
                $messageDeviceRow->idMensagem = $messageRow->idMensagem;
                $messageDeviceRow->idDispositivoMovel = $deviceId;
                $messageDeviceRow->save();
            }
        }
    }

    /**
     * Monta todos os filtros padr&otilde;es das consultas e tamb&eacute;m montar os filtros din&acirc;micos.
     *
     * @param Zend_Db_Table_Select $consulta
     * @param stdClass $objParam
     * @return Zend_Db_Table_Select
     */
    public function montarFiltrosListarDeDispositivo($consulta, stdClass $objParam)
    {
        $consulta
            ->where(new Zend_Db_Expr('m.dtExclusao IS NULL'))
            ->where(new Zend_Db_Expr('md.dtExclusao IS NULL'))
            ->where('d.idRegistration = ?', $objParam->idRegistration? $objParam->idRegistration: '');

        if ($objParam->new) {
            $consulta->where(new Zend_Db_Expr('m.dtAcesso IS NULL'));
        }

        return $consulta;
    }

    public function buscarTotalListarDeDispositivo(stdClass $objParam)
    {
        $total = 0;
        $consulta = $this->select();
        $consulta->setIntegrityCheck(false);
        $consulta
            ->from(array('m' => 'tbMensagem'), array('total' => new Zend_Db_Expr('COUNT(DISTINCT m.idMensagem)')), 'SAC.dbo')
            ->join(array('md' => 'tbMensagemDispositivoMovel'), 'm.idMensagem = md.idMensagem', array(), 'SAC.dbo')
            ->join(array('d' => 'tbDispositivoMovel'), 'md.idDispositivoMovel = d.idDispositivoMovel', array(), 'SAC.dbo');
        $this->montarFiltrosListarDeDispositivo($consulta, $objParam);

        $rs = $this->fetchRow($consulta);
        if ($rs) {
            $total = (int)$rs->total;
        }

        return $total;
    }

    /**
     * Lista mensagens por codigo de registro do dispositivo.
     *
     * @param stdClass $objParam
     * @return Zend_Db_Table_Rowset_Abstract
     */
    public function listarDeDispositivo(stdClass $objParam)
    {
        $consulta = $this->select();
        $consulta->setIntegrityCheck(false);
        $consulta
            ->from(array('m' => 'tbMensagem'), array(
                'idMensagem',
                'tpMensagem',
                'nrCPF',
                'idPronac',
                'idMensagem as idDiligencia', // nao existe idDiligencia
                'titulo',
                'descricao',
                'dtEnvio',
                'dtAcesso'), 'SAC.dbo')
            ->join(array('md' => 'tbMensagemDispositivoMovel'), 'm.idMensagem = md.idMensagem', array(), 'SAC.dbo')
            ->join(array('d' => 'tbDispositivoMovel'), 'md.idDispositivoMovel = d.idDispositivoMovel', array(), 'SAC.dbo')
            ->group(array(
                'm.idMensagem',
                'tpMensagem',
                'm.nrCPF',
                'm.idPronac',
                'm.idDiligencia',
                'm.titulo',
                'm.descricao',
                'm.dtEnvio',
                'm.dtAcesso'))
            ->order(array(
                'dtAcesso ASC',
                'dtEnvio DESC'));

        $consulta = $this->montarFiltrosListarDeDispositivo($consulta, $objParam);

        if ($objParam->next) {
            $consulta->limit($objParam->next, (int)$objParam->offset);
        }
        return $this->fetchAll($consulta);
    }
}
