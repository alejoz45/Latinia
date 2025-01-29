import React from "react";

const OrderItem = ({ item }) => (
  <div className="flex justify-between items-center mb-2">
    <span>{item.name}</span>
    <span>{item.quantity} x ${item.price} = ${item.total.toFixed(2)}</span>
  </div>
);

export default OrderItem;
