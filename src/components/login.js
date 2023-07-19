import * as React from "react";
import * as CONST from '../constant';
import Avatar from "@mui/material/Avatar";
import Button from "@mui/material/Button";
import CssBaseline from "@mui/material/CssBaseline";
import TextField from "@mui/material/TextField";
import Link from "@mui/material/Link";
import Grid from "@mui/material/Grid";
import Box from "@mui/material/Box";
import LockOutlinedIcon from "@mui/icons-material/LockOutlined";
import Typography from "@mui/material/Typography";
import Container from "@mui/material/Container";
import { createTheme, ThemeProvider } from "@mui/material/styles";
import { useState } from 'react';
import axios from 'axios';
import { useNavigate } from "react-router-dom";
import 'react-confirm-alert/src/react-confirm-alert.css';




export default function Login() {
  const navigate = useNavigate();
  const [inputs, setInputs] = useState([]);
  let logedin = localStorage.getItem('isUserLoggedIn');
  console.log(logedin);
  if (logedin != undefined || logedin != null || logedin) {
    navigate("/home");
  }

  const theme = createTheme();
  const [error, setError] = useState();
  const [success, setSucess] = useState();
  const handleChange = (event) => {
    const name = event.target.name;
    const value = event.target.value;
    setInputs(values => ({ ...values, [name]: value }));
  }

  function handleSubmit() {
    axios.post(CONST.API_URL+'user.php', inputs).then(function (response) {
      let result = response.data.status;
      let message = response.data.message;
      if (result == 1) {
        localStorage.setItem('isUserLoggedIn', true);
        localStorage.setItem('UserId', response.data.user_id);
        localStorage.setItem('UserName', response.data.username);
        setError('');
        setSucess('User Logged in Successfully. Redirecting')
        setTimeout(function () {
          navigate("/home");
        }, 700);
      } else {
        console.log(message);
        setError(message);
      }
    }).catch(function (error) {
      setError('Error while sending data')
    });
  }

  function handleLogin() {
    navigate("/signup");
  }

  return (
    <ThemeProvider theme={theme}>
      <Container component="main" maxWidth="xs">
        <CssBaseline />
        <Box
          sx={{
            marginTop: 8,
            display: "flex",
            flexDirection: "column",
            alignItems: "center",
          }}
        >
          <Avatar sx={{ m: 1, bgcolor: "secondary.main" }}>
            <LockOutlinedIcon />
          </Avatar>
          <Typography component="h1" variant="h5">
            Sign In
          </Typography>
          <Box
            sx={{ mt: 3 }}
          >
            <Grid container spacing={2}>
              <Grid item xs={12}>
                <TextField
                  required fullWidth id="userName" label="User Name" name="username" autoComplete="family-name"
                  onChange={handleChange}
                />
              </Grid>
              <Grid item xs={12}>
                <TextField
                  required fullWidth name="password" label="Password" type="password" id="password" autoComplete="new-password"
                  onChange={handleChange}
                />
              </Grid>
            </Grid>
            <Button type="submit" fullWidth onClick={handleSubmit} variant="contained" sx={{ mt: 3, mb: 2 }}>
              Sign in
            </Button>

            <div align='center'><strong>{error}{success}</strong></div>
            <Grid container justifyContent="flex-end">
              <Grid item>
                <Link href="#" variant="body2" onClick={handleLogin}>
                  Don't have an account? Register
                </Link>
              </Grid>
            </Grid>

          </Box>
        </Box>

      </Container>
    </ThemeProvider>
  );
}