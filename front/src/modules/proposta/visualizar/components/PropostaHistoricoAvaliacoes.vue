<template>
    <v-data-table
        :headers="headers"
        :items="dado"
        class="elevation-1"
    >
        <template
            slot="items"
            slot-scope="props">
            <td>{{ props.item.Tipo }}</td>
            <td class="text-xs-right">{{ props.item.DtAvaliacao | formatarData }}</td>
            <td
                class="text-xs-justify"
                v-html="props.item.Avaliacao"/>
        </template>
    </v-data-table>
</template>
<script>
import axios from 'axios';
import { utils } from '@/mixins/utils';

export default {
    name: 'PropostaHistoricoAvaliacoes',
    mixins: [utils],
    props: {
        idpreprojeto: {
            type: [String, Number],
            default: '',
        },
    },
    data() {
        return {
            dado: [],
            headers: [
                {
                    text: 'Tipo',
                    align: 'left',
                    value: 'Tipo',
                },
                { text: 'Dt. Avaliação', value: 'DtAvaliacao' },
                { text: 'Avaliação', value: 'Avaliacao' },
            ],
        };
    },
    watch: {
        idpreprojeto(value) {
            this.fetch(value);
        },
    },
    mounted() {
        if (typeof this.idpreprojeto !== 'undefined') {
            this.fetch(this.idpreprojeto);
        }
    },
    methods: {
        fetch(id) {
            if (id) {
                const self = this;
                axios.get(`/proposta/visualizar/obter-historico-avaliacoes/idPreProjeto/${id}`)
                    .then((response) => {
                        self.dado = response.data.data;
                    });
            }
        },
    },
};
</script>
