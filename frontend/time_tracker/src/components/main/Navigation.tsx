import Container from 'react-bootstrap/Container';
import Nav from 'react-bootstrap/Nav';
import Navbar from 'react-bootstrap/Navbar';

function Navigation() {
    return (
        <Navbar expand="lg" className="bg-body-tertiary main-navbar">
            <Container>
                <Navbar.Brand href="/">Time Tracker</Navbar.Brand>
                <Nav className="me-auto">
                    <Nav.Link href="users">Users List</Nav.Link>
                    <Nav.Link href="userCreate">Create New User</Nav.Link>
                </Nav>
            </Container>
        </Navbar>
    )
}

export default Navigation
