<template>
    <div class="content">
        <div class="row card-panel">
            <div class="col s12">
                <div
                    class=""
                    style="text-align: center; display: table; vertical-align: top; margin: 0 auto">
                    <div
                        v-for="(fase, key) of fases"
                        :key="fase.id"
                        style="display: table-cell;">
                        <i
                            v-if="key > 0"
                            class="tiny material-icons grey-text">forward</i>
                        <a
                            :class="{ disabled : dadosProposta.fase_proposta !== fase.id }"
                            :data-tooltip="fase.descricao"
                            :href="(fase.link) ? fase.link : 'javascript:void(0)'"
                            :style="(fase.link) ? '' : 'cursor: default'"
                            class="margin10 btn small btn-primary tooltipped">
                            <i class="tiny material-icons left">{{ fase.icon }}</i>
                            <span v-html="fase.label"/>
                        </a>
                        <div
                            v-for="(children) of fase.childrens"
                            :key="children.id"
                            style="display:block;"
                        >
                            <template v-if="dadosProposta && fase.childrens">
                                <div>
                                    <i
                                        class="tiny material-icons grey-text"
                                        style="transform: rotate(90deg);">forward</i>
                                </div>
                                <a
                                    :data-tooltip="children.descricao"
                                    :class="{
                                        disabled : dadosProposta.fase_proposta !== children.id
                                    }"
                                    :href="(children.link) ? children.link : 'javascript:void(0)'"
                                    :style="(children.link) ? '' : 'cursor: default'"
                                    class="margin10 btn small btn-primary tooltipped">
                                    <i class="tiny material-icons left">{{ children.icon }}</i>
                                    <span v-html="children.label"/>
                                </a>
                            </template>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import { mapGetters } from 'vuex';

export default {
    props: {
        idPreProjeto: {
            type: Number,
            required: true,
        },
    },
    data() {
        return {
            fases: [
                {
                    id: 'proposta_com_proponente',
                    label: 'Em edição',
                    descricao: 'A proposta está disponível para edição.',
                    link: `/proposta/manterpropostaincentivofiscal/identificacaodaproposta/idPreProjeto/${this.idPreProjeto}`,
                    icon: 'create',
                },
                {
                    id: 'proposta_analise_inicial',
                    label: 'Em Avalia&ccedil;&atilde;o',
                    descricao: 'A proposta está em avaliação pelo Ministério da Cidadania, acompanhe na aba histórico de avaliações.',
                    icon: 'how_to_reg',
                    childrens: [
                        {
                            id: 'proposta_arquivada',
                            label: 'Arquivada',
                            descricao: 'Proposta arquivada, acesse a aba histórico de avaliações para saber mais.',
                            link: '/proposta/manterpropostaincentivofiscal/listar-propostas-arquivadas',
                            icon: 'archive',
                        },
                    ],
                },
                {
                    id: 'proposta_em_enquadramento',
                    label: 'Enquadramento',
                    descricao: 'Sua proposta está em enquadramento.',
                    icon: 'build',
                    childrens: [
                        {
                            id: 'recurso_enquadramento',
                            label: 'Recurso',
                            descricao: 'Sua proposta foi enquadrada, caso discorde, você pode interpor recurso.',
                            link: `/recurso/recurso-proposta/visao-proponente/idPreProjeto/${this.idPreProjeto}`,
                            icon: 'feedback',
                        },
                    ],
                },
                {
                    id: 'projeto_cultural',
                    label: 'Projeto',
                    descricao: 'A proposta já é um projeto cultural.',
                    icon: 'beenhere',
                },
            ],
        };
    },
    computed: {
        ...mapGetters({
            dadosProposta: 'proposta/proposta',
        }),
    },
};
</script>
