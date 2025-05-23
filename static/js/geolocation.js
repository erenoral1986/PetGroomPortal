// Kullanıcının konumunu alıp, koordinatlardan en yakın şehri belirleyen fonksiyon
document.addEventListener('DOMContentLoaded', function() {
    // Global değişken oluştur - bu değişken her sayfada sadece bir popup gösterilmesini sağlayacak
    window.popupShownOnThisPage = false;
    
    // Test için localStorage sıfırlama düğmesi (geliştirme ve test için)
    const testButton = document.createElement('button');
    testButton.textContent = 'Test: Konum İznini Sıfırla';
    testButton.className = 'btn btn-sm btn-warning mt-2';
    testButton.style.position = 'fixed';
    testButton.style.bottom = '10px';
    testButton.style.right = '10px';
    testButton.style.opacity = '0.7';
    testButton.style.zIndex = '1000';
    
    testButton.addEventListener('click', function() {
        localStorage.removeItem('locationPermissionGranted');
        window.popupShownOnThisPage = false;
        alert('Konum izni sıfırlandı. Sayfayı yenileyin.');
        location.reload(); // Sayfayı yenile
    });
    
    document.body.appendChild(testButton);
    
    // Arama kutusu için konum butonu ekle
    const locationInput = document.getElementById('location');
    if (locationInput) {
        const locationInputParent = locationInput.parentElement;
        if (locationInputParent) {
            // Konum butonu ekle
            const locationButton = document.createElement('span');
            locationButton.className = 'input-group-text bg-light border-start-0 border-end-0 cursor-pointer';
            locationButton.innerHTML = '<i class="fas fa-crosshairs text-muted"></i>';
            locationButton.style.cursor = 'pointer';
            locationButton.title = 'Konumunuzu kullanın';
            
            // Konum butonunu input grubuna ekleyin
            const inputIcon = locationInputParent.querySelector('.input-group-text');
            if (inputIcon) {
                locationInputParent.insertBefore(locationButton, inputIcon.nextSibling);
            }
            
            // Konum butonuna tıklanınca konum izni iste
            locationButton.addEventListener('click', function() {
                // Popup gösterme değişkenini sıfırla
                window.popupShownOnThisPage = false;
                
                // İzin durumuna göre işlem yap
                const permissionStatus = localStorage.getItem('locationPermissionGranted');
                
                if (permissionStatus === 'true') {
                    // İzin verilmiş - konumu al
                    getGeolocation();
                } else if (permissionStatus === 'false') {
                    // İzin reddedilmiş - manuel seçim popup'ı göster
                    showRejectedPermissionPopup();
                } else {
                    // İzin hiç sorulmamış - izin iste
                    requestLocationPermission();
                }
            });
        }
    }
    
    // Sayfa yüklendiğinde konum iznini kontrol et
    checkPermissionOnPageLoad();
});

// Konum iznini kontrol et, izin verilmemişse iste (ve otomatik konumla)
function checkLocationPermission() {
    // Konum izni verilmiş mi kontrol et
    const permissionGranted = localStorage.getItem('locationPermissionGranted');
    
    // Eğer izin verilmişse, konumu al
    if (permissionGranted === 'true') {
        getGeolocation();
        return;
    }
    
    // Eğer izin hiç sorulmamışsa (null veya undefined), izin iste
    if (permissionGranted === null || permissionGranted === undefined) {
        showLocationPermissionPrompt();
        return;
    }
    
    // Eğer reddedilmişse ('false'), manuel seçim yapabileceklerini belirten mesaj göster
    if (permissionGranted === 'false') {
        showManualSelectionPrompt();
    }
}

// Sadece konum iznini kontrol et, popup göster ama konum alma
function checkPermissionStatusSilently() {
    // Konum izni verilmiş mi kontrol et
    const permissionGranted = localStorage.getItem('locationPermissionGranted');
    
    console.log("Konum izni durumu:", permissionGranted); // Debug için izin durumunu göster
    
    // İzin hiç sorulmamışsa, farklı bir mesaj göster (önce bunu kontrol etmeliyiz)
    if (permissionGranted === null || permissionGranted === undefined) {
        console.log("Konum izni hiç verilmemiş"); // Debug
        // Popup göster - hiç izin verilmemiş
        showNeverAskedPopup();
        return; // İşlemi sonlandır
    }
    
    // İzin reddedilmişse, manuel seçim yapabileceklerini belirten mesaj göster
    if (permissionGranted === 'false') {
        console.log("Konum izni reddedilmiş"); // Debug
        // Popup göster - konum izni reddedilmiş
        showNoPermissionPopup();
        return; // İşlemi sonlandır
    }
    
    // İzin verilmişse, hiçbir şey yapma (zaten otomatik konum alınmıyor)
    console.log("Konum izni var, popup gösterilmiyor");
}

// Hiç konum izni verilmemiş durumunda gösterilecek popup
function showNeverAskedPopup() {
    // Sayfa yüklendiğinde daha önce gösterilmiş mi?
    if (window.locationPromptShownThisPageLoad) {
        return;
    }
    
    // Bu sayfa yüklemesinde gösterildiğini işaretle
    window.locationPromptShownThisPageLoad = true;
    
    // Popup oluştur
    const permissionModal = document.createElement('div');
    permissionModal.className = 'position-fixed top-0 start-0 w-100 h-100 d-flex align-items-center justify-content-center';
    permissionModal.style.backgroundColor = 'rgba(0, 0, 0, 0.5)';
    permissionModal.style.zIndex = '9999';
    
    permissionModal.innerHTML = `
        <div class="bg-white p-4 rounded-3 shadow-lg" style="max-width: 400px;">
            <div class="text-center mb-3">
                <i class="fas fa-map-marker-alt fa-3x text-pet-blue mb-3"></i>
                <h5 class="fw-bold">Konum İzni Verilmemiş</h5>
                <p class="text-muted mb-3">Konum izni vermemişsiniz. Şehir adı girerek manuel olarak arama yapabilirsiniz veya konum izni vermeyi deneyebilirsiniz.</p>
            </div>
            <div class="d-flex justify-content-between">
                <button id="closeNeverAskedModal" class="btn btn-outline-secondary px-4">Tamam</button>
                <button id="givePermission" class="btn bg-pet-blue text-white px-4">İzin Ver</button>
            </div>
        </div>
    `;
    
    document.body.appendChild(permissionModal);
    
    // Tamam butonu - sadece kapat
    document.getElementById('closeNeverAskedModal').addEventListener('click', function() {
        permissionModal.remove();
    });
    
    // İzin Ver butonu - tarayıcının izin dialogunu göster
    document.getElementById('givePermission').addEventListener('click', function() {
        permissionModal.remove();
        
        // Tarayıcının konum izni sorgusunu göster (Chrome dialog)
        navigator.geolocation.getCurrentPosition(
            function(position) {
                // Başarılı olunca
                localStorage.setItem('locationPermissionGranted', 'true');
                const locationInput = document.getElementById('location');
                if (locationInput) {
                    // Konum alındı, en yakın şehri bul
                    findNearestCity(position.coords.latitude, position.coords.longitude, locationInput);
                }
            },
            function(error) {
                // Hata olursa veya reddedilirse
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
function showNoPermissionPopup() {
    // Sayfa yüklendiğinde daha önce gösterilmiş mi?
    if (window.locationPromptShownThisPageLoad) {
        return;
    }
    
    // Bu sayfa yüklemesinde gösterildiğini işaretle
    window.locationPromptShownThisPageLoad = true;
    
    // Popup oluştur - manuel seçim popup'ı aynı işi görüyor, onu kullan
    showManualSelectionPrompt();
}

// Doğrudan tarayıcıdan konum izni al ve konumu al
function getGeolocation() {
    const locationInput = document.getElementById('location');
    if (!locationInput) return;
    
    // Konum alınıyor mesajı göster
    locationInput.value = "Konum alınıyor...";
    locationInput.disabled = true;
    
    // Konumu al
    navigator.geolocation.getCurrentPosition(
        // Başarılı olunca
        function(position) {
            const latitude = position.coords.latitude;
            const longitude = position.coords.longitude;
            
            // Koordinatları yakın şehre çevir 
            findNearestCity(latitude, longitude, locationInput);
        },
        // Hata olunca
        function(error) {
            locationInput.disabled = false;
            
            switch(error.code) {
                case error.PERMISSION_DENIED:
                    showLocationError("Konum izni reddedildi");
                    localStorage.setItem('locationPermissionGranted', 'false');
                    break;
                case error.POSITION_UNAVAILABLE:
                    showLocationError("Konum bilgisi mevcut değil");
                    break;
                case error.TIMEOUT:
                    showLocationError("İstek zaman aşımına uğradı");
                    break;
                case error.UNKNOWN_ERROR:
                    showLocationError("Bilinmeyen bir hata oluştu");
                    break;
            }
        },
        // Geolocation Ayarları
        {
            enableHighAccuracy: true,
            timeout: 10000,
            maximumAge: 0
        }
    );
}

// Konum izni isteme mesajını göster
function showLocationPermissionPrompt() {
    // Sadece bu sayfa görüntülemesinde daha önce gösterilmiş mi kontrolü
    if (window.locationPromptShownThisPageLoad) {
        return;
    }
    
    // Bu sayfa yüklemesinde gösterildiğini işaretle (sayfayı yenilememize kadar geçerli)
    window.locationPromptShownThisPageLoad = true;
    
    // Konum izni isteme modal
    const permissionModal = document.createElement('div');
    permissionModal.className = 'position-fixed top-0 start-0 w-100 h-100 d-flex align-items-center justify-content-center';
    permissionModal.style.backgroundColor = 'rgba(0, 0, 0, 0.5)';
    permissionModal.style.zIndex = '9999';
    
    permissionModal.innerHTML = `
        <div class="bg-white p-4 rounded-3 shadow-lg" style="max-width: 400px;">
            <div class="text-center mb-3">
                <i class="fas fa-map-marker-alt fa-3x text-pet-blue mb-3"></i>
                <h5 class="fw-bold">Konumunuzu kullanmamıza izin verin</h5>
                <p class="text-muted mb-3">Size en yakın kuaförleri gösterebilmemiz için konumunuzu paylaşın.</p>
            </div>
            <div class="d-flex justify-content-between">
                <button id="denyLocation" class="btn btn-outline-secondary px-4">Şimdi Değil</button>
                <button id="allowLocation" class="btn bg-pet-blue text-white px-4">İzin Ver</button>
            </div>
        </div>
    `;
    
    document.body.appendChild(permissionModal);
    
    // İzin Ver butonu - Chrome'un izin dialogunu doğrudan göster
    document.getElementById('allowLocation').addEventListener('click', function() {
        permissionModal.remove(); // Önce bizim modal'ı kapat
        
        // Direkt olarak tarayıcının konum izni sorgusunu göster (Chrome dialog)
        navigator.geolocation.getCurrentPosition(
            // Başarılı olunca
            function(position) {
                // Konum izni verildi ve konum alındı
                localStorage.setItem('locationPermissionGranted', 'true');
                
                // Konum bilgisini kullan
                const locationInput = document.getElementById('location');
                if (locationInput) {
                    // Koordinatları yakın şehre çevir 
                    findNearestCity(position.coords.latitude, position.coords.longitude, locationInput);
                }
            },
            // Hata olunca
            function(error) {
                // Hata koduna göre işlem yap
                switch(error.code) {
                    case error.PERMISSION_DENIED:
                        localStorage.setItem('locationPermissionGranted', 'false');
                        showLocationError("Konum izni reddedildi");
                        break;
                    case error.POSITION_UNAVAILABLE:
                        showLocationError("Konum bilgisi mevcut değil");
                        break;
                    case error.TIMEOUT:
                        showLocationError("İstek zaman aşımına uğradı");
                        break;
                    case error.UNKNOWN_ERROR:
                        showLocationError("Bilinmeyen bir hata oluştu");
                        break;
                }
            },
            // Geolocation Ayarları
            {
                enableHighAccuracy: true,
                timeout: 10000,
                maximumAge: 0
            }
        );
    });
    
    // İzin verilmezse modal kapat
    document.getElementById('denyLocation').addEventListener('click', function() {
        localStorage.setItem('locationPermissionGranted', 'false');
        permissionModal.remove();
    });
}

// Manuel seçim yapabileceklerini bildiren popup'ı göster
function showManualSelectionPrompt() {
    // Sadece bu sayfa görüntülemesinde daha önce gösterilmiş mi kontrolü
    if (window.locationPromptShownThisPageLoad) {
        return;
    }
    
    // Bu sayfa yüklemesinde gösterildiğini işaretle
    window.locationPromptShownThisPageLoad = true;
    
    // Popup oluştur
    const permissionModal = document.createElement('div');
    permissionModal.className = 'position-fixed top-0 start-0 w-100 h-100 d-flex align-items-center justify-content-center';
    permissionModal.style.backgroundColor = 'rgba(0, 0, 0, 0.5)';
    permissionModal.style.zIndex = '9999';
    
    permissionModal.innerHTML = `
        <div class="bg-white p-4 rounded-3 shadow-lg" style="max-width: 400px;">
            <div class="text-center mb-3">
                <i class="fas fa-map-marker-alt fa-3x text-pet-blue mb-3"></i>
                <h5 class="fw-bold">Konum İzni Reddedildi</h5>
                <p class="text-muted mb-3">Konum iznini reddetmişsiniz. Şehir adı girerek manuel olarak arama yapabilirsiniz veya tekrar izin vermeyi deneyebilirsiniz.</p>
            </div>
            <div class="d-flex justify-content-between">
                <button id="closeManualModal" class="btn btn-outline-secondary px-4">Tamam</button>
                <button id="retryPermission" class="btn bg-pet-blue text-white px-4">İzin Ver</button>
            </div>
        </div>
    `;
    
    document.body.appendChild(permissionModal);
    
    // Tamam butonu - sadece kapat
    document.getElementById('closeManualModal').addEventListener('click', function() {
        permissionModal.remove();
    });
    
    // İzin Ver butonu - localStorage temizle ve Chrome'un izin dialogunu doğrudan göster
    document.getElementById('retryPermission').addEventListener('click', function() {
        localStorage.removeItem('locationPermissionGranted'); // İzin bilgisini sıfırla
        permissionModal.remove();
        window.locationPromptShownThisPageLoad = false; // Popup gösterimini sıfırla
        
        // Direkt olarak tarayıcının konum izni sorgusunu göster (Chrome dialog)
        navigator.geolocation.getCurrentPosition(
            function(position) {
                // Başarılı olunca
                localStorage.setItem('locationPermissionGranted', 'true');
                const locationInput = document.getElementById('location');
                if (locationInput) {
                    // Konum alındı, en yakın şehri bul
                    findNearestCity(position.coords.latitude, position.coords.longitude, locationInput);
                }
            },
            function(error) {
                // Hata olursa veya reddedilirse
                if (error.code === error.PERMISSION_DENIED) {
                    localStorage.setItem('locationPermissionGranted', 'false');
                    showLocationError("Konum izni reddedildi");
                }
            },
            { enableHighAccuracy: true, timeout: 10000, maximumAge: 0 }
        );
    });
}

// Kullanıcı konumunu al
function getUserLocation() {
    const locationInput = document.getElementById('location');
    if (!locationInput) return;
    
    // Kullanıcının tarayıcısı geolocation destekliyor mu?
    if (navigator.geolocation) {
        // Konum izni verilmiş mi kontrol et
        const permissionGranted = localStorage.getItem('locationPermissionGranted');
        
        if (permissionGranted === 'true') {
            // İzin verilmişse doğrudan konumu al
            getGeolocation();
        } else if (permissionGranted === 'false') {
            // İzin reddedilmişse, manuel seçim popup'ını göster
            showManualSelectionPrompt();
        } else {
            // İzin daha önce sorulmamışsa, izin iste
            showLocationPermissionPrompt();
        }
    } else {
        showLocationError("Tarayıcınız konum bilgisini desteklemiyor");
    }
}

// Konum alınırken hata oluşursa mesaj göster
function showLocationError(message) {
    const locationInput = document.getElementById('location');
    if (locationInput) {
        locationInput.value = "";
        locationInput.placeholder = message;
        
        // 3 saniye sonra normal placeholder'a dön
        setTimeout(() => {
            locationInput.placeholder = "Şehir veya posta kodu giriniz";
        }, 3000);
    }
}

// Başarı mesajı göster
function showLocationSuccess(message) {
    // Popup bildirim (modal) için element oluştur
    const permissionModal = document.createElement('div');
    permissionModal.className = 'position-fixed top-0 start-0 w-100 h-100 d-flex align-items-center justify-content-center';
    permissionModal.style.backgroundColor = 'rgba(0, 0, 0, 0.5)';
    permissionModal.style.zIndex = '9999';
    
    permissionModal.innerHTML = `
        <div class="bg-white p-4 rounded-3 shadow-lg" style="max-width: 400px;">
            <div class="text-center mb-3">
                <i class="fas fa-map-marker-alt fa-3x text-pet-blue mb-3"></i>
                <h5 class="fw-bold">Konum Bilgisi</h5>
                <p class="text-muted mb-3">${message}</p>
            </div>
            <div class="d-flex justify-content-center">
                <button id="closeSuccessModal" class="btn bg-pet-blue text-white px-4">Tamam</button>
            </div>
        </div>
    `;
    
    document.body.appendChild(permissionModal);
    
    // "Tamam" butonuna tıklayınca modal kapat
    document.getElementById('closeSuccessModal').addEventListener('click', function() {
        permissionModal.remove();
    });
    
    // 3 saniye sonra popup'ı otomatik kaldır
    setTimeout(() => {
        permissionModal.remove();
    }, 3000);
}

// Koordinatlardan en yakın şehri bul (basit algoritma)
function findNearestCity(latitude, longitude, locationInput) {
    // Türkiye'nin büyük şehirleri ve koordinatları
    const turkishCities = [
        { name: "İstanbul", lat: 41.0082, lon: 28.9784 },
        { name: "Ankara", lat: 39.9334, lon: 32.8597 },
        { name: "İzmir", lat: 38.4237, lon: 27.1428 },
        { name: "Bursa", lat: 40.1885, lon: 29.0610 },
        { name: "Antalya", lat: 36.8969, lon: 30.7133 },
        { name: "Adana", lat: 37.0000, lon: 35.3213 },
        { name: "Konya", lat: 37.8664, lon: 32.4857 },
        { name: "Gaziantep", lat: 37.0662, lon: 37.3833 },
        { name: "Mersin", lat: 36.8000, lon: 34.6333 },
        { name: "Diyarbakır", lat: 37.9144, lon: 40.2306 },
        { name: "Kayseri", lat: 38.7312, lon: 35.4787 },
        { name: "Eskişehir", lat: 39.7767, lon: 30.5206 },
        { name: "Samsun", lat: 41.2867, lon: 36.3300 },
        { name: "Denizli", lat: 37.7765, lon: 29.0864 },
        { name: "Şanlıurfa", lat: 37.1674, lon: 38.7955 },
        { name: "Malatya", lat: 38.3552, lon: 38.3095 },
        { name: "Trabzon", lat: 41.0015, lon: 39.7178 },
        { name: "Erzurum", lat: 39.9000, lon: 41.2700 },
        { name: "Van", lat: 38.4891, lon: 43.4089 },
        { name: "Manisa", lat: 38.6191, lon: 27.4289 },
        { name: "Kocaeli", lat: 40.7655, lon: 29.9408 },
        { name: "Adıyaman", lat: 37.7648, lon: 38.2786 },
        { name: "Ağrı", lat: 39.7191, lon: 43.0503 },
        { name: "Amasya", lat: 40.6499, lon: 35.8353 },
        { name: "Artvin", lat: 41.1828, lon: 41.8183 },
        { name: "Aydın", lat: 37.8560, lon: 27.8416 },
        { name: "Balıkesir", lat: 39.6484, lon: 27.8826 },
        { name: "Bilecik", lat: 40.1431, lon: 29.9792 },
        { name: "Bingöl", lat: 39.0626, lon: 40.7696 },
        { name: "Bitlis", lat: 38.4007, lon: 42.1095 },
        { name: "Bolu", lat: 40.7392, lon: 31.6089 },
        { name: "Burdur", lat: 37.7205, lon: 30.2900 },
        { name: "Çanakkale", lat: 40.1553, lon: 26.4142 },
        { name: "Çankırı", lat: 40.6013, lon: 33.6134 },
        { name: "Çorum", lat: 40.5489, lon: 34.9533 },
        { name: "Elazığ", lat: 38.6810, lon: 39.2264 },
        { name: "Edirne", lat: 41.6818, lon: 26.5623 },
        { name: "Erzincan", lat: 39.7500, lon: 39.5000 },
        { name: "Giresun", lat: 40.9128, lon: 38.3895 },
        { name: "Gümüşhane", lat: 40.4386, lon: 39.5086 },
        { name: "Hakkari", lat: 37.5833, lon: 43.7667 },
        { name: "Hatay", lat: 36.2000, lon: 36.1667 },
        { name: "Isparta", lat: 37.7648, lon: 30.5566 },
        { name: "Kars", lat: 40.6167, lon: 43.1000 },
        { name: "Kastamonu", lat: 41.3887, lon: 33.7827 },
        { name: "Kırklareli", lat: 41.7333, lon: 27.2167 },
        { name: "Kırşehir", lat: 39.1425, lon: 34.1709 },
        { name: "Kütahya", lat: 39.4167, lon: 29.9833 },
        { name: "Muğla", lat: 37.2167, lon: 28.3667 },
        { name: "Muş", lat: 38.7432, lon: 41.5065 },
        { name: "Nevşehir", lat: 38.6939, lon: 34.6857 },
        { name: "Niğde", lat: 37.9667, lon: 34.6833 },
        { name: "Ordu", lat: 40.9833, lon: 37.8833 },
        { name: "Rize", lat: 41.0201, lon: 40.5234 },
        { name: "Sakarya", lat: 40.7731, lon: 30.3946 },
        { name: "Siirt", lat: 37.9333, lon: 41.9500 },
        { name: "Sinop", lat: 42.0231, lon: 35.1531 },
        { name: "Sivas", lat: 39.7477, lon: 37.0179 },
        { name: "Tekirdağ", lat: 40.9833, lon: 27.5167 },
        { name: "Tokat", lat: 40.3167, lon: 36.5500 },
        { name: "Tunceli", lat: 39.1079, lon: 39.5401 },
        { name: "Uşak", lat: 38.6823, lon: 29.4082 },
        { name: "Yozgat", lat: 39.8181, lon: 34.8147 },
        { name: "Zonguldak", lat: 41.4564, lon: 31.7987 },
        { name: "Aksaray", lat: 38.3687, lon: 34.0370 },
        { name: "Bayburt", lat: 40.2552, lon: 40.2249 },
        { name: "Karaman", lat: 37.1759, lon: 33.2287 },
        { name: "Kırıkkale", lat: 39.8468, lon: 33.5153 },
        { name: "Batman", lat: 37.8812, lon: 41.1351 },
        { name: "Şırnak", lat: 37.5164, lon: 42.4611 },
        { name: "Bartın", lat: 41.6345, lon: 32.3375 },
        { name: "Ardahan", lat: 41.1105, lon: 42.7022 },
        { name: "Iğdır", lat: 39.9167, lon: 44.0333 },
        { name: "Yalova", lat: 40.6500, lon: 29.2667 },
        { name: "Karabük", lat: 41.2061, lon: 32.6204 },
        { name: "Kilis", lat: 36.7184, lon: 37.1212 },
        { name: "Osmaniye", lat: 37.0748, lon: 36.2465 },
        { name: "Düzce", lat: 40.8438, lon: 31.1565 }
    ];
    
    // En yakın şehri bul
    let nearestCity = null;
    let minDistance = Infinity;
    
    for (const city of turkishCities) {
        // İki nokta arasındaki mesafeyi hesapla (Haversine formülü)
        const distance = haversineDistance(latitude, longitude, city.lat, city.lon);
        
        if (distance < minDistance) {
            minDistance = distance;
            nearestCity = city.name;
        }
    }
    
    // En yakın şehri input kutusuna yaz
    if (nearestCity && locationInput) {
        locationInput.value = nearestCity;
        locationInput.disabled = false;
        
        // Şehir seçildiğinde dropdown listesini gizle
        const cityList = document.getElementById('cityList');
        if (cityList) {
            cityList.style.display = 'none';
            cityList.classList.add('hidden');
        }
        
        // İstanbul, Ankara ve İzmir için özel mahalle verileri
        const istanbulNeighborhoods = [
            { name: 'Caferağa', lat: 40.9894, lon: 29.0342 },
            { name: 'Fenerbahçe', lat: 40.9703, lon: 29.0361 },
            { name: 'Koşuyolu', lat: 41.0128, lon: 29.0339 },
            { name: 'Abbasağa', lat: 41.0422, lon: 29.0097 },
            { name: 'Bebek', lat: 41.0770, lon: 29.0418 },
            { name: 'Etiler', lat: 41.0811, lon: 29.0333 },
            { name: 'Levent', lat: 41.0825, lon: 29.0178 },
            { name: 'Cihangir', lat: 41.0317, lon: 28.9833 },
            { name: 'Galata', lat: 41.0256, lon: 28.9742 },
            { name: 'Taksim', lat: 41.0370, lon: 28.9850 },
            { name: 'Mecidiyeköy', lat: 41.0667, lon: 28.9956 },
            { name: 'Erenköy', lat: 40.9717, lon: 29.0636 },
            { name: 'Suadiye', lat: 40.9572, lon: 29.0681 },
            { name: 'Bağcılar', lat: 41.0384, lon: 28.8558 },
            { name: 'Bakırköy', lat: 40.9808, lon: 28.8772 },
            { name: 'Fatih', lat: 41.0186, lon: 28.9394 }
        ];
        
        const ankaraNeighborhoods = [
            { name: 'Kızılay', lat: 39.9208, lon: 32.8541 },
            { name: 'Çukurambar', lat: 39.9114, lon: 32.8119 },
            { name: 'Bahçelievler', lat: 39.9217, lon: 32.8158 },
            { name: 'Ümitköy', lat: 39.9047, lon: 32.6981 },
            { name: 'Çayyolu', lat: 39.8894, lon: 32.6589 },
            { name: 'Batıkent', lat: 39.9692, lon: 32.7306 }
        ];
        
        const izmirNeighborhoods = [
            { name: 'Alsancak', lat: 38.4370, lon: 27.1428 },
            { name: 'Karşıyaka', lat: 38.4602, lon: 27.1100 },
            { name: 'Bornova', lat: 38.4697, lon: 27.2137 },
            { name: 'Göztepe', lat: 38.3922, lon: 27.0808 },
            { name: 'Bostanlı', lat: 38.4464, lon: 27.0983 }
        ];
        
        // Şehir için mahalleleri yükle ve en yakın mahalleyi seç
        console.log("Şehir için mahalleler yükleniyor:", nearestCity);
        updateDistrictsByCity(nearestCity).then(() => {
            // Şehre bağlı olarak uygun mahalle datasını seç
            let neighborhoodData = [];
            if (nearestCity === 'İstanbul') {
                neighborhoodData = istanbulNeighborhoods;
            } else if (nearestCity === 'Ankara') {
                neighborhoodData = ankaraNeighborhoods;
            } else if (nearestCity === 'İzmir') {
                neighborhoodData = izmirNeighborhoods;
            }
            
            // Eğer bu şehir için mahalle verisi varsa, en yakın mahalleyi bul
            if (neighborhoodData.length > 0) {
                let closestNeighborhood = null;
                let minNeighborhoodDistance = Infinity;
                
                for (const nh of neighborhoodData) {
                    const distance = haversineDistance(latitude, longitude, nh.lat, nh.lon);
                    if (distance < minNeighborhoodDistance) {
                        minNeighborhoodDistance = distance;
                        closestNeighborhood = nh.name;
                    }
                }
                
                console.log("En yakın mahalle bulundu:", closestNeighborhood);
                
                // En yakın mahalleyi dropdown'da seç
                if (closestNeighborhood) {
                    setTimeout(() => {
                        const districtSelect = document.getElementById('district');
                        if (districtSelect) {
                            // Dropdown'da mahalleyi bul
                            for (let i = 0; i < districtSelect.options.length; i++) {
                                // Tam adı veya adın bir parçasını içeriyor mu kontrol et
                                if (districtSelect.options[i].text.includes(closestNeighborhood)) {
                                    districtSelect.selectedIndex = i;
                                    console.log("Mahalle dropdown'da seçildi:", districtSelect.options[i].text);
                                    break;
                                }
                            }
                        }
                        
                        // Form'u submit et
                        submitSearchForm();
                    }, 1000); // Mahallelerin yüklenmesi için 1 saniye bekle
                } else {
                    // Mahalle bulunamadıysa direk formu gönder
                    submitSearchForm();
                }
            } else {
                // Eğer şehir için mahalle verisi yoksa, direk formu gönder
                submitSearchForm();
            }
        }).catch(error => {
            console.error("Mahalle yüklerken hata:", error);
            // Hata durumunda sadece form gönder
            submitSearchForm();
        });
        
        // Form submit fonksiyonu
        function submitSearchForm() {
            const searchForm = locationInput.closest('form');
            if (searchForm) {
                console.log("Form otomatik olarak gönderiliyor...");
                // Form submit butonunu bul ve tıkla
                const submitButton = searchForm.querySelector('button[type="submit"]');
                if (submitButton) {
                    submitButton.click();
                } else {
                    // Buton yoksa direkt form'u submit et
                    searchForm.submit();
                }
            }
        }
    }
}

// İki koordinat arasındaki mesafeyi hesaplayan fonksiyon (km cinsinden)
function haversineDistance(lat1, lon1, lat2, lon2) {
    // Dünya yarıçapı (km)
    const R = 6371;
    
    // Radyana çevirme
    const dLat = toRad(lat2 - lat1);
    const dLon = toRad(lon2 - lon1);
    
    const a = 
        Math.sin(dLat/2) * Math.sin(dLat/2) +
        Math.cos(toRad(lat1)) * Math.cos(toRad(lat2)) * 
        Math.sin(dLon/2) * Math.sin(dLon/2);
    
    const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a));
    const distance = R * c;
    
    return distance;
}

// Dereceyi radyana çeviren yardımcı fonksiyon
function toRad(degree) {
    return degree * (Math.PI / 180);
}