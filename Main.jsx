import React, { useState } from "react";
import { Modal, ModalHeader, ModalBody, ModalFooter } from "../components/modal";
import Button from "../components/button";
import { useNavigate } from "react-router-dom";

const Main = () => {
    const [isLoginOpen, setIsLoginOpen] = useState(false);
    const [selectedSucursal, setSelectedSucursal] = useState("");
    const [formData, setFormData] = useState({ username: "", password: "" });
    const navigate = useNavigate();

    const handleLoginClick = (sucursal) => {
        setSelectedSucursal(sucursal);
        setIsLoginOpen(true);
    };

    const closeLoginModal = () => {
        setIsLoginOpen(false);
        setSelectedSucursal("");
        setFormData({ username: "", password: "" });
    };

    const handleInputChange = (e) => {
        const { id, value } = e.target;
        setFormData({ ...formData, [id]: value });
    };

    const handleLoginSubmit = async (e) => {
        e.preventDefault();
        try {
            const response = await fetch("http://localhost/arepas/backend/login.php", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                },
                body: JSON.stringify(formData),
            });
            const result = await response.json();
            if (result.success) {
                navigate(`/${selectedSucursal.toLowerCase()}`);
                closeLoginModal();
            } else {
                alert(result.message || "Error al iniciar sesión");
            }
        } catch (error) {
            console.error("Error al comunicarse con el servidor:", error);
            alert("Error al comunicarse con el servidor.");
        }
    };

    return (
        <div className="min-h-screen bg-gray-100 flex flex-col items-center justify-center px-4 py-8">
            <h1 className="text-4xl font-bold mb-8 text-gray-800 text-center"> Arepitas Pa' Mamá </h1>
            <div className="grid-container">
                {["Fábrica", "Triángulo", "Vélez", "Portal del Cliente", "Distribución"].map(
                    (sucursal, index) => (
                        <div
                            key={index}
                            className="card bg-white shadow-lg rounded-lg overflow-hidden cursor-pointer transform hover:scale-105 transition duration-300"
                            onClick={() => handleLoginClick(sucursal)}
                        >
                            <div className="w-full h-60 flex justify-center items-center">
                                <img
                                    src={`/images/sucursal${index + 1}.jpeg`}
                                    alt={sucursal}
                                    className="w-full h-full object-cover"
                                />
                            </div>
                            <div className="p-4 text-center">
                                <h2 className="text-xl font-semibold text-gray-700">{sucursal}</h2>
                            </div>
                        </div>
                    )
                )}
            </div>
            {isLoginOpen && (
                <Modal isOpen={isLoginOpen} onClose={closeLoginModal}>
                    <ModalHeader>Iniciar Sesión - {selectedSucursal}</ModalHeader>
                    <ModalBody>
                        <form onSubmit={handleLoginSubmit} className="space-y-4">
                            <div>
                                <label htmlFor="username" className="block text-sm font-medium text-gray-700"> Usuario </label>
                                <input
                                    type="text"
                                    id="username"
                                    value={formData.username}
                                    onChange={handleInputChange}
                                    className="w-full px-3 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                    placeholder="Ingrese su usuario"
                                />
                            </div>
                            <div>
                                <label htmlFor="password" className="block text-sm font-medium text-gray-700"> Contraseña </label>
                                <input
                                    type="password"
                                    id="password"
                                    value={formData.password}
                                    onChange={handleInputChange}
                                    className="w-full px-3 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                    placeholder="Ingrese su contraseña"
                                />
                            </div>
                            <Button type="submit" className="w-full bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 rounded-md"> Iniciar Sesión </Button>
                        </form>
                    </ModalBody>
                    <ModalFooter>
                        <Button onClick={closeLoginModal} className="bg-red-500 hover:bg-red-600">Cerrar</Button>
                    </ModalFooter>
                </Modal>
            )}
        </div>
    );
};

export default Main;