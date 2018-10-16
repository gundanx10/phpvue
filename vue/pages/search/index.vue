<template>
<div id="app" class="listbox">

  <div class="seachBox">
    <div class="seachCt"><input name="" class="input98" v-model="keyWord" type="text" placeholder="精选">
      <div class="circle" v-if="keyWord!=''" v-on:click="cancel"><img src="/static/images/cancel3.png" mode="widthFix"></div>
    </div>
    <div class="seachBt" v-on:click="subSearch">搜索</div>
  </div>

  <div class="search_q" v-if="!result || result.length<1">
    <div class="seachTag">
      <div class="tagTitle">分类搜索</div>
      <div class="tagText">
        <h1 v-for="item3 in CatList.cat" :key="item3.catid" v-on:click="catSearch(item3.catid, item3.catname)">{{item3.catname}} </h1>
      </div>
    </div>
    <!--标签-->
    <div class="seachTag" v-if="CatList.hotSearch && CatList.hotSearch.length>0">
      <div class="tagTitle">热门搜索</div>
      <div class="tagText">
        <h1 v-for="item in CatList.hotSearch" :key="item.id" v-on:click="stringSearch(item.search)">{{item.search}} </h1>
      </div>
    </div>
    <!--标签-->
    <div class="seachTag" v-if="CatList.history && CatList.history.length>0">
      <div class="tagTitle">历史搜索</div>
      <div class="tagText">
        <h1 v-for="item in CatList.history" :key="item.id" v-on:click="stringSearch(item.search)">{{item.search}} </h1>
      </div>
    </div>

  </div>

  <div class="search_result" v-else>
    <div v-for="item in result" v-on:click="goUrl(item.app_url)">
      <div class="search_div">
        <h4>{{item.title}}</h4>
        <span>{{item.description}}</span>
      </div>
      <div class="divhr"></div>
    </div>
  </div>

</div>
</template>

<script>
import {
  mapActions
} from 'vuex'
import filters from '@/common/filters'
import dataApi from '@/data/index'
export default {
  data() {
    return {
      keyWord: '',
      searchType: 'string',
      catid: 0,
      CatList: [],
      result: [],
      page: 0,
      end: false
    }
  },
  methods: {
    ...mapActions(['goUrl']),
    init: function() {
      this.keyWord = ''
      this.searchType = 'string'
      this.CatList = []
      this.result = []
      this.page = 0
      const _this = this
      dataApi.getSearchCat(function(res) {
        _this.CatList = res
      })
    },
    cancel: function() {
      this.result = []
      this.keyWord = ''
    },
    subSearch: function() {
      this.stringSearch(this.keyWord)
    },
    stringSearch: function(keyWord) {
      console.log(keyWord)
      this.keyWord = keyWord
      this.searchType = 'string'
      this.page = 0
      this.end = false
      this.getSearch()
    },
    catSearch: function(catid, catname) {
      this.keyWord = catname
      this.catid = catid
      this.searchType = 'cat'
      this.page = 0
      this.end = false
      this.getSearch()
    },
    getSearch: function() {
      this.page = this.page + 1
      console.log(this.page)
      let _this = this
      if (this.searchType === 'string') {
        let params = {
          q: this.keyWord,
          page: this.page
        }
        dataApi.getSearch(function(res) {
          if (res.data) {
            res.data.map(item => {
              item = Object.assign(item, {
                title: filters.foramtIntercept(item.content.title, 32),
                description: filters.foramtIntercept(item.content.description, 50)
              })
              _this.result = _this.result.concat(item)
            })
            if (res.last_page <= res.current_page) {
              _this.end = true
            }
          } else {
            _this.end = true
          }
        }, params)
      } else {
        let params = {
          q: this.catid,
          page: this.page
        }
        dataApi.getSearchCatDetail(function(res) {
          if (res.data.length) {
            res.data.map(item => {
              item = Object.assign(item, {
                title: filters.foramtIntercept(item.title, 32),
                description: filters.foramtIntercept(item.description, 50)
              })
              _this.result = _this.result.concat(item)
            })
            if (res.last_page <= res.current_page) {
              _this.end = true
            }
          } else {
            _this.end = true
          }
        }, params)
      }
    },
    back: function() {
      wx.navigateBack()
    }
  },
  onLoad: function() {
    this.init()
  },
  onReachBottom: function() {
    if (this.end) {
      wx.showToast({
        title: '没有更多内容了',
        icon: 'success',
        duration: 2000
      })
    } else {
      this.getSearch()
    }
  }
}
</script>
<style>
.seachBox {
  height: 50px;
  width: 95%;
  background: #fff;
  padding: 0 2.5% 1.8% 2.5%;
  font-size: 16px;
  position: relative;
  line-height: 50px;
  color: #7b3f38;
  font-weight: bold;
  border-bottom: 1px solid #e1e1e1;
  text-align: center;
}

.seachDope {
  width: 7%;
  height: 42px;
  float: left;
  padding-top: 7px;
  position: relative;
  text-align: left;
}

.seachDope ._img {
  width: 22px
}

.seachTips {
  position: absolute;
  width: 10px;
  height: 10px;
  border-radius: 10px;
  background: #ff3026;
  top: 13px;
  left: 22px;
}

.seachCt {
  width: 90%;
  height: 42px;
  float: left;
  position: relative;
}

.seachCt ._input {
  width: 98%;
  border: 1px solid #e1e1e1;
  height: 30px;
  line-height: 30px;
  font-size: 12px;
  text-align: center;
  border-radius: 5px;
}

.seachBt {
  width: 10%;
  height: 30px;
  line-height: 30px;
  float: left;
  position: relative;
  color: #ef5f07;
  height: 28px;
  background: #fff;
  font-size: 16px;
}

.seachTag {
  width: 94%;
  padding: 3% 3% 0% 3%;
  overflow: hidden;
}

.tagTitle {
  width: 100%;
  line-height: 14px;
  font-size: 14px;
  padding-bottom: 10px;
}

.tagText {
  width: 100%;
}

.tagText ._h1 {
  padding: 1.5% 2.5% 1.5% 2.5%;
  border: 1px solid #e1e1e1;
  float: left;
  font-size: 12px;
  border-radius: 3px;
  margin-right: 10px;
  margin-bottom: 10px;
}

.circle {
  box-sizing: border-box;
  border-radius: 50%;
  width: 30px;
  position: absolute;
  right: 17rpx;
  top: -10rpx;
  height: 80rpx;
  z-index: 100
}

.circle ._img {
  width: 18px
}

.search_div {
  padding: 10px;
  line-height: 25px;
  height: 55px;
}

.search_div ._span {
  font-size: 13px;
}

.divhr {
  border-bottom: 1rpx solid #989898;
  width: 100%
}
</style>
