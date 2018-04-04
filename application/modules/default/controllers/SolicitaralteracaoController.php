<?php
/**
 * SolicitarAlteracaoController
 */

class SolicitarAlteracaoController extends MinC_Controller_Action_Abstract
{
    public function init()
    {
        parent::perfil(2); // perfil scriptcase
        parent::init(); // chama o init() do pai GenericControllerNew
    } // fecha m�todo init()



    public function indexAction()
    {
    }



    public function projetosAction()
    {
        $CgcCpf = $_POST['CgcCpf'];
        if (!empty($CgcCpf)) {
            $CgcCpf = str_replace(".", "", $CgcCpf);
            $CgcCpf = str_replace("-", "", $CgcCpf);
            $CgcCpf = str_replace("/", "", $CgcCpf);
            $Projetos = new SolicitarAlteracaoDAO();
            $resultado = $Projetos->buscarProjetos($CgcCpf);
            if (!empty($resultado)) {
                $this->view->projetos = $resultado;
            } else {
                parent::message(" Nenhum Projeto Encontrado", "solicitaralteracao/index", "ERROR");
            }
        } else {
            parent::message(" Dados Obrigat�rios N�o Informados", "solicitaralteracao/index", "ERROR");
        }
    } // fecha m�todo projetosAction()



    public function acaoprojetoAction()
    {
        $buscaSoliciatacao = new ReadequacaoProjetos();
        $Projetos = new SolicitarAlteracaoDAO();
        $buscaProjetoProduto = new SolicitarReadequacaoCustoDAO();

        $idPronac = $_GET['idpronac'];
        $auth = Zend_Auth::getInstance();
        $idSolicitante = $auth->getIdentity()->IdUsuario;
        $resultado = $buscaSoliciatacao->buscarProjetos($idPronac);
        $this->view->buscaprojeto = $resultado;
        $valores = $buscaSoliciatacao->buscarSolicitacao($idPronac);
        $idPedidoAlteracao = $valores[0]->idPedidoAlteracao;
        $verificaPlanilhaCusto = $buscaProjetoProduto->buscarProdutoAprovacao($idPronac);
        $this->view->buscaPlanilhaCusto = $verificaPlanilhaCusto;
        
        $status = 1;
     
        if (!empty($idPedidoAlteracao)) {
            $resultadoPedidoAlteracao = $Projetos->buscartbPedidoAlteracaoXTipoAlteracao($idPedidoAlteracao, 1);
            $resultadoPedidoAlteracao2 = $Projetos->buscartbPedidoAlteracaoXTipoAlteracao($idPedidoAlteracao, 2);
            $buscatbProposta = $Projetos->buscaNomeProponente($idPedidoAlteracao);
            $buscatbProposta2 = $Projetos->buscarRazaoSocial($idPedidoAlteracao);
            if (!empty($resultadoPedidoAlteracao)) {
                $justicativa = $resultadoPedidoAlteracao[0]->dsJustificativa;
                $this->view->justificativa = $justicativa;
            }
            if (!empty($resultadoPedidoAlteracao2)) {
                $justicativa2 = $resultadoPedidoAlteracao2[0]->dsJustificativa;
                $this->view->justificativa2 = $justicativa2;
            }
            if (!empty($buscatbProposta)) {
                $nomedoProjeto = $buscatbProposta[0]->nmProponente;
                $this->view->buscanome = $nomedoProjeto;
            }
            if (!empty($buscatbProposta2)) {
                $nomedoProjeto2 = $buscatbProposta2[0]->nmProponente;
                $this->view->buscanome2 = $nomedoProjeto2;
            }
        }
    } // fecha m�todo acaoprojetoAction()



    public function nomeproponente2Action()
    {
        if (!empty($_POST)) {
            $stPedido = 'A';
            $nomedoprojeto = $_POST["nomeprojeto"];
            $CPFCNPJ = Mascara::delMaskCPF(Mascara::delMaskCNPJ($_POST["CPFCNPJ"]));
            $CpfCnpjAtual = $_POST["CpfCnpjAtual"];
            
            if ($CpfCnpjAtual == $CPFCNPJ) {
                $status = '1';
            } else {
                $status = '2';
            }

            $idPronac = $_POST["idpronac"];
            $auth = Zend_Auth::getInstance();
            $idSolicitante = $auth->getIdentity()->IdUsuario;
            $dsJustificativa = $_POST["recurso1"];
            $buscaSoliciatacao = new ReadequacaoProjetos();
            $Projetos = new SolicitarAlteracaoDAO();
            $valores = $buscaSoliciatacao->buscarSolicitacao($idPronac);
            $idPedidoAlteracao = $valores[0]->idPedidoAlteracao;
            $coluna = 'nmProjeto';
            if (empty($idPedidoAlteracao)) {
                $inserirSolitacao = $buscaSoliciatacao->inserirSolicitacao($idPronac, $idSolicitante, $stPedido);
                $valores = $buscaSoliciatacao->buscarSolicitacao($idPronac);
                $idPedidoAlteracao = $valores[0]->idPedidoAlteracao;
                $resultadoPedidoAlteracao = $Projetos->buscartbPedidoAlteracaoXTipoAlteracao($idPedidoAlteracao, $status);
                if (empty($resultadoPedidoAlteracao)) {
                    $buscatbProposta = $Projetos->buscarNomeProposta($idPedidoAlteracao);
                    if (empty($buscatbProposta)) {
                        $inserirtbAlteracaoNomeProponente = $Projetos->insertNomeProponente($idPedidoAlteracao, $CPFCNPJ, $nomedoprojeto);
                        $inserirtbProposta = $Projetos->inserttbProposta($idPedidoAlteracao, $coluna, $nomedoprojeto);
                    } else {
                        $updatetbAlteracaoNomeProponente = $Projetos->updateNomeProponente($idPedidoAlteracao, $CPFCNPJ, $nomedoprojeto);
                        $updatetbProposta = $Projetos->updatetbProposta($idPedidoAlteracao, $coluna, $nomedoprojeto);
                    }

                    $justificativa =  $buscaSoliciatacao->inserirJustificativa($idPedidoAlteracao, $dsJustificativa, $status);
                    SolicitarAlteracaoController::cadastrarArquivosMult($_FILES, $idPedidoAlteracao, $status);
                    $enviarsolicitacao = $buscaSoliciatacao->alterarSolicitacao($idPedidoAlteracao, $stPedido);
                    parent::message("Solicita��o enviada com sucesso!", "solicitaralteracao/acaoprojeto?idpronac=$idPronac", "CONFIRM");
                } else {
                    $buscatbProposta = $Projetos->buscatbProposta($idPedidoAlteracao);
                    if (empty($buscatbProposta)) {
                        $inserirtbAlteracaoNomeProponente = $Projetos->insertNomeProponente($idPedidoAlteracao, $CPFCNPJ, $nomedoprojeto);
                        $inserirtbProposta = $Projetos->inserttbProposta($idPedidoAlteracao, $coluna, $nomedoprojeto);
                    } else {
                        $updatetbAlteracaoNomeProponente = $Projetos->updateNomeProponente($idPedidoAlteracao, $CPFCNPJ, $nomedoprojeto);
                        $updatetbProposta = $Projetos->updatetbProposta($idPedidoAlteracao, $coluna, $nomedoprojeto);
                    }
                    $justificativa =  $Projetos->updatetbPedidoAlteracaoXTipoAlteracao($idPedidoAlteracao, $dsJustificativa, $status);
                    $enviarsolicitacao = $buscaSoliciatacao->alterarSolicitacao($idPedidoAlteracao, $stPedido);
                    SolicitarAlteracaoController::cadastrarArquivosMult($_FILES, $idPedidoAlteracao, $status);
                    parent::message("Solicita��o enviada com sucesso!", "solicitaralteracao/acaoprojeto?idpronac=$idPronac", "CONFIRM");
                }
            } else {
                $buscatbProposta = $Projetos->buscatbProposta($idPedidoAlteracao);
                if (empty($buscatbProposta)) {
                    $inserirtbProposta = $Projetos->inserttbProposta($idPedidoAlteracao, $coluna, $nomedoprojeto);
                } else {
                    $updatetbProposta = $Projetos->updatetbProposta($idPedidoAlteracao, $coluna, $nomedoprojeto);
                }
                $resultadoPedidoAlteracao = $Projetos->buscartbPedidoAlteracaoXTipoAlteracao($idPedidoAlteracao, $status);
                if (empty($resultadoPedidoAlteracao)) {
                    $justificativa =  $buscaSoliciatacao->inserirJustificativa($idPedidoAlteracao, $dsJustificativa, $status);
                    $enviarsolicitacao = $buscaSoliciatacao->alterarSolicitacao($idPedidoAlteracao, $stPedido);
                    SolicitarAlteracaoController::cadastrarArquivosMult($_FILES, $idPedidoAlteracao, $status);
                    parent::message("Solicita��o enviada com sucesso!", "solicitaralteracao/acaoprojeto?idpronac=$idPronac", "CONFIRM");
                } else {
                    $justificativa =  $Projetos->updatetbPedidoAlteracaoXTipoAlteracao($idPedidoAlteracao, $dsJustificativa, $status);
                    $enviarsolicitacao = $buscaSoliciatacao->alterarSolicitacao($idPedidoAlteracao, $stPedido);
                    SolicitarAlteracaoController::cadastrarArquivosMult($_FILES, $idPedidoAlteracao, $status);
                    parent::message("Solicita��o enviada com sucesso!", "solicitaralteracao/acaoprojeto?idpronac=$idPronac", "CONFIRM");
                }
            }
        } else {
            parent::message(" Dados Obrigat�rios N�o Informados", "solicitaralteracao/acaoprojeto", "ERROR");
        }
    } // fecha m�todo nomeproponente2Action()



    public function nomeproponenteAction()
    {
        if (!empty($_POST)) {
            $Proponente = $_POST["nomeprojeto"];
            $idPronac = $_POST["idpronac"];
            $CPFCNPJ = Mascara::delMaskCPF(Mascara::delMaskCNPJ($_POST["CPFCNPJ"]));
            $CpfCnpjAtual = $_POST["CpfCnpjAtual"];
            $stPedido = 'T';

            if ($CpfCnpjAtual == $CPFCNPJ) {
                $status = '1';
            } else {
                $status = '2';
            }
           
            // recebe todo(s) arquivo(s) enviado(s)
            
            $auth = Zend_Auth::getInstance();
            $idSolicitante = $auth->getIdentity()->IdUsuario;
            $dsJustificativa = $_POST["recurso1"];
            $buscaSoliciatacao = new ReadequacaoProjetos();
            $Projetos = new SolicitarAlteracaoDAO();
            $valores = $buscaSoliciatacao->buscarSolicitacao($idPronac);
            $idPedidoAlteracao = $valores[0]->idPedidoAlteracao;
              
            if (empty($idPedidoAlteracao)) {
                $inserirSolitacao = $buscaSoliciatacao->inserirSolicitacao($idPronac, $idSolicitante, $stPedido);
                $valores = $buscaSoliciatacao->buscarSolicitacao($idPronac);
                $idPedidoAlteracao = $valores[0]->idPedidoAlteracao;
                $resultadoPedidoAlteracao = $Projetos->buscartbPedidoAlteracaoXTipoAlteracao($idPedidoAlteracao, $status);
               
                if (empty($resultadoPedidoAlteracao)) {
                    $buscatbProposta = $Projetos->buscaNomeProponente($idPedidoAlteracao);
                    if (empty($buscatbProposta)) {
                        $idPedidoAlteracao = $valores[0]->idPedidoAlteracao;
                        $inserirtbProposta = $Projetos->insertNomeProponente($idPedidoAlteracao, $CPFCNPJ, $Proponente);
                    } else {
                        $idPedidoAlteracao = $valores[0]->idPedidoAlteracao;
                        $updatetbProposta = $Projetos->updateNomeProponente($idPedidoAlteracao, $CPFCNPJ, $Proponente);
                    }

                    $justificativa =  $buscaSoliciatacao->inserirJustificativa($idPedidoAlteracao, $dsJustificativa, $status);
                    SolicitarAlteracaoController::cadastrarArquivosMult($_FILES, $idPedidoAlteracao, $status);
                    parent::message("Solicita��o enviada com sucesso!", "solicitaralteracao/acaoprojeto?idpronac=$idPronac", "CONFIRM");
                } else {
                    $idPedidoAlteracao = $valores[0]->idPedidoAlteracao;
                    $buscatbProposta = $Projetos->buscaNomeProponente($idPedidoAlteracao);
                    if (empty($buscatbProposta)) {
                        $idPedidoAlteracao = $valores[0]->idPedidoAlteracao;
                        $inserirtbProposta = $Projetos->insertNomeProponente($idPedidoAlteracao, $CPFCNPJ, $Proponente);
                    } else {
                        $updatetbProposta = $Projetos->updateNomeProponente($idPedidoAlteracao, $CPFCNPJ, $Proponente);
                    }
                    $justificativa =  $Projetos->updatetbPedidoAlteracaoXTipoAlteracao($idPedidoAlteracao, $dsJustificativa, $status);
                    SolicitarAlteracaoController::cadastrarArquivosMult($_FILES, $idPedidoAlteracao, $status);
                    parent::message("Solicita��o enviada com sucesso!", "solicitaralteracao/acaoprojeto?idpronac=$idPronac", "CONFIRM");
                }
            } else {
                $buscatbProposta = $Projetos->buscaNomeProponente($idPedidoAlteracao);
                if (empty($buscatbProposta)) {
                    $inserirtbProposta = $Projetos->insertNomeProponente($idPedidoAlteracao, $CPFCNPJ, $Proponente);
                } else {
                    $updatetbProposta = $Projetos->updateNomeProponente($idPedidoAlteracao, $CPFCNPJ, $Proponente);
                }
                $resultadoPedidoAlteracao = $Projetos->buscartbPedidoAlteracaoXTipoAlteracao($idPedidoAlteracao, $status);
                if (empty($resultadoPedidoAlteracao)) {
                    $justificativa =  $buscaSoliciatacao->inserirJustificativa($idPedidoAlteracao, $dsJustificativa, $status);
                    SolicitarAlteracaoController::cadastrarArquivosMult($_FILES, $idPedidoAlteracao, $status);
                    parent::message("Solicita��o enviada com sucesso!", "solicitaralteracao/acaoprojeto?idpronac=$idPronac", "CONFIRM");
                } else {
                    $justificativa =  $Projetos->updatetbPedidoAlteracaoXTipoAlteracao($idPedidoAlteracao, $dsJustificativa, $status);
                    SolicitarAlteracaoController::cadastrarArquivosMult($_FILES, $idPedidoAlteracao, $status);
                    parent::message("Solicita��o enviada com sucesso!", "solicitaralteracao/acaoprojeto?idpronac=$idPronac", "CONFIRM");
                }
            }
        } else {
            parent::message(" Dados Obrigat�rios N�o Informados", "solicitaralteracao/acaoprojeto", "ERROR");
        }
    } // fecha m�todo nomeproponenteAction()



    public function nomeprojeto2Action()
    {
        if (!empty($_POST)) {
            $stPedido = 'A';
            $razaosocial = $_POST["razaosocial"];
            $CpfCnpjAtual = $_POST["CpfCnpjAtual"];
            $CPFCNPJ = Mascara::delMaskCPF(Mascara::delMaskCNPJ($_POST["CPFCNPJ"]));
            $idPronac = $_POST["idpronac"];
            $auth = Zend_Auth::getInstance();
            $idSolicitante = $auth->getIdentity()->IdUsuario;
            $dsJustificativa = $_POST["recurso2"];
            $buscaSoliciatacao = new ReadequacaoProjetos();
            $Projetos = new SolicitarAlteracaoDAO();
            $valores = $buscaSoliciatacao->buscarSolicitacao($idPronac);
            $idPedidoAlteracao = $valores[0]->idPedidoAlteracao;
            
            if ($CpfCnpjAtual == $CPFCNPJ) {
                $status = '1';
            } else {
                $status = '2';
            }

            if (empty($idPedidoAlteracao)) {
                $inserirSolitacao = $buscaSoliciatacao->inserirSolicitacao($idPronac, $idSolicitante, $stPedido);
                $valores = $buscaSoliciatacao->buscarSolicitacao($idPronac);
                $idPedidoAlteracao = $valores[0]->idPedidoAlteracao;
                $resultadoPedidoAlteracao = $Projetos->buscartbPedidoAlteracaoXTipoAlteracao($idPedidoAlteracao, $status);
                if (empty($resultadoPedidoAlteracao)) {
                    $buscatbProposta = $Projetos->buscarRazaoSocial($idPedidoAlteracao);
                    
                    // caso o usu�rio n�o esteja cadastrado, vai para o manter agentes
                    if (!SolicitarAlteracaoDAO::verificarInteressadosAgentes($CPFCNPJ)) {
                        $this->_redirect("/agente/manteragentes/agentes?acao=cc&cpf=" . $CPFCNPJ);
                    }
                    
                    if (empty($buscatbProposta)) {
                        $inserirtbProposta = $Projetos->insertRazaoSocial($idPedidoAlteracao, $CPFCNPJ, $razaosocial);
                    } else {
                        $updatetbProposta = $Projetos->updateRazaoSocial($idPedidoAlteracao, $CPFCNPJ, $razaosocial);
                    }

                    $justificativa =  $buscaSoliciatacao->inserirJustificativa($idPedidoAlteracao, $dsJustificativa, $status);
                    $enviarsolicitacao = $buscaSoliciatacao->alterarSolicitacao($idPedidoAlteracao, $stPedido);
                    SolicitarAlteracaoController::cadastrarArquivosMult($_FILES, $idPedidoAlteracao, $status);
                    parent::message("Solicita��o enviada com sucesso!", "solicitaralteracao/acaoprojeto?idpronac=$idPronac", "CONFIRM");
                } else {
                    $buscatbProposta = $Projetos->buscarRazaoSocial($idPedidoAlteracao);

                    // caso o usu�rio n�o esteja cadastrado, vai para o manter agentes
                    if (!SolicitarAlteracaoDAO::verificarInteressadosAgentes($CPFCNPJ)) {
                        $this->_redirect("/agente/manteragentes/agentes?acao=cc&cpf=" . $CPFCNPJ);
                    }

                    if (empty($buscatbProposta)) {
                        $inserirtbProposta = $Projetos->insertRazaoSocial($idPedidoAlteracao, $CPFCNPJ, $razaosocial);
                    } else {
                        $updatetbProposta = $Projetos->updateRazaoSocial($idPedidoAlteracao, $CPFCNPJ, $razaosocial);
                    }
                    $justificativa =  $Projetos->updatetbPedidoAlteracaoXTipoAlteracao($idPedidoAlteracao, $dsJustificativa, $status);
                    $enviarsolicitacao = $buscaSoliciatacao->alterarSolicitacao($idPedidoAlteracao, $stPedido);
                    SolicitarAlteracaoController::cadastrarArquivosMult($_FILES, $idPedidoAlteracao, $status);
                    parent::message("Solicita��o enviada com sucesso!", "solicitaralteracao/acaoprojeto?idpronac=$idPronac", "CONFIRM");
                }
            } else {
                $buscatbProposta = $Projetos->buscarRazaoSocial($idPedidoAlteracao);

                // caso o usu�rio n�o esteja cadastrado, vai para o manter agentes
                if (!SolicitarAlteracaoDAO::verificarInteressadosAgentes($CPFCNPJ)) {
                    $this->_redirect("/agente/manteragentes/agentes?acao=cc&cpf=" . $CPFCNPJ);
                }

                if (empty($buscatbProposta)) {
                    $inserirtbProposta = $Projetos->insertRazaoSocial($idPedidoAlteracao, $CPFCNPJ, $razaosocial);
                } else {
                    $updatetbProposta = $Projetos->updateRazaoSocial($idPedidoAlteracao, $CPFCNPJ, $razaosocial);
                }
                $resultadoPedidoAlteracao = $Projetos->buscartbPedidoAlteracaoXTipoAlteracao($idPedidoAlteracao, $status);
                if (empty($resultadoPedidoAlteracao)) {
                    $justificativa =  $buscaSoliciatacao->inserirJustificativa($idPedidoAlteracao, $dsJustificativa, $status);
                    $enviarsolicitacao = $buscaSoliciatacao->alterarSolicitacao($idPedidoAlteracao, $stPedido);
                    SolicitarAlteracaoController::cadastrarArquivosMult($_FILES, $idPedidoAlteracao, $status);
                    parent::message("Solicita��o enviada com sucesso!", "solicitaralteracao/acaoprojeto?idpronac=$idPronac", "CONFIRM");
                } else {
                    $justificativa =  $Projetos->updatetbPedidoAlteracaoXTipoAlteracao($idPedidoAlteracao, $dsJustificativa, $status);
                    $enviarsolicitacao = $buscaSoliciatacao->alterarSolicitacao($idPedidoAlteracao, $stPedido);
                    SolicitarAlteracaoController::cadastrarArquivosMult($_FILES, $idPedidoAlteracao, $status);
                    parent::message("Solicita��o enviada com sucesso!", "solicitaralteracao/acaoprojeto?idpronac=$idPronac", "CONFIRM");
                }
            }
        } else {
            parent::message(" Dados Obrigat�rios N�o Informados", "solicitaralteracao/acaoprojeto", "ERROR");
        }
    } // fecha m�todo nomeprojeto2Action()



    public function nomeprojetoAction()
    {
        if (!empty($_POST)) {
            $stPedido = 'T';
            $razaosocial = $_POST["razaosocial"];
            $CpfCnpjAtual = $_POST["CpfCnpjAtual"];
            $CPFCNPJ = Mascara::delMaskCPF(Mascara::delMaskCNPJ($_POST["CPFCNPJ"]));
            $idPronac = $_POST["idpronac"];
            $auth = Zend_Auth::getInstance();
            $idSolicitante = $auth->getIdentity()->IdUsuario;
            $dsJustificativa = $_POST["recurso2"];
            $buscaSoliciatacao = new ReadequacaoProjetos();
            $Projetos = new SolicitarAlteracaoDAO();
            $valores = $buscaSoliciatacao->buscarSolicitacao($idPronac);
            $idPedidoAlteracao = $valores[0]->idPedidoAlteracao;

            if ($CpfCnpjAtual == $CPFCNPJ) {
                $status = '1';
            } else {
                $status = '2';
            }
  
            if (empty($idPedidoAlteracao)) {
                $inserirSolitacao = $buscaSoliciatacao->inserirSolicitacao($idPronac, $idSolicitante, $stPedido);
                $valores = $buscaSoliciatacao->buscarSolicitacao($idPronac);
                $idPedidoAlteracao = $valores[0]->idPedidoAlteracao;
                $idPedidoAlteracao = $valores[0]->idPedidoAlteracao;
                $resultadoPedidoAlteracao = $Projetos->buscartbPedidoAlteracaoXTipoAlteracao($idPedidoAlteracao, $status);
                if (empty($resultadoPedidoAlteracao)) {
                    $buscatbProposta = $Projetos->buscarRazaoSocial($idPedidoAlteracao);

                    // caso o usu�rio n�o esteja cadastrado, vai para o manter agentes
                    if (!SolicitarAlteracaoDAO::verificarInteressadosAgentes($CPFCNPJ)) {
                        $this->_redirect("/agente/manteragentes/agentes?acao=cc&cpf=" . $CPFCNPJ);
                    }

                    if (empty($buscatbProposta)) {
                        $inserirtbProposta = $Projetos->insertRazaoSocial($idPedidoAlteracao, $CPFCNPJ, $razaosocial);
                    } else {
                        $updatetbProposta = $Projetos->updateRazaoSocial($idPedidoAlteracao, $CPFCNPJ, $razaosocial);
                    }

                    $justificativa =  $buscaSoliciatacao->inserirJustificativa($idPedidoAlteracao, $dsJustificativa, $status);
                    SolicitarAlteracaoController::cadastrarArquivosMult($_FILES, $idPedidoAlteracao, $status);
                    parent::message("Solicita��o enviada com sucesso!", "solicitaralteracao/acaoprojeto?idpronac=$idPronac", "CONFIRM");
                } else {
                    $buscatbProposta = $Projetos->buscarRazaoSocial($idPedidoAlteracao);

                    // caso o usu�rio n�o esteja cadastrado, vai para o manter agentes
                    if (!SolicitarAlteracaoDAO::verificarInteressadosAgentes($CPFCNPJ)) {
                        $this->_redirect("/agente/manteragentes/agentes?acao=cc&cpf=" . $CPFCNPJ);
                    }

                    if (empty($buscatbProposta)) {
                        $inserirtbProposta = $Projetos->insertRazaoSocial($idPedidoAlteracao, $CPFCNPJ, $razaosocial);
                    } else {
                        $updatetbProposta = $Projetos->updateRazaoSocial($idPedidoAlteracao, $CPFCNPJ, $razaosocial);
                    }
                    $justificativa =  $Projetos->updatetbPedidoAlteracaoXTipoAlteracao($idPedidoAlteracao, $dsJustificativa, $status);
                    SolicitarAlteracaoController::cadastrarArquivosMult($_FILES, $idPedidoAlteracao, $status);
                    parent::message("Solicita��o enviada com sucesso!", "solicitaralteracao/acaoprojeto?idpronac=$idPronac", "CONFIRM");
                }
            } else {
                $buscatbProposta = $Projetos->buscarRazaoSocial($idPedidoAlteracao);

                $resultadoPedidoAlteracao = $Projetos->buscartbPedidoAlteracaoXTipoAlteracao($idPedidoAlteracao, $status);
                if (empty($resultadoPedidoAlteracao)) {
                    $justificativa =  $buscaSoliciatacao->inserirJustificativa($idPedidoAlteracao, $dsJustificativa, $status);
                } else {
                    $justificativa =  $Projetos->updatetbPedidoAlteracaoXTipoAlteracao($idPedidoAlteracao, $dsJustificativa, $status);
                }

                // caso o usu�rio n�o esteja cadastrado, vai para o manter agentes
                if (!SolicitarAlteracaoDAO::verificarInteressadosAgentes($CPFCNPJ)) {
                    $this->_redirect("/agente/manteragentes/agentes?acao=cc&cpf=" . $CPFCNPJ);
                }

                if (empty($buscatbProposta)) {
                    $inserirtbProposta = $Projetos->insertRazaoSocial($idPedidoAlteracao, $CPFCNPJ, $razaosocial);
                    SolicitarAlteracaoController::cadastrarArquivosMult($_FILES, $idPedidoAlteracao, $status);
                    parent::message("Solicita��o enviada com sucesso!", "solicitaralteracao/acaoprojeto?idpronac=$idPronac", "CONFIRM");
                } else {
                    $updatetbProposta = $Projetos->updateRazaoSocial($idPedidoAlteracao, $CPFCNPJ, $razaosocial);
                    SolicitarAlteracaoController::cadastrarArquivosMult($_FILES, $idPedidoAlteracao, $status);
                    parent::message("Solicita��o enviada com sucesso!", "solicitaralteracao/acaoprojeto?idpronac=$idPronac", "CONFIRM");
                }
            }
        } else {
            parent::message(" Dados Obrigat�rios N�o Informados", "solicitaralteracao/acaoprojeto", "ERROR");
        }
    } // fecha m�todo nomeprojetoAction()



    public static function cadastrarArquivosMult($_FILES, $idPedidoAlteracao, $status)
    {
        $Projetos = new SolicitarAlteracaoDAO();
        $valor = $_FILES['arquivo']['name'][0];
           
        if (!empty($valor)) {
            for ($i = 0; $i < count($_FILES["arquivo"]["name"]); $i++) {
                // pega as informa��es do arquivo
                $arquivoNome     = $_FILES['arquivo']['name'][$i]; // nome
                $arquivoTemp     = $_FILES['arquivo']['tmp_name'][$i]; // nome tempor�rio
                $arquivoTipo     = $_FILES['arquivo']['type'][$i]; // tipo
                $arquivoTamanho  = $_FILES['arquivo']['size'][$i]; // tamanho

                if (!empty($arquivoNome) && !empty($arquivoTemp)) {
                    $arquivoExtensao = Upload::getExtensao($arquivoNome); // extens�o
                    $arquivoBinario  = Upload::setBinario($arquivoTemp); // bin�rio
                    $arquivoHash     = Upload::setHash($arquivoTemp); // hash

                    // cadastra dados do arquivo
                    $dadosArquivo = array(
                            'nmArquivo'         => $arquivoNome,
                            'sgExtensao'        => $arquivoExtensao,
                            'dsTipoPadronizado' => $arquivoTipo,
                            'nrTamanho'         => $arquivoTamanho,
                            'dtEnvio'           => new Zend_Db_Expr('GETDATE()'),
                            'dsHash'            => $arquivoHash,
                            'stAtivo'           => 'A');
                    $cadastrarArquivo = ArquivoDAO::cadastrar($dadosArquivo);

                    // pega o id do �ltimo arquivo cadastrado
                    $idUltimoArquivo = ArquivoDAO::buscarIdArquivo();
                    $idUltimoArquivo = (int) $idUltimoArquivo[0]->id;

                    // cadastra o bin�rio do arquivo
                    $dadosBinario = array(
                            'idArquivo' => $idUltimoArquivo,
                            'biArquivo' => $arquivoBinario);
                    $cadastrarBinario = ArquivoImagemDAO::cadastrar($dadosBinario);

                    // cadastra o pedido de altera��o
                    $Projetos->insertArquivo($idUltimoArquivo, $idPedidoAlteracao, $status);
                }
            }
        } // fecha for
    } // fecha m�todo cadastrarArquivosMult()



    /**
     * M�todo para buscar todos os arquivos anexados ao item
     * @access public
     * @param integer $idPronac
     * @param integer $status
     * @return void
     */
    public static function buscarArquivos($idPronac, $status)
    {
        $Projetos          = new SolicitarAlteracaoDAO();
        $buscaSoliciatacao = new ReadequacaoProjetos();
        $valores           = $buscaSoliciatacao->buscarSolicitacao($idPronac);
        $idPedidoAlteracao = $valores[0]->idPedidoAlteracao;
    
        if (!empty($idPedidoAlteracao)) {
            $dados = $Projetos->buscarArquivo($idPedidoAlteracao, $status);
    
            // url de exclus�o
            $urlExcluir = Zend_Controller_Front::getInstance()->getBaseUrl().'/solicitaralteracao/excluirarquivo';
    
            echo "
			<script type='text/javascript'>
			function excluirArqPedido(idPedidoAlteracao, idArquivo, nmArquivo)
			{
				dados = 'idPedidoAlteracao=' + encodeURIComponent(idPedidoAlteracao);
				dados+= '&idArquivo=' + encodeURIComponent(idArquivo);
				dados+= '&nmArquivo=' + encodeURIComponent(nmArquivo);
				enviar_pag('".$urlExcluir."', dados, 'excluirArq'+idPedidoAlteracao+''+idArquivo);
			}
			function excluirArqRea(idPedidoAlteracao, idArquivo, nmArquivo)
			{
				confirmar = confirm('Deseja realmente excluir o arquivo anexado?');
				if (confirmar)
				{
					excluirArqPedido(idPedidoAlteracao, idArquivo, nmArquivo);
				}
				else
				{
					return false;
				}
			}
			</script>";
    
            $urlArquivo = Zend_Controller_Front::getInstance()->getBaseUrl().'/upload/abrir?id=';
            foreach ($dados as $arquivos) {
                echo "<div id='excluirArq".$idPedidoAlteracao.$arquivos->idArquivo."'>
					<input type='button' class='btn_exclusao' title='Excluir Arquivo' onclick=\"excluirArqRea(".$idPedidoAlteracao.", ".$arquivos->idArquivo.", '".$arquivos->nmArquivo."');\" /> 
					<a href='".$urlArquivo.$arquivos->idArquivo."' title='Abrir Arquivo'>".$arquivos->nmArquivo."</a>
				</div>";
            }
        } // fecha if
        else {
            echo "Nenhum Arquivo Encontrado!";
        }
    } // fecha m�todo buscarArquivos()



    /**
     * M�todo para buscar todos os arquivos anexados ao item
     * @access public
     * @param integer $idPronac
     * @param integer $status
     * @return void
     */
    public function excluirarquivoAction()
    {
        $this->_helper->layout->disableLayout(); // Desabilita o Zend Layout
    
        // recebe os dados via post
        $post              = Zend_Registry::get('post');
        $idPedidoAlteracao = (int) Seguranca::tratarVarAjaxUFT8($post->idPedidoAlteracao);
        $idArquivo         = (int) Seguranca::tratarVarAjaxUFT8($post->idArquivo);
        $nmArquivo         = Seguranca::tratarVarAjaxUFT8($post->nmArquivo);
    
        if (isset($idPedidoAlteracao) && isset($idArquivo) && !empty($idPedidoAlteracao) && !empty($idArquivo)) {
            SolicitarAlteracaoDAO::excluirArquivo($idPedidoAlteracao, $idArquivo);
            $this->view->nmArquivo = $nmArquivo;
        }
    } // fecha m�todo excluirArquivo()
} // fecha class
