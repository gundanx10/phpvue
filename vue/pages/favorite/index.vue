<template>
<div id="app" class="listbox">

  <div class="successBox2" v-for="item in dataList" :key="item.id" v-on:click="goUrl(item.app_url)">
    <div class="successBox2Show">
      <div class="successCenter">
        <div class="successIcon">
          <img :src="item.thumb" mode="aspectFill" />
        </div>
        <div class="successRt99">
          <h1>{{item.title}}</h1>
          <h2>{{item.created_at}}<b class="zanweifu"></b></h2>
          <h3>{{item.description}}</h3>
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
import filters from '@/common/filters'

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
    moredata: function() {
      const _this = this
      this.page = this.page + 1
      dataApi.getFavoriteList(function(res) {
        if (res.current_page === res.last_page) {
          _this.end = true
        }
        console.log(res)
        res.data.map(item => {
          item = Object.assign(item, {
            created_at: filters.toDate(item.created_at),
            description: filters.foramtIntercept(item.description, 80),
            title: filters.foramtIntercept(item.title, 35),
            thumb: filters.foramtImage(item.thumb, 300)
          })
          _this.dataList.push(item)
        })
      }, this.page)
    }
  },
  onLoad: function(options) {
    this.page = 0
    this.dataList = []
    this.end = false
    this.moredata()
  },
  mounted() {},
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
      title: '装修知识汇',
      desc: '你想要的都在这里',
      path: '/pages/index/main'
    }
  }
}
</script>
<style>
.successBox2 {
  width: 100%;
  overflow: hidden;
}

.successBox2Show {
  width: 100%;
}

.successCenter {
  width: 95%;
  padding: 3% 2.5% 3% 2.5%;
  font-size: 16px;
  line-height: 16px;
  color: #666;
  border-top: 1px solid #e3e3e3;
  overflow: hidden;
  background: #fff;
}

.successRt {
  width: 77.5555555%;
  float: right;
}

.successRt ._h1 {
  line-height: 22px;
  color: #666;
  width: 100%;
  display: block;
  font-size: 14px;
  padding-bottom: 5px;
}

.successRt ._h1 ._b {
  line-height: 22px;
  color: #666;
  display: block;
  font-size: 12px;
  background: url(http://zsh.huyueyue.com/images/right.png) no-repeat right 0px;
  background-size: 22px;
  float: right;
  font-weight: normal;
  padding-right: 6%;
}

.successRt ._h2 {
  line-height: 22px;
  color: #666;
  width: 100%;
  display: block;
  font-weight: normal;
  font-size: 14px;
  color: #481d02;
}

.successRt ._h3 {
  line-height: 22px;
  color: #666;
  width: 100%;
  display: block;
  font-weight: normal;
  font-size: 12px;
  color: #999999;
  line-height: 20px;
}

.successIcon {
  width: 160rpx;
  height: 160rpx;
  border-radius: 80rpx;
  float: left;
  line-height: 160rpx;
  color: #fff;
  text-align: center;
  overflow: hidden;
}

.successIcon ._img {
  border-radius: 80rpx;
  width: 444rpx
}

.successRt99 {
  width: 77.5555555%;
  float: right;
  background: url(http://zsh.huyueyue.com/images/right.png) no-repeat right 24px;
  background-size: 22px;
}

.successRt99 ._h1 {
  line-height: 22px;
  color: #eb5d00;
  width: 100%;
  display: block;
  font-size: 14px;
  padding-bottom: 5px;
}

.successRt99 ._h2 ._b {
  line-height: 22px;
  color: #666;
  display: block;
  font-size: 12px;
  float: right;
  font-weight: normal;
  padding-right: 6%;
  width: 30px;
}

.successRt99 ._h2 {
  line-height: 22px;
  color: #999;
  width: 100%;
  display: block;
  font-weight: normal;
  font-size: 14px;
}

.successRt99 ._h3 {
  line-height: 22px;
  color: #999;
  width: 100%;
  display: block;
  font-weight: normal;
  font-size: 12px;
  color: #999999;
  line-height: 20px;
}

.zanweifu {
  width: 30px;
}

.successIcon {
  width: 160rpx;
  height: 160rpx;
  border-radius: 80rpx;
  float: left;
  line-height: 160rpx;
  color: #fff;
  text-align: center;
  overflow: hidden;
}

.successIcon ._img {
  width: 160rpx;
  height: 160rpx
}
</style>
