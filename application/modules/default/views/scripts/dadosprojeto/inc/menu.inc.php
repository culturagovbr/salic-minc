<!-- ========== IN�CIO MENU ========== -->
<script language="javascript" type="text/javascript" src="<?php echo $this->baseUrl(); ?>/public/scripts/quickmenu.js"></script>
<script type="text/javascript">
    var votacao = window.setInterval(
    function(){
        verificarReuniao('<?php echo isset($this->dadosReuniaoPlenariaAtual['idNrReuniao']) ? $this->dadosReuniaoPlenariaAtual['idNrReuniao'] : $this->dadosReuniaoPlenariaAtual['idnrreuniao']; ?>')
    }, 1000);

    function verificarReuniao(idnrreuniao){
        var idnrreuniao      = idnrreuniao;
        var stplenariaatual  = '<?php echo $this->dadosReuniaoPlenariaAtual['stPlenaria']; ?>';
        $.ajax({
            type: "POST",
            url: "<?php echo $this->url(array('controller' => 'gerenciarpautareuniao', 'action' => 'verificavotacao')) ?>",
            data:{
                idnrreuniao : idnrreuniao
            },
            success: function(dados)
            {
                var votante = <?php echo !empty($this->votante) ? $this->votante : "false"; ?>;
                if(dados){
                    if(!dados.error) {
                        if(dados.real > 0){
                            if(dados.stPlenaria != stplenariaatual){
                                if(dados.dataCron != 'vazio'){
                                    data = dados.dataCron;
                                    valor = data.split(":");
                                    if(dados.stPlenaria == 'A'){
                                        $("#alertar").dialog({
                                            resizable: true,
                                            width:450,
                                            height:150,
                                            modal: true,
                                            autoOpen:false,
                                            buttons :{
                                                'OK':function(){
                                                    $(this).dialog('close');
                                                }
                                            }
                                        });
                                        $("#alertar").html('Em 15 Minutos a Plen�ria ser� iniciada. Favor encerrar as suas atividades!');
                                        $("#alertar").dialog('open');
                                        $('#cronometro').css('display','');
                                        $("#minu").html(valor[0]);
                                        $("#seg").html(valor[1]);
                                        clearInterval(votacao);
                                        cronometro(function(){
                                            if(votante){
                                                abrirmodal(function(){
                                                    window.location = "<?php echo $this->url(array('controller' => 'gerenciarpautareuniao', 'action' => 'gerenciarpautareuniao')); ?>";
                                                });
                                            }
                                            else{
                                                abrirmodal(function(){
                                                    window.location = "<?php echo $this->url(array('controller' => 'areadetrabalho', 'action' => 'index')); ?>";
                                                });
                                            }
                                        });
                                    }
                                    else{
                                        window.location = "<?php echo $this->url(array('controller' => 'areadetrabalho', 'action' => 'index')) ?>";
                                    }
                                }
                            }
                            else if(dados.stPlenaria == stplenariaatual)
                            {
                                if(dados.dataCron != 'vazio'){
                                    if(dados.stPlenaria == 'A'){
                                        data = dados.dataCron;
                                        valor = data.split(":");
                                        $('#cronometro').css('display','');
                                        $("#minu").html(valor[0]);
                                        $("#seg").html(valor[1]);
                                        clearInterval(votacao);
                                        cronometro(function(){
                                            if(votante == 'ok'){
                                                abrirmodal(function(){
                                                    window.location = "<?php echo $this->url(array('controller' => 'gerenciarpautareuniao', 'action' => 'gerenciarpautareuniao')); ?>";
                                                });
                                            }
                                            else{
                                                abrirmodal(function(){
                                                    window.location = "<?php echo $this->url(array('controller' => 'areadetrabalho', 'action' => 'index')); ?>";
                                                });
                                            }
                                        });
                                    }
                                }
                            }
                        } else{
                            if(dados.stPlenaria == 'A'){
                                if(votante){
                                    abrirmodal(function(){
                                        window.location = "<?php echo $this->url(array('controller' => 'gerenciarpautareuniao', 'action' => 'gerenciarpautareuniao')); ?>";
                                    });
                                }
                                else{
                                    abrirmodal(function(){
                                        window.location = "<?php echo $this->url(array('controller' => 'areadetrabalho', 'action' => 'index')); ?>";
                                    });
                                }
                            }
                        }
                    }
                }
            }
            , dataType : 'json'
        });
    }
    var contador;
    function cronometro(funcaofim){
        clearInterval(contador);
        contador = setInterval(function(){
            var minu = $('#cronometro #minu').html()*1;
            var seg = $('#cronometro #seg').html()*1;

            if(seg == 0){
                minu--;
                seg = 59;
            }
            else
                seg--;

            if(seg < 10)
                seg = '0'+seg;
            if(minu < 10)
                minu = '0'+minu;

            $('#cronometro #minu').html(minu);
            $('#cronometro #seg').html(seg);

            if(minu==0 && seg==0){
                clearInterval(contador);
                funcaofim();
            }
        },1000);
    }
    function abrirmodal(funcaoFim){
        $("#iniciareuniao").dialog({
            closeOnEscape: false,
            resizable: true,
            width:450,
            height:150,
            modal: true,
            autoOpen:false,
            buttons  :
                {
                'OK': funcaoFim
            }
        });
        $('#iniciareuniao').dialog('open');
    }
    function layout_fluido()
    {
        var janela = $(window).width();

        var fluidNavGlobal = janela - 245;
        var fluidConteudo = janela - 253;
        var fluidTitulo = janela - 252;
        var fluidRodape = janela - 19;

//        $("#navglobal").css("width",fluidNavGlobal);
//        $("#conteudo").css("width",fluidConteudo);
//        $("#titulo").css("width",fluidTitulo);
//        $("#rodapeConteudo").css("width",fluidConteudo);
//        $("#rodape").css("width",fluidRodape);

        $("div#rodapeConteudo").attr("id", "rodapeConteudo_com_menu");
    }
    $(function(){
        $('.menuHorizontal').each(function(){
            var menu = this;
            $(menu).menu({
                content: $(menu).next().html(),
                flyOut: true
            });
        });
    });
</script>
<div id="menu">



    <!-- in�cio: conte�do principal #container -->
    <div id="container">

        <!-- in�cio: navega��o local #qm0 -->

        <?php
        $getPronac = $this->idpronac;
        ?>

        <div id="menuContexto">
            <div class="top"></div>
            <div id="qm0" class="qmmc sanfona">
                <a class="no_seta" href="<?php echo $this->url(array('controller' => 'dadosprojeto', 'action' => 'index')); ?>" title="Ir para Consultar Projetos">Consultar Projetos </a>
                <a class="no_seta" href="<?php echo $this->url(array('controller' => 'proponente', 'action' => 'index')); ?>" title="Ir para Dados do Proponente">Dados do Proponente</a>
                <a class="no_seta" href="<?php echo $this->url(array('controller' => 'anexardocumentos', 'action' => 'index')); ?>" title="Ir para Documentos Anexados">Documentos Anexados</a>
                <a class="no_seta" href="<?php echo $this->url(array('controller' => 'mantermensagens', 'action' => 'consultarmensagem')); ?>" title="Ir para Mensagens">Mensagens</a>
                <a class="no_seta" href="<?php echo $this->url(array('controller' => 'diligenciarproponente', 'action' => 'index')); ?>" title="Ir para Diligenciar Proponente">Diligenciar Proponente</a>
            </div>
            <div class="bottom">
            </div>
            <div id="cronometro" style="background: #f8f8f8; display: none; font-size: 1.8em; padding-top: 2em; text-align: center; color: red; font-weight: 800; padding-left: 0.3em;" >
                In�cio da Plen�ria em <br/><br/> <span id="minu"></span>' : <span id="seg" ></span>"
            </div>
            <div id="space_menu">
            </div>
        </div>
        <div id="iniciareuniao" class="sumir">Plen�ria Iniciada. Voc� ser� redirecionado</div>

        <script type="text/javascript">

        </script>
        <div id="alertar"></div>
        <!-- final: navega��o local #qm0 -->
    </div>
</div>
<!-- ========== FIM MENU ========== -->