// Fix Images and Styling Issues
document.addEventListener('DOMContentLoaded', function() {
  // Set all images to proper static URLs
  const catImg = document.getElementById('cat-grooming-img');
  if (catImg) {
    // Directly set to a known working image
    catImg.src = '/static/img/kedicik.jpg';
    
    // Add border and adjust size explicitly
    catImg.style.border = '4px solid #fff';
    catImg.style.boxShadow = '0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05)';
    catImg.style.borderRadius = '0.5rem';
    catImg.style.width = '96px';
    catImg.style.height = '96px';
    catImg.style.objectFit = 'cover';
  }
  
  // Completely hide ALL dividers under Hizmetlerimiz with different selectors
  document.querySelectorAll('section').forEach(section => {
    const heading = section.querySelector('h2.text-pet-blue, h2.fs-1.fw-bold.text-pet-blue');
    if (heading && heading.textContent.includes('Hizmetlerimiz')) {
      const dividers = section.querySelectorAll('.bg-pet-teal, .bg-pet-teal.mx-auto.my-3, #hizmetlerimiz-divider');
      dividers.forEach(div => {
        div.style.display = 'none';
      });
    }
  });
  
  // Explicitly hide the divider with ID if it exists
  const serviceDividerById = document.getElementById('hizmetlerimiz-divider');
  if (serviceDividerById) {
    serviceDividerById.style.display = 'none';
  }
  
  // Query and hide any divider that comes after the Hizmetlerimiz heading
  const headings = document.querySelectorAll('h2.text-pet-blue, h2.fs-1.fw-bold.text-pet-blue');
  headings.forEach(heading => {
    if (heading.textContent.includes('Hizmetlerimiz')) {
      let next = heading.nextElementSibling;
      if (next && (next.classList.contains('bg-pet-teal') || next.id === 'hizmetlerimiz-divider')) {
        next.style.display = 'none';
      }
    }
  });
});