<template>
    <div id="app">
        <v-app :dark="isModoNoturno">
            <Cabecalho/>
            <v-content>
                <router-view/>
            </v-content>

            <v-snackbar
                v-model="snackbar"
                :color="getSnackbar.color"
                :top="true"
                :timeout="2000"
                @input="fecharSnackbar"
            >
                {{ getSnackbar.text }}
            </v-snackbar>
            <Rodape/>
        </v-app>
    </div>
</template>

<script>
import { mapActions, mapGetters } from 'vuex';
import Cabecalho from '@/components/layout/header';
import Rodape from '@/components/layout/footer';

export default {
    name: 'Index',
    components: { Cabecalho, Rodape },
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
