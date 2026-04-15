<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AI Agent Chat</title>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/react/18.2.0/umd/react.development.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/react-dom/18.2.0/umd/react-dom.development.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/babel-standalone/7.23.2/babel.min.js"></script>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: sans-serif; background: #f0f2f5; height: 100vh; display: flex; align-items: center; justify-content: center; }
        .container { width: 100%; max-width: 800px; height: 100vh; display: flex; flex-direction: column; background: white; }
        .header { padding: 16px 20px; background: #4f46e5; color: white; font-size: 18px; font-weight: 500; display: flex; justify-content: space-between; align-items: center; }
        .messages { flex: 1; overflow-y: auto; padding: 20px; display: flex; flex-direction: column; gap: 12px; }
        .message { max-width: 70%; padding: 12px 16px; border-radius: 12px; font-size: 14px; line-height: 1.5; }
        .message.user { align-self: flex-end; background: #4f46e5; color: white; border-bottom-right-radius: 4px; }
        .message.agent { align-self: flex-start; background: #f1f5f9; color: #1e293b; border-bottom-left-radius: 4px; }
        .message.thinking { color: #94a3b8; font-style: italic; }
        .input-area { padding: 16px 20px; border-top: 1px solid #e2e8f0; display: flex; gap: 12px; }
        .input-area input { flex: 1; padding: 12px 16px; border: 1px solid #e2e8f0; border-radius: 8px; font-size: 14px; outline: none; }
        .input-area input:focus { border-color: #4f46e5; }
        .input-area button { padding: 12px 24px; background: #4f46e5; color: white; border: none; border-radius: 8px; font-size: 14px; cursor: pointer; }
        .input-area button:disabled { background: #a5b4fc; cursor: not-allowed; }
        .login-form { display: flex; flex-direction: column; gap: 12px; padding: 40px; }
        .login-form input { padding: 12px 16px; border: 1px solid #e2e8f0; border-radius: 8px; font-size: 14px; outline: none; }
        .login-form button { padding: 12px; background: #4f46e5; color: white; border: none; border-radius: 8px; font-size: 14px; cursor: pointer; }
        .error { color: #ef4444; font-size: 13px; }
        .login-title { font-size: 20px; font-weight: 500; }
        .logout-button { background: transparent; border: 1px solid white; color: white; padding: 6px 14px; border-radius: 6px; cursor: pointer; font-size: 13px; }
    </style>
</head>

<body>
    <div id="root"></div>
    <script type="text/babel">
        const API = 'http://localhost/ai-blog/public/api';

        // Login component — no token, no logout button
        function Login({ onLogin }) {
            const [email, setEmail] = React.useState('');
            const [password, setPassword] = React.useState('');
            const [error, setError] = React.useState('');
            const [loading, setLoading] = React.useState(false);

            const handleLogin = async () => {
                setLoading(true);
                setError('');
                try {
                    const res = await fetch(`${API}/login`, {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
                        body: JSON.stringify({ email, password })
                    });
                    const data = await res.json();
                    if (data.success) {
                        localStorage.setItem('token', data.token);
                        onLogin(data.token);
                    } else {
                        setError(data.message);
                    }
                } catch (e) {
                    setError('Connection failed.');
                } finally {
                    setLoading(false);
                }
            };

            return (
                <div className="container">
                    <div className="header">
                        <span>AI Agent — Login</span>
                    </div>
                    <div className="login-form">
                        <h2 className="login-title">Sign in to continue</h2>
                        <input type="email" placeholder="Email" value={email} onChange={e => setEmail(e.target.value)} />
                        <input type="password" placeholder="Password" value={password} onChange={e => setPassword(e.target.value)} />
                        {error && <span className="error">{error}</span>}
                        <button onClick={handleLogin} disabled={loading}>
                            {loading ? 'Signing in...' : 'Sign In'}
                        </button>
                    </div>
                </div>
            );
        }

        // Chat component — has token, has logout button
        function Chat({ token }) {
            const [messages, setMessages] = React.useState([
                { role: 'agent', text: 'Hello! I can help you manage your posts. What would you like to know?' }
            ]);
            const [input, setInput] = React.useState('');
            const [loading, setLoading] = React.useState(false);
            const [conversationId, setConversationId] = React.useState(null);
            const messagesEndRef = React.useRef(null);

            React.useEffect(() => {
                messagesEndRef.current?.scrollIntoView({ behavior: 'smooth' });
            }, [messages]);

            // handleLogout lives HERE in Chat — it has access to token
            const handleLogout = async () => {
                try {
                    await fetch(`${API}/logout`, {
                        method: 'POST',
                        headers: { 'Authorization': `Bearer ${token}`, 'Accept': 'application/json' }
                    });
                } catch (e) {
                    // ignore errors
                } finally {
                    localStorage.removeItem('token');
                    window.location.reload();
                }
            };

            const poll = async (jobId) => {
                return new Promise((resolve) => {
                    let attempts = 0;
                    const maxAttempts = 30;

                    const interval = setInterval(async () => {
                        attempts++;
                        if (attempts >= maxAttempts) {
                            clearInterval(interval);
                            resolve('Request timed out. Please try again.');
                            return;
                        }
                        const res = await fetch(`${API}/agent/status/${jobId}`, {
                            headers: { 'Authorization': `Bearer ${token}`, 'Accept': 'application/json' }
                        });
                        const data = await res.json();
                        if (data.status === 'completed' || data.status === 'failed') {
                            clearInterval(interval);
                            resolve(data.result);
                        }
                    }, 2000);
                });
            };

            const sendMessage = async () => {
                if (!input.trim() || loading) return;
                const userMessage = input.trim();
                setInput('');
                setLoading(true);
                setMessages(prev => [...prev, { role: 'user', text: userMessage }]);
                setMessages(prev => [...prev, { role: 'agent', text: 'Thinking...', thinking: true }]);

                try {
                    const body = { message: userMessage };
                    if (conversationId) body.conversationId = conversationId;

                    const res = await fetch(`${API}/agent`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'Authorization': `Bearer ${token}`
                        },
                        body: JSON.stringify(body)
                    });

                    const data = await res.json();
                    if (!conversationId) setConversationId(data.conversationId);
                    const result = await poll(data.jobId);

                    setMessages(prev => prev.map((m, i) =>
                        i === prev.length - 1 ? { role: 'agent', text: result } : m
                    ));
                } catch (e) {
                    setMessages(prev => prev.map((m, i) =>
                        i === prev.length - 1 ? { role: 'agent', text: 'Something went wrong.' } : m
                    ));
                } finally {
                    setLoading(false);
                }
            };

            const handleKeyDown = (e) => {
                if (e.key === 'Enter') sendMessage();
            };

            return (
                <div className="container">
                    <div className="header">
                        <span>AI Agent Chat</span>
                        <button className="logout-button" onClick={handleLogout}>Logout</button>
                    </div>
                    <div className="messages">
                        {messages.map((m, i) => (
                            <div key={i} className={`message ${m.role} ${m.thinking ? 'thinking' : ''}`}>
                                {m.text}
                            </div>
                        ))}
                        <div ref={messagesEndRef} />
                    </div>
                    <div className="input-area">
                        <input
                            type="text"
                            placeholder="Ask about your posts..."
                            value={input}
                            onChange={e => setInput(e.target.value)}
                            onKeyDown={handleKeyDown}
                            disabled={loading}
                        />
                        <button onClick={sendMessage} disabled={loading}>
                            {loading ? 'Sending...' : 'Send'}
                        </button>
                    </div>
                </div>
            );
        }

        function App() {
            const [token, setToken] = React.useState(localStorage.getItem('token'));
            const handleLogin = (token) => setToken(token);
            return token ? <Chat token={token} /> : <Login onLogin={handleLogin} />;
        }

        ReactDOM.createRoot(document.getElementById('root')).render(<App />);
    </script>
</body>

</html>