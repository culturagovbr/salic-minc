<template>
    <v-card
        class="mb-2">
        <v-card-title
            primary
            class="title">Natureza</v-card-title>
        <v-card-text>
            <v-layout
                v-if="natureza && natureza.length > 0"
                row
                wrap>
                <v-flex
                    xs12
                    sm3
                    md3>
                    <b>Direito</b><br>
                    {{ natureza.direito }}
                </v-flex>
                <v-flex
                    xs12
                    sm6
                    md6>
                    <b>Esfera</b>
                    <div>{{ natureza.esfera }}</div>
                </v-flex>
                <v-flex
                    xs12
                    sm3
                    md3>
                    <b>Poder</b><br>
                    {{ natureza.poder }}
                </v-flex>
                <v-flex
                    xs12
                    sm3
                    md3>
                    <b>Administração</b><br>
                    {{ natureza.Admins }}
                </v-flex>
            </v-layout>
            <div v-else>Natureza não informada</div>
        </v-card-text>
    </v-card>
</template>
<script>
import { utils } from '@/mixins/utils';

export default {
    name: 'AgenteNatureza',
    mixins: [utils],
    props: {
        natureza: {
            type: [Object, Array],
            default: () => {},
        },
    },
};
</script>
