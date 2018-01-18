<?php
/**
 * @author Caio Lucena <caioflucena@gmail.com>
 */
class QuestaoController extends MinC_Controller_Action_Abstract
{
    /**
     * (non-PHPdoc)
     * @see GenericControllerNew::init()
     */
    public function init()
    {
        $this->_helper->getHelper('contextSwitch')
            ->addActionContext('cadastrar', 'json')
            ->addActionContext('atualizar', 'json')
            ->addActionContext('deletar', 'json')
            ->addActionContext('pesquisar', 'json')
            ->initContext()
        ;
    }

    /**
     * (non-PHPdoc)
     * @see GenericControllerNew::postDispatch()
     */
    public function postDispatch()
    {
    }

    /**
     * @todo refatorar levando o cadastro de respostas para model resposta (com loop)
     */
    public function cadastrarAction()
    {
        try {
            $questao = new QuestaoModel(
                null,
                $this->getRequest()->getParam('guia'),
                $this->getRequest()->getParam('textoQuestao'),
                $this->getRequest()->getParam('textoAjudaQuestao'),
                $this->getRequest()->getParam('tipoResposta')
            );

            $questao->cadastrar();
            $this->view->questao = $questao->toStdClass();

            // Se for postada a(s) resposta efetua o cadastro
            $this->view->questao->resposta = array();
            $respostas = $this->getRequest()->getParam('respostaNome') ?: array();
            foreach ($respostas as $resposta) {
                $respostaModel = new RespostaModel(
                    null,
                    $this->getRequest()->getParam('tipoResposta'),
                    $this->view->questao->questao,
                    $resposta
                );
                $respostaModel->cadastrar();
                $this->view->questao->resposta[] = $respostaModel->toStdClass();
            }
        } catch (Exception $e) {
            echo '<pre>';
            print_r($e);
            die;
            $this->view->error = $e;
        }
    }

    /**
     *
     */
    public function atualizarAction()
    {
        $questao = new QuestaoModel(
            $this->getRequest()->getParam('questao'),
            $this->getRequest()->getParam('guia'),
            $this->getRequest()->getParam('textoQuestao'),
            $this->getRequest()->getParam('textoAjudaQuestao'),
            $this->getRequest()->getParam('tipoResposta')
        );
        
        $questao->atualizar();
        $this->view->questao = $questao->toStdClass();

        // Remove as respostas disponiveis para a questao
        $respostaModel = new RespostaModel(null, null, $this->view->questao->questao);
        $respostaModel->deletarPorQuestao();
        // Se for postada a(s) resposta efetua o cadastro
        $this->view->questao->resposta = array();
        
        $respostas = $this->getRequest()->getParam('respostaNome') ?: array();
        foreach ($respostas as $resposta) {
            $respostaModel = new RespostaModel(
                null,
                $this->getRequest()->getParam('tipoResposta'),
                $this->view->questao->questao,
                $resposta
            );
            $respostaModel->cadastrar();
            $this->view->questao->resposta[] = $respostaModel->toStdClass();
        }
    }

    /**
     * @return void
     */
    public function deletarAction()
    {
        $questao = new QuestaoModel($this->getRequest()->getParam('questao'));
        $this->view->questao = $questao->deletar();
    }

    /**
     *
     */
    public function pesquisarAction()
    {
        $questaoModel = new QuestaoModel();
        $this->view->questao = $questaoModel->pesquisar($this->getRequest()->getParam('questao'));

        if (!empty($this->view->questao)) {
            $respostaModel = new RespostaModel();
            $this->view->questao[0]['respostas'] = $respostaModel->pesquisarPorTipoQuestao(null, $this->view->questao[0]['idQuestao']);
        }
    }
}
