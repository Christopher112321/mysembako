import React, { useEffect, useMemo, useState } from "react";
import TopNavbar from "../../components/commons/molecules/TopNavbar";
import BottomNavbar from "../../components/commons/molecules/BottomNavbar";
import { requestUserLocation } from "../../utils/location";

// Gambar ilustrasi location (URL online, atau bisa import asset lokal)
const illustrationUrl =
  "https://cdn-icons-png.flaticon.com/512/535/535239.png"; // Ganti dengan ilustrasi sesuai desain

const LocationPage = () => {
  const [showLocationPopup, setShowLocationPopup] = useState(false);
  const [locationData, setLocationData] = useState(null);
  const [address, setAddress] = useState("");
  const [userCity, setUserCity] = useState("");
  const [loading, setLoading] = useState(true);
  const [savingLocation, setSavingLocation] = useState(false);
  const [products, setProducts] = useState([]);
  const [loadingProducts, setLoadingProducts] = useState(false);

  // Reverse geocoding untuk mendapatkan alamat dari koordinat
  const getAddressFromCoordinates = async (lat, lng) => {
    try {
      const response = await fetch(
        `https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lng}&zoom=18&addressdetails=1`
      );
      const data = await response.json();
      if (data && data.display_name) {
        return data.display_name;
      }
      return null;
    } catch (error) {
      console.error("Error getting address:", error);
      return null;
    }
  };

  // Pemetaan koordinat kasar ke kota yang didukung
  const cityFromCoords = (lat, lng) => {
    const boxes = [
      { name: "Jakarta", lat: [-6.4, -6.0], lng: [106.6, 107.0] },
      { name: "Semarang", lat: [-7.1, -6.8], lng: [110.3, 110.6] },
      { name: "Surabaya", lat: [-7.4, -7.1], lng: [112.6, 112.9] },
      { name: "Jogja", lat: [-7.9, -7.6], lng: [110.3, 110.6] },
    ];
    const match = boxes.find(
      (b) => lat >= b.lat[0] && lat <= b.lat[1] && lng >= b.lng[0] && lng <= b.lng[1]
    );
    return match ? match.name : null;
  };

  // Ambil data user untuk cek kota tersimpan
  useEffect(() => {
    const fetchUser = async () => {
      try {
        const res = await fetch("/api/user", {
          method: "GET",
          headers: { Accept: "application/json" },
          credentials: "same-origin",
        });
        if (res.ok) {
          const data = await res.json();
          setUserCity(data.location || "");
        }
      } catch (err) {
        console.error("Gagal memuat user:", err);
      } finally {
        setLoading(false);
      }
    };
    fetchUser();
  }, []);

  // Ambil produk seperti /home
  useEffect(() => {
    const fetchProducts = async () => {
      try {
        setLoadingProducts(true);
        const res = await fetch("/api/products", {
          headers: { Accept: "application/json" },
        });
        if (res.ok) {
          const data = await res.json();
          setProducts(Array.isArray(data) ? data : []);
        } else {
          setProducts([]);
        }
      } catch (err) {
        console.error("Gagal memuat produk:", err);
        setProducts([]);
      } finally {
        setLoadingProducts(false);
      }
    };

    fetchProducts();
  }, []);

  const filteredProducts = useMemo(() => {
    if (!userCity) return products;
    const cityLower = userCity.toLowerCase();
    const matchCity = (p) => {
      const candidates = [
        p.city,
        p.location,
        p.store_city,
        p.storeCity,
        p.store_location,
        p.storeLocation,
        p.store,
      ]
        .filter(Boolean)
        .map((s) => String(s).toLowerCase());
      return candidates.some((c) => c.includes(cityLower));
    };
    const result = products.filter((p) => matchCity(p));
    return result.length ? result : products;
  }, [products, userCity]);

  const saveCityToBackend = async (city) => {
    const csrfToken = document
      .querySelector('meta[name="csrf-token"]')
      ?.getAttribute("content");
    const res = await fetch("/api/user", {
      method: "PATCH",
      headers: {
        "Content-Type": "application/json",
        "X-CSRF-TOKEN": csrfToken || "",
        Accept: "application/json",
      },
      credentials: "same-origin",
      body: JSON.stringify({ location: city }),
    });
    if (!res.ok) {
      const error = await res.json().catch(() => ({}));
      throw new Error(error.message || "Gagal menyimpan lokasi");
    }
    const data = await res.json();
    setUserCity(data.location || city);
    return data;
  };

  const handleTurnOnLocation = async () => {
    if (!navigator.geolocation) {
      alert("Geolocation is not supported by your browser.");
      return;
    }

    try {
      setSavingLocation(true);
      const location = await requestUserLocation();
      setLocationData(location);

      const city = cityFromCoords(location.latitude, location.longitude);
      if (city) {
        await saveCityToBackend(city);
      }

      // Coba dapatkan alamat dari koordinat
      const addr = await getAddressFromCoordinates(
        location.latitude,
        location.longitude
      );
      if (addr) {
        setAddress(addr);
      }

      setShowLocationPopup(true);
    } catch (error) {
      alert(error.message || "Location permission denied or unavailable.");
    } finally {
      setSavingLocation(false);
    }
  };

  const closeLocationPopup = () => {
    setShowLocationPopup(false);
    // Redirect ke home setelah menutup popup
    setTimeout(() => {
      window.location.href = "/home";
    }, 300);
  };

  return (
    <div
      style={{
        background: "#fff",
        minHeight: "100vh",
        display: "flex",
        flexDirection: "column",
        justifyContent: "space-between"
      }}
    >
      {/* Top Navbar */}
      <TopNavbar />

      {/* Konten utama */}
      <div
        style={{
          flex: 1,
          display: "flex",
          flexDirection: "column",
          alignItems: "center",
          justifyContent: "center",
          minHeight: "400px",
          padding: "16px",
        }}
      >
        <div
          style={{
            marginBottom: "24px",
          }}
        >
          {/* Ilustrasi */}
          <img
            src="/icon/Location.svg"
            alt="Location Illustration"
            style={{ width: 237, height: 210, margin: "0 auto" }}
          />
        </div>

        {/* Jika belum ada kota tersimpan */}
        {!userCity && !loading && (
          <>
            {/* Judul */}
            <div
              style={{
                fontWeight: 700,
                fontSize: "1.17rem",
                textAlign: "center",
                marginBottom: 8,
                color: "#181818",
              }}
            >
              Allow Your Location
            </div>

            {/* Deskripsi */}
            <div
              style={{
                textAlign: "center",
                fontSize: 15,
                color: "#222222",
                marginBottom: 26,
                maxWidth: 290,
                lineHeight: "20px",
              }}
            >
              We'll use your location to recommend
              <br />
              nearest shops around you
            </div>

            {/* Tombol aktifkan lokasi */}
            <button
              style={{
                display: "block",
                background: "#BF4413",
                color: "#fff",
                border: "none",
                borderRadius: 24,
                padding: "11px 32px",
                fontSize: 15,
                fontWeight: 600,
                cursor: "pointer",
                margin: "0 auto 10px auto",
                width: 200,
                boxShadow: "0 2px 10px rgba(191, 68, 19, 0.10)"
              }}
              onClick={handleTurnOnLocation}
              disabled={savingLocation}
            >
              {savingLocation ? "Processing..." : "Turn On Location"}
            </button>

            {/* Maybe Later */}
            <div
              style={{
                color: "#BF4413",
                opacity: 0.7,
                textAlign: "center",
                fontSize: 15,
                marginTop: 2,
                cursor: "pointer",
                userSelect: "none"
              }}
              onClick={() => {
                window.location.href = "/home";
              }}
            >
              Maybe Later
            </div>
          </>
        )}

        {/* Jika sudah ada kota tersimpan */}
        {userCity && (
          <div style={{ textAlign: "center", maxWidth: 900, width: "100%" }}>
            <div
              style={{
                fontWeight: 700,
                fontSize: "1.17rem",
                marginBottom: 8,
                color: "#181818",
              }}
            >
              Toko terdekat sesuai lokasi anda
            </div>
            <div style={{ color: "#444", marginBottom: 16 }}>
              {address
                ? address
                : `Menampilkan rekomendasi berdasarkan kota ${userCity}`}
            </div>
            {/* Grid produk mirip /home */}
            <div className="max-w-7xl mx-auto px-2 md:px-4 w-full">
              {loadingProducts ? (
                <div style={{ padding: 20, textAlign: "center", color: "#888" }}>
                  Memuat produk...
                </div>
              ) : (
                <div className="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-6 md:gap-7">
                  {filteredProducts.length === 0 && (
                    <div className="col-span-full text-center text-gray-400 py-10">
                      Tidak ada produk ditemukan untuk kota {userCity}
                    </div>
                  )}
                  {filteredProducts.map((product, idx) => (
                    <div
                      key={product.id || idx}
                      className="flex flex-col h-full bg-white border border-gray-200 rounded-2xl shadow-sm px-3.5 py-4 md:px-4 md:py-5 mb-3"
                    >
                      <div className="flex justify-center items-start mb-3 min-h-[85px] md:min-h-[110px]">
                        <img
                          src={product.img || product.image || product.foto || "/icon/Logo_Mysembako.svg"}
                          alt={product.name || product.nama_produk}
                          className="h-[70px] md:h-[90px] object-contain"
                        />
                      </div>
                      <div className="flex-grow flex flex-col justify-between">
                        <div className="line-clamp-2 font-semibold text-gray-900 text-xs md:text-base leading-snug mb-1 md:mb-1.5 truncate">
                          {product.name || product.nama_produk}
                        </div>
                        <div className="text-sm md:text-lg font-bold text-gray-900 mb-1 md:mb-2">
                          {product.price
                            ? `Rp ${Number(product.price)
                                .toString()
                                .replace(/\B(?=(\d{3})+(?!\d))/g, ".")}`
                            : product.harga
                              ? `Rp ${Number(product.harga)
                                  .toString()
                                  .replace(/\B(?=(\d{3})+(?!\d))/g, ".")}`
                              : "-"}
                        </div>
                      </div>
                      <div className="text-xs md:text-sm text-gray-400 leading-tight truncate">
                        {product.store || product.toko || userCity}
                      </div>
                    </div>
                  ))}
                </div>
              )}
            </div>

            <div style={{ marginTop: 18 }}>
              <button
                style={{
                  background: "#BF4413",
                  color: "#fff",
                  border: "none",
                  borderRadius: 24,
                  padding: "11px 24px",
                  fontSize: 15,
                  fontWeight: 600,
                  cursor: "pointer",
                  boxShadow: "0 2px 10px rgba(191, 68, 19, 0.10)"
                }}
                onClick={() => setShowLocationPopup(true)}
              >
                Lihat detail lokasi
              </button>
            </div>
          </div>
        )}
      </div>

      {/* Bottom Navbar */}
      <div style={{ paddingBottom: 20, marginTop: "auto" }}>
        <BottomNavbar />
      </div>

      {/* Popup Lokasi User */}
      {showLocationPopup && locationData && (
        <div
          style={{
            position: "fixed",
            top: 0,
            left: 0,
            right: 0,
            bottom: 0,
            backgroundColor: "rgba(0, 0, 0, 0.5)",
            zIndex: 1000,
            display: "flex",
            alignItems: "center",
            justifyContent: "center",
            padding: "20px",
          }}
          onClick={closeLocationPopup}
        >
          <div
            style={{
              backgroundColor: "#fff",
              borderRadius: "16px",
              padding: "24px",
              maxWidth: "400px",
              width: "100%",
              boxShadow: "0 4px 20px rgba(0, 0, 0, 0.15)",
            }}
            onClick={(e) => e.stopPropagation()}
          >
            {/* Header */}
            <div
              style={{
                display: "flex",
                justifyContent: "space-between",
                alignItems: "center",
                marginBottom: "20px",
              }}
            >
              <h2
                style={{
                  margin: 0,
                  fontSize: "1.25rem",
                  fontWeight: 700,
                  color: "#181818",
                }}
              >
                Lokasi User Terkini
              </h2>
              <button
                onClick={closeLocationPopup}
                style={{
                  background: "transparent",
                  border: "none",
                  fontSize: "28px",
                  cursor: "pointer",
                  color: "#999",
                  padding: 0,
                  width: "30px",
                  height: "30px",
                  display: "flex",
                  alignItems: "center",
                  justifyContent: "center",
                }}
              >
                ×
              </button>
            </div>

            {/* Icon Lokasi */}
            <div
              style={{
                display: "flex",
                justifyContent: "center",
                marginBottom: "20px",
              }}
            >
              <div
                style={{
                  width: "80px",
                  height: "80px",
                  borderRadius: "50%",
                  backgroundColor: "#FFE5D0",
                  display: "flex",
                  alignItems: "center",
                  justifyContent: "center",
                }}
              >
                <svg
                  width="40"
                  height="40"
                  viewBox="0 0 24 24"
                  fill="none"
                  xmlns="http://www.w3.org/2000/svg"
                >
                  <path
                    d="M12 2C8.13 2 5 5.13 5 9C5 14.25 12 22 12 22C12 22 19 14.25 19 9C19 5.13 15.87 2 12 2ZM12 11.5C10.62 11.5 9.5 10.38 9.5 9C9.5 7.62 10.62 6.5 12 6.5C13.38 6.5 14.5 7.62 14.5 9C14.5 10.38 13.38 11.5 12 11.5Z"
                    fill="#BF4413"
                  />
                </svg>
              </div>
            </div>

            {/* Informasi Lokasi */}
            <div style={{ marginBottom: "20px" }}>
              {address && (
                <div style={{ marginBottom: "16px" }}>
                  <div
                    style={{
                      fontSize: "12px",
                      color: "#666",
                      marginBottom: "4px",
                      fontWeight: 600,
                    }}
                  >
                    Alamat:
                  </div>
                  <div
                    style={{
                      fontSize: "14px",
                      color: "#222",
                      lineHeight: "1.5",
                    }}
                  >
                    {address}
                  </div>
                </div>
              )}

              <div style={{ marginBottom: "12px" }}>
                <div
                  style={{
                    fontSize: "12px",
                    color: "#666",
                    marginBottom: "4px",
                    fontWeight: 600,
                  }}
                >
                  Koordinat:
                </div>
                <div
                  style={{
                    fontSize: "14px",
                    color: "#222",
                    fontFamily: "monospace",
                  }}
                >
                  Lat: {locationData.latitude.toFixed(6)}
                  <br />
                  Lng: {locationData.longitude.toFixed(6)}
                </div>
              </div>

              {locationData.accuracy && (
                <div>
                  <div
                    style={{
                      fontSize: "12px",
                      color: "#666",
                      marginBottom: "4px",
                      fontWeight: 600,
                    }}
                  >
                    Akurasi:
                  </div>
                  <div style={{ fontSize: "14px", color: "#222" }}>
                    ±{Math.round(locationData.accuracy)} meter
                  </div>
                </div>
              )}
            </div>

            {/* Tombol OK */}
            <button
              onClick={closeLocationPopup}
              style={{
                width: "100%",
                background: "#BF4413",
                color: "#fff",
                border: "none",
                borderRadius: "24px",
                padding: "12px",
                fontSize: "15px",
                fontWeight: 600,
                cursor: "pointer",
                boxShadow: "0 2px 10px rgba(191, 68, 19, 0.20)",
              }}
            >
              OK
            </button>
          </div>
        </div>
      )}
    </div>
  );
};

export default LocationPage;
