<!--https://github.com/raven78/vue-resize-split-pane-->
<template>
    <v-layout
        :class="classObject"
        row
        class="pane-rs root"
        @mousemove="onMouseMove"
        @mouseup="onMouseUp">
        <v-slide-x-transition>
            <pane-comp
                v-if="hasSlot('firstPane')"
                ref="pane1"
                :class="{column: splitTo === 'columns', row: splitTo === 'rows'}"
                :style="iStyleFirst">
                <slot name="firstPane"/>
            </pane-comp>
        </v-slide-x-transition>
        <resizer-comp
            v-if="allowResize && hasSlot('secondPane') && hasSlot('firstPane')"
            :split-to="splitTo"
            :resizer-color="resizerColor"
            :resizer-border-color="resizerBorderColor"
            :resizer-thickness="resizerThickness"
            :resizer-border-thickness="resizerBorderThickness"
            :class="{
                rows: splitTo === 'rows',
                columns: splitTo === 'columns'}"
            @mousedown.native="onMouseDown"
        />

        <pane-comp
            v-if="hasSlot('secondPane')"
            ref="pane2"
            :class="{column: splitTo === 'columns', row: splitTo === 'rows'}"
            :style="iStyleSecond">
            <slot name="secondPane"/>
        </pane-comp>
    </v-layout>
</template>

<script>
import Resizer from '@/components/resize-panel/Resizer';
import Pane from '@/components/resize-panel/Pane';

function unFocus(document, window) {
    if (document.selection) {
        document.selection.empty();
    } else {
        try {
            window.getSelection().removeAllRanges();
            // eslint-disable-next-line no-empty
        } catch (e) {
            // console.log(e)
        }
    }
}

export default {
    name: 'PaneRs',
    components: {
        'resizer-comp': Resizer,
        'pane-comp': Pane,
    },
    props: {
        allowResize: { type: Boolean, default: false },
        splitTo: { type: String, default: 'columns' }, // column || rows
        primary: { type: String, default: 'first' }, // first || second
        size: { type: Number, default: 16 }, // pixels || percents
        units: { type: String, default: 'pixels' }, // pixels || percents
        minSize: { type: Number, default: 16 }, // pixels || percents
        maxSize: { type: Number, default: 0 }, // pixels || percents
        step: { type: Number, default: 0 }, // pixels only
        resizerThickness: { type: Number, default: 2 }, // in px - width of the resizer
        resizerColor: { type: String, default: '#AAA' }, //  any css color - if you set transparency, it will afect the border too
        resizerBorderColor: { type: String, default: 'rgba(0,0,0, 0.15)' }, // any css color - #FFF, rgb(0,0,0), rgba(0,0,0,0)
        resizerBorderThickness: { type: Number, default: 3 }, // in px - border that forms the shadow
    },
    data() {
        return {
            active: false,
            position: 0,
            localSize: this.size,
        };
    },
    computed: {
        classObject() {
            return {
                columns: this.splitTo === 'columns',
                rows: this.splitTo === 'rows',
            };
        },
        iStyleFirst() {
            const el = 'first';
            const style = { flex: 1, position: 'relative', outline: 'none' };

            if (el === this.primary) {
                style.flex = '0 0 auto';
                const units = this.units === 'pixels' ? 'px' : '%';
                // eslint-disable-next-line
                this.splitTo === 'columns'
                    ? (style.width = this.localSize + units)
                    : (style.height = this.localSize + units);
            } else {
                style.flex = '1 1 0%';
            }
            return style;
        },
        iStyleSecond() {
            const el = 'second';
            const style = { flex: 1, position: 'relative', outline: 'none' };

            if (el === this.primary) {
                style.flex = '0 0 auto';
                const units = this.units === 'pixels' ? 'px' : '%';
                // eslint-disable-next-line
                this.splitTo === 'columns'
                    ? (style.width = this.localSize + units)
                    : (style.height = this.localSize + units);
            } else {
                style.flex = '1 1 0%';
            }
            return style;
        },
    },
    watch: {
    // whenever question changes, this function will run
        size(newSize) {
            this.localSize = newSize;
        },
    },
    methods: {
        round2Fixed(value) {
            let val = +value;
            // eslint-disable-next-line
            if (isNaN(val)) return NaN;

            val = Math.round(+(`${val.toString()}e2`));

            return +(`${val.toString()}e-2`);
        },
        onMouseDown(event) {
            if (this.allowResize) {
                const eventWithTouches = Object.assign({}, event, {
                    touches: [
                        {
                            clientX: event.clientX,
                            clientY: event.clientY,
                        },
                    ],
                });
                this.onTouchStart(eventWithTouches);
            }
        },
        onTouchStart(event) {
            if (this.allowResize) {
                unFocus(document, window);
                const position = this.splitTo === 'columns'
                    ? event.touches[0].clientX
                    : event.touches[0].clientY;

                if (typeof this.onDragStarted === 'function') {
                    // eslint-disable-next-line
                    onDragStarted();
                }
                this.active = true;
                this.position = position;
            }
        },
        onMouseMove(event) {
            if (this.allowResize) {
                const eventWithTouches = Object.assign({}, event, {
                    touches: [
                        {
                            clientX: event.clientX,
                            clientY: event.clientY,
                        },
                    ],
                });
                this.onTouchMove(eventWithTouches);
            }
        },
        onTouchMove(event) {
            const { active, position } = this.$data;
            const {
                maxSize,
                minSize,
                allowResize,
                splitTo,
                primary,
            } = this.$props;
            if (allowResize && active) {
                unFocus(document, window);
                const isPrimaryFirst = primary === 'first';
                const ref = isPrimaryFirst ? 'pane1' : 'pane2';
                if (ref) {
                    const node = this.$refs[ref].$vnode.elm;
                    if (node.getBoundingClientRect) {
                        // Where is cursor positioned
                        const current = splitTo === 'columns'
                            ? event.touches[0].clientX
                            : event.touches[0].clientY;

                        // Current pane size (width || height)
                        const size = splitTo === 'columns'
                            ? node.getBoundingClientRect().width
                            : node.getBoundingClientRect().height;
                        // Direct parent size (width || height)
                        const pSize = splitTo === 'columns'
                            ? this.$refs[ref].$parent.$vnode.elm.getBoundingClientRect().width
                            : this.$refs[ref].$parent.$vnode.elm.getBoundingClientRect().height;

                        let positionDelta = position - current;
                        const sizeDelta = isPrimaryFirst ? positionDelta : -positionDelta;
                        let newSize = this.units === 'percents'
                            ? this.round2Fixed((size - sizeDelta) * 100 / pSize)
                            : size - sizeDelta;

                        const newPosition = position - positionDelta;

                        if (this.step) {
                            if (Math.abs(positionDelta) < this.step) {
                                return;
                            }
                            // eslint-disable-next-line no-bitwise
                            positionDelta = ~~(positionDelta / this.step) * this.step;
                        }

                        if (minSize && newSize < minSize) {
                            newSize = minSize;
                        }
                        if (maxSize && newSize > maxSize) {
                            newSize = maxSize;
                        }

                        this.localSize = newSize;
                        this.position = newPosition;
                    }
                }
            }
        },
        onMouseUp() {
            const { allowResize, onDragFinished } = {
                allowResize: this.allowResize,
                onDragFinished: this.onDragFinished,
            };
            const { active, draggedSize } = {
                active: this.active,
                draggedSize: this.draggedSize,
            };
            if (allowResize && active) {
                if (typeof onDragFinished === 'function') {
                    onDragFinished(draggedSize);
                }
                this.$emit('update:size', this.localSize);
                this.active = false;
            }
        },
        hasSlot(name = 'default') {
            return !!this.$slots[name] || !!this.$scopedSlots[name];
        },
    },
};
</script>

<style scoped>
*,
*:before,
*:after {
  -moz-box-sizing: border-box;
  -webkit-box-sizing: border-box;
  box-sizing: border-box;
  position: relative;
}

.root {
  height: 100%;
  width: 100%;
}

.pane-rs {
  display: flex;
  flex: 1;
  /*align-items: stretch;
  align-content: stretch;*/
  /*position: absolute;*/
  outline: none;
  overflow: hidden;
  user-select: text;
}
</style>
