<template>
<div id="app" class="listbox">

<div class="cartBox" v-for="item in data" :key="item.id">
    <div class="shuru" v-if="!item.edit" v-on:click="editCart(item.id)"><img src="http://zsh.huyueyue.com/images/shuru.svg"  mode="widthFix"></div>
    <div class="shuru" v-else v-on:click="editedCart(item.id)">完成</div>
    <div class="cartShow">
            <div class="cartShowRd"><input type="checkbox" class="cb" :checked="item.checked" v-on:click="changeCheck(item)"></div>
            <div class="cartShowLf"><img :src="item.info.thumb" mode="widthFix"></div>
                <div class="cartShowRt">
                  <h1 >{{item.info.title}}</h1>
                  <el-input-number v-if="item.edit" v-model="item.quantity"></el-input-number>
                  <h2 v-if="!item.edit && item.info.description" >
                  <wxParse :content="item.info.description">
                        </wxParse>
                  </h2>
                  <h3 >¥{{item.info.price}}<b v-if="!item.edit">x {{item.quantity}}</b></h3>
                  <div v-if="item.edit" class="delete" v-on:click="deleteCart(item.id)" >删除</div>
                </div>
        </div>
</div>
<div class="MHr40"></div><!--误删，65px的高度-->
<!--FOOTer-->


<div class="accountsBt">
        <div class="accounts01">
          <span><input type="checkbox" id="checkAll" :checked="allCheck()" v-on:click="clickAllChecked()"></span>
            <label for="checkAll"><h1>全选</h1></label>
        </div>

        <div class="accounts02" >合计：<font class="chengse06">¥{{totalPrice}}</font></div>


        <div v-if="account"  class="accounts03" v-on:click="addOrder(data)"><span class="bt50">结算</span></div>
        <div v-else class="accounts03" ><span class="bt50huise">结算</span></div>
</div>

</div>
</template>

<script>
import { mapGetters, mapActions } from 'vuex'
import wxParse from 'mpvue-wxparse'
import filters from '@/common/filters'
//  import dataApi from '@/data/index'
import ElmInput from '@/components/ElmInput'
export default{
  data () {
    return {
      num1: 1,
      checked: false
    }
  },
  computed: {
    ...mapGetters({
      data: 'getCart'
    }),
    totalPrice () {
      var total = 0
      this.data.forEach(function (item, index) {
        if (item.checked === true) {
          total += item.quantity * item.info.price
        }
      })
      if (total === 0) {
        return 0
      }
      return filters.totalPrice(total)
    },
    account () {
      var active = false
      this.data.forEach(function (item, index) {
        if (item.checked === true) {
          active = true
        }
      })
      return active
    }
  },
  components: {
    wxParse,
    'el-input-number': ElmInput
  },
  methods: {
    ...mapActions(['addOrder', 'goUrl']),
    editCart (id) {
      var record = this.data.find(p => p.id === id)
      record.edit = true
    },
    editedCart (id) {
      var record = this.data.find(p => p.id === id)
      record.edit = false
    },
    deleteCart (id) {
      var _this = this
      this.data.forEach(function (item, index) {
        if (item.id === id) {
          _this.data.splice(index, 1)
        }
      })
    },
    allCheck () {
      var checked = true
      this.data.forEach(function (item, index) {
        if (item.checked !== true) {
          checked = false
        }
      })
      return checked
    },
    clickAllChecked () {
      this.data.forEach(function (item, index) {
        item.checked = true
      })
    },
    changeCheck (item) {
      item.checked = !item.checked
    }
  }
}
</script>
<style>
.shopTitleRt ._h1{font-size:15px;padding-top:19px}
.el-input-number{width:125px;line-height:30px;margin:10px 0 5px 0}
.el-input-number .el-input .input__inner{border:1px solid #dcdfe6;height:32px}
.bt50huise,.bt50{display:inline-block;line-height:56px}
.cartShowRt{position:relative;float:right}
.delete{height:80%;background:red;color:#fff;position:absolute;width:50px;top:20%;right:0;text-align:center;line-height:80px}
.shuru{width:95%; padding:0 2.5% 0% 2.5%; text-align:right;}
.shuru ._img{width:24px}
.cartBox{ background:#fff;width:94%;padding:3% 3% 3% 2%; overflow:hidden; border-top:solid 10px #efeff0;border-bottom:solid 1px #c9c9c9;}
.cartShow{ background:#fff; width:100%;overflow:hidden;}
.cartShowLf{ width:40%; line-height:0px; float:left;}
.cartShowLf ._img{ width:100%; line-height:0px;}
.cartShowRt{ padding:2% 2% 2% 0;}
.cartShowRt ._h1{ width:100%; display:block; color:#562020; font-size:14px; }
.cartShowRt ._h2{ width:100%; display:block;  font-size:14px; color:#666; font-size:12px; font-weight:normal;}
.cartShowRt ._h3{ width:100%; display:block; color:#e15506; font-size:14px; font-size:14px; padding-top:5px;}
.cartShowRt ._h3 ._b{ color:#929292; float:right; font-weight:normal;}
.cartShowRd{ width:7%; float:left; line-height:80px;}
.accountsBt{ width:100%; height:105rpx;position: fixed;bottom: 0;left:0; background:#fff;z-index:99999; border:solid 1px #e7e7e7;}
.accounts01{ width:20%; height:105rpx;  float:left;}
.accounts01 ._span{ width:40%; text-align:center; line-height:95rpx; display:block; float:left;}
.accounts01 ._h1{ font-size:14px; float:left; line-height:105rpx;}
.accountsBt .submit{background: #e0380b;text-align: center;line-height: 105rpx;border: 0; width: 100%; color: #fff;font-size: 14px;}
.accountsBt .submit_block{background: #C7C7C7;text-align: center;line-height: 105rpx;border: 0; width: 100%; color: #fff;font-size: 14px;}
.accounts02{ width:37%; height:42px;  float:left; font-size:12px; line-height:38px; text-align:right; padding-right:3%; padding-top:8px;}
.accounts03{ width:40%; height:50px;  float:left;}
.bt50{background:#e0380b; text-align:center; line-height:50px; border:0; width:100%; color:#fff; font-size:14px;}
.bt50huise{background:#eeeeee; text-align:center; line-height:50px; border:0; width:100%; color:#666; font-size:14px;}
.chengse06{ color:#e15506;}
.cartShow .cb{font:20rpx}
.MHr40{width:100%; height:40px;}
</style>
