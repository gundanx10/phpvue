<template>
<div id="app" class="listbox">

  <div class="search">
    <input type="text" placeholder="搜索" confirm-type="search" @focus="goUrl('/pages/search/main')" />
    <icon type="search" size="18"></icon>
  </div>

  <div class="qaBox" v-for="item in dataList" :key="item.id" v-on:click="goUrl('../interactDetail/main?id='+item.id)">
    <h1>{{item.title}}</h1>
    <h2>{{item.result}}</h2>
  </div>
  <div class="qaFoot" title="了解详情"><img v-on:click="questions" src="http://zsh.huyueyue.com/images/m17.jpg" mode="widthFix"></div>
  <popup v-bind:showModalStatus="showModalStatus" v-bind:popType="popType" v-bind:popTitle="popTitle" @postNewQuestion="postNewQuestion($event)" @changeStatus="changeStatus($event)"></popup>
</div>
</div>
</template>

<script>
import {
  mapGetters,
  mapActions
} from 'vuex'
import popup from '@/components/PopUp'
export default {
  data() {
    return {
      showModalStatus: false,
      popTitle: '提交问题',
      popType: 'question'
    }
  },
  computed: mapGetters({
    dataList: 'getInteractList',
    end: 'getInteractEnd'
  }),
  components: {
    popup
  },
  methods: {
    ...mapActions(['goUrl', 'postNewQuestion']),
    moredata: function() {
      this.$store.dispatch('getInteractList')
    },
    questions: function() {
      this.showModalStatus = true
    },
    changeStatus: function(status) {
      this.showModalStatus = status
    }
  },
  onLoad: function(options) {
    this.showModalStatus = false
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
      title: '装修知识汇商城',
      desc: '你想要的都在这里',
      path: '/pages/interact/main'
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

.qaBox {
  width: 94%;
  padding: 3% 3% 3% 3%;
  overflow: hidden;
  border-bottom: 1px solid #e1e1e1;
}

.qaBox ._h1 {
  width: 93%;
  background: url(http://zsh.huyueyue.com/images/q.jpg) no-repeat left 0px;
  background-size: 20px;
  display: block;
  font-size: 14px;
  line-height: 20px;
  padding-left: 7%;
}

.qaBox ._h2 {
  width: 93%;
  background: url(http://zsh.huyueyue.com/images/a.jpg) no-repeat left 0px;
  background-size: 20px;
  display: block;
  font-size: 14px;
  line-height: 20px;
  padding-left: 7%;
  font-weight: normal;
  margin-top: 10px;
}

.qaFoot {
  width: 100%;
  position: fixed;
  z-index: 100;
  bottom: 0px;
  left: 0;
  background: #efeff0;
  line-height: 0px;
  background: #fff;
  text-align: center;
  padding: 2.5% 0 2.5% 0;
}

.qaFoot ._img {
  width: 40%;
  background: #fff;
}

.sortCenter ._span {
  background: none
}

.sortCenter ._span ._b {
  background: #fff;
  border: 1px solid #e1e1e1;
  box-shadow: none;
  padding: 4px 14px;
  height: auto;
  position: relative;
  display: inline-block;
  padding: 0 .6em;
  overflow: hidden;
  height: 1.9em;
  line-height: 1.9em;
  white-space: nowrap;
  cursor: pointer;
}

.sortCenter ._span .checked {
  background: #e6e6e6
}

.commentCenter {
  line-height: 22px;
  color: #555;
  font-size: 12px;
  padding: 1% 0 0 5%;
  width: 95%;
}

.commentSmBox {
  width: 96%;
  padding: 15px 2% 0 2%;
}

.commentIp {
  width: 96%;
  padding: 15px 2% 0 2%;
}

.commentIp .textarea {
  width: 96%;
  padding: 0 2% 0 2%;
  height: 150px;
  border: 1px solid #e9e9e9;
  background: #fff;
  border-radius: 5px 5px 5px 5px;
  color: #666;
  font-size: 14px;
}

.commentBt {
  width: 96%;
  text-align: right;
  padding: 8px 2% 10px 2%;
}

.commentBtShow {
  width: 100%;
  height: 40px;
  font-size: 14px;
  background: #7b3f38;
  border: 1px solid #7b3f38;
  color: #fff;
  text-align: center;
  -moz-border-radius: 5px;
  -webkit-border-radius: 5px;
  border-radius: 5px;
}
</style>
