<?php

/**
 * Description of InconsistenciaBancariaChecaVisao
 *
 * @author Mikhail Cavalcanti <mikhail.leite@xti.com.br>
 */
class InconsistenciaBancariaChecaVisao
{

    /**
     *
     * @param type $cpfCnpj
     * @param type $tipoVisao
     */
    public function incentivadorProponenteIguaisVisaoIncentivador($cpfCnpj)
    {
        $visaoModel = new VisaoModel();
        $visaoModel->adicionaVisao($cpfCnpj, TipoVisaoModel::INCENTIVADOR);
    }
}
