<script language="javascript" type="text/javascript" src="<?php echo $this->baseUrl(); ?>/public/scripts/quickmenu.js"></script>

<div class="col s2" id="menu">
    <div id="container">
        <script type="text/javascript">
            function layout_fluido() {
                var janela = $(window).width();
                var fluidNavGlobal = janela - 245;
                var fluidConteudo = janela - 253;
                var fluidTitulo = janela - 252;
                var fluidRodape = janela - 19;
//                $("#navglobal").css("width",fluidNavGlobal);
//                $("#conteudo").css("width",fluidConteudo);
//                $("#titulo").css("width",100%);
//                $("#rodapeConteudo").css("width",fluidConteudo);
//                $("#rodape").css("width",fluidRodape);
                $("div#rodapeConteudo").attr("id", "rodapeConteudo_com_menu");
            }
        </script>
        <style type="text/css">
            .sanfonaDiv {
                clear: both;
                display: none;
            }
        </style>
        <div id="corfirma" title="Confirmacao" style='display:none;'></div>
        <div id="ok" title="Confirmacao" style='display:none;'></div>
        <?php
            $get = Zend_Registry::get("get");
            $pronac = null;
            $projeto = null;
            //define id do PreProjeto que sera passado as outras implementacoes
            $codPronac = "?idPronac=";
            if (isset($this->idPronac)) {
                $codPronac .= $this->idPronac;
                $pronac = $this->idPronac;
            } elseif (isset($get->idPronac)) {
                $codPronac .= $get->idPronac;
                $pronac = $get->idPronac;
            }

            //define id do PreProjeto que sera passado as outras implementacoes
            $codProjeto = "?idPreProjeto=";
            if (isset($this->idPreProjeto)) {
                $codProjeto .= $this->idPreProjeto;
                $projeto = $get->idPreProjeto;
            } elseif (isset($get->idPreProjeto)) {
                $codProjeto .= $get->idPreProjeto;
                $projeto = $get->idPreProjeto;
            }
        ?>
        <div class="collection">
                <?php if (!empty($pronac)):?>
                    <li class="collection-item"><a class="no_seta" href="<?php echo $this->url(array('module'=> 'default', 'controller' => 'consultardadosprojeto', 'action' => 'index')); ?><?php echo $codPronac;?>">Dados do Projeto</a></li>
                <?php endif;?>
                <?php if (!empty($projeto)):?>
                    <?php if (isset($_GET['edital'])):?>
                        <li class="collection-item"><a class="no_seta" href="<?php echo $this->url(array('module'=> 'default', 'controller' => 'manterpropostaedital', 'action' => 'dadospropostaedital')); ?><?php echo $codProjeto;?>">Dados da Proposta</a></li>
                    <?php else:?>
                        <li class="collection-item"><a class="no_seta" href="<?php echo $this->url(array('module'=> 'default', 'controller' => 'manterpropostaincentivofiscal', 'action' => 'editar')); ?><?php echo $codProjeto;?>">Dados da Proposta</a></li>
                    <?php endif;?>
                <?php endif;?>
                <?php if (isset($this->menumsg)) {
            ?>
                    <div class="sanfonaDiv"></div>
                        <li class="collection-item"><a class="no_seta" href="<?php echo $this->url(array('module'=> 'default', 'controller' => 'mantermensagens', 'action' => 'consultarmensagem')); ?>/idpronac/<?php echo $this->idPronac; ?>">Mensagens</a></li>
                <?php
        } ?>
        </div>
    </div>
</div>
