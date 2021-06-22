<template>
    <div>
        <v-card
            v-for="(comunicado, index) in comunicados"
            v-if="pagination.page == (index+1)"
            :key="index"
            class="my-1; "
            hover>
            <v-toolbar
                card
                dense
            >
                <v-toolbar-title>
                    <span class="subheading">Comunicado {{ index + 1 }}</span>
                </v-toolbar-title>

            </v-toolbar>
            <v-card-text v-html="comunicado.comunicado"/>
        </v-card>
        <div class="text-xs-center mt-2">
            <v-pagination
                v-model="pagination.page"
                :length="pages"/>
        </div>
    </div>
</template>

<script>
import { mapActions, mapGetters } from 'vuex';

export default {
    name: 'Comunicados',
    data: () => ({
        pagination: {
            page: 1,
            rowsPerPage: 1,
        },
    }),
    computed: {
        ...mapGetters({
            comunicados: 'paginaInicial/obterComunicados',
        }),
        pages() {
            return this.comunicados.length;
        },
    },
    created() {
        this.obterComunicados();
    },
    methods: {
        ...mapActions({
            obterComunicados: 'paginaInicial/buscarComunicados',
        }),
    },
};
</script>

<style scoped>

</style>
