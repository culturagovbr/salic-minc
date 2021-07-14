<template>
    <div class="text-xs-center">
        <v-dialog
            v-model="dialog"
            width="90%"
        >
            <v-card>
                <v-card-title
                    class="headline grey lighten-2"
                    primary-title
                >
                    Visualizar Comprovantes
                </v-card-title>
                <v-subheader>Item de custo: {{ item }}</v-subheader>

                <v-card-text>
                    <lista-de-comprovantes :comprovantes="comprovantes"/>
                </v-card-text>
                <v-divider/>

                <v-card-actions>
                    <v-spacer/>
                    <v-btn
                        color="red lighten-2"
                        flat
                        @click="dialog = false"
                    >
                        Fechar
                    </v-btn>
                </v-card-actions>
            </v-card>
        </v-dialog>
    </div>
</template>

<script>
import { mapActions, mapGetters } from 'vuex';
import ListaDeComprovantes from './ListaDeComprovantes';

export default {
    name: 'ModalDetalheItens',
    components: { ListaDeComprovantes },
    props: {
        item: { type: String, default: '' },
        idPronac: { type: String, default: '' },
        uf: { type: String, default: '' },
        codigoCidade: { type: Number, default: 0 },
        codigoProduto: { type: Number, default: 0 },
        stItemAvaliado: { type: String, default: '' },
        codigoEtapa: { type: Number, default: 0 },
        idPlanilhaItens: { type: Number, default: 0 },
    },
    data() {
        return {
            dialog: false,
            currentComprovantes: [],
        };
    },
    computed: {
        ...mapGetters({
            comprovantes: 'avaliacaoResultados/comprovantes',
            isModalVisible: 'modal/default',
        }),
    },
    watch: {
        comprovantes(value) {
            this.currentComprovantes = value;
        },
        isModalVisible(value) {
            if (value === 'visualizar-comprovantes') {
                this.dialog = true;
                this.buscar();
            }
        },
        dialog(value) {
            if (value === false) {
                this.modalClose();
            }
        },
    },
    methods: {
        ...mapActions({
            buscarComprovantes: 'avaliacaoResultados/buscarComprovantes',
            modalClose: 'modal/modalClose',
        }),
        buscar() {
            this.currentComprovantes = [];

            const params = {
                uf: this.uf,
                idPronac: this.idPronac,
                codigoCidade: this.codigoCidade,
                codigoProduto: this.codigoProduto,
                stItemAvaliado: this.stItemAvaliado,
                codigoEtapa: this.codigoEtapa,
                idPlanilhaItens: this.idPlanilhaItens,
            };

            this.buscarComprovantes(params);
        },
    },
};
</script>
