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
    <div className="min-h-screen bg-[#fff6f2] flex flex-col">
      <TopNavbar />
      <main className="flex flex-1 flex-col md:flex-row gap-6 w-full max-w-6xl mx-auto mt-8 md:mt-14 px-2 md:px-6">
        {/* List Kiri */}
        <div className="flex-1 flex flex-col items-stretch px-0 md:px-4 py-4 max-w-[700px] mx-auto md:mx-0">
          <div className="bg-[#fff] rounded-2xl shadow-lg p-3 md:p-6 mb-4 w-full border border-[#ffe1c1]">
            {/* Pilih Semua */}
            <div className="flex items-center mb-5">
              <SelectAllCheckbox
                checked={checked.every(Boolean) && checked.length > 0}
                onChange={handleCheckAll}
              />
              <span className="ml-1 font-semibold text-[#934f19] text-lg select-none">
                Pilih Semua
                <span className="ml-2 text-[#fc8726] font-bold">
                  ({checked.filter(Boolean).length})
                </span>
              </span>
            </div>
            {/* Daftar Keranjang */}
            <div className="flex flex-col gap-4">
              {loading ? (
                <div className="text-center text-[#c0b2a2] py-12 text-lg font-semibold">
                  Memuat keranjang...
                </div>
              ) : cart.length === 0 ? (
                <div className="text-center text-[#c0b2a2] py-12 text-lg font-semibold">
                  Keranjang belanja kosong.
                </div>
              ) : (
                cart.map((item, i) => (
                  <div
                    key={item.id}
                    className="flex items-start bg-white rounded-lg shadow-sm border border-[#f9e6d8] px-3 md:px-5 py-3 hover:bg-[#fff8f4] group transition"
                  >
                    {/* Checkbox (custom centang putih dan kotak orange) */}
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
                      className="w-[62px] h-[62px] object-contain rounded-lg bg-white ring-2 ring-[#ffe5d2] flex-shrink-0 ml-4 shadow-md"
                    />
                    {/* Info Produk */}
                    <div className="min-w-0 flex-1 ml-4">
                      <div className="font-bold text-[#c96e33] text-lg mb-0.5 truncate max-w-[220px]">
                        {item.name}
                      </div>
                      {(item.desc || item.description) && (
                        <div className="text-xs text-[#b68552] font-medium">{item.desc || item.description}</div>
                      )}
                      <div className="flex items-center mt-2 gap-4">
                        <span className="font-bold text-[#e16b27] text-base whitespace-nowrap">
                          Rp {item.price.toLocaleString("id-ID")}
                        </span>
                      </div>
                    </div>
                    {/* Kanan: Qty & hapus */}
                    <div className="flex flex-col items-end min-w-[120px] ml-4 gap-3 mt-1">
                      <div className="flex items-center bg-[#fff4e8] rounded-lg px-2 py-1 space-x-2 border border-[#ffe5d2]">
                        <button
                          className="text-[#fc8726] flex items-center justify-center w-7 h-7 font-bold text-lg rounded-full transition hover:bg-[#ffe5d2] disabled:opacity-40"
                          aria-label="Kurangi jumlah"
                          onClick={() => handleQty(i, -1)}
                          disabled={item.quantity <= 1}
                          style={{
                            border: "none",
                            background: "none",
                            outline: "none",
                          }}
                          tabIndex={0}
                        >
                          –
                        </button>
                        <span className="text-[#e16b27] text-base font-extrabold w-7 text-center select-none">
                          {item.quantity}
                        </span>
                        <button
                          className="text-[#fc8726] flex items-center justify-center w-7 h-7 font-bold text-lg rounded-full transition hover:bg-[#ffe5d2]"
                          aria-label="Tambah jumlah"
                          onClick={() => handleQty(i, 1)}
                          style={{
                            border: "none",
                            background: "none",
                            outline: "none",
                          }}
                          tabIndex={0}
                        >
                          +
                        </button>
                      </div>
                      <button
                        onClick={() => handleRemove(i)}
                        className="mt-1 text-[#e06f6a] text-xs font-bold px-2 py-[3px] rounded-md bg-[#fff4f6] hover:bg-[#ffebef] border border-[#ffe1e6] shadow-sm transition hidden group-hover:inline-block"
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
        <aside className="w-full md:w-[350px] flex-shrink-0 px-0 md:px-0 mt-0 md:mt-4">
          <div className="bg-[#fff] rounded-2xl shadow-lg p-6 mx-auto max-w-xs sticky top-7 md:top-10 border border-[#ffe1c1]">
            <div className="flex justify-between items-center mb-4">
              <span className="font-extrabold text-[#964e0e] text-lg">
                Ringkasan Belanja
              </span>
              <span className="font-semibold text-[#c97d2b] text-base">
                Total
              </span>
            </div>
            <div className="flex justify-between items-baseline mb-4">
              <span className="text-[#C96E33] font-bold text-base">Total:</span>
              <span className="font-bold text-[#e16b27] text-xl">
                Rp {total.toLocaleString("id-ID")}
              </span>
            </div>
            <button
              className={`w-full mb-4 py-2.5 rounded-xl font-semibold text-base shadow-sm transition
              ${
                checked.some(Boolean)
                  ? "bg-[#fc8726] hover:bg-[#fff2ea] text-white"
                  : "bg-[#e7aa74] text-white cursor-not-allowed"
              }`}
              onClick={handleCheckout}
              disabled={!checked.some(Boolean)}
            >
              Beli
              {checked.filter(Boolean).length > 0
                ? ` (${checked.filter(Boolean).length})`
                : ""}
            </button>
            <div className="flex items-center justify-center gap-2 bg-[#fff7ee] text-[#fc8726] text-xs font-semibold rounded-lg px-3 py-2 border border-[#f9e6d8]">
              <svg
                width="18"
                height="18"
                fill="none"
                viewBox="0 0 20 20"
                className="inline"
                style={{ marginRight: 4 }}
              >
                <circle
                  cx="10"
                  cy="10"
                  r="8"
                  stroke="#fc8726"
                  strokeWidth="1.4"
                  fill="none"
                ></circle>
                <rect
                  x="9"
                  y="5"
                  width="2"
                  height="6"
                  rx="1"
                  fill="#fc8726"
                />
                <rect
                  x="9"
                  y="12.2"
                  width="2"
                  height="2"
                  rx="1"
                  fill="#fc8726"
                />
              </svg>
              Tidak ada promo yang berlaku.
            </div>
          </div>
        </aside>
      </main>
      <BottomNavbar />
    </div>
  );
}

export default CartPage;