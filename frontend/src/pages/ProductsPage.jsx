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
    } catch (err) {
      setError('Failed to fetch products');
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
    <div className="products-page">
      <h2>Products</h2>
      <input placeholder="Search products" value={search} onChange={e => setSearch(e.target.value)} />
      <form onSubmit={handleAddOrEdit}>
        <input name="name" placeholder="Name" value={form.name} onChange={handleChange} required />
        <input name="quantity" type="number" placeholder="Quantity" value={form.quantity} onChange={handleChange} required />
        <input name="category" placeholder="Category" value={form.category} onChange={handleChange} />
        <input name="description" placeholder="Description" value={form.description} onChange={handleChange} />
        <input name="price" type="number" step="0.01" placeholder="Price" value={form.price} onChange={handleChange} required />
        <select name="status" value={form.status} onChange={handleChange}>
          <option value="in_stock">In Stock</option>
          <option value="low_stock">Low Stock</option>
          <option value="ordered">Ordered</option>
          <option value="discontinued">Discontinued</option>
        </select>
        <input name="imageurl" placeholder="Image URL" value={form.imageurl} onChange={handleChange} />
        <button type="submit">{editing ? 'Update' : 'Add'} Product</button>
        {editing && <button type="button" onClick={() => { setEditing(null); setForm({ name: '', quantity: '', category: '', description: '', price: '', status: 'in_stock', imageurl: '' }); }}>Cancel</button>}
      </form>
      {error && <div className="error">{error}</div>}
      <ul>
        {products.map(product => (
          <li key={product.id}>
            <img src={product.imageurl} alt={product.name} style={{ width: 60, height: 60, objectFit: 'cover' }} />
            <b>{product.name}</b> ({product.category}) - {product.quantity} pcs - ${product.price} - {product.status}
            <button onClick={() => handleEdit(product)}>Edit</button>
            {user.id === 1 && <button onClick={() => handleDelete(product.id)}>Delete</button>}
          </li>
        ))}
      </ul>
    </div>
  );
}
