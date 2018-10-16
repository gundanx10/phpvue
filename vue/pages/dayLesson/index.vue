<template>
<div class="box">
  <div class="header">
    <img src="https://api.huyueyue.com/app/bg_boo.png" />
    <div class="header_title col">
      <div class="title">{{playTitle}}</div>
      <div class="row titleBox">
        <div class="title_l row">
          <img src="/static/images/music.png" />
          <div>每日一课精选课堂</div>
        </div>
        <div class="title_r">
          <img src="/static/images/open.png" />
        </div>
      </div>
    </div>
  </div>
  <div class="list">
    <div class="item col" v-for="item in dataList" :key="item.id">
      <div class="item_t row">
        <div class="item_title active">{{item.title}}</div>
        <img src="/static/images/music3.png" />
      </div>
      <div class="item_b row">
        <div class="item_b_1 row">
          <img src="/static/images/music1.png" />
          <div>{{item.views}}</div>
        </div>
        <div class="item_b_1 row">
          <img src="/static/images/time.png" />
          <div>{{item.duration}}</div>
        </div>
      </div>
    </div>
  </div>
</div>
</template>
<script>
import dataApi from '@/data/index'
export default {
  data() {
    return {
      playTitle: '',
      dataList: [],
      last_page: 0,
      page: 1
    }
  },
  components: {},
  computed: {},
  methods: {
    getList: function() {
      let that = this
      dataApi.getDailyLessonList(function(res) {
        that.data = res
        res.data.map(item => {
          that.dataList.push(item)
        })
        that.last_page = res.last_page
      }, that.page)
    }
  },
  onReachBottom: function() {
    if (this.page < this.last_page) {
      this.page++
        this.getList()
    }
    if (this.page === this.last_page) {
      wx.showToast({
        title: '没有更多内容了',
        icon: 'success',
        duration: 2000
      })
    }
  },
  mounted() {
    wx.hideLoading()
  },
  created() {
    wx.showLoading({
      title: '加载中'
    })
  },
  async onLoad() {
    this.dataList = []
    this.page = 1
    this.getList()
  },
  onShareAppMessage() {
    return {
      title: '装修知识汇',
      desc: '装修知识大放送',
      path: '/pages/index/main'
    }
  }
}
</script>
<style scoped>
.row {
  display: flex;
  flex-direction: row;
}

.col {
  display: flex;
  flex-direction: column;
}

.ellipsis {
  overflow: hidden;
  text-overflow: ellipsis;
  white-space: nowrap
}

.box {
  width: 750rpx;
  height: auto;
}

.header {
  width: 750rpx;
  height: 330rpx;
  position: relative;
}

.header img {
  width: 750rpx;
  height: 330rpx;
}

.header_title {
  width: 100%;
  position: absolute;
  top: 124rpx;
  padding: 0 30rpx;
  box-sizing: border-box;
}

.titleBox {
  width: 100%;
  justify-content: space-between;
}

.title {
  width: 100%;
  font-size: 28rpx;
  color: #fefefe;
  text-align: left;
}

.title_l {
  align-items: center;
  font-size: 21rpx;
  color: #ffffff;
}

.title_l img {
  width: 21rpx;
  height: 21rpx;
  margin-right: 12rpx;
}

.title_r {
  width: 120rpx;
  height: 120rpx;
}

.title_r img {
  width: 120rpx;
  height: 120rpx;
}

.list {
  width: 100%;
  height: auto;
}

.item {
  width: 100%;
  height: 130rpx;
  padding: 28rpx;
  box-sizing: border-box;
  border-bottom: 1px solid #f2f2f2;
  justify-content: space-between;
}

.item_t {
  width: 100%;
  justify-content: space-between;
  align-items: center;
}

.item_title {
  font-size: 28rpx;
  color: #3a3a3a;
}

.active {
  color: #d06814;
}

.item_t img {
  width: 21rpx;
  height: 21rpx;
}

.item_b {
  width: 100%;
}

.item_b_1 {
  font-size: 17rpx;
  color: #888888;
  margin-right: 50rpx;
  align-items: center;
}

.item_b_1 img {
  width: 15rpx;
  height: 16rpx;
  margin-right: 9rpx;
}
</style>
