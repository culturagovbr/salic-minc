<?php

/**
 * Description of InconsistenciaBancariaModel
 *
 * @author Jefferson Silva <jefferson.silva@xti.com.br>
 */
class InconsistenciaBancariaModel
{

    /**
     *
     * @param string $cpfCnpj
     * @param int $idInconsistencia
     * @throws Exception
     */
    public function resolverIncentivadorProponenteIguais($cpfCnpj, $idInconsistencia)
    {
        try {
            $tbTmpCaptacao = new tbTmpCaptacao();
            $captacaoTemporaria = $tbTmpCaptacao->fetchRow($tbTmpCaptacao->select()->where('idTmpCaptacao = ?', $idInconsistencia));
            $captacaoTemporaria->nrCpfCnpjIncentivador =  "000" . $cpfCnpj; //@todo verificar com o romulo qual eh essa rotina que necessita de zero a esquerda
            $captacaoTemporaria->save();

            $tbTmpInconsistencia = new tbTmpInconsistenciaCaptacao();
            $inconsistenciaSemVisaoIncentivadorRow = $tbTmpInconsistencia->fetchRow(
                $tbTmpInconsistencia->select()->where('idTmpCaptacao = ?', $idInconsistencia)->where('idTipoInconsistencia = ?', TipoInconsistenciaBancariaModel::SEM_VISAO_INCENTIVADOR)
            );

            if ($inconsistenciaSemVisaoIncentivadorRow) {
                $inconsistenciaBancariaChecaVisao = new InconsistenciaBancariaChecaVisao();
                $inconsistenciaBancariaChecaVisao->incentivadorProponenteIguaisVisaoIncentivador($cpfCnpj);
            }
        } catch (Exception $exception) {
            throw new Exception('Não foi possível resolver a inconsistência de incentivador e proponente iguais', null, $exception);
        }
    }
}
