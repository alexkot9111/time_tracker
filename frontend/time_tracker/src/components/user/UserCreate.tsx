import Button from 'react-bootstrap/Button'
import Form from 'react-bootstrap/Form'
import Row from 'react-bootstrap/Row'
import Col from 'react-bootstrap/Col'
import axios from 'axios'
import ModalBlock from '../main/ModalBlock'
import { useState } from 'react'
import { useNavigate } from 'react-router-dom'

function UserCreate() {

    const [inputs, setInputs] = useState({ email: '', first_name: '', last_name: '' })
    const [errors, setErrors] = useState({ email: '', first_name: '', last_name: '' })
    const [modalShow, setModalShow] = useState(false)
    const [modalTitle, setModalTitle] = useState('')
    const [modalText, setModalText] = useState('')
    const navigate = useNavigate()

    const handleInputChange = (event: React.ChangeEvent<HTMLInputElement>) => {
        const { name, value } = event.target;
        setInputs({ ...inputs, [name]: value });
        setErrors({ ...errors, [name]: '' });
    }

    const handleFormSubmit = (event: React.FormEvent<HTMLFormElement>) => {
        event.preventDefault()

        axios.post('http://localhost:8888/api/user/new', inputs)
            .then(response => {
                setModalTitle('Success')
                setModalText(`User "${inputs.first_name} ${inputs.last_name}" has been created successfully.`)
                setModalShow(true)
                setTimeout(() => {
                    setModalShow(false)
                    setTimeout(() => {
                        navigate('/users')
                    }, 1000)
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

    const handleCloseModal = () => {
        setModalShow(false)
    };

  return (
    <div className="container">
        <h1>Create User</h1>
        <div className="row justify-content-center">
            <div className="form-container">
                <h2>Create User Form</h2>
                <Form onSubmit={handleFormSubmit}>
                    <Row className="mb-3">
                        <Form.Group as={Col} md="12"  controlId="formBasicEmail">
                            <Form.Label>Email address</Form.Label>
                            <Form.Control
                                type="email"
                                name="email"
                                placeholder="Enter email"
                                value={inputs.email}
                                onChange={handleInputChange}
                                isInvalid={!!errors.email}
                            />
                            <Form.Control.Feedback type="invalid">
                                {errors.email}
                            </Form.Control.Feedback>
                        </Form.Group>
                    </Row>
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
                                {errors.last_name}
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
            {Array.isArray(errors) && errors.length > 0 && (
                <div>
                    <ul>
                        {errors.map((error: string, index: number) => (
                            <li key={index}>{error}</li>
                        ))}
                    </ul>
                </div>
            )}
        </ModalBlock>
    </div>
  )
}

export default UserCreate
