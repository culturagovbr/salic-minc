<template>
    <v-tooltip
        bottom
    >
        <v-btn
            slot="activator"
            :color="configuracaoDiligencia.cor"
            target="_blank"
            icon
            small
            @click="$emit('click', item)"
        >
            <v-badge
                :value="diasEmDiligencia > 0"
                :color="configuracaoDiligencia.corBadge"
                overlap
                left
            >
                <span slot="badge">{{ diasEmDiligencia }}</span>
                <v-icon
                    :color="configuracaoDiligencia.corIcone"
                >
                    notification_important
                </v-icon>
            </v-badge>
        </v-btn>
        <span> {{ configuracaoDiligencia.texto }} </span>
    </v-tooltip>
</template>

<script>
import moment from 'moment';

export default {
    name: 'DiligenciaButton',
    props: {
        item: {
            type: Object,
            default: () => {},
        },
    },
    computed: {
        diasEmDiligencia() {
            if (!this.item || !this.item.DtSolicitacao) {
                return 0;
            }

            return this.diasData(this.item.DtSolicitacao);
        },
        prazoExpirado() {
            const prazoDiasDiligencia = 20;
            return this.diasEmDiligencia >= prazoDiasDiligencia;
        },
        statusDiligencia() {
            let status = 0;

            if (!this.item || !this.item.DtSolicitacao) {
                return 0;
            }

            if (this.item.DtSolicitacao && this.item.DtResposta) {
                status = 2;
            } else if (this.item.DtSolicitacao && this.prazoExpirado) {
                status = 3;
            } else if (this.item.DtSolicitacao && !this.item.DtResposta) {
                status = 1;
            }

            return status;
        },
        configuracaoDiligencia() {
            let diligencia = {};
            switch (this.statusDiligencia) {
            case 1:
                diligencia = {
                    cor: 'yellow accent-4',
                    corIcone: 'yellow darken-4',
                    corBadge: 'grey lighten-1',
                    texto: `Diligenciado há ${this.diasEmDiligencia} dia(s)`,
                };
                break;
            case 2:
                diligencia = {
                    cor: 'green lighten-3',
                    corIcone: 'green darken-4',
                    corBadge: 'grey lighten-1',
                    texto: 'Diligência respondida',
                };
                break;
            case 3:
                diligencia = {
                    cor: 'orange lighten-3',
                    corIcone: 'orange darken-4',
                    corBadge: 'red lighten-1',
                    texto: `Diligenciado há ${this.diasEmDiligencia} dia(s)`,
                };
                break;
            default:
                diligencia = {
                    cor: 'grey lighten-3',
                    corIcone: 'blue-grey darken-2',
                    corBadge: 'grey lighten-1',
                    texto: 'Disponível para diligência',
                };
                break;
            }
            return diligencia;
        },
    },
    methods: {
        diasData(data) {
            if (typeof data === 'undefined') {
                return 0;
            }

            return moment().diff(data, 'days');
        },
    },
};
</script>
