<template>
<div class="tabBox">
  <div class="tabTag" >
     <div v-if="menuParams.page === 1" v-bind:class="{ 'router-link-active':catid==11 }">
       <a v-on:click="allList">全部</a>
     </div>
     <div v-if="menuParams.page > 1" v-on:click="PreMenu"><img src="/static/images/double-left-chevron.png"  mode="widthFix" /></div>
     <div v-for="(item,index) in dataList" :key="item.id" v-if="menuParams.start <= index && index < menuParams.end" v-bind:class="{ 'router-link-active':catid === item.catid}" v-on:click="changeMenu(item.catid)">{{item.catname}}</div>
     <div v-if="menuParams.count > 4 && menuParams.page <= menuParams.pages" v-on:click="NextMenu"><img src="/static/images/double-angle-pointing-to-right.png"  mode="widthFix" /></div>
  </div>
</div>
</template>
<script>
import {mapGetters, mapActions} from 'vuex'
export default{
  data () {
    return {
      catid: 11
    }
  },
  computed: {
    ...mapGetters({
      dataList: 'getMenu',
      menuParams: 'getMenuParams'
    })
  },
  created () {
    this.getMenu()
  },
  methods: {
    ...mapActions(['getMenu', 'NextMenu', 'PreMenu']),
    allList: function () {
      this.catid = 11
      this.$emit('changeCid', 0)
    },
    changeMenu: function (cid) {
      this.catid = cid
      this.$emit('changeCid', cid)
    }
  },
  mounted () {
  }
}
</script>
<style>
.tabBox{ width:100%; height:42px; background:url(http://zsh.huyueyue.com/images/bgbg.jpg) repeat-x left bottom;border-top:1px solid #e1e1e1;}
.tabTag ._div{width:20%; height:40px; line-height:40px; font-size:14px; text-align:center; float:left;}
.tabTag ._div ._a{ width:100%; height:100%; display:block; color:#333;}
.tabTag .router-link-active{ border-bottom:2px solid #e15603;}
.tabTag .router-link-active ._a{ border-bottom:2px solid #e15603;}
.tabTag ._img{width:25rpx;margin-top:28rpx}
</style>
