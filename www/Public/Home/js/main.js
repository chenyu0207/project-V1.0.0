$(function(){
  // 非空
  $('#searchBtn').on('click',function () {
    if($('#searchArea').val() === ''){
      alert('订单编号不能为空~')
    } else {
      $('#textStatus').show(500)
    }
  });
})