<template>
    <div>
        <div v-if="loading">
            <Carregando :text="'Carregando Dados Complementares do Projeto'"/>
        </div>
        <div v-else-if="Object.keys(dados.Proposta).length === 0 && dados.CustosVinculados.length === 0 ">
            <v-container
                grid-list-md
                text-xs-center>
                <v-layout
                    row
                    wrap>
                    <v-flex>
                        <v-card>
                            <v-card-text class="px-0">Nenhum Dados Complementares do Projeto
                            encontrado
                            </v-card-text>
                        </v-card>
                    </v-flex>
                </v-layout>
            </v-container>
        </div>
        <div v-else>
            <DadosComplementares
                :texto="dados.Proposta.Objetivos"
                label="Objetivos"
                icon="playlist_add_check"/>
            <DadosComplementares
                :texto="dados.Proposta.Justificativa"
                label="Justificativa"/>
            <DadosComplementares
                :itens="dados.CustosVinculados"
                label="Custos Vinculados"
                icon="attach_money"
            >
                <template slot-scope="slotProps">
                    <custos-vinculados
                        :custos-vinculados="slotProps.itens"
                    />
                </template>
            </DadosComplementares>
            <DadosComplementares
                :texto="dados.Proposta.Acessibilidade"
                label="Acessibilidade"
                icon="accessible"/>
            <DadosComplementares
                :texto="dados.Proposta.DemocratizacaoDeAcesso"
                label="Democratiza&ccedil;&atilde;o de Acesso"
                icon="accessibility"/>
            <DadosComplementares
                :texto="dados.Proposta.EtapaDeTrabalho"
                label="Etapa de Trabalho"
                icon="date_range"/>
            <DadosComplementares
                :texto="dados.Proposta.FichaTecnica"
                label="Ficha T&eacute;cnica"
                icon="assignment"/>
            <DadosComplementares
                :texto="dados.Proposta.Sinopse"
                label="Sinopse da Obra"
                icon="burst_mode"/>
            <DadosComplementares
                :texto="dados.Proposta.ImpactoAmbiental"
                label="Impacto Ambiental"
                icon="public"/>
            <DadosComplementares
                :texto="dados.Proposta.EspecificacaoTecnica"
                label="Especifica&ccedil;&otilde;es t&eacute;cnicas do produto"
                icon="assignment"/>
            <DadosComplementares
                :texto="dados.Proposta.OutrasInformacoes"
                label="Outras Informa&ccedil;&otilde;es"
                icon="info_outline"/>
            <DadosComplementares
                :texto="dados.Proposta.DescricaoAtividade"
                label="Descrição de Atividades"
                icon="timeline"/>
        </div>

    </div>
</template>
<script>
import { mapActions, mapGetters } from 'vuex';
import Carregando from '@/components/CarregandoVuetify';
import TextoDadosComplementares from './TextoDadosComplementares';
import DadosComplementares from './DadosComplementares';
import CustosVinculados from './CustosVinculados';

export default {
    name: 'DadosComplementaresView',
    components: {
        CustosVinculados,
        DadosComplementares,
        Carregando,
        TextoDadosComplementares,
    },
    data() {
        return {
            loading: true,
        };
    },
    computed: {
        ...mapGetters({
            dadosProjeto: 'projeto/projeto',
            dados: 'outrasInformacoes/dadosComplementares',
        }),
    },
    watch: {
        dadosProjeto(value) {
            this.loading = true;
            this.buscarDadosComplementares(value.idPronac);
        },
        dados() {
            this.loading = false;
        },
    },
    mounted() {
        if (typeof this.dadosProjeto.idPronac !== 'undefined') {
            this.buscarDadosComplementares(this.dadosProjeto.idPronac);
        }
    },
    methods: {
        ...mapActions({
            buscarDadosComplementares: 'outrasInformacoes/buscarDadosComplementares',
        }),
    },
};
</script>
