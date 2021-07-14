<template>
    <div>
        <div class="fixed-action-btn vertical">
            <a
                id="menu-suspenso"
                class="btn-floating btn-large red">
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
                <slot/>
            </ul>
        </div>
    </div>
</template>
<script>
export default {
    name: 'SalicMenuSuspenso',
    /* eslint-disable */
        mounted() {
            this.irParaOTopo();
            this.irParaOFim();
        },
        methods: {
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
