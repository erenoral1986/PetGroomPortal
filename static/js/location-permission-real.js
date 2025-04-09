// Sayfa yüklendiğinde konum izni kontrolünü yap
document.addEventListener('DOMContentLoaded', function() {
    // Konum kontrolünü başlat
    checkPermissionOnPageLoad();
    
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
                                    
                                    // Önce neighbourhood bilgisini al
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
                                    
                                    // Mahalle bilgisi bulunmuşsa ve mahalle seçim kutusu varsa
                                    if (realNeighborhood) {
                                        console.log("Yeni gerçek mahalle bulundu:", realNeighborhood);
                                        
                                        // Dropdown'u güncelle
                                        const districtSelect = document.getElementById('district');
                                        if (districtSelect) {
                                            for (let i = 0; i < districtSelect.options.length; i++) {
                                                if (districtSelect.options[i].text.includes(realNeighborhood)) {
                                                    console.log("Yeni mahalle bilgisine göre dropdown güncellendi:", districtSelect.options[i].text);
                                                    districtSelect.selectedIndex = i;
                                                    break;
                                                }
                                            }
                                        }
                                    }
                                }
                            })
                            .catch(error => {
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
    
    // Test aşamasında yorum satırı kaldırılarak localStorage temizlenebilir 
    // localStorage.removeItem('locationPermissionGranted');
    const permissionState = localStorage.getItem('locationPermissionGranted');
    console.log("Konum izni durumu:", permissionState);
    
    // Test amaçlı - her zaman izin sorulsun
    showPermissionRequestPopup();
    
    // Normal izin kontrolü kodu (şu an devre dışı)
    /*
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
    */
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
    
    // Konum izni kontrolü - izni her seferinde göster
    localStorage.removeItem('locationPermissionGranted');
    
    // Tarayıcıdan konum iste - WhatsApp'ın kullandığı gibi yüksek hassasiyetli ayarlar kullanarak
    navigator.geolocation.getCurrentPosition(
        // Başarılı olursa
        function(position) {
            // Tarayıcıdan gelen gerçek konum bilgisini kullan
            const latitude = position.coords.latitude;
            const longitude = position.coords.longitude;
            const accuracy = position.coords.accuracy;
            
            console.log("Konum hassasiyeti:", accuracy, "metre");
            
            // Konum bilgilerini detaylı olarak göster
            showLocationDebugInfo(latitude, longitude);
            
            // Hassasiyet çok düşükse (>100m) daha hassas konum almayı dene
            if (accuracy > 100) {
                console.log("Konum hassasiyeti düşük, daha yüksek hassasiyetli konum almaya çalışılıyor...");
                
                // Konum izlemeyi başlat - tek seferlik değil, sürekli izleme (WhatsApp gibi)
                const watchId = navigator.geolocation.watchPosition(
                    function(betterPosition) {
                        // Daha hassas konum bilgisi alındığında
                        const betterAccuracy = betterPosition.coords.accuracy;
                        console.log("Yeni konum hassasiyeti:", betterAccuracy, "metre");
                        
                        // Yeni konum daha hassas mı kontrol et
                        if (betterAccuracy < accuracy) {
                            const newLatitude = betterPosition.coords.latitude;
                            const newLongitude = betterPosition.coords.longitude;
                            
                            console.log("Daha hassas konum bilgisi alındı:", newLatitude, newLongitude);
                            
                            // En yakın şehri bul
                            findNearestCity(newLatitude, newLongitude, locationInput);
                            
                            // İzlemeyi durdur
                            navigator.geolocation.clearWatch(watchId);
                        }
                    },
                    function(error) {
                        console.error("İzleme hatası:", error);
                        navigator.geolocation.clearWatch(watchId);
                    },
                    { 
                        enableHighAccuracy: true, 
                        timeout: 15000, 
                        maximumAge: 0 
                    }
                );
                
                // 15 saniye sonra izlemeyi durdur (zaman aşımı)
                setTimeout(function() {
                    navigator.geolocation.clearWatch(watchId);
                }, 15000);
            } else {
                // Hassasiyet yeterliyse hemen işlem yap
                // En yakın şehri bul
                findNearestCity(latitude, longitude, locationInput);
            }
            
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
            enableHighAccuracy: true,  // WhatsApp gibi yüksek hassasiyet iste
            timeout: 15000,            // Biraz daha uzun timeout süresi (15 sn)
            maximumAge: 0              // Her zaman güncel konum bilgisi iste
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
                <h5 class="fw-bold">Konum İzni Gerekli</h5>
                <p class="text-muted mb-3">Konum izni vermeniz lazım. Şehir adı girerek manuel olarak arama yapabilirsiniz veya konum izni vermeyi deneyebilirsiniz.</p>
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
    
    // Popup oluştur
    const modal = document.createElement('div');
    modal.className = 'position-fixed top-0 start-0 w-100 h-100 d-flex align-items-center justify-content-center';
    modal.style.backgroundColor = 'rgba(0, 0, 0, 0.5)';
    modal.style.zIndex = '9999';
    
    modal.innerHTML = `
        <div class="bg-white p-4 rounded-3 shadow-lg" style="max-width: 400px;">
            <div class="text-center mb-3">
                <i class="fas fa-map-marker-alt fa-3x text-pet-blue mb-3"></i>
                <h5 class="fw-bold">Konum İzni Gerekli</h5>
                <p class="text-muted mb-3">Konum izni vermeniz lazım. Şehir adı girerek manuel olarak arama yapabilirsiniz veya konum izni vermeyi deneyebilirsiniz.</p>
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
                        console.log("İzin soruldu ama reddedildi, manuel şehir için mahalleler yükleniyor: " + city);
                        updateDistrictsByCity(city);
                    }
                }
            },
            { enableHighAccuracy: true, timeout: 10000, maximumAge: 0 }
        );
    });
    
    // İzni reddet butonu - popup'ı kapat ve izni reddet
    document.getElementById('denyPermission').addEventListener('click', function() {
        localStorage.setItem('locationPermissionGranted', 'false');
        modal.remove();
        
        // Eğer şehir seçildiyse o şehrin mahallelerini yükle
        const locationInput = document.getElementById('location');
        if (locationInput && locationInput.value.trim()) {
            const city = locationInput.value.trim();
            console.log("İzin isteği reddedildi, manuel şehir için mahalleler yükleniyor: " + city);
            updateDistrictsByCity(city);
        }
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
            locationInput.placeholder = "Şehir ara";
        }, 3000);
    }
}

// Test için konum koordinatlarını ve adres bilgilerini ekrana yazdırma
function showLocationDebugInfo(latitude, longitude) {
    // Türkiye'deki önemli şehirlerin koordinatları (enlem, boylam) ve isimlerini kontrol et
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
    
    // Tuzla ilçesi için özel konum kontrolü - Tuzla sınırları kabaca:
    const TUZLA_MIN_LAT = 40.75;
    const TUZLA_MAX_LAT = 40.87;
    const TUZLA_MIN_LON = 29.25;
    const TUZLA_MAX_LON = 29.40;
    
    const isTuzlaArea = 
        latitude >= TUZLA_MIN_LAT && 
        latitude <= TUZLA_MAX_LAT && 
        longitude >= TUZLA_MIN_LON && 
        longitude <= TUZLA_MAX_LON;
    
    if (isTuzlaArea) {
        console.log("Tuzla ilçesi sınırları içindesiniz!");
    }
    
    // Tuzla'nın mahalleri
    const tuzlaNeighborhoods = [
        { name: 'Aydınlı', lat: 40.841, lon: 29.321 },
        { name: 'Cami', lat: 40.819, lon: 29.301 },
        { name: 'Evliya Çelebi', lat: 40.819, lon: 29.304 },
        { name: 'Fatih', lat: 40.821, lon: 29.307 },
        { name: 'İçmeler', lat: 40.815, lon: 29.307 },
        { name: 'İstasyon', lat: 40.817, lon: 29.301 },
        { name: 'Mimar Sinan', lat: 40.829, lon: 29.309 },
        { name: 'Postane', lat: 40.820, lon: 29.298 },
        { name: 'Yayla', lat: 40.823, lon: 29.313 },
        { name: 'Aydıntepe', lat: 40.835, lon: 29.322 },
        { name: 'Şifa', lat: 40.817, lon: 29.308 },
        { name: 'Tepeören', lat: 40.879, lon: 29.373 }
    ];
    
    // WhatsApp seviyesinde hassas konum tespiti için
    // Önce Nominatim API'den gerçek mahalle bilgisini almayı deneriz
    // Alamazsak en yakın konumu hesaplarız
    
    // En yakın şehri ve mahalleyi hesapla (API başarısız olursa yedek olarak)
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
    
    // En yakın mahalleyi bul (İstanbul için ya da Tuzla için)
    let closestNeighborhood = null;
    let neighborhoodDistance = 0;
    let minNeighborhoodDistance = Infinity;
    
    if (closestCity && closestCity.name === "İstanbul") {
        // Tuzla bölgesindeyse Tuzla mahallelerini kullan
        const neighborhoodsList = isTuzlaArea ? tuzlaNeighborhoods : istanbulNeighborhoods;
        
        for (const nh of neighborhoodsList) {
            neighborhoodDistance = haversineDistance(latitude, longitude, nh.lat, nh.lon);
            if (neighborhoodDistance < minNeighborhoodDistance) {
                minNeighborhoodDistance = neighborhoodDistance;
                closestNeighborhood = nh;
            }
        }
        
        console.log("En yakın mahalle hesaplandı:", closestNeighborhood ? closestNeighborhood.name : "Bulunamadı");
        console.log("Mahalle mesafesi:", minNeighborhoodDistance.toFixed(2), "km");
    }
    
    // Debug bilgilerini göstermek için banner oluştur
    const debugBanner = document.createElement('div');
    debugBanner.className = 'position-fixed top-0 start-0 w-100 bg-dark text-white p-2';
    debugBanner.style.zIndex = '9998';
    
    // Tahmini adres oluştur
    let estimatedAddress = "";
    if (closestCity) {
        if (isTuzlaArea) {
            estimatedAddress = "Tuzla";
        } else {
            estimatedAddress = `${closestCity.name}`;
        }
        
        if (closestNeighborhood) {
            estimatedAddress += `, ${closestNeighborhood.name} Mahallesi`;
        }
        estimatedAddress += ` (tahmini mesafe: ${minCityDistance.toFixed(1)} km)`;
    }
    
    // Banner içeriği - Gelişmiş test paneli
    debugBanner.innerHTML = `
        <div class="container">
            <div class="mb-2 d-flex justify-content-between align-items-center">
                <h5 class="mb-0 text-primary">Konum Test Bilgileri (Gelişmiş)</h5>
                <button class="btn btn-sm btn-outline-light" id="closeDebugBanner">Kapat</button>
            </div>
            <div class="row g-2">
                <div class="col-md-5">
                    <div class="card bg-dark border border-secondary p-2 mb-1 rounded-2">
                        <h6 class="text-white">📍 Konum Bilgileri</h6>
                        <p class="mb-1"><strong>Koordinatlar:</strong> ${latitude.toFixed(6)}, ${longitude.toFixed(6)}</p>
                        <p class="mb-1"><strong>Hassasiyet:</strong> ${accuracy ? accuracy.toFixed(1) + ' metre' : 'Bilinmiyor'}</p>
                        <p class="mb-1"><strong>Tahmini adres:</strong> ${estimatedAddress}</p>
                        <p class="mb-1"><strong>API:</strong> <span class="badge bg-info">Yandex Maps</span> <span class="badge bg-secondary">Nominatim</span></p>
                        <p class="mb-0"><strong>Zaman:</strong> ${new Date().toLocaleTimeString()}</p>
                    </div>
                </div>
                <div class="col-md-7">
                    <div class="card bg-light text-dark p-2 rounded-2 border border-info">
                        <h6 class="text-primary mb-2">📫 Adres Tespiti</h6>
                        <div id="locationProvider" class="mb-1">
                            <span class="spinner-border spinner-border-sm text-primary" role="status"></span>
                            <span class="text-muted"> Yandex Maps API sorgulanıyor...</span>
                        </div>
                        <div id="locationAddress" class="bg-light-subtle p-2 rounded border">Adres bilgisi alınıyor...</div>
                        <div id="additionalLocationDetails" class="mt-1"></div>
                        <div id="tuzlaIndicator" class="mt-1">
                            <span class="badge bg-secondary">Tuzla bölge tespiti: Kontrol ediliyor</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    `;
    
    document.body.appendChild(debugBanner);
    
    // Kapatma butonu
    document.getElementById('closeDebugBanner').addEventListener('click', function() {
        debugBanner.remove();
    });
    
    // Yandex Maps API ile adres bilgisini almayı dene
    try {
        // Yandex API anahtarı - HTML sayfasındaki gizli alandan al
        const yandexApiKeyElement = document.getElementById('yandexApiKey');
        const yandexApiKey = yandexApiKeyElement ? yandexApiKeyElement.value : "";
        
        // Yandex Geocoder kullanarak konum bilgisini al (WhatsApp'tan daha hassas, özellikle Türkiye'de)
        fetch(`https://geocode-maps.yandex.ru/1.x/?apikey=${yandexApiKey}&format=json&geocode=${longitude},${latitude}&lang=tr_TR&results=1&kind=house`)
        .then(response => response.json())
        .then(data => {
            const addressElement = document.getElementById('locationAddress');
            if (addressElement) {
                try {
                    // Yandex verilerini parse et
                    const geoObject = data.response.GeoObjectCollection.featureMember[0].GeoObject;
                    const formattedAddress = geoObject.metaDataProperty.GeocoderMetaData.text;
                    const addressComponents = geoObject.metaDataProperty.GeocoderMetaData.Address.Components;
                    
                    // Detaylı adres bilgilerini çıkar
                    const city = addressComponents.find(c => c.kind === 'locality')?.name || 'Bilinmiyor';
                    const district = addressComponents.find(c => c.kind === 'district')?.name || '';
                    const street = addressComponents.find(c => c.kind === 'street')?.name || '';
                    const house = addressComponents.find(c => c.kind === 'house')?.name || '';
                    
                    // Test paneli için provider bilgisini güncelle
                    const providerElement = document.getElementById('locationProvider');
                    if (providerElement) {
                        providerElement.innerHTML = `
                            <span class="badge bg-success">✓ Yandex Maps API</span>
                            <span class="text-success small"> Adres bilgileri başarıyla alındı</span>
                        `;
                    }
                    
                    // Adres detayları
                    addressElement.innerHTML = `<strong>Gerçek adres:</strong> ${formattedAddress}`;
                    
                    // Adres detaylarını da göster
                    const addressDetails = [];
                    if (street) addressDetails.push(street);
                    if (house) addressDetails.push(house);
                    if (district) addressDetails.push(district);
                    if (city) addressDetails.push(city);
                    
                    // Mahalle (district) bilgisini kullan
                    let realNeighborhood = district;
                    
                    // Detay bölümünü güncelle
                    const additionalDetailsElement = document.getElementById('additionalLocationDetails');
                    if (additionalDetailsElement && addressDetails.length > 0) {
                        additionalDetailsElement.innerHTML = `
                            <div class="p-2 mt-2 bg-light-subtle rounded border">
                                <strong>Detaylar:</strong><br>
                                ${addressDetails.map(d => `<span class="badge bg-secondary me-1 mb-1">${d}</span>`).join('')}
                            </div>
                        `;
                    }
                    
                    // Özel Tuzla algılama
                    const isTuzlaArea = city.includes('Tuzla') || district.includes('Tuzla');
                    const tuzlaIndicator = document.getElementById('tuzlaIndicator');
                    
                    if (isTuzlaArea) {
                        console.log("Yandex algılamasına göre Tuzla bölgesindesiniz!");
                        
                        // Tuzla göstergesini güncelle
                        if (tuzlaIndicator) {
                            tuzlaIndicator.innerHTML = `
                                <span class="badge bg-success">✓ Tuzla bölgesi: Doğrulandı (Yandex)</span>
                            `;
                        }
                    } else {
                        if (tuzlaIndicator) {
                            tuzlaIndicator.innerHTML = `
                                <span class="badge bg-warning text-dark">Tuzla bölgesi: Tespit edilemedi</span>
                            `;
                        }
                    }
                    
                    // Şehir - şehri dropdown'da seç
                    setTimeout(() => {
                        const locationInput = document.getElementById('location');
                        if (locationInput && city !== 'Bilinmiyor') {
                            locationInput.value = city;
                            
                            // Şehri seçtikten sonra mahalleleri yükle
                            if (typeof updateDistrictsByCity === 'function') {
                                updateDistrictsByCity(city).then(() => {
                                    // Mahalle bilgisi varsa dropdown'da seç
                                    if (realNeighborhood) {
                                        setTimeout(() => {
                                            const districtSelect = document.getElementById('district');
                                            if (districtSelect) {
                                                for (let i = 0; i < districtSelect.options.length; i++) {
                                                    if (districtSelect.options[i].text.includes(realNeighborhood)) {
                                                        console.log("Mahalle dropdown'da seçildi (Yandex):", districtSelect.options[i].text);
                                                        districtSelect.selectedIndex = i;
                                                        break;
                                                    }
                                                }
                                            }
                                        }, 500); // Dropdown yüklenmesi için 0.5 saniye bekle
                                    }
                                });
                            }
                        }
                    }, 500);
                    
                } catch (parseError) {
                    console.error('Yandex verisi parse edilemedi:', parseError);
                    addressElement.textContent = `Yandex verileri okunamadı, tahmini adres kullanılıyor.`;
                }
            }
        })
        .catch(error => {
            console.error('Yandex adres bilgisi alınamadı:', error);
            
            // Yandex başarısız olursa Nominatim API'yi yedek olarak dene
            fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${latitude}&lon=${longitude}&zoom=18&addressdetails=1&accept-language=tr&namedetails=1&extratags=1&email=petapp@example.com`)
            .then(response => response.json())
            .then(data => {
                const addressElement = document.getElementById('locationAddress');
                if (addressElement) {
                    if (data.display_name) {
                        // Test paneli için provider bilgisini güncelle - Nominatim
                        const providerElement = document.getElementById('locationProvider');
                        if (providerElement) {
                            providerElement.innerHTML = `
                                <span class="badge bg-warning text-dark">⚠️ Nominatim API (Yedek)</span>
                                <span class="text-muted small"> Yandex başarısız oldu, yedek API kullanıldı</span>
                            `;
                        }
                        
                        addressElement.innerHTML = `<strong>Gerçek adres (Nominatim):</strong> ${data.display_name}`;
                        
                        // Adres detaylarını da göster (varsa)
                        if (data.address) {
                            const addressDetails = [];
                            if (data.address.road) addressDetails.push(data.address.road);
                            if (data.address.neighbourhood) addressDetails.push(data.address.neighbourhood);
                            if (data.address.suburb) addressDetails.push(data.address.suburb);
                            if (data.address.city_district) addressDetails.push(data.address.city_district);
                            if (data.address.city) addressDetails.push(data.address.city);
                            if (data.address.state) addressDetails.push(data.address.state);
                            if (data.address.country) addressDetails.push(data.address.country);
                            
                            if (addressDetails.length > 0) {
                                const detailsElement = document.createElement('p');
                                detailsElement.className = 'mb-0 small';
                                detailsElement.textContent = `Adres detayları: ${addressDetails.join(', ')}`;
                                // Güncellenen test paneli için detayları düzenle
            const additionalDetailsElement = document.getElementById('additionalLocationDetails');
            if (additionalDetailsElement) {
                additionalDetailsElement.innerHTML = `
                    <div class="p-2 mt-2 bg-light-subtle rounded border">
                        <strong>Detaylar:</strong><br>
                        ${addressDetails.map(d => `<span class="badge bg-secondary me-1 mb-1">${d}</span>`).join('')}
                    </div>
                `;
            } else {
                addressElement.after(detailsElement);
            }
                            }
                            
                            // Gerçek adresten mahalle bilgisini alıp dropdown'da seç
                            let realNeighborhood = null;
                            
                            // Önce neighbourhood bilgisini al
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
                            
                            // Mahalle bilgisi bulunmuşsa ve İstanbul'daysa
                            if (realNeighborhood && (data.address.city === 'İstanbul' || data.address.city === 'Istanbul')) {
                                console.log("Gerçek adres mahallesi bulundu:", realNeighborhood);
                                
                                // Dropdown'u güncelle
                                setTimeout(() => {
                                    const districtSelect = document.getElementById('district');
                                    if (districtSelect) {
                                        for (let i = 0; i < districtSelect.options.length; i++) {
                                            if (districtSelect.options[i].text.includes(realNeighborhood)) {
                                                console.log("Mahalle dropdown'da seçildi (gerçek adres):", districtSelect.options[i].text);
                                                districtSelect.selectedIndex = i;
                                                break;
                                            }
                                        }
                                    }
                                }, 500); // Dropdown yüklenmesi için 0.5 saniye bekle
                            }
                        }
                    } else {
                        addressElement.textContent = `Servis adresi döndürmedi, tahmini adres kullanılıyor.`;
                    }
                }
            })
            .catch(nominatimError => {
                console.error('Nominatim adres bilgisi de alınamadı:', nominatimError);
                const addressElement = document.getElementById('locationAddress');
                if (addressElement) {
                    addressElement.textContent = `Adres bilgisi alınamadı, tahmini adres kullanılıyor.`;
                }
            });
        });
    } catch (error) {
        console.error('Adres sorgusu sırasında hata:', error);
    }
}

// WhatsApp hassasiyetinde koordinatlardan en yakın şehri ve mahalleyi bul
function findNearestCity(latitude, longitude, locationInput) {
    console.log("WhatsApp hassasiyetinde konum tespiti:", latitude, longitude);
    
    // İlk olarak Yandex'ten adres bilgisini almayı dene
    const getYandexAddress = () => {
        return new Promise((resolve, reject) => {
            try {
                // Yandex API anahtarı
                // Sayfadaki gizli alandan API anahtarını al
                const yandexApiKeyElement = document.getElementById('yandexApiKey');
                const yandexApiKey = yandexApiKeyElement ? yandexApiKeyElement.value : "";
                
                // Yandex Geocoder API'sini kullan
                fetch(`https://geocode-maps.yandex.ru/1.x/?apikey=${yandexApiKey}&format=json&geocode=${longitude},${latitude}&lang=tr_TR&results=1&kind=house`)
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Yandex API yanıt vermedi');
                    }
                    return response.json();
                })
                .then(data => {
                    resolve(data);
                })
                .catch(error => {
                    console.error("Yandex adres bilgisi alınamadı:", error);
                    reject(error);
                });
            } catch (error) {
                console.error("Yandex sorgulaması yapılamadı:", error);
                reject(error);
            }
        });
    };
    
    // Yedek olarak Nominatim'den gerçek adres bilgisini almayı dene
    const getNominatimAddress = () => {
        return new Promise((resolve, reject) => {
            try {
                fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${latitude}&lon=${longitude}&zoom=18&addressdetails=1&accept-language=tr&namedetails=1&extratags=1&email=petapp@example.com`)
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Nominatim API yanıt vermedi');
                    }
                    return response.json();
                })
                .then(data => {
                    resolve(data);
                })
                .catch(error => {
                    console.error("Nominatim adres bilgisi alınamadı:", error);
                    reject(error);
                });
            } catch (error) {
                console.error("Nominatim sorgulaması yapılamadı:", error);
                reject(error);
            }
        });
    };
    
    // Konum bilgilerini localStorage'a kaydet (sürekli güncelleme için)
    localStorage.setItem('userLatitude', latitude);
    localStorage.setItem('userLongitude', longitude);
    
    // Türkiye'nin büyük şehirleri - Daha hassas koordinatlarla
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
    
    // Nominatim'den adres bilgisini almayı dene, alamazsak hesaplama yöntemini kullan
    getNominatimAddress()
    .then(data => {
        console.log("Nominatim'den gelen adres bilgileri:", data);
        
        if (data && data.address) {
            // Şehir bilgisini al
            let detectedCity = data.address.city || data.address.town || data.address.county || data.address.state;
            
            // Mahalle bilgisini al
            let detectedNeighborhood = data.address.neighbourhood || data.address.suburb || data.address.quarter || data.address.hamlet;
            
            console.log("Tespit edilen şehir:", detectedCity);
            console.log("Tespit edilen mahalle:", detectedNeighborhood);
            
            // Şehir Türkçe karakterlerle doğru şekilde yazılmış mı kontrol et
            if (detectedCity) {
                // Türkçe karakter çevirileri yap
                if (detectedCity === "Istanbul") detectedCity = "İstanbul";
                if (detectedCity === "Izmir") detectedCity = "İzmir";
                
                // Şehri input'a ayarla
                if (locationInput) {
                    locationInput.value = detectedCity;
                    locationInput.disabled = false;
                }
                
                // Mahalleleri yükle
                if (typeof updateDistrictsByCity === 'function') {
                    updateDistrictsByCity(detectedCity).then(() => {
                        // Mahalle seçiliyse ve dropdown varsa
                        if (detectedNeighborhood) {
                            setTimeout(() => {
                                const districtSelect = document.getElementById('district');
                                if (districtSelect) {
                                    // Dropdown'da mahalleyi bul
                                    let foundMatch = false;
                                    for (let i = 0; i < districtSelect.options.length; i++) {
                                        // Tam adı veya adın bir parçasını içeriyor mu kontrol et
                                        if (districtSelect.options[i].text.includes(detectedNeighborhood)) {
                                            districtSelect.selectedIndex = i;
                                            console.log("Gerçek mahalle dropdown'da seçildi:", districtSelect.options[i].text);
                                            foundMatch = true;
                                            break;
                                        }
                                    }
                                    
                                    // Tuzla bölgesi için özel kontrol - kesin kontrol için lokasyon dedeksiyonunu geliştirdik
                                    // Tuzla'daki tüm konumları tanımlamak için daha geniş bir kordinat aralığı kullanıyoruz
                                    if (!foundMatch && detectedCity === "İstanbul" && 
                                        ((latitude >= 40.75 && latitude <= 40.87 && longitude >= 29.25 && longitude <= 29.40) || 
                                         data.address.city_district === "Tuzla" || 
                                         data.address.county === "Tuzla")) {
                                        // Konum Tuzla sınırları içinde
                                        for (let i = 0; i < districtSelect.options.length; i++) {
                                            if (districtSelect.options[i].text.includes("Tuzla")) {
                                                districtSelect.selectedIndex = i;
                                                console.log("Konum Tuzla'da, Tuzla seçildi");
                                                break;
                                            }
                                        }
                                    }
                                }
                                
                                // Form'u submit et
                                submitForm();
                            }, 1000);
                        } else {
                            // Mahalle bilgisi yoksa direk formu gönder
                            submitForm();
                        }
                    });
                } else {
                    console.error("updateDistrictsByCity fonksiyonu bulunamadı!");
                    submitForm();
                }
                
                return; // API sonucu var, hesaplamaya gerek yok
            }
        }
        
        // API sonucu yoksa veya eksikse, hesaplamaya geç
        useCalculationMethod();
    })
    .catch(error => {
        console.log("Nominatim API hatası, hesaplama yöntemine geçiliyor:", error);
        useCalculationMethod();
    });
    
    // API sonucu alınamazsa kullanılacak hesaplama yöntemi
    function useCalculationMethod() {
        // İstanbul'un tüm bölgeleri için genişletilmiş mahalle verileri
        const istanbulNeighborhoods = [
        // Anadolu Yakası
        // Kadıköy mahalleleri
        { name: 'Caferağa', lat: 40.9894, lon: 29.0342 },
        { name: 'Fenerbahçe', lat: 40.9703, lon: 29.0361 },
        { name: 'Göztepe', lat: 40.9772, lon: 29.0557 },
        { name: 'Koşuyolu', lat: 41.0128, lon: 29.0339 },
        { name: 'Acıbadem', lat: 40.9831, lon: 29.0469 },
        { name: 'Moda', lat: 40.9828, lon: 29.0259 },
        { name: 'Erenköy', lat: 40.9717, lon: 29.0636 },
        { name: 'Suadiye', lat: 40.9572, lon: 29.0681 },
        { name: 'Bostancı', lat: 40.9533, lon: 29.0775 },
        
        // Üsküdar mahalleleri
        { name: 'Beylerbeyi', lat: 41.0471, lon: 29.0382 },
        { name: 'Çengelköy', lat: 41.0652, lon: 29.0488 },
        { name: 'Kandilli', lat: 41.0762, lon: 29.0576 },
        { name: 'Kuzguncuk', lat: 41.0364, lon: 29.0339 },
        
        // Kartal, Pendik ve Tuzla mahalleleri
        { name: 'Kartal', lat: 40.9063, lon: 29.1566 },
        { name: 'Pendik', lat: 40.8766, lon: 29.2516 },
        { name: 'Tuzla', lat: 40.8179, lon: 29.3007 },
        { name: 'Aydınlı', lat: 40.8389, lon: 29.3385 },
        { name: 'İçmeler', lat: 40.8309, lon: 29.3196 },
        { name: 'Postane', lat: 40.8229, lon: 29.2984 },
        { name: 'Evliya Çelebi', lat: 40.8138, lon: 29.3016 },
        { name: 'Yayla', lat: 40.8211, lon: 29.3104 },
        { name: 'Mimar Sinan', lat: 40.8246, lon: 29.3230 },
        { name: 'Cami', lat: 40.8174, lon: 29.3043 },
        { name: 'Fatih', lat: 40.8160, lon: 29.3096 },
        { name: 'Şifa', lat: 40.8140, lon: 29.3125 },
        
        // Avrupa Yakası
        { name: 'Abbasağa', lat: 41.0422, lon: 29.0097 },
        { name: 'Bebek', lat: 41.0770, lon: 29.0418 },
        { name: 'Etiler', lat: 41.0811, lon: 29.0333 },
        { name: 'Levent', lat: 41.0825, lon: 29.0178 },
        { name: 'Cihangir', lat: 41.0317, lon: 28.9833 },
        { name: 'Galata', lat: 41.0256, lon: 28.9742 },
        { name: 'Taksim', lat: 41.0370, lon: 28.9850 },
        { name: 'Mecidiyeköy', lat: 41.0667, lon: 28.9956 },
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
        
        console.log("En yakın şehir bulundu:", closestCity.name);
        
        // Şehir için mahalleleri yükle ve en yakın mahalleyi seç
        console.log("Şehir için mahalleler yükleniyor:", closestCity.name);
        
        // Mevcut updateDistrictsByCity fonksiyonunu çağır (cities.js'de tanımlı)
        if (typeof updateDistrictsByCity === 'function') {
            updateDistrictsByCity(closestCity.name).then(() => {
                // Şehre bağlı olarak uygun mahalle datasını seç
                let neighborhoodData = [];
                if (closestCity.name === 'İstanbul') {
                    neighborhoodData = istanbulNeighborhoods;
                } else if (closestCity.name === 'Ankara') {
                    neighborhoodData = ankaraNeighborhoods;
                } else if (closestCity.name === 'İzmir') {
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
                            submitForm();
                        }, 1000); // Mahallelerin yüklenmesi için 1 saniye bekle
                    } else {
                        // Mahalle bulunamadıysa direk formu gönder
                        submitForm();
                    }
                } else {
                    // Eğer şehir için mahalle verisi yoksa, direk formu gönder
                    submitForm();
                }
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