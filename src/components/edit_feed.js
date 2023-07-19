import { useEffect, useState } from 'react';
import * as CONST from '../constant';
import axios from 'axios';
import Button from '@mui/material/Button';
import Avatar from "@mui/material/Avatar";
import Edit from "@mui/icons-material/Edit";
import Box from '@mui/material/Box';
import TextField from '@mui/material/TextField';
import { useNavigate, useLocation } from "react-router-dom";

export default function Edit_feed() {

  const navigate = useNavigate();
  let logedin = localStorage.getItem('isUserLoggedIn');
  if (logedin == undefined || logedin == null || !logedin) {
    navigate("/");
  }
  let userid = localStorage.getItem('UserId');
  const [inputs, setInputs] = useState([]);
  const [success, setSucess] = useState();
  const [error, setError] = useState();

  const useQuery = () => new URLSearchParams(useLocation().search);
  const query = useQuery();
  const id = query.get('id');

  useEffect(() => {
    const getComments = async () => {
      axios.get(CONST.API_URL+`feed.php?userid=${userid}&id=${id}`).then(function (response) {
        setInputs(response.data);
      }).catch(function (error) {
        console.log('failer');
      });

    };

    getComments();
  }, []);

  console.log(inputs);

  const handleChange = (event) => {
    const name = event.target.name;
    const value = event.target.value;
    setInputs(values => ({ ...values, [name]: value }));
  }

  function handleSubmit() {
    axios.put(CONST.API_URL+'feed.php', inputs).then(function (response) {
      let result = response.data.status;
      let message = response.data.message;
      if (result == 1) {
        setError('');
        setSucess('Data Saved Successfully.. Redirecting to List Feed')
        setTimeout(function () {
          navigate("/home");
        }, 700);
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
            <Edit />
          </Avatar>
        </div>

        <div align='center'><h2>Edit Feed Information</h2></div>
        <div>
          <div>
            <TextField fullWidth name="feed_name" label="Feed Name" required className='login-textfield' value={inputs.feed_name} defaultValue='feed_name' id="fullWidth" onChange={handleChange} />
            <TextField fullWidth name="feed_desc" label="Feed Description" required value={inputs.feed_desc} defaultValue='feed_desc' className='login-textfield' id="fullWidth" onChange={handleChange} />
          </div>
          <div align="center">
            <Button className='login-submit' variant="contained" onClick={handleSubmit}>Update</Button>
            &nbsp; &nbsp; &nbsp;
            <Button className='login-submit' variant="contained" onClick={handleListfeed}>List Feed</Button>
          </div>
        </div>
        <div align='center'><strong>{error}{success}</strong></div>

      </Box>
    </div>
  );
}