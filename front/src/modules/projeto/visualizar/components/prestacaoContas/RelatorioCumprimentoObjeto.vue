<template>
    <div>
        <div v-if="loading">
            <Carregando :text="'Relatório de cumprimento do objeto'"/>
        </div>
        <div v-else-if="Object.keys(dados).length > 0">
            <v-container
                v-if="Object.keys(dados.dadosRelatorio).length > 0"
                fluid>
                <EtapaTrabalho/>
            </v-container>

            <v-container
                v-if="Object.keys(dados.dadosItensOrcamentarios).length > 0"
                fluid>
                <ComprovacaoItensOrcamentarios/>
            </v-container>
            <v-container
                v-if="Object.keys(dados.locaisRealizacao).length > 0"
                fluid>
                <locaisRealizacao/>
            </v-container>
            <v-container
                v-if="Object.keys(dados.planoDeDivulgacao).length > 0"
                fluid>
                <PlanoDivulgacao/>
            </v-container>

            <v-container
                v-if="Object.keys(dados.dadosCompMetas).length > 0"
                fluid>
                <ComprovacaoMetas/>
            </v-container>

            <v-container
                v-if="Object.keys(dados.dadosComprovantes).length > 0"
                fluid>
                <ComprovanteCadastrado/>
            </v-container>

            <v-container
                v-if="Object.keys(dados.planoDistribuicao).length > 0"
                fluid>
                <PlanoDistribuicao/>
            </v-container>

            <v-container
                v-if="Object.keys(dados.bensCadastrados).length > 0"
                fluid>
                <BensImoveisMoveisDoados/>
            </v-container>

            <v-container
                v-if="Object.keys(dados.planosCadastrados).length > 0"
                fluid>
                <BeneficiariosProdutoCultural/>
            </v-container>

            <v-container
                v-if="dados.isPermitidoVisualizarRelatorio && dados.dadosRelatorio.siCumprimentoObjeto === '6'"
                fluid>
                <ParecerAvaliacaoTecnica/>
            </v-container>
            <v-container
                v-if="Object.keys(dados.aceiteObras).length > 0"
                fluid>
                <AceiteObra/>
            </v-container>
        </div>
        <v-layout v-else>
            <v-container
                grid-list-md
                text-xs-center>
                <v-layout
                    row
                    wrap>
                    <v-flex>
                        <v-card>
                            <v-card-text class="px-0">Nenhum Relatório encontrado</v-card-text>
                        </v-card>
                    </v-flex>
                </v-layout>
            </v-container>
        </v-layout>
    </div>
</template>
<script>

import { mapActions, mapGetters } from 'vuex';
import Carregando from '@/components/CarregandoVuetify';
import { utils } from '@/mixins/utils';
import EtapaTrabalho from './components/EtapaTrabalho';
import ComprovacaoItensOrcamentarios from './components/ComprovacaoItensOrcamentarios';
import BeneficiariosProdutoCultural from './components/BeneficiariosProdutoCultural';
import PlanoDivulgacao from './components/PlanoDivulgacao';
import LocaisRealizacao from './components/LocaisRealizacao';
import ComprovacaoMetas from './components/ComprovacaoMetas';
import ComprovanteCadastrado from './components/ComprovanteCadastrado';
import BensImoveisMoveisDoados from './components/BensImoveisMoveisDoados';
import AceiteObra from './components/AceiteObra';
import PlanoDistribuicao from './components/PlanoDistribuicao';
import ParecerAvaliacaoTecnica from './components/ParecerAvaliacaoTecnica';

export default {
    name: 'RelatorioCumprimentoObjeto',
    components: {
        Carregando,
        EtapaTrabalho,
        ComprovacaoItensOrcamentarios,
        BeneficiariosProdutoCultural,
        PlanoDivulgacao,
        LocaisRealizacao,
        AceiteObra,
        BensImoveisMoveisDoados,
        PlanoDistribuicao,
        ComprovanteCadastrado,
        ComprovacaoMetas,
        ParecerAvaliacaoTecnica,
    },
    mixins: [utils],
    data() {
        return {
            pagination: {
                sortBy: '',
                descending: true,
            },
            loading: true,
        };
    },
    computed: {
        ...mapGetters({
            dadosProjeto: 'projeto/projeto',
            dados: 'prestacaoContas/relatorioCumprimentoObjeto',
        }),
    },
    watch: {
        dadosProjeto(value) {
            this.loading = true;
            this.buscarRelatorioCumprimentoObjeto(value.idPronac);
        },
        dados() {
            this.loading = false;
        },
    },
    mounted() {
        if (typeof this.dadosProjeto.idPronac !== 'undefined') {
            this.buscarRelatorioCumprimentoObjeto(this.dadosProjeto.idPronac);
        }
    },
    methods: {
        ...mapActions({
            buscarRelatorioCumprimentoObjeto: 'prestacaoContas/buscarRelatorioCumprimentoObjeto',
        }),
    },
};
</script>
