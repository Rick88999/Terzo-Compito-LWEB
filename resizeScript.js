function resizeContent() {
  var windowHeight = window.innerHeight;
  var windowWidth = window.innerWidth;


  var flex_showing_box = document.querySelector('.flex_showing_box');
  flex_showing_box.style.height = windowHeight + 'px';


  var show_data = document.querySelector('.show_data');
  show_data.style.width = windowWidth * 0.48 + 'px';
  var show_image = document.querySelector('.show_image');
  show_image.style.width = windowWidth * 0.48 + 'px';
  show_image.style.height = windowWidth * 0.48 + 'px';



}


window.addEventListener('resize', resizeContent);


resizeContent();
