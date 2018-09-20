<template>
    <div>
        <div v-if="dados">
        <v-toolbar
          app
          color="green darken-4 "
          dense
          dark
        >
            <v-toolbar-title>
                <img src="/public/img/logo_salic.png" alt="logo" height="30px">
            </v-toolbar-title>
            <v-spacer></v-spacer>
            <v-menu
                :nudge-width="100"
                v-for="(item, index) in dadosMenu"
                :key="item.id"
                offset-y
            >
                <v-toolbar-title
                    slot="activator"
                >
                    <v-btn flat>
                        <span v-html="item.label"></span>
                        <v-icon right dark>arrow_drop_down</v-icon>
                    </v-btn>
                </v-toolbar-title>
                <v-list
                    v-for="(menu, index) in item.menu"
                    :key="index"
                >
                    <v-list-tile>
                        <v-list-tile-title >
                            <a
                                :href="('/' + menu.url.module + '/' + menu.url.controller + '/' + menu.url.action)"
                                v-html="menu.label">
                            </a>
                        </v-list-tile-title>
                    </v-list-tile>
                </v-list>
            </v-menu>
            <v-spacer></v-spacer>
            <v-menu
                :nudge-width="100"
                offset-y
            >
                <v-toolbar-title
                    slot="activator"
                >
                    <span>Olá, Romulo</span>
                    <v-icon dark>arrow_drop_down</v-icon>
                </v-toolbar-title>
                <v-list>
                    <v-list-tile>
                        <v-list-tile-title >
                            Teste
                        </v-list-tile-title>
                    </v-list-tile>
                </v-list>
            </v-menu>
        </v-toolbar>
    </div>
    <div v-else>
        Carregando... 
    </div>
    </div>
</template>

<script>
import { mapActions, mapGetters } from 'vuex';

export default {
    name: 'SlNav',
    components: {
    },
    props: [],
    data() {
        return {
            dados: this.dadosMenuAjax(),
            // dados: '',
        };
    },
    computed: {
        ...mapGetters({
            dadosMenu: 'avaliacaoResultados/dadosMenu',
        }),
    },
    mounted() {
        // this.dadosMenuAjax();
    },
    methods: {
        ...mapActions({
            dadosMenuAjax: 'avaliacaoResultados/dadosMenu',
        }),
    },
};
</script>
