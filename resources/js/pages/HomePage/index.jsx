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

const CATEGORY_EMOJIS = {
  "Beras": "🌾",
  "Minyak Goreng": "🛢️",
  "Gula Pasir": "🧂",
  "Telur": "🥚",
  "Bumbu Dapur": "🌶️",
  "Mie & Pasta": "🍜",
  "Kopi & Teh": "☕",
  "Susu & Olahan": "🥛",
  "Air Minum / Galon": "💧",
  "Gas LPG": "🔥",
  "Snack & Cemilan": "🍪",
};

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
  const [toastMessage, setToastMessage] = useState("");

  const showToast = (msg) => {
    setToastMessage(msg);
    setTimeout(() => setToastMessage(""), 3000);
  };

  // Fetch produk dari API
  const fetchProducts = async () => {
    try {
      setLoading(true);
      const response = await fetch("/api/products", {
        headers: { Accept: "application/json" },
      });
      if (!response.ok) {
        throw new Error("Gagal memuat produk");
      }
      const data = await response.json();
      const list = Array.isArray(data) ? data : [];
      setAllProducts(list);
      setProductsList(list);
    } catch (error) {
      console.error("Error fetch produk:", error);
      setAllProducts([]);
      setProductsList([]);
    } finally {
      setLoading(false);
    }
  };

  useEffect(() => {
    fetchProducts();
  }, []);

  // Fetch kategori dari API
  useEffect(() => {
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
      } catch (error) {
        console.error("Error fetch kategori:", error);
      }
    };
    fetchCategories();
  }, []);

  // Filter produk berdasarkan search & kategori
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
    return "Rp " + number.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
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

  const addToCart = async (product, qty = 1) => {
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
          quantity: qty,
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

      showToast(`✓ ${product.name} berhasil ditambahkan ke keranjang!`);
      closeModal();
    } catch (error) {
      console.error('Error adding to cart:', error);
      alert('Terjadi kesalahan saat menambahkan produk ke keranjang');
    } finally {
      setAddingToCart(false);
    }
  };

  return (
    <div className="bg-[#FAF9F6] min-h-screen text-slate-800">
      {/* Toast Notification */}
      {toastMessage && (
        <div className="fixed top-20 left-1/2 -translate-x-1/2 z-50 bg-emerald-600 text-white px-5 py-3 rounded-full shadow-2xl font-medium text-sm flex items-center gap-2 animate-bounce">
          {toastMessage}
        </div>
      )}

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

      <main className="max-w-7xl mx-auto px-3 sm:px-6 pt-3 pb-28">
        {/* Modern Promotional Hero Banner */}
        <div className="relative overflow-hidden rounded-3xl bg-gradient-to-r from-orange-500 via-amber-500 to-orange-600 text-white p-6 sm:p-8 shadow-lg shadow-orange-500/15 mb-6">
          <div className="relative z-10 max-w-xl">
            <span className="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-white/20 backdrop-blur-md mb-3">
              ✨ Belanja Sembako Tanpa Ribet
            </span>
            <h1 className="text-2xl sm:text-3xl font-extrabold tracking-tight leading-tight">
              Kebutuhan Pokok Segar, Lengkap & Hemat Setiap Hari!
            </h1>
            <p className="mt-2 text-white/90 text-xs sm:text-sm leading-relaxed">
              Langsung diantar dari toko kelontong terdekat di daerah Anda dengan harga grosir terbaik.
            </p>
            <div className="mt-4 flex flex-wrap gap-2 text-xs font-medium">
              <span className="bg-black/15 px-3 py-1 rounded-full">🚀 Pengiriman Cepat</span>
              <span className="bg-black/15 px-3 py-1 rounded-full">🌿 Kualitas Terjamin</span>
              <span className="bg-black/15 px-3 py-1 rounded-full">🏷️ Diskon Langsung</span>
            </div>
          </div>
          {/* Decorative Circles */}
          <div className="absolute -right-10 -bottom-10 w-48 h-48 sm:w-64 sm:h-64 bg-white/10 rounded-full blur-2xl pointer-events-none"></div>
          <div className="absolute right-12 top-6 text-7xl sm:text-8xl opacity-30 select-none pointer-events-none hidden sm:block">
            🛒
          </div>
        </div>

        {/* Category Pills Slider */}
        <div className="bg-white rounded-2xl p-4 sm:p-5 border border-orange-100 shadow-sm mb-6">
          <div className="flex items-center justify-between gap-3 mb-3">
            <h2 className="text-sm sm:text-base font-bold text-gray-900 flex items-center gap-2">
              <span>🏷️</span> Kategori Pilihan
            </h2>
            {selectedCategory && (
              <button
                type="button"
                onClick={() => setSelectedCategory("")}
                className="text-xs font-semibold text-orange-600 hover:text-orange-700 underline"
              >
                Hapus Filter Kategori
              </button>
            )}
          </div>

          <div className="flex gap-2 overflow-x-auto pb-2 scrollbar-thin scrollbar-thumb-orange-200">
            <button
              type="button"
              onClick={() => setSelectedCategory("")}
              className={`flex-shrink-0 px-4 py-2 rounded-xl text-xs sm:text-sm font-semibold transition-all duration-200 ${
                selectedCategory === ""
                  ? "bg-orange-500 text-white shadow-md shadow-orange-500/25 scale-105"
                  : "bg-orange-50/60 text-gray-700 hover:bg-orange-100/70 border border-orange-100"
              }`}
            >
              Semua Produk
            </button>
            {categoryOptions.map((cat) => {
              const isActive = selectedCategory === cat;
              const emoji = CATEGORY_EMOJIS[cat] || "📦";
              return (
                <button
                  key={cat}
                  type="button"
                  onClick={() => setSelectedCategory((prev) => (prev === cat ? "" : cat))}
                  className={`flex-shrink-0 flex items-center gap-1.5 px-3.5 py-2 rounded-xl text-xs sm:text-sm font-semibold transition-all duration-200 ${
                    isActive
                      ? "bg-orange-500 text-white shadow-md shadow-orange-500/25 scale-105"
                      : "bg-white text-gray-700 hover:bg-orange-50 border border-orange-200/80"
                  }`}
                >
                  <span>{emoji}</span>
                  <span>{cat}</span>
                </button>
              );
            })}
          </div>
        </div>

        {/* Section Header */}
        <div className="flex items-center justify-between mb-4">
          <div>
            <h2 className="text-lg sm:text-xl font-bold text-gray-900">
              {selectedCategory ? `Kategori: ${selectedCategory}` : "Daftar Produk Sembako"}
            </h2>
            <p className="text-xs text-gray-500">
              Menampilkan {productsList.length} produk siap beli
            </p>
          </div>
        </div>

        {/* Shimmer Loading Skeletons */}
        {loading && (
          <div className="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-3.5 sm:gap-5">
            {[1, 2, 3, 4, 5, 6, 7, 8, 9, 10].map((n) => (
              <div
                key={n}
                className="bg-white rounded-2xl p-3 border border-orange-50 shadow-sm animate-pulse flex flex-col justify-between"
              >
                <div className="w-full aspect-square bg-slate-100 rounded-xl mb-3"></div>
                <div className="h-4 bg-slate-100 rounded w-3/4 mb-2"></div>
                <div className="h-4 bg-slate-100 rounded w-1/2 mb-3"></div>
                <div className="h-8 bg-slate-100 rounded-lg"></div>
              </div>
            ))}
          </div>
        )}

        {/* Empty State */}
        {!loading && productsList.length === 0 && (
          <div className="bg-white rounded-3xl p-12 text-center border border-dashed border-orange-200 my-6 shadow-sm">
            <div className="text-5xl mb-3">🔍</div>
            <h3 className="text-base font-bold text-gray-800">Tidak ada produk ditemukan</h3>
            <p className="text-xs text-gray-500 mt-1 max-w-sm mx-auto">
              {searchQuery
                ? `Tidak ada produk dengan kata kunci "${searchQuery}". Coba kata kunci lain.`
                : "Belum ada produk aktif pada kategori ini."}
            </p>
            <button
              onClick={() => {
                setSelectedCategory("");
                setSearchQuery("");
                setProductsList(allProducts);
              }}
              className="mt-4 px-4 py-2 bg-orange-500 hover:bg-orange-600 text-white rounded-xl text-xs font-semibold shadow-md shadow-orange-500/20 transition"
            >
              Lihat Semua Produk
            </button>
          </div>
        )}

        {/* Product Grid */}
        {!loading && productsList.length > 0 && (
          <div className="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-3.5 sm:gap-5">
            {productsList.map((product) => (
              <div
                key={product.id}
                className="group flex flex-col bg-white border border-orange-100/80 rounded-2xl p-3 sm:p-4 shadow-sm hover:shadow-xl hover:-translate-y-1 transition-all duration-300 relative cursor-pointer overflow-hidden"
                onClick={() => openModal(product)}
              >
                {/* Product Image Container */}
                <div className="w-full aspect-square bg-gradient-to-b from-orange-50/50 to-slate-50/20 rounded-xl overflow-hidden mb-3 relative flex items-center justify-center p-2">
                  <img
                    src={product.img}
                    alt={product.name}
                    loading="lazy"
                    className="w-full h-full object-cover rounded-lg group-hover:scale-105 transition-transform duration-300"
                    onError={(e) => {
                      e.target.src = "https://images.unsplash.com/photo-1542838132-92c53300491e?auto=format&fit=crop&w=600&q=80";
                    }}
                  />
                  {product.stock <= 10 && (
                    <span className="absolute top-2 left-2 bg-rose-500 text-white text-[10px] font-bold px-2 py-0.5 rounded-full shadow-sm">
                      Sisa {product.stock}
                    </span>
                  )}
                </div>

                {/* Info Container */}
                <div className="flex-1 flex flex-col justify-between">
                  <div>
                    <span className="text-[10px] font-medium text-orange-600 bg-orange-50 px-2 py-0.5 rounded-md mb-1 inline-block">
                      {product.category || "Sembako"}
                    </span>
                    <h3 className="font-bold text-gray-900 text-xs sm:text-sm line-clamp-2 leading-snug group-hover:text-orange-600 transition-colors">
                      {product.name}
                    </h3>
                  </div>

                  <div className="mt-2.5 pt-2 border-t border-slate-100 flex flex-col gap-1.5">
                    <div className="text-sm sm:text-base font-extrabold text-orange-600">
                      {formatRupiah(product.price)}
                    </div>
                    <div className="flex items-center justify-between text-[11px] text-gray-400">
                      <span className="truncate flex items-center gap-1">
                        🏪 {product.store}
                      </span>
                      <span className="text-emerald-600 font-medium">Stok: {product.stock}</span>
                    </div>

                    <button
                      type="button"
                      onClick={(e) => {
                        e.stopPropagation();
                        addToCart(product, 1);
                      }}
                      className="mt-1 w-full py-1.5 px-3 bg-orange-500 hover:bg-orange-600 text-white rounded-xl text-xs font-bold transition shadow-sm flex items-center justify-center gap-1"
                    >
                      <span>+ Keranjang</span>
                    </button>
                  </div>
                </div>
              </div>
            ))}
          </div>
        )}
      </main>

      {/* Product Detail Modal */}
      {showModal && selectedProduct && (
        <div className="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm animate-fadeIn">
          <div className="bg-white rounded-3xl max-w-sm w-full p-5 sm:p-6 shadow-2xl relative animate-scaleUp overflow-hidden">
            <button
              onClick={closeModal}
              className="absolute top-4 right-4 w-8 h-8 rounded-full bg-gray-100 hover:bg-gray-200 text-gray-500 flex items-center justify-center text-lg font-bold transition"
            >
              &times;
            </button>

            <div className="w-full aspect-square bg-orange-50/60 rounded-2xl overflow-hidden mb-4 p-2 flex items-center justify-center">
              <img
                src={selectedProduct.img}
                alt={selectedProduct.name}
                className="w-full h-full object-cover rounded-xl"
              />
            </div>

            <span className="text-xs font-semibold text-orange-600 bg-orange-50 px-2.5 py-0.5 rounded-md inline-block mb-1.5">
              {selectedProduct.category || "Sembako"}
            </span>

            <h2 className="text-base sm:text-lg font-bold text-gray-900 leading-snug">
              {selectedProduct.name}
            </h2>

            <p className="text-xs text-gray-500 mt-1 leading-relaxed line-clamp-3">
              {selectedProduct.description || "Produk sembako berkualitas, segar dan siap kirim ke alamat Anda."}
            </p>

            <div className="mt-3 flex items-center justify-between text-xs text-gray-500 border-y border-gray-100 py-2">
              <span>🏪 {selectedProduct.store}</span>
              <span className="text-emerald-600 font-semibold">Tersedia: {selectedProduct.stock} pcs</span>
            </div>

            <div className="mt-4 flex items-center justify-between">
              <div className="flex items-center border border-gray-200 rounded-xl overflow-hidden">
                <button
                  type="button"
                  onClick={() => setQuantity((q) => Math.max(1, q - 1))}
                  className="w-9 h-9 flex items-center justify-center text-gray-600 hover:bg-gray-100 font-bold"
                >
                  -
                </button>
                <span className="w-10 text-center font-bold text-sm text-gray-800">
                  {quantity}
                </span>
                <button
                  type="button"
                  onClick={() => setQuantity((q) => Math.min(selectedProduct.stock, q + 1))}
                  className="w-9 h-9 flex items-center justify-center text-gray-600 hover:bg-gray-100 font-bold"
                >
                  +
                </button>
              </div>

              <div className="text-right">
                <div className="text-[10px] text-gray-400">Total Harga</div>
                <div className="text-lg font-extrabold text-orange-600">
                  {formatRupiah(parsePrice(selectedProduct.price) * quantity)}
                </div>
              </div>
            </div>

            <button
              type="button"
              disabled={addingToCart}
              onClick={() => addToCart(selectedProduct, quantity)}
              className="mt-5 w-full py-3 bg-orange-500 hover:bg-orange-600 active:scale-[0.98] text-white font-bold rounded-2xl shadow-lg shadow-orange-500/30 text-sm transition flex items-center justify-center gap-2 disabled:opacity-50"
            >
              {addingToCart ? (
                <span>Menambahkan...</span>
              ) : (
                <>
                  <span>🛒</span>
                  <span>Masukkan ke Keranjang</span>
                </>
              )}
            </button>
          </div>
        </div>
      )}

      {/* Bottom Floating Navigation */}
      <BottomNavbar />
    </div>
  );
}

export default HomePage;
