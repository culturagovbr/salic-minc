<template>
    <v-data-table
        :headers="headers"
        :items="dado"
        class="elevation-1"
        disable-initial-sort
    >
        <template
            slot="items"
            slot-scope="props">
            <td>{{ props.item.data_avaliacao | formatarData }}</td>
            <td class="text-xs-left">{{ props.item.usu_nome }}</td>
            <td class="text-xs-center">{{ props.item.org_sigla }}</td>
            <td class="text-xs-center">{{ props.item.area }}</td>
            <td class="text-xs-left">{{ props.item.segmento }}</td>
            <td class="text-xs-left">{{ props.item.enquadramento }}</td>
            <td
                class="text-xs-justify"
                v-html="props.item.descricao_motivacao"/>
        </template>
    </v-data-table>
</template>
<script>
import { mapActions, mapGetters } from 'vuex';
import { utils } from '@/mixins/utils';

export default {
    name: 'PropostaHistoricoSugestoesEnquadramento',
    mixins: [utils],
    props: {
        idpreprojeto: {
            type: Number,
            default: 0,
        },
    },
    data() {
        return {
            headers: [
                { text: 'Data', value: 'data_avaliacao' },
                { text: 'Avaliador', value: 'usu_nome' },
                { text: 'Unidade', align: 'center', value: 'org_sigla' },
                { text: 'Área', align: 'center', value: 'area' },
                { text: 'Segmento', align: 'left', value: 'segmento' },
                { text: 'Enquadramento', align: 'left', value: 'enquadramento' },
                { text: 'Parecer', value: 'descricao_motivacao' },
            ],
        };
    },
    computed: {
        ...mapGetters({
            dado: 'proposta/historicoEnquadramento',
        }),
    },
    watch: {
        idpreprojeto(value) {
            this.buscarHistoricoEnquadramento(value);
        },
    },
    mounted() {
        if (this.idpreprojeto !== 0) {
            this.buscarHistoricoEnquadramento(this.idpreprojeto);
        }
    },
    methods: {
        ...mapActions({
            buscarHistoricoEnquadramento: 'proposta/buscarHistoricoEnquadramento',
        }),
    },
};
</script>
