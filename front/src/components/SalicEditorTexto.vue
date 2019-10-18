<template>
    <div>
        <v-label v-if="label">{{ label }}</v-label>
        <vue-editor
            class="mt-2"
            v-model="editor"
            :editor-toolbar="customToolbar"
            v-bind="$attrs"
            @input="$emit('input', $event)"
            @focus="$emit('focus', $event)"
            @blur="$emit('blur', $event)"
            @selection-change="$emit('selection-change', $event)"
            @text-change="$emit('text-change', $event)"
            @imageAdded="$emit('imageAdded', $event)"
        />
        <input
            type="hidden"
            name="teste"
            value="1"
        >
        <div class="v-text-field__details pa-2">
            <div
                class="v-messages theme--light error--text"
            >
                <div
                    v-if="hasError.error"
                    class="v-messages__wrapper"
                >
                    <div
                        class="v-messages__message"
                    >
                        {{ hasError.msg }}
                    </div>
                </div>
            </div>
            <div
                :class="obterClasseContador()"
                class="v-counter theme--light"
            >
                {{ contador }}
                <span v-if="maxChar"> / {{ maxChar }}</span>
            </div>
        </div>
    </div>
</template>

<script>
import { VueEditor } from 'vue2-editor';

export default {
    name: 'SEditorTexto',
    components: {
        VueEditor,
    },
    inheritAttrs: false,
    props: {
        value: {
            type: String,
            default: '',
        },
        label: {
            type: String,
            default: '',
        },
        counter: {
            type: Boolean,
            default: false,
        },
        minChar: {
            type: [String, Number],
            default: null,
        },
        maxChar: {
            type: [String, Number],
            default: null,
        },
    },
    data() {
        return {
            editor: '',
            customToolbar: [
                [{ header: [false, 1, 2, 3, 4, 5, 6] }],
                [{ size: ['small', false, 'large', 'huge'] }],
                ['bold', 'italic', 'underline', 'strike'],
                [{ align: '' }, { align: 'center' }, { align: 'right' }, { align: 'justify' }],
                [{ list: 'ordered' }, { list: 'bullet' }],
                [{ indent: '-1' }, { indent: '+1' }],
                [{ color: [] }],
            ],
            editorConfigs: {
                show: false,
                color: '',
                style: '',
                msg: '',
                enable: true,
            },
        };
    },
    computed: {
        contador() {
            return this.editor.replace(/(<([^>]+)>)/ig, '').trim().length;
        },
        hasError() {
            let editorParecerRules = {
                error: false,
                msg: '',
            };
            if (this.minCharRule) {
                editorParecerRules = {
                    error: true,
                    msg: `O texto deve conter mais que ${this.minChar} caracteres`,
                };
            }
            if (this.maxCharRule) {
                editorParecerRules = {
                    error: true,
                    msg: `O texto não pode conter mais que ${this.maxChar} caracteres`,
                };
            }
            return editorParecerRules;
        },
        maxCharRule() {
            return this.maxChar && parseInt(this.maxChar, 10) < this.contador;
        },
        minCharRule() {
            return this.minChar && parseInt(this.minChar, 10) > this.contador;
        },
    },
    watch: {
        value(val) {
            this.editor = val.trim();
        },
        editor() {
            this.$emit('editor-texto-counter', this.contador);
        },
        hasError(val) {
            if (val.error) {
                this.$emit('update:error', val.msg);
            }
        },
    },
    mounted() {
        this.editor = this.value.trim();
    },
    methods: {
        obterClasseContador() {
            return {
                'red--text': this.maxCharRule || this.minCharRule,
            };
        },
    },
};
</script>
