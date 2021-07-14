<template>
    <div>
        <v-card>
            <v-card-title>
                <h6>BENEFICIÁRIOS DE PRODUTO CULTURAL</h6>
            </v-card-title>
            <v-data-table
                :pagination.sync="pagination"
                :headers="headers"
                :items="dados.planosCadastrados"
                class="elevation-1 container-fluid"
            >
                <template
                    slot="items"
                    slot-scope="props">
                    <td class="text-xs-left">{{ props.item.Produto }}</td>
                    <td class="text-xs-left">{{ props.item.idTipoBeneficiario | tipoBeneficiario }}</td>
                    <td
                        class="text-xs-left"
                        style="white-space: nowrap"
                    >
                        {{ props.item.CNPJCPF | cnpjFilter }}
                    </td>
                    <td class="text-xs-left">{{ props.item.Beneficiario }}</td>
                    <td class="text-xs-right">{{ props.item.qtRecebida }}</td>
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
                </template>
            </v-data-table>
        </v-card>
    </div>
</template>
<script>
import { mapGetters } from 'vuex';
import { utils } from '@/mixins/utils';
import cnpjFilter from '@/filters/cnpj';

export default {
    name: 'BeneficiariosProdutoCultural',
    filters: {
        cnpjFilter,
        tipoBeneficiario(idTipoBeneficiario) {
            let tipoBeneficiario = '';
            switch (idTipoBeneficiario) {
            case 19:
                tipoBeneficiario = 'Patrocinador';
                break;
            case 20:
                tipoBeneficiario = 'Divulgação';
                break;
            default:
                tipoBeneficiario = 'Beneficiário';
            }
            return tipoBeneficiario;
        },
    },
    mixins: [utils],
    data() {
        return {
            pagination: {
                sortBy: '',
                descending: true,
            },
            headers: [
                {
                    text: 'Produto',
                    align: 'left',
                    value: 'Produto',
                },
                {
                    text: 'Tipo Beneficiário',
                    align: 'left',
                    value: 'idTipoBeneficiario',
                },
                {
                    text: 'CNPJ/CPF',
                    align: 'left',
                    value: 'CNPJCPF',
                },
                {
                    text: 'Nome',
                    align: 'left',
                    value: 'Beneficiario',
                },
                {
                    text: 'Quantidade',
                    align: 'right',
                    value: 'qtRecebida',
                },
                {
                    text: 'Arquivo',
                    align: 'left',
                    value: 'nmArquivo',
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
