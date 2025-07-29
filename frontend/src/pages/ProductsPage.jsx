import React, { useEffect, useState } from 'react';
import axios from 'axios';

const API_URL = 'http://localhost:8000/api';

export default function ProductsPage({ token, user }) {
  const [products, setProducts] = useState([]);
  const [form, setForm] = useState({ name: '', quantity: '', category: '', description: '', price: '', status: 'in_stock', imageurl: '' });
  const [search, setSearch] = useState('');
  const [error, setError] = useState('');
  const [editing, setEditing] = useState(null);

  const fetchProducts = async () => {
    try {
      const res = await axios.get(`${API_URL}/products`, {
        headers: { Authorization: `Bearer ${token}` },
        params: search ? { search } : {}
      });
      setProducts(res.data.data.products);
      setError('');
    } catch (err) {
      let msg = 'Failed to fetch products';
      if (err.response) {
        msg += ': ' + (err.response.data?.message || err.response.statusText);
      } else if (err.request) {
        msg += ': No response from server';
      } else if (err.message) {
        msg += ': ' + err.message;
      }
      setError(msg);
    }
  };

  useEffect(() => { fetchProducts(); }, [search]);

  const handleChange = e => setForm({ ...form, [e.target.name]: e.target.value });

  const handleAddOrEdit = async e => {
    e.preventDefault();
    setError('');
    try {
      if (editing) {
        await axios.put(`${API_URL}/products/${editing.id}`, form, { headers: { Authorization: `Bearer ${token}` } });
      } else {
        await axios.post(`${API_URL}/products`, form, { headers: { Authorization: `Bearer ${token}` } });
      }
      setForm({ name: '', quantity: '', category: '', description: '', price: '', status: 'in_stock', imageurl: '' });
      setEditing(null);
      fetchProducts();
    } catch (err) {
      setError('Failed to save product');
    }
  };

  const handleEdit = product => {
    setEditing(product);
    setForm(product);
  };

  const handleDelete = async id => {
    if (user.id !== 1) return setError('Only admin can delete products');
    try {
      await axios.delete(`${API_URL}/products/${id}`, { headers: { Authorization: `Bearer ${token}` } });
      fetchProducts();
    } catch (err) {
      setError('Failed to delete product');
    }
  };

  return (
    <div className="products-page" style={{ display: 'flex', flexDirection: 'column', alignItems: 'center', minHeight: '60vh' }}>
      <div style={{ background: '#fff', borderRadius: 16, boxShadow: '0 4px 24px rgba(25,118,210,0.08)', padding: 32, minWidth: 320, maxWidth: 700, width: '100%' }}>
        <h2 style={{ textAlign: 'center', color: '#1976d2', marginBottom: 24 }}>Products</h2>
        <input placeholder="Search products" value={search} onChange={e => setSearch(e.target.value)} style={{ marginBottom: 18 }} />
        <form onSubmit={handleAddOrEdit} style={{ display: 'flex', flexWrap: 'wrap', gap: 12, marginBottom: 24 }}>
          <input name="name" placeholder="Name" value={form.name} onChange={handleChange} required style={{ flex: '1 1 120px' }} />
          <input name="quantity" type="number" placeholder="Quantity" value={form.quantity} onChange={handleChange} required style={{ flex: '1 1 80px' }} />
          <input name="category" placeholder="Category" value={form.category} onChange={handleChange} style={{ flex: '1 1 120px' }} />
          <input name="description" placeholder="Description" value={form.description} onChange={handleChange} style={{ flex: '2 1 200px' }} />
          <input name="price" type="number" step="0.01" placeholder="Price" value={form.price} onChange={handleChange} required style={{ flex: '1 1 80px' }} />
          <select name="status" value={form.status} onChange={handleChange} style={{ flex: '1 1 120px' }}>
            <option value="in_stock">In Stock</option>
            <option value="low_stock">Low Stock</option>
            <option value="ordered">Ordered</option>
            <option value="discontinued">Discontinued</option>
          </select>
          <input name="imageurl" placeholder="Image URL" value={form.imageurl} onChange={handleChange} style={{ flex: '2 1 200px' }} />
          <button type="submit" style={{ background: '#1976d2', color: '#fff', fontWeight: 500 }}>{editing ? 'Update' : 'Add'} Product</button>
          {editing && <button type="button" onClick={() => { setEditing(null); setForm({ name: '', quantity: '', category: '', description: '', price: '', status: 'in_stock', imageurl: '' }); }} style={{ background: '#d32f2f', color: '#fff' }}>Cancel</button>}
        </form>
        {error && <div className="error" style={{ textAlign:'center' }}>{error}</div>}
        <ul style={{ marginTop: 24 }}>
          {products.map(product => (
            <li key={product.id} style={{ display: 'flex', alignItems: 'center', gap: 16, marginBottom: 16, background: '#f1f1f1', padding: 12, borderRadius: 10, boxShadow: '0 1px 4px rgba(0,0,0,0.04)', flexWrap: 'wrap' }}>
              <img src={product.imageurl} alt={product.name} style={{ borderRadius: 8, border: '1px solid #ccc', width: 60, height: 60, objectFit: 'cover' }} />
              <div style={{ flex: 1 }}>
                <b style={{ fontSize: '1.1rem' }}>{product.name}</b> <span style={{ color: '#1976d2' }}>({product.category})</span><br />
                <span style={{ color: '#333' }}>{product.quantity} pcs - ${product.price} - <span style={{ color: '#388e3c' }}>{product.status}</span></span>
              </div>
              <button onClick={() => handleEdit(product)} style={{ background: '#42a5f5', color: '#fff' }}>Edit</button>
              {user.id === 1 && <button onClick={() => handleDelete(product.id)} style={{ background: '#d32f2f', color: '#fff' }}>Delete</button>}
            </li>
          ))}
        </ul>
      </div>
    </div>
  );
}
