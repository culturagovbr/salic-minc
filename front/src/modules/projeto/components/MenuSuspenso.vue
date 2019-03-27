<template>
    <div>
        <div class="fixed-action-btn vertical">
            <a class="btn-floating btn-large red">
                <i class="large material-icons">add</i>
            </a>
            <ul>
                <li>
                    <a
                        id="ir-para-o-topo"
                        class="btn-floating yellow darken-1 tooltipped"
                        data-tooltip="Ir para o topo"
                        href="javascript:void(0)">
                        <i class="material-icons">arrow_upward</i>
                    </a>
                </li>
                <li>
                    <a
                        id="ir-para-o-fim"
                        class="btn-floating yellow darken-1 tooltipped"
                        data-tooltip="Ir para o fim"
                        href="javascript:void(0)">
                        <i class="material-icons">arrow_downward</i>
                    </a>
                </li>
                <li v-if="idPronac">
                    <a
                        class="btn-floating blue tooltipped"
                        href="javascript:void(0)"
                        data-tooltip="Imprimir Projeto"
                        @click="imprimirProjeto(idPronac)"
                    ><i class="material-icons">print</i></a>
                </li>
            </ul>
        </div>
        <div
            id="boxImprimirProjeto"
            style="display: none;"/>
    </div>
</template>
<script>
export default {
    /* eslint-disable */
        data() {
            return {
                idPronac: this.$route.params.idPronac
            };
        },
        mounted() {
            this.irParaOTopo();
            this.irParaOFim();
        },
        methods: {
            imprimirProjeto(idPronac) {
                let self = this;

                $("#boxImprimirProjeto").html(
                    "<br><br><center>Carregando dados...</center>"
                );

                $.ajax({
                    url: "/default/consultardadosprojeto/form-imprimir-projeto?idPronac=" +
                    idPronac,
                    data: {
                        idPronac: idPronac
                    },
                    success: function (data) {
                        $("#boxImprimirProjeto").html(data);
                    },
                    type: "post"
                });

                $("#boxImprimirProjeto").dialog({
                    title: "Imprimir Projeto",
                    resizable: true,
                    width: 750,
                    height: 460,
                    modal: true,
                    autoOpen: false,
                    buttons: {
                        Fechar: function () {
                            $(this).dialog("close");
                        },
                        OK: function () {
                            self.submeteForm();
                            //$('#frmOpcoesImpressao').submit();
                        }
                    }
                });
                $("#boxImprimirProjeto").dialog("open");
            },
            submeteForm() {
                var n = $("input:checked").length;
                if (n > 0) {
                    $("#msgErroImpressao").html("");
                    $("#frmOpcoesImpressao").submit();
                } else {
                    $("#msgErroImpressao").html(
                        "<center><font color='red'>� obrigat�rio selecionar ao menos uma informa��o para impress�o.</font></center>"
                    );
                }
            },
            irParaOTopo() {
                if ($3("#ir-para-o-topo").length) {
                    var scrollTrigger = 100, // px
                        backToTop = function () {
                            var scrollTop = $3(window).scrollTop();
                            $3("#ir-para-o-topo")
                                .parent()
                                .hide();
                            if (scrollTop > scrollTrigger) {
                                $3("#ir-para-o-topo")
                                    .parent()
                                    .show();
                            }
                        };
                    backToTop();

                    $3(window).on("scroll", function () {
                        backToTop();
                    });

                    $3("#ir-para-o-topo").on("click", function (e) {
                        e.preventDefault();
                        $3("html,body").animate(
                            {
                                scrollTop: 0,
                            },
                            700,
                        );
                    });
                }
            },
            irParaOFim() {
                if ($3("#ir-para-o-fim").length) {
                    $3("#ir-para-o-fim").on("click", function (e) {
                        e.preventDefault();
                        $3("html,body").animate(
                            {
                                scrollTop: $(document).height(),
                            },
                            700,
                        );
                    });
                }
            },
        },
    };
</script>
