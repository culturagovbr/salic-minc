<template>
    <v-layout>
        <v-flex
            md6
            sm12
            xs12
        >
            <v-card
                flat
            >
                <v-card-title
                    class="subheading"
                >
                    <v-btn
                        fab
                        depressed
                        small
                        class="green lighten-1"
                    >
                        <v-icon color="white">
                            menu
                        </v-icon>
                    </v-btn>
                    Versão original
                </v-card-title>
                <v-card-text
                    v-html="tratarCampoVazio(textDiff.before)"
                />
            </v-card>
        </v-flex>
        <v-spacer/>
        <v-flex
            md6
            sm12
            xs12
        >
            <h4
                v-if="error"
                v-html="errorMessage"
            />
            <h4
                v-if="textsEquals"
                class="grey lighten-4 text-xs-center"
                v-html="textsEqualsMessage"
            />
            <v-card
                flat
            >
                <v-card-title
                    class="subheading"
                >
                    <v-btn
                        fab
                        depressed
                        small
                        class="green lighten-1"
                    >
                        <v-icon color="white">
                            playlist_add
                        </v-icon>
                    </v-btn>
                    Versão alterada
                </v-card-title>
                <v-card-text
                    v-html="tratarCampoVazio(textDiff.after)"
                />
            </v-card>
        </v-flex>
    </v-layout>
</template>

<script>
import * as Diff from 'diff';

export default {
    name: 'CampoDiff',
    props: {
        originalText: {
            type: String,
            default: '',
        },
        changedText: {
            type: String,
            default: '',
        },
        method: {
            type: String,
            default: 'diffWordsWithSpace',
        },
    },
    data() {
        return {
            colors: {
                added: 'green accent-1',
                removed: 'red lighten-3',
                normal: '',
            },
            textDiff: {
                before: '',
                after: '',
            },
            error: false,
            textsEquals: false,
            errorMessage: 'Não é possível comparar: ambos lados devem estar preenchidos.',
            textsEqualsMessage: 'Textos sem diferenças.',
        };
    },
    watch: {
        originalText() {
            if (this.originalText !== '') {
                this.showDiff();
            }
        },
        changedText() {
            if (this.changedText !== '') {
                this.showDiff();
            }
        },
    },
    created() {
        this.showDiff();
    },
    methods: {
        makeDiff(original, changed) {
            /** see all available methods at https://www.npmjs.com/package/diff */
            let dd = {};
            switch (this.method) {
            case 'diffChars':
                dd = Diff.diffChars(original, changed);
                break;
            case 'diffWords':
                dd = Diff.diffWords(original, changed);
                break;
            case 'diffWordsWithSpacediffWords':
                dd = Diff.diffWordsWithSpacediffWords(original, changed);
                break;
            case 'diffLines':
                dd = Diff.diffLines(original, changed);
                break;
            case 'diffSentences':
                dd = Diff.diffSentences(original, changed);
                break;
            default:
                dd = Diff.diffWordsWithSpace(original, changed);
                break;
            }
            return dd;
        },
        showDiff() {
            this.error = false;
            this.message = '';
            this.textDiff.after = '';
            this.textDiff.before = '';
            if (this.originalText.trim() === ''
                || this.changedText.trim() === '') {
                this.error = true;
                this.textDiff.before = this.originalText;
                this.textDiff.after = this.changedText;
                return;
            }
            const dd = this.makeDiff(
                this.stripTags(this.originalText),
                this.stripTags(this.changedText),
            );
            if (dd.length === 1) {
                this.textDiff.before = this.originalText;
                this.textDiff.after = this.changedText;
                this.textsEquals = true;
            }
            dd.forEach((part) => {
                let color = '';
                let span = '';
                if (part.added === true) {
                    color = this.colors.added;
                } else if (part.removed === true) {
                    color = this.colors.removed;
                } else {
                    color = this.colors.normal;
                }
                const re = /<\//;
                if (part.value.match(re)) {
                    span = `<div class="${color}" style="display:inline-block">${part.value}</div>`;
                } else {
                    span = `<span class="${color}">${part.value}</span>`;
                }
                if (part.removed === true) {
                    this.textDiff.before += span;
                } else if (part.added === true) {
                    this.textDiff.after += span;
                } else {
                    this.textDiff.before += span;
                    this.textDiff.after += span;
                }
            });
        },
        stripTags(html) {
            const tmp = document.createElement('DIV');
            tmp.innerHTML = html;
            return tmp.textContent || tmp.innerText || '';
        },
        tratarCampoVazio(value) {
            if (typeof value !== 'undefined') {
                if (value.trim() === '') {
                    const msgVazio = '<em>Campo vazio</em>';
                    return msgVazio;
                }
            }
            return value;
        },
    },
};
</script>
