<template>
<div>

  <div class="recenttTitle"> <span></span>
    <h1>近期内容</h1>
  </div>

  <div class="recenttShow" v-for="item in data" :key="item.id" v-on:click="goUrl('../knowledgeDetail/main?id='+item.id)">
    <div class="recenttShowLf"><img :src="item.thumb" mode="widthFix"></div>
    <div class="recenttShowRt">
      <h1><b>类别</b>{{item.typeName}}</h1>
      <h2>{{item.title}}</h2>
      <h3>{{item.description}}</h3>
      <h4>{{item.inputtime}} 上新</h4>
    </div>
  </div>

</div>
</template>
<script>
import {
  mapActions
} from 'vuex'
import dataApi from '@/data/index'
import filters from '@/common/filters'
export default {
  data() {
    return {
      data: []
    }
  },
  methods: {
    ...mapActions(['goUrl']),
    moredata: function() {
      const _this = this
      dataApi.getRecentList(function(res) {
        const items = []
        res.map(item => {
          item = Object.assign(item, {
            thumb: filters.foramtImage(item.thumb),
            description: filters.foramtIntercept(item.description),
            inputtime: filters.foramtDate(item.inputtime)
          })
          items.push(item)
        })
        _this.data = items
      })
    }
  },
  mounted() {
    this.moredata()
  }
}
</script>
<style>
.recenttTitle {
  width: 100%;
  padding: 0;
  overflow: hidden;
  background: #fff;
  border-top: 15px solid #efeff0;
}

.recenttTitle span {
  float: left;
  display: block;
  line-height: 16px;
  background: #ff5e4c;
  padding: 2% 0 2% 3%;
  height: 16px;
  margin-top: 4.5%;
}

.recenttTitle h1 {
  width: 94%;
  float: right;
  color: #555;
  display: block;
  line-height: 16px;
  font-size: 16px;
  padding: 3% 0 0 3%;
  margin-top: 3.5%;
}

.recenttTitle h1 b {
  font-weight: normal;
  float: right;
  padding-right: 2.5%;
  font-size: 14px;
}

.recenttTitle h1 b a {
  color: #999;
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

.recenttShowLf img {
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
  text-align: right;
}
</style>
