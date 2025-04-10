/**
 * Pet Kuaför - Randevu Sayfası JavaScript Dosyası
 * 
 * Randevu sayfasındaki etkileşimleri yönetir
 */

document.addEventListener('DOMContentLoaded', function() {
    // Hizmet seçimi değiştiğinde
    const serviceSelect = document.getElementById('service_id');
    const petTypeFilterRadios = document.querySelectorAll('input[name="pet_type_filter"]');
    
    // Pet türü filtresi değiştiğinde
    petTypeFilterRadios.forEach(radio => {
        radio.addEventListener('change', function() {
            filterServices(this.value);
        });
    });
    
    // Sayfa yüklendiğinde seçili pet türüne göre filtreleme yap
    if (petTypeFilterRadios.length > 0) {
        const selectedPetType = document.querySelector('input[name="pet_type_filter"]:checked');
        if (selectedPetType) {
            filterServices(selectedPetType.value);
        }
    }
    
    // Hizmet bilgilerini güncelle
    if (serviceSelect) {
        serviceSelect.addEventListener('change', updateServiceInfo);
        
        // Sayfa yüklendiğinde hizmet bilgilerini güncelle
        updateServiceInfo();
    }
    
    // Tarih seçimi değiştiğinde
    const dateInput = document.getElementById('date');
    if (dateInput) {
        dateInput.addEventListener('change', function() {
            updateAvailableTimeSlots(this.value);
        });
        
        // Sayfa yüklendiğinde tarih seçiliyse zaman dilimlerini güncelle
        if (dateInput.value) {
            updateAvailableTimeSlots(dateInput.value);
        }
    }
    
    // Zaman dilimi seçimi değiştiğinde
    const timeSelect = document.getElementById('time_slot');
    if (timeSelect) {
        timeSelect.addEventListener('change', updateSummary);
    }
    
    // Evcil hayvan bilgileri değiştiğinde
    const petNameInput = document.getElementById('pet_name');
    const petTypeSelect = document.getElementById('pet_type');
    
    if (petNameInput) {
        petNameInput.addEventListener('input', updateSummary);
    }
    
    if (petTypeSelect) {
        petTypeSelect.addEventListener('change', updateSummary);
    }
    
    // Hizmetleri filtrele
    function filterServices(petType) {
        if (!serviceSelect) return;
        
        const options = serviceSelect.querySelectorAll('option');
        
        options.forEach(option => {
            if (option.value === '') return; // Boş seçenek her zaman görünür
            
            const servicePetType = option.getAttribute('data-pet-type');
            
            if (petType === 'all' || petType === servicePetType || servicePetType === 'both') {
                option.style.display = '';
            } else {
                option.style.display = 'none';
            }
        });
        
        // Eğer seçili hizmet artık görünür değilse, seçimi temizle
        if (serviceSelect.selectedIndex > 0) {
            const selectedOption = serviceSelect.options[serviceSelect.selectedIndex];
            const selectedPetType = selectedOption.getAttribute('data-pet-type');
            
            if (petType !== 'all' && petType !== selectedPetType && selectedPetType !== 'both') {
                serviceSelect.selectedIndex = 0;
                updateServiceInfo();
            }
        }
    }
    
    // Hizmet bilgilerini güncelle
    function updateServiceInfo() {
        if (!serviceSelect) return;
        
        const selectedOption = serviceSelect.options[serviceSelect.selectedIndex];
        const summaryDiv = document.getElementById('bookingSummary');
        
        if (selectedOption && selectedOption.value) {
            const price = selectedOption.getAttribute('data-price');
            const duration = selectedOption.getAttribute('data-duration');
            const serviceName = selectedOption.text;
            
            // Özet panelini göster
            if (summaryDiv) {
                summaryDiv.classList.remove('d-none');
                
                // Hizmet bilgilerini güncelle
                const serviceElement = document.getElementById('summaryService');
                const priceElement = document.getElementById('summaryPrice');
                
                if (serviceElement) serviceElement.textContent = serviceName;
                if (priceElement) priceElement.textContent = formatCurrency(price);
            }
            
            // Pet türünü otomatik seç
            const petType = selectedOption.getAttribute('data-pet-type');
            const petTypeSelect = document.getElementById('pet_type');
            
            if (petTypeSelect && petType && petType !== 'both') {
                petTypeSelect.value = petType;
            }
            
            // Tarih seçiliyse zaman dilimlerini güncelle
            if (dateInput && dateInput.value) {
                updateAvailableTimeSlots(dateInput.value);
            }
        } else if (summaryDiv) {
            // Özet panelini gizle
            summaryDiv.classList.add('d-none');
        }
        
        updateSummary();
    }
    
    // Mevcut tarihe göre müsait zaman dilimlerini güncelle
    function updateAvailableTimeSlots(selectedDate) {
        const timeSelect = document.getElementById('time_slot');
        if (!timeSelect) return;
        
        timeSelect.innerHTML = '';
        
        // Seçili hizmet süresini al
        let serviceDuration = 0;
        if (serviceSelect && serviceSelect.selectedIndex > 0) {
            const selectedOption = serviceSelect.options[serviceSelect.selectedIndex];
            serviceDuration = parseInt(selectedOption.getAttribute('data-duration') || 0);
        }
        
        // Seçilen tarih
        const selectedDateObj = new Date(selectedDate);
        const today = new Date();
        today.setHours(0, 0, 0, 0);
        
        // Tarih bugünse, geçmiş saatleri devre dışı bırak
        const now = new Date();
        const currentHour = now.getHours();
        const currentMinute = now.getMinutes();
        
        // Seçilebilecek saatler (normalde API'den alınacak)
        // Bu örnek için sabit saat dilimleri kullanılıyor
        const availableTimes = [
            '09:00:00', '09:30:00', '10:00:00', '10:30:00', '11:00:00', '11:30:00',
            '13:00:00', '13:30:00', '14:00:00', '14:30:00', '15:00:00', '15:30:00',
            '16:00:00', '16:30:00', '17:00:00'
        ];
        
        // Placeholder option
        const placeholder = document.createElement('option');
        placeholder.value = '';
        placeholder.textContent = 'Saat seçin';
        timeSelect.appendChild(placeholder);
        
        // Gerçek saatleri ekle
        availableTimes.forEach(time => {
            const option = document.createElement('option');
            option.value = time;
            option.textContent = formatTimeDisplay(time);
            
            // Bugün için geçmiş saatleri devre dışı bırak
            if (selectedDateObj.getTime() === today.getTime()) {
                const [hours, minutes] = time.split(':').map(Number);
                if (hours < currentHour || (hours === currentHour && minutes <= currentMinute)) {
                    option.disabled = true;
                }
            }
            
            // Bu saat daha önce rezerve edilmiş mi kontrol et (gerçek uygulamada API'den alınır)
            // Bu örnek için bazı saatleri rastgele rezerve edilmiş gibi işaretliyoruz
            if ((selectedDateObj.getDay() % 2 === 0 && time === '10:00:00') || 
                (selectedDateObj.getDay() % 3 === 0 && time === '14:30:00')) {
                option.disabled = true;
                option.textContent += ' (Dolu)';
            }
            
            timeSelect.appendChild(option);
        });
        
        // Daha önce seçili bir saat varsa tekrar seç
        const previousSelectedTime = document.getElementById('time_slot').getAttribute('data-selected-time');
        if (previousSelectedTime) {
            for (let i = 0; i < timeSelect.options.length; i++) {
                if (timeSelect.options[i].value === previousSelectedTime && !timeSelect.options[i].disabled) {
                    timeSelect.selectedIndex = i;
                    break;
                }
            }
        }
        
        updateSummary();
    }
    
    // Özet panelini güncelle
    function updateSummary() {
        const dateInput = document.getElementById('date');
        const timeSelect = document.getElementById('time_slot');
        const summaryDate = document.getElementById('summaryDate');
        const summaryTime = document.getElementById('summaryTime');
        
        if (dateInput && dateInput.value && summaryDate) {
            const formattedDate = new Date(dateInput.value).toLocaleDateString('tr-TR', {
                day: 'numeric',
                month: 'long',
                year: 'numeric'
            });
            summaryDate.textContent = formattedDate;
        } else if (summaryDate) {
            summaryDate.textContent = '-';
        }
        
        if (timeSelect && timeSelect.value && summaryTime) {
            summaryTime.textContent = formatTimeDisplay(timeSelect.value);
            
            // Seçilen zamanı hatırla
            timeSelect.setAttribute('data-selected-time', timeSelect.value);
        } else if (summaryTime) {
            summaryTime.textContent = '-';
        }
    }
    
    // Saat formatını düzenle (HH:MM:SS -> HH:MM)
    function formatTimeDisplay(timeString) {
        return timeString.substring(0, 5);
    }
    
    // Para birimini formatla
    function formatCurrency(amount) {
        return new Intl.NumberFormat('tr-TR', {
            style: 'currency',
            currency: 'TRY',
            minimumFractionDigits: 2
        }).format(amount);
    }
});