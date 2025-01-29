import React from "react";
import ReactDOM from "react-dom";

const Modal = ({ isOpen, children }) => {
  if (!isOpen) return null;

  return ReactDOM.createPortal(
    <div className="modal fixed inset-0 flex justify-center items-end z-50 pointer-events-none">
      <div className="modal-content bg-white rounded-lg shadow-lg w-full max-w-sm mx-4 mb-4 pointer-events-auto">
        {children}
      </div>
    </div>,
    document.getElementById("portal")
  );
};

const ModalHeader = ({ children }) => (
  <div className="border-b px-4 py-2 text-xl font-bold text-gray-800">{children}</div>
);

const ModalBody = ({ children }) => <div className="px-4 py-2">{children}</div>;

const ModalFooter = ({ children }) => <div className="border-t px-4 py-2 flex justify-end">{children}</div>;

export { Modal, ModalHeader, ModalBody, ModalFooter };
