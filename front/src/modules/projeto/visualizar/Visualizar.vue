<template>
    <div class="visualiza">
        <Carregando v-if="carregando" :text="'Validando acesso ao projeto'"></Carregando>
        <div v-else-if="Object.keys(dadosProjeto).length > 0 && permissao">
            <SidebarMenu :url-ajax="urlAjax"></SidebarMenu>
            <div class="container-fluid">
                <TituloPagina :titulo="$route.meta.title"></TituloPagina>
                <router-view class="child-view "></router-view>
            </div>
            <MenuSuspenso/>
        </div>
        <div v-if="!permissao">
            <SalicMensagemErro :texto="'Sem permiss&atilde;o de acesso para este projeto'"/>
        </div>
    </div>
</template>
<script>
    import { mapActions, mapGetters } from 'vuex';
    import { utils } from '@/mixins/utils';
    import SidebarMenu from '@/components/SidebarMenu';
    import Carregando from '@/components/Carregando';
    import TituloPagina from '@/components/TituloPagina';
    import SalicMensagemErro from '@/components/SalicMensagemErro';
    import MenuSuspenso from '../components/MenuSuspenso';

    const URL_MENU = '/projeto/menu/obter-menu-ajax/idPronac/';

    export default {
        name: 'Index',
        components: {
            SidebarMenu,
            TituloPagina,
            MenuSuspenso,
            Carregando,
            SalicMensagemErro,
        },
        mixins: [utils],
        data() {
            return {
                urlAjax: URL_MENU + this.$route.params.idPronac,
                carregando: true,
                permissao: true,
            };
        },
        watch: {
            $route(to, from) {
                /**
                 * se alterar apenas o parametro na url, o vue nao recarrega o componente.
                 * aqui esta recarregando os dados do novo projeto se o idPronac for diferente
                 * */
                if (
                    typeof to.params.idPronac !== 'undefined' &&
                    to.params.idPronac !== from.params.idPronac
                ) {
                    this.buscaProjeto(to.params.idPronac);
                    this.urlAjax = URL_MENU + to.params.idPronac;
                }
            },
            dadosProjeto(value) {
                if (Object.keys(value).length > 0) {
                    this.carregando = false;
                    this.permissao = value.permissao;
                }
            },
        },
        created() {
            if (Object.keys(this.dadosProjeto).length === 0) {
                this.buscaProjeto(this.$route.params.idPronac);
            }
        },
        methods: {
            ...mapActions({
                buscaProjeto: 'projeto/buscarProjetoCompleto',
            }),
        },
        computed: {
            ...mapGetters({
                dadosProjeto: 'projeto/projeto',
            }),
        },
    };
</script>
