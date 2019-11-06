<template>
    <v-data-table
        :headers="headers"
        :items="dados"
        :expand="expand"
        item-key="dtSolicitacao"
        class="elevation-1"
        disable-initial-sort
    >
        <template
            slot="items"
            slot-scope="props">
            <tr @click="props.expanded = !props.expanded">
                <td
                    class="text-xs-left">{{ props.item.dsSolicitacao | filtroDiminuirTexto(150) }} ...</td>
                <td class="text-xs-left">{{ props.item.dsEncaminhamento }}</td>
                <td>{{ props.item.dtSolicitacao | formatarData }}</td>
                <td>{{ props.item.dtResposta | formatarData }}</td>
                <td class="text-xs-center">
                    <v-btn
                        color="blue"
                        fab
                        small
                        dark>
                        <v-icon>visibility</v-icon>
                    </v-btn>
                </td>
            </tr>
        </template>
        <template
            slot="expand"
            slot-scope="props">
            <v-card flat>
                <v-card-text>
                    <v-layout row>
                        <v-flex
                            v-if="props.item.idPronac"
                            xs12
                            sm12
                            md3>
                            <b>Pronac: </b><br>{{ props.item.Pronac }}
                        </v-flex>
                        <v-flex
                            v-else
                            xs12
                            sm12
                            md3>
                            <b>N&ordm; da Proposta: </b>{{ props.item.idProjeto }}
                        </v-flex>
                        <v-flex
                            xs12
                            sm12
                            md6>
                            <b>Proposta/Projeto: </b>{{ props.item.NomeProjeto }}
                        </v-flex>
                    </v-layout>
                    <v-layout row>
                        <v-flex
                            md12>
                            <b>Solicitação </b>
                            <div v-html="props.item.dsSolicitacao"/>
                        </v-flex>
                    </v-layout>
                    <v-layout row>
                        <v-flex
                            v-if="props.item.dsResposta"
                            md12
                        >
                            <b>Resposta </b>
                            <div v-html="props.item.dsResposta"/>
                        </v-flex>
                        <v-flex
                            v-else
                            md12>
                            <b>Resposta </b>
                            <div>Sem resposta para esta Solicita&ccedil;&atilde;o.</div>
                        </v-flex>
                    </v-layout>
                </v-card-text>
            </v-card>
        </template>
    </v-data-table>
</template>
<script>
import { mapActions, mapGetters } from 'vuex';
import { utils } from '@/mixins/utils';

export default {
    name: 'PropostaHistoricoSolicitacoes',
    filters: {
        filtroResumoSolicitacao(value) {
            return value.replace(/(<([^>]+)>)/ig, '').slice(0, 150);
        },
    },
    mixins: [utils],
    props: {
        idpreprojeto: {
            type: Number,
            default: 0,
        },
    },
    data() {
        return {
            expand: false,
            headers: [
                { text: 'Solicitação', value: 'dsSolicitacao' },
                { text: 'Estado', value: 'dsEncaminhamento' },
                { text: 'Dt. Solicitação', align: 'left', value: 'dtSolicitacao' },
                { text: 'Dt. Resposta', align: 'left', value: 'dtResposta' },
                { text: '#', align: 'center', value: 'dsEncaminhamento' },
            ],
        };
    },
    computed: {
        ...mapGetters({
            dados: 'proposta/historicoSolicitacoes',
        }),
    },
    watch: {
        idpreprojeto(value) {
            this.buscarHistoricoSolicitacoes(value);
        },
    },
    mounted() {
        if (this.idpreprojeto !== 0) {
            this.buscarHistoricoSolicitacoes(this.idpreprojeto);
        }
    },
    methods: {
        ...mapActions({
            buscarHistoricoSolicitacoes: 'proposta/buscarHistoricoSolicitacoes',
        }),
    },
};
</script>
