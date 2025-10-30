import React, { useState } from "react";
import '../Styles/AuthForm.css';

function Signup({ onSignupSuccess, onSwitchToLogin }) {

    const [form, setForm] = useState({
        full_name: "",
        email: "",
        password: "",
        phone: "",
        address: "",
        role: "client",
    });

    const [message, setMessage] = useState("");
    const [isSuccess, setIsSuccess] = useState(false);
    const handleSubmit = async (e) => {
        e.preventDefault();
        setMessage("");
        setIsSuccess(false);

        try {
            const res = await fetch("http://localhost/backend/auth/signup.php", {
                method: "POST",
                headers: { "Content-Type": "application/json" },
                body: JSON.stringify(form),
            });

            const data = await res.json();
            setMessage(data.message);

            if (res.ok && data.success) {
                setIsSuccess(true);

                setTimeout(() => {
                    onSignupSuccess();
                }, 2000);
            } else {
                setIsSuccess(false);
            }

        } catch (err) {
            console.error(err);
            setIsSuccess(false);
            setMessage("Network error");
        }
    };

    const messageClass = isSuccess ? 'success-message' : 'error-message';

    return (
        <div className="form-container">
            <h2>Sign Up</h2>
            <form onSubmit={handleSubmit}>
                <input placeholder="Full Name" onChange={(e) => setForm({ ...form, full_name: e.target.value })} required />
                <input type="email" placeholder="Email" onChange={(e) => setForm({ ...form, email: e.target.value })} required />
                <input type="password" placeholder="Password" onChange={(e) => setForm({ ...form, password: e.target.value })} required />
                <input placeholder="Phone" onChange={(e) => setForm({ ...form, phone: e.target.value })} />
                <input placeholder="Address" onChange={(e) => setForm({ ...form, address: e.target.value })} />
                <select value={form.role} onChange={(e) => setForm({ ...form, role: e.target.value })}>
                    <option value="client">Client</option>
                    <option value="admin">Admin</option>
                </select>

                <button type="submit">Create Account</button>
            </form>

            {message && <p className={messageClass}>{message}</p>}

            <div className="form-link">
                <p>Already have an account? <a href="#" onClick={(e) => { e.preventDefault(); onSwitchToLogin(); }}>Log in to your account</a></p>
            </div>
        </div>
    );
}

export default Signup;