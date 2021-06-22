Vue.component('sidebar-menu', {
    template: `
    <div>
        <ul id="sidenav" class="sidenav-apoio side-nav fixed">
            <carregando v-if="loading"></carregando>
            <li class="sidebar-info" v-if="menu.informacoes">
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
            <li v-for="(item, index) in menu" v-if="index != 'informacoes'" :class="[item.submenu ? 'no-padding' : 'bold']">
                 <ul v-if="item.submenu"  class="collapsible collapsible-accordion">
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
                                <li v-for="subitem in item.submenu">
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
                <a  v-else 
                    href="javascript:void(0)"
                    v-on:click="carregarDados(item)"
                    >
                    <i v-if="item.icon" class="material-icons left">{{ item.icon}}</i>
                    <span v-html="item.label"></span>
                    <span v-if="item.badge" class="new badge">{{ item.badge }}</span>
                </a>
            </li>
        </ul>
    </div>
  
    `,
    data: function () {
        return {
            active: true,
            configs: {
                idDivRetorno: 'conteudo'
            },
            loading: true,
            menu: {}
        }
    },
    props: {
        id: 0,
        urlAjax: '',
        arrayMenu: {},
    },
    updated: function () {
        this.iniciarCollapsible();
    },
    mounted: function() {
        if(typeof this.urlAjax != 'undefined' && this.urlAjax != '') {
            this.obterMenu();
        }

        if(typeof this.arrayMenu != 'undefined' && this.arrayMenu != '') {
            this.menu = this.arrayMenu;
        }
    },
    methods: {
        obterMenu: function () {
            let self = this;
            $3.ajax({
                type: "GET",
                url: self.urlAjax
            }).done(function (response) {
                if (response) {
                    self.loading = false;
                    self.menu = response.data;
                }
            });
        },
        carregarDados: function (item) {

            if (item.link == '') {
                return;
            }

            if (item.ajax != true) {
                window.location.href = item.link;
                return;
            }

            let divRetorno = this.configs.idDivRetorno;
            $3("#" + divRetorno).html('carregando...');
            $3(".page-title h1").html(item.label);
            $3("#migalhas .last").html(item.label);
            $('#container-loading').fadeIn('slow');
            $3.ajax({
                url: item.link,
                success: function (data) {
                    $('#container-loading').fadeOut('slow');
                    $("#" + divRetorno).html(data);
                },
                type: 'post'
            });
        },
        iniciarCollapsible: function () {
            $3('.collapsible').each(function () {
                $3(this).collapsible();
            });
        }
    }
});
