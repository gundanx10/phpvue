<template>
<div id="app" class="listbox">

  <div class="cartText">
    <ul>
      <li v-on:click="changeAddress"> <span v-if="!userInfo.delivery_address || userInfo.delivery_address.length <1"><img src="http://zsh.huyueyue.com/images/jiahao.svg"  mode="widthFix" /></span>
        <h1 v-if="!userInfo.delivery_address || userInfo.delivery_address.length <1">新增收货地址</h1>
        <h1 v-else>{{userInfo.delivery_name}} {{userInfo.delivery_phone}} {{userInfo.delivery_address}}</h1>
        <em><img src="http://zsh.huyueyue.com/images/right.svg"  mode="widthFix"></em> </li>
    </ul>
  </div>
  <!--循环-->
  <div class="orderBox">
    <div class="cartShow" v-for="item in data">
      <div class="cartShowLf"><img :src="item.product.thumb" mode="widthFix"></div>
      <div class="cartShowRt">
        <h1>{{item.product.title}}</h1>
        <h2>{{item.product.description}}</h2>
        <h3>¥{{item.product.price}}<b>x {{item.quantity}}</b></h3>
      </div>
    </div>

    <div class="orderText">
      <ul>
        <li>
          <h1>配送方式</h1>
          <h2 v-if="freight>0">{{freight}}</h2>
          <h2 v-else>免运费</h2>
        </li>

        <li>
          <h1>合计</h1>
          <h3>¥{{totalPrice}}</h3>
        </li>
      </ul>
    </div>

  </div>

  <div class="cartText">
    <ul class="invoice">
      <li class="title">
        <h1>开具发票</h1></li>
      <li v-on:click="changeInvoice">
        <h2 v-if="Invoice.name">{{Invoice.name}}</h2>
        <h2 v-else>
                <a >添加发票信息</a>
                </h2>
        <em><img src="http://zsh.huyueyue.com/images/right.svg"  mode="widthFix"></em>
      </li>
    </ul>
  </div>

  <div class="MHr40"></div>
  <!--误删，65px的高度-->
  <!--FOOTer-->
  <div class="accountsBt">
    <div class="order01">合计：
      <font class="chengse06">¥{{totalPrice}}</font>
    </div>
    <div class="order02">
      <span v-on:click="createOrder()" class="bt50">提交订单</span>
    </div>
  </div>
</div>
</template>

<script>
import {
  mapGetters,
  mapActions
} from 'vuex'
import wxParse from 'mpvue-wxparse'
import dataApi from '@/data/index'
export default {
  data() {
    return {
      Invoice: {
        name: '',
        id: 0
      }
    }
  },
  computed: {
    ...mapGetters({
      data: 'getOrder',
      userInfo: 'getUserInfo'
    }),
    freight() {
      var freight = 0
      this.data.forEach(function (item, index) {
        freight = (item.product.freight > freight) ? item.product.freight : freight
      })
      return freight
    },
    totalPrice() {
      var total = 0
      this.data.forEach(function (item, index) {
        total += item.quantity * item.product.price
      })
      total += this.freight
      return total
    }
  },
  components: {
    wxParse
  },
  methods: {
    ...mapActions(['goUrl']),
    changeAddress() {
      const _this = this
      wx.chooseAddress({
        success: function (res) {
          _this.upAddress(res)
        }
      })
    },
    changeInvoice() {
      const _this = this
      wx.chooseInvoiceTitle({
        success(res) {
          _this.upInvoice(res)
        }
      })
    },
    upAddress(obj) {
      const _this = this
      dataApi.updateUserAddress(function (res) {
        _this.userInfo.delivery_address = res.delivery_address
      }, obj)
    },
    upInvoice(obj) {
      const _this = this
      dataApi.updateUserInvoice(function (res) {
        _this.Invoice = res
      }, obj)
    },
    createOrder() {
      let params = {
        address: this.userInfo.delivery_address,
        order: this.data,
        type: 'goods',
        inv_id: this.Invoice.id,
        total: this.totalPrice,
        freight: this.freight,
        from: 'app'
      }
      if(this.userInfo.delivery_address === '') {
        wx.showToast({
          title: '请输入正确收货地址',
          image: '/static/images/error2.png',
          duration: 2000
        })
        return false
      }
      dataApi.postCreateOrder(function (res) {
        wx.requestPayment({
          'timeStamp': res.timestamp,
          'nonceStr': res.nonceStr,
          'package': res.package,
          'signType': 'MD5',
          'paySign': res.paySign,
          'success': function (rs) {
            let url = '/pages/payed/main?id=' + res.out_trade_no
            wx.navigateTo({
              url: url
            })
          },
          'fail': function (rs) {}
        })
      }, params)
    }
  }
}
</script>
<style>
.cartText ._img {
  width: 22px
}

.invoice ._img {
  width: 22px
}

.cartText {
  width: 100%;
  padding: 0px;
  overflow: hidden;
  background: #fff;
  overflow: hidden;
  border-top: 10px solid #efeff0;
}

.cartText ._ul {
  margin: 0px;
  padding: 0px;
}

.cartText ._ul ._li {
  position: relative;
  width: 96%;
  display: block;
  list-style-type: none;
  padding: 15px 2% 15px 2%;
  border-bottom: 1px solid #e3e3e3;
  background: url(http://zsh.huyueyue.com/images/right.png) no-repeat -20px 8px;
  background-size: 22px;
  overflow: hidden;
}

.cartText ._ul ._li ._span {
  width: 22px;
  height: 22px;
  display: block;
  float: left;
  font-size: 20px;
}

.cartText ._ul ._li ._h1 {
  float: left;
  line-height: 22px;
  font-size: 14px;
  padding-left: 8px;
  color: #777;
  font-weight: normal;
  width: 95%;
  text-align: left
}

.cartText ._ul ._li ._h1 ._a {
  color: #777;
}

.cartText ._ul ._li ._h2 {
  line-height: 22px;
  font-size: 14px;
  padding-left: 8px;
  color: #777;
  font-weight: normal;
  width: 95%;
  text-align: left
}

.cartText ._ul ._li ._h2 ._a {
  color: #777;
}

.cartText ._ul ._li ._h1 ._em {
  font-style: normal;
  font-weight: normal;
  padding-left: 10px;
  font-size: 16px;
  color: #999999;
}

.cartText ._ul ._li ._em {
  width: 22px;
  height: 22px;
  display: block;
  font-size: 16px;
  position: absolute;
  right: 15rpx;
  top: 30rpx
}

.cartText ._ul ._li ._b {
  width: 10px;
  height: 10px;
  background: #ff0000;
  -moz-border-radius: 10px;
  -webkit-border-radius: 10px;
  border-radius: 10px;
  float: left;
  margin: 6px 0 0 5px;
}

.cartText ._ul ._li ._h1 strong {
  float: right;
  font-weight: normal;
}

.orderBox {
  background: #fff;
  width: 94%;
  padding: 3% 3% 3% 3%;
  overflow: hidden;
}

.orderText {
  width: 100%;
  padding: 0px;
  overflow: hidden;
  background: #fff;
  overflow: hidden;
  margin-top: 15px;
}

.orderText ._ul {
  margin: 0px;
  padding: 0px;
}

.orderText ._ul ._li {
  width: 96%;
  display: block;
  list-style-type: none;
  padding: 15px 2% 15px 2%;
  background: url(http://zsh.huyueyue.com/images/right.png) no-repeat -20px 8px;
  background-size: 22px;
  overflow: hidden;
  border-top: 1px solid #efeff0;
}

.orderText ._ul ._li ._span {
  width: 22px;
  height: 22px;
  display: block;
  float: left;
  font-size: 20px;
}

.orderText ._ul ._li ._h1 {
  float: left;
  line-height: 20px;
  font-size: 12px;
  padding-left: 8px;
  color: #777;
  font-weight: normal;
  float: left
}

.orderText ._ul ._li ._h1 ._a {
  color: #777;
}

.orderText ._ul ._li ._h2 {
  float: left;
  line-height: 20px;
  font-size: 12px;
  padding-left: 8px;
  color: #777;
  font-weight: normal;
  float: right
}

.orderText ._ul ._li ._h2 ._a {
  color: #777;
}

.orderText ._ul ._li ._h3 {
  float: left;
  line-height: 20px;
  font-size: 14px;
  padding-left: 8px;
  color: #e0380b;
  float: right
}

.orderText ._ul ._li ._h3 ._a {
  color: #e0380b;
}

.orderText ._ul ._li ._h1 ._em {
  font-style: normal;
  font-weight: normal;
  padding-left: 10px;
  font-size: 16px;
  color: #999999;
}

.orderText ._ul ._li ._em {
  width: 22px;
  height: 22px;
  display: block;
  float: right;
  font-size: 16px;
}

.orderText ._ul ._li ._b {
  width: 10px;
  height: 10px;
  background: #ff0000;
  -moz-border-radius: 10px;
  -webkit-border-radius: 10px;
  border-radius: 10px;
  float: left;
  margin: 6px 0 0 5px;
}

.orderText ._ul ._li ._h1 ._strong {
  float: right;
  font-weight: normal;
}

.cartShow {
  background: #fff;
  width: 100%;
  overflow: hidden;
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

.cartShowRt {
  padding: 2% 2% 2% 0;
  float: right
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

.MHr40 {
  width: 100%;
  height: 40px;
}

.accountsBt {
  width: 100%;
  height: 105rpx;
  position: fixed;
  bottom: 0;
  left: 0;
  background: #fff;
  z-index: 99999;
  border: solid 1px #e7e7e7;
}

.order01 {
  width: 40%;
  height: 42px;
  float: left;
  font-size: 14px;
  line-height: 42px;
  text-align: right;
  padding-right: 3%;
  padding-top: 8px;
}

.order02 {
  width: 57%;
  height: 105rpx;
  float: left;
}

.chengse06 {
  color: #e15506;
}

.bt50 {
  background: #e0380b;
  text-align: center;
  line-height: 105rpx;
  border: 0;
  width: 100%;
  color: #fff;
  font-size: 14px;
  display: block
}

.bt50huise {
  background: #eeeeee;
  text-align: center;
  line-height: 105rpx;
  border: 0;
  width: 100%;
  color: #666;
  font-size: 14px;
}
</style>
