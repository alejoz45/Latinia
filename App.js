import React from "react";
import { BrowserRouter as Router, Routes, Route } from "react-router-dom";
import Main from "./pages/Main";
import Fabrica from "./pages/Fabrica";
import "./App.css";



const App = () => {
  return (
    <Router>
      <Routes>
        {/* Ruta de la página principal */}
        <Route path="/" element={<Main />} />
        
        {/* Ruta de la sucursal Fábrica */}
        <Route path="/fabrica" element={<Fabrica />} />
        
        {/* Otras rutas de sucursales o páginas se agregarán aquí */}
      </Routes>
    </Router>
  );
};

export default App;
