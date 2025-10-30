import React, { useState } from 'react';

import Login from './pages/login.jsx';
import Signup from './pages/signup.jsx';
import CarsPage from './pages/CarsPages.jsx';
import AddCarPage from './pages/AddCarPages.jsx';
import CarDetailPage from './pages/CarDetailPages.jsx';
import './App.css';

function App() {
  const [currentUser, setCurrentUser] = useState(null);
  const [currentPage, setCurrentPage] = useState('login');
  const [selectedCarId, setSelectedCarId] = useState(null);

  const handleLoginSuccess = (user) => {
    setCurrentUser(user);
    setCurrentPage('cars');
  };


  const handleShowLogin = () => {
    setCurrentPage('login');
  };


  const handleShowSignup = () => {
    setCurrentPage('signup');
  };

  const handleLogout = () => {
    setCurrentUser(null);
    setCurrentPage('login');
  };

  const handleShowAddCarForm = () => {
    setCurrentPage('addCar');
  };

  const handleShowDetails = (carId) => {
    setSelectedCarId(carId);
    setCurrentPage('details');
  };

  const handleBackToList = () => {
    setSelectedCarId(null);
    setCurrentPage('cars');
  };

  if (!currentUser) {
    return (
      <div className="auth-container">
        {currentPage === 'login' ? (

          <Login
            onLoginSuccess={handleLoginSuccess}
            onSwitchToSignup={handleShowSignup}
          />
        ) : (

          <Signup
            onSignupSuccess={handleShowLogin}
            onSwitchToLogin={handleShowLogin}
          />
        )}

      </div>
    );
  }



  if (currentPage === 'details') {
    return (
      <CarDetailPage
        carId={selectedCarId}
        onBack={handleBackToList}
      />
    );
  }

  if (currentPage === 'addCar') {
    return (
      <AddCarPage
        user={currentUser}
        onCarAdded={handleBackToList}
        onCancel={handleBackToList}
      />
    );
  }


  return (
    <CarsPage
      user={currentUser}
      onLogout={handleLogout}
      onShowAddCarForm={handleShowAddCarForm}
      onShowDetails={handleShowDetails}
    />
  );
}

export default App;

