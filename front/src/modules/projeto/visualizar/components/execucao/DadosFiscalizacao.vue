<template>
    <div>
        <div v-if="loading">
            <Carregando :text="'Dados Fiscalização'"/>
        </div>
        <div v-else-if="dadosListagem">
            <v-data-table
                :pagination.sync="pagination"
                :headers="headers"
                :items="dadosListagem"
                class="elevation-1 container-fluid"
                hide-actions
            >
                <template
                    slot="items"
                    slot-scope="props">
                    <td class="text-xs-center pl-5">{{ props.item.dtInicio | formatarData }}</td>
                    <td class="text-xs-center pl-5">{{ props.item.dtFim | formatarData }}</td>
                    <td class="text-xs-left">{{ props.item.cpfTecnico | cnpjFilter }}</td>
                    <td class="text-xs-left">{{ props.item.nmTecnico }}</td>
                    <td class="text-xs-center">
                        <v-btn
                            flat
                            icon>
                            <v-tooltip bottom>
                                <v-icon
                                    slot="activator"
                                    class="material-icons"
                                    dark
                                    @click="showItem(props.item.idFiscalizacao)">visibility
                                </v-icon>
                                <span>Visualizar Dados Fiscalizacao</span>
                            </v-tooltip>
                        </v-btn>
                    </td>
                </template>
            </v-data-table>
        </div>
        <v-layout
            row
            justify-center>
            <VisualizarFiscalizacao
                :dados-visualizacao="dadosVisualizacao"
                :dialog="dialog"/>
        </v-layout>
    </div>
</template>
<script>

import { mapActions, mapGetters } from 'vuex';
import Carregando from '@/components/CarregandoVuetify';
import cnpjFilter from '@/filters/cnpj';
import VisualizarFiscalizacao from './components/VisualizarFiscalizacao';
import { utils } from '@/mixins/utils';

export default {
    name: 'DadosFiscalizacao',
    filters: {
        cnpjFilter,
    },
    components: {
        VisualizarFiscalizacao,
        Carregando,
    },
    mixins: [utils],
    data() {
        return {
            dialog: false,
            loading: true,
            pagination: {
                rowsPerPage: 10,
                sortBy: 'dtInicio',
                descending: true,
            },
            headers: [
                {
                    text: 'DT. INÍCIO',
                    align: 'center',
                    value: 'dtInicio',
                },
                {
                    text: 'DT. FIM',
                    align: 'center',
                    value: 'dtFim',
                },
                {
                    text: 'CPF TÉCNICO',
                    align: 'left',
                    value: 'cpfTecnico',
                },
                {
                    text: 'NOME TÉCNICO',
                    align: 'left',
                    value: 'nmTecnico',
                },
                {
                    text: 'VISUALIZAR',
                    align: 'center',
                    sortable: false,
                    value: 'dados',
                },
            ],
        };
    },
    computed: {
        ...mapGetters({
            dadosProjeto: 'projeto/projeto',
            dadosListagem: 'execucao/dadosFiscalizacaoLista',
            dadosVisualizacao: 'execucao/dadosFiscalizacaoVisualiza',
        }),
    },
    watch: {
        dadosListagem() {
            this.loading = false;
        },
    },
    mounted() {
        if (typeof this.dadosProjeto.idPronac !== 'undefined') {
            this.buscarDadosFiscalizacaoLista(this.dadosProjeto.idPronac);
        }
    },
    methods: {
        showItem(idFiscalizacao) {
            const { idPronac } = this.dadosProjeto;

            this.modalOpen(true);
            this.buscarDadosFiscalizacaoVisualiza({ idPronac, idFiscalizacao });
        },
        ...mapActions({
            buscarDadosFiscalizacaoLista: 'execucao/buscarDadosFiscalizacaoLista',
            buscarDadosFiscalizacaoVisualiza: 'execucao/buscarDadosFiscalizacaoVisualiza',
            modalOpen: 'modal/modalOpen',
        }),
    },
};
</script>
