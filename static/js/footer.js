// Footer link hover effects
document.addEventListener('DOMContentLoaded', function() {
    // Get all footer links
    const footerLinks = document.querySelectorAll('.footer-link');
    
    // Add hover event listeners
    footerLinks.forEach(link => {
        link.addEventListener('mouseenter', function() {
            this.style.color = '#ffffff';
        });
        
        link.addEventListener('mouseleave', function() {
            this.style.color = '';
        });
    });
});