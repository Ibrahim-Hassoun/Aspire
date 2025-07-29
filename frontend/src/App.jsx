import React, { useState } from 'react';
import AuthPage from './pages/AuthPage';
import ProductsPage from './pages/ProductsPage';
import ChatbotPage from './pages/ChatbotPage';

export default function App() {
  const [token, setToken] = useState(localStorage.getItem('token') || '');
  const [user, setUser] = useState(JSON.parse(localStorage.getItem('user') || 'null'));
  const [page, setPage] = useState(token ? 'products' : 'auth');

  const handleAuth = (token, user) => {
    setToken(token);
    setUser(user);
    localStorage.setItem('token', token);
    localStorage.setItem('user', JSON.stringify(user));
    setPage('products');
  };

  const handleLogout = () => {
    setToken('');
    setUser(null);
    localStorage.removeItem('token');
    localStorage.removeItem('user');
    setPage('auth');
  };

  return (
    <div className="app">
      {page === 'auth' && <AuthPage onAuth={handleAuth} />}
      {page !== 'auth' && (
        <>
          <nav>
            <button onClick={() => setPage('products')}>Products</button>
            <button onClick={() => setPage('chatbot')}>Chatbot</button>
            <button onClick={handleLogout}>Logout</button>
            <span style={{ float: 'right' }}>Logged in as: <b>{user?.name}</b> ({user?.email})</span>
          </nav>
          {page === 'products' && <ProductsPage token={token} user={user} />}
          {page === 'chatbot' && <ChatbotPage token={token} />}
        </>
      )}
    </div>
  );
}
