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
                        window.location.pathname === '/index.php' || 
                        window.location.pathname === '/index.html' ||
                        window.location.pathname === '/index.php?page=home';
    
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
                                console.log("Şehire göre mahalleleri yüklüyorum:", realCity);
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
                <button id="closeNeverAskedPopup" class="btn btn-outline-secondary px-4 py-2" style="font-weight: 500;">Tamam</button>
                <button id="allowLocationPopup" class="btn bg-pet-blue text-white px-4 py-2" style="font-weight: 500;">İzin Ver</button>
            </div>
        </div>
    `;
    
    document.body.appendChild(modal);
    
    // Tamam butonu
    document.getElementById('closeNeverAskedPopup').addEventListener('click', function() {
        document.body.removeChild(modal);
    });
    
    // İzin Ver butonu
    document.getElementById('allowLocationPopup').addEventListener('click', function() {
        document.body.removeChild(modal);
        getGeolocation();
    });
}

// Konum izni reddedilmiş durumunda gösterilecek popup
function showRejectedPermissionPopup() {
    console.log("Konum izni reddedildi popup oluşturuluyor...");
    
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
                <h4 class="fw-bold mb-3" style="color: #0096ab;">Konum İzni Reddedildi</h4>
                <p class="mb-4" style="font-size: 16px; line-height: 1.5;">Konum izni vermediğiniz için size yakın pet kuaförlerini otomatik olarak gösteremiyoruz. İsterseniz şehir adı girerek manuel olarak arama yapabilirsiniz.</p>
                <p class="small text-muted fst-italic">Not: Tarayıcı veya cihaz ayarlarından konum iznini etkinleştirebilirsiniz.</p>
            </div>
            <div class="d-flex justify-content-center">
                <button id="closeRejectedPopup" class="btn bg-pet-blue text-white px-4 py-2" style="font-weight: 500;">Tamam</button>
            </div>
        </div>
    `;
    
    document.body.appendChild(modal);
    
    // Tamam butonu
    document.getElementById('closeRejectedPopup').addEventListener('click', function() {
        document.body.removeChild(modal);
    });
}

// Konum hatası göster
function showLocationError(message) {
    const locationStatus = document.getElementById('locationStatus');
    if (locationStatus) {
        locationStatus.textContent = message;
        locationStatus.style.color = 'red';
    } else {
        console.error("Konum hatası:", message);
    }
}

// Test için konum bilgilerini konsola göster
function showLocationDebugInfo(latitude, longitude) {
    console.log(`Konum: Lat: ${latitude}, Lng: ${longitude}`);
}

// En yakın şehri bul
function findNearestCity(latitude, longitude, locationInput) {
    // Türkiye'deki büyük şehirlerin koordinatları
    const cities = [
        { name: "İstanbul", lat: 41.0082, lng: 28.9784 },
        { name: "Ankara", lat: 39.9334, lng: 32.8597 },
        { name: "İzmir", lat: 38.4237, lng: 27.1428 },
        { name: "Bursa", lat: 40.1885, lng: 29.0610 },
        { name: "Antalya", lat: 36.8969, lng: 30.7133 },
        { name: "Adana", lat: 37.0000, lng: 35.3213 },
        { name: "Konya", lat: 37.8746, lng: 32.4932 },
        { name: "Kayseri", lat: 38.7205, lng: 35.4784 }
    ];
    
    let nearestCity = null;
    let minDistance = Infinity;
    
    // En yakın şehri bul
    cities.forEach(city => {
        const distance = haversineDistance(latitude, longitude, city.lat, city.lng);
        if (distance < minDistance) {
            minDistance = distance;
            nearestCity = city;
        }
    });
    
    if (nearestCity && locationInput) {
        console.log(`En yakın şehir: ${nearestCity.name} (${minDistance.toFixed(2)} km)`);
        
        // Bulunan şehri input alanına yaz
        locationInput.value = nearestCity.name;
        locationInput.disabled = false;
        
        // Şehir bilgisini localStorage'a kaydet
        localStorage.setItem('userCity', nearestCity.name);
        
        // Şehre göre mahalle bilgisini yükle
        if (typeof updateDistrictsByCity === 'function') {
            updateDistrictsByCity(nearestCity.name).then(() => {
                // Form otomatik olarak gönderilsin
                submitForm();
            });
        }
    }
}

// Form gönderme
function submitForm() {
    const searchForm = document.getElementById('searchForm');
    if (searchForm) {
        searchForm.submit();
    }
}

// Haversine formülü ile iki nokta arasındaki mesafeyi hesapla (km cinsinden)
function haversineDistance(lat1, lon1, lat2, lon2) {
    const R = 6371; // Dünya yarıçapı (km)
    const dLat = toRad(lat2 - lat1);
    const dLon = toRad(lon2 - lon1);
    const a = 
        Math.sin(dLat/2) * Math.sin(dLat/2) +
        Math.cos(toRad(lat1)) * Math.cos(toRad(lat2)) * 
        Math.sin(dLon/2) * Math.sin(dLon/2);
    const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a));
    const d = R * c;
    return d;
}

// Dereceyi radyana çevir
function toRad(degree) {
    return degree * Math.PI / 180;
}