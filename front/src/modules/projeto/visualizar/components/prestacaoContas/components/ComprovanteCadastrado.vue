<template>
    <div>
        <v-card>
            <v-card-title>
                <h6>COMPROVANTES CADASTRADOS</h6>
            </v-card-title>
            <v-data-table
                :pagination.sync="pagination"
                :headers="headers"
                :items="dados.dadosComprovantes"
                class="elevation-1 container-fluid"
            >
                <template
                    slot="items"
                    slot-scope="props">
                    <td class="text-xs-left">{{ props.item.idTipoDocumento | tipoDocumento }}</td>
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
                    <td class="text-xs-center pl-5">R$ {{ props.item.dtEnvio | formatarData }}</td>
                    <td class="text-xs-left">{{ props.item.dsDocumento }}</td>
                </template>
            </v-data-table>
        </v-card>
    </div>
</template>
<script>
import { mapGetters } from 'vuex';
import { utils } from '@/mixins/utils';

export default {
    name: 'ComprovanteCadastrado',
    filters: {
        tipoDocumento(idTipoDocumento) {
            let tipoDocumento = '';
            switch (idTipoDocumento) {
            case 22:
                tipoDocumento = 'Fotos';
                break;
            case 23:
                tipoDocumento = 'Vídeos';
                break;
            case 24:
                tipoDocumento = 'Arquivo';
                break;
            default:
                tipoDocumento = '';
            }
            return tipoDocumento;
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
                    text: 'Tipo de Comprovante',
                    align: 'left',
                    value: 'idTipoDocumento',
                },
                {
                    text: 'Nome do Arquivo',
                    align: 'left',
                    value: 'nmArquivo',
                },
                {
                    text: 'Data de Envio',
                    align: 'center',
                    value: 'dtEnvio',
                },
                {
                    text: 'Observações',
                    align: 'left',
                    value: 'dsDocumento',
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
