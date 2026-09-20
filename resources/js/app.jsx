import './bootstrap';
import React from 'react';
import { createRoot } from 'react-dom/client';
import { BrowserRouter } from 'react-router-dom';
import Page from './routes';

console.log('App.jsx loaded');

const container = document.getElementById('app');
console.log('Container element:', container);

if (container) {
    const root = createRoot(container);
    console.log('React root created, rendering...');
    root.render(
        <React.StrictMode>
            <BrowserRouter>
                <Page />
            </BrowserRouter>
        </React.StrictMode>
    );
    console.log('React app rendered');
} else {
    console.error('Container element dengan id "app" tidak ditemukan!');
}


