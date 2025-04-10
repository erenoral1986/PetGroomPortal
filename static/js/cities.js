// Şehir listesi ile ilgili fonksiyonlar

// Şehirleri filtrele
function filterCities(searchText) {
    if (!searchText || searchText.length < 3) {
        hideCityList();
        return;
    }
    
    searchText = searchText.toLowerCase();
    let filteredCities = [];
    
    if (typeof CITIES_LIST !== 'undefined' && Array.isArray(CITIES_LIST)) {
        filteredCities = CITIES_LIST.filter(city => 
            city.toLowerCase().includes(searchText)
        );
    }
    
    // Şehir listesini göster
    createCityList(filteredCities);
    showCityList();
}

// Filtrelenmiş şehirler için liste oluştur
function createCityList(filteredCities) {
    const cityList = document.getElementById('cityList');
    if (!cityList) return;
    
    cityList.innerHTML = '';
    
    if (filteredCities.length === 0) {
        const noResult = document.createElement('div');
        noResult.className = 'city-item';
        noResult.textContent = 'Sonuç bulunamadı';
        cityList.appendChild(noResult);
        return;
    }
    
    filteredCities.forEach(city => {
        const cityItem = document.createElement('div');
        cityItem.className = 'city-item';
        cityItem.textContent = city;
        cityItem.addEventListener('click', function() {
            document.getElementById('location').value = city;
            hideCityList();
            
            // Şehir seçilince mahalle listesini güncelle
            if (typeof updateDistrictsByCity === 'function') {
                updateDistrictsByCity(city);
            }
        });
        cityList.appendChild(cityItem);
    });
}

// Şehir listesini göster
function showCityList() {
    const cityList = document.getElementById('cityList');
    if (cityList) {
        cityList.style.display = 'block';
    }
}

// Şehir listesini gizle
function hideCityList() {
    const cityList = document.getElementById('cityList');
    if (cityList) {
        cityList.style.display = 'none';
    }
}

// Sayfa dışı tıklamalarda şehir listesini kapat
document.addEventListener('click', function(event) {
    const cityList = document.getElementById('cityList');
    const locationInput = document.getElementById('location');
    
    if (cityList && locationInput && !cityList.contains(event.target) && event.target !== locationInput) {
        hideCityList();
    }
});

// Gerçek mahalle seçimi
function selectRealNeighborhood(neighborhood) {
    if (!neighborhood) return;
    
    const districtSelect = document.getElementById('district');
    if (!districtSelect) return;
    
    // Mahalle seçimini güncelle
    for (let i = 0; i < districtSelect.options.length; i++) {
        if (districtSelect.options[i].text === neighborhood) {
            districtSelect.selectedIndex = i;
            break;
        }
    }
}

// Şehir değiştiğinde mahalle listesini güncelle
async function updateDistrictsByCity(city) {
    const districtSelect = document.getElementById('district');
    if (!districtSelect) return;
    
    try {
        // API'den mahalle listesini al
        const response = await fetch(`api/get_districts.php?city=${encodeURIComponent(city)}`);
        const data = await response.json();
        
        // Mevcut seçenekleri temizle
        districtSelect.innerHTML = '';
        
        // "Tüm Mahalleler" seçeneğini ekle
        const allOption = document.createElement('option');
        allOption.value = 'all';
        allOption.textContent = 'Tüm Mahalleler';
        districtSelect.appendChild(allOption);
        
        // Mahalleleri ekle
        if (data.districts && data.districts.length > 0) {
            data.districts.forEach(district => {
                if (district === 'Tüm Mahalleler') return; // "Tüm Mahalleler" zaten eklendi
                
                const option = document.createElement('option');
                option.value = district;
                option.textContent = district;
                districtSelect.appendChild(option);
            });
        }
        
        // LocalStorage'da kayıtlı mahalle varsa seç
        const savedNeighborhood = localStorage.getItem('userNeighborhood');
        if (savedNeighborhood) {
            selectRealNeighborhood(savedNeighborhood);
        }
        
        return true;
    } catch (error) {
        console.error('Mahalleler alınırken hata oluştu:', error);
        return false;
    }
}