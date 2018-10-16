<template>
<div id="app" class="listbox">

  <div class="shopPic">
    <img :src="detail.thumb" mode="widthFix"></div>
  <div class="shopTitle">
    <div class="shopTitleLf">¥ {{detail.price}}</div>
    <div class="shopTitleRt">
      <h1>{{detail.buys}} 件已售</h1>
    </div>

  </div>
  <div class="shopHead">
    <div class="shopHeadTop">
      <div class="shopHeadTopLf">
        <h1>{{detail.title}}</h1>
        <h2 v-html="detail.description"></h2>
      </div>
      <div class="shopHeadTopRt"></div>
    </div>
    <div class="shopBottom">运费：
      <font v-if="detail.freight>0">{{detail.freight}}</font>
      <font v-else>免费</font><span>剩余：{{detail.inventory}}</span></div>
  </div>
  <div class="huidu20"></div>
  <div class="detailedTitle">商品详情</div>
  <div class="detailedShow">
    <wxParse v-if="detail.content" :content="detail.content">
    </wxParse>
  </div>
  <div class="MHr40"></div>
  <!--误删，65px的高度-->

  <div class="cartBt">
    <div class="cart01" v-on:click="goUrl('../cart/main')">购物车</div>
    <div class="cart02" v-on:click="addCart(detail)"><span class="bt42">加入购物车</span></div>
    <div class="cart03" v-on:click="quickOrder(detail)"><span class="bt43">立即购买</span></div>
  </div>
</div>
</template>

<script>
import {
  mapGetters,
  mapActions
} from 'vuex'
import wxParse from 'mpvue-wxparse'
export default {
  data() {
    return {
      id: 0
    }
  },
  computed: mapGetters({
    detail: 'getGoodsDetail',
    userInfo: 'getUserInfo'
  }),
  components: {
    wxParse
  },
  methods: {
    ...mapActions(['addCart', 'quickOrder', 'goUrl']),
    moredata: function() {
      this.$store.dispatch('getGoodsDetail', this.id)
    }
  },
  onLoad: function() {
    const {
      id
    } = this.$route.query
    this.id = id
    if (!id) {
      this.$router.back()
    }
    this.moredata()
  },
  onShareAppMessage() {
    return {
      title: this.detail.title,
      desc: this.detail.title,
      path: '/pages/goodsDetail/main?id=' + this.id + '&uid=' + this.userInfo.id
    }
  }
}
</script>
<style>
.shopPic {
  width: 100%;
  padding: 0;
  margin: 0;
  line-height: 0px;
}

.shopPic ._img {
  width: 100%;
  line-height: 0px;
}

.shopTitle {
  width: 95%;
  padding: 0 2.5% 0 2.5%;
  background: #e0380b;
  height: 60px;
}

.shopTitleLf {
  width: 20%;
  line-height: 60px;
  font-size: 18px;
  color: #fff;
  float: left;
}

.shopTitleCt {
  float: left;
}

.shopTitleCt ._h1 {
  text-decoration: line-through;
  font-size: 14px;
  padding-top: 8px;
  color: #ffad7f;
  font-weight: normal;
}

.shopTitleCt ._h2 {
  color: #ffad7f;
  font-weight: normal;
  background: #c22d05;
  padding: 2px;
  font-size: 14px;
  display: block;
  border-radius: 2px;
}

.shopTitleRt {
  float: right;
}

.shopTitleRt ._h1 {
  font-size: 12px;
  padding-top: 8px;
  color: #fff;
  font-weight: normal;
  padding-bottom: 5px;
  text-align: right;
}

.shopTitleRt ._h2 {
  width: 100%;
  font-size: 0.75em;
  font-weight: normal;
}

.shopTitleRt ._h2 ._i {
  width: 100%;
  font-size: 0.75em;
  font-style: normal;
  color: #fff;
}

.shopTitleRt ._h2 ._b {
  border-radius: 3px;
  background: #fff;
  font-size: 0.75em;
  font-weight: normal;
  padding: 4px;
  margin-left: 2px;
  color: #e15506;
}

.shopHead {
  border-bottom: solid 1px #c9c9c9;
  padding-bottom: 2.5%;
  width: 95%;
  padding: 0 2.5% 0 2.5%;
}

.shopHeadTop {
  width: 100%;
  border-bottom: solid 1px #e7e7e7;
  padding-bottom: 2.5%;
  overflow: hidden;
}

.shopHeadTopLf {
  width: 90%;
  padding-top: 8px;
  float: left;
}

.shopHeadTopLf ._h1 {
  color: #562020;
  font-size: 12px;
  padding-bottom: 4px;
}

.shopHeadTopLf ._h2 {
  color: #666;
  font-size: 12px;
}

.shopHeadTopRt {
  float: right;
  width: 10%;
  padding-top: 8px;
}

.shopHeadTopRt ._img {
  width: 100%;
}

.shopBottom {
  width: 100%;
  color: #999;
  font-size: 12px;
  padding: 2.5% 0 2.5% 0
}

.shopBottom ._span {
  float: right;
}

.huidu20 {
  background: #efeff0;
  padding: 3% 0 0 0;
  border-bottom: solid 1px #c9c9c9;
}

.detailedBox {
  width: 100%;
  overflow: hidden;
}

.detailedShow {
  width: 95%;
  padding: 3% 2.5% 3% 2.5%;
  margin-bottom: 2.5%;
  background: #fff;
  overflow: hidden
}

.detailedShow ._h1 {
  width: 100%;
  line-height: 14px;
  font-size: 14px;
  padding-bottom: 8px;
  color: #999;
  font-weight: normal;
}

.detailedShow ._h2 {
  width: 100%;
  line-height: 14px;
  font-size: 15px;
  padding-bottom: 8px;
  color: #999;
  font-weight: normal;
  line-height: 24px;
  color: #666;
}

.detailedShow ._h2 ._b {
  font-weight: normal;
  color: #999;
}

.MHr40 {
  width: 100%;
  height: 55px;
}

.cartShowIco {
  width: 100%;
  background: #fff;
  text-align: center;
}

.cartShowIco ._img {
  width: 30%;
  padding-top: 30px;
}

.cartShowText {
  width: 100%;
  text-align: center;
  padding-top: 3%;
}

.cartShowText ._h1 {
  font-size: 14px;
  color: #562020;
  line-height: 24px;
}

.cartShowText ._h2 {
  font-size: 14px;
  color: #666666;
  line-height: 24px;
}

.cartShowBt {
  width: 40%;
  height: 40px;
  padding: 0 30% 0 30%;
  padding-top: 20px;
}

.cartShowBt ._span {
  border: solid 1px #f46412;
  line-height: 40px;
  display: block;
  text-align: center;
  color: #f46412;
}

.cartShowBt ._span ._a {
  color: #f46412;
  width: 100%;
  height: 100%;
  display: block;
}

.bt42 {
  background: #f46412;
  text-align: center;
  line-height: 55px;
  border: 0;
  width: 100%;
  color: #fff;
  font-size: 14px;
  display: block
}

.bt43 {
  background: #e0380b;
  text-align: center;
  line-height: 55px;
  border: 0;
  width: 100%;
  color: #fff;
  font-size: 14px;
  display: block
}

.cartBt {
  width: 100%;
  height: 110rpx;
  position: fixed;
  bottom: 0;
  left: 0;
  background: #fff;
  z-index: 999;
}

.cart01 {
  width: 16%;
  height: 22px;
  background: #fff url(http://zsh.huyueyue.com/images/cart.png) no-repeat center 10px;
  background-size: 14px;
  float: left;
  font-size: 0.75em;
  padding-top: 25px;
  text-align: center;
}

.cart02 {
  width: 42%;
  height: 55px;
  float: left;
}

.cart03 {
  width: 42%;
  height: 55px;
  float: left;
}
</style>
