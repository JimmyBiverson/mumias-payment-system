// responsive.js
// Adds lazy loading to images and triggers animate.css classes when elements enter viewport
document.addEventListener('DOMContentLoaded', function(){
  // set loading=lazy for images without explicit loading attribute
  document.querySelectorAll('img').forEach(function(img){
    if(!img.hasAttribute('loading')) img.setAttribute('loading','lazy');
  });

  // IntersectionObserver for animated reveal
  var observerSupported = 'IntersectionObserver' in window;
  var animatedEls = document.querySelectorAll('.fade-in-up');
  if(animatedEls.length){
    if(observerSupported){
      var io = new IntersectionObserver(function(entries){
        entries.forEach(function(entry){
          if(entry.isIntersecting){
            entry.target.classList.add('animate__animated','animate__fadeInUp');
            io.unobserve(entry.target);
          }
        })
      },{threshold:0.15});
      animatedEls.forEach(function(el){io.observe(el)});
    }else{
      // fallback: simply add classes
      animatedEls.forEach(function(el){el.classList.add('animate__animated','animate__fadeInUp')});
    }
  }

  // Lazy load for elements with data-src (images used for viewers)
  var lazyData = document.querySelectorAll('img[data-src]');
  if(lazyData.length){
    if(observerSupported){
      var io2 = new IntersectionObserver(function(entries){
        entries.forEach(function(entry){
          if(entry.isIntersecting){
            var img = entry.target;
            img.src = img.getAttribute('data-src');
            img.removeAttribute('data-src');
            io2.unobserve(img);
          }
        })
      });
      lazyData.forEach(function(img){io2.observe(img)});
    }else{
      lazyData.forEach(function(img){img.src = img.getAttribute('data-src');img.removeAttribute('data-src')});
    }
  }
});
