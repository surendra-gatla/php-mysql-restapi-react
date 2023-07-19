import { Navigate } from "react-router-dom";

export default function ProtectedRoute({ children }) {
  if (!localStorage.getItem('isUserLoggedIn')) {
    // user is not authenticated
    return <Navigate to="/" />;
  }
  return children;
};