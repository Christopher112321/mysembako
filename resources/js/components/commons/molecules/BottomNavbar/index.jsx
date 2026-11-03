import React from "react";
import { useNavigate, useLocation } from "react-router-dom";

const BottomNavbar = () => {
  const navigate = useNavigate();
  const location = useLocation();

  // Tentukan activeTab berdasarkan lokasi path saat ini
  const getActiveTab = () => {
    if (location.pathname.startsWith("/cart")) {
      return "cart";
    }
    if (location.pathname.startsWith("/receipt") || location.pathname.startsWith("/orders")) {
      // Sesuaikan jika route "orders" juga perlu aktif untuk receipt
      return "receipt";
    }
    if (location.pathname.startsWith("/location")) {
      return "location";
    }
    if (location.pathname.startsWith("/profile")) {
      return "profile";
    }
    // Default to home jika tidak match route lain
    return "home";
  };

  const activeTab = getActiveTab();

  // Handler untuk navigasi tanpa mengatur state activeTab manual
  const handleHomeClick = () => navigate('/home');
  const handleCartClick = () => navigate('/cart');
  const handleReceiptClick = () => navigate('/receipt');
  const handleLocationClick = () => navigate('/location');
  const handleProfileClick = () => navigate('/profile');

  return (
    <div
      className="bottom-nav fixed left-1/2 -translate-x-1/2 bottom-4 w-[440px] max-w-[96vw] z-[999] bg-orange-100 rounded-full pt-[12px] pb-[12px] flex justify-center items-center gap-6 sm:gap-8 shadow-[0_-2px_10px_0_rgba(0,0,0,0.08)] transition-all duration-300"
    >
      {/* Home Button */}
      <button
        className={`nav-button flex items-center justify-center cursor-pointer px-3 py-2 rounded-xl transition-all duration-300 ${activeTab === 'home' ? "bg-orange-100/60" : "bg-transparent"}`}
        onClick={handleHomeClick}
      >
        <img
          src={activeTab === 'home' ? "/icon/Homeon.svg" : "/icon/Homeoff.svg"}
          alt="Home"
          className="nav-icon w-6 h-6"
        />
      </button>

      {/* Shopping Cart Button */}
      <button
        className={`nav-button flex items-center justify-center cursor-pointer px-3 py-2 rounded-xl transition-all duration-300 ${activeTab === 'cart' ? "bg-orange-100/60" : "bg-transparent"}`}
        onClick={handleCartClick}
      >
        <img
          src={activeTab === 'cart' ? "/icon/Carton.svg" : "/icon/Cartoff.svg"}
          alt="cart"
          className="nav-icon w-6 h-6"
        />
      </button>

      {/* Orders Button */}
      <button
        className={`nav-button flex items-center justify-center cursor-pointer px-3 py-2 rounded-xl transition-all duration-300 ${activeTab === 'receipt' ? "bg-orange-100/60" : "bg-transparent"}`}
        onClick={handleReceiptClick}
      >
        <img
          src={activeTab === 'receipt' ? "/icon/Receipton.svg" : "/icon/Receiptoff.svg"}
          alt="receipt"
          className="nav-icon w-6 h-6"
        />
      </button>

      {/* Location Button */}
      <button
        className={`nav-button flex items-center justify-center cursor-pointer px-3 py-2 rounded-xl transition-all duration-300 ${activeTab === 'location' ? "bg-orange-100/60" : "bg-transparent"}`}
        onClick={handleLocationClick}
        aria-label="Location"
      >
        <img
          src={activeTab === 'location' ? "/icon/Locon.svg" : "/icon/Locoff.svg"}
          alt="location"
          className="nav-icon w-6 h-6"
        />
      </button>

      {/* Profile Button */}
      <button
        className={`nav-button flex items-center justify-center cursor-pointer px-3 py-2 rounded-xl transition-all duration-300 ${activeTab === 'profile' ? "bg-orange-200/80 shadow-sm" : "bg-transparent"}`}
        onClick={handleProfileClick}
        aria-label="Profil"
      >
        <svg
          className="nav-icon w-6 h-6"
          viewBox="0 0 24 24"
          fill="none"
          stroke={activeTab === 'profile' ? "#FE5A19" : "#666"}
          strokeWidth="2"
          strokeLinecap="round"
          strokeLinejoin="round"
        >
          <path d="M20 21V19C20 17.9391 19.5786 16.9217 18.8284 16.1716C18.0783 15.4214 17.0609 15 16 15H8C6.93913 15 5.92172 15.4214 5.17157 16.1716C4.42143 16.9217 4 17.9391 4 19V21" />
          <circle cx="12" cy="7" r="4" />
        </svg>
      </button>
    </div>

    
  );
};

export default BottomNavbar;
