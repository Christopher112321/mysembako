import React, { useCallback, useEffect, useState } from "react";
import { useLocation, useNavigate } from "react-router-dom";

/**
 * TopNavbar component:
 * Navbar dengan logo, search bar, tombol filter & user, serta modal profile & modal filter terpisah.
 * 
 * Props:
 * - userName
 * - onShowProfile
 * - onShowFilter
 * - searchQuery
 * - onSearchChange
 * - selectedCategory
 * - categoryQuery
 * - categoryOptions
 * - onCategorySelect
 * - onCategoryQueryChange
 * - onResetFilter
 */
const TopNavbar = ({
  userName: userNameProp = "Pengguna",
  onShowProfile = () => {},
  onShowFilter = () => {},
  searchQuery = "",
  onSearchChange = () => {},
  selectedCategory = "",
  categoryQuery = "",
  categoryOptions = [],
  onCategorySelect = () => {},
  onCategoryQueryChange = () => {},
  onResetFilter = () => {},
}) => {
  // Hooks
  const location = useLocation();
  const navigate = useNavigate();

  // State
  const [filterPressed, setFilterPressed] = useState(false);
  const [userPressed, setUserPressed] = useState(false);
  const [showProfile, setShowProfile] = useState(false);
  const [showFilter, setShowFilter] = useState(false);
  const [userName, setUserName] = useState(userNameProp);
  const [userData, setUserData] = useState({
    name: userNameProp,
    email: "",
    phone: "",
    location: "",
    role_id: null,
  });
  const [formData, setFormData] = useState({
    name: userNameProp,
    email: "",
    phone: "",
  });
  const [savingProfile, setSavingProfile] = useState(false);
  const [savingLocation, setSavingLocation] = useState(false);
  // Ukuran tombol medium: di-set ke 40px (icon medium) baik mobile maupun desktop
  const [buttonSize, setButtonSize] = useState(40);

  // Mendapatkan path aktif
  const currentPath = location.pathname;

  // Effect: Responsive button size (tapi tetap medium/40px)
  useEffect(() => {
    setButtonSize(40);
    // window resize for future use, but keep 40
    const onResize = () => setButtonSize(40);
    window.addEventListener("resize", onResize);
    return () => window.removeEventListener("resize", onResize);
  }, []);

  // Fetch profil pengguna dari session (API)
  useEffect(() => {
    const fetchUser = async () => {
      try {
        const res = await fetch("/api/user", {
          method: "GET",
          credentials: "same-origin",
          headers: { Accept: "application/json" },
        });

        if (res.ok) {
          const data = await res.json();
          setUserData({
            name: data.name || "Pengguna",
            email: data.email || "",
            phone: data.phone || "",
            location: data.location || "",
            role_id: data.role_id || null,
          });
          setUserName(data.name || "Pengguna");
          setFormData({
            name: data.name || "",
            email: data.email || "",
            phone: data.phone || "",
          });
        }
      } catch (error) {
        console.error("Gagal mengambil profil user:", error);
      }
    };

    fetchUser();
  }, [userNameProp]);

  const handleProfileChange = (field, value) => {
    setFormData((prev) => ({ ...prev, [field]: value }));
  };

  const handleSaveProfile = async () => {
    try {
      setSavingProfile(true);
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
        body: JSON.stringify({
          name: formData.name,
          email: formData.email,
          phone: formData.phone,
        }),
      });

      if (!res.ok) {
        const error = await res.json().catch(() => ({}));
        alert(error.message || "Gagal menyimpan profil");
        return;
      }

      const data = await res.json();
      setUserData((prev) => ({ ...prev, ...data }));
      setUserName(data.name || "Pengguna");
      alert("Profil berhasil diperbarui");
    } catch (error) {
      console.error("Gagal menyimpan profil:", error);
      alert("Terjadi kesalahan saat menyimpan profil");
    } finally {
      setSavingProfile(false);
    }
  };

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

  const handleActivateLocation = () => {
    if (!navigator.geolocation) {
      alert("Geolocation tidak didukung di browser ini.");
      return;
    }

    setSavingLocation(true);
    navigator.geolocation.getCurrentPosition(
      async (pos) => {
        try {
          const { latitude, longitude } = pos.coords;
          const city = cityFromCoords(latitude, longitude);

          if (!city) {
            alert("Lokasi tidak dikenali. Pastikan berada di Jakarta/Semarang/Surabaya/Jogja.");
            return;
          }

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
            alert(error.message || "Gagal menyimpan lokasi");
            return;
          }

          const data = await res.json();
          setUserData((prev) => ({ ...prev, ...data }));
          alert("Lokasi berhasil diperbarui menjadi " + city);
        } catch (err) {
          console.error("Gagal memperbarui lokasi:", err);
          alert("Terjadi kesalahan saat memperbarui lokasi");
        } finally {
          setSavingLocation(false);
        }
      },
      (err) => {
        console.error("Geolocation error:", err);
        alert("Tidak dapat mengakses lokasi. Pastikan izin lokasi diaktifkan.");
        setSavingLocation(false);
      },
      { enableHighAccuracy: true, timeout: 8000, maximumAge: 0 }
    );
  };

  // Handler: Navigasi Logo
  const handleLogoClick = useCallback(() => {
    if (currentPath !== "/") {
      navigate("/");
    }
  }, [currentPath, navigate]);

  // Handler User Button
  const handleUserButtonClick = useCallback(() => {
    if (currentPath === "/profile") {
      return;
    } else {
      setShowProfile(true);
      onShowProfile();
    }
  }, [onShowProfile, currentPath]);

  // Handler Filter Button
  const handleFilterButtonClick = useCallback(() => {
    if (currentPath === "/filter") {
      return;
    } else {
      setShowFilter(true);
      onShowFilter();
    }
  }, [onShowFilter, currentPath]);

  const handleCloseProfile = useCallback(() => setShowProfile(false), []);
  const handleCloseFilter = useCallback(() => setShowFilter(false), []);

  const isProfilePage = currentPath === "/profile";
  const isFilterPage = currentPath === "/filter";

  // Handler Logout: clear local data + hit backend logout
  const handleLogout = async () => {
    // Clear local/session data
    localStorage.removeItem("userName");
    localStorage.removeItem("loggedInUsername");
    sessionStorage.clear();

    try {
      const csrfToken = document
        .querySelector('meta[name="csrf-token"]')
        ?.getAttribute("content");

      await fetch("/logout", {
        method: "POST",
        headers: {
          "X-CSRF-TOKEN": csrfToken || "",
          Accept: "application/json",
        },
        credentials: "same-origin",
      });
    } catch (error) {
      console.error("Logout failed:", error);
    } finally {
      setUserName("Pengguna");
      setShowProfile(false);
      // Gunakan full reload agar halaman login dari Laravel (blade) di-load
      window.location.href = "/login";
    }
  };

  return (
    <header
      className="sticky top-0 w-full flex items-center bg-white border-b border-[#f3f3f3] px-4 py-2 z-[1000] md:px-4 md:py-[10px]"
      style={{ WebkitBackdropFilter: "none", backdropFilter: "none" }}
    >
      <div className="flex w-full items-center justify-between">
        {/* Logo Kiri */}
        <div 
          className="flex items-center flex-shrink-0 cursor-pointer"
          onClick={handleLogoClick}
        >
          <img
            src="/icon/Logo_Mysembako.svg"
            alt="MySembako Logo"
            className="h-9 w-[180px] md:w-[180px] md:h-9 object-contain sm:h-8 sm:w-[136px]"
          />
        </div>

        {/* Area Search + Action Right */}
        <div className="flex items-center gap-4 flex-shrink-0">
          {/* Search Bar */}
          <div className="flex-1 flex justify-center px-4">
            <div className="flex items-center bg-white border border-[#FC6900] rounded-full gap-2 min-w-[120px] py-2 px-4 w-full md:max-w-[400px] md:min-w-[100px] sm:max-w-[180px] sm:text-base"
              style={{ fontSize: "16px" }}
            > 
              <svg width="24" height="24" viewBox="0 0 18 18" fill="none"
                style={{ minWidth: 24, minHeight: 24 }}>
                <path
                  d="M12.5 11H11.71L11.43 10.73C12.41 9.59 13 8.11 13 6.5C13 2.91 10.09 0 6.5 0C2.91 0 0 2.91 0 6.5C0 10.09 2.91 13 6.5 13C8.11 13 9.59 12.41 10.73 11.43L11 11.71V12.5L16 17.49L17.49 16L12.5 11ZM6.5 11C4.01 11 2 8.99 2 6.5C2 4.01 4.01 2 6.5 2C8.99 2 11 4.01 11 6.5C11 8.99 8.99 11 6.5 11Z"
                  fill="#FC6900"
                />
              </svg>
              <input
                className="bg-transparent border-none outline-none flex-1 text-[16px] text-[#333] placeholder:text-[#999] placeholder:opacity-100 focus:ring-0"
                type="text"
                value={searchQuery}
                placeholder="Cari Produk..."
                onChange={e => {
                  if (typeof onSearchChange === 'function') {
                    onSearchChange(e);
                  }
                }}
                style={{ minHeight: 32 }}
                autoComplete="off"
              />
            </div>
          </div>

          {/* Tombol Filter */}
          <button
            className={`transition-all duration-200 flex items-center justify-center rounded-full border-none active:scale-95
              ${isFilterPage ? "ring-2 ring-[#F98D0A] scale-110 bg-[#FC6900] shadow-lg" : ""}
            `}
            style={{
              background: isFilterPage
                ? "#FC6900"
                : filterPressed
                  ? "#FFD4A3"
                  : "#FFE5D0",
              width: buttonSize,
              height: buttonSize,
              color: isFilterPage ? "#fff" : "#E65100",
              boxShadow: isFilterPage
                ? "0 4px 16px 0 #FFD4A355"
                : undefined
            }}
            onMouseDown={() => setFilterPressed(true)}
            onMouseUp={() => setFilterPressed(false)}
            onMouseLeave={() => setFilterPressed(false)}
            onClick={() => {
              if (isFilterPage) return;
              handleFilterButtonClick();
            }}
            aria-label="Filter"
            type="button"
            disabled={isFilterPage}
          >
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none">
              <path d="M4 6H20" stroke={isFilterPage ? "#fff" : "#E65100"} strokeWidth="2.5" strokeLinecap="round" />
              <circle cx="8" cy="6" r="2" fill={isFilterPage ? "#fff" : "#E65100"} />
              <path d="M4 12H20" stroke={isFilterPage ? "#fff" : "#E65100"} strokeWidth="2.5" strokeLinecap="round" />
              <circle cx="16" cy="12" r="2" fill={isFilterPage ? "#fff" : "#E65100"} />
              <path d="M4 18H20" stroke={isFilterPage ? "#fff" : "#E65100"} strokeWidth="2.5" strokeLinecap="round" />
              <circle cx="8" cy="18" r="2" fill={isFilterPage ? "#fff" : "#E65100"} />
            </svg>
          </button>

          {/* Tombol User */}
          <button
            className={`transition-all duration-200 flex items-center justify-center rounded-full border-none active:scale-95
              ${isProfilePage ? "ring-2 ring-[#F98D0A] scale-110 bg-[#FC6900] shadow-lg" : ""}
            `}
            style={{
              background: isProfilePage
                ? "#FC6900"
                : userPressed
                  ? "#FFD4A3"
                  : "#FFE5D0",
              width: buttonSize,
              height: buttonSize,
              color: isProfilePage ? "#fff" : "#E65100",
              boxShadow: isProfilePage
                ? "0 4px 16px 0 #FFD4A355"
                : undefined
            }}
            onMouseDown={() => setUserPressed(true)}
            onMouseUp={() => setUserPressed(false)}
            onMouseLeave={() => setUserPressed(false)}
            onClick={() => {
              if (isProfilePage) return;
              handleUserButtonClick();
            }}
            aria-label="Profil Pengguna"
            type="button"
            disabled={isProfilePage}
          >
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none">
              <path
                d="M20 21V19C20 17.9391 19.5786 16.9217 18.8284 16.1716C18.0783 15.4214 17.0609 15 16 15H8C6.93913 15 5.92172 15.4214 5.17157 16.1716C4.42143 16.9217 4 17.9391 4 19V21"
                stroke={isProfilePage ? "#fff" : "#E65100"}
                strokeWidth="2"
                strokeLinecap="round"
                strokeLinejoin="round"
                fill="none"
              />
              <circle
                cx="12"
                cy="7"
                r="4"
                stroke={isProfilePage ? "#fff" : "#E65100"}
                strokeWidth="2"
                strokeLinecap="round"
                strokeLinejoin="round"
                fill="none"
              />
            </svg>
          </button>
        </div>
      </div>

      {/* Modal Profil */}
      {showProfile && (
        <div
          className="fixed inset-0 bg-black/50 z-[1000] flex items-center justify-center p-5"
          onClick={handleCloseProfile}
        >
          <div
            className="bg-white rounded-2xl p-6 max-w-[400px] w-full shadow-lg"
            onClick={e => e.stopPropagation()}
          >
            <div className="flex justify-between items-center mb-5">
              <h2 className="text-[#E65100] m-0 text-xl font-bold">
                Profil Saya
              </h2>
              <button
                onClick={handleCloseProfile}
                className="bg-transparent border-none text-2xl cursor-pointer text-[#999] p-0 w-[30px] h-[30px] flex items-center justify-center"
              >
                ×
              </button>
            </div>

            <div className="text-center mb-6">
              <div className="w-[100px] h-[100px] rounded-full bg-[#FFE5D0] flex items-center justify-center mx-auto mb-4">
                <svg width="50" height="50" viewBox="0 0 24 24" fill="none">
                  <path
                    d="M20 21V19C20 17.9391 19.5786 16.9217 18.8284 16.1716C18.0783 15.4214 17.0609 15 16 15H8C6.93913 15 5.92172 15.4214 5.17157 16.1716C4.42143 16.9217 4 17.9391 4 19V21"
                    stroke="#E65100"
                    strokeWidth="2"
                    strokeLinecap="round"
                    strokeLinejoin="round"
                  />
                  <circle
                    cx="12"
                    cy="7"
                    r="4"
                    stroke="#E65100"
                    strokeWidth="2"
                    strokeLinecap="round"
                    strokeLinejoin="round"
                  />
                </svg>
              </div>
              <h3 className="text-[#333] m-0 mb-2 text-lg font-semibold">
                {userData.name || "Pengguna"}
              </h3>
              <p className="text-[#999] m-0 text-sm">Anggota MySembako</p>
            </div>

            <div className="mb-4 space-y-4">
              <div>
                <label className="block mb-2 text-[#333] text-sm font-medium">
                  Nama
                </label>
                <input
                  type="text"
                  value={formData.name || ""}
                  onChange={e => handleProfileChange("name", e.target.value)}
                  className="w-full p-3 border border-[#ddd] rounded-lg text-sm outline-none"
                  placeholder="Masukkan nama Anda"
                />
              </div>
              <div>
                <label className="block mb-2 text-[#333] text-sm font-medium">
                  Email
                </label>
                <input
                  type="email"
                  value={formData.email || ""}
                  onChange={e => handleProfileChange("email", e.target.value)}
                  className="w-full p-3 border border-[#ddd] rounded-lg text-sm outline-none"
                  placeholder="Masukkan email Anda"
                />
              </div>
              <div>
                <label className="block mb-2 text-[#333] text-sm font-medium">
                  Nomor Telepon
                </label>
                <input
                  type="text"
                  value={formData.phone || ""}
                  onChange={e => handleProfileChange("phone", e.target.value)}
                  className="w-full p-3 border border-[#ddd] rounded-lg text-sm outline-none"
                  placeholder="Masukkan nomor telepon"
                />
              </div>
              <div>
                <label className="block mb-2 text-[#333] text-sm font-medium">
                  Kota (dibaca otomatis)
                </label>
                <input
                  type="text"
                  value={userData.location || ""}
                  readOnly
                  className="w-full p-3 border border-[#ddd] rounded-lg text-sm outline-none bg-gray-50"
                  placeholder="Belum ada lokasi"
                />
                <button
                  type="button"
                  onClick={handleActivateLocation}
                  className="mt-2 w-full p-3 bg-[#E65100] text-white border-none rounded-lg text-base font-bold cursor-pointer disabled:opacity-70"
                  disabled={savingLocation}
                >
                  {savingLocation ? "Mengambil lokasi..." : "Aktifkan lokasi"}
                </button>
              </div>
            </div>

            <div className="flex flex-col gap-3">
              <button
                onClick={handleSaveProfile}
                className="w-full p-3 bg-[#E65100] text-white border-none rounded-lg text-base font-bold cursor-pointer disabled:opacity-70"
                disabled={savingProfile}
              >
                {savingProfile ? "Menyimpan..." : "Simpan Profil"}
              </button>
              <button
                onClick={handleLogout}
                className="w-full p-3 bg-red-500 hover:bg-red-600 text-white border-none rounded-lg text-base font-bold cursor-pointer mt-2 transition"
                style={{ marginTop: 8 }}
              >
                Logout
              </button>
            </div>
          </div>
        </div>
      )}

      {/* Modal Filter */}
      {showFilter && (
        <div
          className="fixed inset-0 bg-black/50 z-[1000] flex items-center justify-center p-5"
          onClick={handleCloseFilter}
        >
          <div
            className="bg-white rounded-2xl p-6 max-w-[400px] w-full shadow-lg"
            onClick={e => e.stopPropagation()}
          >
            <div className="flex justify-between items-center mb-5">
              <h2 className="text-[#E65100] m-0 text-xl font-bold">
                Filter & Sort
              </h2>
              <button
                onClick={handleCloseFilter}
                className="bg-transparent border-none text-2xl cursor-pointer text-[#999] p-0 w-[30px] h-[30px] flex items-center justify-center"
              >
                ×
              </button>
            </div>

            <div className="mb-5">
              <label className="block mb-2 text-[#333] text-sm font-medium">
                Urutkan Berdasarkan
              </label>
              <select className="w-full p-3 border border-[#ddd] rounded-lg text-sm outline-none">
                <option>Harga Terendah</option>
                <option>Harga Tertinggi</option>
                <option>Nama A-Z</option>
                <option>Nama Z-A</option>
                <option>Terbaru</option>
              </select>
            </div>

            <div className="mb-5">
              <label className="block mb-2 text-[#333] text-sm font-medium">
                Filter Kategori
              </label>
              <div className="flex flex-wrap gap-2 mb-3">
                {categoryOptions.slice(0, 10).map((cat) => {
                  const isActive = selectedCategory === cat;
                  return (
                    <button
                      key={cat}
                      type="button"
                      onClick={() =>
                        typeof onCategorySelect === "function" &&
                        onCategorySelect(cat)
                      }
                      className={`px-3 py-1.5 rounded-full text-sm border transition ${
                        isActive
                          ? "bg-orange-500 text-white border-orange-500"
                          : "bg-white text-gray-700 border-orange-200 hover:border-orange-400"
                      }`}
                    >
                      {cat}
                    </button>
                  );
                })}
              </div>
              <input
                type="text"
                list="topnav-category-list"
                value={categoryQuery}
                onChange={(e) =>
                  typeof onCategoryQueryChange === "function" &&
                  onCategoryQueryChange(e.target.value)
                }
                className="w-full p-3 border border-[#ddd] rounded-lg text-sm outline-none"
                placeholder="Cari kategori lain atau ketik sendiri"
              />
              <datalist id="topnav-category-list">
                {categoryOptions.map((cat) => (
                  <option key={cat} value={cat}></option>
                ))}
              </datalist>
              <p className="text-xs text-gray-500 mt-2">
                Pilih kategori populer atau ketik kategori lain jika tidak ada di daftar.
              </p>
            </div>

            <div className="flex gap-3">
              <button
                onClick={() => {
                  handleCloseFilter();
                }}
                className="flex-1 p-3 bg-[#E65100] text-white border-none rounded-lg text-base font-bold cursor-pointer"
              >
                Terapkan
              </button>
              <button
                onClick={() => {
                  if (typeof onResetFilter === "function") {
                    onResetFilter();
                  }
                  handleCloseFilter();
                }}
                className="flex-1 p-3 bg-transparent text-[#E65100] border border-[#E65100] rounded-lg text-base font-bold cursor-pointer"
              >
                Reset
              </button>
            </div>
          </div>
        </div>
      )}
    </header>
  );
};

export default TopNavbar;
