// Global değişken tanımlaması
window.popupShownOnThisPage = false;

// Geliştirici için: Konsola yazarak konum izni sıfırlama (Canlı sitede gizli, sadece geliştirme için)
// Kullanım: console.js dosyasını açıp console.log(resetLocationPermission()) yazarak çalıştırılabilir
function resetLocationPermission() {
    localStorage.removeItem('locationPermissionGranted');
    localStorage.removeItem('userLatitude');
    localStorage.removeItem('userLongitude');
    localStorage.removeItem('userCity');
    localStorage.removeItem('userNeighborhood');
    console.log("Konum izni durumu sıfırlandı. Sayfayı yenileyin.");
    return "Konum izni sıfırlandı!";
}

// Sayfa yüklendiğinde konum izni kontrolünü yap - sadece anasayfada çalışacak
document.addEventListener('DOMContentLoaded', function() {
    // Sadece anasayfada konum izni iste
    // URL kontrol edilerek sadece ana sayfada çalışması sağlanıyor
    const isHomePage = window.location.pathname === '/' || 
                        window.location.pathname === '/index' || 
                        window.location.pathname === '/index.html';
    
    if (isHomePage) {
        console.log("Anasayfadayız, konum izni kontrolü yapılacak...");
        checkPermissionOnPageLoad();
    } else {
        console.log("Anasayfa dışındayız, konum izni kontrolü yapılmayacak.");
    }
    
    // Her 60 saniyede bir konumu güncelleme işlevi
    setInterval(function() {
        // Eğer konum izni verilmişse
        if (localStorage.getItem('locationPermissionGranted') === 'true') {
            // Kaydedilmiş konum varsa gerçek konum bilgisini güncelle
            if (localStorage.getItem('userLatitude') && localStorage.getItem('userLongitude')) {
                console.log("Konum bilgisi düzenli olarak güncelleniyor...");
                navigator.geolocation.getCurrentPosition(
                    function(position) {
                        // Yeni konum bilgilerini al
                        const newLatitude = position.coords.latitude;
                        const newLongitude = position.coords.longitude;
                        
                        // Eski konum ile yeni konum arasında fark var mı?
                        const oldLatitude = parseFloat(localStorage.getItem('userLatitude'));
                        const oldLongitude = parseFloat(localStorage.getItem('userLongitude'));
                        
                        const distance = haversineDistance(oldLatitude, oldLongitude, newLatitude, newLongitude);
                        
                        // Eğer konum belirgin şekilde değiştiyse (100 metre veya daha fazla)
                        if (distance > 0.1) {
                            console.log("Konum değişikliği tespit edildi:", distance.toFixed(2) + " km");
                            
                            // Konumu güncelle
                            localStorage.setItem('userLatitude', newLatitude);
                            localStorage.setItem('userLongitude', newLongitude);
                            
                            // Gerçek adres bilgisini güncelle
                            fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${newLatitude}&lon=${newLongitude}&zoom=18&addressdetails=1`)
                            .then(response => response.json())
                            .then(data => {
                                if (data.address) {
                                    let realNeighborhood = null;
                                    let realCity = null;
                                    
                                    // Şehir bilgisini al
                                    if (data.address.city) {
                                        realCity = data.address.city;
                                    } else if (data.address.state) {
                                        realCity = data.address.state;
                                    }
                                    
                                    // Önce neighbourhood bilgisini al (en doğru mahalle bilgisi)
                                    if (data.address.neighbourhood) {
                                        realNeighborhood = data.address.neighbourhood;
                                    } 
                                    // Yoksa suburb bilgisini al
                                    else if (data.address.suburb) {
                                        realNeighborhood = data.address.suburb;
                                    }
                                    // Yoksa city_district bilgisini al
                                    else if (data.address.city_district) {
                                        realNeighborhood = data.address.city_district;
                                    }
                                    // Yoksa quarter bilgisini al
                                    else if (data.address.quarter) {
                                        realNeighborhood = data.address.quarter;
                                    }
                                    
                                    // Adres bilgisini localStorage'a kaydet (sayfa yenilenmesi için)
                                    if (realCity) {
                                        localStorage.setItem('userCity', realCity);
                                    }
                                    if (realNeighborhood) {
                                        localStorage.setItem('userNeighborhood', realNeighborhood);
                                    }
                                    
                                    // Eğer şehir ve mahalle bilgisi bulunmuşsa...
                                    if (realCity && realNeighborhood) {
                                        console.log("Gerçek konum bilgileri:", realCity, realNeighborhood);
                                        
                                        // Şehir seçimini güncelle
                                        const locationInput = document.getElementById('location');
                                        if (locationInput && locationInput.value !== realCity) {
                                            locationInput.value = realCity;
                                            
                                            // Şehir için mahalleleri yükle
                                            if (typeof updateDistrictsByCity === 'function') {
                                                console.log("Şehire göre mahalleleri yüklüyorum:", realCity);
                                                updateDistrictsByCity(realCity).then(() => {
                                                    // Mahalleler yüklendikten sonra gerçek mahalleyi seç
                                                    selectRealNeighborhood(realNeighborhood);
                                                });
                                            }
                                        } else {
                                            // Sadece mahalle seçimini güncelle (şehir zaten doğru)
                                            selectRealNeighborhood(realNeighborhood);
                                        }
                                    }
                                }
                            }).catch(error => {
                                console.error('Adres güncellemesi sırasında hata:', error);
                            });
                        }
                    },
                    function(error) {
                        console.error('Konum güncellemesi sırasında hata:', error);
                    },
                    { enableHighAccuracy: true, timeout: 10000, maximumAge: 0 }
                );
            }
        }
    }, 60000); // 60 saniyede bir güncelle (60.000 milisaniye)
});

// Sayfa yüklendiğinde konum izni durumunu kontrol et
function checkPermissionOnPageLoad() {
    console.log("Sayfa yüklendiğinde konum izni kontrolü yapılıyor...");
    
    const permissionState = localStorage.getItem('locationPermissionGranted');
    console.log("Konum izni durumu:", permissionState);
    
    if (permissionState === 'true') {
        console.log("Konum izni verilmiş, konumu alıyorum");
        getGeolocation();
    } else if (permissionState === 'false') {
        console.log("Konum izni reddedilmiş, popup gösteriliyor");
        showRejectedPermissionPopup();
    } else {
        console.log("Konum izni hiç sorulmamış, popup gösteriliyor");
        showNeverAskedPopup();
    }
}

// Anasayfada Konum Butonunu ekle
function addLocationButton() {
    const locationButton = document.getElementById('locationButton');
    if (locationButton) {
        locationButton.addEventListener('click', getGeolocation);
    }
}

// Konum bilgisini al
function getGeolocation() {
    // Konum inputu var mı kontrol et
    const locationInput = document.getElementById('location');
    if (!locationInput) return;
    
    // Input'u devre dışı bırak
    locationInput.disabled = true;
    
    // Konum alındı olarak işaretle
    localStorage.setItem('locationPermissionGranted', 'true');
    
    // Tarayıcıdan konum iste
    navigator.geolocation.getCurrentPosition(
        // Başarılı olursa
        function(position) {
            // Tarayıcıdan gelen gerçek konum bilgisini kullan
            const latitude = position.coords.latitude;
            const longitude = position.coords.longitude;
            
            // Test için konum bilgilerini ekrana yazdır
            showLocationDebugInfo(latitude, longitude);
            
            // Konum bilgisini localStorage'a kaydet
            localStorage.setItem('userLatitude', latitude);
            localStorage.setItem('userLongitude', longitude);
            
            // Gerçek adres bilgilerini al ve en yakın şehri bul
            console.log("Gerçek adres bilgisi alınıyor...");
            
            // Önce Nominatim API ile gerçek adres bilgisini almayı dene
            fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${latitude}&lon=${longitude}&zoom=18&addressdetails=1`)
            .then(response => response.json())
            .then(data => {
                console.log("Adres verileri alındı:", data);
                
                if (data.address) {
                    let realCity = null;
                    let realNeighborhood = null;
                    
                    // Şehir bilgisini al
                    if (data.address.city) {
                        realCity = data.address.city;
                    } else if (data.address.state) {
                        realCity = data.address.state;
                    }
                    
                    // Mahalle bilgisini al
                    if (data.address.neighbourhood) {
                        realNeighborhood = data.address.neighbourhood;
                    } else if (data.address.suburb) {
                        realNeighborhood = data.address.suburb;
                    } else if (data.address.quarter) {
                        realNeighborhood = data.address.quarter;
                    } else if (data.address.city_district) {
                        realNeighborhood = data.address.city_district;
                    }
                    
                    // Adres bilgisini localStorage'a kaydet
                    if (realCity) {
                        localStorage.setItem('userCity', realCity);
                        console.log("Gerçek şehir tespit edildi:", realCity);
                        
                        if (locationInput) {
                            locationInput.value = realCity;
                            locationInput.disabled = false;
                            
                            // Şehre göre mahalle bilgisini yükle
                            if (typeof updateDistrictsByCity === 'function') {
                                console.log("Şehre göre mahalleleri yüklüyorum:", realCity);
                                updateDistrictsByCity(realCity).then(() => {
                                    // Mahalle bilgisi varsa seç
                                    if (realNeighborhood) {
                                        localStorage.setItem('userNeighborhood', realNeighborhood);
                                        selectRealNeighborhood(realNeighborhood);
                                    }
                                    
                                    // Form otomatik olarak gönderilsin
                                    console.log("Form otomatik olarak gönderiliyor...");
                                    submitForm();
                                });
                            }
                        }
                    } else {
                        // Gerçek şehir bulunamadıysa en yakınını bul
                        console.log("Gerçek şehir bilgisi bulunamadı, en yakın şehri buluyorum...");
                        findNearestCity(latitude, longitude, locationInput);
                    }
                } else {
                    // Gerçek adres bilgisi alınamazsa en yakın şehri bul
                    console.log("Adres bilgisi alınamadı, en yakın şehri buluyorum...");
                    findNearestCity(latitude, longitude, locationInput);
                }
            })
            .catch(error => {
                console.error("Adres bilgisi alınırken hata oluştu:", error);
                findNearestCity(latitude, longitude, locationInput);
            });
            
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
    modal.style.backgroundColor = 'rgba(0, 0, 0, 0.7)'; // Daha koyu arkaplan
    modal.style.zIndex = '9999';
    
    modal.innerHTML = `
        <div class="bg-white p-5 rounded-3 shadow-lg" style="max-width: 450px; border: 2px solid #00bed7;">
            <div class="text-center mb-4">
                <i class="fas fa-map-marker-alt fa-4x text-pet-blue mb-3" style="color: #00bed7 !important;"></i>
                <h4 class="fw-bold mb-3" style="color: #0096ab;">Konum İzni Gerekli</h4>
                <p class="mb-4" style="font-size: 16px; line-height: 1.5;">Konum izni vermeniz lazım. Şehir adı girerek manuel olarak arama yapabilirsiniz veya konum izni vermeyi deneyebilirsiniz.</p>
            </div>
            <div class="d-flex justify-content-between">
                <button id="closeNeverAskedPopup" class="btn btn-outline-secondary px-4 py-2" style="font-weight: 500; min-width: 120px;">Tamam</button>
                <button id="givePermissionNeverAsked" class="btn text-white px-4 py-2" style="background-color: #00bed7; font-weight: 500; min-width: 120px; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">İzin Ver</button>
            </div>
        </div>
    `;
    
    document.body.appendChild(modal);
    
    // Tamam butonu - kapat
    document.getElementById('closeNeverAskedPopup').addEventListener('click', function() {
        modal.remove();
        
        // Eğer şehir seçildiyse o şehrin mahallelerini yükle
        const locationInput = document.getElementById('location');
        if (locationInput && locationInput.value.trim()) {
            const city = locationInput.value.trim();
            console.log("Konum izni verilmedi, manuel şehir için mahalleler yükleniyor: " + city);
            updateDistrictsByCity(city);
        }
    });
    
    // İzin ver butonu - Chrome popup'ını göster
    document.getElementById('givePermissionNeverAsked').addEventListener('click', function() {
        modal.remove();
        
        // Chrome'un konum izni dialogunu göster
        navigator.geolocation.getCurrentPosition(
            // Başarılı olursa
            function(position) {
                localStorage.setItem('locationPermissionGranted', 'true');
                
                // Tarayıcıdan gelen gerçek konum bilgisini kullan
                const latitude = position.coords.latitude;
                const longitude = position.coords.longitude;
                
                const locationInput = document.getElementById('location');
                if (locationInput) {
                    findNearestCity(latitude, longitude, locationInput);
                }
            },
            // Hata olursa
            function(error) {
                if (error.code === error.PERMISSION_DENIED) {
                    localStorage.setItem('locationPermissionGranted', 'false');
                    showLocationError("Konum izni reddedildi");
                    
                    // Eğer şehir seçildiyse o şehrin mahallelerini yükle
                    const locationInput = document.getElementById('location');
                    if (locationInput && locationInput.value.trim()) {
                        const city = locationInput.value.trim();
                        console.log("Konum izni reddedildi, manuel şehir için mahalleler yükleniyor: " + city);
                        updateDistrictsByCity(city);
                    }
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
    
    console.log("Konum izni reddedildi popup oluşturuluyor...");
    
    // Popup oluştur
    const modal = document.createElement('div');
    modal.className = 'position-fixed top-0 start-0 w-100 h-100 d-flex align-items-center justify-content-center';
    modal.style.backgroundColor = 'rgba(0, 0, 0, 0.7)'; // Daha koyu arkaplan
    modal.style.zIndex = '9999';
    
    modal.innerHTML = `
        <div class="bg-white p-5 rounded-3 shadow-lg" style="max-width: 450px; border: 2px solid #00bed7;">
            <div class="text-center mb-4">
                <i class="fas fa-map-marker-alt fa-4x text-pet-blue mb-3" style="color: #00bed7 !important;"></i>
                <h4 class="fw-bold mb-3" style="color: #0096ab;">Konum İzni Gerekli</h4>
                <p class="mb-4" style="font-size: 16px; line-height: 1.5;">Konum izni vermeniz lazım. Şehir adı girerek manuel olarak arama yapabilirsiniz veya konum izni vermeyi deneyebilirsiniz.</p>
            </div>
            <div class="d-flex justify-content-between">
                <button id="closeRejectedPopup" class="btn btn-outline-secondary px-4 py-2" style="font-weight: 500; min-width: 120px;">Tamam</button>
                <button id="retryPermission" class="btn text-white px-4 py-2" style="background-color: #00bed7; font-weight: 500; min-width: 120px; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">İzin Ver</button>
            </div>
        </div>
    `;
    
    document.body.appendChild(modal);
    
    // Tamam butonu - kapat
    document.getElementById('closeRejectedPopup').addEventListener('click', function() {
        modal.remove();
        
        // Eğer şehir seçildiyse o şehrin mahallelerini yükle
        const locationInput = document.getElementById('location');
        if (locationInput && locationInput.value.trim()) {
            const city = locationInput.value.trim();
            console.log("Konum izni reddedilmiş, manuel şehir için mahalleler yükleniyor: " + city);
            updateDistrictsByCity(city);
        }
    });
    
    // İzin ver butonu - konum izni verme işlemini başlat
    document.getElementById('retryPermission').addEventListener('click', function() {
        // Popup'ı kaldır
        modal.remove();
        
        // localStorage durumunu sıfırla
        localStorage.removeItem('locationPermissionGranted');
        
        // Tarayıcının izin popup'ını göster
        navigator.geolocation.getCurrentPosition(
            function(position) {
                localStorage.setItem('locationPermissionGranted', 'true');
                
                // Tarayıcıdan gelen gerçek konum bilgisini kullan
                const latitude = position.coords.latitude;
                const longitude = position.coords.longitude;
                
                const locationInput = document.getElementById('location');
                if (locationInput) {
                    findNearestCity(latitude, longitude, locationInput);
                }
            },
            function(error) {
                if (error.code === error.PERMISSION_DENIED) {
                    localStorage.setItem('locationPermissionGranted', 'false');
                    showLocationError("Konum izni reddedildi");
                    
                    // Eğer şehir seçildiyse o şehrin mahallelerini yükle
                    const locationInput = document.getElementById('location');
                    if (locationInput && locationInput.value.trim()) {
                        const city = locationInput.value.trim();
                        console.log("Konum izni hala reddedildi, manuel şehir için mahalleler yükleniyor: " + city);
                        updateDistrictsByCity(city);
                    }
                }
            },
            { enableHighAccuracy: true, timeout: 10000, maximumAge: 0 }
        );
    });
}

// Konum hatası göster
function showLocationError(message) {
    console.error("Konum hatası:", message);
    
    // Hata bildirimi göster
    const errorBanner = document.createElement('div');
    errorBanner.className = 'alert alert-warning alert-dismissible fade show position-fixed top-0 start-50 translate-middle-x mt-4';
    errorBanner.style.zIndex = '10001'; // Diğer elementlerin üzerinde görünsün
    errorBanner.style.boxShadow = '0 4px 8px rgba(0,0,0,0.2)';
    errorBanner.style.minWidth = '300px';
    errorBanner.style.maxWidth = '500px';
    errorBanner.style.border = '1px solid #f0ad4e';
    errorBanner.setAttribute('role', 'alert');
    errorBanner.innerHTML = `
        <div class="d-flex align-items-center">
            <i class="fas fa-exclamation-triangle me-3 text-warning" style="font-size: 24px;"></i>
            <div class="flex-grow-1" style="font-weight: 500;">
                ${message}
            </div>
            <button type="button" class="btn-close ms-2" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    `;
    
    document.body.appendChild(errorBanner);
    
    // 7 saniye sonra otomatik kapat (daha uzun süre göster)
    setTimeout(() => {
        errorBanner.classList.remove('show');
        setTimeout(() => {
            errorBanner.remove();
        }, 500);
    }, 7000);
}

// Konum debug bilgisi göster
function showLocationDebugInfo(latitude, longitude) {
    console.log("Gerçek konum bilgisi alındı (debug):", latitude, longitude);
}

// En yakın şehri bul
function findNearestCity(latitude, longitude, locationInput) {
    if (!locationInput) return;
    
    // Tahmini şehirler ve koordinatları
    const cities = [
        { name: "İstanbul", lat: 41.0082, lon: 28.9784 },
        { name: "Ankara", lat: 39.9334, lon: 32.8597 },
        { name: "İzmir", lat: 38.4237, lon: 27.1428 },
        { name: "Bursa", lat: 40.1885, lon: 29.0610 },
        { name: "Antalya", lat: 36.8969, lon: 30.7133 },
        { name: "Adana", lat: 37.0000, lon: 35.3213 },
        { name: "Konya", lat: 37.8667, lon: 32.4833 },
        { name: "Gaziantep", lat: 37.0662, lon: 37.3833 },
        { name: "Mersin", lat: 36.8000, lon: 34.6333 },
        { name: "Diyarbakır", lat: 37.9144, lon: 40.2306 }
    ];
    
    // İstanbul'daki önemli mahalleler
    const istanbulNeighborhoods = [
        { name: 'Kadıköy', lat: 40.9927, lon: 29.0276 },
        { name: 'Beşiktaş', lat: 41.0422, lon: 29.0093 },
        { name: 'Şişli', lat: 41.0602, lon: 28.9877 },
        { name: 'Üsküdar', lat: 41.0256, lon: 29.0156 },
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
    
    // En yakın şehri bul
    let closestCity = null;
    let minCityDistance = Infinity;
    let cityDistance = 0;
    
    for (const city of cities) {
        cityDistance = haversineDistance(latitude, longitude, city.lat, city.lon);
        if (cityDistance < minCityDistance) {
            minCityDistance = cityDistance;
            closestCity = city;
        }
    }
    
    // En yakın mahalleyi bul (sadece İstanbul için)
    let closestNeighborhood = null;
    let neighborhoodDistance = 0;
    let minNeighborhoodDistance = Infinity;
    
    if (closestCity && closestCity.name === "İstanbul") {
        for (const nh of istanbulNeighborhoods) {
            neighborhoodDistance = haversineDistance(latitude, longitude, nh.lat, nh.lon);
            if (neighborhoodDistance < minNeighborhoodDistance) {
                minNeighborhoodDistance = neighborhoodDistance;
                closestNeighborhood = nh;
            }
        }
    }
    
    // Input'u güncelle
    locationInput.disabled = false;
    if (closestCity) {
        console.log("En yakın şehir olarak tespit edildi:", closestCity.name);
        locationInput.value = closestCity.name;
        
        // Şehre göre mahalleleri yükle
        if (typeof updateDistrictsByCity === 'function') {
            console.log("Şehir için mahalle verileri yükleniyor:", closestCity.name);
            updateDistrictsByCity(closestCity.name).then(() => {
                // Eğer İstanbul ise ve en yakın mahalle bulunduysa
                if (closestCity.name === "İstanbul" && closestNeighborhood) {
                    console.log("İstanbul için en yakın mahalle:", closestNeighborhood.name);
                    
                    // Mahalle seçim kutusu
                    const districtSelect = document.getElementById('district');
                    if (districtSelect) {
                        // En yakın mahalleyi dropdown'da seç
                        for (let i = 0; i < districtSelect.options.length; i++) {
                            if (districtSelect.options[i].text.includes(closestNeighborhood.name)) {
                                console.log("Mahalle dropdown'da seçildi:", districtSelect.options[i].text);
                                districtSelect.selectedIndex = i;
                                break;
                            }
                        }
                    }
                }
                
                // Form otomatik olarak gönder
                submitForm();
            }).catch(error => {
                console.error("Mahalle yüklerken hata:", error);
                // Hata durumunda sadece form gönder
                submitForm();
            });
        } else {
            console.error("updateDistrictsByCity fonksiyonu bulunamadı!");
            submitForm();
        }
        
        // Form submit fonksiyonu
        function submitForm() {
            const form = locationInput.closest('form');
            if (form) {
                console.log("Form otomatik olarak gönderiliyor...");
                form.submit();
            }
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