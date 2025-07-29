import React, { useState } from 'react';
import axios from 'axios';


const API_URL = 'http://localhost:8000/api';

export default function AuthPage({ onAuth }) {
  const [isLogin, setIsLogin] = useState(true);
  const [form, setForm] = useState({ name: '', email: '', password: '', password_confirmation: '' });
  const [error, setError] = useState('');
  const [loading, setLoading] = useState(false);

  const handleChange = e => setForm({ ...form, [e.target.name]: e.target.value });

  const handleSubmit = async e => {
    e.preventDefault();
    setError('');
    setLoading(true);
    try {
      if (isLogin) {
        const res = await axios.post(`${API_URL}/auth/login`, { email: form.email, password: form.password });
        onAuth(res.data.data.token, res.data.data.user);
      } else {
        // Register, then immediately login to get fresh user data
        await axios.post(`${API_URL}/auth/register`, form);
        const loginRes = await axios.post(`${API_URL}/auth/login`, { email: form.email, password: form.password });
        onAuth(loginRes.data.data.token, loginRes.data.data.user);
      }
    } catch (err) {
      setError(err.response?.data?.message || 'Authentication failed');
    } finally {
      setLoading(false);
    }
  };

  return (
    <div className="auth-page" style={{ display: 'flex', flexDirection: 'column', alignItems: 'center', justifyContent: 'center', minHeight: '60vh' }}>
      <div style={{ background: '#fff', borderRadius: 16, boxShadow: '0 4px 24px rgba(25,118,210,0.08)', padding: 32, minWidth: 320, maxWidth: 400, width: '100%' }}>
        <h2 style={{ textAlign: 'center', color: '#1976d2', marginBottom: 24 }}>{isLogin ? 'Login' : 'Register'}</h2>
        <form onSubmit={handleSubmit} style={{ display: 'flex', flexDirection: 'column', gap: 12 }}>
          {!isLogin && (
            <input name="name" placeholder="Name" value={form.name} onChange={handleChange} required />
          )}
          <input name="email" type="email" placeholder="Email" value={form.email} onChange={handleChange} required />
          <input name="password" type="password" placeholder="Password" value={form.password} onChange={handleChange} required />
          {!isLogin && (
            <input name="password_confirmation" type="password" placeholder="Confirm Password" value={form.password_confirmation} onChange={handleChange} required />
          )}
          <button type="submit" disabled={loading} style={{ marginTop: 8 }}>{loading ? 'Please wait...' : (isLogin ? 'Login' : 'Register')}</button>
        </form>
        <button onClick={() => setIsLogin(!isLogin)} disabled={loading} style={{ marginTop: 16, background: '#42a5f5' }}>
          {isLogin ? 'Need an account? Register' : 'Already have an account? Login'}
        </button>
        {loading && <div style={{marginTop:8, color:'#1976d2', textAlign:'center'}}>Loading...</div>}
        {error && <div className="error" style={{ textAlign:'center' }}>{error}</div>}
      </div>
    </div>
  );
}
