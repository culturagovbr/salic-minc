<template>
    <v-card
        v-if="identificacao"
        class="mb-2">
        <v-card-title
            primary
            class="title">Identificação</v-card-title>
        <v-card-text>
            <v-layout
                row
                wrap>
                <v-flex
                    xs12
                    sm3
                    md3>
                    <b>CNPJ/CPF</b><br>
                    {{ identificacao.cnpjcpf | formatarCpfOuCnpj }}
                </v-flex>
                <v-flex
                    xs12
                    sm6
                    md6>
                    <b>Nome Proponente</b>
                    <div>{{ identificacao.descricao }}</div>
                </v-flex>
                <v-flex
                    xs12
                    sm3
                    md3>
                    <b>Tipo de Pessoa</b><br>
                    {{ identificacao.tipopessoa | formatarTipoPessoa }}
                </v-flex>
            </v-layout>
        </v-card-text>
    </v-card>
</template>
<script>
import { utils } from '@/mixins/utils';

export default {
    name: 'AgenteIdentificacao',
    mixins: [utils],
    props: {
        identificacao: {
            type: Object,
            default: () => {},
        },
    },
};
</script>
