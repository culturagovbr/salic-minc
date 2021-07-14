<template>
    <v-container>
        <v-layout
            row
            wrap
            align-center
        >
            <v-flex
                offset-xs1
                xs1
                class="text-xs-center"
            >
                <v-icon
                    size="56"
                    class="red--text mr-2"
                >not_interested
                </v-icon>
            </v-flex>
            <v-flex
                xs3
                class="text-xs-left"
            >
                <strong v-html="texto"/>
                <v-btn
                    v-if="urlRetorno"
                    :href="urlRetorno"
                    outline
                    color="error"
                    v-html="msgUrlRetorno"
                />
                <v-btn
                    v-if="rotaRetorno"
                    :to="rotaRetorno"
                    outline
                    color="error"
                    v-html="msgUrlRetorno"
                />
            </v-flex>
        </v-layout>
    </v-container>
</template>

<script>
export default {
    name: 'SalicMensagemErro',
    props: {
        texto: {
            type: String,
            default: '',
        },
        urlRetorno: {
            type: String,
            default: '',
        },
        msgUrlRetorno: {
            type: String,
            default: 'Voltar',
        },
        rotaRetorno: {
            type: [String, Object],
            default: '',
        },
    },
};
</script>
