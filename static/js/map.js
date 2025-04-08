document.addEventListener('DOMContentLoaded', function() {
    // Check if the map container exists
    const mapContainer = document.getElementById('salon-map');
    if (!mapContainer) return;
    
    // Get salon coordinates from data attributes
    const lat = parseFloat(mapContainer.dataset.lat);
    const lng = parseFloat(mapContainer.dataset.lng);
    
    // If coordinates are not available, hide the map container
    if (isNaN(lat) || isNaN(lng)) {
        mapContainer.style.display = 'none';
        return;
    }
    
    // Initialize the map
    function initMap() {
        // Create the map
        const map = new google.maps.Map(mapContainer, {
            center: { lat: lat, lng: lng },
            zoom: 15,
            styles: [
                { elementType: "geometry", stylers: [{ color: "#242f3e" }] },
                { elementType: "labels.text.stroke", stylers: [{ color: "#242f3e" }] },
                { elementType: "labels.text.fill", stylers: [{ color: "#746855" }] },
                {
                    featureType: "administrative.locality",
                    elementType: "labels.text.fill",
                    stylers: [{ color: "#d59563" }],
                },
                {
                    featureType: "poi",
                    elementType: "labels.text.fill",
                    stylers: [{ color: "#d59563" }],
                },
                {
                    featureType: "poi.park",
                    elementType: "geometry",
                    stylers: [{ color: "#263c3f" }],
                },
                {
                    featureType: "poi.park",
                    elementType: "labels.text.fill",
                    stylers: [{ color: "#6b9a76" }],
                },
                {
                    featureType: "road",
                    elementType: "geometry",
                    stylers: [{ color: "#38414e" }],
                },
                {
                    featureType: "road",
                    elementType: "geometry.stroke",
                    stylers: [{ color: "#212a37" }],
                },
                {
                    featureType: "road",
                    elementType: "labels.text.fill",
                    stylers: [{ color: "#9ca5b3" }],
                },
                {
                    featureType: "road.highway",
                    elementType: "geometry",
                    stylers: [{ color: "#746855" }],
                },
                {
                    featureType: "road.highway",
                    elementType: "geometry.stroke",
                    stylers: [{ color: "#1f2835" }],
                },
                {
                    featureType: "road.highway",
                    elementType: "labels.text.fill",
                    stylers: [{ color: "#f3d19c" }],
                },
                {
                    featureType: "transit",
                    elementType: "geometry",
                    stylers: [{ color: "#2f3948" }],
                },
                {
                    featureType: "transit.station",
                    elementType: "labels.text.fill",
                    stylers: [{ color: "#d59563" }],
                },
                {
                    featureType: "water",
                    elementType: "geometry",
                    stylers: [{ color: "#17263c" }],
                },
                {
                    featureType: "water",
                    elementType: "labels.text.fill",
                    stylers: [{ color: "#515c6d" }],
                },
                {
                    featureType: "water",
                    elementType: "labels.text.stroke",
                    stylers: [{ color: "#17263c" }],
                },
            ],
        });
        
        // Add a marker for the salon location
        const marker = new google.maps.Marker({
            position: { lat: lat, lng: lng },
            map: map,
            title: mapContainer.dataset.name || 'Pet Grooming Salon'
        });
        
        // Create an info window
        const infoWindow = new google.maps.InfoWindow({
            content: `
                <div class="info-window">
                    <h5>${mapContainer.dataset.name || 'Pet Grooming Salon'}</h5>
                    <p>${mapContainer.dataset.address || ''}</p>
                    <a href="https://www.google.com/maps/dir/?api=1&destination=${lat},${lng}" 
                       target="_blank" class="btn btn-sm btn-primary">Get Directions</a>
                </div>
            `
        });
        
        // Open info window when marker is clicked
        marker.addListener('click', () => {
            infoWindow.open(map, marker);
        });
        
        // Open info window by default
        infoWindow.open(map, marker);
    }
    
    // Call the map initialization function if Google Maps API is available
    if (typeof google !== 'undefined' && google.maps) {
        initMap();
    } else {
        // If Google Maps API is not loaded, create a fallback display
        const fallbackEl = document.createElement('div');
        fallbackEl.className = 'text-center p-4 bg-dark text-light rounded';
        fallbackEl.innerHTML = `
            <p><i class="fas fa-map-marker-alt fa-2x mb-3"></i></p>
            <h5>${mapContainer.dataset.name || 'Pet Grooming Salon'}</h5>
            <p>${mapContainer.dataset.address || ''}</p>
            <a href="https://www.google.com/maps/search/?api=1&query=${lat},${lng}" 
               target="_blank" class="btn btn-primary">View on Google Maps</a>
        `;
        mapContainer.innerHTML = '';
        mapContainer.appendChild(fallbackEl);
    }
});
