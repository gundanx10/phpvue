<template>
<div id="app" class="listbox">
  <div class="search">
    <input type="text" placeholder="搜索" confirm-type="search" @focus="goUrl('/pages/search/main')" />
    <icon type="search" size="18"></icon>
  </div>
  <div class="listBox ">

    <div>
      <ul>
        <li v-for="item in knowledgeList" :key="item.id" v-on:click="goUrl('../newsDetail/main?id='+item.id)">
          <div class="avatar"><img :src="item.thumb" mode="widthFix"></div>
          <div class="avatarTitle">{{item.title}}</div>
          <div class="avatarText">{{item.description}}</div>
          <div class="readbox">
            <div class="zanIcon">{{item.like}}</div>
            <div class="commentIcon">{{item.comment}}</div>
          </div>
        </li>
      </ul>
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
import dataApi from '@/data/index'
import filters from '@/common/filters'
export default {
  data() {
    return {
      end: false,
      page: 0,
      knowledgeList: []
    }
  },
  computed: mapGetters({
    userInfo: 'getUserInfo'
  }),
  methods: {
    ...mapActions(['goUrl']),
    moredata: function() {
      const _this = this
      this.page = this.page + 1
      dataApi.getNewList(function(res) {
        if (res.current_page === res.last_page) {
          _this.end = true
        }
        res.data.map(item => {
          item = Object.assign(item, {
            thumb: filters.foramtImage(item.thumb, 800),
            description: filters.foramtIntercept(item.description, 200)
          })
          _this.knowledgeList.push(item)
        })
      }, this.page)
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
      title: '装修知识汇知识',
      desc: '装修知识大放送',
      path: '/pages/news/main?pid=' + this.userInfo.id
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

.topText {
  width: 15%;
  height: 44x;
  float: right;
  position: relative;
  text-align: right;
  font-size: 12px;
  font-weight: normal;
  color: #666;
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


.listBox {
  width: 94%;
  padding: 0 3% 0 3%;
  overflow: hidden;
}

.listBox ._ul {
  margin: 0px;
  padding: 0px;
}

.listBox ._ul ._li {
  width: 100%;
  display: block;
  list-style-type: none;
  padding: 2.5% 0 2.5% 0;
  overflow: hidden;
}

.avatar {
  width: 100%;
}

.avatar ._img {
  width: 100%;
}

.avatarTitle {
  width: 100%;
  line-height: 24px;
  font-size: 14px;
  color: #481d02;
  font-weight: bold;
}

.avatarTitle ._a {
  color: #67302b;
}

.avatarText {
  width: 100%;
  line-height: 22px;
  font-size: 12px;
  color: #94887f;
}

.avatarText ._a {
  color: 94887f;
}

.readbox {
  width: 100%;
  height: 14px;
  padding-top: 8px;
}

.readIcon {
  float: left;
  height: 14px;
  line-height: 14px;
  background: url(http://zsh.huyueyue.com/images/yuedu.svg) no-repeat left center;
  background-size: 14px;
  font-size: 0.75em;
  color: #000;
  padding-left: 18px;
}

.commentIcon {
  float: right;
  height: 14px;
  line-height: 14px;
  background: url(http://zsh.huyueyue.com/images/pinglun.svg) no-repeat left center;
  background-size: 14px;
  font-size: 0.75em;
  color: #666;
  padding-left: 18px;
  margin-right: 2.5%;
}

.zanIcon {
  float: right;
  height: 14px;
  line-height: 14px;
  background: url(http://zsh.huyueyue.com/images/zan.svg) no-repeat left center;
  background-size: 14px;
  font-size: 0.75em;
  color: #666;
  padding-left: 18px;
}

.MHr {
  width: 100%;
  height: 20px;
}
</style>
