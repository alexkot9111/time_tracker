import axios from 'axios'
import { useEffect, useState } from 'react'
import { format } from 'date-fns';
import ListGroup from 'react-bootstrap/ListGroup';
import Row from 'react-bootstrap/Row';
import Col from 'react-bootstrap/Col';
import Dropdown from 'react-bootstrap/Dropdown';

function UserList() {

    const [users, setUsers] = useState({});
    useEffect(() => {
        getUsers()
    }, [])

    const getUsers = () => {
        axios.get('http://localhost:8888/api/user')
            .then(response => {
                console.log(response.data);
                setUsers(response.data);
            })
    }

    const deleteUser = (event, userId) => {
        event.preventDefault()

        axios.delete(`http://localhost:8888/api/user/${userId}`)
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
                              <Col>
                                  <Dropdown>
                                      <Dropdown.Toggle variant="primary" id="dropdown-actions">
                                          Actions
                                      </Dropdown.Toggle>

                                      <Dropdown.Menu>
                                          <Dropdown.Item href={`/users/${user.id}/edit`}>Edit</Dropdown.Item>
                                          <Dropdown.Item href="" onClick={(e) => {
                                              deleteUser(e, user.id)
                                          }}>Delete</Dropdown.Item>
                                      </Dropdown.Menu>
                                  </Dropdown>
                              </Col>
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
