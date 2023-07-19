import { useEffect, useState } from 'react';
import * as CONST from '../constant';
import axios from 'axios';
import ReactPaginate from "react-paginate";
import { Button } from "@mui/material";
import { useNavigate, Link } from "react-router-dom";
import { confirmAlert } from 'react-confirm-alert';
import TextField from '@mui/material/TextField';
import 'react-confirm-alert/src/react-confirm-alert.css'; // Import css



function Home() {
  const navigate = useNavigate();
  let logedin = localStorage.getItem('isUserLoggedIn');
  if (logedin == undefined || logedin == null || !logedin) {
    navigate("/");
  }

  let userid = localStorage.getItem('UserId');
  const [inputs, setInputs] = useState([]);
  const [error, setError] = useState([]);
  const [items, setItems] = useState([]);
  const [appurl, setAppUrl] = useState([]);
  const [pageCount, setpageCount] = useState(0);
  const [search, setSearch] = useState("");

  // No Of Records per Each page
  let limit = CONST.ROW_COUNT;

  useEffect(() => {
    getPageload();
    setAppUrl(CONST.APP_URL);
  }, [limit]);
  console.log(appurl);
  const handleChange = (event) => {
    setSearch(event.target.value);
    const name = event.target.name;
    const value = event.target.value;
    setInputs(values => ({ ...values, [name]: value }));
  }

  const getPageload = async () => {
    const res = await fetch(
      CONST.API_URL + `feed.php?userid=${userid}&page=1&limit=${limit}`
    );
    const data = await res.json();
    const total = data.count;
    setpageCount(Math.ceil(total / limit));
    setItems(data.data);
    console.log(data.data);
  };

  const fetchComments = async (currentPage) => {
    const search_val = document.getElementById('search').value;
    const res = await fetch(
      CONST.API_URL + `feed.php?search=${search_val}&userid=${userid}&page=${currentPage}&limit=${limit}`
    );
    const data = await res.json();
    return data.data;
  };

  const handlePageClick = async (data) => {
    console.log(data.selected);
    let currentPage = data.selected + 1;
    const commentsFormServer = await fetchComments(currentPage);
    setItems(commentsFormServer);
  };

  function handleaddfeed() {
    navigate("/add_feed");
  }

  const handledeletefeed = (id) => {
    confirmAlert({
      title: 'Confirm to Delete',
      message: 'Are you sure to do this record.',
      buttons: [
        {
          label: 'Yes',
          onClick: () => handledeletefeed2(id)
        },
        {
          label: 'No'
        }
      ]
    });
  }

  const handledeletefeed2 = (id) => {
    axios.delete(CONST.API_URL + 'feed.php', { data: id }).then(function (response) {
      let result = response.data.status;
      if (result == 1) {
        confirmAlert({
          message: 'Data Deleted Successfully',
          buttons: [
            {
              label: 'Ok',
              onClick: () => getPageload()
            }
          ]
        });
      } else {
        console.log(response.data.message);
        alert('Error occured while Deleting data. Please try again');
      }
    }).catch(function (error) {
      console.log('failer');
    });
  };

  const handlesearchfeed = async (id) => {
    const search_val = document.getElementById('search').value;
    if (search_val == '') {
      alert("Please enter serach value");
    }
    else {
      const res = await fetch(
        CONST.API_URL + `feed.php?search=${search_val}&userid=${userid}&page=1&limit=${limit}`
      );

      const data = await res.json();
      const total = data.count;
      setpageCount(Math.ceil(total / limit));
      setItems(data.data);
    }
  };

  function handleListfeed() {
    setSearch('');
    getPageload()
  }

  function handlelogout() {
    localStorage.clear();
    navigate("/");
  }

  return (
    <div className="container">
      <div><div align='center'><h2>List Feed data</h2></div>
        <div class="row" >
          <div class="col-sm-12 col-md-3">
            <Button className='login-submit' variant="contained" onClick={handleaddfeed}>Add Feed</Button>
          </div>
          <div class="col-sm-12 col-md-6" >
            <table><td className='center'>
              <TextField label="Search" id="search" name="search" value={search} className='center' onChange={handleChange} />
            </td>
              <td >
                <Button className='center' variant="contained" onClick={() => handlesearchfeed(userid)}>Search</Button>
                &nbsp;
                <Button className='login-submit' variant="contained" onClick={handleListfeed}>List Feed</Button></td>
            </table>
          </div>
          <div class="col-sm-12 col-md-3" align="right">
            <Button className='login-submit' variant="contained" onClick={handlelogout}>Logout</Button>
          </div>
        </div>
        <div className="row m-2">
          <table class="table table-hover">
            <thead>
              <tr>
                <th scope="col">Feed ID</th>
                <th scope="col">Feed Name</th>
                <th scope="col">Description</th>
                <th scope="col">Create Date</th>
                <th scope="col">Updated Date</th>
                <th scope="col">User Name</th>
                <th scope="col">Handle</th>
                <th scope="col">Handle</th>
              </tr>
            </thead>
            <tbody>
              {

                items.map((item) => {
                  return (
                    <tr>
                      <th scope="row">{item.id}</th>
                      <td>{item.feed_name}</td>
                      <td>{item.feed_desc}</td>
                      <td>{item.created_date}</td>
                      <td>{item.last_updated_date}</td>
                      <td>{item.name} </td>
                      <td>
                        <Link to={appurl + `edit_feed?id=${item.id}`} style={{ marginRight: "10px" }}>Edit</Link></td>
                      <td>
                        <Button class='btn btn-danger' variant="contained" onClick={() => handledeletefeed(item.id)}>Delete</Button></td>
                    </tr>
                  );
                })}
            </tbody>
          </table>
        </div>
      </div>
      <ReactPaginate
        previousLabel={"previous"}
        nextLabel={"next"}
        breakLabel={"..."}
        pageCount={pageCount}
        marginPagesDisplayed={2}
        pageRangeDisplayed={3}
        onPageChange={handlePageClick}
        containerClassName={"pagination justify-content-center"}
        pageClassName={"page-item"}
        pageLinkClassName={"page-link"}
        previousClassName={"page-item"}
        previousLinkClassName={"page-link"}
        nextClassName={"page-item"}
        nextLinkClassName={"page-link"}
        breakClassName={"page-item"}
        breakLinkClassName={"page-link"}
        activeClassName={"active"}
      />
    </div>
  );
}

export default Home;
