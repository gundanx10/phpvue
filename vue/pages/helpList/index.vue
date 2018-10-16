<template>
<div id="app" class="listbox">

  <div class="helpTop"><img src="http://zsh.huyueyue.com/images/m10.jpg" mode="widthFix"></div>
  <div class="helpTitle">{{catname}}</div>
  <div class="UserText">
    <ul>

      <li v-for="item in helpList" :key="item.id" v-on:click="goUrl('../helpDetail/main?id='+item.id)">
        <h1>{{item.title}}</h1>
        <em><img src="http://zsh.huyueyue.com/images/right.png"  mode="widthFix"></em>
      </li>

    </ul>
  </div>
</div>
</template>

<script>
import dataApi from '@/data/index'
import {
  mapActions
} from 'vuex'
export default {
  data() {
    return {
      cid: 0,
      helpList: [],
      catname: '',
      end: false,
      page: 0
    }
  },
  beforeMount() {
    const {
      id
    } = this.$route.query
    this.cid = id
    if(!id) {
      this.$router.back()
    }
    this.moredata()
  },
  methods: {
    ...mapActions(['goUrl']),
    moredata: function () {
      let _this = this
      dataApi.getHelpList(function (res) {
        if(res.current_page === res.last_page) {
          _this.end = true
        }
        _this.helpList = _this.helpList.concat(res.data)
        res.data.map(item => {
          _this.catname = item.catName
        })
      }, 10, this.page, this.cid)
    }
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
  onLoad: function (options) {}
}
</script>
<style>
.helpTitle {
  width: 94%;
  line-height: 14px;
  background: #efeff0;
  font-size: 12px;
  padding: 3% 3% 0 3%;
  font-size: 14px;
  color: #666;
}

.helpTop {
  width: 100%;
  line-height: 0px;
}

.helpTop ._img {
  width: 100%;
  line-height: 0px;
}

.titleCt {
  text-align: center;
}

.UserText {
  width: 100%;
  padding: 0px;
  overflow: hidden;
  background: #fff;
  overflow: hidden;
  border-top: 10px solid #efeff0;
}

.UserText ._ul {
  margin: 0px;
  padding: 0px;
}

.UserText ._ul ._li {
  width: 96%;
  display: block;
  list-style-type: none;
  padding: 15px 2% 15px 2%;
  border-bottom: 1px solid #e3e3e3;
  background-size: 22px;
  overflow: hidden;
}

.UserText ._ul ._li ._img {
  width: 22px
}

.UserText ._ul ._li ._span {
  width: 22px;
  height: 22px;
  display: block;
  float: left;
  font-size: 20px;
}

.UserText ._ul ._li ._h1 {
  float: left;
  line-height: 22px;
  font-size: 14px;
  padding-left: 8px;
  color: #777;
  font-weight: normal;
  float: left
}

.UserText ._ul ._li ._h1 ._a {
  color: #777;
}

.UserText ._ul ._li ._h1 ._em {
  font-style: normal;
  font-weight: normal;
  padding-left: 10px;
  font-size: 16px;
  color: #999999;
}

.UserText ._ul ._li ._em {
  width: 22px;
  height: 22px;
  display: block;
  float: right;
  font-size: 16px;
}

.UserText ._ul ._li ._b {
  width: 10px;
  height: 10px;
  background: #ff0000;
  -moz-border-radius: 10px;
  -webkit-border-radius: 10px;
  border-radius: 10px;
  float: left;
  margin: 6px 0 0 5px;
}

.UserText ._ul ._li ._h1 ._strong {
  float: right;
  font-weight: normal;
}
</style>
