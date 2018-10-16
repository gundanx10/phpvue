<template>
  <div class="box">
    <div class="header">当前招募计划暂无详细描述</div>
    <div class="footer" @click="goApply">{{btnVal}}</div>
    <div class="bg" v-if="setValStatus"></div>
    <div class="col valBox" v-if="setValStatus">
      <div class="name">姓名<span>*</span></div>
      <input class="textName" type="text" placeholder="请输入姓名" v-model="name" />
      <div class="name">手机号<span>*</span></div>
      <input class="textName" type="text" placeholder="请输入手机号" v-model="phone" />
      <!--<div class="name">验证码<span>*</span></div>-->
      <!--<div class="codeBox row">-->
      <!--<input class="textCode" type="text" placeholder="请输入验证码" v-model="codeVal" />-->
      <!--<div class="getCode">获取验证码</div>-->
      <!--</div>-->
    </div>
  </div>
</template>
<script>
  import dataApi from '@/data/index'
  export default {
    data() {
      return {
        setValStatus: false,
        btnVal: '申请成为高级推广员',
        phone: '',
        name: ''
      }
    },
    components: {

    },
    computed: {},
    methods: {
      goApply: function() {
        if (this.setValStatus === true) {
          let that = this
          if(that.name === '') {
            wx.showToast({
              title: '请输入姓名',
              icon: 'none'
            })
            return false
          }
          if(that.phone === '') {
            wx.showToast({
              title: '请输入手机号',
              icon: 'none'
            })
            return false
          }
          if(!(/^1[34578]\d{9}$/.test(that.phone))) {
            wx.showToast({
              title: '请正确输入手机号',
              icon: 'none'
            })
            return false
          }
          dataApi.setApply(function(res) {
            that.setValStatus = false
            that.btnVal = '申请成为高级推广员'
            wx.showToast({
              title: '提交成功',
              icon: 'success',
              duration: 2000
            })
          }, that.name, that.phone)
        } else {
          this.setValStatus = true
          this.btnVal = '提交'
        }
      }
    },
    mounted() {
      wx.hideLoading()
    },
    created() {
      wx.showLoading({
        title: '加载中'
      })
    },
    async onLoad() {},
    onShareAppMessage() {
      return {
        title: '装修知识汇',
        desc: '装修知识大放送',
        path: '/pages/index/main'
      }
    }
  }
</script>
<style scoped>
  .row {
    display: flex;
    flex-direction: row;
  }

  .col {
    display: flex;
    flex-direction: column;
  }

  .ellipsis {
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap
  }

  .box {
    width: 750rpx;
    height: auto;
    min-height: 1334rpx;
    background: #f2f2f2;
  }

  .header {
    width: 750rpx;
    background: #fff;
    font-size: 25rpx;
    color: #333333;
    padding: 34rpx 0 47rpx 20rpx;
    box-sizing: border-box;
    box-sizing: border-box;
    border-top: 1px solid #f2f2f2;
  }

  .footer {
    position: fixed;
    bottom: 0;
    left: 0;
    width: 750rpx;
    height: 105rpx;
    background-color: #fd6b31;
    text-align: center;
    line-height: 105rpx;
    font-size: 33rpx;
    color: #ffffff;
    z-index: 2;
  }

  .bg {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.4);
    z-index: 1;
  }

  .valBox {
    width: 100%;
    /*height: 468rpx;*/
    height: 340rpx;
    background: #fff;
    padding: 40rpx 38rpx 26rpx;
    box-sizing: border-box;
    justify-content: space-between;
    position: fixed;
    bottom: 105rpx;
    left: 0;
    z-index: 2;
  }

  .name {
    font-size: 29rpx;
    color: #333333;
  }

  .name span {
    font-size: 25rpx;
    color: #f41c06;
  }

  .textName {
    width: 674rpx;
    height: 71rpx;
    border: 1px solid #f2f2f2;
    border-radius: 7rpx;
    padding: 0 20rpx;
    box-sizing: border-box;
  }

  .codeBox {
    width: 100%;
    justify-content: space-between;
  }

  .textCode {
    width: 438rpx;
    height: 71rpx;
    border: 1px solid #f2f2f2;
    border-radius: 7rpx;
    padding: 0 20rpx;
    box-sizing: border-box;
  }

  .getCode {
    width: 215rpx;
    height: 71rpx;
    background-color: #ffa785;
    border-radius: 7rpx;
    font-size: 25rpx;
    color: #ffffff;
    text-align: center;
    line-height: 71rpx;
  }
</style>
