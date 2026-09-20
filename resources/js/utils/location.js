/**
 * Utility functions for handling user location
 */

/**
 * Get user location from localStorage
 * @returns {Object|null} Location object with latitude, longitude, accuracy, and timestamp, or null if not found
 */
export const getUserLocation = () => {
  try {
    const locationData = localStorage.getItem('userLocation');
    if (locationData) {
      return JSON.parse(locationData);
    }
    return null;
  } catch (error) {
    console.error('Error getting user location:', error);
    return null;
  }
};

/**
 * Get user location from sessionStorage (faster access)
 * @returns {Object|null} Location object with latitude, longitude, accuracy, and timestamp, or null if not found
 */
export const getUserLocationFromSession = () => {
  try {
    const locationData = sessionStorage.getItem('userLocation');
    if (locationData) {
      return JSON.parse(locationData);
    }
    return null;
  } catch (error) {
    console.error('Error getting user location from session:', error);
    return null;
  }
};

/**
 * Check if user location is available
 * @returns {boolean} True if location is available, false otherwise
 */
export const hasUserLocation = () => {
  return getUserLocation() !== null;
};

/**
 * Clear user location from storage
 */
export const clearUserLocation = () => {
  localStorage.removeItem('userLocation');
  sessionStorage.removeItem('userLocation');
};

/**
 * Request user location using Geolocation API
 * @param {Object} options - Geolocation options (enableHighAccuracy, timeout, maximumAge)
 * @returns {Promise<Object>} Promise that resolves with location data or rejects with error
 */
export const requestUserLocation = (options = {}) => {
  return new Promise((resolve, reject) => {
    if (!navigator.geolocation) {
      reject(new Error('Geolocation is not supported by your browser.'));
      return;
    }

    const defaultOptions = {
      enableHighAccuracy: true,
      timeout: 10000,
      maximumAge: 0
    };

    const geolocationOptions = { ...defaultOptions, ...options };

    navigator.geolocation.getCurrentPosition(
      (position) => {
        const locationData = {
          latitude: position.coords.latitude,
          longitude: position.coords.longitude,
          accuracy: position.coords.accuracy,
          timestamp: new Date().toISOString()
        };

        // Save to storage
        localStorage.setItem('userLocation', JSON.stringify(locationData));
        sessionStorage.setItem('userLocation', JSON.stringify(locationData));

        resolve(locationData);
      },
      (error) => {
        let errorMessage = 'Failed to get your location. ';
        
        switch (error.code) {
          case error.PERMISSION_DENIED:
            errorMessage += 'Please allow location access in your browser settings.';
            break;
          case error.POSITION_UNAVAILABLE:
            errorMessage += 'Location information is unavailable.';
            break;
          case error.TIMEOUT:
            errorMessage += 'The request to get your location timed out.';
            break;
          default:
            errorMessage += 'An unknown error occurred.';
            break;
        }

        reject(new Error(errorMessage));
      },
      geolocationOptions
    );
  });
};

/**
 * Calculate distance between two coordinates using Haversine formula
 * @param {number} lat1 - Latitude of first point
 * @param {number} lon1 - Longitude of first point
 * @param {number} lat2 - Latitude of second point
 * @param {number} lon2 - Longitude of second point
 * @returns {number} Distance in kilometers
 */
export const calculateDistance = (lat1, lon1, lat2, lon2) => {
  const R = 6371; // Radius of the Earth in kilometers
  const dLat = (lat2 - lat1) * Math.PI / 180;
  const dLon = (lon2 - lon1) * Math.PI / 180;
  const a = 
    Math.sin(dLat / 2) * Math.sin(dLat / 2) +
    Math.cos(lat1 * Math.PI / 180) * Math.cos(lat2 * Math.PI / 180) *
    Math.sin(dLon / 2) * Math.sin(dLon / 2);
  const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
  const distance = R * c;
  return distance;
};

