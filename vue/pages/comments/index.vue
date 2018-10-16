<template>
<div id="app" class="listbox">
  <div class="resourcesTitle2">{{data.title}}</div>

  <div class="commentTitle" v-if="hot_flag">热评</div>

  <div class="commentBox" v-for="item in hot_comment" :key="item.id">
    <div class="commentNot">
      <div class="commentPhoto"><img :src="item.head"></div>
      <div class="commentName">{{item.nickname}}<br><b>{{item.created_time}}</b></div>
      <div class="commentZan heart" v-bind:class="{ NavActive3: item.like_status }" v-on:click="like(item.id)">
        <div class="likeCount" id="likeCount1">{{item.like_count}}</div>
      </div>
      <div class="commentZan2"></div>
    </div>
    <div class="commentCenter">
      <div class="commentCenter">{{item.comment}}</div>
    </div>
  </div>


  <div class="commentTitle" v-if="new_flag">最新</div>
  <div class="commentBox" v-for="item in comment" :key="item.id">
    <div class="commentNot">
      <div class="commentPhoto"><img :src="item.head"></div>
      <div class="commentName">{{item.nickname}}<br><b>{{item.created_time}}</b></div>
      <div class="commentZan heart " v-bind:class="{ NavActive3: item.like_status }" v-on:click="like(item.id)">
        <div class="likeCount" id="likeCount1">{{item.like_count}}</div>
      </div>
      <div class="commentZan2"></div>
    </div>
    <div class="commentCenter">
      <div class="commentCenter">{{item.comment}}</div>
    </div>
  </div>

  <popup v-bind:showModalStatus="showModalStatus" v-bind:popType="popType" v-bind:popTitle="popTitle" v-bind:itemId="data.id" v-bind:itemType="type" @postComment="postComment($event)" @changeStatus="changeStatus($event)"></popup>
  <div class="MHr"></div>
  <!--误删，65px的高度-->
  <!--FOOTer-->
  <div class="pageFoot">
    <div class="pageReturn"><a v-on:click="back"><img src="http://zsh.huyueyue.com/images/fh.png" width="30px" mode="widthFix" /></a></div>
    <div class="pencilRight" v-on:click="commentBtn">
      <div class="pencilHight"><img src="/static/images/square.png" mode="widthFix" /></div>
      <div class="comment">评论</div>
    </div>
  </div>
</div>
</template>
<script>
import popup from '@/components/PopUp'
import dataApi from '@/data/index'
import wxParse from 'mpvue-wxparse'
export default {
  data() {
    return {
      id: 0,
      type: 'news',
      showModalStatus: false,
      popTitle: '提交评论',
      popType: 'comment',
      data: [],
      comment: [],
      hot_comment: [],
      hot_flag: false,
      new_flag: false,
      page: 0,
      end: false
    }
  },
  created() {

  },
  methods: {
    init: function () {
      this.end = false
      this.hot_flag = false
      this.new_flag = false
      this.hot_comment = []
      this.comment = []
      this.data = []
      this.page = 0
    },
    moredata: function () {
      let _this = this
      this.page = this.page + 1
      dataApi.getCommentList(function (res) {
        if(_this.page === 1) {
          if(res.hot.length > 0) {
            _this.hot_flag = true
            _this.hot_comment = res.hot
          }
          _this.data = res.content
        }

        if(res.comment.data.length) {
          _this.new_flag = true
          _this.comment = _this.comment.concat(res.comment.data)
          if(res.comment.last_page <= res.comment.current_page) {
            _this.end = true
          }
        } else {
          _this.end = true
        }
      }, this.id, this.type, this.page)
    },
    commentBtn: function () {
      this.showModalStatus = true
    },
    changeStatus: function (status) {
      this.showModalStatus = status
    },
    back: function () {
      wx.navigateBack()
    },
    postComment: function (obj) {
      let _this = this
      if(obj.comment.length > 0) {
        dataApi.postComment(function (res) {
          _this.comment.unshift(res)
          _this.newComment = ''
        }, this.id, this.type, obj.comment)
      }
    },
    like: function (id) {
      let _this = this
      let _id = id
      dataApi.postLike(function (res) {
        let record = _this.comment.find(p => p.id === _id)
        record.like_status = res.status
        record.like_count = res.count
      }, id, 'comment')
    }
  },
  components: {
    wxParse,
    popup
  },
  onReachBottom: function () {
    if(this.end) {
      wx.showToast({
        title: '没有更多内容了',
        icon: 'success',
        duration: 2000
      })
    } else {
      this.moredata()
    }
  },
  onLoad: function () {
    const {
      id,
      type
    } = this.$route.query
    console.log(id, type)
    this.id = id
    this.type = type
    if(!id) {
      this.$router.back()
    }
    this.showModalStatus = false
    this.init()
    this.moredata()
  }
}
</script>

<style>
.resourcesTitle2 {
  width: 95%;
  padding: 3% 2.5% 3% 2.5%;
  line-height: 24px;
  font-size: 14px;
  font-weight: bold;
  background: #fff;
  border-bottom: 1px solid #e3e3e3;
  color: #333;
}

.commentTitle {
  width: 95%;
  padding: 2.5% 2.5% 2.5% 2.5%;
  background: #f7f7f7;
  line-height: 12px;
  font-size: 12px;
  color: #000;
  font-weight: bold;
}

.commentBox {
  width: 95%;
  padding: 3% 2.5% 3% 2.5%;
  border-bottom: 1px solid #DDDEDF;
}

.commentNot {
  width: 100%;
  height: 32px;
  width: 100%;
}

.commentPhoto {
  width: 32px;
  height: 32px;
  float: left;
}

.commentPhoto img {
  width: 32px;
  height: 32px;
  border-radius: 32px;
}

.commentName {
  float: left;
  font-size: 12px;
  color: #4a4a4a;
  font-weight: bold;
  line-height: 16px;
  padding-left: 6px;
}

.commentName b {
  font-size: 0.75em;
  color: #b2b2b2;
  font-weight: normal;
}

.commentZan {
  float: right;
  height: 32px;
  background: url(http://zsh.huyueyue.com/images/off_zan.png) no-repeat right 4px;
  background-size: 20px;
  padding-right: 22px;
  line-height: 32px;
  font-size: 0.75em;
  color: #673007;
}

.NavActive3 {
  background: url(http://zsh.huyueyue.com/images/on_zan.png) no-repeat right center;
  background-size: 20px;
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

.MHr {
  width: 100%;
  height: 20px;
}

.pageFoot {
  width: 100%;
  height: 55px;
  border-top: 1px solid #b2b2b2;
  position: fixed;
  bottom: 0;
  left: 0;
  background: #fff;
  z-index: 99;
}

.pageReturn {
  float: left;
  height: 42px;
  width: 40px;
  position: absolute;
  z-index: 9999;
  left: 5px;
  bottom: 0px
}

.pageReturn ._img {
  width: 30px;
}

.pencilRight {
  float: right;
  height: 55px;
  line-height: 55px;
  width: 100px;
  z-index: 9999;
  color: #462917;
}

.pencilHight {
  float: left;
  line-height: 122rpx;
  margin-right: 7px;
}

.pencilHight ._img {
  width: 20px
}

.comment {
  float: left;
  line-height: 55px;
  color: #462917;
  font-size: 33rpx
}
</style>
