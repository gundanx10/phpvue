<template>
<div id="app" class="listbox">

  <div class="assistTitle">
    <h1>{{detail.title}}</h1>
    <h2>{{detail.inputtime}}<b>装修知识汇</b></h2>
  </div>
  <div class="assistText">
    <wxParse v-if="detail.content" :content="detail.content">
    </wxParse>
  </div>

</div>
</template>

<script>
import wxParse from 'mpvue-wxparse'
import filters from '@/common/filters'
import dataApi from '@/data/index'
export default {
  data() {
    return {
      id: 0,
      detail: []
    }
  },
  components: {
    wxParse
  },
  methods: {
    moredata: function () {
      let _this = this
      dataApi.getHelpDetail(function (res) {
        if(res) {
          res = Object.assign(res, {
            inputtime: filters.foramtDate(res.inputtime)
          })
          _this.detail = res
        }
      }, this.id)
    }
  },
  onLoad: function () {
    const {
      id
    } = this.$route.query
    this.id = id
    if(!id) {
      this.$router.back()
    }
    this.moredata()
  }
}
</script>
<style>
.assistTitle {
  padding: 3% 3% 3% 3%;
  overflow: hidden;
  border-top: 8px solid #efeff0;
}

.assistTitle ._h1 {
  font-size: 14px;
}

.assistTitle ._h2 {
  font-size: 12px;
  font-weight: normal;
  padding-top: 5px;
  color: #888;
}

.assistTitle ._h2 ._b {
  padding-left: 5px;
  font-weight: normal;
}

.assistText {
  padding: 0% 3% 3% 3%;
  overflow: hidden;
}

.assistText ._h1 {
  font-size: 12px;
}

.assistText ._h2 {
  font-size: 12px;
  font-weight: normal;
  padding-top: 5px;
  color: #888;
  line-height: 22px;
  padding-bottom: 5px;
}
</style>
