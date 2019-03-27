<template>
    <div>
        <HeaderMenuPrincipalSidebar :dados-menu="dadosMenu"/>
        <v-toolbar
            app
            dense
            dark
            fixed
            clipped-left
            color="primary"
        >
            <HeaderLogo/>
            <v-toolbar-title class="ma-0 hidden-sm-and-down">Salic</v-toolbar-title>
            <v-spacer/>
            <HeaderMenuPrincipalToolbar :dados-menu="dadosMenu"/>
            <HeaderSolicitacoes/>
            <HeaderInformacoesDaConta/>
            <v-divider
                vertical
                class="hidden-md-and-up"/>
            <v-toolbar-side-icon
                v-if="$vuetify.breakpoint.smAndDown"
                class="hidden-md-and-up"
                @click.stop="drawerRight = !drawerRight"
            />
        </v-toolbar>
    </div>
</template>

<script>
import { mapActions, mapGetters } from 'vuex';
import HeaderMenuPrincipalToolbar from './HeaderMenuPrincipalToolbar';
import HeaderMenuPrincipalSidebar from './HeaderMenuPrincipalSidebar';
import HeaderInformacoesDaConta from './HeaderInformacoesDaConta';
import HeaderSolicitacoes from './HeaderSolicitacoes';
import HeaderLogo from './HeaderLogo';

export default {
    name: 'Header',
    components: {
        HeaderInformacoesDaConta,
        HeaderMenuPrincipalToolbar,
        HeaderMenuPrincipalSidebar,
        HeaderSolicitacoes,
        HeaderLogo,
    },
    data() {
        return {
            drawerRight: false,
        };
    },
    computed: {
        ...mapGetters({
            dadosMenu: 'layout/getDadosMenu',
            statusSidebarDireita: 'layout/getStatusSidebarDireita',
        }),
    },
    watch: {
        statusSidebarDireita(value) {
            this.drawerRight = value;
        },
        drawerRight(value) {
            this.atualizarStatusSidebar(value);
        },
    },
    created() {
        this.buscarDadosMenu();
        this.buscarDadosLayout();
    },
    methods: {
        ...mapActions({
            buscarDadosMenu: 'layout/buscarDadosMenu',
            buscarDadosLayout: 'layout/buscarDadosLayout',
            atualizarStatusSidebar: 'layout/atualizarStatusSidebarDireita',
        }),
    },
};
</script>
