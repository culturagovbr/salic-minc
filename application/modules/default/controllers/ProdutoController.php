<?php
class PlanoDistribuicaoController extends MinC_Controller_Action_Abstract
{
    /**
     * Reescreve o método init()
     * @access public
     * @param void
     * @return void
     */
    public function init()
    {
        // verifica as permissões
        $PermissoesGrupo = array();
        $PermissoesGrupo[] = 93;  // Coordenador de Parecerista
            $PermissoesGrupo[] = 94;  // Parecerista
            $PermissoesGrupo[] = 121; // Técnico
            $PermissoesGrupo[] = 122; // Coordenador de Acompanhamento
            parent::perfil(1, $PermissoesGrupo);
        parent::init();

        //print_r(get_include_path());
            //Zend_Loader::loadClass("PlanoDistribuicao");
    }

    public function indexAction()
    {
        $tblPlanoDistribuicao = new PlanoDistribuicao();
        $rsPlanoDistribuicao = $tblPlanoDistribuicao->busca(array("Segmento=?"=>61), array("idPlanoDistribuicao DESC"), 10);
        $arrDados = array(
                            "planosDistribuicao"=>$rsPlanoDistribuicao,
                            "formulario"=>$this->_urlPadrao."/plano-distribuicao/frm-plano-distribuicao"
                        );

        $this->montaTela("planodistribuicao/index.phtml", $arrDados);
    }
}
