import React, { useEffect, useState } from 'react';
import { Navigate } from 'react-router-dom';
import HomePage from '../pages/HomePage';
import Intro from '../pages/Intro';
import CartPage from '../pages/CartPage';
import CheckoutPage from '../pages/CheckoutPage';
import ReceiptPage from '../pages/ReceiptPage';
import LocationPage from '../pages/LocationPage';

console.log('AppRoutes loaded');

/**
 * Menentukan arah redirect ketika route tidak ditemukan:
 * - Jika sesi masih ada (berhasil panggil /api/user), arahkan ke /home.
 * - Jika tidak ada sesi/unauthorized, paksa pindah ke halaman login server.
 */
const NotFoundRedirect = () => {
  const [status, setStatus] = useState('checking');

  useEffect(() => {
    let cancelled = false;

    const checkSession = async () => {
      try {
        const res = await fetch('/api/user', {
          method: 'GET',
          credentials: 'same-origin',
          headers: { Accept: 'application/json' },
        });

        if (!cancelled) {
          setStatus(res.ok ? 'authenticated' : 'guest');
        }
      } catch (err) {
        if (!cancelled) {
          console.error('Failed to check session for redirect:', err);
          setStatus('guest');
        }
      }
    };

    checkSession();
    return () => {
      cancelled = true;
    };
  }, []);

  if (status === 'checking') {
    return <div style={{ padding: '20px', fontSize: '18px' }}>Memeriksa sesi...</div>;
  }

  if (status === 'authenticated') {
    return <Navigate to="/home" replace />;
  }

  // Guest / tidak ada sesi -> full reload ke halaman login Laravel
  window.location.href = '/login';
  return <div style={{ padding: '20px', fontSize: '18px' }}>Mengarahkan ke halaman login...</div>;
};

// Paksa redirect penuh ke halaman login Laravel (bukan route React)
const LoginRedirect = () => {
  useEffect(() => {
    window.location.href = '/login';
  }, []);
  return <div style={{ padding: '20px', fontSize: '18px' }}>Mengarahkan ke halaman login...</div>;
};

const AppRoutes = [
  {
    path: '/',
    element: <Intro />,
  },
  {
    path: '/home',
    element: <HomePage/>,
  },
  {
    path: '/homepage',
    element: <Navigate to="/home" replace />,
  },
  {
    path: '/login',
    element: <LoginRedirect />,
  },
  {
    path: '/cart',
    element: <CartPage />,
  },
  {
    path: '/checkout',
    element: <CheckoutPage />,
  },
  {
    path: '/receipt',
    element: <ReceiptPage />,
  },
  {
    path: '/orders',
    element: <ReceiptPage />,
  },
  {
    path: '/location',
    element: <LocationPage />,
  },
  {
    path: '*',
    element: <NotFoundRedirect />,
  },
  // Tambahkan route lain di sini jika ada halaman tambahan, misal Register, Dashboard, dsb.
];

export default AppRoutes;
