<template>
<div id="app" class="listbox">

  <div class="successBox"> <span><img src="http://zsh.huyueyue.com/images/cg.svg" mode="widthFix"/></span>
    <h1>恭喜您，支付成功！</h1>
  </div>
  <div class="minuteBox">
    <div class="minuteTitle">装修知识汇</div>
    <div class="minuteText">商品<span>{{data.remark}}</span></div>
    <div class="minuteText">交易单号<span>{{data.order_id}}</span></div>
    <div class="minuteText">交易时间<span>{{data.pay_time}}</span></div>
    <div class="minuteText">交易状态<span>支付成功</span></div>
    <div class="minuteBt">
      <h1 v-on:click="goUrl('/pages/orderDetail/main?id='+data.order_id)">
                  订单详情
                </h1>

      <h1 v-if="data.type === 'goods'" v-on:click="goUrl('/pages/goods/main')">
                  <a>继续购买</a>
                </h1>
      <h1 v-if="data.type === 'member'" v-on:click="goUrl('/pages/summary/main')">
                  <a>返回</a>
                </h1>
      <h1 v-if="data.type === 'course'" v-on:click="goUrl('/pages/knowledge/main')">
                  <a>返回</a>
                </h1>
    </div>

  </div>
</div>
</template>

<script>
import {
  mapActions
} from 'vuex'
import dataApi from '@/data/index'
import filters from '@/common/filters'
export default {
  data() {
    return {
      id: '',
      data: []
    }
  },
  methods: {
    ...mapActions(['goUrl']),
    moredata: function() {
      const _this = this
      dataApi.getOrderDetail(function(res) {
        res.pay_time = filters.foramtTime(res.pay_time)
        _this.data = res
      }, this.id)
    }
  },
  mounted() {
    const {
      id
    } = this.$route.query
    this.id = id
    if (!id) {
      this.$router.back()
    }
    this.moredata()
  }
}
</script>
<style>
.successBox ._img {
  width: 36px
}

.successBox {
  width: 100%;
  padding: 5% 0 5% 0;
  background: #fff;
  border-top: 1px solid #c9c9c9;
}

.successBox ._span {
  width: 100%;
  display: block;
  text-align: center;
}

.successBox ._h1 {
  width: 100%;
  display: block;
  text-align: center;
  font-size: 18px;
  line-height: 18px;
  padding-top: 10px;
  color: #00a966;
}

.successBox ._h2 {
  width: 100%;
  display: block;
  text-align: center;
  font-size: 16px;
  line-height: 16px;
  padding-top: 14px;
  color: #31c7ca;
  font-weight: normal;
  color: #999;
}

.successBox ._h3 {
  width: 100%;
  display: block;
  text-align: center;
  font-size: 16px;
  line-height: 28px;
  padding-top: 14px;
  color: #31c7ca;
  font-weight: normal;
  color: #ff7f42;
}

.successBox ._h3 ._a {
  color: #42c2ff;
  text-decoration: underline;
}

.minuteBox {
  width: 94%;
  padding: 0% 3% 0% 3%;
  border-top: 8px solid #efeff0;
}

.minuteTitle {
  width: 100%;
  text-align: center;
  font-size: 14px;
  line-height: 40px;
  border-bottom: 1px solid #ddd;
  font-weight: bold
}

.minuteText {
  width: 100%;
  line-height: 36px;
  font-size: 12px
}

.minuteText ._span {
  float: right;
}

.minuteText2 {
  width: 100%;
  line-height: 36px;
  font-size: 12px;
  border-top: 1px solid #ddd;
  text-align: right;
  font-weight: bold
}

.minuteText2 ._b {
  color: #e15506;
}

.minuteBt {
  width: 100%;
  border-top: 1px solid #ddd;
  padding-top: 10px;
}

.minuteBt ._h1 {
  width: 25%;
  text-align: center;
  border: 1px solid #ddd;
  float: right;
  border-radius: 5px;
  font-size: 12px;
  line-height: 30px;
  margin-left: 15px;
}

.minuteBt ._h1 ._a {
  width: 100%;
  height: 100%;
  display: block;
}

.minuteBt ._h2 {
  width: 25%;
  text-align: center;
  border: 1px solid #ddd;
  float: right;
  border-radius: 5px;
  font-size: 12px;
  line-height: 30px;
}

.minuteBt ._h2 ._a {
  width: 100%;
  height: 100%;
  display: block;
}
</style>
