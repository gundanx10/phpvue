<template>
<div id="app">
      <div class="el-input-number">
      <span role="button"  :class="{'is-disabled': minDisabled}"
      v-on:click="decrease"
      class="el-input-number__decrease">

      <img src="/static/images/minus-symbol.png"  mode="widthFix" />
      <i class="fa fa-minus"></i>
      </span>
      <span role="button"  :class="{'is-disabled': maxDisabled}"
      v-on:click="increase"
      class="el-input-number__increase">
      <img src="/static/images/plus-black-symbol.png"  mode="widthFix" />
      </span><div class="el-input">

      <input
        v-bind="$props"
        class="el-input__inner"
        :autocomplete="autoComplete"
        :value="currentValue"
        ref="input"
        @input="handleInput"
        @focus="handleFocus"
        @blur="handleBlur"
        @change="handleChange"
        :aria-label="label"
      >
      </div></div>

</div>
</template>
<script>
  export default {
    name: 'ElInput',
    inject: {
      elForm: {
        default: ''
      },
      elFormItem: {
        default: ''
      }
    },

    data () {
      return {
        currentValue: this.value,
        hovering: false,
        focused: false,
        max: 2000,
        min: 0
      }
    },

    props: {
      value: [String, Number],
      placeholder: String,
      size: String,
      resize: String,
      name: String,
      form: String,
      id: String,
      maxlength: Number,
      minlength: Number,
      readonly: Boolean,
      autofocus: Boolean,
      disabled: Boolean,
      type: {
        type: String,
        default: 'text'
      },
      autosize: {
        type: [Boolean, Object],
        default: false
      },
      rows: {
        type: Number,
        default: 2
      },
      autoComplete: {
        type: String,
        default: 'off'
      },
      validateEvent: {
        type: Boolean,
        default: true
      },
      suffixIcon: String,
      prefixIcon: String,
      label: String,
      clearable: {
        type: Boolean,
        default: false
      },
      tabindex: String,
      step: {
        type: Number,
        default: 1
      }
    },
    computed: {
      minDisabled () {
        return this._decrease(this.value, this.step) < this.min
      },
      maxDisabled () {
        return this._increase(this.value, this.step) > this.max
      },
      precision () {
        const { value, step, getPrecision } = this
        return Math.max(getPrecision(value), getPrecision(step))
      },
      controlsAtRight () {
        return this.controlsPosition === 'right'
      },
      _elFormItemSize () {
        return (this.elFormItem || {}).elFormItemSize
      },
      inputNumberSize () {
        return this.size || this._elFormItemSize || (this.$ELEMENT || {}).size
      }
    },
    methods: {
      focus () {
        (this.$refs.input || this.$refs.textarea).focus()
      },
      getMigratingConfig () {
        return {
          props: {
            'icon': 'icon is removed, use suffix-icon / prefix-icon instead.',
            'on-icon-click': 'on-icon-click is removed.'
          },
          events: {
            'click': 'click is removed.'
          }
        }
      },
      handleBlur (event) {
        this.focused = false
        this.$emit('blur', event)
      },
      getPrecision (value) {
        const valueString = value.toString()
        const dotPosition = valueString.indexOf('.')
        let precision = 0
        if (dotPosition !== -1) {
          precision = valueString.length - dotPosition - 1
        }
        return precision
      },
      inputSelect () {
        (this.$refs.input || this.$refs.textarea).select()
      },
      handleFocus (event) {
        this.focused = true
        this.$emit('focus', event)
      },
      handleInput (event) {
        const value = event.target.value
        this.$emit('input', value)
        this.setCurrentValue(value)
      },
      handleChange (event) {
        this.$emit('change', event.target.value)
      },
      setCurrentValue (newVal) {
        const oldVal = this.currentValue
        if (newVal >= this.max) newVal = this.max
        if (newVal <= this.min) newVal = this.min
        if (oldVal === newVal) {
          return
        }
        this.$emit('change', newVal, oldVal)
        this.$emit('input', newVal)
        this.currentValue = newVal
      },
      calcIconOffset (place) {
        const pendantMap = {
          'suf': 'append',
          'pre': 'prepend'
        }
        const pendant = pendantMap[place]

        if (this.$slots[pendant]) {
          return { transform: `translateX(${place === 'suf' ? '-' : ''}${this.$el.querySelector(`.el-input-group__${pendant}`).offsetWidth}px)` }
        }
      },
      clear () {
        this.$emit('input', '')
        this.$emit('change', '')
        this.setCurrentValue('')
        this.focus()
      },
      increase () {
        if (this.disabled || this.maxDisabled) return
        const value = this.value || 0
        const newVal = this._increase(value, this.step)
        if (newVal > this.max) return
        this.setCurrentValue(newVal)
      },
      decrease () {
        if (this.disabled || this.minDisabled) return
        const value = this.value || 0
        const newVal = this._decrease(value, this.step)
        if (newVal < this.min) return
        this.setCurrentValue(newVal)
      },
      toPrecision (num, precision) {
        if (precision === undefined) precision = this.precision
        return parseFloat(parseFloat(Number(num).toFixed(precision)))
      },
      _increase (val, step) {
        const precisionFactor = Math.pow(10, this.precision)
        return this.toPrecision((precisionFactor * val + precisionFactor * step) / precisionFactor)
      },
      _decrease (val, step) {
        const precisionFactor = Math.pow(10, this.precision)
        return this.toPrecision((precisionFactor * val - precisionFactor * step) / precisionFactor)
      }
    },
    created () {
      this.$on('inputSelect', this.inputSelect)
    },

    mounted () {
    }
  }
</script>
<style scoped>
.el-input-number{position: relative;
    display: inline-block;
    width: 125px;
    line-height: 90rpx;
    margin: 10px 0 5px 0;
    }
.el-input-number__decrease.is-disabled, .el-input-number__increase.is-disabled {
    color: #c0c4cc;
    cursor: not-allowed;}
.el-input-number__decrease {
    left: 1px;
    border-right: 1px solid #dcdfe6;
}
.el-input-number__decrease, .el-input-number__increase {
    position: absolute;
    z-index: 1;
    top: 20rpx;
    width: 40px;
    height: 75rpx;
    text-align: center;
    background: #f5f7fa;
    color: #606266;
    cursor: pointer;
    font-size: 13px;
}
.el-input-number__increase {
    right: -1px;
    border-left: 1px solid #dcdfe6;
}
.el-input-number__increase ._img{width:30rpx;}
.el-input-number__decrease ._img{width:30rpx}
.el-input-number__decrease, .el-input-number__increase {
    position: absolute;
    z-index: 1;
    top: 1px;
    width: 40px;
    height: 75rpx;
    text-align: center;
    background: #f5f7fa;
    color: #606266;
    cursor: pointer;
    font-size: 13px;
}
.el-input-number .el-input {
    display: block;
}
.el-input-number .el-input {
    border: 1px solid #dcdfe6;
    height: 77rpx;
}
.el-input {
    position: relative;
    font-size: 14px;
    display: inline-block;
    width: 100%;
}
.el-input-number .el-input__inner {
    -webkit-appearance: none;
    padding-left: 50px;
    padding-right: 50px;
    text-align: center;
}
.el-input__inner {
    -webkit-appearance: none;
    background-color: #fff;
    box-sizing: border-box;
    color: #606266;
    display: inline-block;
    font-size: inherit;
    height: 75rpx;
    line-height: 1;
    outline: 0;
    padding: 0 15px;
    -webkit-transition: border-color .2s cubic-bezier(.645,.045,.355,1);
    transition: border-color .2s cubic-bezier(.645,.045,.355,1);
    width: 100%;
}
</style>
