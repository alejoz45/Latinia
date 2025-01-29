const Card = ({ children, className, onClick }) => (
    <div
      className={`bg-white shadow-md rounded-lg p-4 ${className}`}
      onClick={onClick}
    >
      {children}
    </div>
  );
  
  export default Card;
  