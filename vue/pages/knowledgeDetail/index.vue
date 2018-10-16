<template>
<div id="app" class="listbox">

  <div class="audioBox">
    <div class="audioTab01On"><a>视频</a></div>
    <div class="audioTab03Off"><a v-on:click="goUrl('../music/main?id='+detail.id)">音频</a></div>
  </div>

  <div>
    <div class="audioTitle">{{detail.title}}</div>

    <div class="videoShow">
      <video :src="videoSrc" controls v-if="detail.need_pay <1">
      </video>

      <div v-else class="playCharge">
        <img :src="detail.thumb" mode="aspectFill">
        <div class="programaCharge"><img src="/static/images/clock-circular-outline.png" mode="widthFix"> &nbsp;课程是付费栏目内容</div>
      </div>
    </div>

    <div class="audioTitle2">
      <h1>1</h1>
      <h2>知识点汇总</h2>
    </div>
    <div class="audioText">
      <div class="audioTextShow">
        <wxParse v-if="detail.knowledge_content" :content="detail.knowledge_content">
        </wxParse>
      </div>
    </div>
    <div class="audioTitle2">
      <h1>2</h1>
      <h2>思维导图</h2>
    </div>
    <div class="audioText">
      <div class="audioTextShow"><img :src="detail.mind_mapping" mode="widthFix"></div>
    </div>

  </div>

  <div>
    <div class="audioTitle4">
      <h1></h1>
      <h2>课件PPT</h2>
    </div>
    <div class="audioText2">
      <div class="audioTextShow">
        <wxParse v-if="detail.content" :content="detail.content">
        </wxParse>
      </div>
    </div>
  </div>

  <guesslike v-bind:id="detail.id" v-bind:cid="detail.catid"></guesslike>

  <payPopup @payConfirm="payConfirm" @closePopUp="closePopUp($event)" v-bind:showPopUP="showPopUP" v-bind:detail="detail"></payPopup>

  <auth v-if="checkUser"></auth>


  <div class="MHr"></div>
  <detailFooter v-bind:detail="detail" @buyConfirm="buyConfirm"></detailFooter>

</div>
</template>

<script>
import {
  mapGetters,
  mapActions
} from 'vuex'
import wxParse from 'mpvue-wxparse'
import polyv from '@/utils/polyv'
import guesslike from '@/components/GuessYourLike'
import dataApi from '@/data/index'
import filters from '@/common/filters'
import detailFooter from '@/components/DetailFooter'
import payPopup from '@/components/PayPopup'
import auth from '@/components/Auth'

export default {
  data() {
    return {
      id: 0,
      newVideoSrc: '',
      detail: [],
      showPopUP: false
    }
  },
  computed: {
    videoSrc: function() {
      const _this = this
      if (this.detail.video_id) {
        polyv.getVideo(this.detail.video_id, function(videoInfo) {
          if (!videoInfo.error) {
            _this.newVideoSrc = videoInfo.src[1] + '?ts=' + _this.detail.ts + '&sign=' + _this.detail.hash
          }
        }, this.detail.ts, this.detail.hash)
        return this.newVideoSrc
      }
    },
    ...mapGetters({
      checkUser: 'checkUser'
    })
  },
  components: {
    wxParse,
    guesslike,
    detailFooter,
    payPopup,
    auth
  },
  methods: {
    ...mapActions(['goUrl']),
    moredata: function(id) {
      const _this = this
      dataApi.getKnowledgeDetail(function(res) {
        res = Object.assign(res, {
          content: filters.trimHtml(res.content),
          knowledge_content: filters.trimHtml(res.knowledge_content)
        })
        _this.detail = res
      }, id)
    },
    buyConfirm: function() {
      this.showPopUP = true
    },
    payConfirm: function() {
      this.showPopUP = false
      let params = {
        type: 'course',
        total: this.detail.cat_price,
        cid: this.detail.catid,
        cat_name: this.detail.pay_catname,
        from: 'app'
      }
      dataApi.postCreateOrder(function(res) {
        wx.requestPayment({
          'timeStamp': res.timestamp,
          'nonceStr': res.nonceStr,
          'package': res.package,
          'signType': 'MD5',
          'paySign': res.paySign,
          'success': function(rs) {
            let url = '/pages/payed/main?id=' + res.out_trade_no
            wx.navigateTo({
              url: url
            })
          },
          'fail': function(rs) {}
        })
      }, params)
    },
    closePopUp: function(status) {
      this.showPopUP = status
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
    this.moredata(id)
  },
  beforeMount() {},
  onShareAppMessage() {
    return {
      title: this.detail.title,
      desc: this.detail.title,
      path: '/pages/knowledgeDetail/main?id=' + this.id
    }
  }
}
</script>
<style>
.audioBox {
  width: 94%;
  height: 36px;
  padding: 3%;
}

.audioTab01On {
  width: 33%;
  height: 32px;
  background: #562020;
  border-radius: 5px 0 0 5px;
  text-align: center;
  line-height: 32px;
  font-size: 14px;
  color: #fff;
  float: left;
}

.audioTab01On ._a {
  color: #fff;
}

.audioTab01Off {
  width: 33%;
  height: 30px;
  border-radius: 5px 0 0 5px;
  text-align: center;
  line-height: 32px;
  font-size: 14px;
  color: #562020;
  border: 1px solid #7b3f38;
  float: left;
}

.audioTab01Off ._a {
  color: #562020;
}

.audioTab02On {
  width: 32%;
  height: 32px;
  background: #562020;
  text-align: center;
  line-height: 32px;
  font-size: 14px;
  color: #fff;
  float: left;
}

.audioTab02On ._a {
  color: #fff;
}

.audioTab02Off {
  width: 32%;
  height: 30px;
  text-align: center;
  line-height: 32px;
  font-size: 14px;
  color: #562020;
  border: 1px solid #7b3f38;
  border-right: none;
  border-left: none;
  float: left;
}

.audioTab02Off ._a {
  color: #562020;
}

.audioTab03On {
  width: 33%;
  height: 32px;
  background: #562020;
  border-radius: 0px 5px 5px 0;
  text-align: center;
  line-height: 32px;
  font-size: 14px;
  color: #fff;
  float: left;
}

.audioTab03On ._a {
  color: #fff;
}

.audioTab03Off {
  width: 33%;
  height: 30px;
  border-radius: 0px 5px 5px 0px;
  text-align: center;
  line-height: 32px;
  font-size: 14px;
  color: #562020;
  border: 1px solid #7b3f38;
  float: left;
}

.audioTab03Off ._a {
  color: #562020;
}

.audioTitle {
  width: 94%;
  padding: 0 3% 3% 3%;
  line-height: 22px;
  color: #562020;
}

.audioShow {
  width: 94%;
  padding: 0 3% 3% 3%;
}

.videoShow {
  width: 96%;
  padding: 0 2% 3% 2%;
}

.VideoShow ._video {
  width: 100%;
  height: 240px
}

.videoShow video {
  width: 96%;
  height: 240px;
  padding: 0% 2%;
}

.audioShow ._img {
  width: 100%;
}

.audioTitle2 {
  width: 94%;
  padding: 0 3% 3% 3%;
  overflow: hidden;
}

.audioTitle2 ._h1 {
  background: #84ccc9;
  line-height: 30px;
  font-size: 14px;
  color: #fff;
  display: block;
  float: left;
  padding: 0 10px 0 10px;
  float: left
}

.audioTitle2 ._h2 {
  background: #313131;
  line-height: 30px;
  font-size: 14px;
  color: #fff;
  display: block;
  float: left;
  padding: 0 10px 0 10px;
  float: left
}

.audioText {
  width: 94%;
  padding: 0 3% 3% 3%;
}

.audioTextShow {
  width: 94%;
  padding: 3% 3% 3% 3%;
  background: #eeeeee;
  line-height: 24px;
  overflow: hidden;
  font-size: 14px;
  color: #333
}

.audioTextShow ._img {
  width: 100%;
}

.pptAd {
  width: 94%;
  padding: 0 3% 3% 3%;
  line-height: 0px;
}

.pptAd ._img {
  width: 100%;
  line-height: 0px;
}

.pptPhoto {
  width: 94%;
  padding: 3%;
  text-align: center;
  overflow: hidden;
}

.pptPhoto ._span {
  display: block;
  margin: 0 auto 0 auto;
  width: 35%;
}

.pptPhoto ._span ._img {
  width: 100%;
}

.pptPhoto ._h1 {
  font-size: 14px;
  line-height: 30px;
}

.audioTitle4 {
  width: 94%;
  padding: 0 3% 3% 3%;
  overflow: hidden;
}

.audioTitle4 ._h1 {
  background: #313131;
  line-height: 30px;
  font-size: 14px;
  color: #fff;
  display: block;
  float: left;
  padding: 30px 3px 0 0;
}

.audioTitle4 ._h2 {
  background: #1e9d9a;
  line-height: 30px;
  font-size: 14px;
  color: #fff;
  display: block;
  float: left;
  padding: 0 10px 0 10px;
}

.audioText2 {
  width: 94%;
  padding: 0 3% 3% 3%;
}

.audioText2Show {
  width: 100%;
  line-height: 24px;
  overflow: hidden;
  font-size: 14px;
  color: #333;
  padding-top: 3%
}

.audioText2Show ._img {
  width: 100%;
}

.MHr {
  width: 100%;
  height: 40px;
}

.audioCharge {
  width: 100%;
  height: 77px;
  overflow: hidden;
  position: relative;
}

.playCharge {
  width: 100%;
  height: 220px;
  overflow: hidden;
  position: relative;
}

.playCharge .programaCharge {
  line-height: 220px;
}

.playCharge ._img {
  width: 100%
}

.programaCharge {
  width: 100%;
  height: 100%;
  position: absolute;
  top: 0;
  text-align: center;
  line-height: 77px;
  background: #000;
  opacity: 0.9;
  color: #fff
}

.playCharge .programaCharge ._img {
  width: 20px
}
</style>
