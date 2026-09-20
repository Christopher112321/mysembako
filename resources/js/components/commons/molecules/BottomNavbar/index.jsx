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
    // Default to home jika tidak match route lain
    return "home";
  };

  const activeTab = getActiveTab();

  // Handler untuk navigasi tanpa mengatur state activeTab manual
  const handleHomeClick = () => navigate('/home');
  const handleCartClick = () => navigate('/cart');
  const handleReceiptClick = () => navigate('/receipt');
  const handleLocationClick = () => navigate('/location');

  return (
    <div
      className="bottom-nav fixed left-1/2 -translate-x-1/2 bottom-4 w-[420px] max-w-[94vw] z-[999] bg-orange-100 rounded-full pt-[14px] pb-[14px] flex justify-center items-center gap-9 shadow-[0_-2px_10px_0_rgba(0,0,0,0.08)] transition-all duration-300"
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
      >
        <img
          src={activeTab === 'location' ? "/icon/Locon.svg" : "/icon/Locoff.svg"}
          alt="location"
          className="nav-icon w-6 h-6"
        />
      </button>
    </div>

    
  );
};

export default BottomNavbar;
