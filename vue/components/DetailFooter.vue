<template>
<div>

  <div class="MHr"></div>

  <div class="pageFoot">
    <div class="pageReturn"><a v-on:click="back"><img src="http://zsh.huyueyue.com/images/fh.png" mode="widthFix"></a></div>
    <div class="pageBuy" v-on:click="buyConfirm()" v-if="detail.need_pay >0">
      购买
    </div>
    <div class="pageZan  heart pd32" v-bind:class="{ NavActive2: detail.likeStatus }" v-on:click="addLike(detail.id, getType)">
      <div class="likeCount" id="likeCount1">{{detail.likeCount}}</div>
    </div>
    <div class="pageLove" v-bind:class="{ NavActive: detail.favoriteStatus }" v-on:click="addFavorite(detail.id, getType)"><span>收藏</span></div>
    <div class="pagePinlun" v-on:click="goUrl('/pages/comments/main?id='+detail.id+'&type='+getType)"><span>{{detail.commentCount}}</span></div>
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
    return {}
  },
  props: ['detail'],
  computed: {
    getType: function () {
      let type = 'news'
      if(this.detail.catid !== 22) {
        type = 'knowledge'
      }
      return type
    }
  },
  created() {},
  methods: {
    ...mapActions(['goUrl']),
    addFavorite: function (id, type) {
      let _this = this
      dataApi.postFavorite(function (res) {
        _this.detail.favoriteStatus = res.status
      }, id, type)
    },
    addLike: function (id, type) {
      let _this = this
      dataApi.postLike(function (res) {
        _this.detail.likeStatus = res.status
        _this.detail.likeCount = res.count
      }, id, type)
    },
    back: function () {
      this.$router.go(-1)
    },
    buyConfirm: function () {
      this.$emit('buyConfirm')
    }
  },
  mounted() {}
}
</script>
<style>
.MHr {
  width: 100%;
  height: 20px;
}

.pageFoot {
  width: 100%;
  height: 55px;
  border-top: 1px solid #b2b2b2;
  position: fixed;
  bottom: 0;
  left: 0;
  background: #fff;
  z-index: 99;
}

.pageReturn {
  float: left;
  height: 42px;
  width: 40px;
  position: absolute;
  z-index: 9999;
  left: 5px;
  bottom: 0px
}

.pageReturn ._img {
  width: 30px;
}

.pagePinlun {
  width: 14%;
  float: right;
  height: 55px;
  background: url(http://zsh.huyueyue.com/images/on_pinglun.png) no-repeat center 10px;
  background-size: 20px;
  position: fixed;
  bottom: 0px;
  left: 0;
  position: relative;
  text-align: center;
  font-size: 0.75em;
}

.pagePinlun ._span {
  color: #7b3f38;
  width: 100%;
  height: 20px;
  display: block;
  padding-top: 32px;
}

.pageZan {
  width: 14%;
  float: right;
  height: 55px;
  background: url(http://zsh.huyueyue.com/images/off_zan.png) no-repeat center 10px;
  background-size: 20px;
  position: fixed;
  bottom: 0px;
  left: 0;
  position: relative;
  text-align: center;
  font-size: 0.75em;
}

.pageZan ._span {
  color: #7b3f38;
  width: 100%;
  height: 20px;
  display: block;
  font-size: 30rpx;
  line-height: 43rpx;
  padding-top: 32px;
}

.pageBuy {
  width: 17%;
  float: right;
  height: 55px;
  line-height: 55px;
  background-size: 20px;
  background: #f46412;
  bottom: 0px;
  left: 0;
  position: relative;
  text-align: center;
  color: #fff;
  font-size: 18px
}

.pageLove {
  width: 14%;
  float: right;
  height: 55px;
  background: url(http://zsh.huyueyue.com/images/off_love.png) no-repeat center 10px;
  background-size: 20px;
  position: fixed;
  bottom: 0px;
  left: 0;
  position: relative;
  text-align: center;
  font-size: 0.75em;
}

.pageLove ._span {
  color: #7b3f38;
  width: 100%;
  height: 20px;
  display: block;
  padding-top: 32px;
  font-size: 27rpx;
  line-height: 40rpx
}

.NavActive {
  background: url(http://zsh.huyueyue.com/images/on_love.png) no-repeat center 10px;
  background-size: 20px;
}

.pageShere {
  width: 14%;
  float: right;
  height: 55px;
  background: url(http://zsh.huyueyue.com/images/off_shere.png) no-repeat center 10px;
  background-size: 20px;
  position: fixed;
  bottom: 0px;
  left: 0;
  position: relative;
  text-align: center;
  font-size: 0.75em;
}

.pageShere ._span {
  color: #7b3f38;
  width: 100%;
  height: 20px;
  display: block;
  padding-top: 32px;
  font-size: 30rpx;
  line-height: 50rpx
}

.pd32 {
  padding-top: 32px;
  color: #7b3f38;
  padding-bottom: 0;
}

.NavActive2 {
  background: url(http://zsh.huyueyue.com/images/on_zan.png) no-repeat center 10px;
  background-size: 20px;
}

.pageBuy {
  width: 17%;
  float: right;
  height: 55px;
  line-height: 55px;
  background-size: 20px;
  background: #f46412;
  bottom: 0px;
  left: 0;
  position: relative;
  text-align: center;
  color: #fff;
  font-size: 18px
}
</style>
