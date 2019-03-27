<template>
    <div>
        <div v-if="loading">
            <Carregando :text="'Carregando Certidoes Negativas'"/>
        </div>
        <div v-else-if="dados.certidoes">
            <v-data-table
                :pagination.sync="pagination"
                :headers="headers"
                :items="dados.certidoes"
                class="elevation-1 container-fluid"
            >
                <template
                    slot="items"
                    slot-scope="props">
                    <td
                        class="text-xs-left"
                        v-html="props.item.dsCertidao"/>
                    <td class="text-xs-center pl-5">{{ props.item.DtEmissao | formatarData }}</td>
                    <td class="text-xs-center pl-5">{{ props.item.DtValidade | formatarData }}</td>
                    <td class="text-xs-right">{{ props.item.Pronac }}</td>
                    <td
                        v-if="props.item.Situacao"
                        class="text-xs-left"
                        v-html="props.item.Situacao"/>
                    <td
                        v-else
                        class="text-xs-left">
                        Vencida
                    </td>
                </template>
                <template
                    slot="pageText"
                    slot-scope="props">
                    Items {{ props.pageStart }} - {{ props.pageStop }} de {{ props.itemsLength }}
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
    name: 'CertidoesNegativas',
    components: {
        Carregando,
    },
    mixins: [utils],
    data() {
        return {
            search: '',
            pagination: {
                sortBy: 'DtEmissao',
                descending: true,
            },
            selected: [],
            loading: true,
            headers: [
                {
                    text: 'CERTIDÕES',
                    align: 'left',
                    value: 'dsCertidao',
                },
                {
                    text: 'DATA DE EMISSÃO',
                    align: 'center',
                    value: 'DtEmissao',
                },
                {
                    text: 'DATA DE VALIDADE',
                    align: 'center',
                    value: 'DtValidade',
                },
                {
                    text: 'PRONAC',
                    align: 'center',
                    value: 'Pronac',
                },
                {
                    text: 'SITUAÇÃO',
                    align: 'left',
                    value: 'Situacao',
                },
            ],
        };
    },
    computed: {
        ...mapGetters({
            dadosProjeto: 'projeto/projeto',
            dados: 'outrasInformacoes/certidoesNegativas',
        }),
    },
    watch: {
        dados() {
            this.loading = false;
        },
    },
    mounted() {
        if (typeof this.dadosProjeto.idPronac !== 'undefined') {
            this.buscarCertidoesNegativas(this.dadosProjeto.idPronac);
        }
    },
    methods: {
        ...mapActions({
            buscarCertidoesNegativas: 'outrasInformacoes/buscarCertidoesNegativas',
        }),
    },
};
</script>
