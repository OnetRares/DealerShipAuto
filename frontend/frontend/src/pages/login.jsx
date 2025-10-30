import React, { useState } from "react";
import '../Styles/AuthForm.css';

function Login({ onLoginSuccess, onSwitchToSignup }) {
    const [form, setForm] = useState({ email: "", password: "" });
    const [message, setMessage] = useState("");

    const handleLogin = async (e) => {
        e.preventDefault();
        setMessage("");

        console.log("Form being sent:", form);

        if (!form.email || !form.password) {
            setMessage("Email și parola sunt obligatorii.");
            return;
        }

        try {
            const res = await fetch("http://localhost/backend/auth/login.php", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                },
                credentials: "include",
                body: JSON.stringify(form),
            });

            const data = await res.json();

            if (res.ok && data.success && data.user && typeof data.user.user_id !== 'undefined') {
                onLoginSuccess(data.user);
                localStorage.setItem("user", JSON.stringify(data.user));
                console.log("Rol primit:", data.user.role_id);
            } else {
                setMessage(data.error || data.message || "A apărut o eroare.");
            }
        } catch (error) {
            console.error("Fetch error:", error);
            setMessage("Nu s-a putut conecta la server.");
        }
    };

    return (
        <div className="form-container">
            <h2>Login</h2>
            <form onSubmit={handleLogin}>
                <input
                    type="email"
                    placeholder="Email"
                    value={form.email} // <--- fix important
                    onChange={(e) => setForm({ ...form, email: e.target.value })}
                    required
                />
                <input
                    type="password"
                    placeholder="Parola"
                    value={form.password} // <--- fix important
                    onChange={(e) => setForm({ ...form, password: e.target.value })}
                    required
                />
                <button type="submit">Login</button>
            </form>

            {message && (
                <p className="error-message">{message}</p>
            )}

            <div className="form-link">
                <p>
                    Nu ai cont?{" "}
                    <a href="#" onClick={(e) => { e.preventDefault(); onSwitchToSignup(); }}>
                        Înregistrează-te
                    </a>
                </p>
            </div>
        </div>
    );
}

export default Login;
