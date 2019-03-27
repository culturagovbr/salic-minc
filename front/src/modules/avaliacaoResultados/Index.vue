<template>
    <div id="app">
        <v-app :dark="isModoNoturno">
            <SlNav/>
            <v-content>
                <v-container
                    v-if="Object.keys(usuario).length > 0"
                    fluid>
                    <v-layout>
                        <v-fade-transition mode="out-in">
                            <router-view/>
                        </v-fade-transition>
                    </v-layout>
                </v-container>
            </v-content>

            <v-snackbar
                v-model="snackbar"
                :color="getSnackbar.color"
                :top="true"
                :left="true"
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
import Rodape from '@/components/layout/footer';
import SlNav from './components/SlNav';

export default {
    name: 'Index',
    components: { SlNav, Rodape },
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
