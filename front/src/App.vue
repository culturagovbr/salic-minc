<template>
    <div id="app">
        <v-app :dark="isModoNoturno">
            <Cabecalho />
            <v-content>
                <router-view />
            </v-content>

            <v-snackbar
                v-model="snackbar"
                :color="getSnackbar.color"
                :top="true"
                :timeout="4000"
                right
                @input="fecharSnackbar"
            >
                <span v-html="getSnackbar.text" />
            </v-snackbar>

            <!-- global modal confirm -->
            <salic-dialog-confirmacao ref="confirm" />

            <!-- global modal confirm -->
            <salic-dialog-ajuda ref="ajuda" />

            <Rodape />
        </v-app>
    </div>
</template>

<script>
import { mapActions, mapGetters } from 'vuex';
import Cabecalho from '@/components/layout/header';
import Rodape from '@/components/layout/footer';

import SalicDialogConfirmacao from '@/components/dialog/SalicDialogConfirmacao';
import SalicDialogAjuda from '@/components/dialog/SalicDialogAjuda';

export default {
    name: 'Index',
    components: {
        Cabecalho, Rodape, SalicDialogConfirmacao, SalicDialogAjuda,
    },
    data() {
        return {
            dark: false,
            snackbar: false,
        };
    },
    computed: {
        ...mapGetters({
            getSnackbar: 'noticias/getDados',
            isModoNoturno: 'layout/modoNoturno',
        }),
    },
    watch: {
        getSnackbar(val) {
            this.snackbar = val.ativo;
        },
    },
    mounted() {
        this.setSnackbar({ ativo: false, color: 'success' });
        this.setUsuario();
        this.obterModoNoturno();

        this.$root.$confirm = this.$refs.confirm.open;
        this.$root.$dialogAjuda = this.$refs.ajuda.open;
    },
    methods: {
        ...mapActions({
            setSnackbar: 'noticias/setDados',
            setUsuario: 'autenticacao/usuarioLogado',
            obterModoNoturno: 'layout/obterModoNoturno',
        }),
        fecharSnackbar() {
            this.setSnackbar({ ativo: false });
        },
    },
};
</script>
