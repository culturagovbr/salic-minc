<template>
    <span
        :style="resStyle"
        class="Resizer"
        @mouseover="isMouseOver = true"
        @mouseout="isMouseOver = false"/>
</template>

<script>
export default {
    name: 'ResizerComp',
    props: {
        splitTo: {
            type: String,
            default: '',
        },
        resizerColor: {
            type: String,
            default: '',
        },
        resizerBorderColor: {
            type: String,
            default: '',
        },
        resizerThickness: {
            type: Number,
            default: 0,
        },
        resizerBorderThickness: {
            type: Number,
            default: 0,
        },
    },
    data() {
        return {
            isMouseOver: false,
        };
    },
    computed: {
        resizerTotalThickness() {
            return this.resizerThickness + this.resizerBorderThickness * 2;
        },
        margin() {
            return Math.floor(this.resizerThickness / 2) + this.resizerBorderThickness;
        },
        rBorder() {
            if (this.splitTo === 'rows') {
                return { border1: 'top', border2: 'bottom' };
            }
            return { border1: 'left', border2: 'right' };
        },
        resStyle() {
            const tmpStyle = {};

            tmpStyle['background-color'] = this.resizerColor;

            if (this.splitTo === 'rows') {
                tmpStyle.height = `${this.resizerTotalThickness}px`;
                tmpStyle.margin = `-${this.margin}px 0`;
                tmpStyle.padding = `0 ${this.resizerBorderThickness}px`;
            } else {
                tmpStyle.height = `${this.heightResizer}px`;
                tmpStyle.width = `${this.resizerTotalThickness}px`;
                tmpStyle.margin = `${'0 -'}${this.margin}px`;
                tmpStyle.padding = `${this.resizerBorderThickness}px 0`;
            }

            if (this.isMouseOver) {
                // eslint-disable-next-line
                tmpStyle[`border-${this.rBorder.border1}`] = tmpStyle[
                    `border-${this.rBorder.border2}`
                ] = `${this.resizerBorderColor
                } solid ${
                    this.resizerBorderThickness
                }px`;
            } else {
                // eslint-disable-next-line
                tmpStyle[`border-${this.rBorder.border1}`] = tmpStyle[
                    `border-${this.rBorder.border2}`
                ] = `transparent solid ${this.resizerBorderThickness}px`;
            }

            return tmpStyle;
        },
        heightResizer() {
            if (this.$parent.$refs.pane2) {
                return this.$parent.$refs.pane2.$el.offsetHeight;
            }
            return 0;
        },
    },
};
</script>

<style scoped>
.Resizer {
  width: 8px;
  position: relative;
  -moz-box-sizing: border-box;
  -webkit-box-sizing: border-box;
  box-sizing: border-box;
  z-index: 2;
  -webkit-background-clip: padding-box;
  background-clip: padding-box;
  min-height: calc(100vh - 60px);
}

.Resizer:hover {
  -webkit-transition: all 0.3s ease;
  transition: all 0.3s ease;
}

.Resizer.rows {
  cursor: row-resize;
  width: 100%;
}

.Resizer.columns {
  height: 100%;
  cursor: col-resize;
}
</style>
