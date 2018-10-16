<template>
<div>
  <div class="drawer_screen" v-if="showModalStatus"></div>
  <div class="drawer_box" v-if="showModalStatus">
    <div class="drawer_close" v-on:click="closeDrawer"><img src="/static/images/cancel.png" mode="widthFix" /></div>
    <div class="drawer_title">{{popTitle}}</div>
    <div class="drawer_content">
      <div class="top grid">
        <textarea class="input_base input_h30 col-1" maxlength="300" name="content" v-model="content" cols="" rows="" focus="true"></textarea>
      </div>

    </div>
    <div class="commentBt"><input v-on:click="Submit()" class="commentBtShow" type="button" value="提交" /></div>
  </div>
</div>
</template>
<script>
export default {
  data() {
    return {
      content: '',
      error: ''
    }
  },
  props: ['showModalStatus', 'popTitle', 'popType', 'itemType', 'itemId'],
  computed: {},
  created() {},
  methods: {
    closeDrawer: function () {
      this.showModalStatus = false
      this.$emit('changeStatus', this.showModalStatus)
    },
    Submit: function () {
      if(this.content && this.content.length > 2) {
        this.showModalStatus = false
        this.$emit('changeStatus', this.showModalStatus)
        if(this.popType === 'comment') {
          this.$emit('postComment', {
            comment: this.content
          })
        }
        if(this.popType === 'question') {
          this.$emit('postNewQuestion', this.content)
        }
        this.content = ''
        this.error = ''
        wx.showToast({
          title: '提交成功',
          icon: 'success',
          duration: 2000
        })
      } else {
        wx.showToast({
          title: '请输入正确内容',
          image: '/static/images/error2.png',
          duration: 2000
        })
      }
    },
    changeText: function () {
      this.error = ''
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
  top: 10%;
  left: 0;
  z-index: 1001;
  background: #FAFAFA;
  margin: 0 50rpx 0 50rpx;
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
</style>
