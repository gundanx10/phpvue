<template>
<div id="app">

  <!--个人中心-->
  <div class="userBox"><img src="/static/images/user.jpg"></div>
  <div class="userTips">
    <div class="userPic"><img v-if="userInfo.head" :src="userInfo.head" mode="widthFix"></div>
    <div class="userName">
      <div class="userTitle">{{userInfo.nickname}}</div>
      <div class="userRt"><a href="../favorite/main">收藏</a></div>
    </div>
  </div>
  <div class="UserText">
    <ul>
      <li v-on:click="goUrl('../orderList/main')">
        <h1>我的订单</h1>
        <img class="imgicon" src="/static/images/right.png" mode="widthFix"> </li>
      <li v-on:click="goUrl('../extend/main')">
        <h1>推广中心</h1>
        <img class="imgicon" src="/static/images/right.png" mode="widthFix"> </li>
      <li v-on:click="goUrl('../invite/main')">
        <h1><a>邀请好友</a></h1>
        <img class="imgicon" src="/static/images/right.png" mode="widthFix"> </li>
      <li v-on:click="goUrl('../help/main')">
        <h1>帮助中心</h1>
        <img class="imgicon" src="/static/images/right.png" mode="widthFix"> </li>

    </ul>
  </div>
  <auth v-if="checkUser"></auth>
</div>
</template>

<script>
import auth from '@/components/Auth'
import {
  mapGetters,
  mapActions
} from 'vuex'
export default {
  computed: mapGetters({
    userInfo: 'getUserInfo',
    checkUser: 'checkUser'
  }),
  components: {
    auth
  },
  methods: {
    ...mapActions(['goUrl'])
  },
  created() {

  }
}
</script>

<style>
.userBox {
  width: 100%;
  position: relative;
  line-height: 0px;
}

.userBox ._img {
  width: 100%;
  line-height: 0px;
}

.userTips {
  width: 95%;
  padding: 0 2.5% 0 2.5%;
  height: 60px;
  background: #fff;
  position: relative;
  border-bottom: 1px solid #e5e5e5;
}

.userPic {
  width: 60px;
  height: 60px;
  border-radius: 60px;
  position: absolute;
  bottom: 40px;
  border: 2px solid #fff;
}

.userPic ._img {
  width: 60px;
  height: 60px;
  border-radius: 60px;
}

.userName {
  line-height: 60px;
  width: 100%;
}

.userTitle {
  padding-right: 8px;
  font-size: 14px;
  font-weight: bold;
  float: left;
  color: #7b3f38;
}

.userIcon {
  float: left;
  line-height: 0px;
  padding-top: 22px;
}

.userRt {
  float: right;
  font-size: 14px;
}

.userRt ._a {
  color: #7b3f38;
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
  background: url(http://zsh.huyueyue.com/images/right.png) no-repeat -20px 8px;
  background-size: 22px;
  overflow: hidden;
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

.UserText ._ul ._li ._h1 a {
  color: #777;
}

.UserText ._ul ._li ._h1 em {
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

.UserText ._ul ._li ._h1 strong {
  float: right;
  font-weight: normal;
}

.center50 {
  text-align: center;
}

.center90 {
  width: 90%;
}

.imgicon {
  width: 22px;
  height: 22px;
  display: block;
  float: right;
  font-size: 16px;
}
</style>
