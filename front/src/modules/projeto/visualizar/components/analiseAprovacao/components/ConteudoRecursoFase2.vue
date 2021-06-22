<template>
    <v-card>
        <v-card-text>
            <v-container fluid>
                <v-layout
                    justify-space-around
                    row
                    wrap>
                    <v-flex
                        lg12
                        dark>
                        <b>
                            <h4 v-if="recurso.dadosRecurso.tpRecurso === '1'">
                                DADOS DO RECURSO
                            </h4>
                            <h4 v-else>AVALIAÇÃO DO COORDENADOR</h4>
                        </b>
                        <v-divider class="pb-2"/>
                    </v-flex>
                    <v-flex>
                        <span v-html="recurso.dadosRecurso.dsSolicitacaoRecurso"/>
                    </v-flex>
                    <v-flex>
                        <b>Tipo do Recurso</b><br>
                        <span v-html="recurso.dadosRecurso.tpRecursoDesc"/>
                    </v-flex>
                    <v-flex>
                        <b>Tipo da Solicitação</b>
                        <p v-html="recurso.dadosRecurso.tpSolicitacaoDesc"/>
                    </v-flex>
                    <v-flex>
                        <b>Status do Recurso</b>
                        <p>{{ recurso.dadosRecurso.siRecursoDesc }}</p>
                    </v-flex>
                    <v-flex>
                        <b>Dt. Recurso</b>
                        <p>{{ recurso.dadosRecurso.dtSolicitacaoRecurso | formatarData }}</p>
                    </v-flex>
                </v-layout>
                <div>
                    <v-layout
                        justify-space-around
                        row
                        wrap>
                        <v-flex>
                            <b v-if="recurso.dadosRecurso.tpRecurso === '1'">Avaliação do Pedido</b>
                            <b v-else>Descrição</b>
                            <p v-html="recurso.dadosRecurso.dsAvaliacao"/>
                        </v-flex>
                        <v-flex>
                            <b>Dt. Avaliação</b>
                            <p>{{ recurso.dadosRecurso.dtAvaliacao | formatarData }}</p>
                        </v-flex>
                        <v-flex v-if="recurso.dadosRecurso.tpRecurso === '2'">
                            <b>Situação</b>
                            <p>{{ (recurso.dadosRecurso.stAtendimento === 'D') ? 'Deferido': '' }}</p>
                        </v-flex>
                    </v-layout>
                </div>
                <div>
                    <v-layout
                        v-if="recurso.dadosRecurso.tpSolicitacao === 'PI' ||
                            recurso.dadosRecurso.tpSolicitacao === 'EO' ||
                        recurso.dadosRecurso.tpSolicitacao === 'OR'"
                        justify-space-around
                        row
                        wrap>
                        <v-flex
                            lg12
                            dark
                            class="text-xs-left">
                            <b><h4>PARECER TÉCNICO</h4></b>
                            <v-divider class="pb-2"/>
                        </v-flex>
                        <v-flex
                            lg12
                            dark
                            class="text-xs-left">
                            <h6>Produtos: </h6>
                        </v-flex>
                        <v-expansion-panel
                            v-for="(dadosprodutos, index) in recurso.produtosRecurso"
                            :key="index"
                            popout
                            focusable>
                            <v-expansion-panel-content
                                class="elevation-1">
                                <div slot="header">
                                    <b><span v-html="dadosprodutos.Produto"/></b>
                                    <v-chip
                                        v-if="dadosprodutos.stPrincipal === 1"
                                        outline
                                        color="red">Produto Principal
                                    </v-chip>
                                </div>
                                <v-card>
                                    <v-card-text>
                                        <v-container fluid>
                                            <div>
                                                <v-layout
                                                    justify-space-around
                                                    row
                                                    wrap>
                                                    <v-flex
                                                        lg12
                                                        dark>
                                                        <b><h4>ANÁLISE DE CONTEÚDO</h4></b>
                                                        <v-divider class="pb-2"/>
                                                    </v-flex>
                                                    <v-flex>
                                                        <b>Enquadramento Favorável? *</b>
                                                        <p>
                                                            {{
                                                                (dadosprodutos.ParecerFavoravel === 0) ?
                                                                    'Não':
                                                                    'Sim'
                                                            }}
                                                        </p>
                                                    </v-flex>
                                                    <template v-if="recurso.in2017 !== true">
                                                        <v-flex>
                                                            <b>Lei 8.313/91 alterada pela Lei 9.874/1999
                                                            *</b>
                                                            <p>
                                                                {{ (dadosprodutos.Lei8313 === 1) ? 'Sim' :
                                                                'Não'
                                                                }}
                                                            </p>
                                                        </v-flex>
                                                        <v-flex>
                                                            <b>Artigo 3°</b>
                                                            <p>
                                                                {{ (dadosprodutos.Artigo3 === 1) ? 'Sim' :
                                                                'Não'
                                                                }}
                                                            </p>
                                                        </v-flex>
                                                        <v-flex>
                                                            <b>Inciso</b>
                                                            <p>{{ dadosprodutos.IncisoArtigo3 | filtrarIncisos }}</p>
                                                        </v-flex>
                                                        <v-flex>
                                                            <b>Alinea Artigo 3</b>
                                                            <p>{{ dadosprodutos.AlineaArtigo3 }}</p>
                                                        </v-flex>
                                                        <v-flex>
                                                            <b>Artigo Enquadramento</b>
                                                            <p>
                                                                {{ (dadosprodutos.Artigo18 === 1) ? 'Artigo 18' : 'Artigo 26'
                                                                }}
                                                            </p>
                                                        </v-flex>
                                                        <v-flex>
                                                            <b>Alínea Artigo 18</b>
                                                            <p>{{ dadosprodutos.AlineaArtigo18 }}</p>
                                                        </v-flex>
                                                    </template>
                                                </v-layout>
                                                <v-layout
                                                    v-if="recurso.in2017 !== true"
                                                    justify-space-around
                                                    row
                                                    wrap>
                                                    <v-flex
                                                        xs12
                                                        sm3
                                                        md3>
                                                        <b>Decreto 5761/2006 *</b>
                                                        <p>{{ (dadosprodutos.Lei5761 === 1) ? 'Sim' :
                                                        'Não' }}</p>
                                                    </v-flex>
                                                    <v-flex
                                                        xs12
                                                        sm2
                                                        md3>
                                                        <b>Artigo 27°</b>
                                                        <p>{{ (dadosprodutos.Artigo27 === 1) ? 'Sim' :
                                                        'Não' }}</p>
                                                    </v-flex>
                                                    <v-layout
                                                        row
                                                        wrap
                                                        align-start
                                                    >
                                                        <v-flex
                                                            xs12
                                                            sm12
                                                            md12>
                                                            <b>Inciso(s)</b>
                                                        </v-flex>
                                                        <v-flex
                                                            shrink
                                                            ml-4
                                                        >
                                                            <v-checkbox
                                                                v-model="dadosprodutos.IncisoArtigo27_I"
                                                                label="I"
                                                                value
                                                                disabled
                                                            />
                                                        </v-flex>
                                                        <v-flex
                                                            shrink
                                                            ml-4

                                                        >
                                                            <v-checkbox
                                                                v-model="dadosprodutos.IncisoArtigo27_II"
                                                                label="II"
                                                                value
                                                                disabled
                                                            />
                                                        </v-flex>
                                                        <v-flex
                                                            shrink
                                                            ml-4

                                                        >
                                                            <v-checkbox
                                                                v-model="dadosprodutos.IncisoArtigo27_III"
                                                                label="III"
                                                                value
                                                                disabled
                                                            />
                                                        </v-flex>
                                                        <v-flex
                                                            shrink
                                                            ml-4

                                                        >
                                                            <v-checkbox
                                                                v-model="dadosprodutos.IncisoArtigo27_IV"
                                                                label="IV"
                                                                value
                                                                disabled
                                                            />
                                                        </v-flex>
                                                    </v-layout>
                                                </v-layout>
                                                <v-layout
                                                    justify-space-around
                                                    row
                                                    wrap>
                                                    <v-flex>
                                                        <b>Justificativa *</b>
                                                        <p v-html="dadosprodutos.ParecerDeConteudo"/>
                                                    </v-flex>
                                                </v-layout>
                                            </div>
                                        </v-container>
                                    </v-card-text>
                                </v-card>
                            </v-expansion-panel-content>
                        </v-expansion-panel>
                    </v-layout>
                    <v-layout
                        v-if="recurso.dadosRecurso.tpSolicitacao === 'EO' ||
                        recurso.dadosRecurso.tpSolicitacao === 'OR'"
                        justify-space-around
                        row
                        wrap>
                        <v-flex
                            lg12
                            dark>
                            <b><h4>PLANILHA</h4></b>
                            <v-divider class="pb-2"/>
                        </v-flex>
                        <v-flex lg12>
                            <planilha-proposta-autorizada/>
                        </v-flex>
                    </v-layout>
                    <!--Enquadramento -->
                    <v-layout
                        justify-space-around
                        row
                        wrap>
                        <v-flex
                            lg12
                            dark>
                            <b><h4>ENQUADRAMENTO</h4></b>
                            <v-divider class="pb-2"/>
                        </v-flex>
                        <v-flex v-if="Object.keys(recurso.parecerRecurso).length > 0">
                            <v-flex
                                v-for="(dadosParecer, index) in recurso.parecerRecurso"
                                :key="index">
                                <v-flex>
                                    <b>Parecer Favorável?</b>
                                    <p>
                                        {{ (dadosParecer.ParecerFavoravel === '2') ? 'Sim' : 'Não' }}
                                    </p>
                                </v-flex>
                                <v-flex>
                                    <b>Dt. Parecer</b>
                                    <p>
                                        {{ dadosParecer.DtParecer | formatarData }}
                                    </p>
                                </v-flex>
                            </v-flex>
                        </v-flex>
                        <v-flex>
                            <b>Área</b>
                            <p v-html="recurso.projetosENRecurso.area"/>
                        </v-flex>
                        <v-flex>
                            <b>Segmento</b>
                            <p v-html="recurso.projetosENRecurso.segmento"/>
                        </v-flex>
                        <v-flex>
                            <b>Enquadramento</b>
                            <p>{{ recurso.projetosENRecurso.artigo }}</p>
                        </v-flex>
                    </v-layout>
                    <!--resumo do parecer-->
                    <v-layout
                        v-if="recurso.dadosRecurso.siRecurso === '9 '"
                        justify-space-around
                        row
                        wrap>
                        <v-flex
                            v-for="(parecer, index) in recurso.parecerRecurso"
                            :key="index">
                            <b>Resumo do Parecer</b>
                            <p v-html="parecer.ResumoParecer"/>
                        </v-flex>
                    </v-layout>
                </div>
            </v-container>
        </v-card-text>
    </v-card>
</template>

<script>
import { utils } from '@/mixins/utils';
import PlanilhaPropostaAutorizada from '../../incentivo/planilha/PlanilhaHomologada';

export default {
    name: 'ConteudoRecursoFase2',
    components: { PlanilhaPropostaAutorizada },
    filters: {
        filtrarIncisos(tipo) {
            let inciso = '';
            switch (tipo) {
            case 1:
                inciso = 'Inciso I';
                break;
            case 2:
                inciso = 'Inciso II';
                break;
            case 3:
                inciso = 'Inciso III';
                break;
            case 4:
                inciso = 'Inciso IV';
                break;
            case 5:
                inciso = 'Inciso V';
                break;
            default:
                inciso = '';
            }
            return inciso;
        },
    },
    mixins: [utils],
    props: {
        recurso: {
            type: Object,
            default: () => {
            },
        },
    },
};
</script>
