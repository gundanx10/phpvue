<template>
<div id="app" class="listbox">

  <div class="search">
    <input type="text" placeholder="搜索" confirm-type="search" @focus="goUrl('/pages/search/main')" />
    <icon type="search" size="18"></icon>
  </div>

  <my-menu @changeCid="changeCid($event)"></my-menu>

  <!-- 排序-->

  <div>
    <div v-for="item in dataList" :key="item.id" v-on:click="goUrl('../knowledgeDetail/main?id='+item.id)">
      <div class="titleCenter">
        <h1>{{item.keywords}}</h1>
      </div>
      <div class="recenttShow">
        <div class="recenttShowLf"><img :src="item.thumb" mode="widthFix"></div>
        <div class="recenttShowRt">
          <h1><b>类别</b>{{item.typeName}}</h1>
          <h2>{{item.title}}</h2>
          <h3>{{item.description}}</h3>
          <h4>{{item.inputtime}}  上新</h4>
        </div>
      </div>
    </div>

  </div>
  <div class="MHr"></div>
</div>
</template>

<script>
import {
  mapActions,
  mapGetters
} from 'vuex'

import Menu from '@/components/CourseMenu'
import dataApi from '@/data/index'
import filters from '@/common/filters'
export default {
  data() {
    return {
      dataList: [],
      end: false,
      page: 0,
      cid: 0
    }
  },
  components: {
    'my-menu': Menu
  },
  computed: mapGetters({
    userInfo: 'getUserInfo'
  }),
  methods: {
    ...mapActions(['goUrl']),
    back: function() {
      wx.navigateBack()
    },
    changeCid: function(cid) {
      this.cid = cid
      this.dataList = []
      this.page = 0
      this.end = false
      this.moredata()
    },
    moredata: function() {
      const _this = this
      this.page = this.page + 1
      dataApi.getKnowledgeList(function(res) {
        if (res.current_page === res.last_page) {
          _this.end = true
        }
        res.data.map(item => {
          item = Object.assign(item, {
            thumb: filters.foramtImage(item.thumb),
            inputtime: filters.foramtDate(item.inputtime),
            description: filters.foramtIntercept(item.description)
          })
          _this.dataList.push(item)
        })
      }, this.page, this.cid)
    }
  },
  onLoad: function(options) {
    this.moredata()
  },
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
      title: '装修知识汇课程',
      desc: '装修知识大放送',
      path: '/pages/knowledge/main?pid=' + this.userInfo.id
    }
  }
}
</script>
<style>
.search {
  width: 100%;
  height: 96rpx;
  top: 140rpx;
  background: #f2f2f2;
  padding: 18rpx 0;
  box-sizing: border-box;
}

.search input {
  width: 706rpx;
  height: 60rpx;
  background: #f2f2f2;
  border-radius: 6rpx;
  margin: auto;
  font-size: 26rpx;
  color: #999;
  box-sizing: border-box;
  background: #fff;
  text-align: center;
}

.search icon {
  position: absolute;
  top: 31rpx;
  margin-left: 40%;
  z-index: 2;
}

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

.topTips {
  position: absolute;
  width: 10px;
  height: 10px;
  border-radius: 10px;
  background: #ff3026;
  top: 13px;
  left: 22px;
}

.topLogo ._img {
  width: 22px
}

.topLogo {
  width: 100%;
  height: 44px;
  float: left;
  position: relative;
  text-align: center;
  color: #555;
}

.MHr {
  width: 100%;
  height: 20px;
}

.tabBox {
  width: 100%;
  height: 42px;
  background: url(http://zsh.huyueyue.com/images/bgbg.jpg) repeat-x left bottom;
  border-top: 1px solid #e1e1e1;
}

.tabTag ._div {
  width: 20%;
  height: 40px;
  line-height: 40px;
  font-size: 14px;
  text-align: center;
  float: left;
}

.tabTag ._div ._a {
  width: 100%;
  height: 100%;
  display: block;
  color: #333;
}

.tabTag .router-link-active {
  border-bottom: 2px solid #e15603;
}

.tabTag .router-link-active a {
  border-bottom: 2px solid #e15603;
}

.titleCenter {
  width: 94%;
  background: #fff;
  padding: 3.5% 3% 0 3%;
  overflow: hidden;
}

.titleCenter ._h1 {
  width: 40%;
  float: left;
  font-weight: normal;
  font-size: 14px;
  line-height: 20px;
  display: block;
  background: url(http://zsh.huyueyue.com/images/tx.png) no-repeat left 0px;
  background-size: 20px;
  padding-left: 7%;
}


.recenttShow {
  width: 94%;
  background: #fff;
  padding: 3%;
  border-bottom: 1px solid #d0d0d0;
  overflow: hidden;
}

.recenttShowLf {
  width: 30%;
  line-height: 0px;
  float: left;
}

.recenttShowLf ._img {
  width: 100%;
  line-height: 0px;
}

.recenttShowRt {
  width: 65%;
  float: right;
}

.recenttShowRt ._h1 {
  width: 100%;
  font-size: 15px;
  font-weight: normal;
  line-height: 20px;
  color: #8f8d8d;
}

.recenttShowRt ._h1 ._b {
  background: #ff7060;
  display: block;
  line-height: 20px;
  padding: 0 2.5%;
  float: left;
  font-size: 12px;
  color: #fff;
  border-radius: 4px;
  font-weight: normal;
  margin-right: 3%;
}

.recenttShowRt ._h2 {
  width: 100%;
  font-size: 14px;
  font-weight: normal;
  line-height: 20px;
  color: #333;
  padding-top: 3%;
}

.recenttShowRt ._h3 {
  width: 100%;
  font-size: 14px;
  font-weight: normal;
  line-height: 20px;
  color: #7f7f7f;
  padding-top: 2.5%;
}

.recenttShowRt ._h4 {
  width: 100%;
  font-size: 12px;
  font-weight: normal;
  line-height: 20px;
  color: #888;
  padding-top: 2.5%;
  text-align: left;
}
</style>
