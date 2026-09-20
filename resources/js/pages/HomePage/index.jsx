import React, { useState, useEffect } from "react";
import TopNavbar from "../../components/commons/molecules/TopNavbar";
import BottomNavbar from "../../components/commons/molecules/BottomNavbar";

const DEFAULT_CATEGORIES = [
  "Beras",
  "Minyak Goreng",
  "Gula Pasir",
  "Telur",
  "Bumbu Dapur",
  "Mie & Pasta",
  "Kopi & Teh",
  "Susu & Olahan",
  "Air Minum / Galon",
  "Gas LPG",
  "Snack & Cemilan",
];

function HomePage() {
  const [productsList, setProductsList] = useState([]);
  const [allProducts, setAllProducts] = useState([]);
  const [loading, setLoading] = useState(true);
  const [searchQuery, setSearchQuery] = useState("");
  const [categoryOptions, setCategoryOptions] = useState(DEFAULT_CATEGORIES);
  const [selectedCategory, setSelectedCategory] = useState("");
  const [categoryQuery, setCategoryQuery] = useState("");
  const [showModal, setShowModal] = useState(false);
  const [selectedProduct, setSelectedProduct] = useState(null);
  const [quantity, setQuantity] = useState(1);
  const [addingToCart, setAddingToCart] = useState(false);

  // Fetch produk dengan instant cache untuk menghilangkan delay / lag
  useEffect(() => {
    // 1. Cek cache lokal terlebih dahulu (load instant 0ms)
    try {
      const cached = sessionStorage.getItem("mysembako_cached_products");
      if (cached) {
        const parsed = JSON.parse(cached);
        if (Array.isArray(parsed) && parsed.length > 0) {
          setAllProducts(parsed);
          setProductsList(parsed);
          setLoading(false);
        }
      }
    } catch (_) {}

    // 2. Fetch fresh data di background
    const fetchProducts = async () => {
      try {
        const response = await fetch("/api/products", {
          headers: { Accept: "application/json" },
        });
        if (!response.ok) {
          throw new Error("Gagal memuat produk dari database");
        }
        const data = await response.json();
        const list = Array.isArray(data) ? data : [];
        setAllProducts(list);
        setProductsList(list);
        sessionStorage.setItem("mysembako_cached_products", JSON.stringify(list));
      } catch (error) {
        console.error("Error fetch produk:", error);
      } finally {
        setLoading(false);
      }
    };

    fetchProducts();
  }, []);

  // Fetch kategori dari API
  useEffect(() => {
    try {
      const cachedCats = sessionStorage.getItem("mysembako_cached_categories");
      if (cachedCats) {
        setCategoryOptions(JSON.parse(cachedCats));
      }
    } catch (_) {}

    const fetchCategories = async () => {
      try {
        const res = await fetch("/api/categories", {
          headers: { Accept: "application/json" },
        });
        if (!res.ok) return;
        const data = await res.json();
        const names = Array.from(
          new Set([
            ...DEFAULT_CATEGORIES,
            ...(data || []).map((cat) => cat.name).filter(Boolean),
          ])
        );
        setCategoryOptions(names);
        sessionStorage.setItem("mysembako_cached_categories", JSON.stringify(names));
      } catch (error) {
        console.error("Error fetch kategori:", error);
      }
    };
    fetchCategories();
  }, []);

  // Filter produk berdasarkan search query
  useEffect(() => {
    const query = searchQuery.toLowerCase().trim();
    const categoryFilter = selectedCategory.toLowerCase().trim();
    const categorySearch = categoryQuery.toLowerCase().trim();

    if (!query && !categoryFilter && !categorySearch) {
      setProductsList(allProducts);
      return;
    }

    const filtered = allProducts.filter((product) => {
      const nameMatch = (product.name || "").toLowerCase().includes(query);
      const descMatch = (product.description || product.desc || "")
        .toLowerCase()
        .includes(query);
      const storeMatch = (product.store || "").toLowerCase().includes(query);
      const categoryText = (product.category || "").toLowerCase();
      const selectedMatch = categoryFilter
        ? categoryText.includes(categoryFilter)
        : true;
      const customCategoryMatch = categorySearch
        ? categoryText.includes(categorySearch)
        : true;

      const baseMatch = query ? nameMatch || descMatch || storeMatch : true;
      return baseMatch && selectedMatch && customCategoryMatch;
    });

    setProductsList(filtered);
  }, [searchQuery, allProducts, selectedCategory, categoryQuery]);

  const parsePrice = (priceStr) => {
    return Number(
      typeof priceStr === "number"
        ? priceStr
        : String(priceStr).replace(/Rp\s?|\./g, "").trim()
    );
  };

  const formatRupiah = (number) => {
    if (typeof number !== "number") number = Number(number) || 0;
    return (
      "Rp " +
      number
        .toString()
        .replace(/\B(?=(\d{3})+(?!\d))/g, ".")
    );
  };

  const openModal = (product) => {
    setSelectedProduct(product);
    setQuantity(1);
    setShowModal(true);
  };

  const closeModal = () => {
    setShowModal(false);
    setSelectedProduct(null);
    setQuantity(1);
  };

  const addToCart = async (product) => {
    try {
      setAddingToCart(true);
      const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

      const response = await fetch('/api/cart', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': csrfToken || '',
          'Accept': 'application/json',
        },
        credentials: 'same-origin',
        body: JSON.stringify({
          product_id: product.id,
          quantity: quantity,
        }),
      });

      if (response.status === 401) {
        window.location.href = '/login';
        return;
      }

      const data = await response.json();

      if (!response.ok) {
        alert(data.message || 'Gagal menambahkan produk ke keranjang');
        return;
      }

      alert('Produk berhasil ditambahkan ke keranjang!');
      closeModal();
    } catch (error) {
      console.error('Error adding to cart:', error);
      alert('Terjadi kesalahan saat menambahkan produk ke keranjang');
    } finally {
      setAddingToCart(false);
    }
  };

  const incQty = () => {
    if (quantity < selectedProduct.stock) setQuantity(quantity + 1);
  };

  const decQty = () => {
    if (quantity > 1) setQuantity(quantity - 1);
  };

  let dynamicTotalPrice = selectedProduct
    ? formatRupiah(parsePrice(selectedProduct.price) * quantity)
    : "";

  return (
    <div className="bg-[#FAF7F2] min-h-screen text-slate-800 flex flex-col">
      {/* Top Navbar */}
      <TopNavbar
        searchQuery={searchQuery}
        onSearchChange={(e) => setSearchQuery(e.target.value)}
        selectedCategory={selectedCategory}
        categoryQuery={categoryQuery}
        categoryOptions={categoryOptions}
        onCategorySelect={(value) =>
          setSelectedCategory((prev) => (prev === value ? "" : value))
        }
        onCategoryQueryChange={(value) => setCategoryQuery(value)}
        onResetFilter={() => {
          setSelectedCategory("");
          setCategoryQuery("");
          setSearchQuery("");
          setProductsList(allProducts);
        }}
      />

      {/* Filter Kategori */}
      <main className="flex-1 max-w-7xl w-full mx-auto px-3 sm:px-6 pt-5 pb-32">
        <div className="bg-white/95 backdrop-blur-sm border border-[#FBE3D4] rounded-2xl p-4 sm:p-6 shadow-sm mb-6 transition-all duration-300">
          <div className="flex flex-wrap items-center justify-between gap-3">
            <div className="text-sm sm:text-base font-bold text-gray-800 tracking-tight">
              Pilih kategori atau cari sendiri
            </div>
            <button
              type="button"
              onClick={() => {
                setSelectedCategory("");
                setCategoryQuery("");
                setProductsList(allProducts);
              }}
              className="text-xs sm:text-sm font-semibold text-[#FE5A19] hover:text-[#E04B0E] transition-colors"
            >
              Reset kategori
            </button>
          </div>

          <div className="mt-3.5 flex flex-wrap gap-2">
            {categoryOptions.slice(0, 12).map((cat) => {
              const isActive = selectedCategory === cat;
              return (
                <button
                  key={cat}
                  type="button"
                  onClick={() =>
                    setSelectedCategory((prev) => (prev === cat ? "" : cat))
                  }
                  className={`px-3.5 py-1.5 rounded-full text-xs sm:text-sm font-medium transition-all duration-200 ${
                    isActive
                      ? "bg-[#FE5A19] text-white shadow-sm shadow-orange-500/25 scale-[1.02]"
                      : "bg-[#FFF9F5] text-gray-700 border border-[#FBE3D4] hover:border-[#FE5A19]/50 hover:bg-white"
                  }`}
                >
                  {cat}
                </button>
              );
            })}
          </div>

          <div className="mt-4 flex flex-wrap gap-3 items-center">
            <div className="flex-1 min-w-[220px]">
              <input
                type="text"
                list="category-search-list"
                value={categoryQuery}
                onChange={(e) => setCategoryQuery(e.target.value)}
                className="w-full px-4 py-2 bg-[#FFFDFB] border border-[#FBE3D4] rounded-full text-xs sm:text-sm text-gray-800 placeholder-gray-400 focus:outline-none focus:border-[#FE5A19] focus:ring-2 focus:ring-orange-100 transition"
                placeholder="Cari kategori lain (misal: rempah, sayur)..."
              />
              <datalist id="category-search-list">
                {categoryOptions.map((cat) => (
                  <option key={cat} value={cat}></option>
                ))}
              </datalist>
              <p className="text-xs text-gray-500 mt-1.5 leading-normal">
                Jika kategori belum ada di pilihan, ketik saja — kami akan cari yang cocok.
              </p>
            </div>
          </div>
        </div>

        {/* Produk grid */}
        {loading && productsList.length === 0 && (
          <div className="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-3.5 sm:gap-5">
            {[1, 2, 3, 4, 5, 6, 7, 8, 9, 10].map((n) => (
              <div
                key={n}
                className="bg-white rounded-2xl p-4 border border-[#F5EBE1] shadow-sm animate-pulse flex flex-col justify-between"
              >
                <div className="w-full aspect-square bg-slate-100 rounded-xl mb-3"></div>
                <div className="h-4 bg-slate-100 rounded w-3/4 mb-2"></div>
                <div className="h-4 bg-slate-100 rounded w-1/2 mb-2"></div>
                <div className="h-3 bg-slate-100 rounded w-1/3"></div>
              </div>
            ))}
          </div>
        )}

        <div className="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-3.5 sm:gap-5">
          {!loading && productsList.length === 0 && (
            <div className="col-span-full bg-white rounded-2xl border border-[#FBE3D4] p-12 text-center text-gray-400 py-16 shadow-sm">
              {searchQuery.trim()
                ? `Tidak ada produk ditemukan untuk "${searchQuery}"`
                : "Tidak ada produk ditemukan."}
            </div>
          )}

          {productsList.map((product, idx) => (
            <div
              key={product.id || idx}
              className="flex flex-col h-full bg-white border border-[#F2ECE4] rounded-2xl shadow-sm hover:shadow-md hover:-translate-y-0.5 p-3.5 sm:p-4 transition-all duration-200 cursor-pointer group"
              onClick={() => openModal(product)}
            >
              <div className="w-full aspect-square bg-gradient-to-b from-[#FFF5ED]/60 to-[#FAF7F2]/30 rounded-xl overflow-hidden mb-3 flex items-center justify-center p-2 relative">
                <img
                  src={product.img}
                  alt={product.name}
                  loading="lazy"
                  className="w-full h-full object-cover rounded-lg group-hover:scale-105 transition-transform duration-300"
                  onError={(e) => {
                    e.target.src = "https://images.unsplash.com/photo-1542838132-92c53300491e?auto=format&fit=crop&w=600&q=80";
                  }}
                />
              </div>

              <div className="flex-grow flex flex-col justify-between">
                <div>
                  <div className="line-clamp-2 font-bold text-gray-900 text-xs sm:text-sm leading-snug mb-1 group-hover:text-[#FE5A19] transition-colors">
                    {product.name}
                  </div>
                </div>

                <div className="mt-2 pt-2 border-t border-gray-100">
                  <div className="text-sm sm:text-base font-extrabold text-[#FE5A19] mb-1">
                    {typeof product.price === "number"
                      ? formatRupiah(product.price)
                      : product.price}
                  </div>
                  <div className="text-[11px] sm:text-xs text-gray-400 truncate flex items-center gap-1">
                    <span>🏪</span>
                    <span className="truncate">{product.store}</span>
                  </div>
                </div>
              </div>
            </div>
          ))}
        </div>
      </main>

      {/* Modal */}
      {showModal && selectedProduct && (
        <div className="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-sm animate-fadeIn">
          <div
            className="bg-white rounded-3xl shadow-2xl p-6 sm:p-7 max-w-xs sm:max-w-sm w-full relative animate-scaleUp border border-[#FBE3D4]"
          >
            <button
              onClick={closeModal}
              className="absolute top-3 right-4 text-gray-400 hover:text-gray-600 text-2xl font-bold focus:outline-none transition-colors"
              aria-label="Tutup"
            >
              &times;
            </button>

            <div className="flex flex-col items-center">
              <div className="w-28 h-28 sm:w-32 sm:h-32 bg-[#FFF5ED] rounded-2xl overflow-hidden p-2 mb-3.5 flex items-center justify-center">
                <img
                  src={selectedProduct.img}
                  alt={selectedProduct.name}
                  className="w-full h-full object-cover rounded-xl"
                />
              </div>

              <h2 className="text-base sm:text-lg font-bold mb-1 text-center text-gray-900 leading-snug">
                {selectedProduct.name}
              </h2>

              <div className="text-gray-600 text-xs sm:text-sm mb-2 text-center line-clamp-2">
                {selectedProduct.description || selectedProduct.desc || ""}
              </div>

              <div className="text-[#FE5A19] font-extrabold text-base sm:text-lg mb-1">
                {dynamicTotalPrice}
              </div>

              <div className="text-gray-500 text-xs mb-4">
                Sisa Stock: <span className="font-semibold text-gray-700">{selectedProduct.stock}</span>
              </div>

              {/* Selector jumlah produk */}
              <div className="mb-5 flex items-center justify-center gap-3">
                <button
                  type="button"
                  onClick={decQty}
                  className="w-8 h-8 rounded-full border border-gray-300 flex items-center justify-center text-gray-600 hover:bg-gray-100 font-bold transition"
                >
                  -
                </button>
                <span className="w-8 text-center font-bold text-sm text-gray-800 select-none">
                  {quantity}
                </span>
                <button
                  type="button"
                  onClick={incQty}
                  className="w-8 h-8 rounded-full border border-gray-300 flex items-center justify-center text-gray-600 hover:bg-gray-100 font-bold transition"
                >
                  +
                </button>
              </div>

              <button
                type="button"
                disabled={addingToCart}
                onClick={() => addToCart(selectedProduct)}
                className="w-full py-3 bg-[#FE5A19] hover:bg-[#E04B0E] active:scale-[0.98] text-white font-bold rounded-2xl shadow-md shadow-orange-500/20 text-sm transition disabled:opacity-50"
              >
                {addingToCart ? "Menambahkan..." : "Masukkan ke Keranjang"}
              </button>
            </div>
          </div>
        </div>
      )}

      {/* Bottom Navbar */}
      <BottomNavbar />
    </div>
  );
}

export default HomePage;
