<template>
    <s-carregando
        v-if="loading"
        text="Carregando conteúdo"
    />
    <v-container
        v-else
        style="max-width: 800px"
    >
        <v-divider class="mt-3" />

        <v-layout
            my-1
            align-center
        >
            <strong class="mx-3 info--text text--darken-3">
                Pendentes: {{ remainingTasks }}
            </strong>

            <v-divider vertical />

            <strong class="mx-3 black--text">
                Validado: {{ completedTasks }}
            </strong>

            <v-spacer />

            <v-progress-circular
                :value="progress"
                color="#2cbe4e"
                class="mr-2"
            />
        </v-layout>

        <v-divider class="mb-3" />

        <v-card v-if="tasks.length > 0">
            <v-slide-y-transition
                class="py-0"
                group
                tag="v-list"
            >
                <template v-for="(task, i) in tasks">
                    <v-divider
                        v-if="i !== 0"
                        :key="`${i}-divider`"
                    />

                    <v-list-tile
                        :key="`${i}-${task.label}`"
                        two-line
                    >
                        <v-list-tile-content>
                            <v-list-tile-title
                                :class="task.done && 'text--primary' || 'grey--text'"
                                v-text="task.label"
                            />
                            <v-list-tile-sub-title
                                v-if="!task.done && !task.loading"
                                class="red--text"
                                v-text="task.error"
                            />
                        </v-list-tile-content>
                        <v-spacer />
                        <v-progress-circular
                            v-if="!task.done && task.loading"
                            :width="3"
                            color="primary"
                            small
                            indeterminate
                        />
                        <v-list-tile-action>
                            <v-scroll-x-transition>
                                <v-icon
                                    v-if="task.done && !task.loading"
                                    color="success"
                                >
                                    check
                                </v-icon>
                                <v-icon
                                    v-if="!task.done && !task.loading"
                                    color="red"
                                >
                                    clear
                                </v-icon>
                            </v-scroll-x-transition>
                        </v-list-tile-action>
                    </v-list-tile>
                </template>
            </v-slide-y-transition>
        </v-card>
        <div class="text-xs-center mt-3">
            <v-btn
                :disabled="remainingTasks > 0 || loadingButton"
                :loading="loadingButton"
                color="primary"
                @click="finalizarParecer()"
            >
                <v-icon left>
                    send
                </v-icon>
                Finalizar parecer
            </v-btn>
        </div>
    </v-container>
</template>

<script>

import { mapActions, mapGetters } from 'vuex';
import SCarregando from '@/components/CarregandoVuetify';

export default {
    components: { SCarregando },
    data: () => ({
        loading: true,
        loadingButton: false,
        tasks: [
            {
                name: 'analise-conteudo',
                label: 'Análise de conteúdo',
                getter: 'analiseConteudo',
                loading: true,
                done: false,
                rules: [(v, self) => (Object.keys(v).length > 0
                    && self.stripTags(v.ParecerDeConteudo).length > 10) || 'Falta parecer da análise de conteúdo'],
                error: '',
            },
            {
                name: 'analise-de-custos',
                label: 'Análise de custos',
                getter: 'planilha',
                action: 'obterPlanilhaParecer',
                loading: true,
                done: false,
                rules: [
                    (v, self) => (v.length > 0 && v.filter(i => i.stCustoPraticadoParc === 1
                    && self.stripTags(i.dsJustificativaParecerista).length < 10).length === 0)
                    || 'Existem itens com o valor praticado e não justificado',
                ],
                error: '',
            },
            {
                name: 'analise-outros-produtos',
                label: 'Outros produtos',
                getter: 'produtosSecundarios',
                action: 'obterProdutosSecundarios',
                loading: true,
                done: false,
                rules: [
                    v => (v.length > 0 && v.filter(i => (i.siAnalise === 0
                        || i.siAnalise === 1)
                        && i.stPrincipal === 0).length === 0)
                        || 'Existem produtos pendentes de análise',
                ],
                error: '',
            },
            {
                name: 'analise-consolidacao',
                label: 'Consolidação do projeto',
                getter: 'consolidacao',
                action: 'obterConsolidacao',
                loading: true,
                done: false,
                rules: [(v, self) => (Object.keys(v).length > 0 && v.ResumoParecer
                    && self.stripTags(v.ResumoParecer).length >= 10) || 'Falta parecer da consolidação'],
                error: '',
            },
            {
                name: 'analise-diligencia',
                label: 'Diligências',
                getter: 'produto',
                loading: true,
                done: false,
                rules: [v => (Object.keys(v).length > 0
                    && (v.stDiligencia !== 1 && v.stDiligencia !== 3))
                    || 'Existe diligência pendente'],
                error: '',
            },
        ],
        task: null,
    }),
    computed: {
        ...mapGetters({
            produto: 'parecer/getProduto',
            analiseConteudo: 'parecer/getAnaliseConteudo',
            planilha: 'parecer/getPlanilhaParecer',
            consolidacao: 'parecer/getConsolidacao',
            produtosSecundarios: 'parecer/getProdutosSecundarios',
        }),
        completedTasks() {
            return this.tasks.filter(task => task.done).length;
        },
        progress() {
            return this.completedTasks / this.tasks.length * 100;
        },
        remainingTasks() {
            return this.tasks.length - this.completedTasks;
        },
    },
    mounted() {
        this.removerChecks();

        const params = {
            id: this.$route.params.id,
            idPronac: this.$route.params.idPronac,
            stPrincipal: this.$route.params.produtoPrincipal,
        };
        // cria observadores para as tasks
        this.tasks.forEach((task, index) => {
            this.$watch(
                task.getter, () => {
                    this.checkTask(task, index);
                },
                { deep: true },
            );
            if (task.action) {
                this[task.action](params);
            } else {
                this.checkTask(task, index);
            }
        });
    },
    methods: {
        ...mapActions({
            obterAnaLiseConteudo: 'parecer/obterAnaLiseConteudo',
            obterPlanilhaParecer: 'parecer/obterPlanilhaParaAnalise',
            obterProdutosSecundarios: 'parecer/obterProdutosSecundarios',
            obterConsolidacao: 'parecer/obterConsolidacao',
            finalizarAnalise: 'parecer/finalizarAnalise',
        }),
        checkTask(task, index) {
            if (!!this[task.getter].length
                || !!Object.keys(this[task.getter]).length) {
                this.tasks[index].loading = false;
            }
            if (Array.isArray(task.rules)) {
                task.rules.forEach((f) => {
                    const valid = typeof f === 'function' ? f(this[task.getter], this) : false;
                    if (typeof valid === 'string') {
                        this.tasks[index].error = valid;
                    }
                    this.tasks[index].done = typeof valid === 'boolean' ? valid : false;
                });
            }
        },
        stripTags(string) {
            if (typeof string !== 'string') {
                return string;
            }
            return string.replace(/(<([^>]+)>)/ig, '');
        },
        deleteTaskByName(name) {
            return this.$delete(this.tasks, this.getIndexTaskByName(name));
        },
        getIndexTaskByName(name) {
            return this.tasks.findIndex(element => element.name === name);
        },
        removerChecks() {
            if (this.produto.quantidadeProdutos === 1) {
                this.deleteTaskByName('analise-outros-produtos');
            }
            if (this.produto.stPrincipal !== 1) {
                this.deleteTaskByName('analise-outros-produtos');
                this.deleteTaskByName('analise-consolidacao');
            }

            if (this.produto.tipoAnalise === 1) {
                this.deleteTaskByName('analise-conteudo');
                this.deleteTaskByName('analise-outros-produtos');
                this.deleteTaskByName('analise-consolidacao');
            }
            this.loading = false;
        },
        finalizarParecer() {
            if (this.remainingTasks > 0) {
                return;
            }

            const analise = {
                idPronac: this.produto.idPronac,
                idProduto: this.produto.idProduto,
                idDistribuirParecer: this.produto.idDistribuirParecer,
                situacao: this.produto.situacao,
            };
            this.loadingButton = true;
            const { produto } = this;
            this.finalizarAnalise(analise)
                .then(() => {
                    if (produto.stPrincipal === 0) {
                        this.$router.push({ name: 'parecer-listar-view' });
                    }
                })
                .finally(() => {
                    this.loadingButton = false;
                });
        },
    },
};
</script>
