<template>
<div id="app" class="listbox">

    <div  class="qaBox" >
      <h1>{{detail.title}}</h1>
      <h2><div class="assistText">
      <wxParse v-if="detail" :content="detail.result"></wxParse>
      </div></h2>
    </div>


</div>
</template>

<script>
import { mapGetters } from 'vuex'
import wxParse from 'mpvue-wxparse'

export default{
  data () {
    return {
      id: 0
    }
  },
  computed: mapGetters({
    detail: 'getInteractDetail'
  }),
  components: {
    wxParse
  },
  methods: {
    moredata: function () {
      this.$store.dispatch('getInteractDetail', this.id)
    }
  },
  onLoad: function () {
    const {id} = this.$route.query
    this.id = id
    if (!id) {
      this.$router.back()
    }
    this.moredata()
  }
}
</script>
<style>
.qaBox{width:94%;padding:3% 3% 3% 3%; overflow:hidden; border-bottom:1px solid #e1e1e1;}
.qaBox ._h1{ width:93%;background: url(http://zsh.huyueyue.com/images/q.jpg) no-repeat left 0px;background-size:20px; display:block; font-size:14px; line-height:20px; padding-left:7%;}
.qaBox ._h2{ width:93%;background: url(http://zsh.huyueyue.com/images/a.jpg) no-repeat left 0px;background-size:20px; display:block; font-size:14px; line-height:20px; padding-left:7%; font-weight:normal; margin-top:10px;}
.assistText{padding:0% 3% 3% 3%; overflow:hidden;}
.assistText ._h1{ font-size:12px;}
.assistText ._h2{ font-size:12px; font-weight:normal; padding-top:5px; color:#888; line-height:22px;padding-bottom:5px; }
</style>
