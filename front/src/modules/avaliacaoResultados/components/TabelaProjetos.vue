<template>
    <v-data-table
            :headers="cabecalho"
            :items="dadosTabelaTecnico.items"
            hide-actions
            class="elevation-1"
    >
        <template slot="items" slot-scope="props">
            <td>{{ props.index+1 }}</td>
            <td class="text-xs-right">
                <v-flex xs12 sm4 text-xs-center>
                    <div>
                        <v-btn :href="'/projeto/#/incentivo/'+ props.item.idPronac">{{ props.item.Pronac }}</v-btn>
                    </div>
                </v-flex>
            </td>
            <td class="text-xs-right">{{ props.item.NomeProjeto }}</td>
            <td class="text-xs-right">{{ props.item.Situacao }}</td>
            <td class="text-xs-right">{{ props.item.Area }}/{{ props.item.Segmento }}</td>
            <td class="text-xs-right">{{ props.item.UfProjeto }}</td>
            <td class="text-xs-right">{{ props.item.Mecanismo }}</td>
            <td class="text-xs-right">{{ props.item.DtSituacao }}</td>
            <td class="text-xs-right">
                <v-btn flat icon color="green" :to="{ name: 'tipoAvaliacao', params:{ id:props.item.idPronac }}">
                    <v-icon class="material-icons">compare_arrows</v-icon>
                </v-btn>
            </td>
            <td class="text-xs-right">
                <v-btn flat icon color="indigo" :href="'/proposta/diligenciar/listardiligenciaanalista?idPronac='+ props.item.idPronac +'&situacao=E17&tpDiligencia=174'">
                    <v-icon>warning</v-icon>
                </v-btn>
            </td>
            <td class="text-xs-right">
                <Historico
                        :id-pronac="props.item.idPronac"
                        :pronac="props.item.Pronac"
                        :nome-projeto="props.item.NomeProjeto"
                ></Historico>

            </td>
        </template>
        <template slot="no-data">
            <v-alert :value="true" color="error" icon="warning">
                Nenhum dado encontrado ¯\_(?)_/¯
            </v-alert>
        </template>
    </v-data-table>
</template>

<script>
    import { mapActions, mapGetters } from 'vuex';
    import ModalTemplate from '@/components/modal';
    import Historico from './Historico';

    export default {
        name: 'Painel',
        created() {
            this.obterDadosTabelaTecnico();
        },

        data() {
            return {
                cabecalho: [
                    {
                        text: '#',
                        align: 'left',
                        sortable: false,
                        value: 'numero',
                    },
                    { text: 'PRONAC', value: 'pronac' },
                    { text: 'Nome Do Projeto', value: 'nome' },
                    { text: 'Situacao', value: 'situacao' },
                    { text: 'Area/Segmento', value: 'area' },
                    { text: 'Estado', value: 'estado' },
                    { text: 'Mecanismo', value: 'mecanismo' },
                    { text: 'Dt.recebimento', value: 'data' },
                    { text: 'Analisar', value: 'analisar' },
                    { text: 'Diligencia', value: 'diligencia' },
                    { text: 'Historico', value: 'historico' },

                ],
            };
        },
        components: {
            ModalTemplate,
            Historico,
        },
        methods: {
            ...mapActions({
                obterDadosTabelaTecnico: 'avaliacaoResultados/obterDadosTabelaTecnico',
            }),
        },
        computed: {
            ...mapGetters({
                dadosTabelaTecnico: 'avaliacaoResultados/dadosTabelaTecnico',
            }),
        },
    };
</script>
