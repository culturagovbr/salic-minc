<template>
    <v-layout>
        <v-tooltip bottom>
            <v-icon
                slot="activator"
                color="blue darken-3"
                class="material-icons"
                @click.stop="dialog = true"
            >
                forward
            </v-icon>
            <span>Visualizar Readequação</span>
        </v-tooltip>
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
                Distribuir readequação
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
