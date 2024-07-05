import axios from 'axios'
import { useEffect, useState } from 'react'
import { format } from 'date-fns';
import Container from 'react-bootstrap/Container';
import ListGroup from 'react-bootstrap/ListGroup';
import Row from 'react-bootstrap/Row';
import Col from 'react-bootstrap/Col';

function UserList() {

    const [users, setUsers] = useState({});
    useEffect(() => {
        getUsers()
    }, [])

    function getUsers() {
        axios.get('http://localhost:8888/api/user')
            .then(response => {
                console.log(response.data);
                setUsers(response.data);
            })
    }
  return (
      <div className="container">
          <h1>Users List</h1>
          <ListGroup>
              <ListGroup.Item>
                  <Row>
                      <Col xs={4}>Name</Col>
                      <Col xs={4}>Email</Col>
                      <Col>Created</Col>
                      <Col>Actions</Col>
                  </Row>
              </ListGroup.Item>
              {users && users.length > 0 ? (
                  users.map((user, key) => (
                      <ListGroup.Item key={key}>
                          <Row>
                              <Col xs={4}>{user.first_name} {user.last_name}</Col>
                              <Col xs={4}>{user.email}</Col>
                              <Col>{format(new Date(user.created), 'yyyy-MM-dd HH:mm:ss')}</Col>
                              <Col>Action</Col>
                          </Row>
                      </ListGroup.Item>
                  ))
              ) : (
                  <ListGroup.Item>
                      <Row>
                          <p>No users available.</p>
                      </Row>
                  </ListGroup.Item>
              )}
          </ListGroup>
      </div>
  )
}

export default UserList
