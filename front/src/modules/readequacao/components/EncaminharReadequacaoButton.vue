<template>
    <v-layout>
        <v-tooltip bottom>
            <v-icon
                slot="activator"
                color="green darken-3"
                @click.stop="dialog = true"
            >
                forward
            </v-icon>
            <span>Encaminhar Readequação</span>
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
                    :text="'Montando encaminhar readequação...'"
                    class="mt-5"
                />
            </v-card>
            <v-card
                v-else
            >
                Encaminhar readequação
            </v-card>
        </v-dialog>
    </v-layout>
</template>
<script>
import Carregando from '@/components/CarregandoVuetify';

export default {
    name: 'EncaminharReadequacaoButton',
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
