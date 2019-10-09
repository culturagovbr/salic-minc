<template>
    <v-container
        fluid>
        <v-toolbar>
            <v-btn
                icon
                class="hidden-xs-only"
                @click="goBack()"
            >
                <v-icon>arrow_back</v-icon>
            </v-btn>
            <v-toolbar-title>
                Projeto Base: {{ projetoBase.NomeProjeto }}
            </v-toolbar-title>
        </v-toolbar>
        <v-data-table
            :headers="headers"
            :items="projetosSimilares"
            class="elevation-1"
        >
            <template
                slot="items"
                slot-scope="props">
                <td>{{ props.item.NomeProjeto }}</td>
                <td><VisualizarPlanilhaButtton :id-pronac="props.item.IdPRONAC"/></td>
            </template>
        </v-data-table>
    </v-container>
</template>

<script>
import Vue from 'vue';
import { mapActions, mapGetters } from 'vuex';
import Moeda from '../../../../filters/money';
import VisualizarPlanilhaButtton from '../analise/VisualizarPlanilhaButtton';

Vue.filter('moedaMasc', Moeda);

export default {
    name: 'Planilha',
    components: {
        VisualizarPlanilhaButtton,
    },
    data() {
        return {
            headers: [
                {
                    text: 'Nome', value: 'NomeProjeto', sortable: false,
                },
                {
                    text: 'Ações', value: null, sortable: false,
                },
            ],
            idPronac: this.$route.params.idpronac,
        };
    },
    computed: {
        ...mapGetters({
            getProjetosSimilares: 'avaliacaoResultados/getProjetosSimilares',
        }),
        projetosSimilares() {
            if (Object.keys(this.getProjetosSimilares).length > 0) {
                return this.getProjetosSimilares.projetos;
            }
            return [];
        },
        projetoBase() {
            if (Object.keys(this.getProjetosSimilares).length > 0) {
                return this.getProjetosSimilares.projetoBase;
            }
            return {};
        },
    },
    mounted() {
        this.setProjetoSimilaresAction(this.idPronac);
    },
    methods: {
        ...mapActions({
            setProjetoSimilaresAction: 'avaliacaoResultados/projetoSimilaresAction',
        }),
        goBack() {
            if (window.history.length > 1) {
                this.$router.go(-1);
            } else {
                this.$router.push('/');
            }
        },
    },
};
</script>
