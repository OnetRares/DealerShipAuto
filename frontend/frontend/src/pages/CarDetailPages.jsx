import React, { useState, useEffect } from 'react';
import '../Styles/CarsDetailPages.css';

function CarDetailPage({ carId, onBack }) {

    const [car, setCar] = useState(null);
    const [loading, setLoading] = useState(true);
    const [error, setError] = useState(null);
    const [message, setMessage] = useState(null);

    const user = JSON.parse(localStorage.getItem("user"));

    useEffect(() => {
        if (!carId) return;

        setLoading(true);
        setError(null);

        fetch(`http://localhost/backend/cars/getcarsdetail.php?id=${carId}`)
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    setCar(data.car);
                } else {
                    throw new Error(data.error || "The car was not found..");
                }
            })
            .catch(err => {
                console.error("Error retrieving details:", err);
                setError(err.message || "Could not load details.");
            })
            .finally(() => setLoading(false));

    }, [carId]);

    const formatBase64Image = (img) => {
        if (!img || !img.image_base64 || !img.image_mime) {
            return null;
        }
        return `data:${img.image_mime};base64,${img.image_base64}`;
    };

    const handleBuyCar = async () => {
        if (!car) return;

        try {
            setMessage("Processing purchase...");

            const response = await fetch("http://localhost/backend/cars/buycar.php", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                },
                body: JSON.stringify({
                    car_id: car.car_id,
                    buyer_id: user?.user_id,
                    final_price: car.price,
                    payment_method: "Card"
                })
            });

            const data = await response.json();

            if (data.success) {
                setMessage("✅ Purchase successful! The car has been added to the transaction list.");
            } else {
                setMessage(` Error: ${data.error || data.message}`);
            }

        } catch (err) {
            console.error("Buy car error:", err);
            setMessage(" Server error while trying to buy the car.");
        }
    };

    if (loading) {
        return <div className="loading">Loading details...</div>;
    }
    if (error) {
        return <div className="error">{error}</div>;
    }
    if (!car) {
        return <div className="error">The car was not found.</div>;
    }

    const mainImageSrc = formatBase64Image(car);

    return (
        <div className="car-detail-container">
            <button onClick={onBack} className="back-button">
                &larr; Back to the list
            </button>

            <div className="car-detail-content">

                <div className="car-image-simple">
                    {mainImageSrc ? (
                        <img src={mainImageSrc} alt={`${car.brand_name} ${car.model_name}`} />
                    ) : (
                        <div className="placeholder-image">
                            <p>Image unavailable</p>
                        </div>
                    )}
                </div>

                <div className="car-info">
                    <h2>{car.brand_name} {car.model_name}</h2>

                    <p className="price">Price: {Number(car.price).toLocaleString('ro-RO')} €</p>

                    <h3>Specifications</h3>
                    <ul>
                        <li><strong>Year:</strong> {car.year}</li>
                        <li><strong>Milage:</strong> {Number(car.mileage).toLocaleString('ro-RO')} km</li>
                        {car.fuel_type && <li><strong>Fuel:</strong> {car.fuel_type}</li>}
                        {car.color && <li><strong>Color:</strong> {car.color}</li>}
                        {car.condition && <li><strong>Condition:</strong> {car.condition}</li>}
                        {car.vin && <li><strong>VIN:</strong> {car.vin}</li>}
                    </ul>

                    <button className="buy-button" onClick={handleBuyCar}>
                        🛒 Buy Car
                    </button>

                    {message && <p className="buy-message">{message}</p>}
                </div>
            </div>
        </div>
    );
}

export default CarDetailPage;
