<template>
    <div>
        <v-card>
            <v-card-title>
                <h6>LOCAL DE REALIZAÇÃO</h6>
            </v-card-title>
            <v-data-table
                :pagination.sync="pagination"
                :headers="headers"
                :items="dados.locaisRealizacao"
                class="elevation-1 container-fluid"
            >
                <template
                    slot="items"
                    slot-scope="props">
                    <td class="text-xs-left">{{ props.item.Pais }}</td>
                    <td class="text-xs-left">{{ props.item.UF }}</td>
                    <td class="text-xs-left">{{ props.item.Municipio }}</td>
                    <td class="text-xs-left">{{ props.item.siAbrangencia | tipoAbrangencia }}</td>
                    <td class="text-xs-center pl-5">{{ props.item.dtInicioRealizacao | formatarData }}</td>
                    <td class="text-xs-center pl-5">{{ props.item.dtFimRealizacao | formatarData }}</td>
                    <td
                        v-if="props.item.dsJustificativa"
                        class="text-xs-center">
                        <v-btn
                            slot="activator"
                            flat
                            icon
                            @click="showItem(props.item)"
                        >
                            <v-icon>visibility</v-icon>
                        </v-btn>
                    </td>
                    <td
                        v-else
                        class="text-xs-center"> - </td>
                </template>
            </v-data-table>
            <v-dialog
                v-model="dialog"
                width="500"
            >
                <v-card>
                    <v-card-title
                        class="headline grey lighten-2"
                        primary-title
                    >
                        Justificativa
                    </v-card-title>

                    <v-card-text>
                        {{ itemEmVisualizacao.dsJustificativa }}
                    </v-card-text>
                    <v-divider/>

                    <v-card-actions>
                        <v-spacer/>
                        <v-btn
                            color="red lighten-2"
                            flat
                            @click="dialog = false"
                        >
                            Fechar
                        </v-btn>
                    </v-card-actions>
                </v-card>
            </v-dialog>
        </v-card>
    </div>
</template>
<script>
import { mapGetters } from 'vuex';
import { utils } from '@/mixins/utils';

export default {
    name: 'LocaisRealizacao',
    filters: {
        tipoAbrangencia(siAbrangencia) {
            let tipoAbrangencia = '';
            switch (siAbrangencia) {
            case '0':
                tipoAbrangencia = 'Sem Informação';
                break;
            case '1':
                tipoAbrangencia = 'Não Realizado';
                break;
            case '2':
                tipoAbrangencia = 'Realizado';
                break;
            case '3':
                tipoAbrangencia = 'Realizado com outras fontes';
                break;
            default:
                tipoAbrangencia = '';
            }
            return tipoAbrangencia;
        },
    },
    mixins: [utils],
    data() {
        return {
            itemEmVisualizacao: {},
            dialog: false,
            pagination: {
                sortBy: '',
                descending: true,
            },
            headers: [
                {
                    text: 'País',
                    align: 'left',
                    value: 'Pais',
                },
                {
                    text: 'UF',
                    align: 'left',
                    value: 'UF',
                },
                {
                    text: 'Município',
                    align: 'left',
                    value: 'Municipio',
                },
                {
                    text: 'Realizado',
                    align: 'left',
                    value: 'siAbrangencia',
                },
                {
                    text: 'Dt. Início Realização',
                    align: 'center',
                    value: 'dtInicioRealizacao',
                },
                {
                    text: 'Dt. Fim Realização',
                    align: 'center',
                    value: 'dtFimRealizacao',
                },
                {
                    text: 'Justificativa',
                    align: 'center',
                    value: 'dsJustificativa',
                },
            ],
        };
    },
    computed: {
        ...mapGetters({
            dados: 'prestacaoContas/relatorioCumprimentoObjeto',
        }),
    },
    methods: {
        showItem(item) {
            this.itemEmVisualizacao = Object.assign({}, item);
            this.dialog = true;
        },
    },
};
</script>
