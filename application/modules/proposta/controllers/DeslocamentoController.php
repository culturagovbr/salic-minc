<?php

/**
 * Proposta_DeslocamentoController
 *
 * @uses MinC_Controller_Action_Abstract
 * @author  wouerner <wouerner@gmail.com>
 */
class Proposta_DeslocamentoController extends Proposta_GenericController
{

    /**
     * Reescreve o metodo init()
     * @access public
     * @param void
     * @return void
     */
    public function init()
    {
        parent::init();

        $mapperUf = new Agente_Model_UFMapper();
        $uf = $mapperUf->fetchPairs('idUF', 'Sigla');
        $this->view->comboestados = $uf;

        $table = new Agente_Model_DbTable_Pais();
        $this->view->paises = $table->fetchPairs('idPais', 'Descricao');

        if (!empty($this->idPreProjeto)) {
            $this->view->idPreProjeto = $this->idPreProjeto;
            $this->validarEdicaoProposta();
        } else {
            if ($this->idPreProjeto != '0') {
                parent::message("Necess&aacute;rio informar o n&uacute;mero da proposta.", "/proposta/manterpropostaincentivofiscal/index", "ERROR");
            }
        }
    }

    /**
     * indexAction
     *
     * @access public
     * @return void
     *
     * @author Ruy Junior Ferreira Silva <ruyjfs@gmail.com>
     * @since 21/09/2016
     *
     * @todo Refatorar metodo para user metodos padroes do Zend
     */
    public function indexAction()
    {
        if (empty($this->_request->getParam("verifica"))) {
            $this->_helper->layout->disableLayout();
        }

        try {
            $id = $this->_request->getParam("id", null);


            $idPreProjeto = $this->idPreProjeto;

            $deslocamentos = new Proposta_Model_TbDeslocamentoMapper();
            $dados = $deslocamentos->getDbTable()->buscarDeslocamento($idPreProjeto, $id);

            if ($id && !empty($dados)) {
                foreach ($dados as $d) {
                    $idPaisO 		= $d['idpaisorigem'];
                    $idUFO		    = $d['iduforigem'];
                    $idCidadeO 		= $d['idmunicipioorigem'];
                    $idPaisD		= $d['idpaisdestino'];
                    $idUFD		    = $d['idufdestino'];
                    $idCidadeD 		= $d['idmunicipiodestino'];
                    $Qtde  		    = $d['qtde'];
                }

                $mapperMunicipio = new Agente_Model_MunicipiosMapper();
                $this->view->combocidadesO = $mapperMunicipio->fetchPairs('idMunicipioIBGE', 'Descricao', array('idufibge' => $idUFO));
                //$this->view->combocidadesO = Cidade::buscar($idUFO);
                $this->view->combocidadesD = $mapperMunicipio->fetchPairs('idMunicipioIBGE', 'Descricao', array('idufibge' => $idUFD));

                $this->view->idPaisO 	= $idPaisO;
                $this->view->idPaisD 	= $idPaisD;
                $this->view->idUFO 	= $idUFO;
                $this->view->idUFD 	= $idUFD;
                $this->view->idCidadeO 	= $idCidadeO;
                $this->view->idCidadeD 	= $idCidadeD;
                $this->view->Qtde 	= $Qtde;
                $this->view->idDeslocamento = $id;
            }
            $this->view->s = $this->_request->getParam("s");
            $this->view->id = $id;
            $this->view->idPreProjeto	= $idPreProjeto;
            $this->view->deslocamentos = $deslocamentos->getDbTable()->buscarDeslocamento($idPreProjeto, null);
        } catch(Exception $e) {
            echo "Erro ao carregar deslocamento: " . $e->getMessage(); die;
        }
    }

    /**
     *
     * @name salvarAction
     *
     * @author Ruy Junior Ferreira Silva <ruyjfs@gmail.com>
     * @since 21/09/2016
     *
     * @todo trocar futuramente no formulario para ficar igual ao banco e nao precisar fazer o tratamento mais.
     */
    public function salvarAction()
    {
        $post = array_change_key_case($this->getRequest()->getPost());
        $mapper = new Proposta_Model_TbDeslocamentoMapper();

        $post['idprojeto'] = $post['idpreprojeto'];
        $post['idpaisorigem'] = $post['paisorigem'];
        $post['idpaisdestino'] = $post['paisdestino'];
        $post['iduforigem'] = ($post['uf']) ? $post['uf'] : 0;
        $post['idufdestino'] = ($post['ufd']) ? $post['ufd'] : 0;
        $post['idmunicipioorigem'] = ($post['cidade']) ? $post['cidade'] : 0;
        $post['idmunicipiodestino'] = ($post['cidaded'])? $post['cidaded'] : 0;
        $post['qtde'] = $post['quantidade'];
        $post['idusuario'] = $this->idAgente;

        $deslocamentos = $mapper->getDbTable()->buscarDeslocamentosGeral(array(
            "de.idpaisorigem "=>$post["idpaisorigem"],
            "de.idpaisdestino "=> $post["idpaisdestino"],
            "de.idmunicipioorigem "=> $post["idmunicipioorigem"],
            "de.idmunicipiodestino "=> $post["idmunicipiodestino"],
            "de.idprojeto "=> $post['idprojeto'],
            "de.qtde "=>$post['qtde']), array(), array('iddeslocamento' => $post['iddeslocamento']));

        if (!empty($deslocamentos)) {
            parent::message("Trecho j&aacute; cadastrado, transa&ccedil;&atilde;o cancelada!", "/proposta/localderealizacao/index?idPreProjeto=".$this->idPreProjeto, "ALERT");
            die;
        }

//        $mapper->beginTransaction();
        try {
            if (empty($post['iddeslocamento'])) {
                unset($post['iddeslocamento']);
            }

            $intIdSave = $mapper->save(new Proposta_Model_TbDeslocamento($post));
//            $mapper->commit();
            if ($post['iddeslocamento'] == '') {
                parent::message("Cadastro realizado com sucesso!", "/proposta/localderealizacao/index?deslocamento=true&idPreProjeto=".$this->idPreProjeto, "CONFIRM");
            } else {
                parent::message("Altera&ccedil;&atilde;o realizada com sucesso!", "/proposta/localderealizacao/index?deslocamento=true&idPreProjeto=".$this->idPreProjeto, "CONFIRM");
            }
        } catch (Zend_Exception $ex) {
//            $mapper->rollback();
            echo $ex->getMessage();
        }
        parent::message("N&atilde;o foi poss&iacute;vel realizar a opera&ccedil;&atilde;o! <br>", "/proposta/localderealizacao/index?deslocamento=true&idPreProjeto=".$this->idPreProjeto, "ERROR");
    }

    /**
     *
     * @name excluirAction
     *
     * @author Ruy Junior Ferreira Silva <ruyjfs@gmail.com>
     * @since 21/09/2016
     */
    public function excluirAction()
    {
        if ($_GET['id']) {
            try {
                $mapper = new Proposta_Model_TbDeslocamentoMapper();
                $excluir = $mapper->delete($_GET['id']);
                parent::message("Exclus&atilde;o realizada com sucesso!", "/proposta/localderealizacao/index?deslocamento=true&idPreProjeto=".$this->idPreProjeto, "CONFIRM");
            } catch (Zend_Exception $ex) {
                $this->view->message      = $ex->getMessage();
                $this->vies->message_type = "ERROR";
            }
        }
        $this->redirect("localderealizacao\\index");
    }

    public function alterarAction()
    {
        $id = isset($_GET['id']);
        $idPreProjeto = $this->idPreProjeto;
        $this->redirect("localderealizacao\\index?idPreProjeto=".$idPreProjeto."&id=".$id);
    }

    public function consultarcomponenteAction()
    {
        $this->_helper->layout->disableLayout(); // desabilita o layout
        if (!empty($this->idPreProjeto)) {
            $this->view->deslocamentos = DeslocamentoDAO::buscarDeslocamentos($this->idPreProjeto, null);
        } else {
            die;
        }
    }
}
