import React, { useState, useEffect } from "react";
import TopNavbar from "../../components/commons/molecules/TopNavbar";
import BottomNavbar from "../../components/commons/molecules/BottomNavbar";

const ProfilePage = () => {
  const [userData, setUserData] = useState({
    name: "",
    email: "",
    phone: "",
    location: "",
  });
  const [formData, setFormData] = useState({
    name: "",
    email: "",
    phone: "",
  });
  const [loading, setLoading] = useState(true);
  const [savingProfile, setSavingProfile] = useState(false);
  const [savingLocation, setSavingLocation] = useState(false);
  const [loggingOut, setLoggingOut] = useState(false);
  const [message, setMessage] = useState({ type: "", text: "" });

  useEffect(() => {
    const fetchUser = async () => {
      try {
        setLoading(true);
        const res = await fetch("/api/user", {
          headers: { Accept: "application/json" },
          credentials: "same-origin",
        });
        if (res.ok) {
          const data = await res.json();
          setUserData(data);
          setFormData({
            name: data.name || "",
            email: data.email || "",
            phone: data.phone || "",
          });
        }
      } catch (err) {
        console.error("Gagal mengambil data user:", err);
      } finally {
        setLoading(false);
      }
    };

    fetchUser();
  }, []);

  const handleChange = (field, value) => {
    setFormData((prev) => ({ ...prev, [field]: value }));
  };

  const handleSaveProfile = async (e) => {
    e.preventDefault();
    try {
      setSavingProfile(true);
      setMessage({ type: "", text: "" });
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
        body: JSON.stringify(formData),
      });

      if (!res.ok) {
        const error = await res.json().catch(() => ({}));
        setMessage({
          type: "error",
          text: error.message || "Gagal menyimpan perubahan profil.",
        });
        return;
      }

      const data = await res.json();
      setUserData((prev) => ({ ...prev, ...data }));
      localStorage.setItem("userName", data.name);
      setMessage({
        type: "success",
        text: "Profil Anda berhasil diperbarui!",
      });
    } catch (err) {
      console.error("Gagal menyimpan profil:", err);
      setMessage({
        type: "error",
        text: "Terjadi kesalahan saat menyimpan profil.",
      });
    } finally {
      setSavingProfile(false);
    }
  };

  const handleActivateLocation = () => {
    if (!navigator.geolocation) {
      alert("Geolocation tidak didukung pada browser ini.");
      return;
    }

    setSavingLocation(true);
    navigator.geolocation.getCurrentPosition(
      async (pos) => {
        try {
          const { latitude, longitude } = pos.coords;
          const boxes = [
            { name: "Jakarta", lat: [-6.4, -6.0], lng: [106.6, 107.0] },
            { name: "Semarang", lat: [-7.1, -6.8], lng: [110.3, 110.6] },
            { name: "Surabaya", lat: [-7.4, -7.1], lng: [112.6, 112.9] },
            { name: "Jogja", lat: [-7.9, -7.6], lng: [110.3, 110.6] },
          ];
          const match = boxes.find(
            (b) =>
              latitude >= b.lat[0] &&
              latitude <= b.lat[1] &&
              longitude >= b.lng[0] &&
              longitude <= b.lng[1]
          );
          const city = match ? match.name : "Lokasi Terdeteksi";

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

          if (res.ok) {
            const data = await res.json();
            setUserData((prev) => ({ ...prev, ...data }));
            setMessage({
              type: "success",
              text: `Lokasi berhasil diperbarui: ${city}`,
            });
          }
        } catch (err) {
          console.error("Gagal memperbarui lokasi:", err);
        } finally {
          setSavingLocation(false);
        }
      },
      (err) => {
        console.error("Geolocation error:", err);
        alert("Izin lokasi tidak diberikan.");
        setSavingLocation(false);
      },
      { enableHighAccuracy: true, timeout: 8000, maximumAge: 0 }
    );
  };

  const handleLogout = async () => {
    if (!window.confirm("Apakah Anda yakin ingin keluar dari akun?")) {
      return;
    }

    setLoggingOut(true);
    try {
      const csrfToken = document
        .querySelector('meta[name="csrf-token"]')
        ?.getAttribute("content");

      await fetch("/logout", {
        method: "POST",
        headers: {
          "X-CSRF-TOKEN": csrfToken || "",
          Accept: "application/json",
          "Content-Type": "application/json",
        },
        credentials: "same-origin",
      });
    } catch (err) {
      console.error("Logout request failed:", err);
    } finally {
      localStorage.removeItem("userName");
      localStorage.removeItem("loggedInUsername");
      sessionStorage.clear();
      window.location.href = "/login";
    }
  };

  return (
    <div className="min-h-screen bg-gray-50 pb-28">
      <TopNavbar />

      <main className="max-w-xl mx-auto px-4 py-8">
        <div className="bg-white rounded-3xl shadow-sm border border-gray-100 p-6 sm:p-8">
          {/* Header Card Profil */}
          <div className="flex flex-col items-center text-center pb-6 border-b border-gray-100">
            <div className="w-24 h-24 rounded-full bg-orange-100 border-4 border-orange-50 flex items-center justify-center mb-4 shadow-inner">
              <svg
                width="48"
                height="48"
                viewBox="0 0 24 24"
                fill="none"
                stroke="#FE5A19"
                strokeWidth="2"
                strokeLinecap="round"
                strokeLinejoin="round"
              >
                <path d="M20 21V19C20 17.9391 19.5786 16.9217 18.8284 16.1716C18.0783 15.4214 17.0609 15 16 15H8C6.93913 15 5.92172 15.4214 5.17157 16.1716C4.42143 16.9217 4 17.9391 4 19V21" />
                <circle cx="12" cy="7" r="4" />
              </svg>
            </div>
            <h1 className="text-2xl font-bold text-gray-900 mb-1">
              {userData.name || "Pengguna MySembako"}
            </h1>
            <p className="text-sm text-gray-500 mb-2">
              {userData.email || "Memuat email..."}
            </p>
            <span className="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-orange-100 text-orange-800">
              Pelanggan MySembako
            </span>
          </div>

          {message.text && (
            <div
              className={`mt-4 p-4 rounded-xl text-sm ${
                message.type === "success"
                  ? "bg-green-50 text-green-700 border border-green-200"
                  : "bg-red-50 text-red-700 border border-red-200"
              }`}
            >
              {message.text}
            </div>
          )}

          {/* Form Informasi Profil */}
          <form onSubmit={handleSaveProfile} className="mt-6 space-y-4">
            <div>
              <label className="block text-sm font-semibold text-gray-700 mb-1.5">
                Nama Lengkap
              </label>
              <input
                type="text"
                value={formData.name}
                onChange={(e) => handleChange("name", e.target.value)}
                required
                className="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-orange-500 focus:ring-2 focus:ring-orange-200 outline-none text-sm transition"
                placeholder="Nama lengkap Anda"
              />
            </div>

            <div>
              <label className="block text-sm font-semibold text-gray-700 mb-1.5">
                Alamat Email
              </label>
              <input
                type="email"
                value={formData.email}
                onChange={(e) => handleChange("email", e.target.value)}
                required
                className="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-orange-500 focus:ring-2 focus:ring-orange-200 outline-none text-sm transition"
                placeholder="Email aktif"
              />
            </div>

            <div>
              <label className="block text-sm font-semibold text-gray-700 mb-1.5">
                Nomor Telepon / WhatsApp
              </label>
              <input
                type="tel"
                value={formData.phone}
                onChange={(e) => handleChange("phone", e.target.value)}
                className="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-orange-500 focus:ring-2 focus:ring-orange-200 outline-none text-sm transition"
                placeholder="Contoh: 081234567890"
              />
            </div>

            <div>
              <label className="block text-sm font-semibold text-gray-700 mb-1.5">
                Kota Operasional (Toko Terdekat)
              </label>
              <div className="flex gap-2">
                <input
                  type="text"
                  value={userData.location || "Belum ada lokasi"}
                  readOnly
                  className="w-full px-4 py-3 rounded-xl border border-gray-200 bg-gray-50 text-gray-700 text-sm outline-none"
                />
                <button
                  type="button"
                  onClick={handleActivateLocation}
                  disabled={savingLocation}
                  className="px-4 py-3 bg-orange-100 hover:bg-orange-200 text-orange-700 rounded-xl text-sm font-semibold whitespace-nowrap transition disabled:opacity-50"
                >
                  {savingLocation ? "Mendeteksi..." : "📍 Lokasi Saya"}
                </button>
              </div>
            </div>

            <div className="pt-4">
              <button
                type="submit"
                disabled={savingProfile}
                className="w-full py-3.5 px-4 bg-[#FE5A19] hover:bg-[#d44800] text-white font-bold rounded-2xl shadow-md transition-all duration-300 disabled:opacity-70 cursor-pointer"
              >
                {savingProfile ? "Menyimpan Perubahan..." : "Simpan Perubahan Profil"}
              </button>
            </div>
          </form>

          {/* Section Logout */}
          <div className="mt-8 pt-6 border-t border-gray-100">
            <h2 className="text-sm font-semibold text-gray-500 uppercase tracking-wider mb-3">
              Kelola Sesi Akun
            </h2>
            <button
              type="button"
              onClick={handleLogout}
              disabled={loggingOut}
              className="w-full py-3.5 px-4 bg-red-600 hover:bg-red-700 text-white font-bold rounded-2xl flex items-center justify-center gap-2 shadow-sm transition cursor-pointer disabled:opacity-50"
            >
              <svg
                width="20"
                height="20"
                viewBox="0 0 24 24"
                fill="none"
                stroke="currentColor"
                strokeWidth="2.5"
                strokeLinecap="round"
                strokeLinejoin="round"
              >
                <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4" />
                <polyline points="16 17 21 12 16 7" />
                <line x1="21" y1="12" x2="9" y2="12" />
              </svg>
              {loggingOut ? "Sedang Keluar..." : "Keluar / Logout dari Akun"}
            </button>
          </div>
        </div>
      </main>

      <BottomNavbar />
    </div>
  );
};

export default ProfilePage;
