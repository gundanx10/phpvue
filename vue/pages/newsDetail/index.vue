<template>
<div id="app" class="listbox">

  <div class="pageTitle">{{detail.title}}</div>
  <div class="pageTips">
    <h1>{{detail.type}}</h1></div>
  <div class="pageContent">
    <wxParse v-if="detail.content" :content="detail.content">
    </wxParse>
  </div>

  <detailFooter v-bind:detail="detail"></detailFooter>

  <auth v-if="checkUser"></auth>
</div>
</template>
<script>
import {
  mapGetters,
  mapActions
} from 'vuex'
import detailFooter from '@/components/DetailFooter'
import wxParse from 'mpvue-wxparse'
import auth from '@/components/Auth'
import dataApi from '@/data/index'
export default {
  data() {
    return {
      id: 0,
      detail: []
    }
  },
  computed: mapGetters({
    checkUser: 'checkUser'
  }),
  created() {},
  methods: {
    ...mapActions(['goUrl']),
    moredata: function () {
      const _this = this
      dataApi.getNewsDetail(function (res) {
        _this.detail = res
      }, this.id)
    }
  },
  components: {
    wxParse,
    detailFooter,
    auth
  },
  beforeMount() {
    const {
      id
    } = this.$route.query
    this.id = id
    if(!id) {
      this.$router.back()
    }
    this.moredata()
  },
  mounted() {},
  onLoad: function () {},
  onShareAppMessage() {
    return {
      title: this.detail.title,
      desc: this.detail.title,
      path: '/pages/newsDetail/main?id=' + this.id
    }
  }
}
</script>

<style>
.pageTitle {
  width: 90%;
  padding: 5%;
  overflow: hidden;
  font-size: 16px;
  color: #481d02;
  line-height: 22px;
}

.pageTips {
  width: 90%;
  padding: 0% 5% 3% 5%;
}

.pageTips ._h1 {
  width: 100%;
  display: block;
  background: url(http://zsh.huyueyue.com/images/admin.png) no-repeat left 0px;
  background-size: 24px;
  font-size: 13px;
  line-height: 24px;
  padding-left: 8%;
}

.pageShow {
  width: 90%;
  padding: 0 5% 5% 5%;
  overflow: hidden;
  line-height: 22px;
}

.pageContent {
  width: 90%;
  padding: 0 5% 5% 5%;
  overflow: hidden;
  line-height: 22px;
}

.pageShow img {
  width: 100%;
  line-height: 0px;
}

.pageShow h1 {
  width: 100%;
  line-height: 24px;
  font-size: 14px;
  display: block;
  padding-bottom: 10px;
}

.pageShow p {
  display: block;
  color: #666;
  line-height: 24px;
  font-size: 14px;
  padding-bottom: 10px;
}

.MHr {
  width: 100%;
  height: 20px;
}
</style>
