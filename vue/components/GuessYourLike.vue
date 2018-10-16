<template>
<div>
  <div class="recenttTitle2"> <span></span>
    <h1>猜你喜欢</h1>
    <b v-on:click="changeGuessList()">换一换</b>
  </div>


  <div class="guessBox">
    <div class="guessShow" v-for="item in data" :key="item.id" v-on:click="goUrl('../knowledgeDetail/main?id='+item.id)">
      <h1>{{item.views}}</h1>
      <span><img :src="item.thumb" mode="widthFix"></span>
      <h2>{{item.title}}</h2>
    </div>
  </div>

</div>
</template>
<script>
import {
  mapGetters,
  mapActions
} from 'vuex'
// import dataApi from '@/data/index'
// import filters from '@/common/filters'
export default {
  computed: mapGetters({
    data: 'getGuess'
  }),
  props: ['id', 'cid'],
  mounted() {
    this.$store.dispatch('getGuessList', {
      id: this.id,
      cid: this.cid
    })
  },
  methods: mapActions(['changeGuessList', 'goUrl'])
}
</script>

<style>
.recenttTitle2 {
  width: 100%;
  padding: 0;
  overflow: hidden;
  background: #fff;
}

.recenttTitle2 ._span {
  float: left;
  display: block;
  line-height: 16px;
  background: #ff5e4c;
  padding: 2% 0 2% 3%;
  height: 16px;
  margin-top: 4.5%;
}

.recenttTitle2 ._h1 {
  width: 70%;
  float: left;
  color: #555;
  display: block;
  line-height: 16px;
  font-size: 16px;
  padding: 3% 0 0 3%;
  margin-top: 3.5%;
}

.recenttTitle2 ._b {
  font-weight: normal;
  float: right;
  padding-right: 3%;
  font-size: 14px;
  display: block;
  background: url(http://zsh.huyueyue.com/images/hyh.png) no-repeat left 3px;
  background-size: 14px;
  padding-left: 5.5%;
  font-size: 14px;
  margin-top: 5.5%;
}

.guessBox {
  width: 94%;
  background: #fff;
  padding: 3%;
  overflow: hidden;
}

.guessShow {
  width: 33.333%;
  float: left;
}

.guessShow ._h1 {
  width: 80%;
  display: block;
  background: url(http://zsh.huyueyue.com/images/huo.png) no-repeat left 0;
  background-size: 12px;
  padding-left: 13%;
  font-size: 14px;
  font-weight: normal;
  line-height: 20px;
  color: #ff5e4c;
}

.guessShow ._span {
  width: 85%;
  line-height: 0px;
  float: left;
  display: block;
  padding: 2.5% 0;
}

.guessShow ._img {
  width: 100%;
  line-height: 0px;
}

.guessShow ._h2 {
  width: 85%;
  font-size: 14px;
  font-weight: normal;
  line-height: 20px;
  color: #333;
  display: block;
  color: #7f7f7f;
}

.guessShow ._h2 a {
  color: #7f7f7f;
}
</style>
