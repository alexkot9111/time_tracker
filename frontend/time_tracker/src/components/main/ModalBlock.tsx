import Button from 'react-bootstrap/Button';
import Modal from 'react-bootstrap/Modal';

type ModalBlockProps = {
    show: boolean;
    handleClose: () => void;
    title: string;
    children: React.ReactNode;
};

const  ModalBlock: React.FC<ModalBlockProps> = ({ show, handleClose, title, children }) => {
    if (!show) {
        return null;
    }

    return (
        <Modal show={show} onHide={handleClose}>
            <Modal.Header closeButton>
                <Modal.Title>{title}</Modal.Title>
            </Modal.Header>
            <Modal.Body>{children}</Modal.Body>
            <Modal.Footer>
                <Button variant="primary" onClick={handleClose}>
                    Close
                </Button>
            </Modal.Footer>
        </Modal>
    );
};

export default ModalBlock;
