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
      className="bottom-nav fixed left-1/2 -translate-x-1/2 bottom-5 w-[380px] max-w-[92vw] z-[999] bg-white/90 backdrop-blur-xl border border-orange-200/70 rounded-full py-2 px-5 flex justify-between items-center shadow-[0_10px_30px_rgba(254,90,25,0.12)] transition-all duration-300"
    >
      {/* Home Button */}
      <button
        className={`nav-button flex items-center justify-center cursor-pointer p-2.5 rounded-full transition-all duration-200 hover:scale-110 active:scale-95 ${activeTab === 'home' ? "bg-orange-500/10 shadow-sm" : "bg-transparent hover:bg-orange-50/50"}`}
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
        className={`nav-button flex items-center justify-center cursor-pointer p-2.5 rounded-full transition-all duration-200 hover:scale-110 active:scale-95 ${activeTab === 'cart' ? "bg-orange-500/10 shadow-sm" : "bg-transparent hover:bg-orange-50/50"}`}
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
        className={`nav-button flex items-center justify-center cursor-pointer p-2.5 rounded-full transition-all duration-200 hover:scale-110 active:scale-95 ${activeTab === 'receipt' ? "bg-orange-500/10 shadow-sm" : "bg-transparent hover:bg-orange-50/50"}`}
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
        className={`nav-button flex items-center justify-center cursor-pointer p-2.5 rounded-full transition-all duration-200 hover:scale-110 active:scale-95 ${activeTab === 'location' ? "bg-orange-500/10 shadow-sm" : "bg-transparent hover:bg-orange-50/50"}`}
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
