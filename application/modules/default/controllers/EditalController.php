<?php

class EditalController extends MinC_Controller_Action_Abstract
{
    public function init()
    {
        $auth = Zend_Auth::getInstance(); // instancia da autenticacao;
        $GrupoAtivo = new Zend_Session_Namespace('GrupoAtivo'); // cria a sessao com o grupo ativo
        $codGrupo = $GrupoAtivo->codGrupo; //  Grupo ativo na sessao
        $codOrgao = $GrupoAtivo->codOrgao; //  Orgao ativo na sessao
        $this->view->codOrgao = $codOrgao;
        //Da permissao de acesso a todos os grupos do usuario logado afim de atender o UC72
        if (isset($auth->getIdentity()->usu_codigo)) {
            //Recupera todos os grupos do Usuario
            $Usuario = new Autenticacao_Model_Usuario(); // objeto usuário
            $grupos = $Usuario->buscarUnidades($auth->getIdentity()->usu_codigo, 21);
            foreach ($grupos as $grupo) {
                $PermissoesGrupo[] = $grupo->gru_codigo;
            }
            $this->idusuario = $auth->getIdentity()->usu_codigo;
            $this->view->idUsuarioLogado = $this->idusuario;
            isset($auth->getIdentity()->usu_codigo) ? parent::perfil(1, $PermissoesGrupo) : parent::perfil(4, $PermissoesGrupo);
        } else {
            @$this->idusuario = $auth->getIdentity()->IdUsuario;
        }

        // verifica as permissoes
        $PermissoesGrupo = array();
        $PermissoesGrupo[] = 114;  // Coordenador de Editais
        $PermissoesGrupo[] = 97;  // Gestor salic
        $PermissoesGrupo[] = 1111; //Proponente
        //parent::perfil(1, $PermissoesGrupo);

        parent::init();
        // chama o init() do pai GenericControllerNew
    }

    public function indexAction()
    {
        $TipoEdital = new TipoEdital();
        $tipoedital = $TipoEdital->buscarTipoEdital();
        $this->view->tipoedital = $tipoedital;

        $Fluxo = new Fluxo();
        $fluxo = $Fluxo->buscarFluxo();
        $this->view->fluxo = $fluxo;
        
        $ItemFluxo = new ItemFluxo();
        $tipoitemfluxo = $ItemFluxo->buscarItensFluxo();
        $this->view->tipoitemfluxo = $tipoitemfluxo;

        $composicao = new Composicao();
        $composicaoEdital = $composicao->buscarComposicao(array('idComposicao NOT IN (?)' => array(2,3,4,6,8,10)));
        $this->view->composicao = $composicaoEdital;
    }
    
    public function montagemEditalAction()
    {
        $idEdital = $this->_request->getParam('idEdital');
        $this->view->idEdital = $idEdital;
        
        $modelTextoEdital = new tbTextoEdital();
        $editalMontado = $modelTextoEdital->buscarTextoEdital($idEdital);
        $this->view->editalmontado = $editalMontado;

        $modelCriterioAvaliacao = new tbCriteriosAvaliacao();
        $criterioAvaliacao  =   $modelCriterioAvaliacao->buscarcriterioporidEdital($idEdital);
        $this->view->criterioavaliacao = $criterioAvaliacao;
    }
    
    /**
     * Método atualizaListaTextos()
     * atualiza a lista de textos
     * @access public
     * @param void
     * @return list
     */
    public function atualizaListaTextosAction()
    {
        $this->_helper->layout->disableLayout();        // Desabilita o Zend Layout
        $this->_helper->viewRenderer->setNoRender();
                
        $modelTextoEdital = new tbTextoEdital();
        
        try {
            $stringTextoId   = str_replace("&", "", $this->_request->getParam('textoId'));
            $arrayTextoId     = array_slice(explode("textoId[]=", $stringTextoId), 1);
            $count = 1;
            foreach ($arrayTextoId as $textoId) {
                $where = array('idTextoEdital = ?' => $textoId);

                $modelTextoEdital->update(array('nrTexto' => $count), $where);

                $count++;
            }
        } catch (Exception $ex) {
//            echo $ex->getMessage();
            echo 'Erro ao atualizar a lista.';
        }
        
        
        echo 'Lista atualizada com sucesso!';
    }
    
    public function adicionarTextoAction()
    {
        $idEdital = $this->_request->getParam('idEdital');
        $this->view->idEdital = $idEdital;
        
        $modelTextoEdital = new tbTextoEdital();
        $editalMontado = $modelTextoEdital->buscarTextoEdital($idEdital);
        $this->view->editalmontado = $editalMontado;
        
        $ordem = 0;
        $nrOrdem = $modelTextoEdital->buscarUltimaOrdem($idEdital);
        if (count($nrOrdem) > 0) {
            $this->view->ultimaOrdem = ($nrOrdem[0]['nrTexto']) + 1;
        }
        
        $this->view->ultimaOrdem = 1;
        
        $modelCriterioAvaliacao = new tbCriteriosAvaliacao();
        $criterioAvaliacao  =   $modelCriterioAvaliacao->buscarcriterioporidEdital($idEdital);
        $this->view->criterioavaliacao = $criterioAvaliacao;
    }
    
    public function editarTextoEditalAction()
    {
        $idEdital       = $this->_request->getParam('idEdital');
        $idTextoEdital  = $this->_request->getParam('idTextoEdital');
        
        $this->view->idEdital = $idEdital;
        
        $modelTextoEdital = new tbTextoEdital();
        $textoEdital = $modelTextoEdital->buscar(array('idEdital = ?' => $idEdital, 'idTextoEdital = ?' => $idTextoEdital))->toArray();
        $this->view->textoEdital = $textoEdital[0];
        
        
//        $modelCriterioAvaliacao = new tbCriteriosAvaliacao();
//        $criterioAvaliacao  =   $modelCriterioAvaliacao->buscarcriterioporidEdital($idEdital);
//        $this->view->criterioavaliacao = $criterioAvaliacao;
    }
    
    public function adicionarReferenciaAction()
    {
        $idEdital = $this->_request->getParam('idEdital');
        $this->view->idEdital = $idEdital;
        
        $modelTextoEdital = new tbTextoEdital();
        $editalMontado = $modelTextoEdital->buscarTextoEdital($idEdital);
        $this->view->editalmontado = $editalMontado;
        
        $modelCriterioAvaliacao = new tbCriteriosAvaliacao();
        $criterioAvaliacao  =   $modelCriterioAvaliacao->buscarcriterioporidEdital($idEdital);
        $this->view->criterioavaliacao = $criterioAvaliacao;
    }
    
    public function salvarTextoAction()
    {
        $idEdital           = $this->_request->getParam('idEdital');
        $nrTexto            = $this->_request->getParam('nrTexto');
        $dsTexto            = $this->_request->getParam('txtConteudo');
        $nrReferencia       = $this->_request->getParam('nrReferencia');
        $nrCodReferencia    = $this->_request->getParam('nrCodReferencia');
        
        if ($nrReferencia == '') {
            $dadosTextosEdital = array(
                    'idEdital'          => $idEdital,
                    'nrTexto'           => $nrTexto,
                    'dsTexto'           => $dsTexto,
                    'nrReferencia'      => $nrReferencia,
            );
        } else {
            if ($nrCodReferencia == 1) {
                $dsTexto = "Criterio de Avaliação";
            } else {
                $dsTexto = "Forma de Pagamento";
            }
            
            $dadosTextosEdital = array(

                    'idEdital'          => $idEdital,
                    'nrTexto'           => $nrReferencia,
                    'dsTexto'           => $dsTexto,
            );
        }
        
        $modelEditalFinal   = new EditalFinal();

        $db = Zend_Db_Table::getDefaultAdapter();

        try {
            $db->beginTransaction();

            $modelEditalFinal->salvarTextoEdital($dadosTextosEdital);
            
            $db->commit();
            parent::message('Texto do Edital cadastrado com sucesso!', 'edital/montagem-edital/idEdital/'. $idEdital, 'CONFIRM');
        } catch (Exception $exc) {
            $db->rollBack();
            xd($exc->getMessage());
            parent::message('Erro ao cadastrar o texto do Edital', 'edital/adicionar-texto/idEdital/' . $idEdital, 'ERROR');
        }
    }
    
    public function salvarTextoEditadoAction()
    {
        $idEdital           = $this->_request->getParam('idEdital');
        $idTextoEdital      = $this->_request->getParam('idTextoEdital');
        $dsTexto            = $this->_request->getParam('txtConteudo');
        
        $dadosTextosEdital = array(
            'dsTexto'           => $dsTexto
        );
        
        $modelEditalFinal   = new EditalFinal();

        $db = Zend_Db_Table::getDefaultAdapter();

        
        try {
            $db->beginTransaction();
            $modelEditalFinal->update($dadosTextosEdital, array('idTextoEdital = ?' => $idTextoEdital));
            
            $db->commit();
            parent::message('Texto do Edital editado com sucesso!', 'edital/montagem-edital/idEdital/'. $idEdital, 'CONFIRM');
        } catch (Exception $exc) {
            $db->rollBack();
            xd($exc->getMessage());
            parent::message('Erro ao editar o texto do Edital', 'edital/editar-texto-edital/idTextoEdital/'.$idTextoEdital.'/idEdital/' . $idEdital, 'ERROR');
        }
    }
    
    public function deletarTextoEditalAction()
    {
        $idTextoEdital  = $this->_request->getParam('idTextoEdital');
        $idEdital       = $this->_request->getParam('idEdital');
        
        $modelEditalFinal   = new EditalFinal();
        if (($idTextoEdital == '') || ($idEdital == '')) {
            parent::message('Texto do Edital excluído com sucesso!', 'edital/montagem-edital/idEdital/'.$idEdital, 'ERROR');
        }
        
        $db = Zend_Db_Table::getDefaultAdapter();

        
        $where = array('idTextoEdital = ?' => $idTextoEdital);
        
        try {
            $db->beginTransaction();
            //deleta o criterio de avaliação
            $modelEditalFinal->delete($where);

            $db->commit();
            parent::message('Texto do Edital excluído com sucesso!!', 'edital/montagem-edital/idEdital/'.$idEdital, 'CONFIRM');
        } catch (Exception $exc) {
            $db->rollBack();
            parent::message('Erro ao excluir texto do Edital', 'edital/montagem-edital/idEdital/'.$idEdital, 'ERROR');
        }
    }
    
    public function visualizarEditalAction()
    {
        $idEdital = $this->_request->getParam('idEdital');
        
        $modelTextoEdital = new tbTextoEdital();
        $editalMontado = $modelTextoEdital->buscarTextoEdital($idEdital);
        $this->view->editalmontado = $editalMontado;
        
//        $modelFormaPagamento        = new tbFormaPagamento();
//        $formaPagamento             = $modelFormaPagamento->buscarFormaPagamentoPorIdEdital($idEdital);
//        $this->view->formapagamento = $formaPagamento;
        
        $modelCriterioAvaliacao = new tbCriteriosAvaliacao();
        $criterioAvaliacao  =   $modelCriterioAvaliacao->buscarcriterioporidEdital($idEdital);
        $this->view->criterioavaliacao = $criterioAvaliacao;
    }
    
    public function informacaoGeralAction()
    {
        $idEdital = $this->_request->getParam('idEdital');
        $this->view->idEdital = $idEdital;
        $this->view->existePRP = 0;
        
        if (isset($idEdital) && !empty($idEdital)) {
            $tbEdital = new NovoEdital();
            $dadosEdital = $tbEdital->buscar(array('idEdital = ?' => $idEdital))->current();
            $this->view->dadosEdital = $dadosEdital;
            

            
            $tbEditalTipoParticipacao = new tbEditalTipoParticipacao();
            $dadosParticipacaoEdital = $tbEditalTipoParticipacao->buscar(array('idEdital = ?' => $idEdital));
            $this->view->dadosParticipacaoEdital = $dadosParticipacaoEdital;
            
            $tbEditalComposicao = new tbEditalComposicao();
            $dadosComposicaoEdital = $tbEditalComposicao->buscar(array('idEdital = ?' => $idEdital));
            $this->view->dadosComposicaoEdital = $dadosComposicaoEdital;
            
            $existeComposicaoPRP = $tbEditalComposicao->buscar(array('idEdital = ?' => $idEdital, 'idComposicao = ?' => 9));
            if (count($existeComposicaoPRP) > 0) {
                $this->view->existePRP = 1;
            }
        }

        $tipopremiacao = new TipoPremiacao();
        $tipoPremiacaoEdital = $tipopremiacao->buscarTipoPremiacao();
        $this->view->tipopremiacao = $tipoPremiacaoEdital;

        $origemrecurso = new Verificacao();
        $idTpEdital = 15;
        
        if ($dadosEdital->idTpEdital == 2) {
            $idTpEdital = 5;
        }
        
        $where = array(
            'idTipo = ?'    => $idTpEdital,
            'stEstado = ?'  => 1
        );
        
        $origRecurso = $origemrecurso->buscarOrigemRecurso($where);
        $this->view->origemrecursoedital = $origRecurso;

        $areaEdital = new Area();
        $area = $areaEdital->BuscarAreas();
        $this->view->area = $area;

        $segmentoArea = new Segmento();
        $segmento = $segmentoArea->buscaCompleta();
        $this->view->segmento = $segmento;
        
        $criterioAvaliacao = new tbCriteriosAvaliacao();
        $critAvaliacao = $criterioAvaliacao->buscarCriteriosAvaliacao();
        $this->view->criterioavaliacao = $critAvaliacao;
        
        $modelTipoParticipacao = new tbTipoParticipacao();
        $tipoParticipacao = $modelTipoParticipacao->buscarTipoParticipacao();
        $this->view->tipoParticipacao = $tipoParticipacao;
        
        $tbEditalAreaSegmento = new tbEditalAreaSegmento();
        $editalAreas = array();
        $editalSegmentos = array();
        
        $areaSeguimentos = $tbEditalAreaSegmento->buscarAreasSegmento($idEdital, false);
        
        foreach ($areaSeguimentos as $as) {
            if (!in_array($as['idArea'], $editalAreas)) {
                array_push($editalAreas, $as['idArea']);
            }
            
            if (!in_array($as['idSegmento'], $editalSegmentos)) {
                array_push($editalSegmentos, $as['idSegmento']);
            }
        }
        
        $this->view->editalAreas = $editalAreas;
        $this->view->editalSegmentos = $editalSegmentos;
    }

    public function moduloseditalAction()
    {
        $idEdital = $this->_request->getParam('idEdital');
        $this->view->idEdital = $idEdital;

        // Models
        $modelFluxoEdital            = new tbEditalFluxo();
        $modelEdital                 = new NovoEdital();
        $modelModulos                = new tbModulo();
        $modelCategoriaModulo        = new tbCategoria();
        $modelEditalTipoParticipacao = new tbEditalTipoParticipacao();
        
        // validar se existe o Edital ou é do proprietário
        $edital = $modelEdital->buscar(array('idEdital = ?' => $idEdital));
        if (count($edital) == 0) {
            parent::message('Edital não encontrado ou não pertence a esse orgão!', 'edital/', 'ALERT');
        }
        
        $arrModulos     = array();
        $mostrarSubMenuPag  = 0;
                
        $fluxoPagamentoEdital = $modelFluxoEdital->buscarFluxoPorEdital(array('ef.idEdital = ?' => $idEdital, 'ef.idFluxo = ?' => 3));
        if (count($fluxoPagamentoEdital) > 0) {
            $mostrarSubMenuPag = 1;
        }
        $this->view->mostrarSubMenuPag = $mostrarSubMenuPag;
        
        $infoEdital = $modelEdital->buscarIdTipoEdital($idEdital);
        $this->view->idTpEdital = $infoEdital['idTpEdital'];
        
        $listaModulos = $modelModulos->buscarModulo($idEdital);

        if (count($listaModulos)>0) {
            $i = 0;
            foreach ($listaModulos as $mod) {
                $arrModulos[$i]['idModulo'] = $mod->idModulo;
                $arrModulos[$i]['dsModulo'] = $mod->dsModulo;
                $listaCategMod =  $modelCategoriaModulo->buscar(array('idModulo = ?' => $mod->idModulo));
                $arrCatModulos = array();
                $c = 0;
                foreach ($listaCategMod as $cat) {
                    $arrCatModulos[$c]['idCategoria'] = $cat['idCategoria'];
                    $arrCatModulos[$c]['nmCategoria'] = $cat['nmCategoria'];
                    $c++;
                }
                $arrModulos[$i]['catModulo'] = $arrCatModulos;
                $i++;
            }
        }

        // Tem que buscar somente os do Edital
        $participacoes = $modelEditalTipoParticipacao->buscarParticipacaoPorEdital(array('idEdital = ?' => $idEdital));
        $this->view->participacoes = $participacoes;
        
        // Recupera a lista de guias
        $this->view->modulos = $arrModulos;
        $this->view->qtdModulos = count($arrModulos);
    }
    
    public function criteriosParticipacaoAction()
    {
        $this->view->submenuativo = 'criterioparticipacao';
        
        $idEdital       = $this->_request->getParam('idEdital');
        $idModulo       = $this->_request->getParam('idModulo');
        $idCategoria    = $this->_request->getParam('idCategoria');

        $this->view->idEdital       = $idEdital;
        $this->view->idModulo       = $idModulo;
        $this->view->idCategoria    = $idCategoria;
        
        $modelModulos               = new tbModulo();
        $modelCategoriaModulo       = new tbCategoria();
        $modelCriterioParticipacao  = new tbCriterioParticipacao();
        $modelUF                    = new Uf();
        $modelMunicipios            = new Municipios();
        $modelFluxoEdital           = new tbEditalFluxo();
        
        $mostrarSubMenuPag = 0;
        $fluxoPagamentoEdital = $modelFluxoEdital->buscarFluxoPorEdital(array('ef.idEdital = ?' => $idEdital, 'ef.idFluxo = ?' => 3));
        if (count($fluxoPagamentoEdital) > 0) {
            $mostrarSubMenuPag = 1;
        }
        $this->view->mostrarSubMenuPag = $mostrarSubMenuPag;
        
        $listaUF = $modelUF->buscar(array(), array('Descricao'));
        $this->view->listaUF = $listaUF;
        
        $listaMunicipios = $modelMunicipios->buscaCompleta(array(), array('mu.idUFIBGE','mu.Descricao'), false);
//        $listaMunicipios = $modelMunicipios->buscar(array(), array('idUFIBGE','Descricao'));
        $this->view->listaMunicipios = $listaMunicipios;
        

        
        $criterioParticipacao = $modelCriterioParticipacao->buscarCriterioPorIdCategoria($idCategoria);
        $this->view->criterioParticipacao = $criterioParticipacao;
        
        $arrModulos     = array();
        
        $listaModulos = $modelModulos->buscarModulo($idEdital);

        if (count($listaModulos)>0) {
            $i = 0;
            foreach ($listaModulos as $mod) {
                $arrModulos[$i]['idModulo'] = $mod->idModulo;
                $arrModulos[$i]['dsModulo'] = $mod->dsModulo;
                $listaCategMod =  $modelCategoriaModulo->buscar(array('idModulo = ?' => $mod->idModulo));
                $arrCatModulos = array();
                $c = 0;
                foreach ($listaCategMod as $cat) {
                    $arrCatModulos[$c]['idCategoria'] = $cat['idCategoria'];
                    $arrCatModulos[$c]['nmCategoria'] = $cat['nmCategoria'];

                    $c++;
                }
                $arrModulos[$i]['catModulo'] = $arrCatModulos;
                $i++;
            }
        }
        
        $this->view->modulos = $arrModulos;
        $this->view->qtdModulos = count($arrModulos);
    }
    
    public function informacoesCategoriaAction()
    {
        $this->view->submenuativo = 'informacaogeral';
        
        $idEdital       = $this->_request->getParam('idEdital');
        $idModulo       = $this->_request->getParam('idModulo');
        $idCategoria    = $this->_request->getParam('idCategoria');

        $this->view->idEdital       = $idEdital;
        $this->view->idModulo       = $idModulo;
        $this->view->idCategoria    = $idCategoria;
        
        $modelModulos               = new tbModulo();
        $modelCategoriaModulo       = new tbCategoria();
        $modelFluxoEdital           = new tbEditalFluxo();
        
        $mostrarSubMenuPag = 0;
        $fluxoPagamentoEdital = $modelFluxoEdital->buscarFluxoPorEdital(array('ef.idEdital = ?' => $idEdital, 'ef.idFluxo = ?' => 3));
        if (count($fluxoPagamentoEdital) > 0) {
            $mostrarSubMenuPag = 1;
        }
        $this->view->mostrarSubMenuPag = $mostrarSubMenuPag;
        
        
        $dadosCategoria = $modelCategoriaModulo->find($idCategoria);
        $this->view->nmCategoria    = $dadosCategoria[0]->nmCategoria;
        
        $arrModulos     = array();
        $listaModulos = $modelModulos->buscarModulo($idEdital);

        if (count($listaModulos)>0) {
            $i = 0;
            foreach ($listaModulos as $mod) {
                $arrModulos[$i]['idModulo'] = $mod->idModulo;
                $arrModulos[$i]['dsModulo'] = $mod->dsModulo;
                $listaCategMod =  $modelCategoriaModulo->buscar(array('idModulo = ?' => $mod->idModulo));
                $arrCatModulos = array();
                $c = 0;
                foreach ($listaCategMod as $cat) {
                    $arrCatModulos[$c]['idCategoria'] = $cat['idCategoria'];
                    $arrCatModulos[$c]['nmCategoria'] = $cat['nmCategoria'];

                    $c++;
                }
                $arrModulos[$i]['catModulo'] = $arrCatModulos;
                $i++;
            }
        }
        
        $this->view->modulos = $arrModulos;
        $this->view->qtdModulos = count($arrModulos);
    }
    
    public function informacoesModuloAction()
    {
        $idEdital       = $this->_request->getParam('idEdital');
        $idModulo       = $this->_request->getParam('idModulo');

        $this->view->idEdital       = $idEdital;
        $this->view->idModulo       = $idModulo;
        
        $modelModulos               = new tbModulo();
        $modelCategoriaModulo       = new tbCategoria();
        $modelFluxoEdital           = new tbEditalFluxo();
        
        $fluxoPagamentoEdital = $modelFluxoEdital->buscarFluxoPorEdital(array('ef.idEdital = ?' => $idEdital, 'ef.idFluxo = ?' => 3));
        if (count($fluxoPagamentoEdital) > 0) {
            $mostrarSubMenuPag = 1;
        }
        $this->view->mostrarSubMenuPag = $mostrarSubMenuPag;
        
        $dadosModulo = $modelModulos->find($idModulo);
        $this->view->dsModulo    = $dadosModulo[0]->dsModulo;
        
        $arrModulos     = array();
        $listaModulos = $modelModulos->buscarModulo($idEdital);

        if (count($listaModulos)>0) {
            $i = 0;
            foreach ($listaModulos as $mod) {
                $arrModulos[$i]['idModulo'] = $mod->idModulo;
                $arrModulos[$i]['dsModulo'] = $mod->dsModulo;
                $listaCategMod =  $modelCategoriaModulo->buscar(array('idModulo = ?' => $mod->idModulo));
                $arrCatModulos = array();
                $c = 0;
                foreach ($listaCategMod as $cat) {
                    $arrCatModulos[$c]['idCategoria'] = $cat['idCategoria'];
                    $arrCatModulos[$c]['nmCategoria'] = $cat['nmCategoria'];

                    $c++;
                }
                $arrModulos[$i]['catModulo'] = $arrCatModulos;
                $i++;
            }
        }
        
        $this->view->modulos = $arrModulos;
        $this->view->qtdModulos = count($arrModulos);
        
        // Tem que buscar somente os do Edital
        $modelEditalTipoParticipacao = new tbEditalTipoParticipacao();
        $participacoes = $modelEditalTipoParticipacao->buscarParticipacaoPorEdital(array('idEdital = ?' => $idEdital));
        $this->view->participacoes = $participacoes;
    }

    public function novoModuloAction()
    {
        $idEdital = $this->_request->getParam('idEdital');

        $modelCriteriosAvaliacao    = new tbCriteriosAvaliacao();
        $modelModulos               = new tbModulo();
        $modelCategoriaModulo       = new tbCategoria();
        $modelCriterioParticipacao  = new tbCriterioParticipacao();
        $modelFluxoEdital           = new tbEditalFluxo();
        
        $mostrarSubMenuPag = 0;
        $fluxoPagamentoEdital = $modelFluxoEdital->buscarFluxoPorEdital(array('ef.idEdital = ?' => $idEdital, 'ef.idFluxo = ?' => 3));
        if (count($fluxoPagamentoEdital) > 0) {
            $mostrarSubMenuPag = 1;
        }
        $this->view->mostrarSubMenuPag = $mostrarSubMenuPag;
        
        $criterios = $modelCriteriosAvaliacao->buscar(array('idEdital = ?' => $idEdital));
        if (count($criterios) == 0) {
            parent::message('Informe os critérios de avaliação!', 'edital/criterios-avaliacao/idEdital/'.$idEdital, 'ALERT');
        }
        
        $arrModulos     = array();
        $idCat          = 0;
        $idCCategoria   = 0;
                
        $listaModulos = $modelModulos->buscarModulo($idEdital);

        if (count($listaModulos)>0) {
            $i = 0;
            foreach ($listaModulos as $mod) {
                $arrModulos[$i]['idModulo'] = $mod->idModulo;
                $arrModulos[$i]['dsModulo'] = $mod->dsModulo;
                $listaCategMod =  $modelCategoriaModulo->buscar(array('idModulo = ?' => $mod->idModulo));
                $arrCatModulos = array();
                $c = 0;
                foreach ($listaCategMod as $cat) {
                    $idCCategoria   = $cat['idCategoria'];
                    $idCat          = $cat['idCategoria'];
                    $arrCatModulos[$c]['idCategoria'] = $cat['idCategoria'];
                    $arrCatModulos[$c]['nmCategoria'] = $cat['nmCategoria'];

                    $c++;
                }
                $arrModulos[$i]['catModulo'] = $arrCatModulos;
                $i++;
            }
        }
        
        $this->view->modulos = $arrModulos;
        $this->view->qtdModulos = count($arrModulos);

        // Tem que buscar somente os do Edital
        $modelEditalTipoParticipacao = new tbEditalTipoParticipacao();
        $participacoes = $modelEditalTipoParticipacao->buscarParticipacaoPorEdital(array('idEdital = ?' => $idEdital));
        $this->view->participacoes = $participacoes;
        
        $criterioParticipacao = $modelCriterioParticipacao->buscarCriterioPorIdCategoria($idCat);
        $this->view->criterioParticipacao = $criterioParticipacao;
        
        $modelTipoParticipacao = new tbTipoParticipacao();
        $tipoParticipacaoModulo = $modelTipoParticipacao->buscarTipoParticipacao();
        $this->view->tipoParticipacao = $tipoParticipacaoModulo;
        
        $this->view->idEdital = $idEdital;
    }
    
    public function novaCategoriaAction()
    {
        $idEdital = $this->_request->getParam('idEdital');
        $idModulo = $this->_request->getParam('idModulo');

        $modelCriteriosAvaliacao    = new tbCriteriosAvaliacao();
        $modelModulos               = new tbModulo();
        $modelCategoriaModulo       = new tbCategoria();
        $modelCriterioParticipacao  = new tbCriterioParticipacao();
        $modelFluxoEdital           = new tbEditalFluxo();
        
        $mostrarSubMenuPag = 0;
        $fluxoPagamentoEdital = $modelFluxoEdital->buscarFluxoPorEdital(array('ef.idEdital = ?' => $idEdital, 'ef.idFluxo = ?' => 3));
        if (count($fluxoPagamentoEdital) > 0) {
            $mostrarSubMenuPag = 1;
        }
        $this->view->mostrarSubMenuPag = $mostrarSubMenuPag;
        
        $criterios = $modelCriteriosAvaliacao->buscar(array('idEdital = ?' => $idEdital));
        if (count($criterios) == 0) {
            parent::message('Informe os critérios de avaliação!', 'edital/criterios-avaliacao/idEdital/'.$idEdital, 'ALERT');
        }
        
        $arrModulos     = array();
        $idCat          = 0;
        $idCCategoria   = 0;
                
        $listaModulos = $modelModulos->buscarModulo($idEdital);

        if (count($listaModulos)>0) {
            $i = 0;
            foreach ($listaModulos as $mod) {
                $arrModulos[$i]['idModulo'] = $mod->idModulo;
                $arrModulos[$i]['dsModulo'] = $mod->dsModulo;
                $listaCategMod =  $modelCategoriaModulo->buscar(array('idModulo = ?' => $mod->idModulo));
                $arrCatModulos = array();
                $c = 0;
                foreach ($listaCategMod as $cat) {
                    $idCCategoria   = $cat['idCategoria'];
                    $idCat          = $cat['idCategoria'];
                    $arrCatModulos[$c]['idCategoria'] = $cat['idCategoria'];
                    $arrCatModulos[$c]['nmCategoria'] = $cat['nmCategoria'];

                    $c++;
                }
                $arrModulos[$i]['catModulo'] = $arrCatModulos;
                $i++;
            }
        }
        
        $this->view->modulos = $arrModulos;
        $this->view->qtdModulos = count($arrModulos);

        $criterioParticipacao = $modelCriterioParticipacao->buscarCriterioPorIdCategoria($idCat);
        $this->view->criterioParticipacao = $criterioParticipacao;
        
        $modelTipoParticipacao = new tbTipoParticipacao();
        $tipoParticipacaoModulo = $modelTipoParticipacao->buscarTipoParticipacao();
        $this->view->tipoParticipacao = $tipoParticipacaoModulo;
        
        $this->view->idEdital = $idEdital;
        $this->view->idModulo = $idModulo;
    }
    
    public function questionarioAction()
    {
        $this->view->submenuativo = 'questionario';
        
        $idEdital   = $this->_request->getParam('idEdital');
        $idModulo   = $this->_request->getParam('idModulo');
        $categoria  = $this->_request->getParam('categoria');

        $modelFluxoEdital           = new tbEditalFluxo();
        $modelCriteriosAvaliacao    = new tbCriteriosAvaliacao();
        $modelModulos               = new tbModulo();
        $modelCategoriaModulo       = new tbCategoria();
        $modelCriterioParticipacao  = new tbCriterioParticipacao();
        $mostrarSubMenuPag          = 0;
        
        $criterios = $modelCriteriosAvaliacao->buscar(array('idEdital = ?' => $idEdital));
        if (count($criterios) == 0) {
            parent::message('Informe os critérios de avaliação!', 'edital/criterios-avaliacao/idEdital/'.$idEdital, 'ALERT');
        }
        
        $fluxoPagamentoEdital = $modelFluxoEdital->buscarFluxoPorEdital(array('ef.idEdital = ?' => $idEdital, 'ef.idFluxo = ?' => 3));
        if (count($fluxoPagamentoEdital) > 0) {
            $mostrarSubMenuPag = 1;
        }
        $this->view->mostrarSubMenuPag = $mostrarSubMenuPag;
        
        $arrModulos     = array();
        $idCat          = 0;
        $idCCategoria   = 0;
                
        $listaModulos = $modelModulos->buscarModulo($idEdital);

        if (count($listaModulos)>0) {
            $i = 0;
            foreach ($listaModulos as $mod) {
                $arrModulos[$i]['idModulo'] = $mod->idModulo;
                $arrModulos[$i]['dsModulo'] = $mod->dsModulo;
                $listaCategMod =  $modelCategoriaModulo->buscar(array('idModulo = ?' => $mod->idModulo));
                $arrCatModulos = array();
                $c = 0;
                foreach ($listaCategMod as $cat) {
                    $idCCategoria   = $cat['idCategoria'];
                    $idCat          = $cat['idCategoria'];
                    $arrCatModulos[$c]['idCategoria'] = $cat['idCategoria'];
                    $arrCatModulos[$c]['nmCategoria'] = $cat['nmCategoria'];

                    $c++;
                }
                $arrModulos[$i]['catModulo'] = $arrCatModulos;
                $i++;
            }
        }
        
        $this->view->modulos = $arrModulos;
        $this->view->qtdModulos = count($arrModulos);

        $criterioParticipacao = $modelCriterioParticipacao->buscarCriterioPorIdCategoria($idCat);
        $this->view->criterioParticipacao = $criterioParticipacao;
        
        $modelTipoParticipacao = new tbTipoParticipacao();
        $tipoParticipacaoModulo = $modelTipoParticipacao->buscarTipoParticipacao();
        $this->view->tipoParticipacao = $tipoParticipacaoModulo;
        
        $tipoRespostaModel = new TipoRespostaModel();
        $this->view->tiposRespostas = $tipoRespostaModel->pesquisar();
        
        // Lista de Guias com perguntas
        $guiaModel      = new GuiaModel();
        $questaoModel   = new QuestaoModel();
        $arrayGuias     = array();
        $listaGuias     = $guiaModel->pesquisarPorEditalModuloCategoria($categoria);
        
        $g = 0;
        foreach ($listaGuias as $guia) {
            $arrayGuias[$g]['idGuia']       = $guia['idGuia'];
            $arrayGuias[$g]['nmGuia']       = $guia['nmGuia'];
            $arrayGuias[$g]['txAuxilio']    = $guia['txAuxilio'];
            $arrayGuias[$g]['idCategoria']  = $guia['idCategoria'];
            $arrayGuias[$g]['orGuia']       = $guia['orGuia'];
            
            $arrayQuestoes = array();
            $listarQuestoes = $questaoModel->buscarQuestoesPorGuia($guia['idGuia'], false);
            $q = 0;
            foreach ($listarQuestoes as $quest) {
                $arrayQuestoes[$q]['idQuestao']     = $quest['idQuestao'];
                $arrayQuestoes[$q]['dsQuestao']     = $quest['dsQuestao'];
                $arrayQuestoes[$q]['dsAjuda']       = $quest['dsAjuda'];
                $arrayQuestoes[$q]['dsTpResposta']  = $quest['dsTpResposta'];
                $arrayQuestoes[$q]['orQuestao']     = $quest['orQuestao'];
                $q++;
            }
            
            $arrayGuias[$g]['questoes'] = $arrayQuestoes;
            
            $g++;
        }
        
        $this->view->guias          = $arrayGuias;
        $this->view->idEdital       = $idEdital;
        $this->view->idModulo       = $idModulo;
        $this->view->idCategoria    = $categoria;
    }
    
    public function formaPagamentoAction()
    {
        $this->view->submenuativo = 'formapagamento';
        
        $idEdital       = $this->_request->getParam('idEdital');
        $idModulo       = $this->_request->getParam('idModulo');
        $idCategoria    = $this->_request->getParam('idCategoria');

        $modelFluxoEdital           = new tbEditalFluxo();
        $modelModulos               = new tbModulo();
        $modelCategoriaModulo       = new tbCategoria();
        $modelCriterioParticipacao  = new tbCriterioParticipacao();
        $modelFormaPagamento        = new tbFormaPagamento();
        $mostrarSubMenuPag          = 0;
        
        $fluxoPagamentoEdital = $modelFluxoEdital->buscarFluxoPorEdital(array('ef.idEdital = ?' => $idEdital, 'ef.idFluxo = ?' => 3));
        if (count($fluxoPagamentoEdital) > 0) {
            $mostrarSubMenuPag = 1;
        }
        $this->view->mostrarSubMenuPag = $mostrarSubMenuPag;
        
        $arrModulos     = array();
        $idCat          = 0;
                
        $listaModulos = $modelModulos->buscarModulo($idEdital);

        if (count($listaModulos)>0) {
            $i = 0;
            foreach ($listaModulos as $mod) {
                $arrModulos[$i]['idModulo'] = $mod->idModulo;
                $arrModulos[$i]['dsModulo'] = $mod->dsModulo;
                $listaCategMod =  $modelCategoriaModulo->buscar(array('idModulo = ?' => $mod->idModulo));
                $arrCatModulos = array();
                $c = 0;
                foreach ($listaCategMod as $cat) {
                    $idCat          = $cat['idCategoria'];
                    $arrCatModulos[$c]['idCategoria'] = $cat['idCategoria'];
                    $arrCatModulos[$c]['nmCategoria'] = $cat['nmCategoria'];

                    $c++;
                }
                $arrModulos[$i]['catModulo'] = $arrCatModulos;
                $i++;
            }
        }
        
        $this->view->modulos = $arrModulos;
        $this->view->qtdModulos = count($arrModulos);

        $criterioParticipacao = $modelCriterioParticipacao->buscarCriterioPorIdCategoria($idCat);
        $this->view->criterioParticipacao = $criterioParticipacao;
        
        $modelTipoParticipacao = new tbTipoParticipacao();
        $tipoParticipacaoModulo = $modelTipoParticipacao->buscarTipoParticipacao();
        $this->view->tipoParticipacao = $tipoParticipacaoModulo;
        
        if (count($listaModulos) > 0) {
            $formaPagamentoCategoria            = $modelFormaPagamento->buscarFormaPagamentoPorIdCategoria($idCategoria);
            $this->view->formapagamento         = $formaPagamentoCategoria;
        }
        
        $this->view->idEdital       = $idEdital;
        $this->view->idModulo       = $idModulo;
        $this->view->idCategoria    = $idCategoria;
    }

    public function atualizaInfoGeralModuloAction()
    {
        
         //Recupera as informações: nome do modulo a ser cadastrado, e o id do Edital a ser associado.
        $idEdital           = $this->_request->getParam('idEdital');
        $idModulo           = $this->_request->getParam('idModulo');
        $nomeModulo         = $this->_request->getParam('nomeModulo');
        $tipoParticipacao   = $this->_request->getParam('tipoParticipacaoModulo');
        $qtParticipacao     = $this->_request->getParam('qtParticipacao');
   
        $modelModulo    = new tbModulo();

        $db = Zend_Db_Table::getDefaultAdapter();

        
        try {
            $db->beginTransaction();
            $dadoModulo = array('dsModulo' => $nomeModulo);
 
            $where['idModulo = ?'] = $idModulo;
            $modelModulo->atualizaModulo($dadoModulo, $where);

            foreach ($tipoparticipacao as $tpart) {
                $dadosTipoParticipacao = array(
                        'idEdital'          => $idEdital,
                        'idTpParticipacao'  => $tipoParticipacao[0],
                        'qtParticipacao'    => $qtTipoParticipacao
                );
                
                $modelEditalTipoParticipacao->associarEditalTipoParticipacao($dadosTipoParticipacao);
            }
            
            $db->commit();
            parent::message('Informações gerais atualizado com sucesso!', 'edital/informacoes-modulo/idEdital/'.$idEdital.'/idModulo/'.$idModulo, 'CONFIRM');
        } catch (Exception $exc) {
            $db->rollBack();
            xd($exc);
            parent::message('Erro ao atualizar as informações gerais', 'edital/informacoes-modulo/idEdital/'.$idEdital.'/idModulo/'.$idModulo, 'ERROR');
        }
    }
    
    public function salvaCriterioParticipacaoAction()
    {
        $idEdital                  = $this->_request->getParam('idEdital');
        $idModulo                  = $this->_request->getParam('idModulo');
        $idCategoria               = $this->_request->getParam('idCategoria');
        $dsCriterioParticipacao    = $this->_request->getParam('dsCriterioParticipacao');
        $regraCampo                = $this->_request->getParam('regraCampo');
        $stObrigatorio             = $this->_request->getParam('respostaObrigatoria');
        
        if (!isset($stObrigatorio)) {
            $stObrigatorio = 'N';
        }
        
        $UF                         = $this->_request->getParam('uf');
        $idCidade                   = $this->_request->getParam('idCidade');
        $dsFaixaEtariaInicio        = $this->_request->getParam('dsFaixaEtariaInicio');
        $dsFaixaEtariaFim           = $this->_request->getParam('dsFaixaEtariaFim');
        $dsRegiao                   = $this->_request->getParam('dsRegiao');
        $dsSexo                     = $this->_request->getParam('dsSexo');
        $municipio                  = $this->_request->getParam('municipio');
        
        
        $modelUF                            = new Uf();
        $modelCriterioParticipacao          = new tbCriterioParticipacao();
        $modelRegiaoCriterioParticipacao    = new tbRegiaoCriterioParticipacao();


        
        $db = Zend_Db_Table::getDefaultAdapter();

        
        try {
            $db->beginTransaction();
            if ($regraCampo == 'DN') {
                $dadosCriterioParticipacao = array(
                        'idCategoria'               => $idCategoria,
                        'dsCriterioParticipacao'    => $dsCriterioParticipacao,
                        'rgCriterioParticipacao'    => $regraCampo,
                        'dsFaixaEtariaInicio'       => $dsFaixaEtariaInicio,
                        'dsFaixaEtariaFim'          => $dsFaixaEtariaFim,
                        'stObrigatorio'             => $stObrigatorio
                );

                $idCriterioParticipacao = $modelCriterioParticipacao->salvarcriterioparticipacao($dadosCriterioParticipacao);
            } elseif ($regraCampo == 'SX') {
                $dadosCriterioParticipacao = array(
                        'idCategoria'               => $idCategoria,
                        'dsCriterioParticipacao'    => $dsCriterioParticipacao,
                        'rgCriterioParticipacao'    => $regraCampo,
                        'dsSexo'                    => $dsSexo,
                        'stObrigatorio'             => $stObrigatorio
                );

                $idCriterioParticipacao = $modelCriterioParticipacao->salvarcriterioparticipacao($dadosCriterioParticipacao);
            } elseif ($regraCampo == 'RE') {
                $listarUFs = array();
                $regioes = '';
                $i = 1;
                
                foreach ($dsRegiao as $re) {
                    $regioes .= $re;
                    if ($i < count($dsRegiao)) {
                        $regioes .= ' - ';
                    }

                    if ($re == 'N') {
                        $ufs = $modelUF->buscar(array('Regiao = ?' => 'Norte'))->toArray();
                        foreach ($ufs as $uf) {
                            array_push($listarUFs, $uf['idUF']);
                        }
                    } elseif ($re == 'NO') {
                        $ufs = $modelUF->buscar(array('Regiao = ?' => 'Nordeste'))->toArray();
                        foreach ($ufs as $uf) {
                            array_push($listarUFs, $uf['idUF']);
                        }
                    } elseif ($re == 'S') {
                        $ufs = $modelUF->buscar(array('Regiao = ?' => 'Sul'))->toArray();
                        foreach ($ufs as $uf) {
                            array_push($listarUFs, $uf['idUF']);
                        }
                    } elseif ($re == 'SU') {
                        $ufs = $modelUF->buscar(array('Regiao = ?' => 'Sudeste'))->toArray();
                        foreach ($ufs as $uf) {
                            array_push($listarUFs, $uf['idUF']);
                        }
                    } elseif ($re == 'CO') {
                        $ufs = $modelUF->buscar(array('Regiao = ?' => 'Centro Oeste'))->toArray();
                        foreach ($ufs as $uf) {
                            array_push($listarUFs, $uf['idUF']);
                        }
                    }

                    $i++;
                }

                $dadosCriterioParticipacao = array(
                        'idCategoria'               => $idCategoria,
                        'dsCriterioParticipacao'    => $dsCriterioParticipacao,
                        'rgCriterioParticipacao'    => $regraCampo,
                        'stObrigatorio'             => $stObrigatorio
                );

                $idCriterioParticipacao = $modelCriterioParticipacao->salvarcriterioparticipacao($dadosCriterioParticipacao);

                // Foreach $dsRegiao
                foreach ($listarUFs as $ufc) {
                    $dadosRegiaoCriterioParticipacao = array(
                            'idCriterioParticipacao'    => $idCriterioParticipacao,
                            'dsRegiao'                  => $regioes,
                            'idUf'                      => $ufc
                    );

                    $modelRegiaoCriterioParticipacao->inserir($dadosRegiaoCriterioParticipacao);
                }
            } elseif ($regraCampo == 'CI') {
                $dadosCriterioParticipacao = array(
                        'idCategoria'               => $idCategoria,
                        'dsCriterioParticipacao'    => $dsCriterioParticipacao,
                        'rgCriterioParticipacao'    => $regraCampo,
                        'stObrigatorio'             => $stObrigatorio
                );

                $idCriterioParticipacao = $modelCriterioParticipacao->salvarcriterioparticipacao($dadosCriterioParticipacao);

                // Foreach $idCidade
                foreach ($municipio as $ci) {
                    $dadosCidade = explode(',', $ci);
                    
                    $idUF       = $dadosCidade[0];
                    $idCidade   = $dadosCidade[1];
                    
                    $dadosCidadesCriterioParticipacao = array(
                            'idCriterioParticipacao'    => $idCriterioParticipacao,
                            'idUf'                      => $idUF,
                            'idCidade'                  => $idCidade
                    );
                    
                    $modelRegiaoCriterioParticipacao->inserir($dadosCidadesCriterioParticipacao);
                }
            } elseif ($regraCampo == 'UF') {
                $dadosCriterioParticipacao = array(
                        'idCategoria'               => $idCategoria,
                        'dsCriterioParticipacao'    => $dsCriterioParticipacao,
                        'rgCriterioParticipacao'    => $regraCampo,
                        'stObrigatorio'             => $stObrigatorio
                );

                $idCriterioParticipacao = $modelCriterioParticipacao->salvarcriterioparticipacao($dadosCriterioParticipacao);

                // Foreach $idUf
                foreach ($UF as $u) {
                    $dadosRegiaoCriterioParticipacao = array(
                            'idCriterioParticipacao'    => $idCriterioParticipacao,
                            'idUf'                      => $u
                    );

                    $modelRegiaoCriterioParticipacao->inserir($dadosRegiaoCriterioParticipacao);
                }
            }

            $db->commit();
            parent::message('Critério de Participação cadastrado com sucesso!', 'edital/criterios-participacao/idEdital/'.$idEdital.'/idModulo/'.$idModulo.'/idCategoria/'.$idCategoria, 'CONFIRM');
        } catch (Exception $exc) {
            $db->rollBack();
            xd($exc->getMessage());
            parent::message('Erro ao cadastrar os Critérios de Participação', 'edital/criterios-participacao/idEdital/'.$idEdital.'/idModulo/'.$idModulo.'/idCategoria/'.$idCategoria, 'ERROR');
        }
    }
    
    public function deletarCriterioParticipacaoAction()
    {
        $idEdital                   = $this->_request->getParam('idEdital');
        $idModulo                   = $this->_request->getParam('idModulo');
        $idCategoria                = $this->_request->getParam('idCategoria');
        $idCriterioParticipacao     = $this->_request->getParam('idCriterioParticipacao');
        
        $modelCriterioParticipacao = new tbCriterioParticipacao();
        $modelRegiaoCriterioParticipacao = new tbRegiaoCriterioParticipacao();
        
        if (($idCriterioParticipacao == '') || ($idEdital == '')) {
            parent::message('Criterio de Participacao não encontrado.', 'edital/criterios-participacao/idEdital/'.$idEdital.'/idModulo/'.$idModulo.'/idCategoria/'.$idCategoria, 'ERROR');
        }
        
        $db = Zend_Db_Table::getDefaultAdapter();

        
        $where = array('idCriterioParticipacao = ?' => $idCriterioParticipacao);
        
        try {
            $db->beginTransaction();
            //deleta o criterio de avaliação
            $modelRegiaoCriterioParticipacao->delete($where);
            $modelCriterioParticipacao->delete($where);

            $db->commit();
            parent::message('Crit&eacute;rio de Participacao excluído!', 'edital/criterios-participacao/idEdital/'.$idEdital.'/idModulo/'.$idModulo.'/idCategoria/'.$idCategoria, 'CONFIRM');
        } catch (Exception $exc) {
            $db->rollBack();
            parent::message('Erro ao excluir Crit&eacute;rio de Participacao', 'edital/criterios-participacao/idEdital/'.$idEdital.'/idModulo/'.$idModulo.'/idCategoria/'.$idCategoria, 'ERROR');
        }
    }
    
    public function salvaFormaPagamentoAction()
    {
        $idFormaPagamento       = $this->_request->getParam('idFormaPagamento');
        $idEdital               = $this->_request->getParam('idEdital');
        $idCategoria            = $this->_request->getParam('idCategoria');
        $idModulo               = $this->_request->getParam('idModulo');
        $dsFormaPagamento       = $this->_request->getParam('dsFormaPagamento');
        $vlrApoio               = $this->_request->getParam('vlrApoio');
        $qtPremiados            = $this->_request->getParam('qtPremiados');
        $qtParcela              = $this->_request->getParam('qtdParcelas');
        $parcelas               = $this->_request->getParam('parcela');
        

        
        $this->view->idEdital   = $idEdital;
        
        $modelFormaPagamento    = new tbFormaPagamento();
        $modelParcelaFormaPagamento    = new tbParcelaFormaPagamento();

        $db = Zend_Db_Table::getDefaultAdapter();

        
        try {
            $db->beginTransaction();
            $dadosFormaPagamento = array(
                    'idCategoria'               => $idCategoria,
                    'dsFormaPagamento'          => $dsFormaPagamento,
                    'vlrApoio'                  => Mascara::delMaskMoeda($vlrApoio),
                    'qtPremiados'               => $qtPremiados,
                    'qtParcelas'                => $qtParcela
            );
            
            if (!empty($idFormaPagamento) && isset($idFormaPagamento)) {
                $modelFormaPagamento->update($dadosFormaPagamento, array('idFormaPagamento = ?' => $idFormaPagamento));
            } else {
                $idFormaPagamento = $modelFormaPagamento->salvarFormaPagamento($dadosFormaPagamento);
            }
            
            $modelParcelaFormaPagamento->delete(array('idFormaPagamento = ?' => $idFormaPagamento));
            
            if (count($parcelas) > 0) {
                $nrParcela = 1;
                foreach ($parcelas as $p) {
                    $dadosParcela = array(
                        'idFormaPagamento'  => intval($idFormaPagamento),
                        'nrParcela'         => intval($nrParcela),
                        'vlrParcela'        => Mascara::delMaskMoeda($p)
                    );
                    
                    // Salva a parcela
                    $modelParcelaFormaPagamento->inserir($dadosParcela);
                    
                    $nrParcela++;
                }
            }
            
            $db->commit();
            parent::message('Forma de Pagamento cadastrado com sucesso!', 'edital/forma-pagamento/idEdital/'.$idEdital.'/idModulo/'.$idModulo.'/idCategoria/'.$idCategoria, 'CONFIRM');
        } catch (Exception $exc) {
            $db->rollBack();
            xd($exc);
            parent::message('Erro ao cadastrar a Forma de Pagamento', 'edital/forma-pagamento/idEdital/'.$idEdital.'/idModulo/'.$idModulo.'/idCategoria/'.$idCategoria, 'ERROR');
        }
    }

    public function deletarFormaPagamentoAction()
    {
        $idFormaPagamento   = $this->_request->getParam('idFormaPagamento');
        $idEdital           = $this->_request->getParam('idEdital');
        $idModulo           = $this->_request->getParam('idModulo');
        $idCategoria        = $this->_request->getParam('idCategoria');
        
        $modelFormaPagamento        = new tbFormaPagamento();
        $modelParcelaFormaPagamento = new tbParcelaFormaPagamento();
        
        if (($idFormaPagamento == '') || ($idEdital == '')) {
            parent::message('Forma de Pagamento excluído com sucesso!', 'edital/forma-pagamento/idEdital/'.$idEdital.'/idModulo/'.$idModulo.'/idCategoria/'.$idCategoria, 'ERROR');
        }
        
        $db = Zend_Db_Table::getDefaultAdapter();

        
        $where = array('idFormaPagamento = ?' => $idFormaPagamento);

        try {
            $db->beginTransaction();
            // Deleta todas as parcelas do pagamento
            $modelParcelaFormaPagamento->delete($where);
            
            // Delete a forma de pagamento
            $modelFormaPagamento->delete($where);

            $db->commit();
            parent::message('Forma de Pagamento excluída com sucesso!', 'edital/forma-pagamento/idEdital/'.$idEdital.'/idModulo/'.$idModulo.'/idCategoria/'.$idCategoria, 'CONFIRM');
        } catch (Exception $exc) {
            $db->rollBack();
            xd($exc->getMessage());
            parent::message('Erro ao excluir forma de pagamento', 'edital/forma-pagamento/idEdital/'.$idEdital.'/idModulo/'.$idModulo.'/idCategoria/'.$idCategoria, 'ERROR');
        }
    }
    
    public function buscaFormaPagamentoAjaxAction()
    {
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);

        $modelFormaPagamento = new tbFormaPagamento();
        $retorno    = array();
        $idFormaPagamento = $this->_request->getParam('idFormaPagamento');
        try {
            if (!$idFormaPagamento) {
                $retorno['error'] = utf8_encode('Error');
            } else {
                $formaPagamento = $modelFormaPagamento->buscarFormaPagamentoPorId($idFormaPagamento);

                if (count($formaPagamento) == 0) {
                    $retorno['error'] = utf8_encode('Forma de Pagamento não encontrado!');
                } else {
                    $retorno['dados']['idFormaPagamento']       = $formaPagamento[0]['idFormaPagamento'];
                    $retorno['dados']['idCategoria']            = $formaPagamento[0]['idCategoria'];
                    
                    $vlrApoio = $formaPagamento[0]['vlrApoio'];
                    $retorno['dados']['vlrApoio']               = str_replace(".", ",", $vlrApoio);
                    
                    $retorno['dados']['qtPremiados']            = $formaPagamento[0]['qtPremiados'];
                    $retorno['dados']['qtParcelas']             = $formaPagamento[0]['qtParcelas'];
                    $retorno['dados']['dsFormaPagamento']       = $formaPagamento[0]['dsFormaPagamento'];
                    $retorno['error'] = '';
                }
            }
        } catch (Exception $exc) {
            $retorno['error'] = $exc->getTraceAsString();
        }

        $this->_helper->json($retorno);
    }
    
    public function salvaModuloAction()
    {

        //Recupera as informações: nome do modulo a ser cadastrado, e o id do Edital a ser associado.
        $idEdital           = $this->_request->getParam('idEdital');
        $nomeModulo         = $this->_request->getParam('nomeModulo');
        $tipoParticipacao   = $this->_request->getParam('tipoParticipacaoModulo');
        $qtParticipacao   = $this->_request->getParam('qtParticipacaoModulo');

        $nModulo = new tbModulo();
        $editalModulo = new tbEditalModulo();
        $modelModuloParticipacao = new tbModuloParticipacao();
        
        $db = Zend_Db_Table::getDefaultAdapter();

        try {
            $db->beginTransaction();
            //Cria uma instância do modulo, grava e recupera o id.
            $idModulo = $nModulo->salvarModulo($nomeModulo);

            //Pega cria uma instancia da tabela moduloedital e associa o edital ao modulo cadastrado.
            $dados = array(
                'idEdital' => $idEdital,
                'idModulo' => $idModulo
            );

            $editalModulo->associarModuloEdital($dados);
            
            foreach ($tipoparticipacao as $tpart) {
                $dadosTipoParticipacao = array(
                        'idEdital'          => $idEdital,
                        'idTpParticipacao'  => $tipoparticipacao[0],
                        'qtParticipacao'    => $qtParticipacao
                );
                
                $modelEditalTipoParticipacao->associarModuloParticipacao($dadosTipoParticipacao);
            }
            
            $db->commit();
            parent::message('Módulo cadastrado com sucesso!', 'edital/modulosedital/idEdital/'. $idEdital, 'CONFIRM');
        } catch (Exception $exc) {
            $db->rollBack();
            parent::message('Erro ao cadastrar módulo', 'edital/modulosedital/idEdital/' . $idEdital, 'ERROR');
        }
    }

    public function salvaCategoriaAction()
    {

        //Recupera as informações: nome do modulo a ser cadastrado, e o id do Edital a ser associado.
        $idEdital       = $this->_request->getParam('idEdital');
        $idModulo       = $this->_request->getParam('idModulo');
        $nomeCategoria  = $this->_request->getParam('nomeCategoria');

        $modelCategoria = new tbCategoria();
 
        $db = Zend_Db_Table::getDefaultAdapter();

        try {
            $db->beginTransaction();
            $dados = array(
                'nmCategoria'   => $nomeCategoria,
                'idModulo'      => $idModulo
            );
            
            $idCategoria = $modelCategoria->inserirCategoria($dados);
           
            
            $db->commit();
            parent::message('Categoria cadastrada com sucesso!', 'edital/informacoes-categoria/idEdital/'.$idEdital.'/idModulo/'.$idModulo.'/idCategoria/'.$idCategoria, 'CONFIRM');
        } catch (Exception $exc) {
            $db->rollBack();
            parent::message('Erro ao cadastrar categoria', 'edital/informacoes-categoria/idEdital/'.$idEdital.'/idModulo/'.$idModulo, 'ERROR');
        }
    }
    
    public function atualizaInfoGeralCategoriaAction()
    {
             
         //Recupera as informações: nome do modulo a ser cadastrado, e o id do Edital a ser associado.
        $idEdital           = $this->_request->getParam('idEdital');
        $idModulo           = $this->_request->getParam('idModulo');
        $idCategoria        = $this->_request->getParam('idCategoria');
        $nomeCategoria      = $this->_request->getParam('nomeCategoria');
        
        $modelCategoria = new tbCategoria();

        $db = Zend_Db_Table::getDefaultAdapter();

        try {
            $db->beginTransaction();
            $dadoCategoria = array(
                'nmCategoria' => $nomeCategoria
            );
               
            $where['idCategoria = ?'] = $idCategoria;
            $where['idModulo = ?'] = $idModulo;
            $modelCategoria->atualizaCategoria($dadoCategoria, $where);

            $db->commit();
            parent::message('Informações gerais da categoria atualizado com sucesso!', 'edital/informacoes-categoria/idEdital/'.$idEdital.'/idModulo/'.$idModulo.'/idCategoria/'.$idCategoria, 'CONFIRM');
        } catch (Exception $exc) {
            $db->rollBack();
            parent::message('Erro ao atualizar as informações gerais da Categoria', 'edital/informacoes-categoria/idEdital/'.$idEdital.'/idModulo/'.$idModulo.'/idCategoria/'.$idCategoria, 'ERROR');
        }
    }
    
    public function salvarFluxoEditalAction()
    {

        //Recupera os parametros que vem da View
        $fluxoFilho = $this->_request->getParam('fluxoFilho');
        $tipoEdital = $this->_request->getParam('tipoEdital');
        $composicao = $this->_request->getParam('composicao');
        
        $auth = Zend_Auth::getInstance(); // instancia da autenticacao;
        
        // Models
        $edital     = new NovoEdital();
        $nEdital    = new tbEditalFluxo();
        $editalComposicao = new tbEditalComposicao();

        $db = Zend_Db_Table::getDefaultAdapter();

        
        try {
            $db->beginTransaction();
            // idEdital
            $arrEdital = array(
                'idTpEdital'    => $tipoEdital,
                'idTpPremiacao' => 1,
                'idUsuario'     => $auth->getIdentity()->usu_codigo
            );

            //Salva o Edital e recupera o ID
            $idEdital = $edital->inserir($arrEdital);

            //Salva o(s) fluxo(s) que o edital pode(m) ter
            foreach ($fluxoFilho as $ff) {
                $exp = explode(',', $ff);

                $idFluxo     = $exp[0];
                $idItemFluxo = $exp[1];

                $dados = array(
                    'idEdital'      => $idEdital,
                    'idFluxo'       => $idFluxo,
                    'idItemFluxo'   => $idItemFluxo
                );

                $nEdital->salvarFluxoEdital($dados);
            }

            //Salva a composição(ões) que o Edital pode(m) ter
            foreach ($composicao as $comp) {
                $dados = array(
                    'idEdital'      => $idEdital,
                    'idComposicao'  => $comp
                );

                $editalComposicao->salvarEditalComposicao($dados);
            }
        
            $db->commit();
            parent::message('Fluxo cadastrado com sucesso!', 'edital/informacao-geral/idEdital/'.$idEdital, 'CONFIRM');
        } catch (Exception $exc) {
            $db->rollBack();
            parent::message('Erro ao cadastrar o fluxo', 'edital/index', 'ERROR');
        }
    }

    /**
     *
     */
    public function salvarGuia()
    {
        xd($this->getRequest()->getParams());
    }

    public function salvarInformacaoGeralAction()
    {

        //Recupera os parametros que vem da View
        $idEdital           = $this->_request->getParam('idEdital');
        $nmEdital           = $this->_request->getParam('nmEdital');
        $nrEdital           = $this->_request->getParam('nrEdital');
        $dtInicioEdital     = $this->_request->getParam('dtInicioElaboracao');
        $dtFimEdital        = $this->_request->getParam('dtFimElaboracao');
        $dtInicioRealizacao = $this->_request->getParam('dtInicioRealizacao');
        $dtFimRealizacao    = $this->_request->getParam('dtFimRealizacao');
        $origemRecurso      = $this->_request->getParam('origemRecurso');
        $tipoPremiacao      = $this->_request->getParam('tipoPremiacao');
        $qtAvaliadores      = $this->_request->getParam('quantidadeAvaliadores');
        $segmento           = $this->_request->getParam('segmento');
        $tipoparticipacao   = $this->_request->getParam('tipoParticipacaoEdital');
        $qtTipoParticipacao = $this->_request->getParam('qtdTipoParticipacaoEdital');

        $dtInicioEditalInvertida        = Data::dataAmericana($dtInicioEdital);
        $dtFimEditalInvertida           = Data::dataAmericana($dtFimEdital);
        
        $nEdital                        = new NovoEdital();
        $modelEditalTipoParticipacao    = new tbEditalTipoParticipacao();
        $tbEditalAreaSegmento           = new tbEditalAreaSegmento();
        
        $db = Zend_Db_Table::getDefaultAdapter();

        
        try {
            $db->beginTransaction();
            $dados = array(
                'nmEdital'              => $nmEdital,
                'nrEdital'              => $nrEdital,
                'dtInicioEdital'        => $dtInicioEditalInvertida,
                'dtFimEdital'           => $dtFimEditalInvertida,
                'dtInicioRealizacao'    => $dtInicioRealizacao == '' ? new Zend_Db_Expr('NULL') : Data::dataAmericana($dtInicioRealizacao),
                'dtFimRealizacao'       => $dtFimRealizacao == '' ? new Zend_Db_Expr('NULL') : Data::dataAmericana($dtFimRealizacao),
                'qtAvaliadores'         => $qtAvaliadores,
                'idOrigemRecurso'       => $origemRecurso == '0' ? new Zend_Db_Expr('NULL') : $origemRecurso,
                'idTpPremiacao'         => $tipoPremiacao
            );


            $where = array('idEdital = ?' => $idEdital);
            //Atualizando informações na tabela tbEdital
            $nEdital->salvardadosgerais($dados, $where);

            
            
            // Excluir todos com idEdital = $idEdital
            $tbEditalAreaSegmento->delete(array('idEdital = ?' => $idEdital));
            
            // Pega os dados de segmento
            foreach ($segmento as $as) {
                $idArea = substr($as, 0, 1);
                $idSegmento = $as;

                $dados = array(
                    'idEdital'   => $idEdital,
                    'idArea'     => $idArea,
                    'idSegmento' => $idSegmento
                );
                
                x($dados);
                // salva na nova tabela nova tbEditalAreaSegmento
                $tbEditalAreaSegmento->inserir($dados);
            }
            
            if (isset($tipoparticipacao)) {
                $modelEditalTipoParticipacao->delete(array('idEdital = ?' => $idEdital));
                
                foreach ($tipoparticipacao as $key => $tpart) {
                    $dadosTipoParticipacao = array(
                        'idEdital'          => $idEdital,
                        'idTpParticipacao'  => $tpart,
                        'qtParticipacao'    => $qtTipoParticipacao[$tpart-1] //Array de Tipos de Participação começa com 0. Array de Qntd começa com 1. Por isso a diminuição do valor para pegar o valor correspondente.
                    );
                    
                    $modelEditalTipoParticipacao->associarEditalTipoParticipacao($dadosTipoParticipacao);
                }
            }
            
            $db->commit();
            
            if (isset($_POST['salvarDados']) && !empty($_POST['salvarDados'])) {
                parent::message('Dados salvos com sucesso!', 'edital/informacao-geral/idEdital/' . $idEdital, 'CONFIRM');
            }
            
            parent::message('Informações gerais do Edital com sucesso!', 'edital/criterios-avaliacao/idEdital/' . $idEdital, 'CONFIRM');
        } catch (Exception $exc) {
            $db->rollBack();
            xd($exc->getMessage());
            parent::message('Erro ao cadastrar as Informações gerais do Edital', 'edital/informacao-geral/idEdital/'.$idEdital, 'ERROR');
        }
    }
    
    public function criteriosAvaliacaoAction()
    {
        $idEdital = $this->_request->getParam('idEdital');
        $this->view->idEdital = $idEdital;

        $tipopremiacao = new TipoPremiacao();
        $tipoPremiacaoEdital = $tipopremiacao->buscarTipoPremiacao();
        $this->view->tipopremiacao = $tipoPremiacaoEdital;

        $origemrecurso = new Verificacao();
        $origRecurso = $origemrecurso->buscarOrigemRecurso();
        $this->view->origemrecursoedital = $origRecurso;

        $areaEdital = new Area();
        $area = $areaEdital->BuscarAreas();
        $this->view->area = $area;

        $segmentoArea = new Segmento();
        $segmento = $segmentoArea->buscaSegmentos();
        $this->view->segmento = $segmento;

        $criterioAvaliacao = new tbCriteriosAvaliacao();
        $critAvaliacao = $criterioAvaliacao->buscarcriterioporidEdital($idEdital);
        $this->view->criterioavaliacao = $critAvaliacao;
        $this->view->qtdCriterioAvaliacao = count($critAvaliacao);
    }

    public function salvarcriteriodeavaliacaoAction()
    {

        //Recupera os parametros que vem da View
        $idCriterioAvaliacao    = $this->_request->getParam('idCriterioAvaliacao');
        $idEdital               = $this->_request->getParam('idEdital');
        $nmCriterio             = $this->_request->getParam('nomeCriterio');
        $stDesempate            = $this->_request->getParam('desempate');
        $orDesempate            = $this->_request->getParam('ordemDesempate');
        $psAvaliacao            = $this->_request->getParam('pesoCriterioAvaliacao');
        $ntInicialAvaliacao     = $this->_request->getParam('notaInicialCriterioAvaliacao');
        $ntFinalAvaliacao       = $this->_request->getParam('notaFinalCriterioAvaliacao');
        $varNotaAvaliacao       = $this->_request->getParam('variacaoNotaCriterioAvaliacao');
        $txtCriterio            = $this->_request->getParam('txtCriterio');

        $nStDesempate = 0;

        if ($stDesempate == 'on') {
            $nStDesempate = 1;
        } else {
            $nStDesempate = 0;
        }

        $dados = array(
            'idEdital'              => $idEdital,
            'nmCriterioAvaliacao'   => $nmCriterio,
            'stDesempate'           => $nStDesempate,
            'orDesempate'           => $orDesempate,
            'nrPeso'                => str_replace(",", ".", $psAvaliacao),
            'notaInicio'            => str_replace(",", ".", $ntInicialAvaliacao),
            'notaFim'               => str_replace(",", ".", $ntFinalAvaliacao),
            'varNota'               => str_replace(",", ".", $varNotaAvaliacao),
            'txCriterioAvaliacao'   => $txtCriterio
        );


        $nEdital = new tbCriteriosAvaliacao();
        
        
        $db = Zend_Db_Table::getDefaultAdapter();

        $msg = '';
        try {
            $db->beginTransaction();
            if ($idCriterioAvaliacao != '') {
                $nEdital->update($dados, array('idCriterioAvaliacao = ?' => $idCriterioAvaliacao));
                $msg = 'Critério de avaliação atualizado com sucesso!';
            } else {
                $nEdital->salvarcriterioavaliacao($dados);
                $msg = 'Critério de avaliação cadastrado com sucesso!';
            }
            //Salva o Edital e recupera o ID

            $db->commit();
            parent::message($msg, 'edital/criterios-avaliacao/idEdital/'.$idEdital, 'CONFIRM');
        } catch (Exception $exc) {
            $db->rollBack();
            parent::message('Erro ao cadastrar o fluxo', 'edital/criterios-avaliacao/idEdital/'.$idEdital, 'ERROR');
        }
    }

    public function deletarCriterioAvaliacaoAction()
    {
        $idCriterio = $this->_request->getParam('id');
        $idEdital   = $this->_request->getParam('idEdital');
        
        $criterioAvaliacao = new tbCriteriosAvaliacao();
        if (($idCriterio == '') || ($idEdital == '')) {
            parent::message('Critério de avaliação excluído com sucesso!', 'edital/criterios-avaliacao/idEdital/'.$idEdital, 'ERROR');
        }
        
        $db = Zend_Db_Table::getDefaultAdapter();

        
        $where = array('idCriterioAvaliacao = ?' => $idCriterio);
        
        try {
            $db->beginTransaction();
            //deleta o criterio de avaliação
            $criterioAvaliacao->delete($where);

            $db->commit();
            parent::message('Crit&eacute;rio de avaliação excluído com sucesso!', 'edital/criterios-avaliacao/idEdital/'.$idEdital, 'CONFIRM');
        } catch (Exception $exc) {
            $db->rollBack();
            parent::message('Erro ao excluir o critério de avaliação', 'edital/criterios-avaliacao/idEdital/'.$idEdital, 'ERROR');
        }
    }
    
    public function buscaCriterioAjaxAction()
    {
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);

        $criterioAvaliacao = new tbCriteriosAvaliacao();
        $retorno    = array();
        $idCriterio = $this->getRequest()->getParam('idCriterio');
        try {
            if (!$idCriterio) {
                $retorno['error'] = utf8_encode('Error');
            } else {

                // Busca a PJ por CNPJ
                $criterio = $criterioAvaliacao->buscarCriteriosAvaliacao(array('idCriterioAvaliacao = ?' => $idCriterio));

                if (count($criterio) == 0) {
                    $retorno['error'] = utf8_encode('Critério não encontrado!');
                } else {
                    $retorno['dados']['idCriterioAvaliacao']    = $criterio[0]['idCriterioAvaliacao'];
                    $retorno['dados']['idEdital']               = $criterio[0]['idEdital'];
                    $retorno['dados']['nrPeso']                 = $criterio[0]['nrPeso'];
                    $retorno['dados']['notaInicio']             = $criterio[0]['notaInicio'];
                    $retorno['dados']['notaFim']                = $criterio[0]['notaFim'];
                    $retorno['dados']['varNota']                = $criterio[0]['varNota'];
                    $retorno['dados']['txCriterioAvaliacao']    = utf8_encode($criterio[0]['txCriterioAvaliacao']);
                    $retorno['dados']['nmCriterioAvaliacao']    = utf8_encode($criterio[0]['nmCriterioAvaliacao']);
                    $retorno['dados']['stDesempate']            = $criterio[0]['stDesempate'];
                    $retorno['dados']['orDesempate']            = $criterio[0]['orDesempate'];
                    $retorno['error'] = '';
                }
            }
        } catch (Exception $exc) {
            $retorno['error'] = $exc->getTraceAsString();
        }

        $this->_helper->json($retorno);
    }
    
    public function salvarPlanilhaOrcamentariaAction()
    {

        
        
        //Recupera os parametros que vem da View
        $idEdital                   = $this->_request->getParam('idEdital');
        $idPlanOrcEdital            = $this->_request->getParam('idPlanOrcEdital');
        $idCategoria                = $this->_request->getParam('idCategoria');
        $etapaEdital                = $this->_request->getParam('etapaEdital');
        $itemEdital                 = $this->_request->getParam('itemEdital');
        $qtdItemEdital              = $this->_request->getParam('qtdItemEdital');
        $ocorrenciaItemEdital       = $this->_request->getParam('ocorrenciaItemEdital');
        $valorUnitarioItemEdital    = $this->_request->getParam('valorUnitarioItemEdital');
        $dsOutro                    = $this->_request->getParam('dsOutro');
        
        
        
        $modelPlanilhaOrcamentaria      = new tbPlanilhaOrcamentaria();
        $modelPlanilhaItemPlanilhaEtapa = new tbPlanilhaItemPlanilhaEtapa();

        $dadosPlanilhaOrcamentaria = array(
            'idCategoria'   => $idCategoria
        );
            
        $db = Zend_Db_Table::getDefaultAdapter();

        $msg = '';
        try {
            $db->beginTransaction();
            $idPlnalinhaOrcamentaria = $modelPlanilhaOrcamentaria->inserir($dadosPlanilhaOrcamentaria);
            
            
            $dadosPlanilhaItemPlanilhaEtapa = array(
            
                 
                'idPlanilhaItem'        => 1,
                'idPlanilhaEtapa'       => 1,
                'dsPlanilhaEtapa'       => $etapaEdital,
                'dsPlanilhaItem'        => $itemEdital,
                'idPlanOrcEdital'       => 17,
                'dsOutro'               => $dsOutro,
                'qtItemEdital'          => $qtdItemEdital,
                'qtOcorrencia'          => $ocorrenciaItemEdital,
                'vlrUnitario'           => $valorUnitarioItemEdital
            
            );
                
            $modelPlanilhaItemPlanilhaEtapa->salvarPlanilhaEtapaPlanilhaItem($dadosPlanilhaItemPlanilhaEtapa);
            $msg = 'Etapa/Item cadastrado à planilha orçamentária com sucesso!';
//            }
            
            //Salva o Edital e recupera o ID
            $db->commit();
            parent::message('Etapa/Item cadastrado à planilha orçamentária com sucesso', 'edital/modulosedital/idEdital/'.$idEdital, 'CONFIRM');
        } catch (Exception $exc) {
            $db->rollBack();
            xd($exc);
            parent::message('Erro ao cadastrar a etapa/item à planilha orçamentária.', 'edital/modulosedital/idEdital/'.$idEdital, 'ERROR');
        }
    }
    
    public function buscaPlanilhaOrcamentariaAjaxAction()
    {
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        
        $modelPlanilhaItemPlanilhaEtapa = new tbPlanilhaItemPlanilhaEtapa();
        
        $retorno        = array();
        $idPlanOrcEdital = $this->getRequest()->getParam('idPlanOrcEdital');
        
        try {
            if (!$idPlanOrcEdital) {
                $retorno['error'] = utf8_encode('Error');
            } else {
                $planilhaOrcamentaria = $modelPlanilhaItemPlanilhaEtapa->buscarPlanilhaOrcamentaria(array('idPlanOrcEdital = ?' => $idPlanOrcEdital));
                
                if (count($planilhaOrcamentaria) == 0) {
                    $retorno['error'] = utf8_encode('Ítem/Etapa não encontrados!');
                } else {
                    $retorno['dados']['dsOutro']        = $planilhaOrcamentaria[0]['dsOutro'];
                    $retorno['dados']['qtUnidade']      = $planilhaOrcamentaria[0]['qtUnidade'];
                    $retorno['dados']['qtOcorrencia']   = $planilhaOrcamentaria[0]['qtOcorrencia'];
                    $retorno['dados']['vlrUnitario']    = $planilhaOrcamentaria[0]['vlrUnitario'];
                }
            }
        } catch (Exception $exc) {
            $retorno['error'] = $exc->getTraceAsString();
        }

        $this->_helper->json($retorno);
    }
}
