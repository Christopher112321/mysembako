import { Link } from "react-router-dom";
import { useEffect, useRef, useState } from "react";

// Import font Poppins via Google Fonts
// eslint-disable-next-line
import "../../styles/font.css";

const features = [
  {
    icon: (
      <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="#FE5A19" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round">
        <path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z"/>
        <circle cx="12" cy="10" r="3"/>
      </svg>
    ),
    title: "Temukan Toko Terdekat",
    description: "Cari produk sembako di toko-toko sembako terdekat dari lokasimu."
  },
  {
    icon: (
      <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="#FE5A19" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round">
        <rect width="7" height="7" x="3" y="3" rx="1"/>
        <rect width="7" height="7" x="14" y="3" rx="1"/>
        <rect width="7" height="7" x="14" y="14" rx="1"/>
        <rect width="7" height="7" x="3" y="14" rx="1"/>
      </svg>
    ),
    title: "Cari Berdasarkan Kategori",
    description: "Beras, minyak, gula, dan lainnya — temukan produk sesuai kebutuhanmu."
  },
  {
    icon: (
      <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="#FE5A19" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round">
        <path d="M3 9h18v10a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V9Z"/>
        <path d="m3 9 2.45-4.9A2 2 0 0 1 7.24 3h9.52a2 2 0 0 1 1.8 1.1L21 9"/>
        <path d="M12 3v6"/>
      </svg>
    ),
    title: "Daftarkan Tokomu Gratis",
    description: "Punya toko sembako? Daftar gratis dan jangkau lebih banyak pelanggan!",
    isWhatsApp: true
  }
];

const FeatureCard = ({ feature, index }) => {
  const ref = useRef(null);
  const [isVisible, setIsVisible] = useState(false);

  useEffect(() => {
    const observer = new IntersectionObserver(
      ([entry]) => {
        if (entry.isIntersecting) {
          setIsVisible(true);
        }
      },
      { threshold: 0.3 }
    );

    if (ref.current) {
      observer.observe(ref.current);
    }

    return () => observer.disconnect();
  }, []);

  return (
    <div
      ref={ref}
      className={`flex flex-col items-center text-center p-8 rounded-3xl transition-all duration-700 ${
        isVisible ? "opacity-100 translate-y-0" : "opacity-0 translate-y-12"
      }`}
      style={{ 
        backgroundColor: "#FFFFFF",
        boxShadow: "0 10px 40px rgba(254, 90, 25, 0.1)",
        transitionDelay: `${index * 150}ms`,
        fontFamily: "'Poppins'"
      }}
    >
      <div 
        className="w-20 h-20 rounded-full flex items-center justify-center mb-6"
        style={{ backgroundColor: "#FFE6DD" }}
      >
        {feature.icon}
      </div>
      <h3 
        className="text-xl md:text-2xl font-bold mb-3"
        style={{ color: "#1a1a1a", fontFamily: "'Poppins'" }}
      >
        {feature.title}
      </h3>
      <p 
        className="text-base leading-relaxed mb-4"
        style={{ color: "#666666", fontFamily: "'Poppins'" }}
      >
        {feature.description}
      </p>
      {feature.isWhatsApp && (
        <a
          href="https://wa.me/6285229947441?text=Halo%20Admin%20MySembako,%20saya%20ingin%20mendaftarkan%20toko%20saya."
          target="_blank"
          rel="noopener noreferrer"
          className="inline-flex items-center gap-2 px-6 py-3 rounded-full font-semibold transition-all duration-300 hover:scale-105"
          style={{ backgroundColor: "#25D366", color: "#FFFFFF", fontFamily: "'Poppins'" }}
        >
          <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
            <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
          </svg>
          Hubungi Admin
        </a>
      )}
    </div>
  );
};

const Bumper = () => {
  return (
    <div style={{ fontFamily: "'Poppins'" }}>
      {/* Hero Section */}
      <main 
        className="min-h-screen flex flex-col items-center justify-center px-6 py-12 relative"
        style={{ backgroundColor: "#FFFFFF", fontFamily: "'Poppins'" }}
      >
        {/* Logo */}
        <div className="mb-8 animate-[fadeIn_0.8s_ease-out_forwards]">
          <img 
            src="/icon/Logo_Mysembako.svg" 
            alt="MySembako Logo" 
            className="h-20 md:h-28 w-auto"
            style={{ fontFamily: "'Poppins'" }}
          />
        </div>

        {/* Content */}
        <div 
          className="text-center max-w-2xl opacity-0 animate-[slideUp_0.8s_ease-out_0.2s_forwards]"
          style={{ fontFamily: "'Poppins'" }}
        >
          <h1 
            className="text-4xl md:text-5xl lg:text-6xl font-bold tracking-tight mb-6"
            style={{ color: "#1a1a1a", fontFamily: "'Poppins'" }}
          >
            MySembako — Belanja Sembako Lebih Mudah
          </h1>
          
          <p 
            className="text-lg md:text-xl mb-10 leading-relaxed"
            style={{ color: "#666666", fontFamily: "'Poppins'" }}
          >
            Mulai dari kebutuhan harian hingga grosir — semua dalam satu platform.
          </p>

          {/* CTA Button */}
          <Link to="/login">
            <button 
              className="font-bold text-lg px-10 py-4 rounded-2xl shadow-md transition-all duration-300 hover:shadow-lg hover:scale-105"
              style={{ 
                backgroundColor: "#FE5A19", 
                color: "#FFFFFF",
                fontFamily: "'Poppins'"
              }}
              onMouseEnter={(e) => e.target.style.backgroundColor = "#BF4413"}
              onMouseLeave={(e) => e.target.style.backgroundColor = "#FE5A19"}
            >
              Mulai Belanja
            </button>
          </Link>
        </div>

        {/* Scroll Indicator */}
        <div className="absolute bottom-8 animate-bounce">
          <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="#FE5A19" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round">
            <path d="m6 9 6 6 6-6"/>
          </svg>
        </div>
      </main>

      {/* Features Section */}
      <section 
        className="py-20 px-6"
        style={{ backgroundColor: "#FFF9F6", fontFamily: "'Poppins'" }}
      >
        <div className="max-w-6xl mx-auto">
          <h2 
            className="text-3xl md:text-4xl font-bold text-center mb-4"
            style={{ color: "#1a1a1a", fontFamily: "'Poppins'" }}
          >
            Kenapa MySembako?
          </h2>
          <p 
            className="text-center text-lg mb-16 max-w-xl mx-auto"
            style={{ color: "#666666", fontFamily: "'Poppins'" }}
          >
            Platform yang memudahkan pembeli dan penjual sembako.
          </p>
          
          <div className="grid md:grid-cols-3 gap-8">
            {features.map((feature, index) => (
              <FeatureCard key={index} feature={feature} index={index} />
            ))}
          </div>
        </div>
      </section>

      {/* Footer */}
      <footer 
        className="py-8 text-center text-sm"
        style={{ backgroundColor: "#FFFFFF", color: "#999999" }}
      >
        <span style={{ fontFamily: "'Poppins'" }}>© 2024 MySembako. </span>
        <span style={{ fontFamily: "serif" }}>All rights reserved.</span>
      </footer>

      {/* Keyframe Styles */}
      <style>{`
        @keyframes fadeIn {
          from {
            opacity: 0;
            transform: translateY(-10px);
          }
          to {
            opacity: 1;
            transform: translateY(0);
          }
        }
        @keyframes slideUp {
          from {
            opacity: 0;
            transform: translateY(20px);
          }
          to {
            opacity: 1;
            transform: translateY(0);
          }
        }
      `}</style>
    </div>
  );
};

export default Bumper;
