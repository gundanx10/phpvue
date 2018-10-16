<template>
<div id="app" class="listbox">
  <div class="search">
    <input type="text" placeholder="搜索" confirm-type="search" @focus="goUrl('/pages/search/main')" />
    <icon type="search" size="18"></icon>
  </div>

  <div class="ad">
    <swiper :indicator-dots="indicatorDots" :autoplay="autoplay" :interval="interval" :duration="duration">
      <swiper-item class="item" v-for="(img, index) in imgs" :key="index">
        <img :src="img.src" mode="widthFix">
      </swiper-item>
    </swiper>
  </div>

  <div class="godBox" v-for="item in dataList" :key="item.id" v-on:click="goUrl('../goodsDetail/main?id='+item.id)">
    <div class="godShow">
      <div class="godShowLf"><img :src="item.thumb" mode="widthFix"></div>
      <div class="godShowRt">
        <h1>{{item.title}}</h1>
        <h2>{{item.description}}</h2>
        <h3>¥{{item.price}}<b>已有{{item.buys}}人付款</b></h3>
      </div>
    </div>
  </div>

  <div class="MHr"></div>
</div>
</template>

<script>
import {
  mapGetters,
  mapActions
} from 'vuex'
export default {
  data() {
    return {
      imgs: [{
          src: 'http://zsh.huyueyue.com/images/WechatIMG759.jpeg'
        },
        {
          src: 'http://zsh.huyueyue.com/images/WechatIMG758.jpeg'
        }
      ],
      indicatorDots: true,
      autoplay: true,
      interval: 5000,
      duration: 1000
    }
  },
  computed: mapGetters({
    dataList: 'getGoodsList',
    end: 'getGoodsEnd',
    userInfo: 'getUserInfo'
  }),
  methods: {
    ...mapActions(['goUrl']),
    moredata: function() {
      this.$store.dispatch('getGoodsList')
    }
  },
  onLoad: function(options) {
    this.moredata()
  },
  onReachBottom: function() {
    if (this.end) {
      wx.showToast({
        title: '没有更多内容了',
        icon: 'success',
        duration: 2000
      })
    } else {
      this.moredata()
    }
  },
  onShareAppMessage() {
    return {
      title: '装修知识汇商城',
      desc: '你想要的都在这里',
      path: '/pages/goods/main?pid=' + this.userInfo.id
    }
  }
}
</script>
<style>
.search {
  width: 100%;
  height: 96rpx;
  top: 140rpx;
  background: #f2f2f2;
  padding: 18rpx 0;
  box-sizing: border-box;
}

.search input {
  width: 706rpx;
  height: 60rpx;
  background: #f2f2f2;
  border-radius: 6rpx;
  margin: auto;
  font-size: 26rpx;
  color: #999;
  box-sizing: border-box;
  background: #fff;
  text-align: center;
}

.search icon {
  position: absolute;
  top: 31rpx;
  margin-left: 40%;
  z-index: 2;
}

.topBox {
  height: 44px;
  width: 94%;
  background: #fff;
  padding: 0 3% 0 3%;
  font-size: 16px;
  position: relative;
  line-height: 44px;
  color: #7b3f38;
  font-weight: bold;
  text-align: center;
}

.topTips {
  position: absolute;
  width: 10px;
  height: 10px;
  border-radius: 10px;
  background: #ff3026;
  top: 13px;
  left: 22px;
}

.topLogo ._img {
  width: 22px
}

.topLogo {
  width: 100%;
  height: 44px;
  float: left;
  position: relative;
  text-align: center;
  color: #555;
}

.MHr {
  width: 100%;
  height: 20px;
}

/**************************广告****************************/

.ad {
  width: 100%;
  overflow: hidden;
  line-height: 0px;
}

.ad ._img {
  width: 100%;
  line-height: 0px;
}

._swiper {
  height: 335rpx
}

swiper {
  height: 335rpx
}

.godBox {
  background: #efeff0;
  width: 94%;
  padding: 3% 3% 0% 3%;
  overflow: hidden;
}

.godShow {
  background: #fff;
  width: 100%;
  overflow: hidden;
}

.godShowLf {
  width: 43%;
  line-height: 0px;
  float: left;
}

.godShowLf ._img {
  width: 100%;
  line-height: 0px;
}

.godShowRt {
  width: 52%;
  float: right;
  padding: 2% 2% 2% 0;
}

.godShowRt ._h1 {
  width: 100%;
  display: block;
  color: #562020;
  font-size: 14px;
}

.godShowRt ._h2 {
  width: 100%;
  display: block;
  font-size: 14px;
  color: #666;
  font-size: 12px;
  font-weight: normal;
}

.godShowRt ._h3 {
  width: 100%;
  display: block;
  color: #e15506;
  font-size: 14px;
  font-size: 14px;
  padding-top: 5px;
}

.godShowRt ._h3 ._b {
  color: #929292;
  float: right;
  font-weight: normal;
}
</style>
