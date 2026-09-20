import React, { useState, useEffect } from "react";
import { useNavigate } from "react-router-dom";
import BottomNavbar from "../../components/commons/molecules/BottomNavbar";
import TopNavbar from "../../components/commons/molecules/TopNavbar";

function CartPage() {
  const navigate = useNavigate();
  const [cart, setCart] = useState([]);
  const [checked, setChecked] = useState([]);
  const [loading, setLoading] = useState(true);

  // Fetch cart items from API
  useEffect(() => {
    const fetchCart = async () => {
      try {
        setLoading(true);
        const response = await fetch("/api/cart", {
          method: "GET",
          headers: {
            "Accept": "application/json",
          },
          credentials: "same-origin",
        });

        if (!response.ok) {
          if (response.status === 401) {
            // User not authenticated, redirect to login
            window.location.href = "/login";
            return;
          }
          throw new Error("Gagal memuat keranjang");
        }

        const data = await response.json();
        setCart(data);
        setChecked(new Array(data.length).fill(true));
      } catch (error) {
        console.error("Error fetching cart:", error);
        setCart([]);
        setChecked([]);
      } finally {
        setLoading(false);
      }
    };

    fetchCart();
  }, []);

  // Handler select all
  const handleCheckAll = (val) => {
    setChecked(new Array(cart.length).fill(val));
  };

  // Handler per checkbox item
  const handleCheck = (index) => {
    setChecked((prev) => {
      const newChecked = [...prev];
      newChecked[index] = !newChecked[index];
      return newChecked;
    });
  };

  // Handler quantity (+ / -)
  const handleQty = async (idx, amt) => {
    const cartItem = cart[idx];
    const newQuantity = Math.max(1, cartItem.quantity + amt);

    // Check stock availability
    if (newQuantity > cartItem.stock) {
      alert(`Stok tidak mencukupi. Stok tersedia: ${cartItem.stock}`);
      return;
    }

    try {
      const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
      
      const response = await fetch(`/api/cart/${cartItem.id}`, {
        method: 'PUT',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': csrfToken || '',
          'Accept': 'application/json',
        },
        credentials: 'same-origin',
        body: JSON.stringify({
          quantity: newQuantity,
        }),
      });

      const data = await response.json();

      if (!response.ok) {
        alert(data.message || 'Gagal memperbarui jumlah produk');
        return;
      }

      // Update local state
      setCart((prev) =>
        prev.map((item, i) =>
          i === idx ? { ...item, quantity: newQuantity } : item
        )
      );
    } catch (error) {
      console.error('Error updating quantity:', error);
      alert('Terjadi kesalahan saat memperbarui jumlah produk');
    }
  };

  // Handler remove
  const handleRemove = async (idx) => {
    const cartItem = cart[idx];
    
    if (!confirm(`Yakin ingin menghapus ${cartItem.name} dari keranjang?`)) {
      return;
    }

    try {
      const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
      
      const response = await fetch(`/api/cart/${cartItem.id}`, {
        method: 'DELETE',
        headers: {
          'X-CSRF-TOKEN': csrfToken || '',
          'Accept': 'application/json',
        },
        credentials: 'same-origin',
      });

      if (!response.ok) {
        const data = await response.json();
        alert(data.message || 'Gagal menghapus produk dari keranjang');
        return;
      }

      // Update local state
      setCart((prev) => prev.filter((_, i) => i !== idx));
      setChecked((prev) => prev.filter((_, i) => i !== idx));
    } catch (error) {
      console.error('Error removing item:', error);
      alert('Terjadi kesalahan saat menghapus produk');
    }
  };

  // Total untuk item yang tercentang saja
  const total = cart.reduce(
    (acc, item, i) => acc + (checked[i] ? item.price * item.quantity : 0),
    0
  );

  // Handler checkout
  const handleCheckout = () => {
    if (!checked.some(Boolean)) {
      alert("Tidak ada item yang dipilih.");
      return;
    }
    
    // Get selected items
    const selectedItems = cart.filter((_, index) => checked[index]);
    
    // Navigate to checkout page with selected items
    navigate("/checkout", { state: { items: selectedItems } });
  };

  // Custom Checkbox with bigger touch area
  const CustomCheckbox = ({
    checked,
    onChange,
    className = "",
    ...props
  }) => (
    <label
      className={`relative flex items-center justify-center w-6 h-6 rounded-[6px] border border-[#dbbdae] transition 
        ${checked ? "bg-[#fc8726] border-[#fc8726]" : "bg-white"} ${className}`}
      tabIndex={0}
      role="checkbox"
      aria-checked={checked}
      onClick={(e) => {
        e.preventDefault();
        if (onChange) {
          onChange(e);
        }
      }}
      onKeyDown={(e) => {
        if (e.key === " " || e.key === "Enter") {
          e.preventDefault();
          if (onChange) {
            onChange(e);
          }
        }
      }}
      style={{ cursor: "pointer" }}
      {...props}
    >
      {checked && (
        <svg
          width="15"
          height="15"
          viewBox="0 0 13 13"
          className="block"
          aria-hidden="true"
        >
          <polyline
            points="3,7 6,10 10,3"
            fill="none"
            stroke="#fff"
            strokeWidth="2"
            strokeLinecap="round"
            strokeLinejoin="round"
          />
        </svg>
      )}
    </label>
  );

  // Custom Select All Checkbox (Centang Putih)
  const SelectAllCheckbox = ({ checked, onChange }) => (
    <label
      className={`inline-flex items-center justify-center w-6 h-6 rounded-[6px] border border-[#dbbdae] 
        ${checked ? "bg-[#fc8726] border-[#fc8726]" : "bg-white"}`}
      tabIndex={0}
      role="checkbox"
      aria-checked={checked}
      onClick={(e) => {
        e.preventDefault();
        onChange(!checked);
      }}
      onKeyDown={(e) => {
        if (e.key === " " || e.key === "Enter") {
          e.preventDefault();
          onChange(!checked);
        }
      }}
      style={{ verticalAlign: "middle", marginRight: 10, cursor: "pointer" }}
    >
      {checked && (
        <svg
          width="18"
          height="18"
          viewBox="0 0 13 13"
          className="block"
          aria-hidden="true"
        >
          <polyline
            points="3,7 6,10 10,3"
            fill="none"
            stroke="#fff"
            strokeWidth="2"
            strokeLinecap="round"
            strokeLinejoin="round"
          />
        </svg>
      )}
    </label>
  );

  return (
    <div className="min-h-screen bg-[#FAF7F2] flex flex-col">
      <TopNavbar />
      <main className="flex flex-1 flex-col md:flex-row gap-6 w-full max-w-6xl mx-auto mt-6 md:mt-10 px-3 md:px-6 pb-28">
        {/* List Kiri */}
        <div className="flex-1 flex flex-col items-stretch px-0 md:px-2 py-2 max-w-[700px] mx-auto md:mx-0 w-full">
          <div className="bg-white rounded-2xl shadow-sm p-4 md:p-6 mb-4 w-full border border-[#FBE3D4]">
            {/* Pilih Semua */}
            <div className="flex items-center mb-5 pb-3 border-b border-gray-100">
              <SelectAllCheckbox
                checked={checked.every(Boolean) && checked.length > 0}
                onChange={handleCheckAll}
              />
              <span className="ml-2 font-bold text-gray-800 text-base md:text-lg select-none">
                Pilih Semua
                <span className="ml-2 text-[#FE5A19] font-bold">
                  ({checked.filter(Boolean).length})
                </span>
              </span>
            </div>
            {/* Daftar Keranjang */}
            <div className="flex flex-col gap-3">
              {loading ? (
                <div className="text-center text-gray-400 py-12 text-base font-medium">
                  Memuat keranjang...
                </div>
              ) : cart.length === 0 ? (
                <div className="text-center text-gray-400 py-12 text-base font-medium">
                  Keranjang belanja kosong.
                </div>
              ) : (
                cart.map((item, i) => (
                  <div
                    key={item.id}
                    className="flex items-start bg-white rounded-xl shadow-sm border border-[#F5EBE1] px-3 md:px-5 py-3.5 hover:border-orange-200 transition-all duration-200"
                  >
                    {/* Checkbox */}
                    <span className="relative flex-shrink-0 mt-2">
                      <CustomCheckbox
                        checked={checked[i] || false}
                        onChange={() => handleCheck(i)}
                      />
                    </span>
                    {/* Gambar */}
                    <img
                      src={item.img}
                      alt={item.name}
                      className="w-16 h-16 object-cover rounded-xl bg-orange-50/50 border border-orange-100/60 flex-shrink-0 ml-3.5 shadow-sm"
                    />
                    {/* Info Produk */}
                    <div className="min-w-0 flex-1 ml-3.5">
                      <div className="font-bold text-gray-900 text-sm md:text-base mb-0.5 truncate">
                        {item.name}
                      </div>
                      {(item.desc || item.description) && (
                        <div className="text-xs text-gray-500 font-medium line-clamp-1">{item.desc || item.description}</div>
                      )}
                      <div className="flex items-center mt-2">
                        <span className="font-extrabold text-[#FE5A19] text-sm md:text-base whitespace-nowrap">
                          Rp {item.price.toLocaleString("id-ID")}
                        </span>
                      </div>
                    </div>
                    {/* Kanan: Qty & hapus */}
                    <div className="flex flex-col items-end min-w-[100px] ml-3 gap-2">
                      <div className="flex items-center bg-[#FFF8F3] rounded-lg px-2 py-0.5 space-x-2 border border-[#FBE3D4]">
                        <button
                          className="text-[#FE5A19] flex items-center justify-center w-7 h-7 font-bold text-base rounded-md hover:bg-orange-100 transition disabled:opacity-30"
                          aria-label="Kurangi jumlah"
                          onClick={() => handleQty(i, -1)}
                          disabled={item.quantity <= 1}
                        >
                          –
                        </button>
                        <span className="text-[#FE5A19] text-sm md:text-base font-bold w-6 text-center select-none">
                          {item.quantity}
                        </span>
                        <button
                          className="text-[#FE5A19] flex items-center justify-center w-7 h-7 font-bold text-base rounded-md hover:bg-orange-100 transition"
                          aria-label="Tambah jumlah"
                          onClick={() => handleQty(i, 1)}
                        >
                          +
                        </button>
                      </div>
                      <button
                        onClick={() => handleRemove(i)}
                        className="text-xs font-semibold text-rose-500 hover:text-rose-700 py-0.5 px-2 rounded hover:bg-rose-50 transition"
                        title="Hapus dari keranjang"
                      >
                        Hapus
                      </button>
                    </div>
                  </div>
                ))
              )}
            </div>
          </div>
        </div>
        {/* Ringkasan Belanja */}
        <aside className="w-full md:w-[350px] flex-shrink-0 px-0 md:px-0">
          <div className="bg-white rounded-2xl shadow-sm p-6 mx-auto max-w-sm sticky top-20 border border-[#FBE3D4]">
            <div className="flex justify-between items-center mb-4 pb-2 border-b border-gray-100">
              <span className="font-bold text-gray-900 text-base md:text-lg">
                Ringkasan Belanja
              </span>
              <span className="font-semibold text-gray-500 text-sm">
                Total
              </span>
            </div>
            <div className="flex justify-between items-baseline mb-5">
              <span className="text-gray-700 font-semibold text-sm">Total:</span>
              <span className="font-extrabold text-[#FE5A19] text-xl">
                Rp {total.toLocaleString("id-ID")}
              </span>
            </div>
            <button
              className={`w-full mb-4 py-3 rounded-xl font-bold text-sm shadow-md transition active:scale-[0.98]
              ${
                checked.some(Boolean)
                  ? "bg-[#FE5A19] hover:bg-[#E04B0E] text-white shadow-orange-500/20"
                  : "bg-gray-200 text-gray-400 cursor-not-allowed shadow-none"
              }`}
              onClick={handleCheckout}
              disabled={!checked.some(Boolean)}
            >
              Beli
              {checked.filter(Boolean).length > 0
                ? ` (${checked.filter(Boolean).length})`
                : ""}
            </button>
            <div className="flex items-center justify-center gap-2 bg-[#FFF8F3] text-orange-600 text-xs font-semibold rounded-lg px-3 py-2 border border-[#FBE3D4]">
              <svg
                width="16"
                height="16"
                fill="none"
                viewBox="0 0 20 20"
                className="inline flex-shrink-0"
              >
                <circle
                  cx="10"
                  cy="10"
                  r="8"
                  stroke="#FE5A19"
                  strokeWidth="1.4"
                  fill="none"
                ></circle>
                <rect
                  x="9"
                  y="5"
                  width="2"
                  height="6"
                  rx="1"
                  fill="#FE5A19"
                />
                <rect
                  x="9"
                  y="12.2"
                  width="2"
                  height="2"
                  rx="1"
                  fill="#FE5A19"
                />
              </svg>
              <span>Tidak ada promo yang berlaku.</span>
            </div>
          </div>
        </aside>
      </main>
      <BottomNavbar />
    </div>
  );
}

export default CartPage;