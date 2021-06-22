<template>
    <div>
        <v-card>
            <v-card-title>
                <h6>ACEITE DE OBRA</h6>
            </v-card-title>
            <v-data-table
                :pagination.sync="pagination"
                :headers="headers"
                :items="dados.aceiteObras"
                class="elevation-1 container-fluid"
            >
                <template
                    slot="items"
                    slot-scope="props">
                    <td class="text-xs-left">{{ (props.item.stConstrucaoCriacaoRestauro === 1) ? 'SIM' : 'NÃO' }}</td>
                    <td class="text-xs-left">{{ props.item.dsDescricaoTermoAceite }}</td>
                    <td class="text-xs-left">
                        <v-btn
                            :href="`/upload/abrir?id=${props.item.idArquivo}`"
                            target="_blank"
                            style="text-decoration: none"
                            round
                            small
                        >
                            {{ props.item.nmArquivo }}
                        </v-btn>
                    </td>
                    <td class="text-xs-right">{{ props.item.dtEnvio | formatarData }}</td>
                </template>
            </v-data-table>
        </v-card>
    </div>
</template>
<script>
import { mapGetters } from 'vuex';
import { utils } from '@/mixins/utils';

export default {
    name: 'AceiteObra',
    mixins: [utils],
    data() {
        return {
            pagination: {
                sortBy: '',
                descending: true,
            },
            headers: [
                {
                    text: 'Prevê construção, reforma, restauro ou similares?',
                    align: 'left',
                    value: 'stConstrucaoCriacaoRestauro',
                },
                {
                    text: 'Descrição do Termo',
                    align: 'left',
                    value: 'dsDescricaoTermoAceite',
                },
                {
                    text: 'Arquivo',
                    align: 'left',
                    value: 'nmArquivo',
                },
                {
                    text: 'Data de Envio',
                    align: 'right',
                    value: 'PercFinanceiro',
                },
            ],
        };
    },
    computed: {
        ...mapGetters({
            dados: 'prestacaoContas/relatorioCumprimentoObjeto',
        }),
    },
};
</script>
