<template>
<div>
  <div v-if="detail.charge === 1" class="playCharge">
    <img src="/static/images/clock-circular-outline.png" mode="widthFix"> &nbsp;课程是付费栏目内容
  </div>

  <div class="container">
    <image class="img" :src="detail.thumb" background-size="cover"></image>
    <div class='info'>
      <text class='title'>{{detail.title}}</text>
    </div>


    <div class='footer'>
      <div>
        <div class='slider'>
          <text>{{currentTime}}</text>
          <slider id="progressBar" @touchend="progressTouchEnd" @touchstart="progressTouchStart" @change="progressTouchMove" min="0" max="100" :value="sliderValue" />
          <text>{{endTime}}</text>
        </div>
        <div class='control'>
          <image v-on:click='prevHandler' src='/static/images/prev.svg' class='side' background-size="cover"></image>
          <image v-on:click='mainHandler' :src="playIcon" class='center' background-size="cover"></image>
          <image v-on:click='nextHandler' src='/static/images/next.svg' class='side' background-size="cover"></image>
          <image v-on:click='likeHandler' :src="likedIcon" class='side like' background-size="cover"></image>
        </div>
      </div>
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
      currentTime: '00:00', // 歌曲当前时间，分钟
      endTime: '00:00', // 歌曲总时长，分钟
      duration: 0, // 歌曲总时长，秒
      playIcon: '/static/images/pause.svg', // 播放图标
      likedIcon: '/static/images/unliked.svg', // 收藏，喜欢 图标
      sliderValue: 0,
      sliderIsChanging: false,
      detail: []
    }
  },
  components: {},
  methods: {
    ...mapActions(['goUrl']),
    moredata: function (id) {
      const _this = this
      dataApi.getKnowledgeDetail(function (res) {
        _this.detail = res
        _this.setMp3()
      }, id)
    },
    setMp3: function () {
      let mp3 = this.detail.radio_url
      if(!mp3) {
        return
      }
      // 设置背景音乐的相关属性
      const bam = wx.getBackgroundAudioManager()
      if(!bam.src && !bam.src !== mp3) {
        bam.src = mp3
        bam.title = this.detail.title
        bam.coverImgUrl = this.detail.thumb
      }

      const _this = this
      bam.onTimeUpdate(function () {
        _this.setDuration(bam.duration)
        // 实时更新slider长度，和左侧显示时间
        const currentTime = Math.floor(bam.currentTime)
        let seconds = Math.floor(currentTime % 60) + ''
        if(seconds.length === 1) {
          // 个位数的话，前面加0
          seconds = '0' + seconds
        }
        if(!_this.sliderIsChanging) {
          _this.currentTime = Math.floor(currentTime / 60) + ':' + seconds
          _this.sliderValue = Math.floor(currentTime / bam.duration * 100)
        }
      })
    },
    setDuration: function (duration) {
      // 设置歌曲时长，和转换后的分钟数 （显示时）
      let seconds = Math.floor(duration % 60) + ''
      if(seconds.length === 1) {
        // 个位数的话，前面加0
        seconds = '0' + seconds
      }
      this.duration = duration
      this.endTime = Math.floor(duration / 60) + ':' + seconds
    },
    mainHandler: function () {
      const bam = wx.getBackgroundAudioManager()
      if(bam.paused) {
        bam.play()
        this.playIcon = '/static/images/pause.svg'
      } else {
        bam.pause()
        this.playIcon = '/static/images/play.svg'
      }
    },
    progressTouchStart: function (e) {
      this.sliderIsChanging = true
    },
    progressTouchEnd: function (e) {
      this.sliderIsChanging = false
    },
    progressTouchMove: function (e) {
      const value = e.mp.detail.value
      const bam = wx.getBackgroundAudioManager()
      const currentTime = Math.floor(this.duration * value / 100)
      let seconds = Math.floor(currentTime % 60) + ''
      if(seconds.length === 1) {
        // 个位数的话，前面加0
        seconds = '0' + seconds
      }

      // 暂停时候，不能改变音乐的进度，所以只能先播放，再暂停了

      if(bam.paused) {
        bam.play()
        bam.seek(currentTime)
        bam.pause()
      } else {
        bam.seek(currentTime)
      }
      this.currentTime = Math.floor(currentTime / 60) + ':' + seconds
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
    this.moredata(id)
  },
  created() {

  },
  async onLoad() {

  },
  onShareAppMessage() {
    return {
      title: this.detail.title,
      desc: this.detail.title,
      path: '/pages/music/main?id=' + this.id
    }
  }
}
</script>
<style>
.playCharge {
  width: 100%;
  height: 100%;
  position: fixed;
  top: 0;
  left: 0;
  z-index: 1000;
  background: #000;
  opacity: 0.9;
  overflow: hidden;
  line-height: 500px;
  color: #fff;
  text-align: center;
}

.playCharge ._img {
  width: 20px
}

.container {
  display: flex;
  flex-direction: column;
  align-items: center;
  padding: 20rpx 40rpx 320rpx 40rpx;
}

.container .info {
  display: flex;
  flex-direction: column;
  box-shadow: 0 2px 12px 0 rgba(0, 0, 0, .1);
}

.singer,
.album {
  display: flex;
  align-items: center;
  font-size: 30rpx;
}

.singer .h4,
.album .h4 {
  margin-right: 7rpx;
  font-weight: bold;
}

.info {
  width: 100%;
  margin: 50rpx 0;
  box-sizing: border-box;
  padding: 20rpx;
}

.info>text {
  width: fit-content;
  margin: auto;
  font-size: 35rpx;
  font-weight: bold;
}

.footer {
  width: 100%;
  position: fixed;
  bottom: 0rpx;
  left: 0;
  background: #fff;
  height: 200rpx;
  padding: 20rpx 40rpx 0 40rpx;
  box-sizing: border-box;
  display: flex;
  align-items: center;
  box-shadow: 0 -10px 12px 0 rgba(0, 0, 0, .1);
}

.footer>div {
  width: 100%;
}

.footer .slider {
  display: flex;
  align-items: center;
  justify-content: space-around;
}

.slider>text {
  font-size: 30rpx;
  color: grey;
}

.footer .slider slider {
  width: 80%;
}

.footer .control {
  margin: auto;
  width: 50%;
  display: flex;
  align-items: center;
  justify-content: space-around;
  position: relative;
}

.footer .side {
  width: 50rpx;
  height: 50rpx;
}

.footer .comment {
  width: 45rpx;
  height: 45rpx;
  position: absolute;
  left: -100rpx;
  bottom: 18rpx;
}

.footer .center {
  width: 70rpx;
  height: 70rpx;
}

.footer .like {
  position: absolute;
  right: -100rpx;
  bottom: 8rpx;
}

.lyric text {
  text-align: right;
}
</style>
