<template>
    <v-layout>
        <v-btn
            dark
            icon
            flat
            small
            color="blue darken-3"
            @click.stop="dialog = true"
        >
            <v-tooltip bottom>
                <v-icon slot="activator">
                    forward
                </v-icon>
                <span>Visualizar Readequação</span>
            </v-tooltip>
        </v-btn>
        <v-dialog
            v-model="dialog"
            fullscreen
            hide-overlay
            transition="dialog-bottom-transition"
            @keydown.esc="dialog = false"
        >
            <v-card
                v-if="loading"
            >
                <carregando
                    :text="'Montando distribuir readequação...'"
                    class="mt-5"
                />
            </v-card>
            <v-card
                v-else
            >
            </v-card>
        </v-dialog>
    </v-layout>
</template>
<script>
import Carregando from '@/components/CarregandoVuetify';

export default {
    name: 'DistribuirReadequacaoButton',
    components: {
        Carregando,
    },
    data() {
        return {
            loading: false,
            dialog: false,
        };
    },
};
</script>
