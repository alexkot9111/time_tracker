import Button from 'react-bootstrap/Button'
import Form from 'react-bootstrap/Form'
import Row from 'react-bootstrap/Row'
import Col from 'react-bootstrap/Col'
import axios from 'axios'
import ModalBlock from '../main/ModalBlock'
import { useState, useEffect  } from 'react'
import { useParams } from 'react-router-dom';


const UserEdit = () => {
    const { userId } = useParams();
    const [inputs, setInputs] = useState({ first_name: '', last_name: '' })
    const [errors, setErrors] = useState([])
    const [modalShow, setModalShow] = useState(false)
    const [modalTitle, setModalTitle] = useState('')
    const [modalText, setModalText] = useState('')

    useEffect(() => {
        // Fetch user data on component mount
        axios.get(`http://localhost:8888/api/user/${userId}`)
            .then(response => {
                setInputs(response.data);
            })
            .catch(error => {
                setErrors({ ...errors, [name]: '' });
            });
    }, [userId]);

    const handleInputChange = (event) => {
        const { name, value } = event.target;
        setInputs({ ...inputs, [name]: value });
        setErrors({ ...errors, [name]: '' });
    }

    const handleFormSubmit = (event) => {
        event.preventDefault()

        axios.put(`http://localhost:8888/api/user/${userId}`, inputs)
            .then(response => {
                setModalTitle('Success')
                setModalText(`User "${inputs.first_name} ${inputs.last_name}" has been updated successfully.`)
                setModalShow(true)
                setTimeout(() => {
                    setModalShow(false)
                }, 3000)
                console.log(response.data)
            })
            .catch(error => {
                if (error.response && error.response.status === 400) {
                    setErrors(error.response.data.errors)
                } else {
                    console.error('An unexpected error occurred:', error)
                }
            })
    }

    const handleOpenModal = () => {
        setModalShow(true)
    };

    const handleCloseModal = () => {
        setModalShow(false)
    };

    return (
        <div className="container">
            <h1>Update User</h1>
            <div className="row justify-content-center">
                <div className="form-container">
                    <h2>Update User Form</h2>
                    <Form onSubmit={handleFormSubmit}>
                        <Row className="mb-3">
                            <Form.Group as={Col} md="6" controlId="formBasicFirstName">
                                <Form.Label>First Name</Form.Label>
                                <Form.Control
                                    type="text"
                                    name="first_name"
                                    placeholder="First Name"
                                    value={inputs.first_name}
                                    onChange={handleInputChange}
                                    isInvalid={!!errors.first_name}
                                />
                                <Form.Control.Feedback type="invalid">
                                    {errors.first_name}
                                </Form.Control.Feedback>
                            </Form.Group>
                            <Form.Group as={Col} md="6" controlId="formBasicLastName">
                                <Form.Label>Last Name</Form.Label>
                                <Form.Control
                                    type="text"
                                    name="last_name"
                                    placeholder="Last Name"
                                    value={inputs.last_name}
                                    onChange={handleInputChange}
                                    isInvalid={!!errors.last_name}
                                />
                                <Form.Control.Feedback type="invalid">
                                    {errors.first_name}
                                </Form.Control.Feedback>
                            </Form.Group>
                        </Row>
                        <Row className="mb-3">
                            <div className="col-12 d-grid gap-2">
                                <Button variant="primary" type="submit" className="btn btn-primary">
                                    Create
                                </Button>
                            </div>
                        </Row>
                    </Form>
                </div>
            </div>
            <ModalBlock show={modalShow} handleClose={handleCloseModal} title={modalTitle}>
                {modalText}
                {errors.length > 0 && (
                    <div>
                        <ul>
                            {errors.map((error, index) => (
                                <li key={index}>{error}</li>
                            ))}
                        </ul>
                    </div>
                )}
            </ModalBlock>
        </div>
    )
}

export default UserEdit
