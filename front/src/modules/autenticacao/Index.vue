<template>
    <div id="app">
        <v-app>
            <router-view/>
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
        </v-app>
    </div>
</template>

<script>
import { mapActions, mapGetters } from 'vuex';

export default {
    name: 'Index',
    data() {
        return {
            snackbar: false,
        };
    },
    computed: {
        ...mapGetters({
            getSnackbar: 'noticias/getDados',
        }),
    },
    watch: {
        getSnackbar(val) {
            this.snackbar = val.ativo;
        },
    },
    methods: {
        ...mapActions({
            setSnackbar: 'noticias/setDados',
        }),
        fecharSnackbar() {
            this.setSnackbar({ ativo: false });
        },
    },
};
</script>
<style>
#app {
    background-image: url(/public/img/md-bg-green-orange.jpg);
}
</style>
