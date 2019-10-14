<template>
    <div id="app">
        <v-app :dark="isModoNoturno">
            <SlNav />
            <v-content v-if="Object.keys(usuario).length > 0">
                <v-fade-transition mode="out-in">
                    <router-view />
                </v-fade-transition>
            </v-content>
            <carregando
                v-else
                class="mt-5"
                text="Aguarde ..."
            />

            <v-snackbar
                v-model="snackbar"
                :color="getSnackbar.color"
                :top="true"
                :right="true"
                :timeout="2000"
                @input="fecharSnackbar"
            >
                {{ getSnackbar.text }}
            </v-snackbar>
            <Rodape />
        </v-app>
    </div>
</template>

<script>
import { mapActions, mapGetters } from 'vuex';
import Rodape from '@/components/layout/footer';
import SlNav from './components/SlNav';
import Carregando from '@/components/CarregandoVuetify';

export default {
    name: 'Index',
    components: { Carregando, SlNav, Rodape },
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
            usuario: 'autenticacao/getUsuario',
        }),
    },
    watch: {
        getSnackbar(val) {
            this.snackbar = val.ativo;
        },
    },
    created() {
        this.recoverAction();
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
            recoverAction: 'autenticacao/recoverAction',
        }),
        fecharSnackbar() {
            this.setSnackbar({ ativo: false });
        },
    },
};
</script>
