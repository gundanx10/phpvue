<template>
<div id="app" class="listbox">

  <div class="lbsPic" v-if="data.delivery_name"><img src="http://zsh.huyueyue.com/images/lbs.jpg" mode="widthFix"></div>
  <div class="lbsName" v-if="data.delivery_name">
    <div class="lbsNameLf"><img src="http://zsh.huyueyue.com/images/wz.jpg" mode="widthFix"></div>
    <div class="lbsNameRt">
      <h1>{{data.delivery_name}}<span>{{data.delivery_phone}}</span></h1>
      <h2>【收货地址】{{data.delivery_address}}</h2>
    </div>
  </div>
  <div class="orderBox10" v-for="item in data.detail">
    <div class="cartShow">
      <div class="cartShowLf"><img :src="item.itemInfo.thumb" mode="widthFix"></div>
      <div class="cartShowRt">
        <h1>{{item.itemInfo.title}}</h1>
        <h2>{{item.itemInfo.description}}</h2>
        <h3>¥{{item.price}}<b>x {{item.number}}</b></h3>
      </div>
    </div>
  </div>

  <div class="orderBox10" v-if="data.type === 'course'">
    <div class="cartShow">
      <h1>{{data.remark}}</h1>
    </div>
  </div>

  <div class="minuteBox">
    <div class="minuteText">商品总金额<span>¥{{data.total_amount}}</span></div>
    <div class="minuteText2">实付金额 <b>¥{{data.payed}}</b></div>
  </div>
  <div class="minuteBoxWe">
    <div class="minuteTextWe">下单时间：{{data.created_at}}</div>
    <div class="minuteTextWe">支付方式：微信支付</div>
    <div class="minuteTextWe">订单编号：{{data.order_id}}</div>
  </div>

</div>
</template>

<script>
import {
  mapActions
} from 'vuex'
import dataApi from '@/data/index'
export default {
  data() {
    return {
      id: '',
      data: []
    }
  },
  methods: {
    ...mapActions(['goUrl']),
    moredata: function () {
      const _this = this
      dataApi.getOrderDetail(function (res) {
        console.log(res)
        _this.data = res
      }, this.id)
    }
  },
  mounted() {
    const {
      id
    } = this.$route.query
    this.id = id
    if(!id) {
      this.$router.back()
    }
    this.moredata()
  }
}
</script>
<style>
.lbsPic {
  width: 100%;
  line-height: 0px;
  overflow: hidden;
}

.lbsPic ._img {
  width: 100%;
}

.lbsName {
  width: 97%;
  padding: 3% 3% 3% 0%;
  overflow: hidden;
  border-top: 10px solid #efeff0;
}

.lbsNameLf {
  width: 15%;
  float: left;
}

.lbsNameLf ._img {
  width: 100%;
}

.lbsNameRt {
  width: 85%;
  float: left;
}

.lbsNameRt ._h1 {
  color: #333;
  font-size: 12px;
  padding-top: 5px;
}

.lbsNameRt ._h1 ._span {
  float: right;
}

.lbsNameRt ._h2 {
  color: #666;
  font-size: 12px;
  padding-top: 5px;
  line-height: 22px;
  font-weight: normal
}

.lbsNameRt ._h2 ._span {
  float: right;
}

.minuteTextWe {
  width: 100%;
  line-height: 24px;
  font-size: 12px
}

.minuteBoxWe {
  width: 94%;
  padding: 3% 3% 0% 3%;
  border-top: 8px solid #efeff0;
}

.orderBox10 {
  background: #fff;
  width: 94%;
  padding: 3% 3% 3% 3%;
  overflow: hidden;
  border-top: 10px solid #efeff0;
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

.cartShow {
  background: #fff;
  width: 100%;
  overflow: hidden;
}

.cartShowRt {
  padding: 2% 2% 2% 0;
  position: relative;
  float: right
}

.cartShow ._h1 {
  width: 100%;
  display: block;
  color: #562020;
  font-size: 14px;
}

.cartShowRt ._h1 {
  width: 100%;
  display: block;
  color: #562020;
  font-size: 14px;
}

.cartShowRt ._h2 {
  width: 100%;
  display: block;
  font-size: 14px;
  color: #666;
  font-size: 12px;
  font-weight: normal;
}

.cartShowRt ._h3 {
  width: 100%;
  display: block;
  color: #e15506;
  font-size: 14px;
  font-size: 14px;
  padding-top: 5px;
}

.cartShowRt ._h3 ._b {
  color: #929292;
  float: right;
  font-weight: normal;
}

.cartShowLf {
  width: 40%;
  line-height: 0px;
  float: left;
}

.cartShowLf ._img {
  width: 100%;
  line-height: 0px;
}

.minuteBox {
  width: 94%;
  padding: 0% 3% 0% 3%;
  border-top: 8px solid #efeff0;
}
</style>
