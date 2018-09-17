<template>
    <aside id="sidebar-vue">
        <ul id="sidenav" class="sidenav-apoio side-nav fixed">
            <Carregando v-if="loading"></Carregando>
            <li v-show="!loading" class="sidebar-info" v-if="menu.informacoes">
                <div>
                    <p>
                        <i class="material-icons left tiny">
                            <span v-if="menu.informacoes.ativo">{{menu.informacoes.icone_ativo}}</span>
                            <span v-else>{{menu.informacoes.icone_inativo}}</span>
                        </i>
                        <b v-html="menu.informacoes.titulo"></b>
                    </p>
                    <p class="info-title" v-html="menu.informacoes.descricao"></p>
                </div>
            </li>
            <li v-show="!loading"
                v-for="(item, index) in menu"
                v-if="index != 'informacoes'"
                :key="index"
                :class="[item.submenu ? 'no-padding' : 'bold']">
                <ul v-if="item.submenu" class="collapsible collapsible-accordion">
                    <li class="bold">
                        <a
                            class="collapsible-header waves-effect waves-cyan"
                            href="javascript:void(0)"
                        >
                            <i v-if="item.icon" class="material-icons left">{{ item.icon }}</i>
                            <span v-html="item.label"></span>
                            <span v-if="item.badge" class="new badge">{{ item.badge }}</span>
                        </a>
                        <div class="collapsible-body">
                            <ul>
                                <li v-for="(subitem, index) in item.submenu" :key="index">
                                    <a class="waves-effect waves-cyan"
                                       href="javascript:void(0)"
                                       v-on:click="carregarDados(subitem)"
                                       title="Ir para"
                                       v-html="subitem.label"
                                    ></a>
                                </li>
                            </ul>
                        </div>
                    </li>
                </ul>
                <a v-else
                   href="javascript:void(0)"
                   v-on:click="carregarDados(item)"
                >
                    <i v-if="item.icon" class="material-icons left">{{ item.icon}}</i>
                    <span v-html="item.label"></span>
                    <span v-if="item.badge" class="new badge">{{ item.badge }}</span>
                </a>
            </li>
        </ul>
    </aside>
</template>

<script>
    /* eslint-disable */
    import Carregando from '@/components/Carregando';

    export default {
        name: 'SidebarMenu',
        components: {
            Carregando
        },
        data() {
            return {
                active: true,
                configs: {
                    idDivRetorno: 'conteudo',
                },
                loading: true,
                menu: {}
            };
        },
        props: {
            id: 0,
            urlAjax: "",
            arrayMenu: {}
        },
        updated() {
            this.iniciarCollapsible();
        },
        created() {
            this.adicionarBotaoNoTopo();
        },
        mounted() {
            if (typeof this.urlAjax !== 'undefined' && this.urlAjax !== '') {
                this.obterMenu();
            }

            if (typeof this.arrayMenu !== 'undefined' && this.arrayMenu !== '') {
                this.menu = this.arrayMenu;
            }
        },
        watch: {
            urlAjax(value) {
                if (typeof value !== 'undefined' && value !== '') {
                    this.obterMenu();
                }
            }
        },
        methods: {
            obterMenu() {
                let self = this;
                this.loading = true;
                $3
                    .ajax({
                        type: "GET",
                        url: self.urlAjax
                    })
                    .done(function (response) {
                        if (response) {
                            self.loading = false;
                            self.menu = response.data;
                        }
                    });
            },
            carregarDados(item) {
                if (item.ajax != true) {
                    window.location.href = item.link;
                    return;
                }

                this.$router.push({
                    name: "containerAjax",
                    params: {idPronac: this.$route.params.idPronac}
                });

                let divRetorno = this.configs.idDivRetorno;
                $("#container-loading").fadeIn("slow");
                $3.ajax({
                    url: item.link,
                    success(data) {
                        $("#container-loading").fadeOut("slow");
                        $("#" + divRetorno).html(data);
                        $3(".page-title h1").html(item.label);
                        $3("#migalhas .last").html(item.label);
                    },
                    type: "post"
                });
            },
            iniciarCollapsible() {
                $3(".collapsible").each(function () {
                    $3(this).collapsible();
                });
            },
            adicionarBotaoNoTopo() {
                if ($3("#small-menu-button").length == 0) {
                    $3("#navbar-header nav").prepend(
                        '<a id="small-menu-button" href="javascript:void(0);" class="left hide-on-med-and-down">' +
                        '<i class="material-icons">more_vert</i>' +
                        "</a>" +
                        '<a id="menu-left" href="javascript:void(0);" data-activates="sidenav" class="button-collapse left"><i class="material-icons">menu</i></a>'
                    );

                    $3("#menu-left").sideNav({edge: "left", menuWidth: 250});

                    $3("#small-menu-button").on("click", function () {
                        $3(this)
                            .find("i")
                            .text("more_horiz");
                        $3("body").toggleClass("small-menu");

                        if ($3("body.small-menu").is(":visible")) {
                            $3(this)
                                .find("i")
                                .text("more_horiz");
                            setCookie("menu", "small-menu", 365);
                        } else {
                            $3(this)
                                .find("i")
                                .text("more_vert");
                            setCookie("menu", "large-menu", 365);
                        }

                        return false;
                    });
                }
            },
        },
    };
</script>
