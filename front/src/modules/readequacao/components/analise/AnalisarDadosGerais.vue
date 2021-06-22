<template>
    <v-card tile>
        <v-card-text>
            <div class="mb-4">
                <h3 class="mb-4 mt-4">Data da solicitação</h3>
                <div>
                    {{ dadosReadequacao.dtSolicitacao | formatarData }}
                </div>
            </div>
            <div class="mb-4">
                <h3 class="mb-4">Justificativa da solicitação</h3>
                <div
                    v-html="dadosReadequacao.dsJustificativa"
                />
            </div>
            <div
                v-if="dadosReadequacao.idDocumento"
                class="mb-4">
                <h3 class="mb-4">Anexo</h3>
                <v-btn
                    flat
                    color="blue darken-3"
                    @click="abrirArquivo(dadosReadequacao.idDocumento)"
                >
                    visualizar anexo
                    <v-icon
                        dark
                        color="blue darken-3"
                        class="white--text">
                        insert_drive_file
                    </v-icon>
                </v-btn>
            </div>
        </v-card-text>
    </v-card>
</template>
<script>
import { mapGetters } from 'vuex';
import { utils } from '@/mixins/utils';
import MxReadequacao from '../../mixins/Readequacao';

export default {
    name: 'AnalisarDadosGerais',
    mixins: [
        utils,
        MxReadequacao,
    ],
    computed: {
        ...mapGetters({
            dadosReadequacao: 'readequacao/getReadequacao',
        }),
    },
};
</script>
