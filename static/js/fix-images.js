// Fix Images and Styling Issues
document.addEventListener('DOMContentLoaded', function() {
  // Set all images to proper static URLs
  const catImg = document.getElementById('cat-grooming-img');
  if (catImg) {
    catImg.src = '/static/img/cat-grooming.jpg';
  }
  
  // Ensure all dividers are properly styled
  const serviceDivider = document.getElementById('hizmetlerimiz-divider');
  if (serviceDivider) {
    serviceDivider.style.display = 'none';
  }
  
  // Remove all dividers with class bg-pet-teal that are not necessary
  const dividers = document.querySelectorAll('.bg-pet-teal.mx-auto.my-3');
  dividers.forEach(divider => {
    if (divider.id !== 'hizmetlerimiz-divider') {
      divider.style.height = '2px';
      divider.style.borderRadius = '1px';
    }
  });
});