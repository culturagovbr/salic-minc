<template>
    <div id="app">
        <v-app @enlarge-text="alert('te')">
            <SlNav></SlNav>
            <v-content>
                <v-container fluid>
                  <v-layout>
                    <router-view></router-view>
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
                {{ this.getSnackbar.text }}
            </v-snackbar>
        <SlFoot></SlFoot>
      </v-app>
    </div>
</template>

<script>
import { mapActions, mapGetters } from 'vuex';
import SlNav from './components/SlNav';
import SlFoot from './components/SlFoot';

export default {
    name: 'Index',
    components: { SlNav, SlFoot },
    methods: {
        ...mapActions({
            setSnackbar: 'noticias/setDados',
        }),
        fecharSnackbar() {
            this.setSnackbar({ ativo: false });
        },
    },
    computed: {
        ...mapGetters({
            getSnackbar: 'noticias/getDados',
        }),
    },
    mounted() {
        this.setSnackbar({ ativo: false, color: 'success' });
    },
    data() {
        return {
            snackbar: false,
        };
    },
    watch: {
        getSnackbar(val) {
            this.snackbar = val.ativo;
        },
    },
};
</script>
