// Buton hover efektleri için JavaScript
document.addEventListener('DOMContentLoaded', function() {
    // Ana sayfa butonları
    
    // Kuaför Bul
    const kuaforBulBtn = document.getElementById('kuaforBulBtn');
    if (kuaforBulBtn) {
        kuaforBulBtn.addEventListener('mouseenter', function() {
            this.style.backgroundColor = '#167f95'; // Koyu mavi
        });
        
        kuaforBulBtn.addEventListener('mouseleave', function() {
            this.style.backgroundColor = '#1a9cb7'; // Normal mavi
        });
    }
    
    // Hizmetleri İncele
    const hizmetlerBtn = document.getElementById('hizmetlerBtn');
    if (hizmetlerBtn) {
        hizmetlerBtn.addEventListener('mouseenter', function() {
            this.style.backgroundColor = '#1a9cb7';
            this.style.color = 'white';
        });
        
        hizmetlerBtn.addEventListener('mouseleave', function() {
            this.style.backgroundColor = 'transparent';
            this.style.color = '#1a9cb7';
        });
    }
    
    // Tüm hizmetleri görüntüle butonu
    const allServicesBtn = document.getElementById('allServicesBtn');
    if (allServicesBtn) {
        allServicesBtn.addEventListener('mouseenter', function() {
            this.style.backgroundColor = '#167f95'; // Koyu mavi
        });
        
        allServicesBtn.addEventListener('mouseleave', function() {
            this.style.backgroundColor = '#1a9cb7'; // Normal mavi
        });
    }
    
    // Hizmet butonları
    const serviceButtons = document.querySelectorAll('.service-btn');
    serviceButtons.forEach(button => {
        button.addEventListener('mouseenter', function() {
            this.style.backgroundColor = '#167f95'; // Koyu mavi
        });
        
        button.addEventListener('mouseleave', function() {
            this.style.backgroundColor = '#1a9cb7'; // Normal mavi
        });
    });
    
    // Genel butonlar
    // Mavi butonlar (yukarıdakilerin kapsamadığı)
    const blueButtons = document.querySelectorAll('.btn.bg-pet-blue:not(#kuaforBulBtn):not(#allServicesBtn):not(.service-btn)');
    blueButtons.forEach(button => {
        button.addEventListener('mouseenter', function() {
            this.style.backgroundColor = '#167f95'; // Koyu mavi
        });
        
        button.addEventListener('mouseleave', function() {
            this.style.backgroundColor = '#1a9cb7'; // Normal mavi
        });
    });
    
    // Outline butonlar (yukarıdakilerin kapsamadığı)
    const outlineButtons = document.querySelectorAll('.btn.border-pet-blue:not(#hizmetlerBtn)');
    outlineButtons.forEach(button => {
        button.addEventListener('mouseenter', function() {
            this.style.backgroundColor = '#1a9cb7';
            this.style.color = 'white';
        });
        
        button.addEventListener('mouseleave', function() {
            this.style.backgroundColor = 'transparent';
            this.style.color = '#1a9cb7';
        });
    });
});