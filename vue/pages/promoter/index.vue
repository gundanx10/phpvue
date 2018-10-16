<template>
<div class="box">
  <div class="header row">
    <div class="header_l row" :class="{'active': type === 0}" @click="type = 0">
      <div>已绑定（{{data.count}}）</div>
    </div>

  </div>
  <div class="list col">
    <div class="item row" v-for="item in dataList" :key="item.id">
      <div class="item_l row">
        <img :src="item.head" />
        <div class="name">{{item.nickname}}</div>
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
      type: 0,
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
  components: {},
  methods: {
    getList: function() {
      let that = this
      dataApi.promotionUser(function(res) {
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
  height: 95rpx;
  border-top: 1px solid #f2f2f2;
  border-bottom: 1px solid #f2f2f2;
  align-items: center;
}

.header_l {
  width: 50%;
  text-align: center;
  font-size: 25rpx;
  color: #666;
  justify-content: center;
}

.active>div {
  width: 50%;
  height: 100%;
  line-height: 4;
  text-align: center;
  color: #333;
  border-bottom: 5rpx solid #fd6b31;
}

.list {
  width: 100%;
  height: auto;
  padding: 0 36rpx 0 20rpx;
  box-sizing: border-box;
}

.item {
  width: 100%;
  height: 136rpx;
  border-bottom: 1px solid #f2f2f2;
  justify-content: space-between;
  align-items: center;
}

.item img {
  width: 98rpx;
  height: 98rpx;
  -webkit-border-radius: 100%;
  -moz-border-radius: 100%;
  border-radius: 100%;
  margin-right: 43rpx;
}

.item_l {
  align-items: center;
}

.name {
  font-size: 33rpx;
  color: #333333;
}

.item_r {
  font-size: 25rpx;
  color: #ef5f08;
}
</style>
