// PetKuaför JavaScript Dosyası

document.addEventListener('DOMContentLoaded', function() {
    // Şehirlerde arama yapma fonksiyonu
    const locationInput = document.getElementById('location');
    if (locationInput) {
        locationInput.addEventListener('focus', function() {
            // Burada gerçek bir uygulamada AJAX ile şehir listesi çekilebilir
            console.log('Şehir listesi açıldı');
        });
    }

    // Konum izni için buton ekleme
    addLocationButton();

    // Randevu sayfasında hizmet seçimi değiştiğinde
    const serviceSelect = document.getElementById('service');
    if (serviceSelect) {
        serviceSelect.addEventListener('change', function() {
            updateServiceInfo();
        });
    }

    // Randevu sayfasında tarih seçimi değiştiğinde
    const dateInput = document.getElementById('date');
    if (dateInput) {
        dateInput.addEventListener('change', function() {
            const selectedDate = this.value;
            updateAvailableTimeSlots(selectedDate);
        });
    }

    // Mobil menü toggle
    const mobileMenuButton = document.getElementById('mobile-menu-button');
    const mobileMenu = document.getElementById('navbarNav');
    if (mobileMenuButton && mobileMenu) {
        mobileMenuButton.addEventListener('click', function() {
            console.log('Mobil menü tıklandı');
        });
    }

    // Sayfa yükleme tamamlandı
    console.log('Sayfa hazır');
});

// Konum izni için buton ekleme fonksiyonu
function addLocationButton() {
    const locationInput = document.getElementById('location');
    if (!locationInput) return;

    // Konum butonunun konteyneri
    const locationContainer = locationInput.parentElement;
    
    // Konum butonu oluşturma
    const locationButton = document.createElement('button');
    locationButton.type = 'button';
    locationButton.className = 'btn btn-outline-secondary';
    locationButton.innerHTML = '<i class="fas fa-location-arrow"></i>';
    locationButton.title = 'Konumumu Kullan';
    
    // Butonun input grubuna eklenmesi
    locationContainer.appendChild(locationButton);
    
    // Butona tıklama olayı
    locationButton.addEventListener('click', function() {
        getGeolocation();
    });
}

// Konum alma fonksiyonu
function getGeolocation() {
    if (!navigator.geolocation) {
        alert('Tarayıcınız konum özelliğini desteklemiyor.');
        return;
    }

    navigator.geolocation.getCurrentPosition(
        // Başarılı olduğunda
        function(position) {
            const latitude = position.coords.latitude;
            const longitude = position.coords.longitude;
            
            // Burada gerçek bir uygulamada bu koordinatlar kullanılarak
            // en yakın şehir ve semt bilgisi API'den alınabilir
            console.log(`Konum alındı: ${latitude}, ${longitude}`);
            
            // Örnek olarak
            const locationInput = document.getElementById('location');
            if (locationInput) {
                locationInput.value = 'İstanbul'; // Gerçek uygulamada API'den dönen değer
            }
        },
        // Hata olduğunda
        function(error) {
            switch(error.code) {
                case error.PERMISSION_DENIED:
                    showPermissionError();
                    break;
                case error.POSITION_UNAVAILABLE:
                    alert('Konum bilgisi alınamadı.');
                    break;
                case error.TIMEOUT:
                    alert('Konum isteği zaman aşımına uğradı.');
                    break;
                case error.UNKNOWN_ERROR:
                    alert('Bilinmeyen bir hata oluştu.');
                    break;
            }
        }
    );
}

// İzin hatası gösterme
function showPermissionError() {
    alert('Konum izni reddedildi. Konumunuzu kullanabilmemiz için izin vermeniz gerekiyor.');
}

// Hizmet bilgilerini güncelleme (Randevu sayfası için)
function updateServiceInfo() {
    const serviceSelect = document.getElementById('service');
    if (!serviceSelect) return;
    
    const selectedService = serviceSelect.options[serviceSelect.selectedIndex];
    const price = selectedService.getAttribute('data-price') || '0';
    const duration = selectedService.getAttribute('data-duration') || '0';
    
    // Fiyat ve süre bilgilerini güncelleme
    const priceElement = document.getElementById('service-price');
    const durationElement = document.getElementById('service-duration');
    
    if (priceElement) {
        priceElement.textContent = formatCurrency(price);
    }
    
    if (durationElement) {
        durationElement.textContent = `${duration} dk`;
    }
    
    // Randevu özetini güncelleme
    updateSummary();
}

// Mevcut zaman dilimlerini güncelleme (Randevu sayfası için)
function updateAvailableTimeSlots(selectedDate) {
    const timeSelect = document.getElementById('time');
    if (!timeSelect) return;
    
    // Gerçek uygulamada burada seçilen tarihe göre AJAX ile 
    // uygun zaman dilimleri çekilebilir
    
    // Örnek olarak sabit zaman dilimleri
    const availableTimes = [
        '09:00', '09:30', '10:00', '10:30', '11:00', '11:30',
        '13:00', '13:30', '14:00', '14:30', '15:00', '15:30'
    ];
    
    // Tüm mevcut seçenekleri temizle
    timeSelect.innerHTML = '';
    
    // Bir seçenek ekle talimatı
    const defaultOption = document.createElement('option');
    defaultOption.value = '';
    defaultOption.textContent = 'Saat seçin';
    defaultOption.disabled = true;
    defaultOption.selected = true;
    timeSelect.appendChild(defaultOption);
    
    // Mevcut saatleri ekle
    availableTimes.forEach(time => {
        const option = document.createElement('option');
        option.value = time;
        option.textContent = time;
        timeSelect.appendChild(option);
    });
    
    // Randevu özetini güncelleme
    updateSummary();
}

// Randevu özetini güncelleme
function updateSummary() {
    const summaryService = document.getElementById('summary-service');
    const summaryDate = document.getElementById('summary-date');
    const summaryTime = document.getElementById('summary-time');
    const summaryPrice = document.getElementById('summary-price');
    
    const serviceSelect = document.getElementById('service');
    const dateInput = document.getElementById('date');
    const timeSelect = document.getElementById('time');
    
    if (summaryService && serviceSelect) {
        const selectedOption = serviceSelect.options[serviceSelect.selectedIndex];
        summaryService.textContent = serviceSelect.value ? selectedOption.textContent : '-';
    }
    
    if (summaryDate && dateInput) {
        summaryDate.textContent = dateInput.value ? formatDate(dateInput.value) : '-';
    }
    
    if (summaryTime && timeSelect) {
        summaryTime.textContent = timeSelect.value ? timeSelect.value : '-';
    }
    
    if (summaryPrice && serviceSelect) {
        const selectedOption = serviceSelect.options[serviceSelect.selectedIndex];
        const price = selectedOption.getAttribute('data-price') || '0';
        summaryPrice.textContent = serviceSelect.value ? formatCurrency(price) : '-';
    }
}

// Para birimini formatla
function formatCurrency(amount) {
    return new Intl.NumberFormat('tr-TR', {
        style: 'currency',
        currency: 'TRY',
        minimumFractionDigits: 2
    }).format(amount);
}

// Tarihi formatla
function formatDate(dateString) {
    const options = { year: 'numeric', month: 'long', day: 'numeric' };
    return new Date(dateString).toLocaleDateString('tr-TR', options);
}