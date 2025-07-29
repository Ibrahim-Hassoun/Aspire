import React, { useState } from 'react';
import axios from 'axios';

const API_URL = 'http://localhost:8000/api';

export default function ChatbotPage({ token }) {
  const [messages, setMessages] = useState([]);
  const [input, setInput] = useState('');
  const [error, setError] = useState('');

  const sendMessage = async e => {
    e.preventDefault();
    setError('');
    try {
      const res = await axios.post(`${API_URL}/sendMessage`, { message: input }, { headers: { Authorization: `Bearer ${token}` } });
      setMessages([...messages, { user: 'You', text: input }, { user: 'Bot', text: res.data.data.reply || res.data.data.message }]);
      setInput('');
    } catch (err) {
      setError('Failed to send message');
    }
  };

  return (
    <div className="chatbot-page">
      <h2>Chatbot</h2>
      <div className="chat-window">
        {messages.map((msg, i) => (
          <div key={i} className={msg.user === 'You' ? 'user-msg' : 'bot-msg'}>
            <b>{msg.user}:</b> {msg.text}
          </div>
        ))}
      </div>
      <form onSubmit={sendMessage}>
        <input value={input} onChange={e => setInput(e.target.value)} placeholder="Ask the bot..." />
        <button type="submit">Send</button>
      </form>
      {error && <div className="error">{error}</div>}
    </div>
  );
}
