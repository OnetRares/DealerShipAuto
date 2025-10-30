import React, { useState, useEffect } from "react";
import "../Styles/Slideshow.css";

function SlideShow({ cars }) {
    const [currentIndex, setCurrentIndex] = useState(0);

    useEffect(() => {
        if (!cars || cars.length === 0) return;
        const interval = setInterval(() => {
            setCurrentIndex((prevIndex) => (prevIndex + 1) % cars.length);
        }, 3000);
        return () => clearInterval(interval);
    }, [cars]);

    if (!cars || cars.length === 0) return null;

    return (
        <div className="slideshow-container">
            <img
                src={`data:${cars[currentIndex].image_mime};base64,${cars[currentIndex].image_base64}`}
                alt={`${cars[currentIndex].brand_name} ${cars[currentIndex].model_name}`}
                className="slideshow-image"
            />
            <div className="slideshow-caption">
                {cars[currentIndex].brand_name} {cars[currentIndex].model_name}
            </div>
        </div>
    );
}

export default SlideShow;
