<template>
  <div class="box">
    <div class="header">
      <div class="title">可提现余额（元）</div>
      <div class="title1">{{balance}}</div>
    </div>
    <div class="title2">体现信息</div>
    <div class="getMoney">
      <div class="money row">
        <div class="money_title">提现金额</div>
        <input type="number" v-model="money" placeholder="提现金额不低于2元" />
      </div>
      <div class="money row">
        <div class="money_title">收款人</div>
        <input type="number" v-model="name" placeholder="请输入收款人真实姓名" />
      </div>
    </div>
    <div class="info">
      <div class="title3">实际到账金额：0元</div>
      <div class="title4">体现说明</div>
      <div class="title5">
        1.微信规定，实名认证用户每日体现上限为2万。根据监管要求，未实名认证用户不能进行提现操作<br/> 2.最小提现金额为2元，微信官方将收取0.6%的手续费
        <br/> 3.体现金额会在3个工作日内发放，并将直接转入微信零钱
      </div>
      <div class="btn" :class="{'active': money >= 2 && name !== ''}" @click="setWithdraw">立即提现</div>
    </div>
  </div>
</template>
<script>
  import dataApi from '@/data/index'
  export default {
    data() {
      return {
        balance: 0,
        money: '',
        name: ''
      }
    },
    components: {},
    computed: {},
    methods: {
      getBalance: function() {
        let that = this
        dataApi.balance(function(res) {
          that.balance = res.balance
        })
      },
      setWithdraw: function() {
        if(this.money < 2) {
          wx.showToast({
            title: '体现金额不低于2元',
            icon: 'none'
          })
          return false
        }
        if(this.name === '') {
          wx.showToast({
            title: '请输入收款人真实姓名',
            icon: 'none'
          })
          return false
        }
        dataApi.withdraw(function(res) {
          wx.showToast({
            title: '提交成功',
            icon: 'success',
            duration: 2000
          })
        }, this.money, this.name)
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
    async onLoad() {
      this.getBalance()
    },
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
  }

  .header {
    width: 750rpx;
    height: 349rpx;
    background-image: linear-gradient(1deg,
    rgba(255, 123, 53, 1) 0%,
    rgba(255, 156, 71, 1) 100%);
    text-align: center;
  }

  .title {
    width: 100%;
    font-size: 29rpx;
    color: #ffffff;
    margin-top: 113rpx;
  }

  .title1 {
    width: 100%;
    font-size: 95rpx;
    color: #ffffff;
  }

  .title2 {
    width: 750rpx;
    height: 71rpx;
    background-color: #f9f9f9;
    padding: 0 34rpx;
    box-sizing: border-box;
    font-size: 21rpx;
    color: #333333;
    line-height: 71rpx;
  }

  .getMoney {
    width: 750rpx;
    height: 147rpx;
    background-color: #ffffff;
    padding: 0 33rpx;
    box-sizing: border-box;
  }

  .money {
    width: 100%;
    height: 72rpx;
    border-bottom: 1px solid #f2f2f2;
    align-items: center;
    justify-content: space-between;
  }

  .money input {
    width: 80%;
  }

  .money_title {
    font-size: 25rpx;
    color: #333333;
  }

  .info {
    width: 100%;
    height: 666rpx;
    background: #f2f2f2;
    padding: 35rpx 32rpx 0;
    box-sizing: border-box;
    line-height: 2;
  }

  .title3 {
    font-size: 29rpx;
    color: #333333;
  }

  .title4 {
    font-size: 25rpx;
    color: #666666;
  }

  .title5 {
    font-size: 25rpx;
    color: #333333;
  }

  .btn {
    width: 673rpx;
    height: 89rpx;
    background-color: #afdbb1;
    border-radius: 9rpx;
    font-size: 29rpx;
    color: #f9f9f9;
    text-align: center;
    line-height: 89rpx;
    margin-top: 80rpx;
  }

  .active {
    background: #1aae18;
  }
</style>
