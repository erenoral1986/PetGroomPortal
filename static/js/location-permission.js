// Konum izni için tüm fonksiyonları içeren yeni dosya
document.addEventListener('DOMContentLoaded', function() {
    // Global değişken - bu sayfada popup gösterilip gösterilmediğini tutar
    window.popupShownOnThisPage = false;
    
    // Test düğmesi - konum izni durumunu test etmek için
    addTestButton();
    
    // Konum butonu ekle
    addLocationButton();
    
    // Sayfa yüklendiğinde konum iznini kontrol et
    checkPermissionOnPageLoad();
});

// Test düğmesi ekle (geliştirme amaçlı)
function addTestButton() {
    // Birkaç saniye bekleyerek sayfanın tam olarak yüklenmesini sağla
    setTimeout(() => {
        const testButton = document.createElement('button');
        testButton.textContent = 'Test: Konum İznini Sıfırla';
        testButton.className = 'btn btn-sm btn-warning';
        testButton.style.position = 'fixed';
        testButton.style.bottom = '20px';
        testButton.style.right = '20px';
        testButton.style.opacity = '0.9';
        testButton.style.zIndex = '9999';
        testButton.style.padding = '8px 15px';
        testButton.style.fontWeight = 'bold';
        testButton.style.boxShadow = '0 2px 5px rgba(0,0,0,0.2)';
        
        // Click olayını doğrudan özellik olarak ata
        testButton.onclick = function() {
            console.log("Test butonu tıklandı, konum izni sıfırlanıyor...");
            localStorage.removeItem('locationPermissionGranted');
            window.popupShownOnThisPage = false;
            alert('Konum izni sıfırlandı. Sayfa yenileniyor...');
            window.location.reload(true); // Hard reload - önbelleği temizle
            return false; // Olayı durdur
        };
        
        document.body.appendChild(testButton);
        console.log("Test butonu eklendi.");
    }, 1000);
}

// Konum butonunu arama kutusuna ekler
function addLocationButton() {
    const locationInput = document.getElementById('location');
    if (!locationInput) return;
    
    const locationInputParent = locationInput.parentElement;
    if (!locationInputParent) return;
    
    // Konum butonu oluştur
    const locationButton = document.createElement('span');
    locationButton.className = 'input-group-text bg-light border-start-0 border-end-0 cursor-pointer';
    locationButton.innerHTML = '<i class="fas fa-crosshairs text-muted"></i>';
    locationButton.style.cursor = 'pointer';
    locationButton.title = 'Konumunuzu kullanın';
    
    // Konum butonunu input grubuna ekle
    const inputIcon = locationInputParent.querySelector('.input-group-text');
    if (inputIcon) {
        locationInputParent.insertBefore(locationButton, inputIcon.nextSibling);
    }
    
    // Konum butonuna tıklandığında
    locationButton.addEventListener('click', function() {
        // Bu sayfada popup gösterilmediğini işaretle
        window.popupShownOnThisPage = false;
        
        // İzin durumuna göre işlem yap
        const permissionStatus = localStorage.getItem('locationPermissionGranted');
        
        if (permissionStatus === 'true') {
            // İzin verilmiş - konumu al
            getGeolocation();
        } else if (permissionStatus === 'false') {
            // İzin reddedilmiş - "izin reddedilmiş" popup'ı göster
            showRejectedPermissionPopup();
        } else {
            // İzin hiç sorulmamış - izin iste
            showPermissionRequestPopup();
        }
    });
}

// Sayfa yüklendiğinde konum iznini kontrol eder ve gerekli popup'ı gösterir
function checkPermissionOnPageLoad() {
    console.log("Sayfa yüklendiğinde konum izni kontrolü yapılıyor...");
    
    // Konum izni durumunu kontrol et
    const permissionStatus = localStorage.getItem('locationPermissionGranted');
    console.log("Konum izni durumu:", permissionStatus);
    
    // Hiç izin sorulmamışsa - popup göster
    if (permissionStatus === null || permissionStatus === undefined) {
        console.log("Konum izni hiç verilmemiş, popup gösteriliyor");
        showNeverAskedPopup();
        return;
    }
    
    // İzin reddedilmişse - popup göster
    if (permissionStatus === 'false') {
        console.log("Konum izni reddedilmiş, popup gösteriliyor");
        showRejectedPermissionPopup();
        return;
    }
    
    // İzin verilmişse - otomatik konum al ve en yakın şehri bul
    if (permissionStatus === 'true') {
        console.log("Konum izni verilmiş, otomatik konum alınıyor");
        // Arama sayfasındaysak ve konum giriş alanı varsa:
        const locationInput = document.getElementById('location');
        if (locationInput && !locationInput.value) {
            getGeolocation();
        } else {
            console.log("Konum giriş alanı bulunamadı veya zaten bir değer var");
        }
    }
}

// Konum iznini kullanarak kullanıcı konumunu alır
function getGeolocation() {
    const locationInput = document.getElementById('location');
    if (!locationInput) return;
    
    // Konum alınıyor mesajı göster
    locationInput.value = "Konum alınıyor...";
    locationInput.disabled = true;
    
    // Tarayıcıdan konum iste
    navigator.geolocation.getCurrentPosition(
        // Başarılı olursa
        function(position) {
            const latitude = position.coords.latitude;
            const longitude = position.coords.longitude;
            
            // En yakın şehri bul
            findNearestCity(latitude, longitude, locationInput);
            
            // İzin durumunu kaydet
            localStorage.setItem('locationPermissionGranted', 'true');
        },
        // Hata olursa
        function(error) {
            locationInput.disabled = false;
            
            switch(error.code) {
                case error.PERMISSION_DENIED:
                    showLocationError("Konum izni reddedildi");
                    localStorage.setItem('locationPermissionGranted', 'false');
                    break;
                case error.POSITION_UNAVAILABLE:
                    showLocationError("Konum bilgisi alınamadı");
                    break;
                case error.TIMEOUT:
                    showLocationError("İstek zaman aşımına uğradı");
                    break;
                case error.UNKNOWN_ERROR:
                    showLocationError("Bilinmeyen bir hata oluştu");
                    break;
            }
        },
        {
            enableHighAccuracy: true,
            timeout: 10000,
            maximumAge: 0
        }
    );
}

// Konum izni hiç sorulmamış durumunda gösterilecek popup
function showNeverAskedPopup() {
    // Bu sayfada daha önce popup gösterilmiş mi kontrol et
    if (window.popupShownOnThisPage) {
        return;
    }
    
    // Popup gösterildiğini işaretle
    window.popupShownOnThisPage = true;
    
    // Popup oluştur
    const modal = document.createElement('div');
    modal.className = 'position-fixed top-0 start-0 w-100 h-100 d-flex align-items-center justify-content-center';
    modal.style.backgroundColor = 'rgba(0, 0, 0, 0.5)';
    modal.style.zIndex = '9999';
    
    modal.innerHTML = `
        <div class="bg-white p-4 rounded-3 shadow-lg" style="max-width: 400px;">
            <div class="text-center mb-3">
                <i class="fas fa-map-marker-alt fa-3x text-pet-blue mb-3"></i>
                <h5 class="fw-bold">Konum İzni Verilmemiş</h5>
                <p class="text-muted mb-3">Konum izni vermemişsiniz. Şehir adı girerek manuel olarak arama yapabilirsiniz veya konum izni vermeyi deneyebilirsiniz.</p>
            </div>
            <div class="d-flex justify-content-between">
                <button id="closeNeverAskedPopup" class="btn btn-outline-secondary px-4">Tamam</button>
                <button id="givePermissionNeverAsked" class="btn bg-pet-blue text-white px-4">İzin Ver</button>
            </div>
        </div>
    `;
    
    document.body.appendChild(modal);
    
    // Tamam butonu - kapat
    document.getElementById('closeNeverAskedPopup').addEventListener('click', function() {
        modal.remove();
    });
    
    // İzin ver butonu - Chrome popup'ını göster
    document.getElementById('givePermissionNeverAsked').addEventListener('click', function() {
        modal.remove();
        
        // Chrome'un konum izni dialogunu göster
        navigator.geolocation.getCurrentPosition(
            // Başarılı olursa
            function(position) {
                localStorage.setItem('locationPermissionGranted', 'true');
                
                const locationInput = document.getElementById('location');
                if (locationInput) {
                    findNearestCity(position.coords.latitude, position.coords.longitude, locationInput);
                }
            },
            // Hata olursa
            function(error) {
                if (error.code === error.PERMISSION_DENIED) {
                    localStorage.setItem('locationPermissionGranted', 'false');
                    showLocationError("Konum izni reddedildi");
                }
            },
            { enableHighAccuracy: true, timeout: 10000, maximumAge: 0 }
        );
    });
}

// Konum izni reddedilmiş durumunda gösterilecek popup
function showRejectedPermissionPopup() {
    // Bu sayfada daha önce popup gösterilmiş mi kontrol et
    if (window.popupShownOnThisPage) {
        return;
    }
    
    // Popup gösterildiğini işaretle
    window.popupShownOnThisPage = true;
    
    // Popup oluştur
    const modal = document.createElement('div');
    modal.className = 'position-fixed top-0 start-0 w-100 h-100 d-flex align-items-center justify-content-center';
    modal.style.backgroundColor = 'rgba(0, 0, 0, 0.5)';
    modal.style.zIndex = '9999';
    
    modal.innerHTML = `
        <div class="bg-white p-4 rounded-3 shadow-lg" style="max-width: 400px;">
            <div class="text-center mb-3">
                <i class="fas fa-map-marker-alt fa-3x text-pet-blue mb-3"></i>
                <h5 class="fw-bold">Konum İzni Reddedildi</h5>
                <p class="text-muted mb-3">Konum iznini reddetmişsiniz. Şehir adı girerek manuel olarak arama yapabilirsiniz veya konum izni vermeyi deneyebilirsiniz.</p>
            </div>
            <div class="d-flex justify-content-between">
                <button id="closeRejectedPopup" class="btn btn-outline-secondary px-4">Tamam</button>
                <button id="retryPermission" class="btn bg-pet-blue text-white px-4">İzin Ver</button>
            </div>
        </div>
    `;
    
    document.body.appendChild(modal);
    
    // Tamam butonu - kapat
    document.getElementById('closeRejectedPopup').addEventListener('click', function() {
        modal.remove();
    });
    
    // İzin ver butonu - konum izni verme işlemini başlat
    document.getElementById('retryPermission').addEventListener('click', function() {
        // Popup'ı kaldır
        modal.remove();
        
        // localStorage durumunu sıfırla
        localStorage.removeItem('locationPermissionGranted');
        
        // Sayfa yenile - bu yeni bir konum izni isteği başlatacak
        window.location.reload(true);
    });
}

// Kullanıcıdan konum izni isteyen popup
function showPermissionRequestPopup() {
    // Bu sayfada daha önce popup gösterilmiş mi kontrol et
    if (window.popupShownOnThisPage) {
        return;
    }
    
    // Popup gösterildiğini işaretle
    window.popupShownOnThisPage = true;
    
    // Popup oluştur
    const modal = document.createElement('div');
    modal.className = 'position-fixed top-0 start-0 w-100 h-100 d-flex align-items-center justify-content-center';
    modal.style.backgroundColor = 'rgba(0, 0, 0, 0.5)';
    modal.style.zIndex = '9999';
    
    modal.innerHTML = `
        <div class="bg-white p-4 rounded-3 shadow-lg" style="max-width: 400px;">
            <div class="text-center mb-3">
                <i class="fas fa-map-marker-alt fa-3x text-pet-blue mb-3"></i>
                <h5 class="fw-bold">Konumunuzu kullanmamıza izin verin</h5>
                <p class="text-muted mb-3">Size en yakın kuaförleri gösterebilmemiz için konumunuzu paylaşın.</p>
            </div>
            <div class="d-flex justify-content-between">
                <button id="denyPermission" class="btn btn-outline-secondary px-4">Şimdi Değil</button>
                <button id="allowPermission" class="btn bg-pet-blue text-white px-4">İzin Ver</button>
            </div>
        </div>
    `;
    
    document.body.appendChild(modal);
    
    // İzin ver butonu - Chrome'un popup'ını göster
    document.getElementById('allowPermission').addEventListener('click', function() {
        modal.remove();
        
        // Chrome'un konum izni dialogunu göster
        navigator.geolocation.getCurrentPosition(
            function(position) {
                localStorage.setItem('locationPermissionGranted', 'true');
                
                const locationInput = document.getElementById('location');
                if (locationInput) {
                    findNearestCity(position.coords.latitude, position.coords.longitude, locationInput);
                }
            },
            function(error) {
                if (error.code === error.PERMISSION_DENIED) {
                    localStorage.setItem('locationPermissionGranted', 'false');
                    showLocationError("Konum izni reddedildi");
                }
            },
            { enableHighAccuracy: true, timeout: 10000, maximumAge: 0 }
        );
    });
    
    // İzni reddet butonu - popup'ı kapat ve izni reddet
    document.getElementById('denyPermission').addEventListener('click', function() {
        localStorage.setItem('locationPermissionGranted', 'false');
        modal.remove();
    });
}

// Konum hatası olduğunda metin kutusunda hata göster
function showLocationError(message) {
    const locationInput = document.getElementById('location');
    if (locationInput) {
        locationInput.value = "";
        locationInput.placeholder = message;
        locationInput.disabled = false;
        
        // 3 saniye sonra normal placeholder'a dön
        setTimeout(() => {
            locationInput.placeholder = "Şehir veya posta kodu giriniz";
        }, 3000);
    }
}

// Koordinatlardan en yakın şehri bul
function findNearestCity(latitude, longitude, locationInput) {
    // Türkiye'nin büyük şehirleri
    const turkishCities = [
        { name: "İstanbul", lat: 41.0082, lon: 28.9784 },
        { name: "Ankara", lat: 39.9334, lon: 32.8597 },
        { name: "İzmir", lat: 38.4237, lon: 27.1428 },
        { name: "Bursa", lat: 40.1885, lon: 29.0610 },
        { name: "Antalya", lat: 36.8969, lon: 30.7133 },
        { name: "Adana", lat: 37.0000, lon: 35.3213 },
        { name: "Konya", lat: 37.8667, lon: 32.4833 },
        { name: "Gaziantep", lat: 37.0662, lon: 37.3833 },
        { name: "Mersin", lat: 36.8000, lon: 34.6333 },
        { name: "Diyarbakır", lat: 37.9144, lon: 40.2306 },
        { name: "Kayseri", lat: 38.7312, lon: 35.4787 },
        { name: "Eskişehir", lat: 39.7767, lon: 30.5206 },
        { name: "Samsun", lat: 41.2867, lon: 36.3300 },
        { name: "Denizli", lat: 37.7736, lon: 29.0878 },
        { name: "Şanlıurfa", lat: 37.1591, lon: 38.7969 },
        { name: "Malatya", lat: 38.3552, lon: 38.3095 },
        { name: "Erzurum", lat: 39.9000, lon: 41.2700 },
        { name: "Trabzon", lat: 41.0050, lon: 39.7297 }
    ];
    
    // En yakın şehri bul
    let closestCity = null;
    let minDistance = Number.MAX_VALUE;
    
    for (const city of turkishCities) {
        const distance = haversineDistance(
            latitude, longitude, 
            city.lat, city.lon
        );
        
        if (distance < minDistance) {
            minDistance = distance;
            closestCity = city;
        }
    }
    
    // Sonucu göster
    if (closestCity && locationInput) {
        locationInput.value = closestCity.name;
        locationInput.disabled = false;
        
        // Konum seçildiğinde otomatik olarak formu gönder (opsiyonel)
        const form = locationInput.closest('form');
        if (form) {
            form.submit();
        }
    }
}

// İki konum arasındaki kilometreyi hesapla (Haversine formülü)
function haversineDistance(lat1, lon1, lat2, lon2) {
    const R = 6371; // Dünya yarıçapı (km)
    const dLat = toRad(lat2 - lat1);
    const dLon = toRad(lon2 - lon1);
    
    const a = 
        Math.sin(dLat / 2) * Math.sin(dLat / 2) +
        Math.cos(toRad(lat1)) * Math.cos(toRad(lat2)) * 
        Math.sin(dLon / 2) * Math.sin(dLon / 2);
    
    const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
    return R * c;
}

// Dereceyi radyana çevir
function toRad(degree) {
    return degree * Math.PI / 180;
}