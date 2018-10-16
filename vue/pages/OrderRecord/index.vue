<template>
<div class="box">
  <div class="contentList">
    <div class="contentList_item col" v-for="item in dataList" :key="item.id">
      <div class="contentList_itemtop row">
        <div class="t_title ellipsis" v-if="item.info.catid === 20">推广佣金（{{item.info.title}}）</div>
        <div class="t_title ellipsis" v-else>推广佣金（购买栏目-{{item.info.catname}}）</div>
        <div class="t_title1">{{item.price}}</div>
      </div>
      <div class="contentList_itembottom row">
        <div class="time">{{item.created_time}}</div>
        <div class="time">购买人：{{item.user.nickname}}</div>
      </div>
    </div>

  </div>
</div>
</template>
<script>
import {
  mapGetters
} from 'vuex'
import dataApi from '@/data/index'
export default {
  data() {
    return {
      data: [],
      dataList: [],
      last_page: 0,
      page: 1
    }
  },
  components: {},
  computed: mapGetters({
    userInfo: 'getUserInfo',
    checkUser: 'checkUser'
  }),
  methods: {
    getList: function() {
      let that = this
      dataApi.commissionLog(function(res) {
        that.data = res
        res.data.map(item => {
          that.dataList.push(item)
        })
        that.last_page = res.last_page
      }, that.page)
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

.contentList {
  width: 100%;
  height: auto;
  padding: 0 29rpx;
  box-sizing: border-box;
}

.contentList_item {
  width: 100%;
  height: 158rpx;
  border-bottom: 1px solid #f2f2f2;
  justify-content: space-between;
  padding: 42rpx 0;
  box-sizing: border-box;
}

.contentList_itemtop,
.contentList_itembottom {
  width: 100%;
  justify-content: space-between;
}

.t_title {
  width: 531rpx;
  font-size: 25rpx;
  color: #333333;
}

.t_title1 {
  font-size: 25rpx;
  color: #333333;
}

.time {
  font-size: 21rpx;
  color: #888888;
}
</style>
