import Container from 'react-bootstrap/Container';
import Nav from 'react-bootstrap/Nav';
import Navbar from 'react-bootstrap/Navbar';
import NavDropdown from 'react-bootstrap/NavDropdown';

function Navigation(props: { isAuth: boolean; }) {
    return (
        <Navbar expand="lg" className="bg-body-tertiary main-navbar">
            <Container>
                <Navbar.Brand href="/">Time Tracker</Navbar.Brand>
                {props.isAuth ?
                    <>
                        <Nav className="me-auto">
                            <NavDropdown title="Users" id="users-dropdown">
                                <NavDropdown.Item href="/users">Users List</NavDropdown.Item>
                                <NavDropdown.Item href="/users/create">Create New User</NavDropdown.Item>
                            </NavDropdown>
                        </Nav>
                        <Nav>
                            <Nav.Link href="/logout">Logout</Nav.Link>
                        </Nav>
                    </>
                :   <Nav>
                        <Nav.Link href="/login">Login</Nav.Link>
                    </Nav> }
            </Container>
        </Navbar>
    )
}

export default Navigation
