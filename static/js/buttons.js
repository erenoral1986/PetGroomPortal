// Footer menü hover efektleri için JavaScript
document.addEventListener('DOMContentLoaded', function() {
    // Footer linklerini bul
    const footerLinks = document.querySelectorAll('.footer-link');
    
    // Hover olaylarını ekle
    footerLinks.forEach(link => {
        link.addEventListener('mouseenter', function() {
            this.style.color = '#ffffff';
        });
        
        link.addEventListener('mouseleave', function() {
            this.style.color = '';
        });
    });
});