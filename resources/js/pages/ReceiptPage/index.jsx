import React, { useState, useEffect } from "react";
import { useLocation } from "react-router-dom";
import BottomNavbar from "../../components/commons/molecules/BottomNavbar";
import TopNavbar from "../../components/commons/molecules/TopNavbar";

const statusList = [
  { label: "Semua", value: "all" },
  { label: "Pending", value: "pending" },
  { label: "Paid", value: "paid" },
  { label: "Shipped", value: "shipped" },
  { label: "Done", value: "done" },
  { label: "Cancel", value: "cancel" },
];

const statusLabels = {
  pending: "Pending",
  paid: "Dibayar",
  shipped: "Dikirim",
  done: "Selesai",
  cancel: "Dibatalkan",
};

const statusColors = {
  pending: { text: "text-yellow-600", bg: "bg-yellow-100" },
  paid: { text: "text-blue-600", bg: "bg-blue-100" },
  shipped: { text: "text-purple-600", bg: "bg-purple-100" },
  done: { text: "text-green-600", bg: "bg-green-100" },
  cancel: { text: "text-red-600", bg: "bg-red-100" },
};

function ReceiptPage() {
  const location = useLocation();
  const [receipts, setReceipts] = useState([]);
  const [loading, setLoading] = useState(true);
  const [search, setSearch] = useState("");
  const [date, setDate] = useState("");
  const [selectedStatus, setSelectedStatus] = useState("all");
  const [showDatePicker, setShowDatePicker] = useState(false);

  const fetchOrders = async () => {
    try {
      setLoading(true);
      const response = await fetch("/api/orders", {
        method: "GET",
        headers: {
          "Accept": "application/json",
        },
        credentials: "same-origin",
      });

      if (!response.ok) {
        if (response.status === 401) {
          window.location.href = "/login";
          return;
        }
        throw new Error("Gagal memuat pesanan");
      }

      const data = await response.json();
      setReceipts(data);
    } catch (error) {
      console.error("Error fetching orders:", error);
      setReceipts([]);
    } finally {
      setLoading(false);
    }
  };

  // Fetch orders from API
  useEffect(() => {
    fetchOrders();
  }, []);

  // Check if we came from checkout with orderId
  useEffect(() => {
    if (location.state?.orderId) {
      // Scroll to the specific order if needed
      setTimeout(() => {
        const orderElement = document.getElementById(`order-${location.state.orderId}`);
        if (orderElement) {
          orderElement.scrollIntoView({ behavior: "smooth", block: "center" });
        }
      }, 500);
    }
  }, [location.state, receipts]);

  const filteredReceipts = receipts.filter((r) => {
    const matchSearch =
      search === "" ||
      r.items.some(item => 
        item.product_name.toLowerCase().includes(search.toLowerCase())
      ) ||
      `#${r.id}`.toLowerCase().includes(search.toLowerCase());
    const matchDate = !date || r.created_at === date;
    const matchStatus =
      selectedStatus === "all" || r.status === selectedStatus;
    return matchSearch && matchDate && matchStatus;
  });

  // Convert the current date string to yyyy-mm-dd if there is value for the date input
  const getDateInputValue = () => {
    if (!date) return "";
    // expect format: "04 Agustus 2023", want: 2023-08-04
    const month = {
      Januari: "01",
      Februari: "02",
      Maret: "03",
      April: "04",
      Mei: "05",
      Juni: "06",
      Juli: "07",
      Agustus: "08",
      September: "09",
      Oktober: "10",
      November: "11",
      Desember: "12",
    };
    const [d, m, y] = date.split(" ");
    return `${y}-${month[m]}-${d.padStart(2, "0")}`;
  };

  // When picking a date, convert it into the displayed format style
  const handleDateChange = (e) => {
    const val = e.target.value; // yyyy-mm-dd
    if (!val) {
      setDate("");
      return;
    }
    const [y, m, d] = val.split("-");
    const monthName = [
      "", "Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli",
      "Agustus", "September", "Oktober", "November", "Desember"
    ][+m];
    setDate(`${d.padStart(2, "0")} ${monthName} ${y}`);
    setShowDatePicker(false);
  };

  // Handle cancel order
  const handleCancelOrder = async (orderId) => {
    if (!confirm("Apakah Anda yakin ingin membatalkan pesanan ini?")) {
      return;
    }

    try {
      const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
      
      const response = await fetch(`/api/orders/${orderId}/cancel`, {
        method: "PATCH",
        headers: {
          "Content-Type": "application/json",
          "X-CSRF-TOKEN": csrfToken || "",
          "Accept": "application/json",
        },
        credentials: "same-origin",
      });

      if (!response.ok) {
        const error = await response.json();
        alert(error.message || "Gagal membatalkan pesanan");
        return;
      }

      // Refresh orders list
      await fetchOrders();
      alert("Pesanan berhasil dibatalkan");
    } catch (error) {
      console.error("Error canceling order:", error);
      alert("Terjadi kesalahan saat membatalkan pesanan");
    }
  };

  // Handle mark order as done from shipped state
  const handleCompleteOrder = async (orderId) => {
    if (!confirm("Tandai pesanan ini selesai?")) {
      return;
    }

    try {
      const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

      const response = await fetch(`/api/orders/${orderId}/complete`, {
        method: "PATCH",
        headers: {
          "Content-Type": "application/json",
          "X-CSRF-TOKEN": csrfToken || "",
          "Accept": "application/json",
        },
        credentials: "same-origin",
      });

      if (!response.ok) {
        const error = await response.json();
        alert(error.message || "Gagal menyelesaikan pesanan");
        return;
      }

      await fetchOrders();
      alert("Pesanan ditandai selesai");
    } catch (error) {
      console.error("Error completing order:", error);
      alert("Terjadi kesalahan saat menyelesaikan pesanan");
    }
  };

  return (
    <div className="min-h-screen bg-[#fff5f0] flex flex-col">
      <TopNavbar />
      <div className="flex-1 p-3 sm:p-6">
        <div className="bg-[#ffe5d2] rounded-xl shadow-md px-5 py-3 mb-4 flex flex-col sm:flex-row items-start sm:items-center gap-3">
          <div className="flex items-center flex-1 w-full sm:w-auto">
            <div className="bg-white px-3 py-2 rounded-md flex items-center w-full">
              <svg width="18" height="18" fill="none" viewBox="0 0 20 20" className="mr-2 text-[#dbbdae]">
                <circle cx="9" cy="9" r="7" stroke="#dbbdae" strokeWidth="1.4" fill="none"/>
                <path d="M14.2 14.2l3.6 3.6" stroke="#dbbdae" strokeWidth="1.4" strokeLinecap="round"/>
              </svg>
              <input
                className="bg-transparent outline-none border-none w-full text-sm"
                placeholder="Cari transaksi mu di sini"
                value={search}
                onChange={e => setSearch(e.target.value)}
              />
            </div>
          </div>
          <div className="flex items-center relative">
            <button
              type="button"
              className="bg-white px-3 py-2 rounded-md flex items-center text-sm font-semibold outline-none border-none"
              onClick={() => setShowDatePicker((v) => !v)}
            >
              <svg width="16" height="16" fill="none" viewBox="0 0 20 20" className="mr-2 text-[#dbbdae]">
                <rect x="3" y="4" width="14" height="12" rx="3" fill="none" stroke="#dbbdae" strokeWidth="1.4"/>
                <rect x="8" y="2" width="4" height="4" rx="1" fill="none" stroke="#dbbdae" strokeWidth="1.1"/>
              </svg>
              {date ? date : "Pilih tanggal transaksi"}
              {date && (
                <span
                  className="ml-2 text-[#fc8726] font-bold cursor-pointer"
                  role="button"
                  title="Hapus tanggal"
                  onClick={(e) => { e.stopPropagation(); setDate(""); }}
                >
                  ×
                </span>
              )}
            </button>
            {showDatePicker && (
              <input
                type="date"
                value={getDateInputValue()}
                onChange={handleDateChange}
                className="absolute top-12 left-0 bg-white rounded-md shadow p-2 border text-[#8b572a]"
                style={{ zIndex: 30 }}
              />
            )}
          </div>
          <div className="flex items-center mt-2 sm:mt-0">
            <div className="flex space-x-2 overflow-x-auto py-1">
              {statusList.map((s) => (
                <button
                  key={s.value}
                  className={`px-3 py-1 rounded-full text-sm font-semibold ${
                    selectedStatus === s.value
                      ? "bg-[#fc8726] text-white"
                      : "bg-white text-[#8b572a]"
                  } border border-[#dbbdae]`}
                  onClick={() => setSelectedStatus(s.value)}
                >
                  {s.label}
                </button>
              ))}
            </div>
          </div>
        </div>
        
        <div className="bg-white rounded-xl shadow px-0 pb-4">
          <div className="flex flex-col gap-2 pt-4 pl-6 pr-6">
            {loading ? (
              <div className="text-center text-gray-400 py-10">Memuat pesanan...</div>
            ) : filteredReceipts.length === 0 ? (
              <div className="text-center text-gray-400 py-10">Transaksi tidak ditemukan.</div>
            ) : (
              filteredReceipts.map((receipt) => {
                const statusConfig = statusColors[receipt.status] || statusColors.pending;
                const isHighlighted = location.state?.orderId === receipt.id;
                
                return (
                  <div
                    key={receipt.id}
                    id={`order-${receipt.id}`}
                    className={`border border-[#f9e6d8] rounded-lg p-4 mb-4 transition ${
                      isHighlighted ? "ring-2 ring-[#fc8726] bg-[#fff8f4]" : "bg-white hover:bg-[#fff8f4]"
                    }`}
                  >
                    <div className="flex justify-between items-start mb-3">
                      <div>
                        <div className="font-bold text-[#934f19] text-lg">
                          Pesanan #{receipt.id}
                        </div>
                        <div className="text-sm text-gray-600">{receipt.created_at}</div>
                      </div>
                      <span className={`px-3 py-1 text-xs font-semibold rounded-full ${statusConfig.bg} ${statusConfig.text}`}>
                        {statusLabels[receipt.status] || receipt.status}
                      </span>
                    </div>
                    
                    <div className="space-y-2 mb-3">
                      {receipt.items.map((item, idx) => (
                        <div key={idx} className="flex justify-between items-center text-sm">
                          <span className="text-gray-700">
                            {item.product_name} × {item.quantity}
                          </span>
                          <span className="font-semibold text-gray-900">
                            Rp {(item.price * item.quantity).toLocaleString("id-ID")}
                          </span>
                        </div>
                      ))}
                    </div>
                    
                    <div className="flex justify-between items-center pt-3 border-t border-gray-200">
                      <div className="text-sm text-gray-600">
                        Metode: {receipt.payment_method ? receipt.payment_method.charAt(0).toUpperCase() + receipt.payment_method.slice(1) : "-"}
                      </div>
                      <div className="font-bold text-[#e16b27] text-lg">
                        Total: Rp {receipt.total_price.toLocaleString("id-ID")}
                      </div>
                    </div>
                    
                    {receipt.status === "pending" && (
                      <div className="mt-3 pt-3 border-t border-gray-200">
                        <button
                          onClick={() => handleCancelOrder(receipt.id)}
                          className="w-full px-4 py-2 bg-red-500 hover:bg-red-600 text-white rounded-lg font-semibold transition-colors"
                        >
                          Batalkan Pesanan
                        </button>
                      </div>
                    )}

                    {receipt.status === "shipped" && (
                      <div className="mt-3 pt-3 border-t border-gray-200">
                        <button
                          onClick={() => handleCompleteOrder(receipt.id)}
                          className="w-full px-4 py-2 bg-green-500 hover:bg-green-600 text-white rounded-lg font-semibold transition-colors"
                        >
                          Tandai Selesai
                        </button>
                      </div>
                    )}
                  </div>
                );
              })
            )}
          </div>
        </div>
      </div>
      <BottomNavbar />
    </div>
  );
}

export default ReceiptPage;
