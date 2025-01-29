import React, { useState, useEffect } from "react";
import { Modal, ModalHeader, ModalBody, ModalFooter } from "../components/modal";

const formatNumber = (number) => {
  return number.toLocaleString('es-ES'); // Use thousands separator
};

const Fabrica = () => {
  const [products, setProducts] = useState([]);
  const [pendingOrders, setPendingOrders] = useState([]);
  const [currentOrder, setCurrentOrder] = useState([]);
  const [isOrderModalOpen, setIsOrderModalOpen] = useState(false);
  const [customerName, setCustomerName] = useState("");
  const [observations, setObservations] = useState("");

  const fetchPendingOrders = async () => {
    try {
      const response = await fetch("http://localhost/arepas/backend/get_pending_orders.php?sucursal=fabrica");
      const data = await response.json();
      if (data.success) {
        setPendingOrders(data.orders);
      } else {
        console.error(data.message);
      }
    } catch (error) {
      console.error("Error fetching pending orders:", error);
    }
  };

  // Fetch Products and Pending Orders
  useEffect(() => {
    const fetchProducts = async () => {
      try {
        const response = await fetch("http://localhost/arepas/backend/get_products.php?sucursal=fabrica");
        const data = await response.json();
        if (data.success) {
          setProducts(data.products);
        } else {
          console.error(data.message);
        }
      } catch (error) {
        console.error("Error fetching products:", error);
      }
    };

    fetchProducts();
    fetchPendingOrders();
  }, []);

  const handleProductClick = (product) => {
    setIsOrderModalOpen(true);
    const existingProduct = currentOrder.find((item) => item.id === product.id);

    if (existingProduct) {
      setCurrentOrder(
        currentOrder.map((item) =>
          item.id === product.id
            ? {
                ...item,
                quantity: item.quantity + 1,
                total: (item.quantity + 1) * parseInt(item.price, 10),
              }
            : item
        )
      );
    } else {
      setCurrentOrder([
        ...currentOrder,
        {
          id: product.id,
          name: product.name,
          price: parseInt(product.price, 10), // Ensure price is an integer
          quantity: 1,
          total: parseInt(product.price, 10), // Initialize total
        },
      ]);
    }
  };

  const handleCloseOrderModal = () => {
    setIsOrderModalOpen(false);
  };

  const handleCreateOrder = async () => {
    if (currentOrder.length === 0) {
      alert("Por favor, selecciona productos.");
      return;
    }

    try {
      const response = await fetch("http://localhost/arepas/backend/create_order.php?sucursal=fabrica", {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
        },
        body: JSON.stringify({
          customer: customerName || null, // Make customerName optional
          obs: observations,
          items: currentOrder,
          total: currentOrder.reduce((acc, item) => acc + item.total, 0),
        }),
      });

      const data = await response.json();
      if (data.success) {
        alert("Orden creada exitosamente.");
        setCurrentOrder([]);
        setCustomerName("");
        setObservations("");
        setIsOrderModalOpen(false);
        fetchPendingOrders(); // Re-fetch pending orders
      } else {
        alert(data.message || "Error al crear la orden.");
      }
    } catch (error) {
      console.error("Error al crear la orden:", error);
      alert("Error al comunicarse con el servidor.");
    }
  };

  return (
    <div className="min-h-screen bg-gray-100 px-4 py-8">
      <h1 className="text-3xl font-bold mb-6 text-gray-800 text-center">Fábrica</h1>

      {/* Product Grid */}
      <div className="product-grid">
        {products.map((product) => (
          <div
            key={product.id}
            className="product-card bg-blue-100 shadow-lg rounded-lg p-4 text-center cursor-pointer"
            onClick={() => handleProductClick(product)}
          >
            <h2 className="text-lg font-bold text-gray-700">{product.name}</h2>
            <p className="text-sm text-gray-600">Stock: {product.stock}</p>
            <p className="text-sm text-gray-600">Precio: ${formatNumber(parseInt(product.price, 10))}</p>
          </div>
        ))}
      </div>

      {/* Pending Orders Section */}
      <h2 className="text-2xl font-semibold mb-4 text-gray-800">Órdenes Pendientes</h2>
      <div className="pending-orders-container">
        {pendingOrders.map((order) => (
          <div
            key={order.id}
            className="order-card bg-white shadow-lg rounded-lg p-4 border border-gray-300 hover:bg-gray-100 transition duration-300"
          >
            <h3 className="text-lg font-semibold text-gray-700">Orden #{order.id}</h3>
            <p className="text-sm text-gray-600">Total: ${formatNumber(parseInt(order.total, 10))}</p>
            <p className="text-sm text-gray-600">Fecha: {new Date(order.created_at).toLocaleString()}</p>
            <p className="text-sm text-gray-600">Método de Pago: {order.payment_method}</p>
          </div>
        ))}
      </div>

      {/* Current Order Modal */}
      <Modal isOpen={isOrderModalOpen} onClose={handleCloseOrderModal}>
        <ModalHeader>Orden Actual</ModalHeader>
        <ModalBody>
          <div className="space-y-2">
            {currentOrder.map((item) => (
              <div key={item.id} className="flex justify-between items-center border-b py-2">
                <span className="text-sm font-medium text-gray-700">{item.name}</span>
                <span className="text-sm text-gray-600"> x {item.quantity}</span>
                <span className="text-sm text-gray-600"> = ${formatNumber(parseInt(item.total, 10))}</span>
              </div>
            ))}
          </div>
          <p className="font-bold mt-4 text-xl text-gray-800">Total: ${formatNumber(currentOrder.reduce((acc, item) => acc + item.total, 0))}</p>
          <input
            type="text"
            className="w-3/4 px-3 py-2 border rounded-md mt-4 text-lg mx-auto"
            placeholder="Nombre del Cliente (Opcional)"
            value={customerName}
            onChange={(e) => setCustomerName(e.target.value)}
          />
          <textarea
            className="w-3/4 px-3 py-2 border rounded-md mt-2 text-lg mx-auto"
            placeholder="Observaciones"
            value={observations}
            onChange={(e) => setObservations(e.target.value)}
          />
        </ModalBody>
        <ModalFooter>
          <button
            className="bg-blue-500 text-white px-4 py-2 rounded-md hover:bg-blue-600 transition duration-300"
            onClick={handleCreateOrder}
          >
            Crear Orden
          </button>
          <button
            className="bg-red-500 text-white px-4 py-2 rounded-md ml-2 hover:bg-red-600 transition duration-300"
            onClick={handleCloseOrderModal}
          >
            Cerrar
          </button>
        </ModalFooter>
      </Modal>
    </div>
  );
};

export default Fabrica;