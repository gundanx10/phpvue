<template>
<div id="app" class="listbox">
  <div class="helpTop"><img :src=img_url mode="widthFix"></div>
</div>
</template>

<script>
import {
  mapGetters,
  mapActions
} from 'vuex'
export default {
  data() {
    return {
      img_url: ''
    }
  },
  computed: mapGetters({
    user: 'getUserInfo'
  }),
  methods: {
    ...mapActions(['goUrl']),
    init: function() {
      this.img_url = 'https://api.huyueyue.com/xcxqrcode?uid=' + this.user.id
    }
  },
  onLoad: function(options) {
    this.init()
  },
  onShareAppMessage() {
    return {
      title: '装修知识汇商城',
      desc: '你想要的都在这里',
      path: '/pages/index/main?pid=' + this.user.id
    }
  }
}
</script>
<style>
.listbox {
  width: 100%;
  height: 100%;
  background: #efeff0;
}

.helpTitle {
  width: 94%;
  line-height: 14px;
  background: #efeff0;
  font-size: 12px;
  padding: 3% 3% 0 3%;
  font-size: 14px;
  text-align: center;
  color: #666;
}

.helpTop {
  width: 100%;
  height: 100%;
  line-height: 0px;
  background: #efeff0;
}

.helpTop img {
  width: 90%;
  line-height: 0px;
}

.helpTop {}

.helpTop ._img {
  width: 90%;
  padding: 10% 5% 14% 5%
}
</style>
