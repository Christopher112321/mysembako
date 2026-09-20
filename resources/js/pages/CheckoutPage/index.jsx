import React, { useState, useEffect } from "react";
import { useNavigate, useLocation } from "react-router-dom";
import BottomNavbar from "../../components/commons/molecules/BottomNavbar";
import TopNavbar from "../../components/commons/molecules/TopNavbar";

function CheckoutPage() {
  const navigate = useNavigate();
  const location = useLocation();
  
  // Get selected items from location state or fetch from cart
  const selectedItems = location.state?.items || [];
  
  const [user, setUser] = useState(null);
  const [addresses, setAddresses] = useState([]);
  const [selectedAddress, setSelectedAddress] = useState(null);
  const [paymentMethod, setPaymentMethod] = useState("cash");
  const [loading, setLoading] = useState(true);
  const [submitting, setSubmitting] = useState(false);
  const [showAddressForm, setShowAddressForm] = useState(false);
  
  // New address form state
  const [newAddress, setNewAddress] = useState({
    label: "",
    address: "",
    city: "",
    province: "",
    postal_code: "",
    phone: "",
    is_default: false,
  });

  // Fetch user info and addresses
  useEffect(() => {
    if (selectedItems.length === 0) {
      // No items selected, redirect back to cart
      navigate("/cart");
      return;
    }

    const fetchData = async () => {
      try {
        setLoading(true);
        
        // Fetch user info
        const userResponse = await fetch("/api/user", {
          method: "GET",
          headers: {
            "Accept": "application/json",
          },
          credentials: "same-origin",
        });

        if (userResponse.ok) {
          const userData = await userResponse.json();
          setUser(userData);
        }

        // Fetch addresses
        const addressResponse = await fetch("/api/addresses", {
          method: "GET",
          headers: {
            "Accept": "application/json",
          },
          credentials: "same-origin",
        });

        if (addressResponse.ok) {
          const addressData = await addressResponse.json();
          setAddresses(addressData);
          
          // Select default address if available
          const defaultAddr = addressData.find(addr => addr.is_default);
          if (defaultAddr) {
            setSelectedAddress(defaultAddr.id);
          } else if (addressData.length > 0) {
            setSelectedAddress(addressData[0].id);
          }
        }
      } catch (error) {
        console.error("Error fetching data:", error);
      } finally {
        setLoading(false);
      }
    };

    fetchData();
  }, [navigate, selectedItems]);

  // Calculate totals
  const subtotal = selectedItems.reduce(
    (sum, item) => sum + item.price * item.quantity,
    0
  );
  const total = subtotal;

  // Handle address selection
  const handleAddressSelect = (addressId) => {
    setSelectedAddress(addressId);
  };

  // Handle add new address
  const handleAddAddress = async (e) => {
    e.preventDefault();
    
    try {
      const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
      
      const response = await fetch("/api/addresses", {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
          "X-CSRF-TOKEN": csrfToken || "",
          "Accept": "application/json",
        },
        credentials: "same-origin",
        body: JSON.stringify(newAddress),
      });

      if (response.ok) {
        const data = await response.json();
        
        // Refresh addresses
        const addressResponse = await fetch("/api/addresses", {
          headers: { "Accept": "application/json" },
          credentials: "same-origin",
        });
        const addressData = await addressResponse.json();
        setAddresses(addressData);
        setSelectedAddress(data.id);
        setShowAddressForm(false);
        setNewAddress({
          label: "",
          address: "",
          city: "",
          province: "",
          postal_code: "",
          phone: "",
          is_default: false,
        });
      } else {
        const error = await response.json();
        alert(error.message || "Gagal menambahkan alamat");
      }
    } catch (error) {
      console.error("Error adding address:", error);
      alert("Terjadi kesalahan saat menambahkan alamat");
    }
  };

  // Handle checkout submission
  const handleCheckout = async () => {
    if (!selectedAddress) {
      alert("Pilih alamat pengiriman terlebih dahulu");
      return;
    }

    if (!paymentMethod) {
      alert("Pilih metode pembayaran terlebih dahulu");
      return;
    }

    try {
      setSubmitting(true);
      const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
      
      // Prepare order data
      const orderData = {
        items: selectedItems.map(item => ({
          product_id: item.product_id,
          quantity: item.quantity,
          price: item.price,
        })),
        address_id: selectedAddress,
        payment_method: paymentMethod,
        total_price: total,
      };

      const response = await fetch("/api/checkout", {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
          "X-CSRF-TOKEN": csrfToken || "",
          "Accept": "application/json",
        },
        credentials: "same-origin",
        body: JSON.stringify(orderData),
      });

      if (response.ok) {
        const data = await response.json();
        // Redirect to receipt page with order ID
        navigate("/receipt", { state: { orderId: data.order_id, status: "pending" } });
      } else {
        const error = await response.json();
        alert(error.message || "Gagal melakukan checkout");
      }
    } catch (error) {
      console.error("Error during checkout:", error);
      alert("Terjadi kesalahan saat melakukan checkout");
    } finally {
      setSubmitting(false);
    }
  };

  if (loading) {
    return (
      <div className="min-h-screen bg-[#fff6f2] flex items-center justify-center">
        <div className="text-center text-[#c0b2a2] text-lg font-semibold">
          Memuat data checkout...
        </div>
      </div>
    );
  }

  const selectedAddressData = addresses.find(addr => addr.id === selectedAddress);

  return (
    <div className="min-h-screen bg-[#fff6f2] flex flex-col">
      <TopNavbar />
      <main className="flex-1 max-w-6xl mx-auto w-full mt-4 md:mt-8 px-4 md:px-6 pb-20">
        <div className="mb-6">
          <h1 className="text-2xl md:text-3xl font-bold text-[#934f19]">
            Checkout
          </h1>
        </div>

        <div className="grid grid-cols-1 lg:grid-cols-3 gap-6">
          {/* Left Column - Form */}
          <div className="lg:col-span-2 space-y-6">
            {/* Alamat Pengiriman */}
            <div className="bg-white rounded-2xl shadow-lg p-4 md:p-6 border border-[#ffe1c1]">
              <h2 className="text-xl font-bold text-[#934f19] mb-4">
                Alamat Pengiriman
              </h2>
              
              {addresses.length === 0 ? (
                <div className="text-center py-4">
                  <p className="text-gray-600 mb-4">Belum ada alamat tersimpan</p>
                  <button
                    onClick={() => setShowAddressForm(true)}
                    className="px-4 py-2 bg-[#fc8726] text-white rounded-lg font-semibold hover:bg-[#e16b27]"
                  >
                    Tambah Alamat
                  </button>
                </div>
              ) : (
                <div className="space-y-3">
                  {addresses.map((address) => (
                    <label
                      key={address.id}
                      className={`flex items-start p-4 border-2 rounded-lg cursor-pointer transition ${
                        selectedAddress === address.id
                          ? "border-[#fc8726] bg-[#fff8f4]"
                          : "border-[#f9e6d8] hover:border-[#fc8726]"
                      }`}
                    >
                      <input
                        type="radio"
                        name="address"
                        value={address.id}
                        checked={selectedAddress === address.id}
                        onChange={() => handleAddressSelect(address.id)}
                        className="mt-1 mr-3"
                      />
                      <div className="flex-1">
                        {address.label && (
                          <div className="font-semibold text-[#c96e33] mb-1">
                            {address.label}
                          </div>
                        )}
                        <div className="text-sm text-gray-700">
                          {address.address}, {address.city}, {address.province} {address.postal_code}
                        </div>
                        {address.phone && (
                          <div className="text-sm text-gray-600 mt-1">
                            {address.phone}
                          </div>
                        )}
                      </div>
                    </label>
                  ))}
                  
                  <button
                    onClick={() => setShowAddressForm(true)}
                    className="w-full px-4 py-3 border-2 border-dashed border-[#dbbdae] rounded-lg text-[#fc8726] font-semibold hover:bg-[#fff8f4] transition"
                  >
                    + Tambah Alamat Baru
                  </button>
                </div>
              )}

              {/* Address Form Modal */}
              {showAddressForm && (
                <div className="fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4">
                  <div className="bg-white rounded-2xl p-6 max-w-md w-full max-h-[90vh] overflow-y-auto">
                    <div className="flex justify-between items-center mb-4">
                      <h3 className="text-xl font-bold text-[#934f19]">
                        Tambah Alamat Baru
                      </h3>
                      <button
                        onClick={() => setShowAddressForm(false)}
                        className="text-2xl text-gray-500 hover:text-gray-700"
                      >
                        ×
                      </button>
                    </div>
                    
                    <form onSubmit={handleAddAddress} className="space-y-4">
                      <div>
                        <label className="block text-sm font-semibold text-gray-700 mb-2">
                          Label (opsional)
                        </label>
                        <input
                          type="text"
                          value={newAddress.label}
                          onChange={(e) =>
                            setNewAddress({ ...newAddress, label: e.target.value })
                          }
                          placeholder="Contoh: Rumah, Kantor"
                          className="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-[#fc8726]"
                        />
                      </div>
                      
                      <div>
                        <label className="block text-sm font-semibold text-gray-700 mb-2">
                          Alamat Lengkap <span className="text-red-500">*</span>
                        </label>
                        <textarea
                          value={newAddress.address}
                          onChange={(e) =>
                            setNewAddress({ ...newAddress, address: e.target.value })
                          }
                          required
                          rows="3"
                          placeholder="Jl. Contoh No. 123"
                          className="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-[#fc8726]"
                        />
                      </div>
                      
                      <div className="grid grid-cols-2 gap-4">
                        <div>
                          <label className="block text-sm font-semibold text-gray-700 mb-2">
                            Kota <span className="text-red-500">*</span>
                          </label>
                          <input
                            type="text"
                            value={newAddress.city}
                            onChange={(e) =>
                              setNewAddress({ ...newAddress, city: e.target.value })
                            }
                            required
                            className="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-[#fc8726]"
                          />
                        </div>
                        
                        <div>
                          <label className="block text-sm font-semibold text-gray-700 mb-2">
                            Provinsi <span className="text-red-500">*</span>
                          </label>
                          <input
                            type="text"
                            value={newAddress.province}
                            onChange={(e) =>
                              setNewAddress({ ...newAddress, province: e.target.value })
                            }
                            required
                            className="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-[#fc8726]"
                          />
                        </div>
                      </div>
                      
                      <div className="grid grid-cols-2 gap-4">
                        <div>
                          <label className="block text-sm font-semibold text-gray-700 mb-2">
                            Kode Pos <span className="text-red-500">*</span>
                          </label>
                          <input
                            type="text"
                            value={newAddress.postal_code}
                            onChange={(e) =>
                              setNewAddress({ ...newAddress, postal_code: e.target.value })
                            }
                            required
                            className="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-[#fc8726]"
                          />
                        </div>
                        
                        <div>
                          <label className="block text-sm font-semibold text-gray-700 mb-2">
                            No. Telepon
                          </label>
                          <input
                            type="text"
                            value={newAddress.phone}
                            onChange={(e) =>
                              setNewAddress({ ...newAddress, phone: e.target.value })
                            }
                            className="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-[#fc8726]"
                          />
                        </div>
                      </div>
                      
                      <div className="flex items-center">
                        <input
                          type="checkbox"
                          id="is_default"
                          checked={newAddress.is_default}
                          onChange={(e) =>
                            setNewAddress({ ...newAddress, is_default: e.target.checked })
                          }
                          className="mr-2"
                        />
                        <label htmlFor="is_default" className="text-sm text-gray-700">
                          Jadikan alamat utama
                        </label>
                      </div>
                      
                      <div className="flex gap-3 pt-4">
                        <button
                          type="button"
                          onClick={() => setShowAddressForm(false)}
                          className="flex-1 px-4 py-2 bg-gray-300 text-gray-700 rounded-lg font-semibold hover:bg-gray-400"
                        >
                          Batal
                        </button>
                        <button
                          type="submit"
                          className="flex-1 px-4 py-2 bg-[#fc8726] text-white rounded-lg font-semibold hover:bg-[#e16b27]"
                        >
                          Simpan
                        </button>
                      </div>
                    </form>
                  </div>
                </div>
              )}
            </div>

            {/* Informasi Pembeli */}
            <div className="bg-white rounded-2xl shadow-lg p-4 md:p-6 border border-[#ffe1c1]">
              <h2 className="text-xl font-bold text-[#934f19] mb-4">
                Informasi Pembeli
              </h2>
              <div className="space-y-2">
                <div>
                  <span className="text-sm text-gray-600">Nama:</span>
                  <span className="ml-2 font-semibold text-gray-900">
                    {user?.name || "-"}
                  </span>
                </div>
                <div>
                  <span className="text-sm text-gray-600">Email:</span>
                  <span className="ml-2 font-semibold text-gray-900">
                    {user?.email || "-"}
                  </span>
                </div>
                {user?.phone && (
                  <div>
                    <span className="text-sm text-gray-600">No. Telepon:</span>
                    <span className="ml-2 font-semibold text-gray-900">
                      {user.phone}
                    </span>
                  </div>
                )}
              </div>
            </div>

            {/* Metode Pembayaran */}
            <div className="bg-white rounded-2xl shadow-lg p-4 md:p-6 border border-[#ffe1c1]">
              <h2 className="text-xl font-bold text-[#934f19] mb-4">
                Metode Pembayaran
              </h2>
              <div className="space-y-3">
                <label className="flex items-center p-4 border-2 rounded-lg cursor-pointer transition border-[#f9e6d8] hover:border-[#fc8726]">
                  <input
                    type="radio"
                    name="payment"
                    value="cash"
                    checked={paymentMethod === "cash"}
                    onChange={(e) => setPaymentMethod(e.target.value)}
                    className="mr-3"
                  />
                  <div className="flex-1">
                    <div className="font-semibold text-gray-900">
                      Cash on Delivery (COD)
                    </div>
                    <div className="text-sm text-gray-600">
                      Bayar saat barang diterima
                    </div>
                  </div>
                </label>
                
                <label className="flex items-center p-4 border-2 rounded-lg cursor-pointer transition border-[#f9e6d8] hover:border-[#fc8726]">
                  <input
                    type="radio"
                    name="payment"
                    value="transfer"
                    checked={paymentMethod === "transfer"}
                    onChange={(e) => setPaymentMethod(e.target.value)}
                    className="mr-3"
                  />
                  <div className="flex-1">
                    <div className="font-semibold text-gray-900">
                      Transfer Bank
                    </div>
                    <div className="text-sm text-gray-600">
                      Transfer melalui bank
                    </div>
                  </div>
                </label>
              </div>
            </div>
          </div>

          {/* Right Column - Order Summary */}
          <div className="lg:col-span-1">
            <div className="bg-white rounded-2xl shadow-lg p-4 md:p-6 sticky top-4 border border-[#ffe1c1]">
              <h2 className="text-xl font-bold text-[#934f19] mb-4">
                Ringkasan Pesanan
              </h2>

              {/* Items List */}
              <div className="space-y-3 mb-4 max-h-[300px] overflow-y-auto">
                {selectedItems.map((item, index) => (
                  <div key={index} className="flex items-center gap-3 pb-3 border-b border-gray-200">
                    <img
                      src={item.img}
                      alt={item.name}
                      className="w-16 h-16 object-contain rounded-lg bg-white border border-[#ffe5d2]"
                    />
                    <div className="flex-1 min-w-0">
                      <div className="font-semibold text-gray-900 text-sm truncate">
                        {item.name}
                      </div>
                      <div className="text-xs text-gray-600">
                        {item.quantity} × Rp {item.price.toLocaleString("id-ID")}
                      </div>
                    </div>
                    <div className="font-semibold text-[#e16b27] text-sm">
                      Rp {(item.price * item.quantity).toLocaleString("id-ID")}
                    </div>
                  </div>
                ))}
              </div>

              {/* Price Summary */}
              <div className="space-y-2 mb-6 pt-4 border-t border-gray-200">
                <div className="flex justify-between text-gray-700">
                  <span>Subtotal:</span>
                  <span className="font-semibold">
                    Rp {subtotal.toLocaleString("id-ID")}
                  </span>
                </div>
                <div className="flex justify-between text-lg font-bold text-[#e16b27] pt-2 border-t border-gray-200">
                  <span>Total:</span>
                  <span>Rp {total.toLocaleString("id-ID")}</span>
                </div>
              </div>

              {/* Checkout Button */}
              <button
                onClick={handleCheckout}
                disabled={submitting || !selectedAddress}
                className={`w-full py-3 rounded-xl font-semibold text-base shadow-sm transition ${
                  submitting || !selectedAddress
                    ? "bg-gray-400 text-white cursor-not-allowed"
                    : "bg-[#fc8726] hover:bg-[#e16b27] text-white"
                }`}
              >
                {submitting ? "Memproses..." : "Buat Pesanan"}
              </button>
            </div>
          </div>
        </div>
      </main>
      <BottomNavbar />
    </div>
  );
}

export default CheckoutPage;

