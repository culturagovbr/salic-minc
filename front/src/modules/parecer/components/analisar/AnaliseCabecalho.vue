<template>
    <v-toolbar
        height="90"
        color="blue-grey darken-2"
        class="white--text"
        dark
    >
        <v-btn
            icon
            class="hidden-xs-only"
            @click="back()"
        >
            <v-icon>arrow_back</v-icon>
        </v-btn>
        <v-toolbar-title class="ml-2">
            <h5 class="headline font-weight-regular">
                {{ $route.meta.title ? $route.meta.title : 'Análise inicial' }}: {{ produto.nomeProduto }}
            </h5>
            <v-divider />
            <div class="subheading mt-1">
                Projeto: {{ produto.pronac }} - {{ produto.nomeProjeto }}
            </div>
        </v-toolbar-title>
        <v-spacer />
        <v-tooltip
            v-if="produto.quantidadeProdutos > 1"
            bottom
        >
            <v-btn
                slot="activator"
                color="grey lighten-3"
                icon
                small
                @click="$emit('visualizarOutrosProdutos', produto)"
            >
                <v-badge
                    color="grey lighten-1"
                    overlap
                    left
                >
                    <span slot="badge">
                        {{ produto.quantidadeProdutos - 1 }}
                    </span>
                    <v-icon
                        color="blue-grey darken-2"
                    >
                        layers
                    </v-icon>
                </v-badge>
            </v-btn>
            <span> Visualizar outros produtos do projeto </span>
        </v-tooltip>
        <v-tooltip
            bottom
        >
            <v-btn
                slot="activator"
                :color="obterConfigDiligencia(produto).cor"
                target="_blank"
                icon
                small
                @click="$emit('visualizarDiligencias', produto)"
            >
                <v-badge
                    :value="produto.diasEmDiligencia > 0"
                    color="grey lighten-1"
                    overlap
                    left
                >
                    <span slot="badge">
                        {{ produto.diasEmDiligencia }}
                    </span>
                    <v-icon
                        :color="obterConfigDiligencia(produto).corIcone"
                    >
                        notification_important
                    </v-icon>
                </v-badge>
            </v-btn>
            <span> {{ obterConfigDiligencia(produto).texto }} </span>
        </v-tooltip>
        <v-tooltip
            bottom
        >
            <v-btn
                slot="activator"
                color="grey lighten-3"
                icon
                small
                @click="$emit('visualizarHistorico', produto)"
            >
                <v-icon color="blue-grey darken-2">
                    history
                </v-icon>
            </v-btn>
            <span>Visualizar histórico de distribuição deste produto</span>
        </v-tooltip>
        <v-chip
            v-if="produto.stPrincipal === 1"
            light
            color="teal lighten-5"
        >
            Produto Principal
        </v-chip>
        <v-chip
            v-else
            light
            color="blue-grey lighten-5"
        >
            Produto Secundário
        </v-chip>
    </v-toolbar>
</template>

<script>

import MxUtils from '@/mixins/utils';
import MxDiligencia from '@/modules/diligencia/mixins/diligencia';

export default {
    name: 'AnaliseCabecalho',
    mixins: [MxUtils, MxDiligencia],
    props: {
        produto: {
            type: Object,
            default: () => {},
        },
        nomeRotaRetorno: {
            type: String,
            default: '',
        },
    },
    methods: {
        back() {
            if (this.nomeRotaRetorno === '') {
                this.$router.go(-1);
                return;
            }

            this.$router.push({ name: this.nomeRotaRetorno });
        },
    },
};
</script>
