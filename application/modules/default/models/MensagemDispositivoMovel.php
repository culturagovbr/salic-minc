<?php

/**
 * Abstração da tabela SAC.dbo.tbMensagemDispositivoMovel
 *
 * @author rafael.gloria@cultura.gov.br
 */
class MensagemDispositivoMovel extends GenericModel
{
    protected $_name = 'tbMensagemDispositivoMovel';
    protected $_schema = 'SAC';
    protected $_banco = 'SAC';

    /**
     * Lista todos os dispositivos por idPronac.
     *
     * @param integer $idPronac
     * @return array
     */
//    public function listarPorIdPronac($idPronac){
//        $consulta = $this->select();
//        $consulta->setIntegrityCheck(false);
//        $consulta
//            ->from(array('projetos' => 'vwAgentesSeusProjetos'), array(), 'SAC.dbo')
//            ->join(array('usuario' => 'SGCacesso'), 'projetos.IdUsuario = usuario.IdUsuario', array(
//                'cpf' => 'Cpf'), 'ControleDeAcesso.dbo')
//            ->join(array('dispositivo' => 'tbDispositivoMovel'), 'usuario.Cpf = dispositivo.nrCPF', array(
//                'idRegistration'), 'SAC.dbo')
//            ->group(array(
//                'cpf',
//                'idRegistration'))
//        ;
//
//        return $this->fetchAll($consulta);
//    }
}
