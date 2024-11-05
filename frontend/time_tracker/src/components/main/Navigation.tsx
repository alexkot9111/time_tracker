import Container from 'react-bootstrap/Container';
import Nav from 'react-bootstrap/Nav';
import Navbar from 'react-bootstrap/Navbar';
import NavDropdown from 'react-bootstrap/NavDropdown';
import AuthStore from "../auth/Store"
import { useNavigate } from "react-router-dom";
import { useDispatch } from "react-redux";

function Navigation(props: { isAuth: boolean; }) {
    const dispatch = useDispatch();
    const authStore = AuthStore(dispatch);
    const navigate = useNavigate();
    const handleLogout = (event: React.MouseEvent<HTMLElement, MouseEvent>) => {
        event.preventDefault();
        authStore.logout(navigate);
    };
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
                            <Nav.Link onClick={handleLogout}>Logout</Nav.Link>
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
