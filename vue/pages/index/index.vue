<template>
<div>


  <div class="ad">
    <swiper :indicator-dots="indicatorDots" :autoplay="autoplay" :interval="interval" :duration="duration">
      <swiper-item class="item" v-for="(img, index) in imgs" :key="index">
        <img :src="img.src" mode="widthFix">
      </swiper-item>
    </swiper>
  </div>

  <div class="pagBox">
    <span><a href="/pages/dayLesson/main"><img src="https://api.huyueyue.com/app/t1.jpg" mode="widthFix"></a></span>
    <span><a href="/pages/goods/main"><img src="https://api.huyueyue.com/app/t2.jpg" mode="widthFix"></a></span>
    <span><a href="/pages/knowledge/main"><img src="https://api.huyueyue.com/app/t3.jpg" mode="widthFix"></a></span>
    <span><a href="/pages/interact/main"><img src="https://api.huyueyue.com/app/t4.jpg" mode="widthFix"></a></span>
  </div>

  <recent></recent>
  <guesslike></guesslike>
  <div class="MHr"></div>
</div>
</template>
<script>
import {
  mapGetters
} from 'vuex'
import guesslike from '@/components/GuessYourLike'
import recent from '@/components/Recent'
export default {
  data() {
    return {
      userId: null,
      imgs: [{
          src: 'https://api.huyueyue.com/app/banner1.jpg',
          href: ''
        },
        {
          src: 'https://api.huyueyue.com/app/banner2.jpg',
          href: '/'
        },
        {
          src: 'https://api.huyueyue.com/app/banner3.jpg',
          href: '/'
        },
        {
          src: 'https://api.huyueyue.com/app/banner4.jpg',
          href: '/'
        }
      ],
      indicatorDots: true,
      autoplay: true,
      interval: 5000,
      duration: 1000
    }
  },
  components: {
    guesslike,
    recent
  },
  methods: {

  },
  computed: mapGetters({
    userInfo: 'getUserInfo'
  }),
  mounted() {
    wx.hideLoading()
  },
  created() {
    wx.showLoading({
      title: '加载中'
    })
  },
  async onLoad() {

  },
  onShareAppMessage() {
    return {
      title: '装修知识汇',
      desc: '装修知识大放送',
      path: '/pages/index/main?pid=' + this.userInfo.id
    }
  }
}
</script>
<style>
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

.topDope {
  width: 15%;
  float: left;
  position: relative;
  text-align: left;
  line-height: 0px;
  padding-top: 10px;
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

.topSeach {
  width: 15%;
  height: 38px;
  float: right;
  padding-top: 6px;
  position: relative;
  text-align: right;
}

.topLogo {
  width: 70%;
  height: 44px;
  float: left;
  position: relative;
  text-align: center;
  color: #555;
}

.topText {
  width: 15%;
  height: 44x;
  float: right;
  position: relative;
  text-align: right;
  font-size: 12px;
  font-weight: normal;
  color: #666;
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
  height: 230rpx
}

swiper {
  height: 230rpx
}

/**************************标签****************************/

.pagBox {
  width: 97%;
  background: #fff;
  padding: 3% 0% 3% 0%;
  overflow: hidden;
}

.pagBox ._span {
  width: 22%;
  padding-left: 3%;
  float: left;
  display: block;
}

.pagBox ._span ._img {
  width: 100%;
  line-height: 0px;
}

.pagBox ._span ._a {
  width: 100%;
  height: 100%;
  display: block;
}

.MHr {
  width: 100%;
  height: 20px;
}
</style>
