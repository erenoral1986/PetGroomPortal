// Türkiye'nin 81 ili
const cities = [
    "Adana", "Adıyaman", "Afyonkarahisar", "Ağrı", "Amasya", "Ankara", "Antalya", "Artvin", 
    "Aydın", "Balıkesir", "Bilecik", "Bingöl", "Bitlis", "Bolu", "Burdur", "Bursa", 
    "Çanakkale", "Çankırı", "Çorum", "Denizli", "Diyarbakır", "Edirne", "Elazığ", "Erzincan", 
    "Erzurum", "Eskişehir", "Gaziantep", "Giresun", "Gümüşhane", "Hakkari", "Hatay", "Isparta", 
    "Mersin", "İstanbul", "İzmir", "Kars", "Kastamonu", "Kayseri", "Kırklareli", "Kırşehir", 
    "Kocaeli", "Konya", "Kütahya", "Malatya", "Manisa", "Kahramanmaraş", "Mardin", "Muğla", 
    "Muş", "Nevşehir", "Niğde", "Ordu", "Rize", "Sakarya", "Samsun", "Siirt", 
    "Sinop", "Sivas", "Tekirdağ", "Tokat", "Trabzon", "Tunceli", "Şanlıurfa", "Uşak", 
    "Van", "Yozgat", "Zonguldak", "Aksaray", "Bayburt", "Karaman", "Kırıkkale", "Batman", 
    "Şırnak", "Bartın", "Ardahan", "Iğdır", "Yalova", "Karabük", "Kilis", "Osmaniye", 
    "Düzce"
];

// Şehir filtreleme işlevi
function filterCities(searchText) {
    searchText = searchText.toLowerCase();
    return cities.filter(city => city.toLowerCase().includes(searchText));
}

// Global elemanlara referanslar
let locationInput;
let cityList;
let districtSelect;

// Şehir listesini oluştur
function createCityList(filteredCities) {
    if (!cityList) return;
    
    // Önce listeyi temizle
    cityList.innerHTML = '';
    
    filteredCities.forEach(city => {
        const item = document.createElement('div');
        item.className = 'city-item p-3 border-bottom cursor-pointer';
        item.textContent = city;
        item.style.cursor = 'pointer';
        
        // Hover efekti için stil
        item.style.transition = 'background-color 0.2s';
        item.addEventListener('mouseenter', function() {
            this.style.backgroundColor = '#f8f9fa';
        });
        item.addEventListener('mouseleave', function() {
            this.style.backgroundColor = '';
        });
        
        // Tıklama olayı - Şehir seçildiğinde
        item.onclick = function() {
            if (locationInput) {
                locationInput.value = city;
                hideCityList(); // Listeyi gizle
                // Şehir seçildikten sonra ilgili mahalleleri yükle
                updateDistrictsByCity(city);
                // Log ekleyerek kontrol et
                console.log("Şehir seçildi: " + city + ", mahalleler yükleniyor...");
            }
        };
        
        cityList.appendChild(item);
    });
}

// Şehir listesini göster
function showCityList() {
    if (cityList) {
        cityList.style.display = 'block';
        cityList.classList.remove('hidden');
    }
}

// Şehir listesini gizle
function hideCityList() {
    if (cityList) {
        cityList.style.display = 'none';
        cityList.classList.add('hidden');
    }
}

// Şehir değiştiğinde mahalleleri getir
function updateDistrictsByCity(city) {
    if (!city || city.trim() === '') return Promise.reject('Şehir boş');
    
    console.log(`updateDistrictsByCity() fonksiyonu çağrıldı - şehir: "${city}"`);
    
    // Önce mevcut illeri kontrol et
    const turkishCities = [
        'İstanbul', 'Ankara', 'İzmir', 'Bursa', 'Antalya', 'Adana', 
        'Konya', 'Gaziantep', 'Mersin', 'Diyarbakır', 'Kayseri', 
        'Eskişehir', 'Samsun', 'Denizli', 'Şanlıurfa', 'Malatya', 
        'Erzurum', 'Trabzon'
    ];
    
    // Şehir adı gerçekten Türkiye'de bir şehir mi?
    const isValidCity = turkishCities.includes(city);
    if (!isValidCity) {
        console.warn(`"${city}" geçerli bir şehir değil, mahalleler çağrılmayacak.`);
        return Promise.reject('Geçersiz şehir');
    }
    
    // Mahalle seçim kutusunu yükleniyor durumuna getir
    if (districtSelect) {
        districtSelect.innerHTML = '<option value="">Yükleniyor...</option>';
        districtSelect.disabled = true;
    }
    
    // API'den mahalleleri al
    return fetch('/get_districts', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({ city: city })
    })
    .then(response => {
        console.log("API yanıt durumu:", response.status);
        return response.json();
    })
    .then(data => {
        console.log("API yanıtı:", data);
        
        // Mahalle select kutusunu temizle
        if (!districtSelect) {
            console.error("Mahalle seçim kutusu bulunamadı!");
            return;
        }
        
        districtSelect.innerHTML = '';
        districtSelect.disabled = false;
        
        // Mahalleleri ekle
        if (data.districts && data.districts.length > 0) {
            console.log(`${data.districts.length} mahalle bulundu.`);
            data.districts.forEach(district => {
                const option = document.createElement('option');
                option.value = district === 'Tüm Mahalleler' ? 'all' : district;
                option.textContent = district;
                districtSelect.appendChild(option);
            });
        } else {
            console.warn("Mahallelerin uzunluğu sıfır veya boş.");
            // Eğer mahalle bulunamazsa varsayılan seçeneği ekle
            const option = document.createElement('option');
            option.value = 'all';
            option.textContent = 'Tüm Mahalleler';
            districtSelect.appendChild(option);
        }
        
        // İşlem tamamlandı, promise'i resolve et
        return data;
    })
    .catch(error => {
        console.error('Mahalleler alınırken hata oluştu:', error);
        
        // Hata durumunda varsayılan seçenek ekle
        if (!districtSelect) return Promise.reject(error);
        
        districtSelect.disabled = false;
        districtSelect.innerHTML = '';
        const option = document.createElement('option');
        option.value = 'all';
        option.textContent = 'Tüm Mahalleler';
        districtSelect.appendChild(option);
        
        // Hata durumunda promise'i reject et
        return Promise.reject(error);
    });
}

// Sayfa yüklendiğinde
document.addEventListener('DOMContentLoaded', function() {
    // Elemanları al
    locationInput = document.getElementById('location');
    cityList = document.getElementById('cityList');
    districtSelect = document.getElementById('district');
    
    if (!locationInput || !cityList) return;
    
    // Başlangıçta şehirleri gizle
    hideCityList();
    
    // Eğer şehir alanında önceden bir değer varsa, o şehir için mahalleleri yükle
    if (locationInput.value.trim()) {
        console.log("Sayfa yüklendiğinde şehir değeri algılandı:", locationInput.value.trim());
        updateDistrictsByCity(locationInput.value.trim());
    }
    
    // Arama alanına yazılınca
    locationInput.addEventListener('input', function() {
        const searchText = this.value.trim();
        
        if (searchText.length >= 3) {
            const filteredCities = filterCities(searchText);
            createCityList(filteredCities);
            
            if (filteredCities.length > 0) {
                showCityList();
            } else {
                hideCityList();
            }
        } else {
            hideCityList();
        }
    });
    
    // Başka bir yere tıklayınca listeyi gizle
    document.addEventListener('click', function(e) {
        if (locationInput && cityList) {
            if (!locationInput.contains(e.target) && !cityList.contains(e.target)) {
                hideCityList();
            }
        }
    });
    
    // Her seçim sonrası listeyi kapatan ek önlem
    if (cityList) {
        cityList.addEventListener('click', function(e) {
            if (e.target.classList.contains('city-item')) {
                hideCityList();
            }
        });
    }
    
    // Focus olunca en az 3 karakter yazılıysa şehirleri göster
    if (locationInput) {
        locationInput.addEventListener('focus', function() {
            const searchText = this.value.trim();
            if (searchText.length >= 3) {
                const filteredCities = filterCities(searchText);
                createCityList(filteredCities);
                if (filteredCities.length > 0) {
                    showCityList();
                }
            }
        });
        
        // Şehir değiştiğinde (blur olduğunda) mahalleleri güncelle
        locationInput.addEventListener('blur', function() {
            setTimeout(() => {
                const city = this.value.trim();
                if (city) {
                    console.log("Şehir alanı değiştiğinde mahalleler yükleniyor:", city);
                    updateDistrictsByCity(city);
                }
            }, 200);
        });
    }
});