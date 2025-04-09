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

// Şehir listesini oluştur
function createCityList(filteredCities) {
    const cityList = document.getElementById('cityList');
    cityList.innerHTML = '';
    
    filteredCities.forEach(city => {
        const item = document.createElement('div');
        item.className = 'city-item p-2 cursor-pointer hover:bg-gray-100';
        item.textContent = city;
        item.addEventListener('click', () => {
            document.getElementById('location').value = city;
            cityList.classList.add('hidden'); // Seçim yapıldığında listeyi gizle
        });
        cityList.appendChild(item);
    });
}

// Sayfa yüklendiğinde
document.addEventListener('DOMContentLoaded', () => {
    const locationInput = document.getElementById('location');
    const cityList = document.getElementById('cityList');
    
    if (!locationInput) return;
    
    // Başlangıçta şehirleri gizle
    cityList.classList.add('hidden');
    
    // Arama alanına yazılınca
    locationInput.addEventListener('input', function() {
        const searchText = this.value.trim();
        
        if (searchText.length >= 3) {
            const filteredCities = filterCities(searchText);
            createCityList(filteredCities);
            if (filteredCities.length > 0) {
                cityList.classList.remove('hidden');
            } else {
                cityList.classList.add('hidden');
            }
        } else {
            cityList.classList.add('hidden');
        }
    });
    
    // Başka bir yere tıklayınca listeyi gizle
    document.addEventListener('click', function(e) {
        if (!locationInput.contains(e.target) && !cityList.contains(e.target)) {
            cityList.classList.add('hidden');
        }
    });
    
    // Focus olunca en az 3 karakter yazılıysa şehirleri göster
    locationInput.addEventListener('focus', function() {
        const searchText = this.value.trim();
        if (searchText.length >= 3) {
            const filteredCities = filterCities(searchText);
            createCityList(filteredCities);
            if (filteredCities.length > 0) {
                cityList.classList.remove('hidden');
            }
        }
    });
});