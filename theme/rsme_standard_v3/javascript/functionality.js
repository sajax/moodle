var warnLink = document.querySelector('.rsme-warning-link');
warnLink.addEventListener('click', function(event) {
  event.preventDefault();
  
  var warning = alert('Login Message');
  
  if (warning) {
    window.location.href = this.getAttribute('href');
  }
});