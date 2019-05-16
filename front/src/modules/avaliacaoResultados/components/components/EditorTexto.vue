<template>
    <div>
        <vue-editor
            :editor-toolbar="customToolbar"
            v-model="editor"
            :placeholder="'Texto do Documento *'"
            @input="enviar($event)"
            @text-change="counter($event)"
        />
    </div>
</template>

<script>
import { VueEditor } from 'vue2-editor';

export default {
    components: {
        VueEditor,
    },
    props: { value: { type: String, default: '' } },
    data() {
        return {
            editor: '',
            customToolbar: [
                [{ font: [] }],
                [{ header: [false, 1, 2, 3, 4, 5, 6] }],
                [{ size: ['small', false, 'large', 'huge'] }],
                ['bold', 'italic', 'underline', 'strike'],
                [{ align: '' }, { align: 'center' }, { align: 'right' }, { align: 'justify' }],
                [{ list: 'ordered' }, { list: 'bullet' }],
                [{ indent: '-1' }, { indent: '+1' }],
                [{ color: [] }],
            ],
        };
    },
    watch: {
        value() {
            this.setInfo();
        },
    },
    mounted() {
        this.setInfo();
        this.counter();
    },
    methods: {
        enviar(e) {
            this.$emit('editor-texto-input', e);
        },
        counter(e) {
            if (typeof e !== 'undefined' && e.ops.length > 0 && e.ops[0].retain !== undefined) {
                this.$emit('editor-texto-counter', e.ops[0].retain);
            }
        },
        setInfo() {
            this.editor = this.value;
        },
    },

};

</script>
