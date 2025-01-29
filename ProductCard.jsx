import React from "react";

const ProductCard = ({ product, onClick }) => (
  <div
    className="card bg-blue-100 shadow-lg rounded-lg p-4 text-center cursor-pointer"
    onClick={onClick}
  >
    <h2 className="text-lg font-bold text-gray-700">{product.name}</h2>
    <p className="text-sm text-gray-600">Stock: {product.stock}</p>
    <p className="text-sm text-gray-600">Precio: ${product.price}</p>
  </div>
);

export default ProductCard;
