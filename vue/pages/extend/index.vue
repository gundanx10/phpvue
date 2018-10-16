<template>
<div>
  <div class="header">
    <img src="https://api.huyueyue.com/app/bg.png" class="bg" />
    <div class="headerContent row">
      <div class="row">
        <img :src="userInfo.head" class="avator" />
        <div class="col nameBox">
          <div class="text ellipsis">{{userInfo.nickname}}</div>
          <div class="people">普通推广员</div>
        </div>
      </div>
      <div class="goGao" v-on:click="goUrl('/pages/plan/main')">成为高级推广员</div>
    </div>
  </div>
  <div class="numBox col">
    <div class="numBox_t col">
      <div class="numBox_title">累计推广收益</div>
      <div class="row money">
        <div class="money_l"><span>￥</span>{{data.commission_sum}}</div>
        <div class="money_r row">
          <div class="money_r_text" v-on:click="goUrl('/pages/income/main')">立即提现</div>
          <img src="/static/images/right1.png">
        </div>
      </div>
    </div>
    <div class="numBox_b row">
      <div class="numBox_b_l row" v-on:click="goUrl('/pages/promoter/main')">累计客户<span>{{data.promotion_user_count}}</span></div>
      <div class="numBox_b_r row" v-on:click="goUrl('/pages/OrderRecord/main')">推广订单<span>{{data.commission_count}}</span></div>
    </div>
  </div>
  <div class="title1">推广商品<span>*商户更改佣金比例后会实时更新，请注意查看</span></div>
  <div class="list col">
    <div class="item row" v-for="item in dataList" :key="item.id">
      <div class="item_l row">
        <img :src="item.info.thumb" mode="aspectFill" />
        <div class="col title_all">
          <div class="title2">{{item.info.title}}</div>
          <div class="title3">佣金比例：{{item.commission}}%</div>
          <div class="title4">预计收益：{{item.promotion_money}}</div>
        </div>
      </div>
      <button class="item_r" open-type="share">推广</button>
    </div>

  </div>
  <!-- <div class="noData">以加载全部</div> -->
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
      page: 1,
      shareimg: ''
    }
  },
  components: {},
  computed: mapGetters({
    userInfo: 'getUserInfo',
    checkUser: 'checkUser'
  }),
  methods: {
    ...mapActions(['goUrl']),
    getList: function() {
      let that = this
      dataApi.promotion(function(res) {
        that.data = res
        res.data.map(item => {
          if (item.info) {
            that.dataList.push(item)
            that.shareimg = item.info.thumb
          }
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
  onLoad() {
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
      imageUrl: this.shareimg,
      path: '/pages/index/main?pid=' + this.userInfo.id
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

.header {
  width: 750rpx;
  height: auto;
  position: relative;
}

.bg {
  width: 100%;
  height: 310rpx;
}

.headerContent {
  width: 100%;
  height: 140rpx;
  top: 35rpx;
  left: 0;
  position: absolute;
  justify-content: space-between;
  align-items: flex-end;
  padding-left: 44rpx;
  box-sizing: border-box;
}

.avator {
  width: 97rpx;
  height: 97rpx;
  border-radius: 100%;
  margin-right: 20rpx;
}

.nameBox {
  height: 90rpx;
  justify-content: space-between;
}

.text {
  width: 150rpx;
  font-size: 29rpx;
  color: #fff;
}

.people {
  width: 142rpx;
  height: 37rpx;
  background-color: #f6b37f;
  border-radius: 5rpx;
  font-size: 22rpx;
  color: #c7344e;
  text-align: center;
  line-height: 37rpx;
}

.goGao {
  width: 230rpx;
  height: 50rpx;
  background-color: #f36365;
  border-top-left-radius: 30rpx;
  border-bottom-left-radius: 30rpx;
  font-size: 25rpx;
  color: #fcdda7;
  text-align: center;
  line-height: 50rpx;
}

.numBox {
  width: 712rpx;
  height: 276rpx;
  background-color: #ffffff;
  box-shadow: 0px 3px 18px 0px rgba(137, 136, 136, 0.42);
  margin: auto;
  padding: 0 12rpx;
  box-sizing: border-box;
  margin-top: -51rpx;
  position: relative;
}

.numBox_t {
  width: 100%;
  height: 165rpx;
  border-bottom: 1px solid #dcdcdd;
  padding: 30rpx 34rpx 30rpx 40rpx;
  box-sizing: border-box;
  justify-content: space-between;
}

.numBox_title {
  font-size: 25rpx;
  color: #666666;
}

.money {
  justify-content: space-between;
}

.money_l {
  font-size: 43rpx;
  color: #fd6b32;
}

.money_l span {
  font-size: 29rpx;
}

.money_r {
  align-items: center;
}

.money_r_text {
  font-size: 25rpx;
  color: #4d6b85;
}

.money_r img {
  width: 12rpx;
  height: 24rpx;
  margin-left: 14rpx;
}

.numBox_b {
  width: 100%;
  height: 107rpx;
  align-items: center;
  justify-content: space-between;
  padding: 0 90rpx;
  box-sizing: border-box;
  font-size: 25rpx;
  color: #666666;
}

.numBox_b span {
  font-size: 42rpx;
  color: #4d6b85;
  margin-left: 35rpx;
}

.numBox_b_l,
.numBox_b_r {
  align-items: center;
}

.title1 {
  width: 100%;
  margin: 20rpx 24rpx;
  font-size: 29rpx;
  color: #333333;
}

.title1 span {
  font-size: 22rpx;
  color: #666666;
  margin-left: 26rpx;
}

.list {
  width: 100%;
  height: auto;
}

.item {
  width: 100%;
  height: 238rpx;
  padding: 20rpx 25rpx;
  box-sizing: border-box;
  border-bottom: 1px solid #dcdcdd;
  justify-content: space-between;
  align-items: center;
}

.item img {
  width: 157rpx;
  height: 199rpx;
  margin-right: 20rpx;
}

.title_all {}

.title2 {
  font-size: 28rpx;
  color: #333333;
}

.title3 {
  font-size: 25rpx;
  color: #7f7f7f;
  line-height: 2.5;
}

.title4 {
  font-size: 25rpx;
  color: #fd6b32;
}

.item_r {
  width: 123rpx;
  height: 53rpx;
  background-color: #dcdcdd;
  border-radius: 7rpx;
  text-align: center;
  line-height: 53rpx;
  font-size: 29rpx;
  color: #fd6b32;
  margin: 0;
  padding: 0;
}

.noData {
  width: 100%;
  height: 110rpx;
  background: #f2f2f2;
  text-align: center;
  line-height: 110rpx;
  font-size: 25rpx;
  color: #999999;
}
</style>
