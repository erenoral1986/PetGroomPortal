// Fix Images and Styling Issues
document.addEventListener('DOMContentLoaded', function() {
  // Set all images to proper static URLs
  const catImg = document.getElementById('cat-grooming-img');
  if (catImg) {
    // Directly set to a known working image
    catImg.src = '/static/img/cat-image.jpg';
  }
  
  // Completely hide the service divider
  const serviceDivider = document.querySelector('.text-center.mb-5 h2.text-pet-blue + .bg-pet-teal');
  if (serviceDivider) {
    serviceDivider.style.display = 'none';
  }
  
  // Explicitly hide the divider with ID if it exists
  const serviceDividerById = document.getElementById('hizmetlerimiz-divider');
  if (serviceDividerById) {
    serviceDividerById.style.display = 'none';
  }
  
  // Fix all dividers 
  const dividers = document.querySelectorAll('.bg-pet-teal.mx-auto.my-3');
  dividers.forEach(divider => {
    if (divider.closest('section') && divider.closest('section').querySelector('h2.text-pet-blue') && 
        divider.closest('section').querySelector('h2.text-pet-blue').textContent.includes('Hizmetlerimiz')) {
      divider.style.display = 'none';
    } else {
      divider.style.height = '2px';
      divider.style.borderRadius = '1px';
    }
  });
});