import { useEffect, useState } from 'react';
import * as CONST from '../constant';
import axios from 'axios';
import Button from '@mui/material/Button';
import Avatar from "@mui/material/Avatar";
import Add from "@mui/icons-material/Add";
import Box from '@mui/material/Box';
import TextField from '@mui/material/TextField';
import { useNavigate } from "react-router-dom";
import { confirmAlert } from 'react-confirm-alert';
import 'react-confirm-alert/src/react-confirm-alert.css'; // Import css

export default function Add_feed() {
  const navigate = useNavigate();
  let logedin = localStorage.getItem('isUserLoggedIn');
  console.log(logedin);
  if (logedin == undefined || logedin == null || !logedin) {
    navigate("/");
  }
  const userid = localStorage.getItem('UserId');

  useEffect(() => {
    const name = 'user_id';
    const value = userid;
    setInputs(values => ({ ...values, [name]: value }));
  }, []);

  const [error, setError] = useState();
  const [inputs, setInputs] = useState([]);
  const handleChange = (event) => {
    const name = event.target.name;
    const value = event.target.value;
    setInputs(values => ({ ...values, [name]: value }));
  }

  function handleSubmit() {
    axios.post(CONST.API_URL+'feed.php', inputs).then(function (response) {
      let result = response.data.status;
      let message = response.data.message;
      if (result == 1) {
        console.log('success');
        setError('');
        confirmAlert({
          message: 'Data Added Successfully',
          buttons: [
            {
              label: 'Ok',
              onClick: () => navigate("/home")
            }
          ]
        });

      } else {
        console.log('fail');
        setError(message);
      }
    }).catch(function (error) {
      console.log('failer');
      setError('Error while sending data')
    });
  }

  function handleListfeed() {
    navigate("/home");
  }

  function handlelogout() {
    localStorage.clear();
    navigate("/");
  }

  return (
    <div className='login-container'>
      <Box
        sx={{
          width: 500,
          maxWidth: '100%',
          display: "flex",
          flexDirection: "column",

        }}
      >
        <div class="row" >
          <div class="col-sm-12 col-md-12" align="right">
            <Button className='login-submit' variant="contained" onClick={handlelogout}>Logout</Button>
          </div>
        </div>
        <br />
        <div class="col-sm-12 col-md-12" align="center" >
          <Avatar sx={{ m: 1, bgcolor: "secondary.main" }}>
            <Add />
          </Avatar>
        </div>
        <div align='center'><h2>Add Feed Information</h2></div>

        <div>
          <div>
            <TextField fullWidth readOnly={true} label="Feed Name" required name="feed_name" className='login-textfield' id="fullWidth" onChange={handleChange} />
            <TextField fullWidth readOnly={true} label="Feed Description" required name="feed_desc" className='login-textfield' id="fullWidth" onChange={handleChange} />
          </div>
          <div align="center">
            <Button className='login-submit' variant="contained" onClick={handleSubmit}>Submit</Button>
            &nbsp; &nbsp; &nbsp;
            <Button className='login-submit' variant="contained" onClick={handleListfeed}>List Feed</Button>
          </div>
        </div>
        <div align='center'><strong>{error}</strong></div>

      </Box>
    </div>
  );
}