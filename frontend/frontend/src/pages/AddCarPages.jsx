import React, { useState } from 'react';
import '../Styles/AuthForm.css';

function AddCarPage({ user, onCarAdded, onCancel }) {

    const isAdmin = user && user.role_id === 1;

    const [form, setForm] = useState({
        brand_name: '',
        model_name: '',
        year: '',
        price: '',
        mileage: '',
        fuel_type: 'Gasoline',
        condition: 'Used',
        vin: '',
        color: '',
        image_url: ''
    });

    const [message, setMessage] = useState('');
    const [loading, setLoading] = useState(false);
    const [isSuccess, setIsSuccess] = useState(false);

    const handleChange = (e) => {
        const { name, value } = e.target;
        setForm(prevForm => ({
            ...prevForm,
            [name]: value
        }));
    };

    const handleSubmit = async (e) => {
        e.preventDefault();
        setLoading(true);
        setMessage('');
        setIsSuccess(false);

        const payload = {
            ...form,
            seller_id: user.user_id
        };

        try {
            const res = await fetch("http://localhost/backend/cars/addcars.php", {
                method: "POST",
                headers: { "Content-Type": "application/json" },
                body: JSON.stringify(payload),
            });

            const data = await res.json();
            setMessage(data.message);

            if (res.ok && data.success) {
                setIsSuccess(true);
                setTimeout(() => {
                    onCarAdded();
                }, 2000);
            } else {
                setIsSuccess(false);
            }

        } catch (err) {
            setIsSuccess(false);
            setMessage("Network error. Could not add car.");
        } finally {
            setLoading(false);
        }
    };

    const messageClass = isSuccess ? 'success-message' : 'error-message';

    if (!isAdmin) {
        return (
            <div className="form-container" style={{ maxWidth: '600px', textAlign: 'center' }}>
                <h2 style={{ color: '#dc3545' }}>Access Denied</h2>
                <p>You do not have permission to access this page..</p>
                <button
                    onClick={onCancel}
                    className="toggle-auth"
                    style={{ marginTop: '10px' }}
                >
                    Back to Cars
                </button>
            </div>
        );
    }

    return (
        <div className="form-container" style={{ maxWidth: '600px' }}>
            <h2>Add New Car</h2>
            <form onSubmit={handleSubmit}>
                <div className="form-grid">
                    <input name="brand_name" value={form.brand_name} onChange={handleChange} placeholder="Brand (ex: Dacia)" required />
                    <input name="model_name" value={form.model_name} onChange={handleChange} placeholder="Model (ex: Logan)" required />

                    <input name="year" type="number" value={form.year} onChange={handleChange} placeholder="Year" required />
                    <input name="price" type="number" step="0.01" value={form.price} onChange={handleChange} placeholder="Price(ex: 12000.00)" required />

                    <input name="mileage" type="number" value={form.mileage} onChange={handleChange} placeholder="Mileage (ex: 85000)" required />
                    <input name="color" value={form.color} onChange={handleChange} placeholder="Color" required />

                    <input name="vin" value={form.vin} onChange={handleChange} placeholder=" VIN" required />

                    <select name="fuel_type" value={form.fuel_type} onChange={handleChange} required>
                        <option value="Gasoline">Gasoline</option>
                        <option value="Diesel">Diesel</option>
                        <option value="Electric">Electric</option>
                        <option value="Hybrid">Hybrid</option>
                        <option value="GPL">GPL</option>
                    </select>

                    <select name="condition" value={form.condition} onChange={handleChange} required>
                        <option value="Used">Second-hand</option>
                        <option value="New">New</option>
                    </select>
                </div>

                <input name="image_url" value={form.image_url} onChange={handleChange} placeholder="URL Imagine (ex: https://...)" required />

                <button type="submit" disabled={loading}>
                    {loading ? 'Adding...' : 'Publish Ad'}
                </button>
            </form>

            {message && <p className={messageClass}>{message}</p>}

            <button
                onClick={onCancel}
                className="toggle-auth"
                style={{ marginTop: '10px' }}
                disabled={loading}
            >
                Cancel and return
            </button>
        </div>
    );
}

const gridStyles = `
.form-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 1rem;
    margin-bottom: 1rem;
}
@media (max-width: 600px) {
    .form-grid {
        grid-template-columns: 1fr;
    }
}
`;

if (!document.getElementById('add-car-grid-styles')) {
    const styleSheet = document.createElement("style");
    styleSheet.id = 'add-car-grid-styles';
    styleSheet.innerText = gridStyles;
    document.head.appendChild(styleSheet);
}

export default AddCarPage;
