function setRem () {
  let htmlWidth = document.documentElement.clientWidth || document.body.clientWidth;
  let htmlDom = document.getElementsByTagName('html')[0];

  htmlWidth = htmlWidth > 750 ? 750 : htmlWidth

  // 10: 设计稿width / rootValue (vue.config.js) eg: 375 / 37.5 = 10rem
  htmlDom.style.fontSize= htmlWidth / 10 + 'px';
}

// init
setRem()
window.onresize = function () {
  location.reload();
  setRem();
}
