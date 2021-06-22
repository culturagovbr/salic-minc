<template>
    <div>
        <div v-if="loading">
            <Carregando :text="'Relatório Físico'"/>
        </div>
        <div v-else>
            <v-data-table
                :pagination.sync="pagination"
                :headers="headers"
                :items="indexItems()"
                :rows-per-page-items="[10, 25, 50, {'text': 'Todos', value: -1}]"
                item-key="id"
                class="elevation-1 container-fluid"
            >
                <template
                    slot="items"
                    slot-scope="props">
                    <td class="text-xs-center pl-5">{{ props.item.id + 1 }}</td>
                    <td class="text-xs-left">{{ props.item.Etapa }}</td>
                    <td class="text-xs-left">{{ props.item.Item }}</td>
                    <td class="text-xs-left">{{ props.item.Unidade }}</td>
                    <td class="text-xs-right">{{ props.item.qteProgramada }}</td>
                    <td class="text-xs-right">{{ props.item.vlProgramado | filtroFormatarParaReal }}</td>
                    <td class="text-xs-right">{{ props.item.PercExecutado | filtroFormatarParaReal }} %</td>
                    <td class="text-xs-right">{{ props.item.vlExecutado | filtroFormatarParaReal }}</td>
                    <td class="text-xs-right">{{ props.item.PercAExecutar | filtroFormatarParaReal }} %</td>
                </template>
            </v-data-table>
        </div>
    </div>
</template>
<script>

import { mapActions, mapGetters } from 'vuex';
import Carregando from '@/components/CarregandoVuetify';
import { utils } from '@/mixins/utils';

export default {
    name: 'RelatorioFisico',
    components: {
        Carregando,
    },
    mixins: [utils],
    data() {
        return {
            pagination: {
                sortBy: '',
                descending: true,
            },
            loading: true,
            headers: [
                {
                    text: 'N°',
                    align: 'center',
                    value: 'id',
                },
                {
                    text: 'ETAPA',
                    align: 'left',
                    value: 'Etapa',
                },
                {
                    text: 'ITEM',
                    align: 'left',
                    value: 'Item',
                },
                {
                    text: 'UNIDADE',
                    align: 'left',
                    value: 'Unidade',
                },
                {
                    text: 'QTDE PROGRAMADA',
                    align: 'right',
                    value: 'qteProgramada',
                },
                {
                    text: 'VL. PROGRAMADO',
                    align: 'right',
                    value: 'vlProgramado',
                },
                {
                    text: '% EXECUTADO',
                    align: 'right',
                    value: 'PercExecutado',
                },
                {
                    text: 'VL. EXECUTADO',
                    align: 'right',
                    value: 'vlExecutado',
                },
                {
                    text: '% A EXECUTAR',
                    align: 'right',
                    value: 'PercAExecutar',
                },
            ],
        };
    },
    computed: {
        ...mapGetters({
            dadosProjeto: 'projeto/projeto',
            dados: 'prestacaoContas/relatorioFisico',
        }),
    },
    watch: {
        dadosProjeto(value) {
            this.loading = true;
            this.buscarRelatorioFisico(value.idPronac);
        },
        dados() {
            this.loading = false;
        },
    },
    mounted() {
        if (typeof this.dadosProjeto.idPronac !== 'undefined') {
            this.buscarRelatorioFisico(this.dadosProjeto.idPronac);
        }
    },
    methods: {
        ...mapActions({
            buscarRelatorioFisico: 'prestacaoContas/buscarRelatorioFisico',
        }),
        indexItems() {
            const currentItems = this.dados;
            return currentItems.map((item, index) => ({
                id: index,
                ...item,
            }));
        },
    },
};
</script>
