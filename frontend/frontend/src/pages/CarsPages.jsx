import React, { useState, useEffect } from "react";
import '../Styles/CarsPages.css';
import SlideShow from "./SlideShow.jsx";

// 1. Adaugă 'onShowDetails' la props
function CarsPage({ userId, onLogout, onShowAddCarForm, onShowDetails }) {
    const [cars, setCars] = useState([]);
    const [loading, setLoading] = useState(true);
    const [message, setMessage] = useState("");

    const API = "http://localhost/backend/cars/getcars.php";

    useEffect(() => {
        const fetchCars = async () => {
            setLoading(true);
            setMessage("");

            try {
                const res = await fetch(API);
                const data = await res.json();

                if (res.ok && data.success) {
                    setCars(data.cars || []);
                    if ((data.cars || []).length === 0) {
                        setMessage("There are no cars to display..");
                    }
                } else {
                    setMessage(data.error || "An error occurred while picking up the cars.");
                }
            } catch (error) {
                console.error("Fetch error:", error);
                setMessage("Could not connect to the server. Check your connection or contact your administrator..");
            } finally {
                setLoading(false);
            }
        };

        fetchCars();
    }, []);

    const handleViewDetails = (carId) => {
        onShowDetails(carId);
    };

    return (
        <div className="page-container">
            <div className="page-header">
                <h2 className="page-title">Available Cars</h2>
                <div className="header-actions">
                    <button className="add-button" onClick={onShowAddCarForm}>
                        + Add Car
                    </button>
                    <button className="logout-button" onClick={onLogout}>
                        Logout
                    </button>
                </div>
            </div>
            <SlideShow cars={cars} />
            {loading && <p className="loading-message">Se încarcă...</p>}
            {!loading && message && <p className="status-message">{message}</p>}

            {!loading && cars.length > 0 && (
                <div className="grid-container">
                    {cars.map((car) => (
                        <div key={car.car_id} className="card">
                            {car.image_base64 ? (
                                <img
                                    src={`data:${car.image_mime};base64,${car.image_base64}`}
                                    alt={`${car.brand_name} ${car.model_name}`}
                                    className="card-image"
                                />
                            ) : (
                                <div className="card-image-placeholder">
                                    Fără imagine
                                </div>
                            )}
                            <div className="card-content">
                                <div className="card-brand">{car.brand_name}</div>
                                <div className="card-model">{car.model_name}</div>
                                <button
                                    className="details-button"
                                    onClick={() => handleViewDetails(car.car_id)}
                                >
                                    See Details
                                </button>
                            </div>
                        </div>
                    ))}
                </div>
            )}
        </div>
    );
}

export default CarsPage;