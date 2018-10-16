<template>
<div class="box">
  <div class="header">
    <div class="title1">推广中心</div>
    <div class="col moneyBox">
      <div class="title2">可提现余额（元）</div>
      <div class="title3">{{data.balance}}</div>
      <div class="title2">今日收益：{{data.today_commission_sum}}元</div>
      <div class="title4" v-on:click="goUrl('/pages/getCash/main')">提现</div>
    </div>
  </div>
  <div class="list col">
    <div class="item row">
      <div class="item_l">收益历史</div>
      <div class="item_r">历史总收益：{{data.commission_sum}}元</div>
    </div>
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
</div>
</template>
<script>
import {
  mapGetters,
  mapActions
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
  computed: mapGetters({
    userInfo: 'getUserInfo',
    checkUser: 'checkUser'
  }),
  methods: {
    ...mapActions(['goUrl']),
    getList: function() {
      let that = this
      dataApi.withdrawInfo(function(res) {
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
  components: {},
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
  width: 100%;
  height: 502rpx;
  padding: 45rpx 38rpx 40rpx;
  box-sizing: border-box;
  background-image: linear-gradient(1deg,
  rgba(255, 123, 53, 1) 0%,
  rgba(255, 156, 71, 1) 100%);
}

.title1 {
  width: 114rpx;
  height: 45rpx;
  border: 1px solid #fff;
  border-radius: 3rpx;
  font-size: 21rpx;
  color: #ffffff;
  text-align: center;
  line-height: 45rpx;
}

.moneyBox {
  margin-top: 47rpx;
  align-items: center;
  line-height: 1.5;
}

.title2 {
  font-size: 29rpx;
  color: #ffffff;
}

.title3 {
  font-size: 110rpx;
  color: #ffffff;
}

.title4 {
  width: 356rpx;
  height: 70rpx;
  background-color: #ff9d47;
  border-radius: 9rpx;
  font-size: 29rpx;
  color: #ffffff;
  text-align: center;
  line-height: 70rpx;
  margin-top: 40rpx;
}

.list {
  width: 100%;
  height: auto;
}

.item {
  width: 750rpx;
  height: 71rpx;
  background-color: #f9f9f9;
  justify-content: space-between;
  align-items: center;
  font-size: 21rpx;
  padding: 0 21rpx 0 34rpx;
  box-sizing: border-box;
}

.item_l {
  color: #333333;
}

.item_r {
  color: #666666;
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
