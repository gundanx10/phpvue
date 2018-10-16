<template>
<div id="app" class="listbox">

  <div class="orderTabBox">
    <div class="orderTabIcon1On"><a>全部订单</a></div>
  </div>

  <div class="orderAllBox" v-for="item in dataList">
    <div class="orderAllTitle" v-if="item.pay_status==1"><b>已付款</b></div>
    <div class="orderAllTitle" v-else>未付款</div>
    <div class="cartShowRt">
      <h1>{{item.remark}}</h1>
    </div>
    <div class="orderAllText">
      <ul>
        <li>
          <h3>合计：<i>¥{{item.total_amount}}</i></h3>
        </li>
        <li class="noneno" v-on:click="goUrl('/pages/orderDetail/main?id='+item.order_id)">
          <h4><a class="topSeach" >订单详情</a></h4></li>
      </ul>
    </div>
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
      dataList: [],
      end: false,
      page: 0
    }
  },
  methods: {
    ...mapActions(['goUrl']),
    moredata: function () {
      const _this = this
      this.page = this.page + 1
      dataApi.getOrderList(function (res) {
        if(res.current_page === res.last_page) {
          _this.end = true
        }
        res.data.map(item => {
          _this.dataList.push(item)
        })
      }, this.page)
    }
  },
  mounted() {
    this.moredata()
  },
  onReachBottom: function () {
    if(this.end) {
      wx.showToast({
        title: '没有更多内容了',
        icon: 'success',
        duration: 2000
      })
    } else {
      this.moredata()
    }
  }
}
</script>
<style>
.orderTabBox {
  width: 100%;
  height: 58px;
  background: #fff;
  border-top: 1px solid #c9c9c9;
  border-bottom: 1px solid #c9c9c9;
  overflow: hidden;
}

.orderTabIcon1On {
  width: 33.33333%;
  float: left;
  height: 55px;
  background: url(http://zsh.huyueyue.com/images/d2.png) no-repeat center 10px;
  background-size: 20px;
  position: fixed;
  bottom: 0px;
  left: 0;
  position: relative;
  text-align: center;
  font-size: 0.75em;
  border-bottom: 3px solid #e0380b;
}

.orderTabIcon1On ._a {
  color: #e0380b;
  width: 100%;
  height: 20px;
  display: block;
  padding-top: 32px;
  font-size: 14px;
}

.orderAllBox {
  background: #fff;
  width: 94%;
  padding: 3% 3% 3% 3%;
  overflow: hidden;
  border-top: 12px solid #efeff0;
}

.orderAllTitle {
  width: 100%;
  padding: 0%;
  line-height: 30px;
  font-size: 12px;
  text-align: right;
  border-bottom: 1px solid #ddd;
  margin-bottom: 12px;
  color: #888;
}

.orderAllTitle ._b {
  color: #e15506;
  font-weight: normal
}

.orderAllText {
  width: 100%;
  padding: 0px;
  overflow: hidden;
  background: #fff;
  overflow: hidden;
  margin-top: 0px;
}

.orderAllText ._ul {
  margin: 0px;
  padding: 0px;
}

.orderAllText ._ul ._li {
  width: 96%;
  display: block;
  list-style-type: none;
  padding: 15px 2% 15px 2%;
  background: url(http://zsh.huyueyue.com/images/right.png) no-repeat -20px 8px;
  background-size: 22px;
  overflow: hidden;
  border-top: 1px solid #efeff0;
}

.orderAllText ._ul ._li ._span {
  width: 22px;
  height: 22px;
  display: block;
  float: left;
  font-size: 20px;
}

.orderAllText ._ul ._li ._h1 {
  float: left;
  line-height: 20px;
  font-size: 12px;
  padding-left: 8px;
  color: #777;
  font-weight: normal;
  float: left
}

.orderAllText ._ul ._li ._h1 ._a {
  color: #777;
}

.orderAllText ._ul ._li ._h2 {
  float: left;
  line-height: 20px;
  font-size: 12px;
  padding-left: 8px;
  color: #777;
  font-weight: normal;
  float: right
}

.orderAllText ._ul ._li ._h2 ._a {
  color: #777;
}

.orderAllText ._ul ._li ._h3 {
  float: left;
  line-height: 20px;
  font-size: 14px;
  padding-left: 8px;
  float: right;
  color: #666;
}

.orderAllText ._ul ._li ._h3 ._i {
  color: #e0380b;
  font-style: normal
}

.orderAllText ._ul ._li ._h3 ._a {
  color: #e0380b;
}

.orderAllText ._ul ._li ._h1 ._em {
  font-style: normal;
  font-weight: normal;
  padding-left: 10px;
  font-size: 16px;
  color: #999999;
}

.orderAllText ._ul ._li ._em {
  width: 22px;
  height: 22px;
  display: block;
  float: right;
  font-size: 16px;
}

.orderAllText ._ul ._li ._b {
  width: 10px;
  height: 10px;
  background: #ff0000;
  -moz-border-radius: 10px;
  -webkit-border-radius: 10px;
  border-radius: 10px;
  float: left;
  margin: 6px 0 0 5px;
}

.orderAllText ._ul ._li ._h1 ._strong {
  float: right;
  font-weight: normal;
}

.orderAllText ._ul ._li ._h4 {
  width: 26%;
  line-height: 30px;
  border-radius: 5px;
  border: 1px solid #ddd;
  display: block;
  float: right;
  font-size: 12px;
  font-weight: normal;
  text-align: center;
}

.orderAllText ._ul ._li ._h4 ._a {
  width: 100%;
  height: 100%;
  display: block;
  text-align: center
}

.topSeach {
  width: 15%;
  height: 38px;
  float: right;
  padding-top: 6px;
  position: relative;
  text-align: right;
}

.noneno {
  width: 96%;
  display: block;
  list-style-type: none;
  padding: 0px 2% 15px 2%;
  background: url(http://zsh.huyueyue.com/images/right.png) no-repeat -20px 8px;
  background-size: 22px;
  overflow: hidden;
  border-top: none
}

.cartShowRt {
  padding: 2% 2% 2% 0;
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
</style>
