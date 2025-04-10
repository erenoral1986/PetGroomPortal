/**
 * Pet Kuaför - Ana JavaScript Dosyası
 * 
 * Tüm sayfalarda kullanılan ortak işlevleri içerir
 */

// DOMContentLoaded etkinliği
document.addEventListener('DOMContentLoaded', function() {
    // Tooltip'leri başlat
    enableTooltips();
    
    // Onay gerektiren işlemler için olay dinleyicileri ekle
    setupConfirmActions();
    
    // Bootstrap dropdown'ları otomatik olarak kapat
    setupDropdownAutoClose();
});

/**
 * Bootstrap tooltip'leri etkinleştir
 */
function enableTooltips() {
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function(tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
}

/**
 * data-confirm özniteliği olan öğeler için onay işlemlerini ayarla
 */
function setupConfirmActions() {
    const confirmElements = document.querySelectorAll('[data-confirm]');
    
    confirmElements.forEach(function(element) {
        element.addEventListener('click', function(event) {
            const message = this.getAttribute('data-confirm');
            if (!confirmAction(message)) {
                event.preventDefault();
            }
        });
    });
}

/**
 * Bootstrap dropdown'ları için otomatik kapanma özelliğini ayarla
 */
function setupDropdownAutoClose() {
    document.addEventListener('click', function(event) {
        const dropdowns = document.querySelectorAll('.dropdown-menu.show');
        dropdowns.forEach(function(dropdown) {
            if (!dropdown.contains(event.target)) {
                const dropdownToggle = document.querySelector('[data-bs-toggle="dropdown"][aria-expanded="true"]');
                if (dropdownToggle && !dropdownToggle.contains(event.target)) {
                    new bootstrap.Dropdown(dropdownToggle).hide();
                }
            }
        });
    });
}

/**
 * Onay iletişim kutusu göster
 * 
 * @param {string} message - Gösterilecek mesaj
 * @returns {boolean} - Kullanıcı onayı
 */
function confirmAction(message) {
    return confirm(message || 'Bu işlemi yapmak istediğinize emin misiniz?');
}

/**
 * Para birimini formatlı göster
 * 
 * @param {number} amount - Tutar
 * @returns {string} - Formatlanmış tutar
 */
function formatCurrency(amount) {
    return new Intl.NumberFormat('tr-TR', {
        style: 'currency',
        currency: 'TRY',
        minimumFractionDigits: 2,
        maximumFractionDigits: 2
    }).format(amount);
}

/**
 * Tarihi formatlı göster
 * 
 * @param {string} dateString - Tarih string'i
 * @returns {string} - Formatlanmış tarih
 */
function formatDate(dateString) {
    const date = new Date(dateString);
    return new Intl.DateTimeFormat('tr-TR', {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric'
    }).format(date);
}

/**
 * Saati formatlı göster
 * 
 * @param {string} timeString - Saat string'i (HH:MM:SS)
 * @returns {string} - Formatlanmış saat (HH:MM)
 */
function formatTime(timeString) {
    // Saati parçalara ayır
    const timeParts = timeString.split(':');
    if (timeParts.length >= 2) {
        return timeParts[0].padStart(2, '0') + ':' + timeParts[1].padStart(2, '0');
    }
    return timeString;
}

/**
 * Tarih ve saati birleştirerek tam DateTime nesnesi oluştur
 * 
 * @param {string} dateString - Tarih string'i (YYYY-MM-DD)
 * @param {string} timeString - Saat string'i (HH:MM:SS)
 * @returns {Date} - DateTime nesnesi
 */
function combineDateAndTime(dateString, timeString) {
    const dateParts = dateString.split('-');
    const timeParts = timeString.split(':');
    
    const year = parseInt(dateParts[0]);
    const month = parseInt(dateParts[1]) - 1; // JavaScript ayları 0-11 arasında
    const day = parseInt(dateParts[2]);
    
    const hour = parseInt(timeParts[0]);
    const minute = parseInt(timeParts[1]);
    const second = timeParts.length > 2 ? parseInt(timeParts[2]) : 0;
    
    return new Date(year, month, day, hour, minute, second);
}

/**
 * Şu anki tarih ve saat bilgisini al
 * 
 * @returns {Object} - Tarih ve saat bilgisi ({ date: 'YYYY-MM-DD', time: 'HH:MM:SS' })
 */
function getCurrentDateTime() {
    const now = new Date();
    
    const year = now.getFullYear();
    const month = (now.getMonth() + 1).toString().padStart(2, '0');
    const day = now.getDate().toString().padStart(2, '0');
    
    const hours = now.getHours().toString().padStart(2, '0');
    const minutes = now.getMinutes().toString().padStart(2, '0');
    const seconds = now.getSeconds().toString().padStart(2, '0');
    
    return {
        date: `${year}-${month}-${day}`,
        time: `${hours}:${minutes}:${seconds}`
    };
}