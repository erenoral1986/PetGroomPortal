document.addEventListener('DOMContentLoaded', function() {
    const dateInput = document.getElementById('date');
    const serviceSelect = document.getElementById('service_id');
    const timeSlotSelect = document.getElementById('time_slot');
    const salonIdInput = document.getElementById('salon_id');
    
    if (dateInput && serviceSelect && timeSlotSelect && salonIdInput) {
        // Initialize flatpickr for date picker
        const datePicker = flatpickr(dateInput, {
            minDate: "today",
            dateFormat: "Y-m-d",
            disableMobile: "true",
            onChange: function(selectedDates, dateStr, instance) {
                updateAvailableTimeSlots(dateStr);
            }
        });
        
        // Update time slots when service changes
        serviceSelect.addEventListener('change', function() {
            const selectedDate = dateInput.value;
            if (selectedDate) {
                updateAvailableTimeSlots(selectedDate);
            }
        });
        
        // Function to fetch and update available time slots
        function updateAvailableTimeSlots(selectedDate) {
            const salonId = salonIdInput.value;
            const serviceId = serviceSelect.value;
            
            if (!salonId || !serviceId) {
                return;
            }
            
            // Clear existing options
            timeSlotSelect.innerHTML = '<option value="">Select a time</option>';
            
            // Show loading indicator
            timeSlotSelect.disabled = true;
            timeSlotSelect.innerHTML += '<option value="" disabled>Loading...</option>';
            
            // Fetch available slots from server
            fetch(`/salon/${salonId}/available_slots`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    date: selectedDate,
                    service_id: serviceId
                }),
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(data => {
                // Clear loading indicator
                timeSlotSelect.innerHTML = '<option value="">Select a time</option>';
                
                // Add available slots
                if (data.slots && data.slots.length > 0) {
                    data.slots.forEach(slot => {
                        const option = document.createElement('option');
                        option.value = slot;
                        option.textContent = formatTimeDisplay(slot);
                        timeSlotSelect.appendChild(option);
                    });
                } else {
                    const option = document.createElement('option');
                    option.value = "";
                    option.disabled = true;
                    option.textContent = "No available slots";
                    timeSlotSelect.appendChild(option);
                }
            })
            .catch(error => {
                console.error('Error fetching available slots:', error);
                
                // Show error message
                timeSlotSelect.innerHTML = '<option value="">Error loading time slots</option>';
            })
            .finally(() => {
                timeSlotSelect.disabled = false;
            });
        }
        
        // Format time for display (convert from 24h to 12h format)
        function formatTimeDisplay(timeString) {
            const [hours, minutes] = timeString.split(':');
            const hour = parseInt(hours, 10);
            const ampm = hour >= 12 ? 'PM' : 'AM';
            const hour12 = hour % 12 || 12;
            return `${hour12}:${minutes} ${ampm}`;
        }
    }
    
    // Service duration and price display
    const servicePriceElement = document.getElementById('service-price');
    const serviceDurationElement = document.getElementById('service-duration');
    
    if (serviceSelect && servicePriceElement && serviceDurationElement) {
        // Initialize with data attributes from the currently selected option
        updateServiceInfo();
        
        // Update when selection changes
        serviceSelect.addEventListener('change', updateServiceInfo);
        
        function updateServiceInfo() {
            const selectedOption = serviceSelect.options[serviceSelect.selectedIndex];
            if (selectedOption && selectedOption.value) {
                const priceText = selectedOption.getAttribute('data-price');
                const durationText = selectedOption.getAttribute('data-duration');
                
                if (priceText) {
                    servicePriceElement.textContent = '$' + priceText;
                    servicePriceElement.parentElement.style.display = 'block';
                } else {
                    servicePriceElement.parentElement.style.display = 'none';
                }
                
                if (durationText) {
                    serviceDurationElement.textContent = durationText + ' minutes';
                    serviceDurationElement.parentElement.style.display = 'block';
                } else {
                    serviceDurationElement.parentElement.style.display = 'none';
                }
            } else {
                servicePriceElement.parentElement.style.display = 'none';
                serviceDurationElement.parentElement.style.display = 'none';
            }
        }
    }
});
