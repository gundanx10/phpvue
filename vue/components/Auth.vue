<template>
<div>
  <div class="drawer_screen" v-if="showModalStatus"></div>
  <div class="drawer_box" v-if="showModalStatus">
    <div class="drawer_close" v-on:click="closeDrawer"><img src="/static/images/cancel.png" mode="widthFix" /></div>
    <div class="drawer_title">允许本应用获取用户昵称头像</div>
    <button class="authBtShow" open-type="getUserInfo" lang="zh_CN" @getuserinfo="onGetUserInfo">确认</button>
  </div>
</div>
</template>
<script>
export default {
  data() {
    return {
      showModalStatus: true
    }
  },
  methods: {
    closeDrawer: function () {
      this.showModalStatus = false
    },
    onGetUserInfo: function (e) {
      console.log(e)
      if(e.mp.detail.errMsg === 'getUserInfo:ok') {
        this.$store.dispatch('syncUserInfo', e.mp.detail.userInfo)
      }
      this.closeDrawer()
    }
  },
  mounted() {}
}
</script>
<style>
.drawer_screen {
  width: 100%;
  height: 100%;
  position: fixed;
  top: 0;
  left: 0;
  z-index: 1000;
  background: #000;
  opacity: 0.5;
  overflow: hidden;
}

.drawer_close {
  width: 20px;
  height: 20px;
  position: absolute;
  right: 8px;
  top: 8px
}

.drawer_close ._img {
  width: 20px;
}

/*content*/

.drawer_box {
  width: 650rpx;
  overflow: hidden;
  position: fixed;
  top: 50%;
  left: 0;
  z-index: 1001;
  background: #FAFAFA;
  margin: -180rpx 50rpx 0 50rpx;
  border-radius: 3px;
}

.drawer_title {
  padding: 10px;
  font: 15px "microsoft yahei";
  text-align: center;
}

.drawer_content {
  overflow-y: scroll;
  /*超出父盒子高度可滚动*/
}

.btn_ok {
  padding: 10px;
  font: 20px "microsoft yahei";
  text-align: center;
  border-top: 1px solid #E8E8EA;
  color: #3CC51F;
}

.top {
  padding-top: 8px;
}

.bottom {
  padding-bottom: 8px;
}

.title {
  height: 30px;
  line-height: 30px;
  width: 160rpx;
  text-align: center;
  display: inline-block;
  font: 300 28rpx/30px "microsoft yahei";
}

.input_base {
  font: 15px "microsoft yahei";
  border: 2rpx solid #ccc;
  padding-left: 10rpx;
  margin: 0rpx 30rpx 30rpx 30rpx;
}

.input_h30 {
  height: 130px;
  line-height: 20px;
}

.input_h60 {
  height: 60px;
}

.input_view {
  font: 12px "microsoft yahei";
  background: #fff;
  color: #000;
  line-height: 30px;
}

input {
  font: 12px "microsoft yahei";
  background: #fff;
  color: #000;
}

radio {
  margin-right: 20px;
}

.grid {
  display: -webkit-box;
  display: box;
}

.col-0 {
  -webkit-box-flex: 0;
  box-flex: 0;
}

.col-1 {
  -webkit-box-flex: 1;
  box-flex: 1;
}

.fl {
  float: left;
}

.fr {
  float: right;
}

.commentBt {
  width: 96%;
  text-align: right;
  padding: 8px 2% 10px 2%;
}

.commentBtShow {
  width: 100%;
  height: 40px;
  line-height: 40px;
  font-size: 14px;
  background: #7b3f38;
  border: 1px solid #7b3f38;
  color: #fff;
  text-align: center;
  -moz-border-radius: 5px;
  -webkit-border-radius: 5px;
  border-radius: 5px;
}

.authBtShow {
  width: 50%;
  height: 40px;
  margin-top: 25px;
  margin-bottom: 25px;
  line-height: 40px;
  background: #f46412;
  color: #fff;
  font-size: 14px;
  border: 1px solid #39B009;
  text-align: center;
  -moz-border-radius: 5px;
  -webkit-border-radius: 5px;
  border-radius: 5px;
}
</style>
