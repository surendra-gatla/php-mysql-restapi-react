import * as React from "react";
import {
  createBrowserRouter,
  RouterProvider,
} from "react-router-dom";
import Signup from "./components/Signup";
import Login from "./components/login";
import Home from "./components/Home";
import Add_feed from "./components/add_feed";
import Edit_feed from "./components/edit_feed";
import ProtectedRoute from "./components/ProtectedRoute";
import './App.css';

const router = createBrowserRouter([
  {
    path: "/",
    element: <Login />
  },
  {
    path: "signup",
    element: <Signup />,
  },
  {
    path: "add_feed",
    element: <Add_feed />,
  },
  {
    path: "edit_feed",
    element: <Edit_feed />,
  },
  {
    path: "home",
    element: <ProtectedRoute><Home /></ProtectedRoute>,
  }
]);

export default function App() {
  return (<RouterProvider router={router} />)
}

